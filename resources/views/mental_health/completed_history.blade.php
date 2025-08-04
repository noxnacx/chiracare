<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติเคสที่เสร็จสิ้น | ระบบจัดการผู้ป่วยพิเศษ</title>
    {{-- Fonts, Icons, Framework --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Styles --}}
    <style>
        :root {
            --bs-primary: #0a4d68; --bs-primary-rgb: 10, 77, 104;
            --bs-light: #f8f9fa;
            --font-family-sans-serif: 'IBM Plex Sans Thai', sans-serif;
            --background-color: #f0f2f5; --card-bg: #ffffff;
            --card-border-color: #e9ecef; --text-color: #343a40;
            --text-muted: #6c757d;
        }
        body { background-color: var(--background-color); font-family: var(--font-family-sans-serif); }
        .content-wrapper { padding: 1.5rem; }
        .card { border: 1px solid var(--card-border-color); border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .page-header h1 { margin-bottom: 0; }
        .main-content-grid { display: grid; grid-template-columns: 1fr 420px; gap: 1.5rem; }
        @media (max-width: 1200px) { .main-content-grid { grid-template-columns: 1fr; } }
        .details-sidebar .card-body { padding: 0; }
        .details-sidebar .nav-tabs .nav-link { border-radius: 0; font-weight: 600; color: var(--text-muted); border: none; border-bottom: 3px solid transparent; padding: 1rem; }
        .details-sidebar .nav-tabs .nav-link.active { color: var(--bs-primary); border-bottom-color: var(--bs-primary); background-color: transparent; }
        .details-sidebar .tab-content { padding: 1.5rem; max-height: 70vh; overflow-y: auto; }
        .table-modern { border-collapse: separate; border-spacing: 0 8px; }
        .table-modern thead th { background-color: var(--bs-light); border: none; font-weight: 600; padding: 1rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .table-modern tbody tr { background-color: var(--card-bg); transition: all 0.2s ease; }
        .table-modern td { padding: 1rem; vertical-align: middle; border: none; border-top: 1px solid var(--card-border-color); border-bottom: 1px solid var(--card-border-color); }
        .table-modern td:first-child { border-left: 1px solid var(--card-border-color); border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
        .table-modern td:last-child { border-right: 1px solid var(--card-border-color); border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
        .table-modern tbody tr.clickable-row { cursor: pointer; }
        .table-modern tbody tr.table-active { background-color: rgba(var(--bs-primary-rgb), 0.05); box-shadow: 0 0 0 2px var(--bs-primary); z-index: 10; position: relative; }
        .placeholder-text { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; min-height: 300px; color: var(--text-muted); }
        .placeholder-text i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
        .badge { font-size: 0.8em; font-weight: 600; padding: 0.5em 0.75em; }
        .badge.bg-warning { background-color: rgba(255, 193, 7, 0.2) !important; color: #664d03 !important; }
        .badge.bg-info { background-color: rgba(13, 202, 240, 0.15) !important; color: #055160 !important; }
        .badge.bg-danger { background-color: rgba(220, 53, 69, 0.15) !important; color: #dc3545 !important; }
        .summary-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--card-border-color); }
        .summary-item:last-child { border-bottom: none; }
        .summary-item .summary-label { font-weight: 500; display: flex; align-items: center; }
        .summary-item .summary-value { font-weight: 700; font-size: 1.1rem; }
        .summary-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 10px; }
        .dot-yellow { background-color: #ffc107; }
        .dot-blue { background-color: #0dcaf0; }
        .dot-gray { background-color: #6c757d; }
        .dot-red { background-color: #dc3545; }
        .form-control, .form-select { border-radius: 0.5rem; background-color: var(--bs-light); border-color: var(--card-border-color); }
        .modal-content { border-radius: 0.75rem; border: none; }
        .modal-header, .modal-footer { border-color: var(--card-border-color); }
        .btn { border-radius: 0.5rem; font-weight: 500; }
    </style>
</head>
<body>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="page-header">
            <div><h1 class="h2 fw-bold">ประวัติเคสที่เสร็จสิ้น</h1><p class="text-muted mb-0">รายการเคสที่ปิดแล้วทั้งหมด</p></div>
            <a href="{{ route('mental-health.dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>กลับหน้าหลัก</a>
        </div>

        <div class="main-content-grid">
            {{-- LEFT COLUMN --}}
            <div class="main-table-container">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('mental-health.completed') }}" id="filterForm">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg"><input type="text" name="search" class="form-control form-control-lg" placeholder="ค้นหาด้วยชื่อ, นามสกุล, หรือบัตรประชาชน..." value="{{ request('search') }}"></div>
                                <div class="col-auto"><button type="button" class="btn btn-outline-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="fas fa-filter me-1"></i>ตัวกรอง</button></div>
                                <div class="col-auto"><button type="submit" class="btn btn-primary btn-lg" title="ค้นหา"><i class="fas fa-search"></i></button></div>
                                <div class="col-auto"><a href="{{ route('mental-health.completed') }}" class="btn btn-light btn-lg" title="รีเซ็ต"><i class="fas fa-undo"></i></a></div>
                            </div>
                        </form>
                    </div>
                </div>
                <form action="{{ route('mental-health.download.pdf') }}" method="POST" id="mainCompletedForm">
                    @csrf
                    <input type="hidden" name="selected_ids" id="selected_ids_completed">
                    <div class="d-flex justify-content-end mb-3">
                       <div class="dropdown">
                           <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-download me-2"></i>ดาวน์โหลดรายงาน PDF</button>
                           <ul class="dropdown-menu dropdown-menu-end">
                               <li><h6 class="dropdown-header">รายงานเคสที่เสร็จสิ้น</h6></li>
                               <li><button class="dropdown-item" type="submit" name="action" value="selected"><i class="fas fa-check-square fa-fw me-2"></i>ดาวน์โหลดที่เลือก</button></li>
                               <li><button class="dropdown-item" type="submit" name="action" value="all"><i class="fas fa-file-alt fa-fw me-2"></i>ทั้งหมด (ตามที่กรอง)</button></li>
                           </ul>
                       </div>
                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 table-modern">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                                            <th>ชื่อ-สกุล / ID</th>
                                            <th>ผลัด/หน่วย</th>
                                            <th>ความเสี่ยง</th>
                                            <th>วันที่ปิดเคส</th>
                                            <th class="text-center">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($completedSoldiers as $item)
                                        <tr class="clickable-row case-row" data-details='{{ json_encode($item, JSON_UNESCAPED_UNICODE) }}'>
                                            <td class="text-center" onclick="event.stopPropagation();"><input type="checkbox" class="form-check-input soldier-checkbox-completed" value="{{ $item->soldier_id }}"></td>
                                            <td><div class="fw-bold">{{ $item->soldier->first_name ?? '' }} {{ $item->soldier->last_name ?? '' }}</div><small class="text-muted">{{ $item->soldier->soldier_id_card ?? 'N/A' }}</small></td>
                                            <td><div>{{ $item->soldier->rotation->rotation_name ?? '-' }}</div><small class="text-muted">{{ $item->soldier->trainingUnit->unit_name ?? '-' }}</small></td>
                                            <td>@if($item->risk_type == 'at_risk') <span class="badge bg-warning">จากผลประเมิน</span> @else <span class="badge bg-info">มีประวัติเดิม</span> @endif</td>
                                            <td>{{ \Carbon\Carbon::parse($item->updated_at)->thaidate('j M Y, H:i') }} น.</td>
                                            <td class="text-center"><a href="{{ route('mental-health.history', $item->soldier_id) }}" class="btn btn-sm btn-outline-primary" title="ดูประวัติการรักษาทั้งหมด"><i class="fas fa-history"></i> ดูประวัติ</a></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center py-5"><div class="text-muted"><i class="fas fa-folder-open fs-2 mb-2"></i><p>ไม่พบข้อมูล</p></div></td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($completedSoldiers->hasPages())
                        <div class="card-footer d-flex flex-wrap align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <form method="GET" action="{{ route('mental-health.completed') }}" class="d-flex align-items-center me-3" id="perPageForm">
                                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                    @endforeach
                                    <label for="per_page" class="me-2 text-nowrap">แสดง</label>
                                    <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                        <option value="15" @if($perPage == 15) selected @endif>15</option><option value="25" @if($perPage == 25) selected @endif>25</option><option value="50" @if($perPage == 50) selected @endif>50</option><option value="100" @if($perPage == 100) selected @endif>100</option>
                                    </select>
                                    <span class="ms-2 text-nowrap">รายการ</span>
                               </form>
                            </div>
                           <div class="ms-auto">{{ $completedSoldiers->links('pagination::bootstrap-5') }}</div>
                        </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- RIGHT COLUMN (SIDEBAR) --}}
            <div class="details-sidebar">
                <div class="card sticky-top" style="top: 1.5rem;">
                    <div class="card-body">
                         <nav><div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-summary-tab" data-bs-toggle="tab" data-bs-target="#tab-summary" type="button" role="tab">สรุปข้อมูลเคส</button>
                            <button class="nav-link" id="nav-treatment-tab" data-bs-toggle="tab" data-bs-target="#tab-treatment" type="button" role="tab">ข้อมูลเคสที่เลือก</button>
                        </div></nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="tab-summary" role="tabpanel">
                                <h6 class="mb-3">ประเภทความเสี่ยง</h6>
                                <div class="summary-item"><div class="summary-label"><span class="summary-dot dot-yellow"></span>จากผลประเมิน</div><div class="summary-value">{{ $riskTypeCounts['at_risk'] ?? 0 }}</div></div>
                                <div class="summary-item"><div class="summary-label"><span class="summary-dot dot-blue"></span>มีประวัติเดิม</div><div class="summary-value">{{ $riskTypeCounts['prior_history'] ?? 0 }}</div></div>
                                <hr>
                                <h6 class="my-3">แบบประเมินที่เสี่ยง</h6>
                                <div class="summary-item"><div class="summary-label"><span class="summary-dot dot-gray"></span>ภาวะซึมเศร้า</div><div class="summary-value">{{ $assessmentCounts['depression'] ?? 0 }}</div></div>
                                <div class="summary-item"><div class="summary-label"><span class="summary-dot dot-red"></span>เสี่ยงฆ่าตัวตาย</div><div class="summary-value">{{ $assessmentCounts['suicide_risk'] ?? 0 }}</div></div>
                            </div>
                            <div class="tab-pane fade" id="tab-treatment" role="tabpanel">
                                 <div class="placeholder-text"><i class="fas fa-hand-pointer"></i><p class="h6">คลิกที่รายชื่อในตาราง<br>เพื่อดูข้อมูลเคสล่าสุด</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
{{-- MODALS --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"><i class="fas fa-filter me-2"></i>ตัวกรองเพิ่มเติม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-md-6"><label class="form-label">ผลัด</label><select name="rotation_id" class="form-select" form="filterForm"> <option value="">-- ทุกผลัด --</option> @foreach($rotations as $rotation) <option value="{{ $rotation->id }}" {{ request('rotation_id') == $rotation->id ? 'selected' : '' }}>{{ $rotation->rotation_name }}</option> @endforeach </select></div><div class="col-md-6"><label class="form-label">หน่วยฝึก</label><select name="training_unit_id" class="form-select" form="filterForm"> <option value="">-- ทุกหน่วยฝึก --</option> @foreach($trainingUnits as $unit) <option value="{{ $unit->id }}" {{ request('training_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option> @endforeach </select></div><div class="col-md-12"><label class="form-label">ประเภทความเสี่ยง</label><select name="risk_type" class="form-select" form="filterForm"> <option value="">-- ทุกประเภท --</option> <option value="at_risk" {{ request('risk_type') == 'at_risk' ? 'selected' : '' }}>จากผลประเมิน</option> <option value="prior_history" {{ request('risk_type') == 'prior_history' ? 'selected' : '' }}>มีประวัติเดิม</option> </select></div><div class="col-12"><label class="form-label">ช่วงวันที่ปิดเคส</label><div class="input-group"><input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" form="filterForm"><span class="input-group-text">ถึง</span><input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" form="filterForm"></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button><button type="submit" class="btn btn-primary" form="filterForm"><i class="fas fa-check me-1"></i>ใช้ตัวกรอง</button></div></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const soldierCheckboxes = document.querySelectorAll('.soldier-checkbox-completed');
    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            soldierCheckboxes.forEach(checkbox => { checkbox.checked = selectAllCheckbox.checked; });
        });
    }
    soldierCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (!checkbox.checked) { selectAllCheckbox.checked = false; }
            else if (document.querySelectorAll('.soldier-checkbox-completed:checked').length === soldierCheckboxes.length) { selectAllCheckbox.checked = true; }
        });
    });
    const mainForm = document.getElementById('mainCompletedForm');
    if(mainForm){
        mainForm.addEventListener('submit', function(e) {
            const action = e.submitter ? e.submitter.value : null;
            if (action === 'selected') {
                const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox-completed:checked')).map(cb => cb.value);
                if (selectedIds.length === 0) {
                    e.preventDefault();
                    alert('กรุณาเลือกรายการที่ต้องการดาวน์โหลดอย่างน้อย 1 รายการ');
                    return;
                }
                document.getElementById('selected_ids_completed').value = selectedIds.join(',');
            }
        });
    }

    const caseRows = document.querySelectorAll('.case-row');
    const treatmentTabPane = document.getElementById('tab-treatment');
    const treatmentTabButton = document.getElementById('nav-treatment-tab');
    const riskTypeMap = { 'at_risk': '<span class="badge bg-warning">จากผลประเมิน</span>', 'prior_history': '<span class="badge bg-info">มีประวัติเดิม</span>' };
    const assessmentMap = { 'depression': '<span class="badge bg-danger me-1">ภาวะซึมเศร้า</span>', 'suicide_risk': '<span class="badge bg-danger me-1">เสี่ยงฆ่าตัวตาย</span>' };

    function formatDate(dateString) { if (!dateString) return '-'; return new Date(dateString).toLocaleDateString('th-TH', { day: 'numeric', month: 'long', year: 'numeric' }); }

    caseRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('a.btn') || e.target.closest('input[type="checkbox"]')) return;
            caseRows.forEach(r => r.classList.remove('table-active'));
            this.classList.add('table-active');
            const details = JSON.parse(this.dataset.details);
            const assessmentsHtml = details.all_risk_assessment_types && details.all_risk_assessment_types.length > 0 ? details.all_risk_assessment_types.map(type => assessmentMap[type] || type).join('') : '<span class="text-muted">ไม่มี</span>';
            const latestAppointment = details.appointments ? details.appointments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0] : null;
            const latestTreatment = latestAppointment && latestAppointment.treatment ? latestAppointment.treatment : null;
            let treatmentHtml = '<hr><div class="text-center text-muted mt-3">ไม่พบข้อมูลการรักษาล่าสุด</div>';
            if(latestTreatment) {
                treatmentHtml = `<hr><h6 class="mb-3">การรักษาล่าสุด (${formatDate(latestTreatment.treatment_date)})</h6><dl class="row"><dt class="col-sm-5">แพทย์ผู้รักษา</dt><dd class="col-sm-7">${latestTreatment.doctor_name || '-'}</dd><dt class="col-sm-5">ยาที่รักษา</dt><dd class="col-sm-7">${latestTreatment.medicine_name || 'ไม่มี'}</dd><dt class="col-sm-5">ข้อมูลเพิ่มเติม</dt><dd class="col-sm-7">${latestTreatment.notes || 'ไม่มี'}</dd></dl>`;
            }
            const finalHtml = `<h5 class="mb-3">${details.soldier.first_name} ${details.soldier.last_name}</h5><dl class="row"><dt class="col-sm-5">ประเภทความเสี่ยง</dt><dd class="col-sm-7">${riskTypeMap[details.risk_type] || '-'}</dd><dt class="col-sm-5">แบบประเมินที่เสี่ยง</dt><dd class="col-sm-7">${assessmentsHtml}</dd></dl>${treatmentHtml}`;
            treatmentTabPane.innerHTML = finalHtml;
            new bootstrap.Tab(treatmentTabButton).show();
        });
    });
});
</script>
</body>
</html>
