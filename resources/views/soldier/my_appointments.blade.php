<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นัดหมายของฉัน</title>

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

        /* --- Custom Card Styling for Appointments --- */
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .appointment-card {
            border-left: 5px solid;
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        /* Color coding for appointment types */
        .appointment-card.type-physical { border-left-color: var(--bs-secondary-color); }
        .appointment-card.type-mental { border-left-color: var(--bs-primary); }

        .appointment-card .card-header {
            background-color: transparent;
        }
        .appointment-card.type-mental .card-header,
        .appointment-card.type-mental .header-icon {
            color: var(--bs-primary) !important;
        }

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
                    <li class="nav-item"><a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p class="ms-2">ประวัติการทำแบบประเมิน</p></a></li>
                    @if(isset($soldier))
                    <li class="nav-item"><a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-calendar-check"></i><p class="ms-2">นัดหมายของฉัน</p></a></li>
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
                    <h2 class="h4 fw-bold mb-4"><i class="fas fa-calendar-check me-2 text-primary"></i>นัดหมายของฉัน</h2>

                    @forelse($allAppointments as $appointment)
                        <div class="card appointment-card mb-4 {{ $appointment->type == 'สุขภาพกาย' ? 'type-physical' : 'type-mental' }}">
                            <div class="card-header fw-bold">
                                <i class="fas fa-calendar-day me-2 header-icon text-secondary"></i>
                                วันที่นัด: {{ \Carbon\Carbon::parse($appointment->appointment_date)->thaidate('j F Y') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong class="d-inline-block" style="min-width: 100px;">ประเภท:</strong> {{ $appointment->type }}</p>
                                        <p class="mb-2"><strong class="d-inline-block" style="min-width: 100px;">เวลา:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }} น.</p>
                                        <p class="mb-2"><strong class="d-inline-block" style="min-width: 100px;">สถานที่:</strong> {{ $appointment->appointment_location ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong class="d-inline-block" style="min-width: 100px;">ประเภทเคส:</strong>
                                            @if($appointment->case_type == 'critical')
                                                <span class="text-danger fw-bold">เคสเร่งด่วน</span>
                                            @else
                                                <span>เคสทั่วไป</span>
                                            @endif
                                        </p>
                                        <p class="mb-0"><strong class="d-inline-block" style="min-width: 100px;">หมายเหตุ:</strong> {{ $appointment->notes ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">คุณยังไม่มีประวัติการนัดหมาย</h4>
                            </div>
                        </div>
                    @endforelse

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
