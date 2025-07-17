<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Soldier;
use App\Models\Rotation;
use App\Models\TrainingUnit;
use App\Models\MedicalDiagnosis;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session; // ⭐️ เพิ่มบรรทัดนี้เข้ามา
use App\Models\Appointment;
use Illuminate\Support\Facades\Storage;


class SoldierController extends Controller
{
    private $assessmentSequence = [
        'depression',
        'suicide_risk',
        'alcohol',
        'smoking',
        'drug_use'
    ];
    /**
     * แสดงหน้าฟอร์มล็อกอินสำหรับทหาร
     */
    public function showLoginForm()
    {
        // ตรวจสอบว่าถ้ามีการล็อกอินค้างอยู่ ให้ redirect ไปที่โปรไฟล์เลย
        if (Session::has('soldier_id')) {
            return redirect()->route('profile.inv.soldier', ['id' => Session::get('soldier_id')]);
        }
        return view('soldier.login');
    }

    /**
     * จัดการการยืนยันตัวตนของทหาร (Authenticate)
     */
    public function authenticate(Request $request)
    {
        $request->validate(['soldier_id_card' => 'required|string|max:13']);

        $soldier = Soldier::where('soldier_id_card', $request->soldier_id_card)->first();

        if ($soldier) {
            Session::put('soldier_id', $soldier->id);

            if ($soldier->consent_accepted == 0) {
                // ไปหน้าแก้ไขข้อมูลครั้งแรก
                return redirect()->route('soldier.edit_personal_info', ['id' => $soldier->id])
                                 ->with('info', 'กรุณาตรวจสอบและอัปเดตข้อมูลส่วนตัวของคุณ');
            } else {
                // ไปหน้าโปรไฟล์ตามปกติ
                return redirect()->route('profile.inv.soldier', ['id' => $soldier->id]);
            }
        }

        return redirect()->route('soldier.login')
                         ->with('error', 'ไม่พบข้อมูลทหาร กรุณาตรวจสอบเลขบัตรประชาชน');
    }

    public function totalSoldiers()
    {
        // Get the total count of soldiers
        $totalSoldiers = Soldier::count();

        return view('admin.dashboardadmin', compact('totalSoldiers'));
    }
    // แสดงฟอร์มเพิ่มทหาร พร้อมดึงผลัดและหน่วยฝึกที่เป็น Active
    public function create_soldier()
    {
        $rotations = Rotation::where('status', 'active')->get(); // ดึงเฉพาะผลัดที่ Active
        $units = TrainingUnit::where('status', 'active')->get(); // ดึงเฉพาะหน่วยฝึกที่ Active

        return view('admin.add_soldier_form', compact('rotations', 'units'));
    }


    public function store_soldier(Request $request)
    {
        $request->validate([
            'soldier_id_card' => 'required|string|max:13|unique:soldier,soldier_id_card',
            'first_name' => 'required|string|max:48',
            'last_name' => 'required|string|max:48',
            'rotation_id' => 'required|exists:rotation,id',
            'training_unit_id' => 'required|exists:training_unit,id',
            'affiliated_unit' => 'nullable|string|max:48',
            'weight_kg' => 'nullable|numeric|min:30|max:150',
            'height_cm' => 'nullable|integer|min:120|max:250',
            'medical_allergy_food_history' => 'nullable|string',
            'underlying_diseases' => 'nullable|string',
            'selection_method' => 'required|string|max:48',
            'service_duration' => 'required|integer|min:1|max:60',
            'soldier_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // ✅ รองรับเฉพาะไฟล์รูปภาพ
        ]);

        $imagePath = null;
        $uploadDir = public_path('uploads/soldiers'); // ✅ ใช้ public_path('uploads/soldiers') ตรงๆ

        // ✅ ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
        if ($request->hasFile('soldier_image')) {
            $image = $request->file('soldier_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadDir, $filename);
            $imagePath = 'uploads/soldiers/' . $filename;
        }

        Soldier::create([
            'soldier_id_card' => $request->soldier_id_card,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'rotation_id' => $request->rotation_id,
            'training_unit_id' => $request->training_unit_id,
            'affiliated_unit' => $request->affiliated_unit,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'medical_allergy_food_history' => $request->medical_allergy_food_history,
            'underlying_diseases' => $request->underlying_diseases,
            'selection_method' => $request->selection_method,
            'service_duration' => $request->service_duration,
            'soldier_image' => $imagePath // ✅ บันทึกพาธของรูปภาพ
        ]);

        return redirect('/soldier')->with('success', 'เพิ่มข้อมูลทหารเรียบร้อยแล้ว!');
    }


    public function delete_soldier($id)
    {
        $soldier = Soldier::find($id);

        if (!$soldier) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทหารที่ต้องการลบ');
        }

        $soldier->delete();

        return redirect()->back()->with('success', 'ลบข้อมูลทหารเรียบร้อยแล้ว');
    }
    public function edit_soldier($id)
    {
        // ค้นหาข้อมูลทหารที่ต้องการแก้ไข
        $soldier = Soldier::find($id);

        if (!$soldier) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทหาร');
        }

