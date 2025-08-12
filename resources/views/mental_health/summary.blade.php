<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สรุปผลการทำแบบประเมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Sarabun', sans-serif;
        }

        /* --- ดีไซน์ Stat Card (เหมือนเดิม) --- */
        .stat-card-v2 {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 0.75rem; /* เพิ่มความมน */
            padding: 1.25rem;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 6px rgba(0,0,0,0.04);
        }
        .stat-card-v2 a { text-decoration: none; color: inherit; }
        .stat-card-v2:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .stat-card-v2 .icon-bg {
            width: 60px; height: 60px; display: flex; align-items: center;
            justify-content: center; border-radius: 0.75rem; font-size: 1.75rem;
            color: #fff; margin-right: 1.25rem;
        }
        .stat-card-v2 .icon-bg.bg-primary { background-color: #0d6efd !important; }
        .stat-card-v2 .icon-bg.bg-success { background-color: #198754 !important; }
        .stat-card-v2 .icon-bg.bg-danger { background-color: #dc3545 !important; }
        .stat-card-v2 .info .title { font-size: 0.9rem; font-weight: 500; color: #6c757d; margin-bottom: 0.25rem; }
        .stat-card-v2 .info .number { font-size: 2rem; font-weight: 700; color: #212529; }

        /* --- ดีไซน์ตารางและ Card หลัก --- */
        .main-content-card {
            background-color: #ffffff;
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .table-custom thead th {
            background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;
            color: #495057; font-weight: 600; text-align: center;
            vertical-align: middle; text-transform: uppercase; font-size: 0.8rem;
        }
        .table-custom tbody tr { border-top: 1px solid #e9ecef; }
        .table-custom td { text-align: center; vertical-align: middle; padding: 1rem 0.5rem; }
        .status-icon-complete { color: #198754; font-size: 1.5rem; }
        .status-icon-incomplete { color: #dc3545; font-size: 1.5rem; }
        .soldier-name { font-weight: 500; text-align: left !important; }
        .score-cell { font-weight: 700; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="container-fluid my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-dark">สรุปผลการทำแบบประเมิน</h1>
            <button class="btn btn-dark" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel-fill me-2"></i>ตัวกรองข้อมูล
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('mental-health.assessment.summary', $request->except('completed_status')) }}" class="text-decoration-none">
                    <div class="stat-card-v2 h-100">
                        <div class="icon-bg bg-primary"><i class="bi bi-people-fill"></i></div>
                        <div class="info"><div class="title">รายชื่อทั้งหมด</div><div class="number">{{ $totalCount }}</div></div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                 <a href="{{ route('mental-health.assessment.summary', array_merge($request->except('completed_status'), ['completed_status' => 'complete'])) }}" class="text-decoration-none">
                    <div class="stat-card-v2 h-100">
                        <div class="icon-bg bg-success"><i class="bi bi-person-check-fill"></i></div>
                        <div class="info"><div class="title">ประเมินครบแล้ว</div><div class="number">{{ $completedCount }}</div></div>
                    </div>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('mental-health.assessment.summary', array_merge($request->except('completed_status'), ['completed_status' => 'incomplete'])) }}" class="text-decoration-none">
                    <div class="stat-card-v2 h-100">
                        <div class="icon-bg bg-danger"><i class="bi bi-person-x-fill"></i></div>
                        <div class="info"><div class="title">ยังไม่ครบ / รอประเมิน</div><div class="number">{{ $incompleteCount }}</div></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="main-content-card">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ชื่อ - สกุล</th><th>เลขประจำตัว</th><th>ผลัด</th><th>หน่วย</th>
                            @foreach ($assessmentLabels as $label)<th>{{ $label }}</th>@endforeach
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessmentData as $data)
                        <tr>
                            <td class="soldier-name px-3">{{ $data['soldier']->first_name }} {{ $data['soldier']->last_name }}</td>
                            <td>{{ $data['soldier']->soldier_id_card }}</td>
                            <td>{{ $data['soldier']->rotation->rotation_name ?? 'N/A' }}</td>
                            <td>{{ $data['soldier']->trainingUnit->unit_name ?? 'N/A' }}</td>
                            @foreach ($assessmentTypes as $type)<td class="score-cell">{{ $data['scores'][$type] ?? '-' }}</td>@endforeach
                            <td>
                                @if ($data['soldier']->initial_assessment_complete)
                                <i class="bi bi-check-circle-fill status-icon-complete" title="ครบถ้วน"></i>
                                @else
                                <i class="bi bi-x-circle-fill status-icon-incomplete" title="ยังไม่ครบ"></i>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="{{ 4 + count($assessmentTypes) + 1 }}" class="text-center p-5"><h5 class="text-muted">ไม่พบข้อมูลตามเงื่อนไข</h5></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('mental-health.assessment.summary') }}" method="GET">
                    <div class="modal-header"><h5 class="modal-title" id="filterModalLabel"><i class="bi bi-funnel-fill me-2"></i>ตัวกรองข้อมูล</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label for="unit" class="form-label">หน่วย</label><select name="unit" id="unit" class="form-select"><option value="">-- แสดงทุกหน่วย --</option>@foreach ($units as $unit)<option value="{{ $unit->id }}" {{ $request->unit == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label for="rotation" class="form-label">ผลัด</label><select name="rotation" id="rotation" class="form-select"><option value="">-- แสดงทุกผลัด --</option>@foreach ($rotations as $rotation)<option value="{{ $rotation->id }}" {{ $request->rotation == $rotation->id ? 'selected' : '' }}>{{ $rotation->rotation_name }}</option>@endforeach</select></div>
                        <div><label for="search" class="form-label">ค้นหา (ชื่อ / เลขประจำตัว)</label><input type="text" name="search" id="search" class="form-control" value="{{ $request->search ?? '' }}" placeholder="กรอกเพื่อค้นหา..."></div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('mental-health.assessment.summary') }}" class="btn btn-outline-secondary">ล้างค่า</a>
                        <button type="submit" class="btn btn-primary">ใช้ตัวกรอง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
