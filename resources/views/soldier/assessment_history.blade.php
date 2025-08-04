<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการประเมิน</title>

    {{-- Bootstrap และ Icons --}}
    @include('themes.head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* --- Theme Colors --- */
    :root {
        --theme-bg: #f4f6f9;
        --theme-card-bg: #ffffff;
        --theme-border-color: #e3e6f0;
        --theme-text-dark: #343a40;
        --theme-text-light: #6c757d;

        /* Info Color (Blue-Gray) */
        --theme-info-color: #A9C5C8;
        --theme-info-bg-subtle: rgba(169, 197, 200, 0.15);
        --theme-info-focus-ring: rgba(169, 197, 200, 0.5);

        /* Accent Purple Color */
        --theme-accent-color: #8E44AD;
        --theme-accent-darker: #7D3C98;
        --theme-accent-bg-subtle: rgba(142, 68, 173, 0.1);
    }

    /* --- Body & Wrapper --- */
    body {
        background-color: var(--theme-bg);
    }
    .content-wrapper {
        background-color: transparent;
    }

    /* --- Cards --- */
    .filter-card {
        background-color: var(--theme-card-bg);
        border: none;
        border-radius: .5rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    .history-card {
        background-color: var(--theme-card-bg);
        border-radius: .5rem;
        border: 1px solid var(--theme-border-color);
        transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
    }
    .history-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }

    /* --- Assessment Elements --- */
    .assessment-icon {
        font-size: 1.5rem;
        width: 40px; height: 40px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
        background-color: var(--theme-info-bg-subtle); /* Themed */
        color: var(--theme-info-color); /* Themed */
    }
    .score-box {
        text-align: right;
    }
    h1.m-0 { /* Title color */
        color: var(--theme-text-dark);
    }

    /* --- Form Controls --- */
    .input-group-text {
        background-color: #f8f9fa; /* Kept light gray for contrast */
    }
    .form-select:focus {
        border-color: var(--theme-info-color); /* Themed */
        box-shadow: 0 0 0 0.25rem var(--theme-info-focus-ring); /* Themed */
    }

    /* --- Themed Buttons on this page only --- */
    .btn-outline-primary {
        color: var(--theme-info-color);
        border-color: var(--theme-info-color);
    }
    .btn-outline-primary:hover {
        background-color: var(--theme-info-color);
        border-color: var(--theme-info-color);
        color: white;
    }
    .btn-outline-success {
        color: var(--theme-accent-color);
        border-color: var(--theme-accent-color);
    }
    .btn-outline-success:hover {
        background-color: var(--theme-accent-color);
        border-color: var(--theme-accent-color);
        color: white;
    }

    /* --- Modal Styling --- */
    #completedAssessmentModal .modal-content {
        border-radius: .75rem;
        border: none;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.2);
    }
    #completedAssessmentModal .modal-body { padding: 2rem; }
    #completedAssessmentModal .modal-icon-wrapper {
        background-color: var(--theme-accent-bg-subtle); /* Themed */
        color: var(--theme-accent-color); /* Themed */
        width: 60px; height: 60px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        margin-bottom: 1.5rem;
    }
    #completedAssessmentModal .modal-icon-wrapper i {
        font-size: 1.75rem;
        color: var(--theme-accent-color); /* Themed */
    }
    #completedAssessmentModal .btn-retake {
        background-color: var(--theme-accent-color); /* Themed */
        color: #fff;
        font-weight: 500;
    }
    #completedAssessmentModal .btn-retake:hover {
        background-color: var(--theme-accent-darker); /* Themed */
        color: #fff;
    }
    #completedAssessmentModal .btn-history {
        border-color: var(--theme-border-color);
        color: var(--theme-text-dark);
        font-weight: 500;
    }
    #completedAssessmentModal .btn-history:hover {
        background-color: #f8f9fa;
    }
    #completedAssessmentModal .btn-exit {
        color: var(--theme-text-light);
    }

    /* Original Responsive CSS (Unchanged) */
    @media (max-width: 767.98px) {
        .history-card .card-body {
            flex-direction: column; align-items: flex-start !important;
        }
        .score-box {
            text-align: left; margin-top: 1rem; width: 100%;
        }
    }
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    @include('themes.soldier.navbarsoldier')
    @include('themes.soldier.menusoldier')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0"><i class="fas fa-history me-2" style="color: var(--theme-info-color);"></i>ประวัติการทำแบบประเมิน</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="card filter-card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-center">
                            <div class="col-md">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                                    <select class="form-select" name="range" onchange="this.form.submit()">
                                        <option value="">ระยะเวลาทั้งหมด</option>
                                        <option value="7" {{ request('range') == '7' ? 'selected' : '' }}>7 วันที่ผ่านมา</option>
                                        <option value="30" {{ request('range') == '30' ? 'selected' : '' }}>30 วันที่ผ่านมา</option>
                                        <option value="90" {{ request('range') == '90' ? 'selected' : '' }}>90 วันที่ผ่านมา</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-list-task"></i></span>
                                    <select class="form-select" name="type" onchange="this.form.submit()">
                                        <option value="">ทุกประเภท</option>
                                        <option value="smoking" {{ request('type') == 'smoking' ? 'selected' : '' }}>สูบบุหรี่</option>
                                        <option value="drug_use" {{ request('type') == 'drug_use' ? 'selected' : '' }}>ใช้สารเสพติด</option>
                                        <option value="alcohol" {{ request('type') == 'alcohol' ? 'selected' : '' }}>แอลกอฮอล์</option>
                                        <option value="depression" {{ request('type') == 'depression' ? 'selected' : '' }}>ภาวะซึมเศร้า</option>
                                        <option value="suicide_risk" {{ request('type') == 'suicide_risk' ? 'selected' : '' }}>เสี่ยงฆ่าตัวตาย</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @php
                    $assessmentDetails = [
                        'smoking' => ['label' => 'การสูบบุหรี่', 'icon' => 'bi-fire', 'max' => 10],
                        'alcohol' => ['label' => 'การดื่มแอลกอฮอล์', 'icon' => 'bi-cup-straw', 'max' => 40],
                        'drug_use' => ['label' => 'การใช้สารเสพติด', 'icon' => 'bi-capsule-pill', 'max' => 36],
                        'depression' => ['label' => 'ภาวะซึมเศร้า', 'icon' => 'bi-emoji-frown', 'max' => 27],
                        'suicide_risk' => ['label' => 'ความเสี่ยงฆ่าตัวตาย', 'icon' => 'bi-heartbreak', 'max' => 16],
                    ];
                    $riskBadges = [
                        'ต่ำ' => 'bg-success-subtle text-success-emphasis',
                        'ปานกลาง' => 'bg-warning-subtle text-warning-emphasis',
                        'สูง' => 'bg-danger-subtle text-danger-emphasis',
                    ];
                @endphp

                @forelse ($histories as $item)
                    @php
                        $assessmentType = optional($item->assessmentType)->assessment_type ?? $item->assessment_type;
                        $details = $assessmentDetails[$assessmentType] ?? ['label' => 'ไม่ระบุ', 'icon' => 'bi-question-circle', 'max' => 0];
                    @endphp
                    <div class="card history-card mb-3">
                        <div class="card-body d-md-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="assessment-icon"><i class="bi {{ $details['icon'] }}"></i></span>
                                </div>
                                <div>
                                    <h5 class="card-title fw-bold mb-0">{{ $details['label'] }}</h5>
                                    <small class="text-muted">ทำเมื่อ: {{ \Carbon\Carbon::parse($item->assessment_date)->thaidate('j M Y, H:i') }} น.</small>
                                </div>
                            </div>

                            <div class="score-box d-flex align-items-center gap-2">
                                <div class="text-end">
                                    <span class="fw-bold fs-5">{{ number_format($item->total_score, 0) }}<small class="text-muted">/{{$details['max']}}</small></span><br>
                                    <span class="badge rounded-pill {{ $riskBadges[$item->risk_level] ?? 'bg-secondary' }}">{{ $item->risk_level }}</span>
                                </div>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assessmentModal{{ $item->id }}">
                                    <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">ดูรายละเอียด</span>
                                </button>
                                <button class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#completedAssessmentModal"
                                        data-assessment-type="{{ $assessmentType }}"
                                        data-retake-url="{{ route('assessment.show', ['soldier_id' => $soldier->id, 'type' => $assessmentType]) }}">
                                    <i class="bi bi-arrow-clockwise"></i> <span class="d-none d-sm-inline">ทำซ้ำ</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center card py-5">
                       <div class="card-body">
                            <i class="bi bi-journal-x fs-1 text-muted"></i>
                            <h4 class="mt-3">ไม่พบข้อมูลการประเมิน</h4>
                            <p class="text-muted">ยังไม่มีประวัติการทำแบบประเมินในช่วงเวลาหรือประเภทที่เลือก</p>
                        </div>
                    </div>
                @endforelse

                <div class="d-flex justify-content-end mt-4">
                    {{ $histories->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </section>
    </div>

    {{-- Modals รายละเอียด (ของเดิม) --}}
    @foreach ($histories as $item)
    <div class="modal fade" id="assessmentModal{{ $item->id }}" tabindex="-1" aria-labelledby="assessmentModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดผลการประเมิน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <p><strong>ประเภท:</strong> {{ $assessmentDetails[optional($item->assessmentType)->assessment_type ?? 'N/A']['label'] ?? 'N/A' }}</p>
                   <p><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($item->assessment_date)->thaidate('j F Y, H:i') }} น.</p>
                   <p><strong>คะแนน:</strong> {{ $item->total_score }}</p>
                   <p><strong>ระดับความเสี่ยง:</strong> {{ $item->risk_level }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Modal for retaking assessment --}}
    <div class="modal fade" id="completedAssessmentModal" tabindex="-1" aria-labelledby="completedAssessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center position-relative p-4">
                    <div class="modal-icon-wrapper">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h5 class="modal-title mb-2" id="completedAssessmentModalLabel">ทำแบบประเมินซ้ำ</h5>
                    <p class="mb-4 text-muted">คุณต้องการดำเนินการใดต่อไปสำหรับแบบประเมินนี้?</p>
                    <div class="d-grid gap-2 mb-3">
                        <a href="#" id="retakeAssessmentBtn" class="btn btn-retake p-2">
                            <i class="fas fa-redo-alt me-1"></i> ทำแบบประเมินซ้ำ
                        </a>
                        <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" id="viewHistoryBtn" class="btn btn-history p-2">
                            <i class="fas fa-history me-1"></i> ดูประวัติการทำแบบประเมิน
                        </a>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-exit" data-bs-dismiss="modal">
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
            if(completedModalEl) {
                const completedModal = new bootstrap.Modal(completedModalEl);
                const retakeBtn = document.getElementById('retakeAssessmentBtn');

                completedModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const retakeUrl = button.getAttribute('data-retake-url');

                    retakeBtn.setAttribute('href', retakeUrl);
                });
            }
        });
    </script>
</body>
</html>
