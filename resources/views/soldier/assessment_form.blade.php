<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $assessmentTitles = [
            'suicide_risk' => 'แบบประเมินความเสี่ยงการฆ่าตัวตาย', 'alcohol' => 'แบบประเมินการดื่มแอลกอฮอล์',
            'smoking' => 'แบบประเมินการสูบบุหรี่', 'depression' => 'แบบประเมินอาการซึมเศร้า',
            'drug_use' => 'แบบประเมินการใช้สารเสพติด',
        ];
    @endphp
    <title>{{ $assessmentTitles[$type] ?? 'แบบประเมิน' }}</title>

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

        /* --- Components --- */
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .form-check-input:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); }

        /* --- Buttons & Modals --- */
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-primary:hover { background-color: var(--bs-primary-hover); border-color: var(--bs-primary-hover); }
        .btn-light { background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color); color: var(--bs-body-color); }
        .btn-light:hover { background-color: #e2e8f0; border-color: #cbd5e1; }
        .modal-content { border: none; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
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
                <div class="container" style="max-width: 800px;">
                    @php
                        $assessmentIcons = ['suicide_risk' => 'fa-heart-broken', 'alcohol' => 'fa-wine-glass-alt', 'smoking' => 'fa-smoking', 'depression' => 'fa-theater-masks', 'drug_use' => 'fa-pills'];
                        $assessmentName = $assessmentTitles[$type] ?? 'แบบประเมิน';
                        $assessmentIcon = $assessmentIcons[$type] ?? 'fa-clipboard-list';
                    @endphp

                    <h2 class="fw-bold text-center mb-4">
                        <i class="fas {{ $assessmentIcon }} me-2 text-primary"></i> {{ $assessmentName }}
                    </h2>

                    <div class="card p-4 p-md-5" id="mainAssessmentForm" style="display: none;">
                        <form action="{{ route('assessment.submit', ['soldier_id' => $soldier->id, 'type' => $type]) }}" method="POST">
                            @csrf
                            @foreach($questions as $index => $question)
                                <div class="pb-4 mb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <p class="fw-bold fs-5">{{ $index + 1 }}. {{ $question->question_text }}</p>
                                    @foreach($question->options as $option)
                                        <div class="form-check fs-6 ps-4">
                                            <input type="radio" class="form-check-input" id="q{{$question->id}}_o{{$option->id}}" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required>
                                            <label class="form-check-label" for="q{{$question->id}}_o{{$option->id}}">{{ $option->option_text }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-lg btn-primary px-5">ส่งแบบประเมิน</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" class="d-none">@csrf</form>

    <div class="modal fade" id="assessmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="assessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h5" id="assessmentModalLabel">คำถามคัดกรอง</h5>
                </div>
                <div class="modal-body text-center p-4">
                    <p id="assessmentQuestion" class="fs-5 mb-4"></p>
                    <div class="d-grid gap-2 col-10 mx-auto">
                        <button class="btn btn-lg"></button> {{-- Yes --}}
                        <button class="btn btn-lg"></button> {{-- Used to, but quit --}}
                        <button class="btn btn-lg"></button> {{-- No --}}
                    </div>
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

        // --- Assessment Screening Logic (from original file) ---
        const currentType = @json($type);
        const soldierId = @json($soldier->id);
        const typesWithPopup = ['smoking', 'alcohol', 'drug_use'];
        const mainForm = document.getElementById('mainAssessmentForm');
        let assessmentModalInstance = null;

        function openAssessmentPopup(type) {
            let questionText = "", option1 = "", option2 = "", option3 = "";

            if (type === 'smoking') {
                questionText = "คุณสูบบุหรี่หรือไม่?";
                option1 = "ใช่, ปัจจุบันยังสูบอยู่";
                option2 = "เคยสูบ แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยสูบ";
            } else if (type === 'alcohol') {
                questionText = "คุณดื่มเครื่องดื่มแอลกอฮอล์หรือไม่?";
                option1 = "ใช่, ปัจจุบันยังดื่มอยู่";
                option2 = "เคยดื่ม แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยดื่ม";
            } else if (type === 'drug_use') {
                questionText = "คุณเคยใช้สารเสพติดหรือไม่?";
                option1 = "ใช่, ปัจจุบันยังใช้อยู่";
                option2 = "เคยใช้ แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยใช้";
            }

            document.getElementById('assessmentQuestion').innerText = questionText;
            const buttons = document.querySelectorAll("#assessmentModal .btn");

            buttons[0].innerText = option1;
            buttons[0].className = 'btn btn-lg btn-primary'; // "Yes" button is green
            buttons[0].onclick = () => setAssessmentStatus(1);

            buttons[1].innerText = option2;
            buttons[1].className = 'btn btn-lg btn-light';
            buttons[1].onclick = () => setAssessmentStatus(2);

            buttons[2].innerText = option3;
            buttons[2].className = 'btn btn-lg btn-light';
            buttons[2].onclick = () => setAssessmentStatus(0);

            const modalEl = document.getElementById('assessmentModal');
            if (modalEl) {
                assessmentModalInstance = new bootstrap.Modal(modalEl);
                assessmentModalInstance.show();
            }
        }

        function setAssessmentStatus(status) {
            // status: 0=No, 1=Yes, 2=Used to but quit
            // If status is 0 (No), skip the assessment.
            // If status is 1 (Yes) or 2 (Used to but quit), show the assessment.
            if (status === 0) {
                const skipUrl = `{{ url('/assessment') }}/${soldierId}/${currentType}/skip?status=${status}`;
                window.location.href = skipUrl;
            } else {
                if (assessmentModalInstance) {
                    assessmentModalInstance.hide();
                }
                mainForm.style.display = 'block';
            }
        }

        if (typesWithPopup.includes(currentType)) {
            openAssessmentPopup(currentType);
        } else {
            mainForm.style.display = 'block';
        }
    });
    </script>
</body>
</html>