        // ดึงข้อมูลผลัดทั้งหมด (Rotation) เพื่อแสดงใน Drop-down
        $rotations = Rotation::all();

        // ดึงข้อมูลหน่วยฝึกทั้งหมด (Training Unit) เพื่อแสดงใน Drop-down
        $training_units = TrainingUnit::all();

        // ส่งข้อมูลไปยัง View (Blade Template) เพื่อแสดงแบบฟอร์มแก้ไข
        return view('admin.soldier_edit', compact('soldier', 'rotations', 'training_units'));
    }

    // 📌 ฟังก์ชันอัปเดตข้อมูลทหาร
    public function update_soldier(Request $request, $id)
    {
        $soldier = Soldier::find($id);

        if (!$soldier) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทหาร');
        }

        $request->validate([
            'first_name' => 'required|string|max:48',
            'last_name' => 'required|string|max:48',
            'rotation_id' => 'nullable|integer|exists:rotation,id',
            'training_unit_id' => 'nullable|integer|exists:training_unit,id',
            'affiliated_unit' => 'nullable|string|max:48',
            'weight_kg' => 'nullable|numeric|min:30|max:200',
            'height_cm' => 'nullable|integer|min:100|max:250',
            'medical_allergy_food_history' => 'nullable|string|max:255',
            'underlying_diseases' => 'nullable|string|max:255',
            'selection_method' => 'nullable|string|max:48',
            'service_duration' => 'nullable|integer|min:1|max:60',
            'soldier_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imagePath = $soldier->soldier_image; // ใช้ path เดิมถ้าไม่มีการอัปโหลดใหม่
        $uploadDir = public_path('uploads/soldiers'); // ✅ ใช้ public_path('uploads/soldiers') ตรงๆ

        // ✅ ตรวจสอบว่ามีการอัปโหลดไฟล์ใหม่หรือไม่
        if ($request->hasFile('soldier_image')) {
            $image = $request->file('soldier_image');
            $filename = time() . '.' . $image->getClientOriginalExtension();

            if (!empty($soldier->soldier_image)) {
                $oldImagePath = public_path($soldier->soldier_image);
                if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // ✅ ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // ✅ บันทึกไฟล์ใหม่ไปที่ public/uploads/soldiers/
            $image->move($uploadDir, $filename);

            // ✅ อัปเดต path ใหม่
            $imagePath = 'uploads/soldiers/' . $filename;
        }

        // ✅ อัปเดตข้อมูลทั้งหมด (แก้ไข `soldier_image` ถ้ามีการอัปโหลดใหม่เท่านั้น)
        $soldier->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'rotation_id' => $request->rotation_id,
            'training_unit_id' => $request->training_unit_id,
            'affiliated_unit' => $request->affiliated_unit,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'medical_allergy_food_history' => $request->medical_allergy_food_history,
            'underlying_diseases' => $request->underlying_diseases,
            'selection_method' => $request->selection_method,
            'service_duration' => $request->service_duration,
            'soldier_image' => $imagePath // ✅ ใช้ path เดิมถ้าไม่มีการอัปโหลดใหม่
        ]);

        return redirect()->route('soldier.edit_soldier', $id)->with('success', 'อัปเดตข้อมูลทหารเรียบร้อยแล้ว');
    }

    public function getImage($id)
    {
        $soldier = Soldier::find($id);

        if (!$soldier || !$soldier->soldier_image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        // ใช้ public_path() ให้ถูกต้อง
        $imagePath = public_path($soldier->soldier_image);

        if (!file_exists($imagePath)) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->file($imagePath, [
            'Content-Type' => mime_content_type($imagePath)
        ]);
    }

    public function view_soldier($id)
    {
        $soldier = Soldier::with(['rotation', 'trainingUnit'])->find($id);

        if (!$soldier) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทหาร');
        }

        return view('admin.soldier_profile', compact('soldier'));
    }

    public function getSoldierImageAttribute($value)
    {
        return asset('uploads/soldiers/' . basename($value)); // ✅ ดึงจาก public/uploads/soldiers/
    }


    public function showInvProfile($id)
    {
        // ใช้ findOrFail เพื่อจัดการกรณีไม่พบข้อมูลและทำให้โค้ดสั้นลง
        $soldier = Soldier::with(['rotation', 'trainingUnit','mentalHealthTracking.appointments.treatment'])->findOrFail($id);

        // ---  ดึงข้อมูลสรุปผลประเมินล่าสุดด้วย Eloquent (เหมือนเดิม) ---
        $recentHistories = $soldier->assessmentScores()
            ->with('assessmentType')
            ->get()
            ->unique(function ($item) {
                return optional($item->assessmentType)->assessment_type;
            });

        // --- ดึงข้อมูลประวัติการรักษาทั้งหมด (เหมือนเดิม) ---
        $medicalHistory = DB::table('medical_diagnosis as md')
            ->join('treatment as t', 'md.treatment_id', '=', 't.id')
            ->join('checkin as c', 't.checkin_id', '=', 'c.id')
            ->join('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->join('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->leftJoin('vital_signs as vs', 'md.vital_signs_id', '=', 'vs.id')
            ->leftJoin(DB::raw('(SELECT medical_diagnosis_id, GROUP_CONCAT(DISTINCT icd10_diseases.icd10_code SEPARATOR ", ") as icd10_codes, GROUP_CONCAT(DISTINCT icd10_diseases.disease_name_en SEPARATOR "; ") as disease_names FROM medical_diagnosis_diseases JOIN icd10_diseases ON medical_diagnosis_diseases.icd10_disease_id = icd10_diseases.id GROUP BY medical_diagnosis_id) as diseases'), 'md.id', '=', 'diseases.medical_diagnosis_id')
            ->where('mr.soldier_id', $soldier->id)
            ->select('md.id', 'mr.symptom_description', 'md.doctor_name', 'md.department_type', 'md.treatment_status', 'md.diagnosis_date', 'vs.temperature', 'vs.blood_pressure', 'vs.heart_rate', 'diseases.icd10_codes', 'diseases.disease_names')
            ->groupBy('md.id', 'mr.symptom_description', 'md.doctor_name', 'md.department_type', 'md.treatment_status', 'md.diagnosis_date', 'vs.temperature', 'vs.blood_pressure', 'vs.heart_rate', 'diseases.icd10_codes', 'diseases.disease_names')
            ->orderBy('md.diagnosis_date', 'desc')
            ->get();

        // กำหนด Type Labels ที่นี่ เพื่อใช้ใน View
        $typeLabels = [
            'smoking' => 'สูบบุหรี่',
            'drug_use' => 'ใช้สารเสพติด',
            'alcohol' => 'แอลกอฮอล์',
            'depression' => 'ภาวะซึมเศร้า',
            'suicide_risk' => 'เสี่ยงฆ่าตัวตาย',
        ];

        // --- ✅ [แก้ไข] ส่งข้อมูลทั้งหมดไปที่ View ---
        return view('soldier.profile_inv_soldier', compact('soldier', 'recentHistories', 'medicalHistory', 'typeLabels'));
    }


    public function getDiagnosisDetails($id)
    {
        // ใช้ Query คล้ายกับของเดิม แต่ดึงข้อมูลที่ละเอียดกว่าสำหรับ ID เดียว
        $diagnosisDetail = DB::table('medical_diagnosis as md')
            ->leftJoin('treatment as t', 'md.treatment_id', '=', 't.id')
            ->leftJoin('checkin as c', 't.checkin_id', '=', 'c.id')
            ->leftJoin('appointment as a', 'c.appointment_id', '=', 'a.id')
            ->leftJoin('medical_report as mr', 'a.medical_report_id', '=', 'mr.id')
            ->leftJoin(DB::raw('(SELECT medical_diagnosis_id, GROUP_CONCAT(DISTINCT icd10_diseases.icd10_code, ": ", icd10_diseases.disease_name_en SEPARATOR "|") as diseases FROM medical_diagnosis_diseases JOIN icd10_diseases ON medical_diagnosis_diseases.icd10_disease_id = icd10_diseases.id GROUP BY medical_diagnosis_id) as diseases'), 'md.id', '=', 'diseases.medical_diagnosis_id')
            ->where('md.id', $id)
            ->select(
                'md.department_type',
                'md.doctor_name',
                'md.treatment_status',
                'md.training_instruction', // เพิ่มคำแนะนำการฝึก
                'mr.symptom_description',  // เพิ่มอาการ
                'diseases.diseases'
            )
            ->first();

        if (!$diagnosisDetail) {
            return response()->json(['error' => 'ไม่พบข้อมูล'], 404);
        }

        // จัดรูปแบบข้อมูล diseases ให้เป็น array
        if ($diagnosisDetail->diseases) {
            $diagnosisDetail->diseases = explode('|', $diagnosisDetail->diseases);
        } else {
            $diagnosisDetail->diseases = [];
        }

        return response()->json($diagnosisDetail);
    }


    public function authenticateSoldier(Request $request)
    {
        $request->validate([
            'soldier_id_card' => 'required|digits:13'
        ]);

        $soldier = Soldier::where('soldier_id_card', $request->soldier_id_card)->first();

        if (!$soldier) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทหาร กรุณาตรวจสอบเลขบัตรประชาชน');
        }

        // ✅ บันทึก soldier_id ลง session
        session(['soldier_id' => $soldier->id]);

        // ✅ เรียกหน้าโปรไฟล์หลังจาก login สำเร็จ
        return redirect()->route('soldier.individual', ['id' => $soldier->id]);
    }


    public function individual_soldier($id)
    {
        $soldier = Soldier::with(['rotation', 'trainingUnit'])->find($id);

        if (!$soldier) {
            return redirect()->route('soldier.login')->with('error', 'ไม่พบข้อมูลทหาร');
        }

        return view('soldier.profile_inv_soldier', compact('soldier'));
    }


    public function acceptConsent(Request $request, $id){
    $soldier = Soldier::findOrFail($id);
    $soldier->consent_accepted = true;
    $soldier->save();

    return response()->json(['status' => 'success', 'message' => 'Consent accepted successfully']);
    }


    public function showMedicalHistory($soldierId)
    {
        // ดึงข้อมูลทหารจากฐานข้อมูล
        $soldier = Soldier::find($soldierId);

        if (!$soldier) {
            return redirect()->back()->with('error', 'ทหารไม่พบ');
        }

        // ดึงข้อมูลการวินิจฉัยทั้งหมดของทหาร
        $medicalDiagnoses = MedicalDiagnosis::whereHas('treatment', function ($query) use ($soldierId) {
            $query->where('soldier_id', $soldierId);
        })->with(['diseases', 'vitalSigns'])->get();

        // ส่งข้อมูลไปยังวิว
        return view('admin.soldier_profile', [
            'soldier' => $soldier,  // ส่งข้อมูลทหาร
            'medicalDiagnoses' => $medicalDiagnoses // ส่งข้อมูลประวัติการรักษา
        ]);
    }

    public function dashboard($id)
    {
        $soldier = Soldier::findOrFail($id);

        // วันนัดหมายถัดไป (ตัวอย่าง)
        $nextAppointment = Carbon::parse('2025-04-30');
        $lastCheckup = Carbon::parse('2025-04-05');

        /// ✅ [ฉบับแก้ไข] ใช้ Relationship เพื่อดึงข้อมูลได้ง่ายขึ้น
        $recentHistories = $soldier->assessmentScores()
            ->with('assessmentType')
            ->limit(5) // สามารถเปลี่ยน limit ตรงนี้ได้ตามต้องการ
            ->get();

        return view('soldier.dashboard', compact(
            'soldier',
            'nextAppointment',
            'lastCheckup',
            'recentHistories'
        ));
    }


    // แสดงฟอร์มแก้ไขข้อมูลส่วนตัวเฉพาะบางช่อง
    public function editPersonalInfo($id)
    {
        $soldier = Soldier::findOrFail($id);
        return view('soldier.edit_personal_info', compact('soldier'));
    }

   // ใน SoldierController.php

