<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกแบบประเมิน</title>
    @include('themes.head')

<style>
    /* --- Theme Colors & Interactions --- */
    :root {
        --theme-secondary-bg: #F8F9FA;
        --theme-text-dark: #343a40;
        --theme-text-light: #6c757d;
        --theme-card-bg: #FFFFFF;
        --theme-border-color: #dee2e6;
        --theme-info-color: #A9C5C8;
        --theme-accent-color: #8E44AD;
        --theme-accent-darker: #7D3C98;
        --theme-accent-bg-subtle: rgba(142, 68, 173, 0.1);
    }

    /* --- General Styling --- */
    body {
        background-color: var(--theme-secondary-bg);
    }
    .content-wrapper {
        background-color: transparent;
    }
    h3, h5, h6 {
        color: var(--theme-text-dark);
        font-weight: bold;
    }

    /* --- Assessment Card Styling --- */
    .assessment-card {
        display: block;
        text-decoration: none;
        color: inherit;
        border: 1px solid var(--theme-border-color);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
        height: 100%;
        cursor: pointer;
        background-color: var(--theme-card-bg);
    }
    .assessment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
    }
    .assessment-card .card-body i {
        font-size: 2.5rem;
        color: var(--theme-info-color); /* Themed Icon Color */
        margin-bottom: 0.5rem;
    }

    /* Disabled Card Style */
    .card-disabled {
        background-color: #f8f9fa;
        opacity: 0.7;
        cursor: not-allowed;
    }
    .card-disabled .card-body i, .card-disabled .card-body h6 {
        color: #adb5bd;
    }

    .completed-check {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
    }

    /* --- Modal Styling --- */
    #completedAssessmentModal .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.2);
    }
    #completedAssessmentModal .modal-body {
        padding: 2rem;
    }
    #completedAssessmentModal .modal-icon-wrapper {
        width: 60px; height: 60px; border-radius: 50%;
        background-color: var(--theme-accent-bg-subtle);
        color: var(--theme-accent-color);
        display: inline-flex; align-items: center; justify-content: center;
        margin-bottom: 1.5rem;
    }
    #completedAssessmentModal .modal-icon-wrapper i {
        font-size: 1.75rem;
        color: var(--theme-accent-color);
    }
    #completedAssessmentModal .btn-retake {
        background-color: var(--theme-accent-color);
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
    }
    #completedAssessmentModal .btn-retake:hover {
        background-color: var(--theme-accent-darker);
    }
    #completedAssessmentModal .btn-history {
        border-radius: 8px;
    }

</style>
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h3 class="mb-4 text-center fw-bold">
                        <i class="fas fa-tasks me-2" style="color: var(--theme-info-color);"></i>เลือกแบบประเมิน
                    </h3>

                    <div class="container" style="max-width: 960px;">
                        <div class="row justify-content-center g-4">
                            @php
                                $assessments = [
                                    ['type' => 'smoking', 'icon' => 'fas fa-smoking', 'label' => 'การสูบบุหรี่'],
                                    ['type' => 'alcohol', 'icon' => 'fas fa-wine-glass-alt', 'label' => 'การดื่มแอลกอฮอล์'],
                                    ['type' => 'drug_use', 'icon' => 'fas fa-pills', 'label' => 'การใช้สารเสพติด'],
                                    ['type' => 'depression', 'icon' => 'fas fa-theater-masks', 'label' => 'ภาวะซึมเศร้า'],
                                    ['type' => 'suicide_risk', 'icon' => 'fas fa-heart-broken', 'label' => 'ความเสี่ยงฆ่าตัวตาย']
                                ];
                            @endphp

                            @foreach ($assessments as $assessment)
                                @php
                                    $type = $assessment['type'];
                                    $isDisabled = ($type === 'depression' || $type === 'suicide_risk') && $hasScheduledCase;
                                @endphp

                                <div class="col-6 col-sm-6 col-md-4 col-lg-3 mb-4">
                                    @if ($isDisabled)
                                        <div class="card text-center p-3 position-relative h-100 card-disabled">
                                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                <i class="{{ $assessment['icon'] }}"></i>
                                                <h6 class="mt-2 mb-0">{{ $assessment['label'] }}</h6>
                                            </div>
                                            <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle p-1" style="font-size: 0.6rem;">
                                                รอนัดพบแพทย์
                                            </span>
                                        </div>
                                    @else
                                        <div class="assessment-card text-center p-3 position-relative"
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

    <div class="modal fade" id="completedAssessmentModal" tabindex="-1" aria-labelledby="completedAssessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center position-relative">
                    <div class="modal-icon-wrapper">
                        <i class="fas fa-clipboard-check"></i>
                    </div>

                    <h5 class="modal-title mb-2" id="completedAssessmentModalLabel">ทำแบบประเมินแล้ว</h5>
                    <p class="mb-4 text-muted">คุณได้ทำแบบประเมินนี้ไปแล้ว ต้องการดำเนินการใดต่อ?</p>

                    <div class="d-grid gap-2 mb-3">
                        <a href="#" id="retakeAssessmentBtn" class="btn btn-retake p-2">
                            <i class="fas fa-redo-alt me-1"></i> ทำแบบประเมินซ้ำ
                        </a>
                        <a href="#" id="viewHistoryBtn" class="btn btn-outline-secondary btn-history p-2">
                            <i class="fas fa-history me-1"></i> ดูประวัติการทำทั้งหมด
                        </a>
                    </div>
                    <div class="text-center">
                       <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">
                           ยกเลิก
                       </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const completedModalEl = document.getElementById('completedAssessmentModal');
            const completedModal = new bootstrap.Modal(completedModalEl);

            const retakeBtn = document.getElementById('retakeAssessmentBtn');
            const historyBtn = document.getElementById('viewHistoryBtn');
            const historyUrl = "{{ route('assessment.history', $soldier->id) }}";

            const assessmentCards = document.querySelectorAll('.assessment-card');

            assessmentCards.forEach(card => {
                card.addEventListener('click', function() {
                    const isCompleted = this.dataset.completed === 'true';
                    const assessmentUrl = this.dataset.urlShow;

                    if (isCompleted) {
                        retakeBtn.href = assessmentUrl;
                        historyBtn.href = historyUrl;
                        completedModal.show();
                    } else {
                        window.location.href = assessmentUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>
