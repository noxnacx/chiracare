<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ระบบจัดการผู้ป่วยพิเศษ</title>

    {{-- Fonts, Icons, Framework --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Modern Hospital Theme Styles --}}
    <style>
        :root {
            --bs-primary: #0a4d68; --bs-primary-rgb: 10, 77, 104;
            --bs-secondary: #6c757d; --bs-secondary-rgb: 108, 117, 125;
            --bs-success: #198754; --bs-success-rgb: 25, 135, 84;
            --bs-info: #0dcaf0; --bs-info-rgb: 13, 202, 240;
            --bs-warning: #ffc107; --bs-warning-rgb: 255, 193, 7;
            --bs-danger: #dc3545; --bs-danger-rgb: 220, 53, 69;
            --bs-light: #f8f9fa; --bs-dark: #212529;
            --font-family-sans-serif: 'IBM Plex Sans Thai', sans-serif;
            --background-color: #f0f2f5; --card-bg: #ffffff;
            --card-border-color: #e9ecef; --text-color: #343a40;
            --text-muted: #6c757d;
        }
        body { background-color: var(--background-color); font-family: var(--font-family-sans-serif); color: var(--text-color); }
        .content-wrapper { margin-left: 0 !important; padding: 1.5rem; }
        .card { border: 1px solid var(--card-border-color); border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; background-color: var(--card-bg); }
        .card-header { background-color: var(--bs-light); border-bottom: 1px solid var(--card-border-color); padding: 1rem 1.25rem; font-weight: 600; }
        .table-modern { border-collapse: separate; border-spacing: 0 8px; }
        .table-modern thead th { background-color: var(--bs-light); border: none; font-weight: 600; padding: 1rem; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .table-modern tbody tr { background-color: var(--card-bg); transition: all 0.2s ease; }
        .table-modern td { padding: 1rem; vertical-align: middle; border: none; border-top: 1px solid var(--card-border-color); border-bottom: 1px solid var(--card-border-color); }
        .table-modern td:first-child { border-left: 1px solid var(--card-border-color); border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; }
        .table-modern td:last-child { border-right: 1px solid var(--card-border-color); border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
        .badge { font-size: 0.8em; font-weight: 600; padding: 0.5em 0.75em; }
        .badge.bg-primary { background-color: rgba(var(--bs-primary-rgb), 0.15) !important; color: var(--bs-primary) !important; }
        .badge.bg-success { background-color: rgba(var(--bs-success-rgb), 0.15) !important; color: var(--bs-success) !important; }
        .badge.bg-warning { background-color: rgba(var(--bs-warning-rgb), 0.2) !important; color: #664d03 !important; }
        .badge.bg-info { background-color: rgba(var(--bs-info-rgb), 0.15) !important; color: #055160 !important; }
        .badge.bg-danger { background-color: rgba(var(--bs-danger-rgb), 0.15) !important; color: var(--bs-danger) !important; }
        .btn { border-radius: 0.5rem; font-weight: 500; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .page-header h1 { margin-bottom: 0; }
        .form-control, .form-select { border-radius: 0.5rem; background-color: var(--bs-light); border-color: var(--card-border-color); }
        .modal-content { border-radius: 0.75rem; border: none; }
        .modal-header, .modal-footer { border-color: var(--card-border-color); }
        .main-content-grid { display: grid; grid-template-columns: 1fr 420px; gap: 1.5rem; }
        @media (max-width: 1200px) { .main-content-grid { grid-template-columns: 1fr; } }

        .stat-item { display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 0; border-bottom: 1px solid var(--card-border-color); }
        .stat-item:last-child { border-bottom: none; }
        .stat-item .stat-label { font-weight: 500; }
        .stat-item .stat-value { font-weight: 600; font-size: 1.2rem; }
        .stat-item .text-info { color: #17a2b8 !important; }
        .stat-item .text-warning { color: var(--bs-warning) !important; }
        .stat-item .text-primary { color: var(--bs-primary) !important; }
        .stat-item .text-success { color: var(--bs-success) !important; }

        .waiting-list-item { display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 0; border-bottom: 1px solid var(--card-border-color); }
        .waiting-list-item:last-child { border-bottom: none; }
        .action-item { display: flex; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid var(--card-border-color); transition: background-color 0.2s ease-in-out; }
        .action-item:last-child { border-bottom: none; }
        .action-item-link { text-decoration: none; color: inherit; display: block; }
        .action-item-link:hover .action-item { background-color: var(--bs-light); }
        .action-icon-wrapper { width: 48px; height: 48px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 1rem; flex-shrink: 0; }
        .action-icon-wrapper i { font-size: 1.25rem; }
        .action-text { flex-grow: 1; }
        .action-title { font-weight: 600; font-size: 1rem; color: var(--text-color); }
        .action-desc { font-size: 0.85rem; color: var(--text-muted); }
        .action-arrow { color: #adb5bd; }
        .bg-primary-soft { background-color: rgba(var(--bs-primary-rgb), 0.1); } .bg-info-soft { background-color: rgba(var(--bs-info-rgb), 0.1); }
        .bg-warning-soft { background-color: rgba(var(--bs-warning-rgb), 0.15); } .bg-success-soft { background-color: rgba(var(--bs-success-rgb), 0.1); }
        .bg-secondary-soft { background-color: rgba(var(--bs-secondary-rgb), 0.1); }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="page-header">
                <div><h1 class="h2 fw-bold">ระบบจัดการผู้ป่วยพิเศษ</h1><p class="text-muted mb-0">ภาพรวมและจัดการเคสที่ต้องดูแลทางจิตเวช</p></div>
            </div>
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
            @if (session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

            <div class="main-content-grid">

                {{-- LEFT COLUMN --}}
                <div class="main-table-container">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('mental-health.dashboard') }}" id="filterForm">
                                <div class="row g-2 align-items-center">
                                    <div class="col-lg"><input type="text" name="search" class="form-control form-control-lg" placeholder="ค้นหาด้วยชื่อ, นามสกุล, หรือบัตรประชาชน..." value="{{ request('search') }}"></div>
                                    <div class="col-auto"><button type="button" class="btn btn-outline-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#filterModal"><i class="fas fa-filter me-1"></i>ตัวกรอง</button></div>
                                    <div class="col-auto"><button type="submit" class="btn btn-primary btn-lg" title="ค้นหา"><i class="fas fa-search"></i></button></div>
                                    <div class="col-auto"><a href="{{ route('mental-health.dashboard') }}" class="btn btn-light btn-lg" title="รีเซ็ต"><i class="fas fa-undo"></i></a></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <form action="{{ route('mental-health.dashboard.download') }}" method="POST" id="mainDashboardForm">
                        @csrf
                        <input type="hidden" name="selected_ids" id="selected_ids">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" id="bulkSendButton" data-bs-toggle="modal" data-bs-target="#appointmentModal" disabled><i class="fas fa-paper-plane me-2"></i>ส่งป่วยที่เลือก</button>
                                <button type="button" class="btn btn-info text-white" id="bulkEditAppointmentButton" data-bs-toggle="modal" data-bs-target="#editAppointmentModal" disabled><i class="fas fa-edit me-2"></i>แก้ไขนัดหมาย</button>
                                <button type="button" class="btn btn-success" id="bulkCloseButton" data-bs-toggle="modal" data-bs-target="#bulkCloseFormModal" disabled><i class="fas fa-check-double me-2"></i>ปิดเคสที่เลือก</button>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-download me-2"></i>ดาวน์โหลด PDF</button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="downloadDropdown">
                                    <li><h6 class="dropdown-header">เคสรอส่งป่วย</h6></li>
                                    <li><button class="dropdown-item" type="submit" name="action" value="download_required_selected"><i class="fas fa-check-square fa-fw me-2"></i>ที่เลือก</button></li>
                                    <li><button class="dropdown-item" type="submit" name="action" value="download_required_all"><i class="fas fa-file-alt fa-fw me-2"></i>ทั้งหมด</button></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">เคสนัดหมายแล้ว</h6></li>
                                    <li><button class="dropdown-item" type="submit" name="action" value="download_scheduled_selected"><i class="fas fa-check-square fa-fw me-2"></i>ที่เลือก</button></li>
                                    <li><button class="dropdown-item" type="submit" name="action" value="download_scheduled_all"><i class="fas fa-file-alt fa-fw me-2"></i>ทั้งหมด</button></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-modern align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 5%;"><input type="checkbox" class="form-check-input" id="selectAllCheckbox"></th>
                                                <th style="width: 25%;">ชื่อ-สกุล / ID</th>
                                                <th style="width: 15%;">ผลัด/หน่วย</th>
                                                <th style="width: 15%;">ความเสี่ยง</th>
                                                <th style="width: 15%;">แบบประเมิน</th>
                                                <th style="width: 10%;">วันที่รับ</th>
                                                <th style="width: 10%;">สถานะ</th>
                                                <th class="text-center" style="width: 5%;">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($trackedSoldiers as $item)
                                                <tr>
                                                    <td class="text-center"><input type="checkbox" class="form-check-input soldier-checkbox" value="{{ $item->id }}"></td>
                                                    <td><div class="fw-bold">{{ $item->soldier->first_name ?? '' }} {{ $item->soldier->last_name ?? '' }}</div><small class="text-muted">{{ $item->soldier->soldier_id_card ?? 'N/A' }}</small></td>
                                                    <td><div>{{ $item->soldier->rotation->rotation_name ?? '-' }}</div><small class="text-muted">{{ $item->soldier->trainingUnit->unit_name ?? '-' }}</small></td>
                                                    <td>@if($item->risk_type == 'at_risk') <span class="badge bg-warning">จากผลประเมิน</span> @else <span class="badge bg-info">มีประวัติเดิม</span> @endif</td>
                                                    <td>
                                                        @foreach($item->all_risk_assessment_types as $typeKey)
                                                            <span class="badge bg-danger me-1">{{ ['depression'=>'ซึมเศร้า','suicide_risk'=>'เสี่ยงฆ่าตัวตาย'][$typeKey] ?? $typeKey }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->thaidate('j M y') }}</td>
                                                    <td>@if($item->status == 'required') <span class="badge bg-primary">รอส่งป่วย</span> @elseif($item->status == 'scheduled') <span class="badge bg-success">นัดหมายแล้ว</span> @endif</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary action-modal-trigger" data-bs-toggle="modal" data-bs-target="#actionModal" data-patient-name="{{ $item->soldier->first_name ?? '' }} {{ $item->soldier->last_name ?? '' }}">จัดการ</button>
                                                        <template class="action-template">
                                                            @php $latestAppointment = $item->appointments->sortByDesc('created_at')->first(); @endphp
                                                            @if($item->status == 'required')
                                                                <a href="#" class="action-item-link refer-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#appointmentModal"><div class="action-item"><div class="action-icon-wrapper bg-primary-soft"><i class="fas fa-paper-plane text-primary"></i></div><div class="action-text"><div class="action-title">ส่งป่วย</div><div class="action-desc">สร้างนัดหมายเพื่อส่งพบแพทย์</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                                            @elseif($item->status == 'scheduled' && $latestAppointment)
                                                                <a href="#" class="action-item-link edit-appointment-btn" data-bs-toggle="modal" data-bs-target="#editAppointmentModal" data-appointment-id="{{ $latestAppointment->id }}" data-date="{{ $latestAppointment->appointment_date }}" data-time="{{ \Carbon\Carbon::parse($latestAppointment->appointment_time)->format('H:i') }}" data-location="{{ $latestAppointment->appointment_location }}" data-notes="{{ $latestAppointment->notes ?? '' }}"><div class="action-item"><div class="action-icon-wrapper bg-warning-soft"><i class="fas fa-pencil-alt text-warning"></i></div><div class="action-text"><div class="action-title">แก้ไขนัดหมาย</div><div class="action-desc">อัปเดตวัน, เวลา, และสถานที่</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                                                <a href="#" class="action-item-link close-case-btn" data-bs-toggle="modal" data-bs-target="#treatmentModal" data-tracking-id="{{ $item->id }}" data-appointment-id="{{ $latestAppointment->id }}"><div class="action-item"><div class="action-icon-wrapper bg-success-soft"><i class="fas fa-check-double text-success"></i></div><div class="action-text"><div class="action-title">ปิดเคส</div><div class="action-desc">บันทึกผลการรักษาและปิดเคส</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                                            @endif
                                                            <hr class="my-0">
                                                            @if($item->risk_type == 'at_risk')
                                                                {{-- ✅✅✅ START: Corrected Alignment Form ✅✅✅ --}}
                                                                <form action="{{ route('mental-health.risk-type.update', $item->id) }}" method="POST" onsubmit="return confirm('คุณต้องการเปลี่ยนเคสนี้เป็น \'เคสมีประวัติเดิม\' ใช่หรือไม่?');" class="action-item-link d-block">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-link text-decoration-none text-start p-0 w-100">
                                                                        <div class="action-item"><div class="action-icon-wrapper bg-secondary-soft"><i class="fas fa-user-clock text-secondary"></i></div><div class="action-text"><div class="action-title">เปลี่ยนเป็นเคสประวัติเดิม</div><div class="action-desc">สำหรับเคสที่ไม่ได้มาจากผลประเมิน</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div>
                                                                    </button>
                                                                </form>
                                                                {{-- ✅✅✅ END: Corrected Alignment Form ✅✅✅ --}}
                                                            @endif
                                                            <a class="action-item-link" href="{{ route('mental-health.history', $item->soldier_id) }}"><div class="action-item"><div class="action-icon-wrapper bg-secondary-soft"><i class="fas fa-history text-secondary"></i></div><div class="action-text"><div class="action-title">ดูประวัติทั้งหมด</div><div class="action-desc">ดูไทม์ไลน์การรักษาก่อนหน้า</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                                        </template>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="text-center py-5"><div class="text-muted"><i class="fas fa-folder-open fs-2 mb-2"></i><p>ไม่พบข้อมูลที่ตรงกับเงื่อนไข</p></div></td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if ($trackedSoldiers->hasPages())
                            <div class="card-footer d-flex flex-wrap align-items-center justify-content-between">
                                <form method="GET" action="{{ route('mental-health.dashboard') }}" class="d-flex align-items-center" id="perPageForm-dashboard"><input type="hidden" name="search" value="{{ request('search') }}"><input type="hidden" name="rotation_id" value="{{ request('rotation_id') }}"><input type="hidden" name="training_unit_id" value="{{ request('training_unit_id') }}"><input type="hidden" name="risk_type" value="{{ request('risk_type') }}"><input type="hidden" name="status" value="{{ request('status') }}"><input type="hidden" name="start_date" value="{{ request('start_date') }}"><input type="hidden" name="end_date" value="{{ request('end_date') }}"><label for="per_page" class="me-2 text-nowrap">แสดง</label><select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()"><option value="15" @if($perPage == 15) selected @endif>15</option><option value="25" @if($perPage == 25) selected @endif>25</option><option value="50" @if($perPage == 50) selected @endif>50</option><option value="100" @if($perPage == 100) selected @endif>100</option></select><span class="ms-2 text-nowrap">รายการ</span></form>
                                <div class="ms-auto" style="flex-shrink: 0;">{{ $trackedSoldiers->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- RIGHT COLUMN (SIDEBAR) --}}
                <div class="details-sidebar">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-chart-bar me-2"></i> สรุปข้อมูลเคส</div>
                        <div class="card-body p-3">
                            <div class="stat-item"><div class="stat-label"><i class="fas fa-users me-2 text-info"></i> เคสทั้งหมด</div><div class="stat-value text-info">{{ $totalCases }}</div></div>
                            <div class="stat-item"><div class="stat-label"><i class="fas fa-hourglass-half me-2 text-warning"></i> รอส่งป่วย</div><div class="stat-value text-warning">{{ $requiredCases }}</div></div>
                            <div class="stat-item"><div class="stat-label"><i class="far fa-calendar-check me-2 text-primary"></i> นัดหมายแล้ว</div><div class="stat-value text-primary">{{ $scheduledCases }}</div></div>
                            <div class="stat-item"><div class="stat-label"><i class="fas fa-user-check me-2 text-success"></i> พบแพทย์แล้ว</div><div class="stat-value text-success">{{ $completedCases }}</div></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><i class="fas fa-list-alt me-2"></i> รายชื่อรอส่งป่วย</div>
                        <div class="card-body p-3" style="max-height: 45vh; overflow-y: auto;">
                             @forelse ($waitingForReferral as $item)
                                <div class="waiting-list-item">
                                    <div>
                                        <div class="fw-bold">{{ $item->soldier->first_name }} {{ $item->soldier->last_name }}</div>
                                        <small class="text-muted">{{ $item->soldier->trainingUnit->unit_name ?? 'N/A' }} | รอมา {{ $item->created_at->diffForHumans(null, true) }}</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary action-modal-trigger" data-bs-toggle="modal" data-bs-target="#actionModal" data-patient-name="{{ $item->soldier->first_name ?? '' }} {{ $item->soldier->last_name ?? '' }}">จัดการ</button>
                                    <template class="action-template">
                                        @php $latestAppointment = $item->appointments->sortByDesc('created_at')->first(); @endphp
                                        @if($item->status == 'required')
                                            <a href="#" class="action-item-link refer-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#appointmentModal"><div class="action-item"><div class="action-icon-wrapper bg-primary-soft"><i class="fas fa-paper-plane text-primary"></i></div><div class="action-text"><div class="action-title">ส่งป่วย</div><div class="action-desc">สร้างนัดหมายเพื่อส่งพบแพทย์</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                        @elseif($item->status == 'scheduled' && $latestAppointment)
                                            <a href="#" class="action-item-link edit-appointment-btn" data-bs-toggle="modal" data-bs-target="#editAppointmentModal" data-appointment-id="{{ $latestAppointment->id }}" data-date="{{ $latestAppointment->appointment_date }}" data-time="{{ \Carbon\Carbon::parse($latestAppointment->appointment_time)->format('H:i') }}" data-location="{{ $latestAppointment->appointment_location }}" data-notes="{{ $latestAppointment->notes ?? '' }}"><div class="action-item"><div class="action-icon-wrapper bg-warning-soft"><i class="fas fa-pencil-alt text-warning"></i></div><div class="action-text"><div class="action-title">แก้ไขนัดหมาย</div><div class="action-desc">อัปเดตวัน, เวลา, และสถานที่</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                            <a href="#" class="action-item-link close-case-btn" data-bs-toggle="modal" data-bs-target="#treatmentModal" data-tracking-id="{{ $item->id }}" data-appointment-id="{{ $latestAppointment->id }}"><div class="action-item"><div class="action-icon-wrapper bg-success-soft"><i class="fas fa-check-double text-success"></i></div><div class="action-text"><div class="action-title">ปิดเคส</div><div class="action-desc">บันทึกผลการรักษาและปิดเคส</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                        @endif
                                        <hr class="my-0">
                                        @if($item->risk_type == 'at_risk')
                                            {{-- ✅✅✅ START: Corrected Alignment Form ✅✅✅ --}}
                                            <form action="{{ route('mental-health.risk-type.update', $item->id) }}" method="POST" onsubmit="return confirm('คุณต้องการเปลี่ยนเคสนี้เป็น \'เคสมีประวัติเดิม\' ใช่หรือไม่?');" class="action-item-link d-block">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-decoration-none text-start p-0 w-100">
                                                    <div class="action-item"><div class="action-icon-wrapper bg-secondary-soft"><i class="fas fa-user-clock text-secondary"></i></div><div class="action-text"><div class="action-title">เปลี่ยนเป็นเคสประวัติเดิม</div><div class="action-desc">สำหรับเคสที่ไม่ได้มาจากผลประเมิน</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div>
                                                </button>
                                            </form>
                                            {{-- ✅✅✅ END: Corrected Alignment Form ✅✅✅ --}}
                                        @endif
                                        <a class="action-item-link" href="{{ route('mental-health.history', $item->soldier_id) }}"><div class="action-item"><div class="action-icon-wrapper bg-secondary-soft"><i class="fas fa-history text-secondary"></i></div><div class="action-text"><div class="action-title">ดูประวัติทั้งหมด</div><div class="action-desc">ดูไทม์ไลน์การรักษาก่อนหน้า</div></div><div class="action-arrow"><i class="fas fa-chevron-right"></i></div></div></a>
                                    </template>
                                </div>
                            @empty
                                <div class="text-center text-muted p-3"><i class="fas fa-check-circle d-block mb-2 fs-2 text-success"></i>ไม่มีเคสที่รอส่งป่วย</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- All Modals --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter me-2"></i>ตัวกรองเพิ่มเติม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3"><div class="col-md-6"><label for="rotation_id" class="form-label">ผลัด</label><select name="rotation_id" class="form-select" form="filterForm"><option value="">-- ทุกผลัด --</option>@foreach($rotations as $rotation)<option value="{{ $rotation->id }}" {{ request('rotation_id') == $rotation->id ? 'selected' : '' }}>{{ $rotation->rotation_name }}</option>@endforeach</select></div><div class="col-md-6"><label for="training_unit_id" class="form-label">หน่วยฝึก</label><select name="training_unit_id" class="form-select" form="filterForm"><option value="">-- ทุกหน่วยฝึก --</option>@foreach($trainingUnits as $unit)<option value="{{ $unit->id }}" {{ request('training_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->unit_name }}</option>@endforeach</select></div><div class="col-md-6"><label for="risk_type" class="form-label">ประเภทความเสี่ยง</label><select name="risk_type" class="form-select" form="filterForm"><option value="">-- ทุกประเภท --</option><option value="at_risk" {{ request('risk_type') == 'at_risk' ? 'selected' : '' }}>จากผลประเมิน</option><option value="prior_history" {{ request('risk_type') == 'prior_history' ? 'selected' : '' }}>มีประวัติเดิม</option></select></div><div class="col-md-6"><label for="status" class="form-label">สถานะ</label><select name="status" class="form-select" form="filterForm"><option value="">-- ทุกสถานะ --</option><option value="required" {{ request('status') == 'required' ? 'selected' : '' }}>รอส่งป่วย</option><option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>นัดแล้วหมายแล้ว</option></select></div><div class="col-12"><label class="form-label">ช่วงวันที่ได้รับข้อมูล</label><div class="input-group"><input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" title="วันที่เริ่ม" form="filterForm"><span class="input-group-text">ถึง</span><input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" title="วันที่สิ้นสุด" form="filterForm"></div></div></div></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ปิด</button><button type="submit" class="btn btn-primary" form="filterForm"><i class="fas fa-check me-1"></i>ใช้ตัวกรอง</button></div></div></div></div>
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="actionModalLabel">จัดการ</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-0" id="actionModalBody"></div></div></div></div>
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="appointmentForm" action="{{ route('mental-health.appointment.create') }}" method="POST">@csrf<div id="tracking_ids_container"></div><div class="modal-header"><h5 class="modal-title">สร้างนัดหมายส่งป่วย</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>คุณกำลังจะสร้างนัดหมายสำหรับ <strong id="selected_count">0</strong> รายการ</p><div class="mb-3"><label class="form-label">วันที่นัดหมาย <span class="text-danger">*</span></label><input type="date" name="appointment_date" class="form-control" required min="{{ date('Y-m-d') }}"></div><div class="mb-3"><label class="form-label">เวลา <span class="text-danger">*</span></label><input type="time" name="appointment_time" class="form-control" required></div><div class="mb-3"><label class="form-label">โรงพยาบาล <span class="text-danger">*</span></label><select name="appointment_location" class="form-select" required><option value="">-- กรุณาเลือก --</option><option value="รพ.ค่ายจิรประวัติ">รพ.ค่ายจิรประวัติ</option><option value="รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)">รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)</option><option value="รพ.จิตเวชราชนครินทร์">รพ.จิตเวชราชนครินทร์</option></select></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-primary">บันทึกนัดหมาย</button></div></form></div></div></div>
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="editAppointmentForm" action="{{ route('mental-health.appointment.update') }}" method="POST">@csrf<div id="edit_appointment_ids_container"></div><div class="modal-header"><h5 class="modal-title">แก้ไขข้อมูลนัดหมาย</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="text-muted"><small>หมายเหตุ: กรอกเฉพาะช่องที่ต้องการแก้ไข</small></p><div class="mb-3"><label class="form-label">วันที่นัดหมายใหม่</label><input type="date" name="appointment_date" class="form-control" min="{{ date('Y-m-d') }}"></div><div class="mb-3"><label class="form-label">เวลาใหม่</label><input type="time" name="appointment_time" class="form-control"></div><div class="mb-3"><label class="form-label">โรงพยาบาลใหม่</label><select name="appointment_location" class="form-select"><option value="">-- ไม่เปลี่ยนแปลง --</option><option value="รพ.ค่ายจิรประวัติ">รพ.ค่ายจิรประวัติ</option><option value="รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)">รพ.สวรรค์ประชารักษ์ (จิตเวชสี่แคว)</option><option value="รพ.จิตเวชราชนครินทร์">รพ.จิตเวชราชนครินทร์</option></select></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-info">บันทึกการเปลี่ยนแปลง</button></div></form></div></div></div>
<div class="modal fade" id="treatmentModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="treatmentForm" method="POST">@csrf<input type="hidden" name="appointment_id" id="treatment_appointment_id"><div class="modal-header"><h5 class="modal-title">บันทึกผลการรักษาและปิดเคส</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">แพทย์ผู้รักษา <span class="text-danger">*</span></label><input type="text" name="doctor_name" class="form-control" required placeholder="ระบุชื่อแพทย์"></div><div class="mb-3"><label class="form-label">ยาที่รักษา</label><textarea name="medicine_name" class="form-control" rows="3" placeholder="ระบุยาที่ใช้รักษา..."></textarea></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3" placeholder="เช่น การวินิจฉัย, แผนการรักษา..."></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-success">บันทึกและปิดเคส</button></div></form></div></div></div>
<div class="modal fade" id="bulkCloseFormModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><form id="bulkCloseForm" action="{{ route('mental-health.case.bulk-close') }}" method="POST">@csrf<div id="bulk_close_ids_container"></div><div class="modal-header"><h5 class="modal-title">บันทึกและปิดเคสที่เลือก</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>คุณกำลังจะบันทึกผลและปิดเคสสำหรับ <strong id="bulk_close_count">0</strong> รายการ</p><div class="mb-3"><label class="form-label">แพทย์ผู้รักษา <span class="text-danger">*</span></label><input type="text" name="doctor_name" class="form-control" required placeholder="ระบุชื่อแพทย์ (สำหรับทุกเคสที่เลือก)"></div><div class="mb-3"><label class="form-label">ยาที่รักษา</label><textarea name="medicine_name" class="form-control" rows="3"></textarea></div><div class="mb-3"><label class="form-label">ข้อมูลเพิ่มเติม</label><textarea name="notes" class="form-control" rows="3"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button><button type="submit" class="btn btn-warning">ยืนยันและปิดเคสทั้งหมด</button></div></form></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
    soldierCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updateBulkButtonsState));
    const downloadForm = document.getElementById('mainDashboardForm');
    const selectedIdsInput = document.getElementById('selected_ids');
    if (downloadForm) {
        downloadForm.addEventListener('submit', function(event) {
            const action = event.submitter ? event.submitter.value : null;
            const requiresSelection = ['download_required_selected', 'download_scheduled_selected'].includes(action);
            if (requiresSelection) {
                const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.value);
                if (selectedIds.length === 0) {
                    event.preventDefault();
                    alert('กรุณาเลือกรายการที่ต้องการดาวน์โหลดอย่างน้อย 1 รายการ');
                    return;
                }
                selectedIdsInput.value = selectedIds.join(',');
            }
        });
    }
    document.body.addEventListener('click', function(event) {
        const actionTrigger = event.target.closest('.action-modal-trigger, .refer-btn, #bulkSendButton, .edit-appointment-btn, #bulkEditAppointmentButton, .close-case-btn, #bulkCloseButton');
        if (!actionTrigger) return;
        if (actionTrigger.matches('.action-modal-trigger')) {
            const patientName = actionTrigger.dataset.patientName;
            const template = actionTrigger.parentElement.querySelector('.action-template');
            document.getElementById('actionModalLabel').textContent = `จัดการ: ${patientName}`;
            document.getElementById('actionModalBody').innerHTML = template.innerHTML;
        }
        const appointmentModalEl = document.getElementById('appointmentModal');
        if (appointmentModalEl) {
            const appointmentFormContainer = appointmentModalEl.querySelector('#tracking_ids_container');
            const selectedCountSpan = appointmentModalEl.querySelector('#selected_count');
            if (actionTrigger.matches('.refer-btn')) {
                appointmentFormContainer.innerHTML = `<input type="hidden" name="tracking_ids[]" value="${actionTrigger.dataset.id}">`;
                selectedCountSpan.textContent = 1;
            } else if (actionTrigger.matches('#bulkSendButton')) {
                const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.value);
                if (selectedIds.length > 0) {
                    appointmentFormContainer.innerHTML = selectedIds.map(id => `<input type="hidden" name="tracking_ids[]" value="${id}">`).join('');
                    selectedCountSpan.textContent = selectedIds.length;
                }
            }
        }
        const editAppointmentModalEl = document.getElementById('editAppointmentModal');
        if (editAppointmentModalEl) {
            const editForm = editAppointmentModalEl.querySelector('#editAppointmentForm');
            const editIdsContainer = editAppointmentModalEl.querySelector('#edit_appointment_ids_container');
            if (actionTrigger.matches('.edit-appointment-btn')) {
                editIdsContainer.innerHTML = `<input type="hidden" name="appointment_ids[]" value="${actionTrigger.dataset.appointmentId}">`;
                editForm.querySelector('[name="appointment_date"]').value = actionTrigger.dataset.date;
                editForm.querySelector('[name="appointment_time"]').value = actionTrigger.dataset.time;
                editForm.querySelector('[name="appointment_location"]').value = actionTrigger.dataset.location;
                editForm.querySelector('[name="notes"]').value = actionTrigger.dataset.notes;
            } else if (actionTrigger.matches('#bulkEditAppointmentButton')) {
                const selectedAppointmentIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.closest('tr').getAttribute('data-appointment-id')).filter(id => id);
                if (selectedAppointmentIds.length > 0) {
                    editIdsContainer.innerHTML = selectedAppointmentIds.map(id => `<input type="hidden" name="appointment_ids[]" value="${id}">`).join('');
                    editForm.reset();
                }
            }
        }
        const treatmentModalEl = document.getElementById('treatmentModal');
        if (treatmentModalEl && actionTrigger.matches('.close-case-btn')) {
            treatmentModalEl.querySelector('#treatmentForm').action = `{{ url('mental-health/close-case') }}/${actionTrigger.getAttribute('data-tracking-id')}`;
            treatmentModalEl.querySelector('#treatment_appointment_id').value = actionTrigger.getAttribute('data-appointment-id');
        }
        const bulkCloseModalEl = document.getElementById('bulkCloseFormModal');
        if(bulkCloseModalEl && actionTrigger.matches('#bulkCloseButton')) {
            const selectedIds = Array.from(document.querySelectorAll('.soldier-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length > 0) {
                bulkCloseModalEl.querySelector('#bulk_close_ids_container').innerHTML = selectedIds.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
                bulkCloseModalEl.querySelector('#bulk_close_count').textContent = selectedIds.length;
            }
        }
    });
    updateBulkButtonsState();
});
</script>
</body>
</html>