public function updatePersonalInfo(Request $request, $id)
{
    // 1. ตรวจสอบความถูกต้องของข้อมูลที่ส่งมา (เหมือนเดิม)
    $request->validate([
        'first_name' => 'required|string|max:48',
        'last_name' => 'required|string|max:48',
        'weight_kg' => 'nullable|numeric',
        'height_cm' => 'nullable|integer',
        'medical_allergy_food_history' => 'nullable|string',
        'underlying_diseases' => 'nullable|string',
        'soldier_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $soldier = Soldier::findOrFail($id);

    // 2. ส่วนจัดการรูปภาพ (ถ้ามี) (เหมือนเดิม)
    $imagePath = $soldier->soldier_image;
    if ($request->hasFile('soldier_image')) {
        $uploadDir = public_path('uploads/soldiers');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        $image = $request->file('soldier_image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move($uploadDir, $filename);
        $imagePath = 'uploads/soldiers/' . $filename;
    }

    // 3. อัปเดตข้อมูล (เหมือนเดิม)
    $soldier->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'weight_kg' => $request->weight_kg,
        'height_cm' => $request->height_cm,
        'medical_allergy_food_history' => $request->medical_allergy_food_history,
        'underlying_diseases' => $request->underlying_diseases,
        'soldier_image' => $imagePath,
        'consent_accepted' => 1,
    ]);

    // ✅ 4. [ส่วนที่แก้ไข] เพิ่มเงื่อนไขในการ Redirect
    // ตรวจสอบว่าเคยทำแบบประเมินชุดแรกเสร็จแล้วหรือยัง
    if (!$soldier->initial_assessment_complete) {
        // Flow สำหรับครั้งแรก: ถ้ายังทำแบบประเมินไม่ครบ
        // ให้ไปที่หน้าทำแบบประเมินแรก
        return redirect()->route('assessment.show', ['soldier_id' => $soldier->id, 'type' => 'smoking'])
                         ->with('info', 'อัปเดตข้อมูลสำเร็จแล้ว กรุณาทำแบบประเมินเบื้องต้น');
    } else {
        // Flow สำหรับครั้งถัดไป: ถ้าเคยทำแบบประเมินครบแล้ว
        // ให้กลับไปที่หน้าแก้ไขข้อมูลเหมือนเดิม พร้อมข้อความว่าสำเร็จ
        return redirect()->route('soldier.edit_personal_info', ['id' => $soldier->id])
                         ->with('success', 'อัปเดตข้อมูลส่วนตัวเรียบร้อยแล้ว');
    }
}

}
