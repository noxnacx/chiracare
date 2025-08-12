<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกแบบประเมิน</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Custom Theme with Green Accent --- */
        :root {
            --bs-body-font-family: 'Sarabun', sans-serif;
            --bs-body-bg: #f8fafc;
            --bs-secondary-bg: #ffffff;
            --bs-tertiary-bg: #f1f5f9;
            --bs-border-color: #e2e8f0;
            --bs-body-color: #334155;
            --bs-heading-color: #1e293b;
            --bs-secondary-color: #64748b;
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

        /* --- Sidebar & Navbar --- */
        .sidebar .nav-link { color: #475569; font-weight: 500; margin-bottom: 0.25rem; display: flex; align-items: center; }
        .sidebar .nav-link .nav-icon { width: 30px; text-align: center; }
        .sidebar .nav-link:hover { background-color: var(--bs-tertiary-bg); }
        .sidebar .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .brand-link { display: flex; align-items: center; justify-content: center; text-decoration: none; }

        /* --- Assessment Card Styling --- */
        .assessment-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 0.75rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
            cursor: pointer;
            background-color: var(--bs-secondary-bg);
        }
        .assessment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .assessment-card-icon {
            font-size: 2.5rem;
            color: var(--bs-secondary-color);
        }
        .card-disabled {
            background-color: var(--bs-tertiary-bg);
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* --- Buttons & Modals --- */
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-primary:hover { background-color: var(--bs-primary-hover); border-color: var(--bs-primary-hover); }
        .btn-light { background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color); color: var(--bs-body-color); }
        .btn-light:hover { background-color: #e2e8f0; border-color: #cbd5e1; }
        .modal-content { border: none; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .icon-circle { width: 60px; height: 60px; background-color: rgba(var(--bs-primary-rgb), 0.1); color: var(--bs-primary); display: flex; align-items: center; justify-content: center; border-radius: 50%; }
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
                    <li class="nav-item"><a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-clipboard-list"></i><p class="ms-2">ทำแบบประเมิน</p></a></li>
                    <li class="nav-item"><a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p class="ms-2">ประวัติการทำแบบประเมิน</p></a></li>
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
                <div class="container" style="max-width: 960px;">
                    <div class="text-center mb-5">
                        <i class="fas fa-tasks fa-2x text-primary"></i>
                        <h2 class="h3 fw-bold mt-2">เลือกแบบประเมิน</h2>
                    </div>

                    <div class="row justify-content-center g-4">
                        @php
                            $assessments = [['type' => 'smoking', 'icon' => 'fas fa-smoking', 'label' => 'การสูบบุหรี่'], ['type' => 'alcohol', 'icon' => 'fas fa-wine-glass-alt', 'label' => 'การดื่มแอลกอฮอล์'], ['type' => 'drug_use', 'icon' => 'fas fa-pills', 'label' => 'การใช้สารเสพติด'], ['type' => 'depression', 'icon' => 'fas fa-theater-masks', 'label' => 'ภาวะซึมเศร้า'], ['type' => 'suicide_risk', 'icon' => 'fas fa-heart-broken', 'label' => 'ความเสี่ยงฆ่าตัวตาย']];
                        @endphp

                        @foreach ($assessments as $assessment)
                            @php
                                $type = $assessment['type'];
                                $isDisabled = ($type === 'depression' || $type === 'suicide_risk') && $hasScheduledCase;
                                $isCompleted = in_array($assessment['type'], $completedAssessments);
                            @endphp

                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="position-relative h-100">
                                    @if ($isDisabled)
                                        <div class="card text-center p-3 h-100 card-disabled d-flex flex-column justify-content-center">
                                            <i class="{{ $assessment['icon'] }} assessment-card-icon text-muted"></i>
                                            <h6 class="mt-2 mb-0 fw-normal text-muted">{{ $assessment['label'] }}</h6>
                                        </div>
                                        <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle p-1 border border-light" style="font-size: 0.6rem;">รอนัดพบแพทย์</span>
                                    @else
                                        <div class="assessment-card text-center p-3 h-100 d-flex flex-column justify-content-center" data-completed="{{ $isCompleted ? 'true' : 'false' }}" data-url-show="{{ route('assessment.show', ['soldier_id' => $soldier->id, 'type' => $assessment['type']]) }}">
                                            <i class="{{ $assessment['icon'] }} assessment-card-icon"></i>
                                            <h6 class="mt-2 mb-0 fw-bold">{{ $assessment['label'] }}</h6>
                                        </div>
                                        @if($isCompleted)
                                            <div class="position-absolute top-0 end-0 p-2" title="ทำแบบประเมินนี้แล้ว">
                                                <i class="fas fa-check-circle text-success fs-5"></i>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            </div>
    </div>

    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="modal fade" id="completedAssessmentModal" tabindex="-1" aria-labelledby="completedAssessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4 text-center">
                    <div class="icon-circle mx-auto mb-3"><i class="fas fa-clipboard-check fs-4 text-primary"></i></div>
                    <h5 class="modal-title h5 mb-2" id="completedAssessmentModalLabel">ทำแบบประเมินแล้ว</h5>
                    <p class="mb-4 text-muted">คุณได้ทำแบบประเมินนี้ไปแล้ว ต้องการดำเนินการใดต่อ?</p>
                    <div class="d-grid gap-2">
                        <a href="#" id="retakeAssessmentBtn" class="btn btn-primary"><i class="fas fa-redo-alt me-1"></i> ทำแบบประเมินซ้ำ</a>
                        <a href="#" id="viewHistoryBtn" class="btn btn-light"><i class="fas fa-history me-1"></i> ดูประวัติการทำทั้งหมด</a>
                    </div>
                    <button type="button" class="btn btn-link text-secondary mt-2" data-bs-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Toggle Logic ---
        const menuButton = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('active'));
        }

        // --- Assessment Card and Modal Logic (from original file) ---
        const completedModalEl = document.getElementById('completedAssessmentModal');
        if(completedModalEl) {
            const completedModal = new bootstrap.Modal(completedModalEl);
            const retakeBtn = document.getElementById('retakeAssessmentBtn');
            const historyBtn = document.getElementById('viewHistoryBtn');
            const historyUrl = "{{ route('assessment.history', ['soldierId' => $soldier->id]) }}";

            document.querySelectorAll('.assessment-card').forEach(card => {
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
        }
    });
    </script>
</body>
</html>
