<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติเคสที่พบแพทย์แล้ว</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Source Sans Pro', sans-serif; }
        .container-fluid { padding: 2rem; }
        .card { border: none; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body class="hold-transition">
<div class="wrapper">
    <div class="content-wrapper" style="margin-left: 0 !important;">
        <div class="container-fluid py-4">
            <h1 class="h3 mb-4 text-gray-800">ประวัติเคสที่พบแพทย์แล้ว</h1>

            {{-- Card for Filtering --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('mental-health.completed') }}">
                        {{-- (โค้ดส่วน Filter ของคุณเหมือนเดิม) --}}
                        <div class="row g-2 align-items-end">
                             <div class="col-md-3">
                                 <label class="form-label">ค้นหา</label>
                                 <input type="text" name="search" class="form-control" placeholder="ชื่อ, สกุล, เลขบัตร..." value="{{ request('search') }}">
                             </div>
                             <div class="col-md-2">
                                 <label class="form-label">ผลัด</label>
                                 <select name="rotation_id" class="form-select">
                                     <option value="">-- ทุกผลัด --</option>
                                     @foreach($rotations as $rotation)
                                         <option value="{{ $rotation->id }}" {{ request('rotation_id') == $rotation->id ? 'selected' : '' }}>
                                             {{ $rotation->rotation_name }}
                                         </option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="col-md-3">
                                 <label class="form-label">หน่วยฝึก</label>
                                 <select name="training_unit_id" class="form-select">
                                     <option value="">-- ทุกหน่วยฝึก --</option>
                                     @foreach($trainingUnits as $unit)
                                         <option value="{{ $unit->id }}" {{ request('training_unit_id') == $unit->id ? 'selected' : '' }}>
                                             {{ $unit->unit_name }}
                                         </option>
                                     @endforeach
                                 </select>
                             </div>
                             <div class="col-md-2">
                                 <label class="form-label">ประเภทความเสี่ยง</label>
                                 <select name="risk_type" class="form-select">
                                     <option value="">ทั้งหมด</option>
                                     <option value="at_risk" {{ request('risk_type') == 'at_risk' ? 'selected' : '' }}>จากผลประเมิน</option>
                                     <option value="prior_history" {{ request('risk_type') == 'prior_history' ? 'selected' : '' }}>มีประวัติเดิม</option>
                                 </select>
                             </div>
                             <div class="col-md-2">
                                 <label class="form-label">วันที่เริ่ม (ปิดเคส)</label>
                                 <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                             </div>
                             <div class="col-md-2">
                                 <label class="form-label">วันที่สิ้นสุด (ปิดเคส)</label>
                                 <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                             </div>
                             <div class="col-auto">
                                <button type="submit" class="btn btn-primary w-100" title="ค้นหา"><i class="fas fa-search"></i></button>
                             </div>
                              <div class="col-auto">
                                <a href="{{ route('mental-health.completed') }}" class="btn btn-secondary w-100" title="รีเซ็ต"><i class="fas fa-undo"></i></a>
                             </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ✅ [เพิ่มส่วนนี้] Form สำหรับดาวน์โหลด PDF --}}
