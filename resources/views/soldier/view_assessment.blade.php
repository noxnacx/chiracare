<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <style>
        .assessment-card {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: .5rem;
            transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
            height: 100%;
            cursor: pointer;
        }
        .assessment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .completed-check {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            opacity: 0.9;
        }
        .card-body i {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        /* ✅ [เพิ่มใหม่] CSS สำหรับปรับดีไซน์ Modal ให้เหมือนในรูปภาพ */
        #completedAssessmentModal .modal-content {
            border-radius: .75rem;
            border: none;
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.2);
        }
        #completedAssessmentModal .modal-body {
            padding: 2rem;
        }
        #completedAssessmentModal .btn-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        #completedAssessmentModal .modal-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f8f9fa;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        #completedAssessmentModal .modal-icon-wrapper i {
            font-size: 1.75rem;
            color: #495057;
        }
        #completedAssessmentModal .modal-title {
            font-weight: 600;
            font-size: 1.25rem;
        }
        #completedAssessmentModal .modal-info-text {
            font-size: 0.8rem;
            color: #6c757d;
            background-color: #f8f9fa;
            padding: 0.5rem 0.75rem;
            border-radius: .25rem;
            text-align: left;
        }
        #completedAssessmentModal .modal-info-text i {
            font-size: 0.8rem;
            color: #6c757d;
            margin-right: 0.5rem;
        }
        #completedAssessmentModal .btn-retake {
            background-color: #212529;
            color: #fff;
            font-weight: 500;
        }
        #completedAssessmentModal .btn-retake:hover {
            background-color: #343a40;
            color: #fff;
        }
        #completedAssessmentModal .btn-history {
            border-color: #dee2e6;
            color: #212529;
            font-weight: 500;
        }
        #completedAssessmentModal .btn-history:hover {
            background-color: #f8f9fa;
        }
        #completedAssessmentModal .btn-exit {
            color: #6c757d;
            font-weight: 500;
            text-decoration: none;
        }
        #completedAssessmentModal .btn-exit:hover {
            color: #212529;
        }
    </style>

    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h3 class="mb-4 text-center fw-bold">เลือกแบบประเมิน</h3>

                    <div class="container" style="max-width: 960px;">
                        <div class="row justify-content-center g-md-4">
                            @php
                                $assessments = [
                                    ['type' => 'smoking', 'icon' => 'fas fa-smoking', 'label' => 'การสูบบุหรี่'],
                                    ['type' => 'alcohol', 'icon' => 'fas fa-wine-glass-alt', 'label' => 'การดื่มแอลกอฮอล์'],
                                    ['type' => 'drug_use', 'icon' => 'fas fa-pills', 'label' => 'การใช้สารเสพติด'],
                                    ['type' => 'depression', 'icon' => 'fas fa-user-injured', 'label' => 'ภาวะซึมเศร้า'],
                                    ['type' => 'suicide_risk', 'icon' => 'fas fa-heart-broken', 'label' => 'ความเสี่ยงฆ่าตัวตาย']
                                ];
                            @endphp

                            @foreach ($assessments as $assessment)
                                @php
                                    // ตรวจสอบเงื่อนไขว่าควรจะ disable บัตรหรือไม่
                                    $type = $assessment['type'];
                                    $isDisabled = ($type === 'depression' || $type === 'suicide_risk') && $hasScheduledCase;
                                @endphp

                                <div class="col-10 col-sm-6 col-md-4 col-lg-2 mb-4">
                                    @if ($isDisabled)
                                        <div class="card text-center p-3 shadow-sm position-relative" style="background-color: #f8f9fa; opacity: 0.65; cursor: not-allowed;">
                                             <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                <i class="{{ $assessment['icon'] }}"></i>
                                                <h6 class="mt-2 mb-0">{{ $assessment['label'] }}</h6>
                                            </div>
                                            <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle p-1" style="font-size: 0.6rem;">
                                                รอนัดพบแพทย์
                                            </span>
                                        </div>
                                    @else
                                        <div class="card text-center p-3 shadow-sm assessment-card position-relative"
                                             data-type="{{ $assessment['type'] }}"
                                             data-completed="{{ in_array($assessment['type'], $completedAssessments) ? 'true' : 'false' }}"
                                             data-url-show="{{ route('assessment.show', ['soldier_id' => $soldier->id, 'type' => $assessment['type']]) }}">

                                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                <i class="{{ $assessment['icon'] }}"></i>
                                                <h6 class="mt-2 mb-0">{{ $assessment['label'] }}</h6>
                                            </div>

                                            @if(in_array($assessment['type'], $completedAssessments))
                                                <div class="completed-check" title="ทำแบบประเมินนี้แล้ว">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                    </div>
                </div>
            </div>
        </div>

        @include('themes.soldier.footersoldier')
    </div>

    {{-- ✅ [ปรับปรุงใหม่] Modal สำหรับแบบประเมินที่เคยทำแล้ว (ปุ่มอยู่คนละแถว) --}}
