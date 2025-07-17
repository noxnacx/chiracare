<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบส่งรายชื่อผู้ป่วยพิเศษ</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Source Sans Pro', sans-serif; }
        .container-fluid { padding: 2rem; }
        .card { border: none; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
        .stat-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: white;
            border-radius: .375rem;
            height: 100%;
        }
        .stat-card-icon {
            font-size: 2.5rem;
            margin-right: 1rem;
        }
        .stat-card-content .stat-card-text {
            font-size: 0.8rem;
            margin-bottom: 0;
            text-transform: uppercase;
        }
        .stat-card-content .stat-card-number {
            font-size: 1.75rem;
            font-weight: bold;
            line-height: 1.2;
        }
    </style>
</head>
<body class="hold-transition">
<div class="wrapper">
    <div class="content-wrapper" style="margin-left: 0 !important;">
        <div class="container-fluid py-4">
            <h1 class="h3 mb-4 text-gray-800">ระบบส่งรายชื่อผู้ป่วยพิเศษ</h1>

            {{-- การ์ดสรุปข้อมูล (เหมือนเดิม) --}}
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card bg-info">
                        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-card-content">
                            <h5 class="stat-card-number">{{ $totalCases }}</h5>
                            <p class="stat-card-text">จำนวนเคสทั้งหมด</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card bg-warning">
                        <div class="stat-card-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-card-content">
                            <h5 class="stat-card-number">{{ $requiredCases }}</h5>
                            <p class="stat-card-text">รอส่งป่วย</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card bg-primary">
                        <div class="stat-card-icon"><i class="far fa-calendar-alt"></i></div>
                        <div class="stat-card-content">
                            <h5 class="stat-card-number">{{ $scheduledCases }}</h5>
                            <p class="stat-card-text">นัดหมายสำเร็จ</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card bg-success">
                        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-card-content">
                            <h5 class="stat-card-number">{{ $completedCases }}</h5>
                            <p class="stat-card-text">พบแพทย์สำเร็จ</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications (เหมือนเดิม) --}}
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
            @if (session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
            @if (session('info'))<div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

            {{-- Filter Form (เหมือนเดิม) --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('mental-health.dashboard') }}">
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-2">
                                <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ, บัตร..." value="{{ request('search') }}">
                            </div>
                            <div class="col-lg-2">
                                <select name="rotation_id" class="form-select">
                                    <option value="">-- ทุกผลัด --</option>
                                    @foreach($rotations as $rotation)
                                        <option value="{{ $rotation->id }}" {{ request('rotation_id') == $rotation->id ? 'selected' : '' }}>
                                            {{ $rotation->rotation_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select name="training_unit_id" class="form-select">
                                    <option value="">-- ทุกหน่วยฝึก --</option>
                                    @foreach($trainingUnits as $unit)
                                        <option value="{{ $unit->id }}" {{ request('training_unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <select name="risk_type" class="form-select">
                                    <option value="">-- ทุกประเภท --</option>
                                    <option value="at_risk" {{ request('risk_type') == 'at_risk' ? 'selected' : '' }}>จากผลประเมิน</option>
                                    <option value="prior_history" {{ request('risk_type') == 'prior_history' ? 'selected' : '' }}>มีประวัติเดิม</option>
                                </select>
                            </div>
                             <div class="col-lg-1">
                                <select name="status" class="form-select">
                                    <option value="">-- ทุกสถานะ --</option>
                                    <option value="required" {{ request('status') == 'required' ? 'selected' : '' }}>รอส่ง</option>
                                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>นัดแล้ว</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group">
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="วันที่เริ่ม">
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="วันที่สิ้นสุด">
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" title="ค้นหา"><i class="fas fa-search"></i></button>
                                <a href="{{ route('mental-health.dashboard') }}" class="btn btn-secondary" title="รีเซ็ต"><i class="fas fa-undo"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ✅✅✅ START: Form หลักสำหรับจัดการข้อมูลและดาวน์โหลด ✅✅✅ --}}
            <form action="{{ route('mental-health.dashboard.download') }}" method="POST" id="mainDashboardForm">
                @csrf
                <input type="hidden" name="selected_ids" id="selected_ids">

                {{-- ส่วนดาวน์โหลด PDF ที่เพิ่มเข้ามาใหม่ --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><h5 class="mb-0"><i class="fas fa-download me-2 text-secondary"></i><strong>ดาวน์โหลดรายงาน PDF</strong></h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <div class="d-flex align-items-center mb-2"><i class="fas fa-clock text-warning fs-4 me-3"></i><div><h6 class="mb-0">เคสรอส่งป่วย</h6><small class="text-muted">สถานะ: Required</small></div></div>
                                <button type="submit" name="action" value="download_required_selected" class="btn btn-sm btn-outline-primary"><i class="fas fa-check-square me-1"></i> ดาวน์โหลดที่เลือก</button>
                                <button type="submit" name="action" value="download_required_all" class="btn btn-sm btn-primary"><i class="fas fa-file-alt me-1"></i> ดาวน์โหลดทั้งหมด</button>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2"><i class="fas fa-calendar-check text-info fs-4 me-3"></i><div><h6 class="mb-0">เคสนัดหมายแล้ว</h6><small class="text-muted">สถานะ: Scheduled</small></div></div>
                                <button type="submit" name="action" value="download_scheduled_selected" class="btn btn-sm btn-outline-info"><i class="fas fa-check-square me-1"></i> ดาวน์โหลดที่เลือก</button>
                                <button type="submit" name="action" value="download_scheduled_all" class="btn btn-sm btn-info text-white"><i class="fas fa-file-alt me-1"></i> ดาวน์โหลดทั้งหมด</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มจัดการต่างๆ (เดิม) --}}
                <div class="mb-3 btn-group" role="group">
                    <button type="button" class="btn btn-primary" id="bulkSendButton" data-bs-toggle="modal" data-bs-target="#appointmentModal" disabled> <i class="fas fa-paper-plane me-1"></i> ส่งป่วยที่เลือก </button>
                    <button type="button" class="btn btn-info" id="bulkEditAppointmentButton" data-bs-toggle="modal" data-bs-target="#editAppointmentModal" disabled> <i class="fas fa-edit me-1"></i> แก้ไขนัดหมายที่เลือก </button>
                    <button type="button" class="btn btn-warning" id="bulkCloseButton" data-bs-toggle="modal" data-bs-target="#bulkCloseFormModal" disabled> <i class="fas fa-archive me-1"></i> ปิดเคสที่เลือก </button>
                </div>

                {{-- ตารางข้อมูลหลัก --}}
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 5%;"><input type="checkbox" id="selectAllCheckbox"></th>
                                        <th style="width: 20%;">ชื่อ-สกุล</th>
                                        <th style="width: 10%;">ผลัด</th>
                                        <th style="width: 15%;">หน่วยฝึก</th>
                                        <th style="width: 15%;">ประเภทความเสี่ยง</th>
                                        <th style="width: 15%;">ชื่อแบบประเมิน</th>
                                        <th style="width: 10%;">วันที่ได้รับข้อมูล</th>
                                        <th style="width: 10%;">สถานะ</th>
                                        <th class="text-center" style="width: 10%;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($trackedSoldiers as $item)
                                        @php $latestAppointment = $item->appointments->sortByDesc('created_at')->first(); @endphp
                                        <tr data-status="{{ $item->status }}" data-appointment-id="{{ $latestAppointment->id ?? '' }}">
                                            <td class="text-center"><input type="checkbox" class="soldier-checkbox" value="{{ $item->id }}"></td>
                                            <td><strong>{{ $item->soldier->first_name ?? 'ไม่พบข้อมูล' }} {{ $item->soldier->last_name ?? '' }}</strong><br><small class="text-muted">ID: {{ $item->soldier->soldier_id_card ?? 'N/A' }}</small></td>
                                            <td>{{ $item->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>{{ $item->soldier->trainingUnit->unit_name ?? '-' }}</td>
                                            <td>@if($item->risk_type == 'at_risk') <span class="badge bg-warning text-dark">จากผลประเมิน</span> @else <span class="badge bg-info text-dark">มีประวัติเดิม</span> @endif</td>
                                            <td>
                                                @php
                                                    $typeNames = [ 'depression' => 'ภาวะซึมเศร้า', 'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย' ];
                                                    $displayTypes = [];
                                                    if (!empty($item->all_risk_assessment_types)) {
                                                        foreach ($item->all_risk_assessment_types as $typeKey) { $displayTypes[] = $typeNames[$typeKey] ?? $typeKey; }
                                                    }
                                                @endphp
                                                @foreach($displayTypes as $typeName) <span class="badge bg-danger me-1">{{ $typeName }}</span> @endforeach
                                            </td>
                                            <td>{{ $item->created_at->format('d M Y') }}</td>
                                            <td>@if($item->status == 'required') <span class="badge bg-primary">รอส่งป่วย</span> @elseif($item->status == 'scheduled') <span class="badge bg-success">นัดหมายสำเร็จ</span> @endif</td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    @if($item->status == 'required')
                                                        <button type="button" class="btn btn-sm btn-primary refer-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#appointmentModal" title="ส่งป่วย"><i class="fas fa-paper-plane"></i></button>
                                                    @elseif($item->status == 'scheduled' && $latestAppointment)
                                                        <button type="button" class="btn btn-sm btn-outline-primary view-appointment-btn" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal" title="ดูข้อมูลนัดหมาย" data-date="{{ $latestAppointment->appointment_date ? \Carbon\Carbon::parse($latestAppointment->appointment_date)->format('d M Y') : '' }}" data-time="{{ $latestAppointment->appointment_time ? \Carbon\Carbon::parse($latestAppointment->appointment_time)->format('H:i') . ' น.' : '' }}" data-location="{{ $latestAppointment->appointment_location ?? '' }}" data-notes="{{ $latestAppointment->notes ?? '' }}"><i class="fas fa-eye"></i></button>
                                                        <button type="button" class="btn btn-sm btn-outline-info edit-appointment-btn" data-bs-toggle="modal" data-bs-target="#editAppointmentModal" data-appointment-id="{{ $latestAppointment->id }}" data-date="{{ $latestAppointment->appointment_date }}" data-time="{{ \Carbon\Carbon::parse($latestAppointment->appointment_time)->format('H:i') }}" data-location="{{ $latestAppointment->appointment_location }}" data-notes="{{ $latestAppointment->notes ?? '' }}" title="แก้ไขนัดหมาย"><i class="fas fa-pencil-alt"></i></button>
                                                        <button type="button" class="btn btn-sm btn-success close-case-btn" data-bs-toggle="modal" data-bs-target="#treatmentModal" data-tracking-id="{{ $item->id }}" data-appointment-id="{{ $latestAppointment->id }}" title="ปิดเคส"><i class="fas fa-check"></i></button>
                                                    @endif
                                                    @if($item->risk_type == 'at_risk')
                                                        <form action="{{ route('mental-health.risk-type.update', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณต้องการเปลี่ยนเคสนี้เป็น \'เคสมีประวัติเดิม\' ใช่หรือไม่?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-dark" title="เปลี่ยนเป็นเคสมีประวัติเดิม"><i class="fas fa-user-clock"></i></button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('mental-health.history', $item->soldier_id) }}" class="btn btn-sm btn-outline-secondary" title="ดูประวัติ"><i class="fas fa-history"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center py-4">ไม่พบข้อมูล</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex align-items-center">
                        <form method="GET" action="{{ route('mental-health.dashboard') }}" class="d-flex align-items-center" id="perPageForm-dashboard">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="rotation_id" value="{{ request('rotation_id') }}">
                            <input type="hidden" name="training_unit_id" value="{{ request('training_unit_id') }}">
                            <input type="hidden" name="risk_type" value="{{ request('risk_type') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <label for="per_page" class="me-2 text-nowrap">แสดง</label>
                            <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                <option value="5" @if($perPage == 5) selected @endif>5</option>
                                <option value="10" @if($perPage == 10) selected @endif>10</option>
                                <option value="15" @if($perPage == 15) selected @endif>15</option>
                                <option value="20" @if($perPage == 20) selected @endif>20</option>
                                <option value="25" @if($perPage == 25) selected @endif>25</option>
                                <option value="50" @if($perPage == 50) selected @endif>50</option>
                            </select>
                            <span class="ms-2 text-nowrap">รายการ</span>
                        </form>
                        @if ($trackedSoldiers->hasPages())
                            <div class="ms-auto" style="flex-shrink: 0;">
                                {{ $trackedSoldiers->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            </form> {{-- ✅ ปิด Form หลัก --}}
        </div>
    </div>

    {{-- MODALS (เหมือนเดิม) --}}
    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="appointmentForm" action="{{ route('mental-health.appointment.create') }}" method="POST">@csrf<div id="tracking_ids_container"></div><div class="modal-header"><h5 class="modal-title" id="appointmentModalLabel">สร้างนัดหมายส่งป่วย</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>คุณกำลังจะสร้างนัดหมายสำหรับ <strong id="selected_count">0</strong> รายการ</p><div class="mb-3"><label class="form-label">วันที่นัดหมาย <span class="text-danger">*</span></label><input type="date" name="appointment_date" class="form-control" required min="{{ date('Y-m-d') }}"></div><div class="mb-3"><label class="form-label">เวลา <span class="text-danger">*</span></label><input type="time" name="appointment_time" class="form-control" required></div><div class="mb-3"><label class="form-label">โรงพยาบาล <span class="text-danger">*</span></label><select name="appointment_location" class="form-select" required><option value="">-- กรุณาเลือก --</option><option value="รพ.ค่ายจิรประวัติ">รพ.ค่ายจิรประวัติ</option><option value="รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)">รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)</option><option value="รพ.จิตเวชราชนครินทร์">รพ.จิตเวชราชนครินทร์</option></select></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกนัดหมาย</button></div></form></div></div></div>
    <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="editAppointmentForm" action="{{ route('mental-health.appointment.update') }}" method="POST">@csrf<div id="edit_appointment_ids_container"></div><div class="modal-header"><h5 class="modal-title">แก้ไขข้อมูลนัดหมาย</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="text-muted"><small>หมายเหตุ: กรอกเฉพาะช่องที่ต้องการแก้ไข</small></p><div class="mb-3"><label class="form-label">วันที่นัดหมายใหม่</label><input type="date" name="appointment_date" class="form-control" min="{{ date('Y-m-d') }}"></div><div class="mb-3"><label class="form-label">เวลาใหม่</label><input type="time" name="appointment_time" class="form-control"></div><div class="mb-3"><label class="form-label">โรงพยาบาลใหม่</label><select name="appointment_location" class="form-select"><option value="">-- ไม่เปลี่ยนแปลง --</option><option value="รพ.ค่ายจิรประวัติ">รพ.ค่ายจิรประวัติ</option><option value="รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)">รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)</option><option value="รพ.จิตเวชราชนครินทร์">รพ.จิตเวชราชนครินทร์</option></select></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-info">บันทึกการเปลี่ยนแปลง</button></div></form></div></div></div>
    <div class="modal fade" id="treatmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="treatmentForm" method="POST">@csrf<input type="hidden" name="appointment_id" id="treatment_appointment_id"><div class="modal-header"><h5 class="modal-title">บันทึกผลการรักษาและปิดเคส</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">แพทย์ผู้รักษา <span class="text-danger">*</span></label><input type="text" name="doctor_name" class="form-control" required placeholder="ระบุชื่อแพทย์"></div><div class="mb-3"><label class="form-label">ยาที่รักษา</label><textarea name="medicine_name" class="form-control" rows="3" placeholder="ระบุยาที่ใช้รักษา..."></textarea></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3" placeholder="เช่น การวินิจฉัย, แผนการรักษา..."></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-success">บันทึกและปิดเคส</button></div></form></div></div></div>
    <div class="modal fade" id="bulkCloseFormModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="bulkCloseForm" action="{{ route('mental-health.case.bulk-close') }}" method="POST">@csrf<div id="bulk_close_ids_container"></div><div class="modal-header"><h5 class="modal-title">บันทึกและปิดเคสที่เลือก</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>คุณกำลังจะบันทึกผลและปิดเคสสำหรับ <strong id="bulk_close_count">0</strong> รายการ</p><div class="mb-3"><label class="form-label">แพทย์ผู้รักษา <span class="text-danger">*</span></label><input type="text" name="doctor_name" class="form-control" required placeholder="ระบุชื่อแพทย์ (สำหรับทุกเคสที่เลือก)"></div><div class="mb-3"><label class="form-label">ยาที่รักษา</label><textarea name="medicine_name" class="form-control" rows="3"></textarea></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-warning">ยืนยันและปิดเคสทั้งหมด</button></div></form></div></div></div>
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="viewAppointmentModalLabel">ข้อมูลนัดหมาย</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label fw-bold">วันที่นัดหมาย:</label><p id="view_appointment_date" class="form-control-plaintext ps-2">-</p></div><div class="mb-3"><label class="form-label fw-bold">เวลา:</label><p id="view_appointment_time" class="form-control-plaintext ps-2">-</p></div><div class="mb-3"><label class="form-label fw-bold">โรงพยาบาล:</label><p id="view_appointment_location" class="form-control-plaintext ps-2">-</p></div><div class="mb-3"><label class="form-label fw-bold">ข้อมูลเพิ่มเติม:</label><div id="view_appointment_notes" class="form-control-plaintext ps-2" style="white-space: pre-wrap; min-height: 60px;">-</div></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button></div></div></div></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- ส่วนที่ 1: โค้ดเดิมของคุณสำหรับจัดการปุ่มต่างๆ ---
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const soldierCheckboxes = document.querySelectorAll('.soldier-checkbox');
    const bulkSendButton = document.getElementById('bulkSendButton');
    const bulkEditAppointmentButton = document.getElementById('bulkEditAppointmentButton');
    const bulkCloseButton = document.getElementById('bulkCloseButton');

    function updateBulkButtonsState() {
        const selectedCheckboxes = document.querySelectorAll('.soldier-checkbox:checked');
        const count = selectedCheckboxes.length;
        if (count === 0) {
            bulkSendButton.disabled = true;
            bulkEditAppointmentButton.disabled = true;
            bulkCloseButton.disabled = true;
            return;
        }
        let allRequired = true;
        let allScheduled = true;
        selectedCheckboxes.forEach(checkbox => {
            const status = checkbox.closest('tr').getAttribute('data-status');
            if (status !== 'required') allRequired = false;
            if (status !== 'scheduled') allScheduled = false;
        });
        bulkSendButton.disabled = !allRequired;
        bulkEditAppointmentButton.disabled = !allScheduled;
        bulkCloseButton.disabled = !allScheduled;
    }

    if(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            soldierCheckboxes.forEach(checkbox => checkbox.checked = selectAllCheckbox.checked);
            updateBulkButtonsState();
        });
    }
    soldierCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if(selectAllCheckbox) {
                selectAllCheckbox.checked = document.querySelectorAll('.soldier-checkbox:checked').length === soldierCheckboxes.length;
            }
            updateBulkButtonsState();
        });
    });
    // --- จบส่วนโค้ดเดิมของคุณ ---

    // ✅✅✅ START: โค้ดใหม่สำหรับฟอร์มดาวน์โหลด PDF ✅✅✅
    const downloadForm = document.getElementById('mainDashboardForm');
    const selectedIdsInput = document.getElementById('selected_ids');

    if (downloadForm) {
        downloadForm.addEventListener('submit', function(event) {
            const action = event.submitter ? event.submitter.value : null;

            // ตรวจสอบเฉพาะปุ่มที่ต้องมีการเลือกรายการ
            const requiresSelection = [
                'download_required_selected',
                'download_scheduled_selected'
            ].includes(action);

            if (requiresSelection) {
                const selectedCheckboxes = document.querySelectorAll('.soldier-checkbox:checked');
                const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

                if (selectedIds.length === 0) {
                    event.preventDefault(); // หยุดการส่งฟอร์ม
                    alert('กรุณาเลือกรายการที่ต้องการดาวน์โหลดอย่างน้อย 1 รายการ');
                    return;
                }
                selectedIdsInput.value = selectedIds.join(',');
            }
        });
    }
    // ✅✅✅ END: โค้ดใหม่สำหรับฟอร์มดาวน์โหลด PDF ✅✅✅


    // --- โค้ดส่วนจัดการ Modal เดิมของคุณ ---
    const appointmentFormContainer = document.getElementById('tracking_ids_container');
    const selectedCountSpan = document.getElementById('selected_count');
    document.querySelectorAll('.refer-btn').forEach(button => {
        button.addEventListener('click', function () {
            appointmentFormContainer.innerHTML = `<input type="hidden" name="tracking_ids[]" value="${this.dataset.id}">`;
            selectedCountSpan.textContent = 1;
        });
    });
    if(bulkSendButton) {
        bulkSendButton.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length > 0) {
                appointmentFormContainer.innerHTML = selectedIds.map(id => `<input type="hidden" name="tracking_ids[]" value="${id}">`).join('');
                selectedCountSpan.textContent = selectedIds.length;
            }
        });
    }

    const editAppointmentForm = document.getElementById('editAppointmentForm');
    const editAppointmentIdsContainer = document.getElementById('edit_appointment_ids_container');
    document.querySelectorAll('.edit-appointment-btn').forEach(button => button.addEventListener('click', function() {
        editAppointmentIdsContainer.innerHTML = `<input type="hidden" name="appointment_ids[]" value="${this.dataset.appointmentId}">`;
        editAppointmentForm.querySelector('[name="appointment_date"]').value = this.dataset.date;
        editAppointmentForm.querySelector('[name="appointment_time"]').value = this.dataset.time;
        editAppointmentForm.querySelector('[name="appointment_location"]').value = this.dataset.location;
        editAppointmentForm.querySelector('[name="notes"]').value = this.dataset.notes;
    }));
    if(bulkEditAppointmentButton) bulkEditAppointmentButton.addEventListener('click', function() {
        const selectedAppointmentIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.closest('tr').getAttribute('data-appointment-id')).filter(id => id);
        if (selectedAppointmentIds.length > 0) {
            editAppointmentIdsContainer.innerHTML = selectedAppointmentIds.map(id => `<input type="hidden" name="appointment_ids[]" value="${id}">`).join('');
            editAppointmentForm.reset();
        }
    });

    document.querySelectorAll('.view-appointment-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('view_appointment_date').textContent = this.dataset.date || '-';
            document.getElementById('view_appointment_time').textContent = this.dataset.time || '-';
            document.getElementById('view_appointment_location').textContent = this.dataset.location || '-';
            document.getElementById('view_appointment_notes').textContent = this.dataset.notes || '-';
        });
    });

    const treatmentForm = document.getElementById('treatmentForm');
    const treatmentAppointmentIdInput = document.getElementById('treatment_appointment_id');
    document.querySelectorAll('.close-case-btn').forEach(button => {
        button.addEventListener('click', function () {
            treatmentForm.action = `{{ url('mental-health/close-case') }}/${this.getAttribute('data-tracking-id')}`;
            treatmentAppointmentIdInput.value = this.getAttribute('data-appointment-id');
        });
    });

    const bulkCloseFormContainer = document.getElementById('bulk_close_ids_container');
    const bulkCloseCountSpan = document.getElementById('bulk_close_count');
    if(bulkCloseButton) {
        bulkCloseButton.addEventListener('click', function() {
            const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length > 0) {
                bulkCloseFormContainer.innerHTML = selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
                bulkCloseCountSpan.textContent = selectedIds.length;
            }
        });
    }
});
</script>
</body>
</html>