<form action="{{ route('mental-health.download.pdf') }}" method="POST" id="downloadPdfForm">
                @csrf
                <input type="hidden" name="selected_ids" id="selected_ids">

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-end">
                        <button type="submit" name="action" value="selected" class="btn btn-primary me-2"><i class="fas fa-download me-1"></i> ดาวน์โหลด PDF (ที่เลือก)</button>
                        <button type="submit" name="action" value="all" class="btn btn-success"><i class="fas fa-file-pdf me-1"></i> ดาวน์โหลด PDF (ทั้งหมด)</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        {{-- ✅ [เพิ่มส่วนนี้] Checkbox เลือกทั้งหมด --}}
                                        <th style="width: 5%;" class="text-center"><input class="form-check-input" type="checkbox" id="selectAll"></th>
                                        <th style="width: 20%;">ชื่อ-สกุล</th>
                                        <th style="width: 10%;">ผลัด</th>
                                        <th style="width: 15%;">หน่วยฝึก</th>
                                        <th style="width: 15%;">ประเภทความเสี่ยง</th>
                                        <th style="width: 15%;">ชื่อแบบประเมิน</th>
                                        <th style="width: 15%;">วันที่ปิดเคสล่าสุด</th>
                                        <th style="width: 10%;">สถานะ</th>
                                        <th class="text-center" style="width: 10%;">ดูประวัติ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($completedSoldiers as $item)
                                        <tr>
                                            {{-- ✅ [เพิ่มส่วนนี้] Checkbox เลือกรายคน --}}
                                            <td class="text-center">
                                                <input class="form-check-input case-checkbox" type="checkbox" value="{{ $item->soldier_id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $item->soldier->first_name ?? 'ไม่พบข้อมูล' }} {{ $item->soldier->last_name ?? '' }}</strong><br>
                                                <small class="text-muted">ID: {{ $item->soldier->soldier_id_card ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $item->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>{{ $item->soldier->trainingUnit->unit_name ?? '-' }}</td>
                                            <td>
                                                @if($item->risk_type == 'at_risk')
                                                    <span class="badge bg-warning text-dark">จากผลประเมิน</span>
                                                @else
                                                    <span class="badge bg-info text-dark">มีประวัติเดิม</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $typeNames = [
                                                        'depression' => 'ภาวะซึมเศร้า',
                                                        'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย',
                                                    ];
                                                    $displayTypes = [];
                                                    if (!empty($item->all_risk_assessment_types)) {
                                                        foreach ($item->all_risk_assessment_types as $typeKey) {
                                                            $displayTypes[] = $typeNames[$typeKey] ?? $typeKey;
                                                        }
                                                    }
                                                @endphp
                                                @foreach($displayTypes as $typeName)
                                                    <span class="badge bg-danger me-1">{{ $typeName }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $item->updated_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <span class="badge bg-success">พบแพทย์สำเร็จ</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('mental-health.history', $item->soldier_id) }}" class="btn btn-sm btn-outline-secondary" title="ดูประวัติทั้งหมด"><i class="fas fa-history"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- ✅ [แก้ไข] เพิ่ม Colspan เป็น 9 --}}
                                        <tr><td colspan="9" class="text-center py-4">ไม่พบข้อมูลเคสที่พบแพทย์สำเร็จ</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- Pagination (เหมือนเดิม) --}}
                     <div class="card-footer d-flex align-items-center">
                         <form method="GET" action="{{ route('mental-health.completed') }}" class="d-flex align-items-center" id="perPageForm-completed">
                             <input type="hidden" name="search" value="{{ request('search') }}">
                             <input type="hidden" name="rotation_id" value="{{ request('rotation_id') }}">
                             <input type="hidden" name="training_unit_id" value="{{ request('training_unit_id') }}">
                             <input type="hidden" name="risk_type" value="{{ request('risk_type') }}">
                             <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                             <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                             <label for="per_page_completed" class="me-2 text-nowrap">แสดง</label>
                             <select name="per_page" id="per_page_completed" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                 <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                                 <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                 <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                                 <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                 <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                 <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                             </select>
                             <span class="ms-2 text-nowrap">รายการ</span>
                         </form>

                         @if ($completedSoldiers->hasPages())
                             <div class="ms-auto" style="flex-shrink: 0;">
                                {{ $completedSoldiers->appends(request()->query())->links('pagination::bootstrap-5') }}
                             </div>
                         @endif
                     </div>
                </div>
            </form> {{-- ✅ ปิด Form ของ PDF --}}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- ✅ [เพิ่มส่วนนี้] JavaScript สำหรับควบคุม Checkbox --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const caseCheckboxes = document.querySelectorAll('.case-checkbox');
    const downloadForm = document.getElementById('downloadPdfForm');
    const selectedIdsInput = document.getElementById('selected_ids');

    // Logic for "Select All" checkbox
    selectAllCheckbox.addEventListener('change', function () {
        caseCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Logic to uncheck "Select All" if an individual box is unchecked
    caseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                // Check if all are checked
                const allChecked = Array.from(caseCheckboxes).every(c => c.checked);
                if (allChecked) {
                    selectAllCheckbox.checked = true;
                }
            }
        });
    });

    // Before submitting the form, gather all selected IDs
    downloadForm.addEventListener('submit', function(event) {
        // Find the button that was clicked to submit the form
        const action = event.submitter ? event.submitter.value : null;

        const selectedCheckboxes = document.querySelectorAll('.case-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        // If "Download Selected" is clicked and nothing is selected, prevent form submission
        if (action === 'selected' && selectedIds.length === 0) {
            event.preventDefault(); // Stop the form from submitting
            alert('กรุณาเลือกรายการที่ต้องการดาวน์โหลดอย่างน้อย 1 รายการ');
            return;
        }

        // Set the hidden input value with the comma-separated IDs
        selectedIdsInput.value = selectedIds.join(',');
    });
});
</script>

</body>
</html>
