<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Soldier;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\AssessmentScore;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentStatusTracking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssessmentController extends Controller
{
    /**
     * กำหนดลำดับของแบบประเมินที่ต้องทำในครั้งแรก
     * @var array
     */
    private $assessmentSequence = [
        'smoking',
        'alcohol',
        'drug_use',
        'depression',
        'suicide_risk'
    ];

    /**
     * แสดงฟอร์มแบบประเมินตามประเภท
     */
    public function showAssessmentForm($soldier_id, $type)
    {
        $soldier = Soldier::findOrFail($soldier_id);
        $questions = AssessmentQuestion::where('assessment_type', $type)
            ->with('options')
            ->get();

        return view('soldier.assessment_form', compact('soldier', 'questions', 'type'));
    }

    /**
     * ฟังก์ชันสำหรับจัดการการข้ามแบบประเมิน (ฉบับแก้ไขล่าสุด)
     */
    public function skipAssessment($soldier_id, $type)
{
    $soldier = Soldier::findOrFail($soldier_id);

    DB::transaction(function () use ($soldier, $type) {
        $assessmentScore = AssessmentScore::create([
            'soldier_id'  => $soldier->id,
            'assessment_type' => $type, // [แก้ไข] เพิ่มการบันทึกประเภท
            'total_score' => 0,
            'risk_level'  => 'ต่ำ',
            'assessment_date' => now(),
        ]);

        $firstQuestion = AssessmentQuestion::where('assessment_type', $type)->first();
        if ($firstQuestion) {
            AssessmentAnswer::create([
                'assessment_score_id'    => $assessmentScore->id,
                'assessment_question_id' => $firstQuestion->id,
                'selected_option_id'     => null,
                'answered_at'            => now(),
            ]);
        }
    });


    // ✅ [ส่วนที่เพิ่ม] เรียกใช้ฟังก์ชันจัดการเคส ถ้าเป็นแบบประเมินที่เกี่ยวข้อง
    if (in_array($type, ['depression', 'suicide_risk'])) {
        $this->manageMentalHealthCase($soldier);
    }

    return $this->redirectToNextAssessment($soldier, $type);
}

    /**
     * บันทึกผลและจัดการลำดับการทำแบบประเมิน
     */
    public function submitAssessment(Request $request, $soldier_id, $type)
{
    $request->validate([
        'answers'   => 'required|array',
        'answers.*' => 'required|integer|exists:assessment_option,id',
    ]);

    $soldier = Soldier::findOrFail($soldier_id);
    $totalScore = 0;

    DB::transaction(function () use ($request, $soldier, $type, &$totalScore) {
        $selectedOptionIds = array_values($request->input('answers'));
        $options = AssessmentOption::whereIn('id', $selectedOptionIds)->get();
        foreach ($options as $option) {
            $totalScore += $option->score;
        }

        $assessmentScore = AssessmentScore::create([
            'soldier_id'  => $soldier->id,
            'assessment_type' => $type, // [แก้ไข] เพิ่มการบันทึกประเภท
            'total_score' => $totalScore,
            'risk_level'  => $this->calculateRiskLevel($type, $totalScore),
            'assessment_date' => now(),
        ]);

        $answers_data = [];
        foreach ($request->input('answers') as $questionId => $optionId) {
            $answers_data[] = [
                'assessment_score_id'    => $assessmentScore->id,
                'assessment_question_id' => $questionId,
                'selected_option_id'     => $optionId,
                'answered_at'            => now(),
                'created_at'             => now(),
                'updated_at'             => now(),
            ];
        }
        AssessmentAnswer::insert($answers_data);
    });

    // ✅ [ส่วนที่เพิ่ม] เรียกใช้ฟังก์ชันจัดการเคส ถ้าเป็นแบบประเมินที่เกี่ยวข้อง
    if (in_array($type, ['depression', 'suicide_risk'])) {
        $this->manageMentalHealthCase($soldier);
    }

    return $this->redirectToNextAssessment($soldier, $type);
}

    /**
     * ฟังก์ชันสำหรับจัดการการ Redirect ไปยังแบบประเมินถัดไป
     */
    private function redirectToNextAssessment(Soldier $soldier, string $currentType)
    {
        // ถ้ายังไม่เคยทำครบ ให้ทำงานตามลำดับ
        if (!$soldier->initial_assessment_complete) {
            $currentIndex = array_search($currentType, $this->assessmentSequence);

            // ตรวจสอบว่ามีแบบประเมินลำดับถัดไปหรือไม่
            if ($currentIndex !== false && isset($this->assessmentSequence[$currentIndex + 1])) {
                $nextAssessmentType = $this->assessmentSequence[$currentIndex + 1];
                return redirect()->route('assessment.show', [
                    'soldier_id' => $soldier->id,
                    'type' => $nextAssessmentType
                ]);
            } else {
                // ทำอันสุดท้ายเสร็จแล้ว
                $soldier->initial_assessment_complete = true;
                $soldier->save();

                return redirect()->route('assessment.history', ['soldierId' => $soldier->id])
                                 ->with('success', 'คุณได้ทำแบบประเมินเบื้องต้นครบถ้วนแล้ว');
            }
        }

        // ถ้าเคยทำครบแล้ว (เป็นการทำซ้ำ) ให้กลับไปหน้าประวัติ
        return redirect()->route('assessment.history', ['soldierId' => $soldier->id])
                         ->with('success', 'บันทึกผลการประเมินเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันคำนวณระดับความเสี่ยง
     */
    private function calculateRiskLevel($type, $score)
    {
        $levels = [
            'suicide_risk' => ($score >= 10 ? 'สูง' : ($score >= 5 ? 'ปานกลาง' : 'ต่ำ')),
            'depression' => ($score >= 13 ? 'สูง' : ($score >= 7 ? 'ปานกลาง' : 'ต่ำ')),
            'smoking' => ($score >= 6 ? 'สูง' : ($score >= 4 ? 'ปานกลาง' : 'ต่ำ')),
            'drug_use' => ($score >= 27 ? 'สูง' : ($score >= 4 ? 'ปานกลาง' : 'ต่ำ')),
            'alcohol' => ($score >= 20 ? 'สูง' : ($score >= 16 ? 'ปานกลาง' : 'ต่ำ')),
        ];

        return $levels[$type] ?? 'ต่ำ';
    }

    /**
     * แสดงหน้ารวมสำหรับเลือกทำแบบประเมิน
     */
    public function viewAssessment($soldier_id)
    {
        $soldier = Soldier::with('assessmentScores.assessmentType')->findOrFail($soldier_id);
        $assessmentScores = $soldier->assessmentScores;

        $completedAssessments = $assessmentScores->map(function ($score) {
            return optional($score->assessmentType)->assessment_type;
        })->filter()->unique()->values()->toArray();

        // ตรวจสอบว่ามีเคสสุขภาพจิตที่อยู่ในสถานะ "นัดหมายสำเร็จ" หรือไม่
        $hasScheduledCase = AssessmentStatusTracking::where('soldier_id', $soldier_id)
                                                    ->where('status', 'scheduled')
                                                    ->exists();

        return view('soldier.view_assessment', compact(
            'soldier',
            'completedAssessments',
            'assessmentScores',
            'hasScheduledCase'
        ));
    }

    /**
     * แสดงหน้าประวัติการทำแบบประเมิน
     */
    public function assessmentHistory(Request $request, $soldierId) // ✅ 3. เพิ่ม Request $request
    {
        $soldier = Soldier::findOrFail($soldierId);

        // ✅ 4. เริ่มต้นสร้าง Query Builder
        $query = AssessmentScore::where('soldier_id', $soldierId);

        // ✅ 5. เพิ่มเงื่อนไขการกรองตาม "ประเภท" (type)
        if ($request->filled('type')) {
            // ใช้ whereHas เพื่อกรองข้อมูลจากตารางที่เชื่อมกันอยู่ (assessment_questions)
            $query->whereHas('assessmentType', function ($q) use ($request) {
                $q->where('assessment_type', $request->type);
            });
        }

        // ✅ 6. เพิ่มเงื่อนไขการกรองตาม "ระยะเวลา" (range)
        if ($request->filled('range')) {
            $days = intval($request->range);
            $query->where('assessment_date', '>=', Carbon::now()->subDays($days));
        }

        // ✅ 7. ดึงข้อมูลท้ายสุดพร้อมจัดลำดับและแบ่งหน้า
        $histories = $query->with('assessmentType')
        ->orderBy('assessment_date', 'desc')
        ->paginate(5) // <-- แก้ไขเป็น 5
        ->appends($request->query());

        $typeLabels = [
            'smoking' => 'สูบบุหรี่',
            'drug_use' => 'ใช้สารเสพติด',
            'alcohol' => 'แอลกอฮอล์',
            'depression' => 'ภาวะซึมเศร้า',
            'suicide_risk' => 'เสี่ยงฆ่าตัวตาย',
        ];

        return view('soldier.assessment_history', compact('histories', 'soldier', 'typeLabels'));
    }

    /**
     * ✅ [ฟังก์ชันใหม่]
     * จัดการเคสสุขภาพจิตอัตโนมัติหลังการทำแบบประเมิน
     *
     * @param \App\Models\Soldier $soldier
     * @return void
     */
    private function manageMentalHealthCase(Soldier $soldier)
{
    // 1. ดึงผลคะแนน "ล่าสุด" ของทั้ง 2 แบบประเมินที่สำคัญ
    $latestDepressionScore = AssessmentScore::where('soldier_id', $soldier->id)
        ->where('assessment_type', 'depression')
        ->latest('assessment_date')
        ->first();

    $latestSuicideScore = AssessmentScore::where('soldier_id', $soldier->id)
        ->where('assessment_type', 'suicide_risk')
        ->latest('assessment_date')
        ->first();

    // 2. ตรวจสอบว่าผลคะแนนล่าสุดยังมีความเสี่ยงอยู่หรือไม่
    $isStillAtRisk = false;
    if ($latestDepressionScore && in_array($latestDepressionScore->risk_level, ['สูง', 'ปานกลาง'])) {
        $isStillAtRisk = true;
    }
    if ($latestSuicideScore && in_array($latestSuicideScore->risk_level, ['สูง', 'ปานกลาง'])) {
        $isStillAtRisk = true;
    }

    // 3. ค้นหาเคสที่ยัง "Active" อยู่
    $activeCase = AssessmentStatusTracking::where('soldier_id', $soldier->id)
        ->whereIn('status', ['required', 'scheduled']) // ✅ แก้ไข: สถานะ Active คือ required และ scheduled
        ->first();

    // 4. ใช้ Logic ใหม่ในการตัดสินใจ
    if ($isStillAtRisk) {
        // ---- กรณี: ผลล่าสุดยังมีความเสี่ยง ----
        if (!$activeCase) {
            // ถ้ายังไม่มีเคส Active อยู่ ให้สร้างเคสใหม่
            $triggeringScore = ($latestDepressionScore && in_array($latestDepressionScore->risk_level, ['สูง', 'ปานกลาง']))
                                ? $latestDepressionScore
                                : $latestSuicideScore;

            AssessmentStatusTracking::create([
                'soldier_id' => $soldier->id,
                'assessment_score_id' => $triggeringScore->id,
                'risk_type' => 'at_risk',
                'risk_level_source' => $triggeringScore->risk_level,
                'status' => 'required' // ✅ แก้ไข: สถานะเริ่มต้นคือ 'required' (รอส่งป่วย)
            ]);
        }
    } else {
        // ---- กรณี: ผลล่าสุดไม่มีความเสี่ยงแล้ว ----
        if ($activeCase) {
            // ถ้ามีเคส Active อยู่ ให้ทำการปิดเคสอัตโนมัติ
            $activeCase->update(['status' => 'not_required']); // ✅ แก้ไข: ใช้ 'not_required' แทน 'normal'
        }
    }
}

}
