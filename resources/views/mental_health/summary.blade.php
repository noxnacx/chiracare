<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดสุขภาพจิต | สรุปผล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #0d6efd;
            --bs-primary-rgb: 13, 110, 253;
            --bs-primary-hover: #0b5ed7;
            --bs-body-font-family: 'Sarabun', sans-serif;
            --bs-body-bg: #f8f9fa;
            --bs-border-color: #dee2e6;
            --bs-border-radius: 0.75rem;
            --text-muted: #6c757d;
            --text-dark: #212529;
        }

        body {
            background-color: var(--bs-body-bg);
            font-family: var(--bs-body-font-family);
            color: var(--text-dark);
        }

        .page-header {
            border-bottom: 1px solid var(--bs-border-color);
            padding-bottom: 1rem;
        }
        .page-header .page-title { font-weight: 700; }
        .page-header .page-subtitle { font-size: 1.1rem; color: var(--text-muted); }

        .main-content-card {
            background-color: #ffffff;
            border: none;
            border-radius: var(--bs-border-radius);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-custom { margin-bottom: 0; }
        .table-custom thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--bs-border-color);
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            font-size: 0.9rem;
            padding: 1rem;
        }
        .table-custom tbody tr {
            border-top: 1px solid #f1f3f5;
            transition: background-color 0.15s ease-in-out;
        }
        .table-custom tbody tr:hover { background-color: #f8f9fa; }
        .table-custom td {
            text-align: center;
            vertical-align: middle;
            padding: 1rem;
        }
        .soldier-info { text-align: left !important; }
        .soldier-info .name { font-weight: 600; }
        .soldier-info .id-card { font-size: 0.85rem; color: var(--text-muted); }

        .score-cell.high { color: #dc3545; font-weight: 700; }
        .score-cell.medium { color: #fd7e14; font-weight: 700; }
        .score-cell { font-weight: 500; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.8rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-badge.complete { background-color: #d1e7dd; color: #0f5132; }
        .status-badge.incomplete { background-color: #f8d7da; color: #842029; }
        .status-badge i { margin-right: 0.4rem; }

        .summary-box.card {
            border: none;
            border-radius: var(--bs-border-radius);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
        }
        .summary-box .card-header {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 1px solid var(--bs-border-color);
        }
        .summary-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0.5rem;
            text-decoration: none;
            color: var(--text-dark);
            border-radius: 0.5rem;
            transition: background-color 0.2s ease;
        }
        .summary-list-item:hover { background-color: #e9ecef; }
        .summary-list-item .count {
            font-weight: 700;
            font-size: 1rem;
            background-color: #e9ecef;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            min-width: 36px;
            text-align: center;
        }
        .summary-list-item.active-filter {
            background-color: var(--bs-primary);
            color: white;
        }
        .summary-list-item.active-filter .count {
            background-color: rgba(255,255,255,0.2);
            color: white;
        }

        .btn { transition: all 0.2s ease; }
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .btn-primary:hover {
            background-color: var(--bs-primary-hover);
            border-color: var(--bs-primary-hover);
        }
        .nav-pills .nav-link.active { background-color: var(--text-dark); }
    </style>
</head>
<body>
    <div class="container-fluid my-4 px-lg-4">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 mb-1">แดชบอร์ดสุขภาพจิต</h1>
                <p class="page-subtitle mb-0">สรุปผลการทำแบบประเมินของกำลังพล</p>
            </div>
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel-fill me-2"></i>ตัวกรอง
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="text-end mb-3">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary bg-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-2"></i>Export PDF
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button class="dropdown-item" type="button" id="downloadSelectedBtn"><i class="bi bi-check2-square me-2"></i>ดาวน์โหลดที่เลือก</button></li>
                            <li><button class="dropdown-item" type="button" id="downloadAllBtn"><i class="bi bi-table me-2"></i>ดาวน์โหลดทั้งหมด</button></li>
                        </ul>
                    </div>
                </div>

                @if($request->filled('risk_level_filter'))
                <div class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-3">
                    <span class="small"><i class="bi bi-funnel-fill me-2"></i>แสดงผลเฉพาะ: ผู้มีความเสี่ยง <strong>{{ $request->risk_level_filter }}</strong> ด้าน <strong>{{ $assessmentLabels[$request->assessment_type_filter] ?? '' }}</strong></span>
                    <a href="{{ route('mental-health.assessment.summary', $request->except(['risk_level_filter', 'assessment_type_filter'])) }}" class="btn-close btn-sm" title="ล้างการกรองนี้"></a>
                </div>
                @endif

                <div class="main-content-card">
                    <div class="p-3 d-flex justify-content-between align-items-center">
                        <form action="{{ route('mental-health.assessment.summary') }}" method="GET" class="d-flex align-items-center">
                            <label for="limit" class="form-label me-2 mb-0 fw-normal">แสดง</label>
                            <select name="limit" id="limit" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="5" {{ $currentLimit == 5 ? 'selected' : '' }}>5</option>
                                <option value="15" {{ $currentLimit == 15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ $currentLimit == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $currentLimit == 50 ? 'selected' : '' }}>50</option>
                            </select>
                             @foreach($request->except(['page', 'limit', 'search']) as $key => $value)
                               <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                        <form action="{{ route('mental-health.assessment.summary') }}" method="GET" class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="ค้นหาด้วยชื่อ, เลขประจำตัว..." value="{{ $request->search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            @foreach($request->except(['page', 'search']) as $key => $value)
                               <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 1%;"><input class="form-check-input" type="checkbox" id="selectAllCheckbox"></th>
                                    <th class="text-start ps-4">ข้อมูลกำลังพล</th>
                                    <th>ผลัด</th><th>หน่วย</th>
                                    @foreach ($assessmentLabels as $label)
                                        <th>{{ $label }}</th>
                                    @endforeach
                                    <th>สถานะการประเมิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assessmentData as $data)
                                <tr>
                                    <td class="text-center"><input class="form-check-input soldier-checkbox" type="checkbox" value="{{ $data['soldier']->id }}"></td>
                                    <td class="soldier-info ps-4">
                                        <div class="name">{{ $data['soldier']->first_name }} {{ $data['soldier']->last_name }}</div>
                                        <div class="id-card">ID: {{ $data['soldier']->soldier_id_card }}</div>
                                    </td>
                                    <td>{{ $data['soldier']->rotation->rotation_name ?? 'N/A' }}</td>
                                    <td>{{ $data['soldier']->trainingUnit->unit_name ?? 'N/A' }}</td>
                                    @foreach ($assessmentTypes as $type)
                                        @php
                                            $score = $data['scores'][$type] ?? null;
                                            $levelClass = '';
                                            if (is_numeric($score)) {
                                                switch ($type) {
                                                    case 'depression': if ($score >= 13) $levelClass = 'high'; elseif ($score >= 7) $levelClass = 'medium'; break;
                                                    case 'suicide_risk': if ($score >= 10) $levelClass = 'high'; elseif ($score >= 5) $levelClass = 'medium'; break;
                                                    case 'smoking': if ($score >= 6) $levelClass = 'high'; elseif ($score >= 4) $levelClass = 'medium'; break;
                                                    case 'alcohol': if ($score >= 20) $levelClass = 'high'; elseif ($score >= 16) $levelClass = 'medium'; break;
                                                    case 'drug_use': if ($score >= 27) $levelClass = 'high'; elseif ($score >= 4) $levelClass = 'medium'; break;
                                                }
                                            }
                                        @endphp
                                        <td class="score-cell {{ $levelClass }}">{{ $score ?? '-' }}</td>
                                    @endforeach
                                    <td>
                                        @if ($data['soldier']->initial_assessment_complete)
                                            <span class="status-badge complete"><i class="bi bi-check-circle"></i>ครบถ้วน</span>
                                        @else
                                            <span class="status-badge incomplete"><i class="bi bi-x-circle"></i>ยังไม่ครบ</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ 4 + count($assessmentTypes) + 1 }}" class="text-center p-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <h5 class="mt-3">ไม่พบข้อมูล</h5>
                                        <p class="text-muted">ไม่พบข้อมูลทหารที่ตรงตามเงื่อนไขการกรองนี้</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($paginator->hasPages())
                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <div><small class="text-muted">แสดง {{ $paginator->firstItem() }} ถึง {{ $paginator->lastItem() }} จากทั้งหมด {{ $paginator->total() }} รายการ</small></div>
                        <div>{{ $paginator->appends($request->all())->links() }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card summary-box">
                    <div class="card-header"><i class="bi bi-bar-chart-line-fill me-2"></i>ภาพรวม</div>
                    <div class="card-body">
                        <a href="{{ route('mental-health.assessment.summary', array_merge($request->except(['risk_level_filter', 'assessment_type_filter', 'completed_status', 'page']))) }}" class="summary-list-item">
                            <span><i class="bi bi-people-fill text-primary me-2"></i>กำลังพลทั้งหมด</span>
                            <span class="count">{{ $totalCount }}</span>
                        </a><hr class="my-2">
                        <a href="{{ route('mental-health.assessment.summary', array_merge($request->except(['risk_level_filter', 'assessment_type_filter', 'page']), ['completed_status' => 'complete'])) }}" class="summary-list-item">
                            <span><i class="bi bi-person-check-fill text-success me-2"></i>ประเมินครบแล้ว</span>
                            <span class="count">{{ $completedCount }}</span>
                        </a><hr class="my-2">
                        <a href="{{ route('mental-health.assessment.summary', array_merge($request->except(['risk_level_filter', 'assessment_type_filter', 'page']), ['completed_status' => 'incomplete'])) }}" class="summary-list-item">
                            <span><i class="bi bi-person-x-fill text-danger me-2"></i>ยังไม่ครบ / รอประเมิน</span>
                            <span class="count">{{ $incompleteCount }}</span>
                        </a>
                    </div>
                </div>

                <div class="card summary-box mt-4">
                    <div class="card-header"><i class="bi bi-exclamation-triangle-fill me-2"></i>ภาพรวมผู้ที่มีความเสี่ยง</div>
                    <div class="card-body">
                        <ul class="nav nav-pills nav-fill mb-3" id="risk-tab" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#high-risk-content">สูง</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#medium-risk-content">ปานกลาง</button></li>
                        </ul>
                        <div class="tab-content" id="risk-tabContent">
                            <div class="tab-pane fade show active" id="high-risk-content">
                                @foreach($assessmentLabels as $key => $label)
                                    @php $isActive = ($request->risk_level_filter == 'สูง' && $request->assessment_type_filter == $key); @endphp
                                    <a href="{{ route('mental-health.assessment.summary', array_merge($request->except('page'), ['risk_level_filter' => 'สูง', 'assessment_type_filter' => $key])) }}" class="summary-list-item {{ $isActive ? 'active-filter' : '' }}">
                                        <span><i class="bi {{ $assessmentIcons[$key] ?? 'bi-question-circle' }} me-2"></i>{{ $label }}</span>
                                        <span class="count">{{ $riskCounts['สูง'][$key] ?? 0 }}</span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="tab-pane fade" id="medium-risk-content">
                                @foreach($assessmentLabels as $key => $label)
                                    @php $isActive = ($request->risk_level_filter == 'ปานกลาง' && $request->assessment_type_filter == $key); @endphp
                                    <a href="{{ route('mental-health.assessment.summary', array_merge($request->except('page'), ['risk_level_filter' => 'ปานกลาง', 'assessment_type_filter' => $key])) }}" class="summary-list-item {{ $isActive ? 'active-filter' : '' }}">
                                        <span><i class="bi {{ $assessmentIcons[$key] ?? 'bi-question-circle' }} me-2"></i>{{ $label }}</span>
                                        <span class="count">{{ $riskCounts['ปานกลาง'][$key] ?? 0 }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <form action="{{ route('mental-health.assessment.summary') }}" method="GET">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title"><i class="bi bi-funnel-fill me-2"></i>ตัวกรองข้อมูล</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label for="filter_unit" class="form-label">หน่วย</label><select name="unit" id="filter_unit" class="form-select"><option value="">-- แสดงทุกหน่วย --</option>@foreach ($units as $unit)<option value="{{ $unit->id }}" {{ $request->unit == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label for="filter_rotation" class="form-label">ผลัด</label><select name="rotation" id="filter_rotation" class="form-select"><option value="">-- แสดงทุกผลัด --</option>@foreach ($rotations as $rotation)<option value="{{ $rotation->id }}" {{ $request->rotation == $rotation->id ? 'selected' : '' }}>{{ $rotation->rotation_name }}</option>@endforeach</select></div>
                        <div class="mb-3"><label for="completed_status_filter" class="form-label">สถานะการประเมิน</label><select name="completed_status" id="completed_status_filter" class="form-select"><option value="">-- ทุกสถานะ --</option><option value="complete" {{ $request->completed_status == 'complete' ? 'selected' : '' }}>ประเมินครบแล้ว</option><option value="incomplete" {{ $request->completed_status == 'incomplete' ? 'selected' : '' }}>ยังไม่ครบ / รอประเมิน</option></select></div>
                        <div class="mb-3"><label for="assessment_type_filter" class="form-label">ประเภทแบบประเมิน</label><select name="assessment_type_filter" id="assessment_type_filter" class="form-select"><option value="">-- ทุกประเภท --</option>@foreach($assessmentLabels as $key => $label)<option value="{{ $key }}" {{ $request->assessment_type_filter == $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
                        <div class=""><label for="risk_level_filter" class="form-label">ระดับความเสี่ยง</label><select name="risk_level_filter" id="risk_level_filter" class="form-select"><option value="">-- ทุกระดับ --</option><option value="ต่ำ" {{ $request->risk_level_filter == 'ต่ำ' ? 'selected' : '' }}>ต่ำ</option><option value="ปานกลาง" {{ $request->risk_level_filter == 'ปานกลาง' ? 'selected' : '' }}>ปานกลาง</option><option value="สูง" {{ $request->risk_level_filter == 'สูง' ? 'selected' : '' }}>สูง</option></select></div>
                    </div>
                    <div class="modal-footer border-0">
                        <a href="{{ route('mental-health.assessment.summary') }}" class="btn btn-outline-secondary">ล้างค่า</a>
                        <button type="submit" class="btn btn-primary">ใช้ตัวกรอง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="downloadForm" action="{{ route('mental-health.assessment.summary.download') }}" method="POST" class="d-none">@csrf<div id="selectedIdsContainer"></div></form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const soldierCheckboxes = document.querySelectorAll('.soldier-checkbox');
            const downloadSelectedBtn = document.getElementById('downloadSelectedBtn');
            const downloadAllBtn = document.getElementById('downloadAllBtn');
            const downloadForm = document.getElementById('downloadForm');
            const selectedIdsContainer = document.getElementById('selectedIdsContainer');

            selectAllCheckbox.addEventListener('change', function () {
                soldierCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
            });

            function triggerDownload(isDownloadAll = false) {
                selectedIdsContainer.innerHTML = '';
                if (isDownloadAll) {
                    const currentUrlParams = new URLSearchParams(window.location.search);
                    currentUrlParams.forEach((value, key) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        selectedIdsContainer.appendChild(input);
                    });
                } else {
                    const selectedIds = Array.from(soldierCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                    if (selectedIds.length === 0) {
                        alert('กรุณาเลือกรายชื่อที่ต้องการดาวน์โหลด');
                        return;
                    }
                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = id;
                        selectedIdsContainer.appendChild(input);
                    });
                }
                downloadForm.submit();
            }

            downloadSelectedBtn.addEventListener('click', () => triggerDownload(false));
            downloadAllBtn.addEventListener('click', () => triggerDownload(true));
        });
    </script>
</body>
</html>