<div class="modal fade" id="completedAssessmentModal" tabindex="-1" aria-labelledby="completedAssessmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center position-relative">
                <div class="modal-icon-wrapper">
                    <i class="fas fa-clipboard-check"></i>
                </div>

                <h5 class="modal-title mb-2" id="completedAssessmentModalLabel">ทำแบบประเมินซ้ำ</h5>
                <p class="mb-4 text-muted">คุณต้องการดำเนินการใดต่อไปสำหรับแบบประเมินนี้?</p>

                <div class="d-grid gap-2 mb-3">
                    <a href="#" id="retakeAssessmentBtn" class="btn btn-retake p-2">
                        <i class="fas fa-redo-alt me-1"></i> ทำแบบประเมินซ้ำ
                    </a>
                    <a href="#" id="viewHistoryBtn" class="btn btn-history p-2">
                        <i class="fas fa-history me-1"></i> ดูประวัติการทำแบบประเมิน
                    </a>
                </div>

                <div class="text-center mb-3">
                     <button type="button" class="btn btn-exit" data-bs-dismiss="modal">
                         <i class="fas fa-times me-1"></i> ออก
                     </button>
                </div>

                <div class="d-flex align-items-center modal-info-text">
                    <i class="fas fa-info-circle flex-shrink-0"></i>
                    <span>การทำแบบประเมินซ้ำจะบันทึกผลลัพธ์ใหม่และแทนที่ผลลัพธ์เดิม</span>
                </div>
            </div>
        </div>
    </div>
</div>

    @include('themes.script')

    {{-- ✅ [เพิ่มใหม่] JavaScript สำหรับควบคุมการทำงาน --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // สร้าง Instance ของ Modal เก็บไว้
            const completedModalEl = document.getElementById('completedAssessmentModal');
            const completedModal = new bootstrap.Modal(completedModalEl);

            // หา Element ของปุ่มใน Modal
            const retakeBtn = document.getElementById('retakeAssessmentBtn');
            const historyBtn = document.getElementById('viewHistoryBtn');
            const historyUrl = "{{ route('assessment.history', $soldier->id) }}";

            // ดึงการ์ดทั้งหมด
            const assessmentCards = document.querySelectorAll('.assessment-card');

            // เพิ่ม Event Listener ให้กับการ์ดแต่ละใบ
            assessmentCards.forEach(card => {
                card.addEventListener('click', function() {
                    const isCompleted = this.dataset.completed === 'true';
                    const assessmentUrl = this.dataset.urlShow;

                    if (isCompleted) {
                        // ถ้าเคยทำแล้ว: ให้แสดง Modal

                        // 1. ตั้งค่าลิงก์ในปุ่มของ Modal ให้ถูกต้อง
                        retakeBtn.href = assessmentUrl;
                        historyBtn.href = historyUrl;

                        // 2. แสดง Modal
                        completedModal.show();
                    } else {
                        // ถ้ายังไม่เคยทำ: ให้ไปที่หน้านั้นทันที
                        window.location.href = assessmentUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>
