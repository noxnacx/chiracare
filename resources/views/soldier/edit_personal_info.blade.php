<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
        .form-control, .form-select { border-color: var(--bs-border-color); }
        .form-control:focus, .form-select:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); }
        .image-preview { border: 2px dashed var(--bs-border-color); padding: 0.5rem; }
        .alert-success { background-color: rgba(var(--bs-primary-rgb), 0.1); border-color: rgba(var(--bs-primary-rgb), 0.2); color: var(--bs-primary-hover); }

        /* --- Button Styling --- */
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
                    <li class="nav-item"><a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p class="ms-2">ประวัติการทำแบบประเมิน</p></a></li>
                    @if(isset($soldier))
                    <li class="nav-item"><a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p class="ms-2">นัดหมายของฉัน</p></a></li>
                    @endif
                    <li class="nav-item"><a href="{{ route('soldier.edit_personal_info', ['id' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-user-edit"></i><p class="ms-2">แก้ไขข้อมูลส่วนตัว</p></a></li>
                 </ul>
            </div>
            <div class="p-3 border-top h-auto mt-auto">
                <a href="{{ route('soldier.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="nav-icon fas fa-sign-out-alt"></i><p class="ms-2">ออกจากระบบ</p>
                </a>
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
                    <h2 class="h4 fw-bold mb-4"><i class="fas fa-user-edit me-2 text-primary"></i>แก้ไขข้อมูลส่วนตัว</h2>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('soldier.update_personal_info', $soldier->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">ชื่อจริง</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $soldier->first_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">นามสกุล</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $soldier->last_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="weight_kg" class="form-label">น้ำหนัก (กก.)</label>
                                        <input type="number" step="0.1" class="form-control" id="weight_kg" name="weight_kg" value="{{ old('weight_kg', $soldier->weight_kg) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="height_cm" class="form-label">ส่วนสูง (ซม.)</label>
                                        <input type="number" class="form-control" id="height_cm" name="height_cm" value="{{ old('height_cm', $soldier->height_cm) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="underlying_diseases" class="form-label">โรคประจำตัว</label>
                                        <input type="text" class="form-control" id="underlying_diseases" name="underlying_diseases" placeholder="ระบุ 'ไม่มี' หากไม่มีโรคประจำตัว" value="{{ old('underlying_diseases', $soldier->underlying_diseases) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="medical_allergy_food_history" class="form-label">ประวัติแพ้ยา / อาหาร</label>
                                        <input type="text" class="form-control" id="medical_allergy_food_history" name="medical_allergy_food_history" placeholder="ระบุ 'ไม่มี' หากไม่มีประวัติแพ้" value="{{ old('medical_allergy_food_history', $soldier->medical_allergy_food_history) }}">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="soldier_image" class="form-label">อัปโหลดรูปภาพใหม่ (ถ้าต้องการ)</label>
                                        <input type="file" name="soldier_image" id="soldier_image" class="form-control">
                                        @if($soldier->soldier_image)
                                            <div class="mt-3">
                                                <label class="form-label d-block">รูปภาพปัจจุบัน:</label>
                                                <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}" alt="Current Image" class="mt-2 rounded image-preview" style="max-width: 150px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ url()->previous() }}" class="btn btn-light me-2">
                                        <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                                    </button>
                                </div>
                            </form>
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
