<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการประเมิน</title>

    {{-- Bootstrap และ Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    {{-- ✅ 1. เพิ่ม CSS สำหรับ Modal ใหม่ --}}
    <style>
        body {
            background-color: #f4f6f9;
        }
        .content-wrapper {
            background-color: transparent;
        }
        .filter-card {
            background-color: #ffffff;
            border: none;
            border-radius: .5rem;
            box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        }
        .history-card {
            border-radius: .5rem;
            border: 1px solid #e3e6f0;
            transition: transform .2s ease-in-out, box-shadow .2s ease-in-out;
        }
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .assessment-icon {
            font-size: 1.5rem;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #f4f4f4;
            color: #6c757d;
        }
        .score-box {
            text-align: right;
        }

        /* CSS สำหรับ Modal ที่คัดลอกมา */
        #completedAssessmentModal .modal-content {
            border-radius: .75rem; border: none; box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.2);
        }
        #completedAssessmentModal .modal-body { padding: 2rem; }
        #completedAssessmentModal .btn-close { position: absolute; top: 1rem; right: 1rem; }
        #completedAssessmentModal .modal-icon-wrapper {
            width: 60px; height: 60px; border-radius: 50%; background-color: #e9ecef;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;
        }
        #completedAssessmentModal .modal-icon-wrapper i { font-size: 1.75rem; color: #495057; }
        #completedAssessmentModal .modal-title { font-weight: 600; font-size: 1.25rem; }
        #completedAssessmentModal .modal-info-text {
            font-size: 0.8rem; color: #6c757d; background-color: #f8f9fa;
            padding: 0.5rem 0.75rem; border-radius: .25rem; text-align: left;
        }
        #completedAssessmentModal .modal-info-text i { font-size: 0.8rem; color: #6c757d; margin-right: 0.5rem; }
        #completedAssessmentModal .btn-retake {
            background-color: #212529; color: #fff; font-weight: 500;
        }
        #completedAssessmentModal .btn-retake:hover { background-color: #343a40; color: #fff; }
        #completedAssessmentModal .btn-history { border-color: #dee2e6; color: #212529; font-weight: 500; }
        #completedAssessmentModal .btn-history:hover { background-color: #f8f9fa; }
        #completedAssessmentModal .btn-exit { color: #6c757d; font-weight: 500; text-decoration: none; }
        #completedAssessmentModal .btn-exit:hover { color: #212529; }

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

<body>
    @include('themes.soldier.navbarsoldier')
    @include('themes.soldier.menusoldier')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">ประวัติการทำแบบประเมิน</h1>
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
                                    <small class="text-muted">ทำเมื่อ: {{ \Carbon\Carbon::parse($item->assessment_date)->format('j M Y, H:i') }} น.</small>
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
                                {{-- ✅ 2. แก้ไขปุ่ม "ทำซ้ำ" ให้เรียก Modal ที่ถูกต้อง --}}
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
        {{-- ... โค้ดส่วนนี้เหมือนเดิม ... --}}
    @endforeach

    {{-- ✅ 3. วาง Modal ใหม่ที่คัดลอกมา --}}
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
                        <span>การทำแบบประเมินซ้ำจะบันทึกผลลัพธ์ใหม่และจะไม่ลบผลลัพธ์เดิม</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ✅ 4. เพิ่ม JavaScript ใหม่สำหรับควบคุม Modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const completedModalEl = document.getElementById('completedAssessmentModal');
            if(completedModalEl) {
                const completedModal = new bootstrap.Modal(completedModalEl);

                const retakeBtn = document.getElementById('retakeAssessmentBtn');
                const historyBtn = document.getElementById('viewHistoryBtn');

                completedModalEl.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const retakeUrl = button.getAttribute('data-retake-url');
                    const historyUrl = "{{ route('assessment.history', ['soldierId' => $soldier->id]) }}";

                    // ตั้งค่าลิงก์ในปุ่มของ Modal ให้ถูกต้อง
                    retakeBtn.setAttribute('href', retakeUrl);
                    historyBtn.setAttribute('href', historyUrl);
                });
            }
        });
    </script>
</body>
</html>
