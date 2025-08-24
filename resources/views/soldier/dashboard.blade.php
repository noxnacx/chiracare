<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สรุปผลการประเมิน</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bs-body-font-family: 'Sarabun', sans-serif;
            --bs-body-bg: #f8fafc;
            --bs-secondary-bg: #ffffff;
            --bs-tertiary-bg: #f1f5f9;
            --bs-border-color: #e2e8f0;
            --bs-body-color: #334155;
            --bs-heading-color: #1e293b;
            --bs-primary: #8b5cf6; /* Violet */
            --bs-primary-rgb: 139, 92, 246;
            --bs-primary-hover: #7c3aed; /* Darker Violet */
            --bs-border-radius: 0.5rem;
        }
        .main-container { display: flex; height: 100vh; min-height: 100vh; }
        .sidebar { width: 256px; flex-shrink: 0; background-color: var(--bs-secondary-bg); box-shadow: 0 0 15px rgba(0,0,0,0.1); transition: transform 0.3s ease-in-out; }
        .main-content { flex-grow: 1; overflow-y: auto; }
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1040; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }
        .sidebar .nav-link { color: #475569; font-weight: 500; margin-bottom: 0.25rem; display: flex; align-items: center; }
        .sidebar .nav-link .nav-icon { width: 30px; text-align: center; }
        .sidebar .nav-link:hover { background-color: var(--bs-tertiary-bg); }
        .sidebar .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-control:focus, .form-select:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25); }
    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar d-flex flex-column" id="sidebar">
            <div class="p-3 border-bottom h-auto">
                <a href="#" class="d-flex align-items-center justify-content-center text-decoration-none">
                    <img src="{{ URL::asset('dist/img/AdminLTELogo.png')}}" alt="Chiracare Logo" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                    <span class="h5 mb-0 fw-bold text-dark">Chiracare MH</span>
                </a>
            </div>
            <div class="flex-grow-1 p-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('mental_health.dashboard') }}" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p class="ms-2">Dashboard</p></a></li>
                    <li class="nav-item"><a href="{{ route('mental_health.summary') }}" class="nav-link active"><i class="nav-icon fas fa-chart-bar"></i><p class="ms-2">สรุปผล</p></a></li>
                    <li class="nav-item"><a href="{{ route('mental_health.completed_history') }}" class="nav-link"><i class="nav-icon fas fa-history"></i><p class="ms-2">ประวัติที่เสร็จสิ้น</p></a></li>
                </ul>
            </div>
            <div class="p-3 border-top h-auto mt-auto">
                <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="nav-icon fas fa-sign-out-alt"></i><p class="ms-2">ออกจากระบบ</p></a>
            </div>
        </aside>

        <div class="main-content d-flex flex-column">
            <nav class="navbar bg-white">
                <div class="container-fluid">
                    <button class="btn border-0 d-md-none" type="button" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <div class="ms-auto fw-medium">ผู้ใช้งาน: {{ Auth::user()->name }}</div>
                </div>
            </nav>

            <div class="flex-grow-1 p-3 p-md-4">
                <div class="container-fluid">
                    <h2 class="fw-bold mb-4"><i class="fas fa-chart-bar me-2"></i>สรุปผลการประเมิน</h2>

                    <div class="card">
                        <div class="card-header bg-white py-3">
                            <form method="GET" action="{{ route('mental_health.summary') }}">
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                                            @php $perPageOptions = [5, 10, 15, 25, 50]; @endphp
                                            @foreach ($perPageOptions as $option)
                                                <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto"><label class="col-form-label">รายการต่อหน้า</label></div>
                                    <div class="col-sm-4 ms-auto">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ, นามสกุล, หน่วย..." value="{{ request('search') }}">
                                            <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>หน่วย</th>
                                            <th>ผลประเมินล่าสุด</th>
                                            <th>วันที่ประเมิน</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- ใช้ @forelse เพื่อจัดการกรณีไม่มีข้อมูล --}}
                                        @forelse($soldiers as $index => $soldier)
                                            <tr>
                                                <td>{{ $soldiers->firstItem() + $index }}</td>
                                                <td>{{ $soldier->s_rank }} {{ $soldier->s_name }} {{ $soldier->s_surname }}</td>
                                                <td>{{ $soldier->unit->unit_name ?? 'N/A' }}</td>
                                                <td>
                                                    {{-- สมมติว่ามีข้อมูลผลประเมินล่าสุดใน $soldier object --}}
                                                    <span class="badge bg-warning">{{ $soldier->latest_assessment_level ?? 'รอประเมิน' }}</span>
                                                </td>
                                                <td>
                                                    {{-- สมมติว่ามีข้อมูลวันที่ประเมินล่าสุดใน $soldier object --}}
                                                    {{ $soldier->latest_assessment_date ? \Carbon\Carbon::parse($soldier->latest_assessment_date)->thaidate('j M y') : '-'}}
                                                </td>
                                                <td>
                                                    <a href="{{ route('mental_health.individual_history', ['soldierId' => $soldier->id]) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye me-1"></i>ดูรายละเอียด
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูล</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($soldiers->hasPages())
                        <div class="card-footer bg-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        แสดง {{ $soldiers->firstItem() }} ถึง {{ $soldiers->lastItem() }} จากทั้งหมด {{ $soldiers->total() }} รายการ
                                    </small>
                                </div>
                                <div>
                                    {{-- ทำให้ pagination links ใช้ parameter จาก query string เดิม (search, per_page) --}}
                                    {{ $soldiers->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{-- route('logout') --}}" method="POST" class="d-none">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('active'));
        }
    });
    </script>
</body>
</html>
