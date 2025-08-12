<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Soldier;
use App\Models\AssessmentScore;
use App\Models\AssessmentStatusTracking;
use App\Models\AppointmentMentalHealth;
use App\Models\TreatmentMentalHealth;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection; // ✅ เพิ่มการ import Collection
use Barryvdh\DomPDF\Facade\Pdf;

class MentalHealthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ส่วนของการนับจำนวนเคสสำหรับแสดงบนการ์ดสรุป
        $totalCases = AssessmentStatusTracking::whereIn('status', ['required', 'scheduled', 'completed'])->count();
        $requiredCases = AssessmentStatusTracking::where('status', 'required')->count();
        $scheduledCases = AssessmentStatusTracking::where('status', 'scheduled')->count();
        $completedCases = AssessmentStatusTracking::where('status', 'completed')->count();

        // Logic การสร้างเคสใหม่จากผลประเมินที่เสี่ยง
        $scoresBySoldier = AssessmentScore::whereIn('risk_level', ['สูง', 'ปานกลาง'])
            ->whereIn('assessment_type', ['depression', 'suicide_risk'])
            ->latest('assessment_date')
            ->get()
            ->groupBy('soldier_id');

        foreach ($scoresBySoldier as $soldierId => $scores) {
            $hasActiveCase = AssessmentStatusTracking::where('soldier_id', $soldierId)
                ->whereIn('status', ['required', 'scheduled'])->exists();

            if (!$hasActiveCase) {
                $mostRecentScore = $scores->first();
                $caseForThisScoreExists = AssessmentStatusTracking::where('assessment_score_id', $mostRecentScore->id)->exists();
                if (!$caseForThisScoreExists) {
                    AssessmentStatusTracking::create([
                        'soldier_id' => $soldierId,
                        'assessment_score_id' => $mostRecentScore->id,
                        'risk_type' => 'at_risk',
                        'risk_level_source' => $mostRecentScore->risk_level,
                        'status' => 'required'
                    ]);
                }
            }
        }

        // ดึงข้อมูลสำหรับ Dropdowns ใน Filter
        $rotations = Rotation::orderBy('rotation_name')->get();
        $trainingUnits = TrainingUnit::orderBy('unit_name')->get();

        // Query หลักสำหรับตารางด้านซ้าย
        $query = AssessmentStatusTracking::whereIn('status', ['required', 'scheduled'])
            ->with(['soldier.rotation', 'soldier.trainingUnit', 'assessmentScore', 'appointments']);

        // Filtering Logic ที่สมบูรณ์
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('soldier', function ($q) use ($searchTerm) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$searchTerm}%")
                  ->orWhere('soldier_id_card', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('rotation_id')) {
            $query->whereHas('soldier', function ($q) use ($request) {
                $q->where('rotation_id', $request->rotation_id);
            });
        }
        if ($request->filled('training_unit_id')) {
            $query->whereHas('soldier', function ($q) use ($request) {
                $q->where('training_unit_id', $request->training_unit_id);
            });
        }
        if ($request->filled('risk_type')) {
            $query->where('risk_type', $request->risk_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $perPage = $request->input('per_page', 15);
        $trackedSoldiers = $query->latest()->paginate($perPage)->withQueryString();

        // Data Enrichment สำหรับการแสดงชื่อแบบประเมินทั้งหมด
        $soldierIdsOnPage = $trackedSoldiers->pluck('soldier_id')->unique();
        $allRisksForPage = new Collection();
        if ($soldierIdsOnPage->isNotEmpty()) {
            $allRisksForPage = AssessmentScore::whereIn('soldier_id', $soldierIdsOnPage)
                ->whereIn('risk_level', ['สูง', 'ปานกลาง'])
                ->whereIn('assessment_type', ['depression', 'suicide_risk'])
                ->select('soldier_id', 'assessment_type')
                ->get()
                ->groupBy('soldier_id');
        }
        $trackedSoldiers->each(function ($trackingItem) use ($allRisksForPage) {
            if (isset($allRisksForPage[$trackingItem->soldier_id])) {
                $types = $allRisksForPage[$trackingItem->soldier_id]->pluck('assessment_type')->unique();
                $trackingItem->all_risk_assessment_types = $types;
            } else {
                $trackingItem->all_risk_assessment_types = collect(
                    $trackingItem->assessmentScore ? [$trackingItem->assessmentScore->assessment_type] : []
                );
            }
        });

        // Query ใหม่สำหรับแท็บ "รายชื่อรอส่งป่วย" ด้านขวา
        $waitingForReferral = AssessmentStatusTracking::where('status', 'required')
            ->with('soldier.trainingUnit')
            ->latest()
            ->get();

        // ส่งตัวแปรทั้งหมดไปที่ View
        return view('mental_health.dashboard', compact(
            'trackedSoldiers', 'perPage', 'totalCases', 'requiredCases',
            'scheduledCases', 'completedCases', 'rotations', 'trainingUnits',
            'waitingForReferral'
        ));
    }

    /**
     * แสดงรายชื่อเคสที่เสร็จสิ้นแล้ว (Completed)
     */
    public function showCompletedHistory(Request $request)
    {
        // 1. ดึงข้อมูลสำหรับ Filter
        $rotations = Rotation::orderBy('rotation_name')->get();
        $trainingUnits = TrainingUnit::orderBy('unit_name')->get();

        // ✅✅✅ START: แก้ไขตรรกะการกรองใหม่ทั้งหมด ✅✅✅

        // 2. เริ่มต้น Query ที่ตารางหลักก่อน
        $query = AssessmentStatusTracking::query()->where('status', 'completed');

        // 3. ใช้ Filter กับตารางหลักและตารางที่เกี่ยวข้อง
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('soldier', function ($q) use ($searchTerm) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$searchTerm}%")
                  ->orWhere('soldier_id_card', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('risk_type')) {
            $query->where('risk_type', $request->risk_type);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }
        if ($request->filled('rotation_id')) {
            $query->whereHas('soldier', function ($q) use ($request) {
                $q->where('rotation_id', $request->rotation_id);
            });
        }
        if ($request->filled('training_unit_id')) {
            $query->whereHas('soldier', function ($q) use ($request) {
                $q->where('training_unit_id', $request->training_unit_id);
            });
        }

        // 4. ดึงข้อมูลทั้งหมดที่ผ่านการกรองแล้ว (ยังอาจมีทหารซ้ำ)
        $allFilteredResults = $query->with('soldier')->get();

        // 5. คัดเลือกเฉพาะเคสล่าสุดของแต่ละคนจากผลลัพธ์ที่กรองแล้ว
        $latestCases = $allFilteredResults
            ->sortByDesc('updated_at')
            ->unique('soldier_id');

        // 6. ดึงข้อมูลที่เกี่ยวข้องทั้งหมดสำหรับเคสที่คัดเลือกแล้ว
        $latestCaseIds = $latestCases->pluck('id');
        $finalCases = AssessmentStatusTracking::whereIn('id', $latestCaseIds)
            ->with(['soldier.rotation', 'soldier.trainingUnit', 'assessmentScore', 'appointments.treatment'])
            ->get()
            ->keyBy('id') // เรียงลำดับให้ง่ายต่อการดึง
            ->sortByDesc('updated_at'); // จัดเรียงอีกครั้ง

        // 7. Data Enrichment (ดึงชื่อแบบประเมินทั้งหมด) และคำนวณยอดรวม
        $soldierIds = $finalCases->pluck('soldier_id')->unique();
        $assessmentCounts = ['depression' => 0, 'suicide_risk' => 0];
        if ($soldierIds->isNotEmpty()) {
            $allRisksForSoldiers = AssessmentScore::whereIn('soldier_id', $soldierIds)
                ->whereIn('risk_level', ['สูง', 'ปานกลาง'])
                ->whereIn('assessment_type', ['depression', 'suicide_risk'])
                ->select('soldier_id', 'assessment_type')->get()->groupBy('soldier_id');

            $finalCases->each(function ($item) use ($allRisksForSoldiers) {
                if (isset($allRisksForSoldiers[$item->soldier_id])) {
                    $item->all_risk_assessment_types = $allRisksForSoldiers[$item->soldier_id]->pluck('assessment_type')->unique();
                } else {
                    $item->all_risk_assessment_types = collect();
                }
            });

            foreach ($finalCases as $item) {
                if ($item->all_risk_assessment_types->contains('depression')) $assessmentCounts['depression']++;
                if ($item->all_risk_assessment_types->contains('suicide_risk')) $assessmentCounts['suicide_risk']++;
            }
        }
        $riskTypeCounts = $finalCases->countBy('risk_type');

        // 8. แบ่งหน้าข้อมูลด้วยตัวเอง
        $perPage = $request->input('per_page', 15);
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $currentPageItems = $finalCases->slice(($currentPage - 1) * $perPage, $perPage);
        $completedSoldiers = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems, $finalCases->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        $completedSoldiers->appends($request->all());

        // 9. ส่งตัวแปรทั้งหมดไปที่ View
        return view('mental_health.completed_history', [
            'completedSoldiers' => $completedSoldiers,
            'perPage' => $perPage,
            'rotations' => $rotations,
            'trainingUnits' => $trainingUnits,
            'riskTypeCounts' => $riskTypeCounts,
            'assessmentCounts' => $assessmentCounts,
        ]);
        // ✅✅✅ END: แก้ไขตรรกะการกรองใหม่ทั้งหมด ✅✅✅
    }
    // ... (ฟังก์ชันอื่นๆ ที่เหลือ เหมือนเดิม) ...

    public function createAppointment(Request $request)
    {
        $request->validate([
            'tracking_ids' => 'required|array|min:1',
            'tracking_ids.*' => 'exists:assessment_status_tracking,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'appointment_location' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function() use ($request) {
            foreach($request->input('tracking_ids') as $trackingId) {
                $trackingCase = AssessmentStatusTracking::find($trackingId);
                if ($trackingCase && $trackingCase->status == 'required') {
                    AppointmentMentalHealth::create([
                        'status_tracking_id' => $trackingId,
                        'appointment_date' => $request->appointment_date,
                        'appointment_time' => $request->appointment_time,
                        'appointment_location' => $request->appointment_location,
                        'notes' => $request->notes,
                        'status' => 'scheduled'
                    ]);
                    $trackingCase->update(['status' => 'scheduled']);
                }
            }
        });

        return redirect()->route('mental-health.dashboard')->with('success', 'ส่งป่วยและสร้างนัดหมายเรียบร้อยแล้ว');
    }

    public function updateAppointments(Request $request)
    {
        $request->validate([
            'appointment_ids'      => 'required|array|min:1',
            'appointment_ids.*'    => 'exists:appointments_mental_health,id',
            'appointment_date'     => 'nullable|date',
            'appointment_time'     => 'nullable',
            'appointment_location' => 'nullable|string',
            'notes'                => 'nullable|string', // เพิ่มการตรวจสอบ notes
        ]);

        $appointmentIds = $request->input('appointment_ids');
        $updateData = [];

        if ($request->filled('appointment_date')) $updateData['appointment_date'] = $request->appointment_date;
        if ($request->filled('appointment_time')) $updateData['appointment_time'] = $request->appointment_time;
        if ($request->filled('appointment_location')) $updateData['appointment_location'] = $request->appointment_location;
        if ($request->exists('notes')) {$updateData['notes'] = $request->input('notes');}

        if (!empty($updateData)) {
            AppointmentMentalHealth::whereIn('id', $appointmentIds)->update($updateData);
            return back()->with('success', 'อัปเดตข้อมูลนัดหมายเรียบร้อยแล้ว');
        }

        return back()->with('info', 'ไม่มีข้อมูลที่ถูกเปลี่ยนแปลง');
    }

    public function closeCase(Request $request, AssessmentStatusTracking $tracking)
    {
        $request->validate([
            'appointment_id'   => 'required|exists:appointments_mental_health,id',
            'doctor_name'      => 'required|string|max:255',
            'medicine_name'    => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        DB::transaction(function() use ($request, $tracking) {
            $appointment = AppointmentMentalHealth::find($request->appointment_id);

            TreatmentMentalHealth::create([
                'appointment_id'   => $appointment->id,
                'treatment_date'   => $appointment->appointment_date,
                'doctor_name'      => $request->doctor_name,
                'medicine_name'    => $request->medicine_name,
                'notes'            => $request->notes,
            ]);

            $tracking->update(['status' => 'completed']);
        });

        return redirect()->route('mental-health.dashboard')->with('success', 'บันทึกผลการรักษาและปิดเคสเรียบร้อยแล้ว');
    }

    public function bulkCloseCases(Request $request)
    {
        $request->validate([
            'ids'              => 'required|array|min:1',
            'ids.*'            => 'exists:assessment_status_tracking,id',
            'doctor_name'      => 'required|string|max:255',
            'medicine_name'    => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $casesToUpdate = AssessmentStatusTracking::whereIn('id', $request->input('ids'))->get();
            foreach ($casesToUpdate as $trackingCase) {
                $latestAppointment = $trackingCase->appointments()->latest()->first();
                if ($latestAppointment && $trackingCase->status == 'scheduled') {
                    TreatmentMentalHealth::create([
                        'appointment_id'   => $latestAppointment->id,
                        'treatment_date'   => $latestAppointment->appointment_date,
                        'doctor_name'      => $request->doctor_name,
                        'medicine_name'    => $request->medicine_name,
                        'notes'            => $request->notes,
                    ]);
                    $trackingCase->update(['status' => 'completed']);
                }
            }
        });

        return redirect()->route('mental-health.dashboard')->with('success', 'ปิดเคสที่เลือกทั้งหมดเรียบร้อยแล้ว');
    }

    public function updateRiskType(AssessmentStatusTracking $tracking)
    {
        $tracking->update(['risk_type' => 'prior_history']);
        return back()->with('success', 'เปลี่ยนประเภทความเสี่ยงเป็น \'มีประวัติเดิม\' เรียบร้อยแล้ว');
    }

    public function showHistory(Request $request, Soldier $soldier) // ✅ เพิ่ม Request $request
    {
        // ✅✅✅ [แก้ไข] รับค่า per_page และใช้ในการ paginate
        $perPage = $request->input('per_page', 5);

        $history = AssessmentStatusTracking::where('soldier_id', $soldier->id)
            ->whereIn('status', ['completed', 'not_required'])
            ->with(['appointments.treatment', 'assessmentScore'])
            ->latest()
            ->paginate($perPage); // ✅ เปลี่ยนจาก get() เป็น paginate()

        // ✅✅✅ [แก้ไข] ส่งตัวแปร perPage ไปที่ View
        return view('mental_health.individual_history', compact('soldier', 'history', 'perPage'));
    }

    public function downloadCompletedHistoryPDF(Request $request)
{
    // ดึง action และ id ที่เลือกมาจาก form
    $action = $request->input('action');
    $selectedSoldierIds = $request->input('selected_ids');

    // ✅✅✅ START: Logic ใหม่ในการดึงข้อมูลล่าสุดเท่านั้น ✅✅✅

    // 1. สร้าง Subquery เพื่อหา ID ล่าสุดของ tracking ที่ completed ของแต่ละ soldier
    $latestTrackingIdsSubQuery = AssessmentStatusTracking::select(DB::raw('MAX(id)'))
        ->where('status', 'completed')
        ->groupBy('soldier_id');

    // 2. เริ่ม Query หลัก โดยใช้ ID ที่ได้จาก Subquery
    $query = AssessmentStatusTracking::whereIn('id', $latestTrackingIdsSubQuery);

    // 3. จัดการกรณี "เลือกดาวน์โหลด"
    if ($action === 'selected' && !empty($selectedSoldierIds)) {
        $idsArray = explode(',', $selectedSoldierIds);
        // ให้ Query กรองเฉพาะ soldier_id ที่ถูกเลือกมา
        $query->whereIn('soldier_id', $idsArray);
    }

    // 4. ดึงข้อมูลพร้อม relationship ที่จำเป็น
    $completedCases = $query->with([
                            'soldier.rotation',
                            'soldier.trainingUnit',
                            'assessmentScore' // ✅ เพิ่มการดึงข้อมูล Score เพื่อเอาชื่อแบบประเมิน
                        ])
                        ->orderBy('updated_at', 'desc')
                        ->get();

    // ✅✅✅ END: Logic ใหม่ ✅✅✅

    // สร้างชื่อไฟล์แบบไดนามิก
    $fileName = 'completed-history-' . now()->format('Y-m-d-His') . '.pdf';

    // สร้าง PDF โดยส่งข้อมูลไปที่ View
    $pdf = PDF::loadView('mental_health.pdf.completed_history_pdf', ['data' => $completedCases]);

    // ตั้งค่ากระดาษเป็น A4 แนวนอน
    $pdf->setPaper('a4', 'landscape');

    // ส่งไฟล์ PDF ให้ผู้ใช้ดาวน์โหลด
    return $pdf->download($fileName);
}

// เพิ่มฟังก์ชันนี้เข้าไปในคลาส MentalHealthController

// คัดลอกทั้งหมดนี้ไปวางทับฟังก์ชันเดิมใน MentalHealthController.php

public function downloadIndividualHistoryPDF($soldier_id)
{
    // Step 1: ดึงข้อมูลทหาร
    $soldier = Soldier::with(['rotation', 'trainingUnit'])->findOrFail($soldier_id);

    // ✅✅✅ [แก้ไข] ดึงข้อมูลให้ครบ โดยเพิ่ม 'appointments.treatment' เข้าไป ✅✅✅
    $treatments = AssessmentStatusTracking::where('soldier_id', $soldier_id)
                                ->whereIn('status', ['completed', 'not_required'])
                                ->with(['appointments.treatment', 'assessmentScore']) // <-- แก้ไขที่นี่
                                ->latest()
                                ->get();

    // Step 2: สร้างชื่อไฟล์
    $fileName = 'history-' . optional($soldier)->soldier_id_card . '-' . now()->format('Ymd') . '.pdf';

    // Step 3: สร้างและส่งไฟล์ PDF
    $pdf = PDF::loadView('mental_health.pdf.individual_history_pdf', compact('soldier', 'treatments'));

    return $pdf->download($fileName);
}

public function downloadDashboardPDF(Request $request)
{
    $action = $request->input('action');
    $selectedIds = $request->input('selected_ids');
    $idsArray = !empty($selectedIds) ? explode(',', $selectedIds) : [];

    $status = null;
    $reportTitle = 'รายงาน';

    // 1. ตรวจสอบ Action เพื่อกำหนด Status และหัวข้อรายงาน
    switch ($action) {
        case 'download_required_selected':
        case 'download_required_all':
            $status = 'required';
            $reportTitle = 'รายงานเคสรอส่งป่วย';
            break;
        case 'download_scheduled_selected':
        case 'download_scheduled_all':
            $status = 'scheduled';
            $reportTitle = 'รายงานเคสนัดหมายสำเร็จ';
            break;
        default:
            // กรณีไม่พบ Action ที่ถูกต้อง, ให้ส่งกลับไปหน้าเดิมพร้อมข้อความแจ้งเตือน
            return redirect()->back()->with('error', 'ไม่สามารถทำรายการได้: ไม่พบ Action ที่ถูกต้อง');
    }

    // 2. เริ่มสร้าง Query โดยดึงข้อมูลที่จำเป็นทั้งหมด
    $query = AssessmentStatusTracking::with([
        'soldier.rotation',
        'soldier.trainingUnit',
        'assessmentScore'
    ])->where('status', $status);

    // 3. จัดการกรณี "ดาวน์โหลดที่เลือก"
    if ($action === 'download_required_selected' || $action === 'download_scheduled_selected') {
        if (empty($idsArray)) {
            return redirect()->back()->with('error', 'กรุณาเลือกรายการที่ต้องการดาวน์โหลด');
        }
        $query->whereIn('id', $idsArray);
    }
    // กรณี "ดาวน์โหลดทั้งหมด" ไม่ต้องทำอะไรเพิ่ม Query จะดึงทุกรายการตามสถานะ

    // 4. ดึงข้อมูลและเรียงลำดับ
    $data = $query->latest()->get();

    // 5. เตรียมข้อมูลสำหรับส่งไปที่ไฟล์ PDF
    $pdfData = [
        'title' => $reportTitle,
        'date' => now()->thaidate('j F Y'),
        'cases' => $data
    ];

    // 6. สร้างชื่อไฟล์และสร้าง PDF
    $fileName = str_replace(' ', '_', $reportTitle) . '_' . now()->format('Y-m-d') . '.pdf';
    $pdf = PDF::loadView('mental_health.pdf.dashboard_report_pdf', $pdfData);

    // ตั้งค่ากระดาษเป็น A4 แนวนอน
    $pdf->setPaper('a4', 'landscape');

    return $pdf->download($fileName);
}


public function assessmentSummary(Request $request)
    {
        // -- 1. ดึงข้อมูลสำหรับ Dropdown ฟิลเตอร์ --
        $units = TrainingUnit::all();
        $rotations = Rotation::all();

        // -- 2. สร้าง Query หลัก พร้อมฟิลเตอร์พื้นฐาน (หน่วย, ผลัด, ค้นหา) --
        $baseQuery = Soldier::query()
            ->when($request->filled('unit'), function ($q) use ($request) {
                $q->where('unit_id', $request->unit);
            })
            ->when($request->filled('rotation'), function ($q) use ($request) {
                $q->where('rotation_id', $request->rotation);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = $request->search;
                $q->where(function($subq) use ($searchTerm) {
                    $subq->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$searchTerm}%")
                         ->orWhere('soldier_id_card', 'LIKE', "%{$searchTerm}%");
                });
            });

        // -- 3. ดึงข้อมูลจาก Query หลักเพื่อนับจำนวนสำหรับ Stat Cards --
        $allFilteredSoldiers = $baseQuery->get();
        $totalCount = $allFilteredSoldiers->count();
        $completedCount = $allFilteredSoldiers->where('initial_assessment_complete', true)->count();
        $incompleteCount = $totalCount - $completedCount;


        // -- 4. ++ แก้ไข: Clone Query หลักขึ้นมาใหม่ก่อนจะเพิ่มเงื่อนไขสำหรับตาราง ++ --
        $soldiersForTableQuery = $baseQuery->clone()->with(['trainingUnit', 'rotation']);

        // -- 5. เพิ่มฟิลเตอร์จาก Stat Cards (ถ้ามี) ให้กับ Query ที่ Clone มา --
        if ($request->input('completed_status') == 'complete') {
            $soldiersForTableQuery->where('initial_assessment_complete', true);
        } elseif ($request->input('completed_status') == 'incomplete') {
            $soldiersForTableQuery->where('initial_assessment_complete', false);
        }

        // -- 6. ดึงข้อมูลสำหรับตารางจาก Query ที่ผ่านการกรองทั้งหมดแล้ว --
        $soldiersForTable = $soldiersForTableQuery->get();

        // -- 7. เตรียมข้อมูลคะแนนสำหรับแสดงผล (ส่วนนี้เหมือนเดิม) --
        $assessmentTypes = ['smoking', 'alcohol', 'drug_use', 'suicide_risk', 'depression'];
        $assessmentLabels = [
            'smoking' => 'การสูบบุหรี่',
            'alcohol' => 'การดื่มสุรา',
            'drug_use' => 'สารเสพติด',
            'suicide_risk' => 'เสี่ยงฆ่าตัวตาย',
            'depression' => 'ภาวะซึมเศร้า'
        ];

        $assessmentData = [];
        foreach ($soldiersForTable as $soldier) {
            $latestScores = [];
            foreach ($assessmentTypes as $type) {
                $score = AssessmentScore::where('soldier_id', $soldier->id)
                                        ->where('assessment_type', $type)
                                        ->latest('created_at')
                                        ->first();
                $latestScores[$type] = $score ? $score->total_score : '-';
            }
            $assessmentData[] = [
                'soldier' => $soldier,
                'scores' => $latestScores,
            ];
        }

        // -- 8. ส่งข้อมูลทั้งหมดไปยัง View (ส่วนนี้เหมือนเดิม) --
        return view('mental_health.summary', [
            'assessmentData' => $assessmentData,
            'assessmentTypes' => $assessmentTypes,
            'assessmentLabels' => $assessmentLabels,
            'units' => $units,
            'rotations' => $rotations,
            'totalCount' => $totalCount,
            'completedCount' => $completedCount,
            'incompleteCount' => $incompleteCount,
            'request' => $request
        ]);
    }





}
