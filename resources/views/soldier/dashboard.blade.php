<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

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
            --bs-success-subtle: #d1fae5;
            --bs-success-emphasis: #065f46;
            --mental-health-color: #8b5cf6; /* Violet */
            --mental-health-bg-subtle: #ede9fe;
            --mental-health-emphasis: #5b21b6;
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

        /* --- Dashboard Specific Styling --- */
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .profile-header {
            background: linear-gradient(135deg, var(--bs-primary), #34d399);
            color: white;
            border-radius: 0.75rem;
        }
        .list-group-item { border-color: var(--bs-border-color); }
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
                    <li class="nav-item"><a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-tachometer-alt"></i><p class="ms-2">Dashboard</p></a></li>
                    <li class="nav-item"><a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-list"></i><p class="ms-2">ทำแบบประเมิน</p></a></li>
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
                <div class="container-fluid">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="profile-header p-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-1 text-white">ยินดีต้อนรับ, {{ $soldier->s_rank }} {{ $soldier->s_name }} {{ $soldier->s_surname }}</h3>
                                <p class="mb-0 text-white-50">ภาพรวมสุขภาพและนัดหมายของคุณ</p>
                            </div>
                            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                                <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="btn btn-light fw-bold"><i class="fas fa-poll me-2"></i>ทำแบบประเมิน</a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mb-4 d-flex flex-column">
                            <div class="card flex-grow-1">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="h6 card-title mb-0 fw-bold">การนัดหมายทั้งหมด</h5>
                                    <a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @forelse($allAppointments as $app)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <p class="mb-1 fw-semibold">
                                                        @if($app->type == 'กาย')
                                                            <span class="badge text-bg-success me-2">กาย</span>
                                                        @else
                                                            <span class="badge me-2" style="background-color: var(--mental-health-color);">จิตใจ</span>
                                                        @endif
                                                        {{ \Str::limit($app->description, 25) }}
                                                    </p>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($app->date)->thaidate('j M y') }}</small>
                                                </div>
                                                <small class="text-muted">สถานะ: {{ $app->status }}</small>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ไม่มีข้อมูลนัดหมาย</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 d-flex flex-column">
                            <div class="card flex-grow-1">
                                <div class="card-header"><h5 class="h6 card-title mb-0 fw-bold">ประวัติการรักษาล่าสุด</h5></div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @forelse($treatmentHistory as $history)
                                            <li class="list-group-item px-0">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <p class="mb-1">
                                                        @if($history->type == 'กาย')
                                                            <span class="badge bg-success-subtle text-success-emphasis me-2">กาย</span>
                                                        @else
                                                            <span class="badge me-2" style="background-color: var(--mental-health-bg-subtle); color: var(--mental-health-emphasis);">จิตใจ</span>
                                                        @endif
                                                        {{ \Str::limit($history->description, 25) }}
                                                    </p>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($history->date)->thaidate('j M y') }}</small>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ไม่มีประวัติการรักษา</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4 d-flex flex-column">
                             <div class="card flex-grow-1">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="h6 card-title mb-0 fw-bold">ผลการประเมินล่าสุด</h5>
                                    <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="btn btn-sm btn-outline-secondary">ดูประวัติ</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @php $typeLabels = ['smoking' => 'การสูบบุหรี่', 'alcohol' => 'การดื่มสุรา', 'drug_use' => 'การใช้สารเสพติด', 'depression' => 'ภาวะซึมเศร้า', 'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย']; @endphp
                                        @forelse ($recentHistories as $item)
                                            @php
                                                $type = optional($item->assessmentType)->assessment_type;
                                                $label = $typeLabels[$type] ?? $type;
                                            @endphp
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span>{{ $label }}</span>
                                                <span class="fw-bold">{{ $item->assessment_level }}</span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ยังไม่มีประวัติการทำแบบประเมิน</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
    </div>

    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" class="d-none">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Toggle Logic ---
        const menuButton = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('active'));
        }
    });
    </script>
</body>
</html>
