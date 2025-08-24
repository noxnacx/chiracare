<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการประเมิน</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Custom Theme with Green Accent --- */
        :root {
            --bs-body-font-family: 'Sarabun', sans-serif;
            --bs-body-bg: #f8fafc; /* Light Gray Background */
            --bs-secondary-bg: #ffffff;
            --bs-tertiary-bg: #f1f5f9;
            --bs-border-color: #e2e8f0;
            --bs-body-color: #334155; /* Dark Slate for Text */
            --bs-heading-color: #1e293b; /* Darker Slate for Headings */
            --bs-secondary-color: #64748b;

            /* New Primary Color: Green for Health & Calmness */
            --bs-primary: #10b981; /* Emerald Green */
            --bs-primary-rgb: 16, 185, 129;
            --bs-primary-hover: #059669; /* Darker Green */

            --bs-border-radius: 0.5rem;
        }

        /* --- Layout --- */
        .main-container { display: flex; height: 100vh; min-height: 100vh; }
        .sidebar { width: 256px; flex-shrink: 0; background-color: var(--bs-secondary-bg); box-shadow: 0 0 15px rgba(0,0,0,0.1); transition: transform 0.3s ease-in-out; }
        .main-content { flex-grow: 1; overflow-y: auto; }
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1040; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }

        /* --- Sidebar & Navbar Customization --- */
        .sidebar .nav-link { color: #475569; font-weight: 500; margin-bottom: 0.25rem; display: flex; align-items: center; }
        .sidebar .nav-link .nav-icon { width: 30px; text-align: center; }
        .sidebar .nav-link:hover { background-color: var(--bs-tertiary-bg); }
        .sidebar .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .brand-link { display: flex; align-items: center; justify-content: center; text-decoration: none; }

        /* --- Card & Component Styling --- */
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .history-card { transition: transform 0.2s ease-out, box-shadow 0.2s ease-out; }
        .history-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .icon-circle { width: 48px; height: 48px; background-color: var(--bs-tertiary-bg); color: var(--bs-body-color); display: flex; align-items: center; justify-content: center; border-radius: 50%; }
        .btn-outline-primary { --bs-btn-color: var(--bs-secondary-color); --bs-btn-border-color: #cbd5e1; --bs-btn-hover-bg: var(--bs-tertiary-bg); --bs-btn-hover-border-color: #cbd5e1; --bs-btn-hover-color: var(--bs-body-color); --bs-btn-active-bg: #e2e8f0; --bs-btn-active-border-color: #cbd5e1; }
        .form-control:focus, .form-select:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); }

        /* --- Pagination & Modal --- */
        .pagination .page-item.active .page-link { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .pagination .page-link { color: var(--bs-primary); } .pagination .page-link:hover { color: var(--bs-primary-hover); }
        .modal-content { border: none; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-primary:hover { background-color: var(--bs-primary-hover); border-color: var(--bs-primary-hover); }
        .btn-light { background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color); color: var(--bs-body-color); }
        .btn-light:hover { background-color: #e2e8f0; border-color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar d-flex flex-column" id="sidebar">
            <div class="p-3 border-bottom h-auto">
                <a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="brand-link">
                    <img src="{{ URL::asset('dist/img/AdminLTELogo.png')}}" alt="Chiracare Logo" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                    <span class="h5 mb-0 fw-bold text-dark">Chiracare</span>
                </a>
            </div>
            <div class="flex-grow-1 p-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('profile.inv.soldier', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-user-circle"></i><p class="ms-2">หน้าแรก (โปรไฟล์)</p></a></li>
                    <li class="nav-item"><a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p class="ms-2">Dashboard</p></a></li>
                    <li class="nav-item"><a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-list"></i><p class="ms-2">ทำแบบประเมิน</p></a></li>
                    <li class="nav-item"><a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-clipboard-check"></i><p class="ms-2">ประวัติการทำแบบประเมิน</p></a></li>
                    @if(isset($soldier))
                    <li class="nav-item"><a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p class="ms-2">นัดหมายของฉัน</p></a></li>
                    @endif
                    <li class="nav-item"><a href="{{ route('soldier.edit_personal_info', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-user-edit"></i><p class="ms-2">แก้ไขข้อมูลส่วนตัว</p></a></li>
                </ul>
            </div>
            <div class="p-3 border-top h-auto mt-auto">
                <a href="{{ route('soldier.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="nav-icon fas fa-sign-out-alt"></i><p class="ms-2">ออกจากระบบ</p></a>
            </div>
        </aside>
        <div class="main-content d-flex flex-column">
            <nav class="navbar bg-white">
                <div class="container-fluid">
                    <button class="btn border-0 d-md-none" type="button" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <div class="ms-auto fw-medium">พลฯ {{ $soldier->first_name }} {{ $soldier->last_name }}</div>
                </div>
            </nav>
            <div class="flex-grow-1 p-3 p-md-4">
                <div class="container-fluid">
                    <h2 class="h4 fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i>ประวัติการทำแบบประเมิน</h2>

                    <div class="card mb-4"><div class="card-body"><form method="GET" class="row g-3 align-items-center">
                        <div class="col-md"><div class="input-group"><span class="input-group-text"><i class="bi bi-calendar3"></i></span><select class="form-select" name="range" onchange="this.form.submit()"><option value="">ระยะเวลาทั้งหมด</option><option value="7" {{ request('range') == '7' ? 'selected' : '' }}>7 วันที่ผ่านมา</option><option value="30" {{ request('range') == '30' ? 'selected' : '' }}>30 วันที่ผ่านมา</option><option value="90" {{ request('range') == '90' ? 'selected' : '' }}>90 วันที่ผ่านมา</option></select></div></div>
                        <div class="col-md"><div class="input-group"><span class="input-group-text"><i class="bi bi-list-task"></i></span><select class="form-select" name="type" onchange="this.form.submit()"><option value="">ทุกประเภท</option><option value="smoking" {{ request('type') == 'smoking' ? 'selected' : '' }}>สูบบุหรี่</option><option value="drug_use" {{ request('type') == 'drug_use' ? 'selected' : '' }}>ใช้สารเสพติด</option><option value="alcohol" {{ request('type') == 'alcohol' ? 'selected' : '' }}>แอลกอฮอล์</option><option value="depression" {{ request('type') == 'depression' ? 'selected' : '' }}>ภาวะซึมเศร้า</option><option value="suicide_risk" {{ request('type') == 'suicide_risk' ? 'selected' : '' }}>เสี่ยงฆ่าตัวตาย</option></select></div></div>
                    </form></div></div>

                    @php
                        $assessmentDetails = ['smoking' => ['label' => 'การสูบบุหรี่', 'icon' => 'bi-fire', 'max' => 10], 'alcohol' => ['label' => 'การดื่มแอลกอฮอล์', 'icon' => 'bi-cup-straw', 'max' => 40], 'drug_use' => ['label' => 'การใช้สารเสพติด', 'icon' => 'bi-capsule-pill', 'max' => 36], 'depression' => ['label' => 'ภาวะซึมเศร้า', 'icon' => 'bi-emoji-frown', 'max' => 27], 'suicide_risk' => ['label' => 'ความเสี่ยงฆ่าตัวตาย', 'icon' => 'bi-heartbreak', 'max' => 16]];
                        $riskBadges = ['ต่ำ' => 'bg-success-subtle text-success-emphasis', 'ปานกลาง' => 'bg-warning-subtle text-warning-emphasis', 'สูง' => 'bg-danger-subtle text-danger-emphasis'];
                    @endphp

                    @forelse ($histories as $item)
                        @php
                            $assessmentType = optional($item->assessmentType)->assessment_type ?? $item->assessment_type;
                            $details = $assessmentDetails[$assessmentType] ?? ['label' => 'ไม่ระบุ', 'icon' => 'bi-question-circle', 'max' => 0];
                        @endphp
                        <div class="card history-card mb-3"><div class="card-body d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                            <div class="d-flex align-items-center mb-3 mb-sm-0"><div class="icon-circle me-3 flex-shrink-0"><i class="bi {{ $details['icon'] }} fs-4"></i></div><div><h5 class="h6 fw-bold mb-0">{{ $details['label'] }}</h5><small class="text-muted">ทำเมื่อ: {{ \Carbon\Carbon::parse($item->assessment_date)->thaidate('j M Y, H:i') }} น.</small></div></div>
                            <div class="d-flex align-items-center justify-content-between justify-content-sm-end gap-2">
                                <div class="text-end">
                                    <span class="fw-bold fs-5">{{ number_format($item->total_score, 0) }}<small class="text-muted">/{{$details['max']}}</small></span><br>
                                    {{-- The risk level will now be calculated by JavaScript --}}
                                    <span class="badge rounded-pill risk-level-badge" data-type="{{ $assessmentType }}" data-score="{{ $item->total_score }}"></span>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assessmentModal{{ $item->id }}" title="ดูรายละเอียด"><i class="bi bi-eye"></i></button>
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#completedAssessmentModal" data-retake-url="{{ route('assessment.show', ['soldier_id' => $soldier->id, 'type' => $assessmentType]) }}" title="ทำซ้ำ"><i class="bi bi-arrow-clockwise"></i></button>
                                </div>
                            </div>
                        </div></div>
                    @empty
                        <div class="card text-center py-5"><div class="card-body"><i class="bi bi-journal-x fs-1 text-muted"></i><h4 class="mt-3">ไม่พบข้อมูลการประเมิน</h4><p class="text-muted">ยังไม่มีประวัติในช่วงเวลาหรือประเภทที่เลือก</p></div></div>
                    @endforelse

                    <div class="mt-4 d-flex justify-content-end">
                        {{ $histories->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" class="d-none">@csrf</form>
    @foreach ($histories as $item)
    <div class="modal fade" id="assessmentModal{{ $item->id }}" tabindex="-1" aria-labelledby="assessmentModalLabel{{ $item->id }}" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">รายละเอียดผลการประเมิน</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body">
            <p><strong>ประเภท:</strong> {{ $assessmentDetails[optional($item->assessmentType)->assessment_type ?? 'N/A']['label'] ?? 'N/A' }}</p>
            <p><strong>วันที่:</strong> {{ \Carbon\Carbon::parse($item->assessment_date)->thaidate('j F Y, H:i') }} น.</p>
            <p><strong>คะแนน:</strong> {{ $item->total_score }}</p>
            {{-- The risk level will also be calculated by JavaScript here --}}
            <p><strong>ระดับความเสี่ยง:</strong> <span class="badge rounded-pill risk-level-badge" data-type="{{ optional($item->assessmentType)->assessment_type }}" data-score="{{ $item->total_score }}"></span></p>
        </div>
    </div></div></div>
    @endforeach

    <div class="modal fade" id="completedAssessmentModal" tabindex="-1" aria-labelledby="completedAssessmentModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-body p-4 text-center">
            <div class="icon-circle mx-auto mb-3"><i class="fas fa-clipboard-check fs-4 text-primary"></i></div>
            <h5 class="modal-title h5 mb-2" id="completedAssessmentModalLabel">ทำแบบประเมินซ้ำ</h5>
            <p class="mb-4 text-muted">คุณต้องการทำแบบประเมินนี้อีกครั้งหรือไม่?</p>
            <div class="d-grid gap-2">
                <a href="#" id="retakeAssessmentBtn" class="btn btn-primary"><i class="fas fa-redo-alt me-1"></i> ยืนยัน ทำแบบประเมินซ้ำ</a>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Toggle Logic ---
        const menuButton = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('active'));
        }

        // --- Retake Modal Logic ---
        const completedModalEl = document.getElementById('completedAssessmentModal');
        if(completedModalEl) {
            const retakeBtn = document.getElementById('retakeAssessmentBtn');
            completedModalEl.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const retakeUrl = button.getAttribute('data-retake-url');
                retakeBtn.setAttribute('href', retakeUrl);
            });
        }

        // --- [START OF FIX] ---
        // This function now contains the correct risk level logic from AssessmentController.php
        function getRiskLevel(type, score) {
            score = parseInt(score, 10);
            const riskLevels = {
                low: { text: 'ต่ำ', class: 'bg-success-subtle text-success-emphasis' },
                medium: { text: 'ปานกลาง', class: 'bg-warning-subtle text-warning-emphasis' },
                high: { text: 'สูง', class: 'bg-danger-subtle text-danger-emphasis' }
            };

            switch (type) {
                case 'depression':
                    if (score >= 13) return riskLevels.high;
                    if (score >= 7) return riskLevels.medium;
                    return riskLevels.low;
                case 'suicide_risk':
                    if (score >= 10) return riskLevels.high;
                    if (score >= 5) return riskLevels.medium;
                    return riskLevels.low;
                case 'smoking':
                    if (score >= 6) return riskLevels.high;
                    if (score >= 4) return riskLevels.medium;
                    return riskLevels.low;
                case 'alcohol':
                    if (score >= 20) return riskLevels.high;
                    if (score >= 16) return riskLevels.medium;
                    return riskLevels.low;
                case 'drug_use':
                    if (score >= 27) return riskLevels.high;
                    if (score >= 4) return riskLevels.medium;
                    return riskLevels.low;
                default:
                    return { text: 'N/A', class: 'bg-secondary-subtle' };
            }
        }

        // This script will find all risk level badges and apply the correct text and color
        const badges = document.querySelectorAll('.risk-level-badge');
        badges.forEach(badge => {
            const type = badge.dataset.type;
            const score = badge.dataset.score;
            if (type && score) {
                const risk = getRiskLevel(type, score);
                badge.textContent = risk.text;
                // Clear old classes before adding the new one
                badge.className = 'badge rounded-pill risk-level-badge';
                badge.classList.add(...risk.class.split(' '));
            }
        });
        // --- [END OF FIX] ---
    });
    </script>
</body>
</html>
