<!DOCTYPE html>
<html lang="th">

@include('themes.head')
<style>
    .container_waitappiontment {
        background-color: #fff;
        height: 500px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* เงาเบาๆ */
        display: flex;
        /* ต้องมีเพื่อใช้ flex-direction */
        flex-direction: column;
        /* จัดเรียงแนวตั้ง */
        padding: 1rem;
        /* ช่องว่างภายในเท่ากับ p-3 20
        position: relative;
        /* สำหรับการจัดวางอื่นๆ */
    }

    .container_static_today {
        background-color: #fff;
        height: 320px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .box-large {
        background-color: #fff;
        min-height: 840px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }



    :root {
        --primary-color: #77B2C9;
        --secondary-color: #D6E7EE;
        --accent-color: #f3e8ff;
        --text-color: #222429;
        --gray-color: rgb(232, 232, 232);
        --white-color: #FFFFFF;
        --snow-color: #f9f9f9;
    }

    .risk-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: var(--white-color);
        margin: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e0e0e0);
    }

    .modern-table thead th {
        background: var(--gray-color);
        color: var(--text-color);
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        text-align: center;
        border: none;
    }

    .modern-table tbody tr:nth-child(odd) {
        background: var(--white-color);
    }

    .modern-table tbody tr:nth-child(even) {
        background: var(--snow-color);
    }

    .modern-table tbody tr:hover {
        background-color: var(--hover-color, #f5f5f5);
    }

    .modern-table tbody td {
        padding: 30px 12px;
        vertical-align: middle;
        border: none;
        font-size: 13px;
        color: var(--text-color);
        text-align: center;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 0.85em;
        font-weight: 500;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
        color: #333;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .status-badge:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* 🔴 จุดสีสถานะ */
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* 🎨 สีของจุด */
    .status-waiting .status-dot,
    .status-not-treated .status-dot,
    .status-risk-warning .status-dot {
        background: #ffc107;
    }

    .status-scheduled .status-dot,
    .status-treated .status-dot,
    .status-risk-normal .status-dot {
        background: #28a745;
    }

    .status-missed .status-dot,
    .status-risk-critical .status-dot {
        background: #dc3545;
    }

    .status-treating .status-dot {
        background: #17a2b8;
    }

    /* 📱 Mobile */
    @media (max-width: 768px) {
        .status-badge {
            font-size: 11px;
            padding: 6px 10px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            margin-right: 6px;
        }
    }

    /* 📱 Responsive Design */
    @media (max-width: 768px) {
        .status-badge {
            font-size: 11px;
            padding: 6px 10px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            margin-right: 4px;
        }
    }

    /* 🎨 Additional States */
    .status-completed {
        border-color: #673ab7;
        background-color: #f3e5f5;
        color: #512da8;
    }

    .status-completed .status-dot {
        background-color: #673ab7;
    }

    .status-cancelled {
        border-color: #607d8b;
        background-color: #f5f5f5;
        color: #455a64;
    }

    .status-cancelled .status-dot {
        background-color: #607d8b;
    }

    .status-pending {
        border-color: #795548;
        background-color: #efebe9;
        color: #5d4037;
    }

    .status-pending .status-dot {
        background-color: #795548;
    }
</style>

<body class="hold-transition layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin-hospital.navbarhospital')
        <!-- Main Sidebar Container -->
        @include('themes.admin-hospital.menuhospital')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                            <h2 id="statusTitle" class="fw-bold mb-0">รายการยังไม่ได้ทำการนัดหมาย</h2>

                            <!-- Container สำหรับ dropdown และปุ่มกรอง -->





                            <div class="d-flex flex-wrap align-items-end justify-content-end gap-3 mt-3">
                                <!-- กล่อง dropdown -->
                                <div>


                                    <select id="statusFilter" class="form-select form-select-sm"
                                        style="min-width: 200px; border-radius: 8px;">
                                        <option value="sent" selected>🕐 ยังไม่ได้นัดหมาย</option>
                                        <option value="scheduled">✅ นัดหมายสำเร็จ</option>
                                        <option value="scheduledComplete">📅 รายการนัดหมายประจำวัน</option>
                                        <option value="todaymakeappointmenttoday">✍️ ประวัติการบันทึกนัดหมาย</option>
                                        <option value="missed">🚫 ไม่มาตามนัด</option>
                                        <option value="today-status">📈 ติดตามสถานะการรักษารายวัน</option>
                                    </select>
                                </div>

                                <!-- ปุ่มตัวกรอง -->
                                <div class="d-flex align-items-end">
                                    <button class="btn btn-info btn-sm px-3" id="openFilterModal"
                                        style="height: 32px; border-radius: 8px;">
                                        <i class="fas fa-filter me-1"></i> ตัวกรอง
                                    </button>
                                </div>

                                <!-- ปุ่มดาวน์โหลด PDF -->

                            </div>

                        </div>

                        <div class="row g-3">
                            <div class="col-9">
                                <div class="box-large border d-flex justify-content-center">

                                    <!-- กล่องสำหรับตาราง sentTable -->
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px;"
                                        id="sentTableContainer">
                                        <table class="modern-table data-table" id="sentTable">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>หน่วยฝึกต้นสังกัด</th>
                                                    <th>ผลัด</th>
                                                    <th>อาการ</th>
                                                    <th>สถานะ</th>
                                                    <th>นัดหมาย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicalReports as $report)
                                                    @if ($report->status !== 'sent')
                                                        @continue
                                                    @endif
                                                    <tr class="report-row" data-status="sent">
                                                        <td class="fw-bold">{{ $report->soldier->first_name }}
                                                            {{ $report->soldier->last_name }}
                                                        </td>
                                                        <td>{{ $report->soldier->trainingUnit->unit_name ?? 'ไม่ระบุ' }}
                                                        </td>
                                                        <td>{{ $report->soldier->affiliated_unit }}</td>
                                                        <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-info btn-sm btn-detail text-truncate w-100 mb-1"
                                                                style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                {{ $report->symptom_description ?? '-' }}
                                                            </button>
                                                            @if($report->vitalSign && $report->vitalSign->risk_level)
                                                                @if($report->vitalSign->risk_level === 'red')
                                                                    <strong><span style="color:red">(วิกฤติ)</span></strong>
                                                                @elseif($report->vitalSign->risk_level === 'yellow')
                                                                    <strong><span style="color:orange">(เฝ้าระวัง)</span></strong>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td><span class="status-badge status-not-treated sent"><span
                                                                    class="status-dot"></span>ยังไม่ได้ทำการนัดหมาย</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-success btn-sm btn-assign"
                                                                data-id="{{ $report->id }}"
                                                                data-name="{{ $report->soldier->first_name }} {{ $report->soldier->last_name }}">
                                                                นัดหมาย
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- กล่องแยกสำหรับตาราง scheduledTable -->
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px; display: none;"
                                        id="scheduledTableContainer">
                                        <table class="modern-table data-table" id="scheduledTable">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ</th>
                                                    <th>ข้อมูลทหาร</th>
                                                    <th>อาการ</th>
                                                    <th>ข้อมูลนัดหมาย</th>
                                                    <th>สถานะ</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicalReports as $report)
                                                    @if (!($report->appointment && $report->appointment->status === 'scheduled'))
                                                        @continue
                                                    @endif

                                                    <tr class="report-row" data-status="scheduled"
                                                        data-date="{{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d') }}"
                                                        data-case="{{ $report->appointment->case_type }}">

                                                        <td class="fw-bold">{{ $report->soldier->first_name }}
                                                            {{ $report->soldier->last_name }}
                                                        </td>
                                                        <td class="text-start">
                                                            <div class="text-start">
                                                                <div class="mb-2 ">
                                                                    <strong>หน่วยต้นสังกัด:</strong>
                                                                    {{ $report->soldier->affiliated_unit ?? '-' }}
                                                                </div>
                                                                <div class="mt-2">
                                                                    <strong>หน่วยฝึก:</strong>
                                                                    {{ $report->soldier->trainingUnit->unit_name ?? '-' }}
                                                                </div>

                                                                <div class="mt-2">
                                                                    <strong>ผลัด:</strong>
                                                                    {{ $report->soldier->rotation->rotation_name ?? '-' }}
                                                                </div>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                {{ $report->symptom_description ?? '-' }}
                                                            </button>



                                                        </td>
                                                        <td>
                                                            <strong>วัน:</strong>
                                                            {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') }}<br>
                                                            <strong>เวลา:</strong>
                                                            {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('H:i') }}<br>
                                                            <strong>สถานที่:</strong>
                                                            {{ $report->appointment->appointment_location }}<br>
                                                            <strong>ประเภทเคส:</strong>
                                                            {{ $report->appointment->case_type === 'normal' ? 'ปกติ' : ($report->appointment->case_type === 'critical' ? 'วิกฤติ' : 'ไม่ระบุ') }}
                                                            <br>

                                                            <!-- เพิ่มหมายเหตุ -->
                                                            <strong>หมายเหตุ:</strong>
                                                            @if($report->appointment->was_missed && $report->appointment->missed_appointment_date && $report->appointment->is_follow_up == 1)
                                                                เคยไม่มาตามนัด, นัดติดตามอาการ
                                                            @elseif($report->appointment->was_missed && $report->appointment->missed_appointment_date)
                                                                เคยไม่มาตามนัด
                                                            @elseif($report->appointment->is_follow_up == 1)
                                                                นัดติดตามอาการ
                                                            @else
                                                                -
                                                            @endif


                                                        </td>

                                                        <td>
                                                            <span class="status-badge status-scheduled">
                                                                <span class="status-dot"></span>
                                                                นัดหมายสำเร็จ
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm mt-2 btn-edit-appointment"
                                                                data-id="{{ $report->appointment->id }}">
                                                                <i class="fas fa-edit me-1"></i> แก้ไข
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px; display: none;"
                                        id="scheduledCompleteTableContainer">
                                        <table class="modern-table data-table" id="scheduledCompleteTable">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>หน่วยฝึกต้นสังกัด</th>
                                                    <th>ผลัด</th>
                                                    <th>อาการ</th>
                                                    <th>ข้อมูลนัดหมาย</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicalReports as $report)
                                                    @php
                                                        // ตรวจสอบว่ามีนัดหมาย และเป็นสถานะ scheduled หรือ completed
                                                        $hasAppointment = $report->appointment &&
                                                            in_array($report->appointment->status, ['scheduled', 'completed', 'missed']);

                                                        $showRecord = false;
                                                        if ($hasAppointment) {
                                                            $appointmentDate = \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d');
                                                            $hasMissed = $report->appointment->was_missed && $report->appointment->missed_appointment_date;
                                                            $missedDate = $hasMissed ? \Carbon\Carbon::parse($report->appointment->missed_appointment_date)->format('Y-m-d') : null;

                                                            // ✅ ตรวจสอบการแสดงผลตาม date parameter
                                                            if (request()->filled('date')) {
                                                                $targetDate = request()->get('date');
                                                                // แสดงถ้าตรงกับวันนัดหมายหรือวันขาดนัด
                                                                $showRecord = ($appointmentDate === $targetDate) ||
                                                                    ($hasMissed && $missedDate === $targetDate);
                                                            } else {
                                                                // ไม่มีการกรองวันที่ - แสดงเฉพาะวันนี้
                                                                $today = \Carbon\Carbon::today()->format('Y-m-d');
                                                                $showRecord = ($appointmentDate === $today) ||
                                                                    ($hasMissed && $missedDate === $today);
                                                            }
                                                        }
                                                    @endphp

                                                    @if (!$showRecord)
                                                        @continue
                                                    @endif

                                                    <tr class="report-row" data-status="{{ $report->appointment->status }}"
                                                        data-date="{{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d') }}"
                                                        data-case="{{ $report->appointment->case_type }}">

                                                        <td class="fw-bold">{{ $report->soldier->first_name }}
                                                            {{ $report->soldier->last_name }}
                                                        </td>
                                                        <td>{{ $report->soldier->trainingUnit->unit_name ?? 'ไม่ระบุ' }}
                                                        </td>
                                                        <td>{{ $report->soldier->affiliated_unit }}</td>
                                                        <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                                        <td>
                                                            <button
                                                                class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                {{ $report->symptom_description ?? '-' }}
                                                            </button>
                                                        </td>
                                                        <td>
                                                            @php
                                                                // ✅ ใช้ logic เดียวกันกับ today-status แต่ไม่เปลี่ยนสถานะ
                                                                $viewingDate = request()->get('date') ?: \Carbon\Carbon::today()->format('Y-m-d');
                                                                $appointmentDate = \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d');
                                                                $hasMissed = $report->appointment->was_missed && $report->appointment->missed_appointment_date;
                                                                $missedDate = $hasMissed ? \Carbon\Carbon::parse($report->appointment->missed_appointment_date)->format('Y-m-d') : null;

                                                                $isViewingMissedDate = $hasMissed && ($viewingDate === $missedDate);
                                                                $isViewingAppointmentDate = ($viewingDate === $appointmentDate);

                                                                // ✅ เช็คว่าวันนัดกับวันขาดนัดเป็นวันเดียวกันหรือไม่
                                                                $isSameDate = false;
                                                                if ($report->appointment->appointment_date && $report->appointment->missed_appointment_date) {
                                                                    $appointmentDateOnly = \Carbon\Carbon::parse($report->appointment->appointment_date)->format('Y-m-d');
                                                                    $missedDateOnly = \Carbon\Carbon::parse($report->appointment->missed_appointment_date)->format('Y-m-d');
                                                                    $isSameDate = ($appointmentDateOnly === $missedDateOnly);
                                                                }

                                                                // ✅ กำหนด displayMode ตามเงื่อนไข
                                                                if ($report->appointment->status === 'missed' && $isSameDate) {
                                                                    // missed และเป็นวันเดียวกัน → แสดงแค่ "ไม่มาตามนัด"
                                                                    $displayDate = $report->appointment->appointment_date;
                                                                    $displayMode = 'missed_same_day';
                                                                } elseif ($isViewingMissedDate) {
                                                                    // วันที่ขาดนัด (วันต่างกัน) → แสดง "นัดใหม่"
                                                                    $displayDate = $report->appointment->missed_appointment_date;
                                                                    $displayMode = 'missed_day';
                                                                } elseif ($isViewingAppointmentDate) {
                                                                    // วันนัดหมายปกติ
                                                                    $displayDate = $report->appointment->appointment_date;
                                                                    $displayMode = 'appointment_day';
                                                                } else {
                                                                    // วันอื่นๆ
                                                                    $displayDate = $report->appointment->appointment_date;
                                                                    $displayMode = 'other_day';
                                                                }
                                                            @endphp

                                                            <strong>วัน:</strong>
                                                            {{ \Carbon\Carbon::parse($displayDate)->format('d/m/Y') }}
                                                            @if($displayMode === 'missed_day')
                                                                <small class="text-danger fw-bold"> (ไม่มาตามนัด)</small>
                                                            @elseif($displayMode === 'appointment_day' && $hasMissed)
                                                                <small class="text-success fw-bold"> (นัดใหม่)</small>
                                                            @endif
                                                            <br>

                                                            <strong>เวลา:</strong>
                                                            {{ \Carbon\Carbon::parse($displayDate)->format('H:i') }}
                                                            @if($displayMode === 'missed_day' && $hasMissed)
                                                                <small class="text-success fw-bold"> → นัดใหม่:
                                                                    {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y H:i') }}</small>
                                                            @endif
                                                            <br>

                                                            <strong>สถานที่:</strong>
                                                            {{ $report->appointment->appointment_location }}<br>
                                                            <strong>ประเภทเคส:</strong>
                                                            {{ $report->appointment->case_type === 'normal' ? 'ปกติ' : ($report->appointment->case_type === 'critical' ? 'วิกฤติ' : 'ไม่ระบุ') }}
                                                            <br>

                                                            <strong>หมายเหตุ:</strong>
                                                            @if($displayMode === 'missed_day')
                                                                -
                                                            @elseif($displayMode === 'appointment_day' && $hasMissed)
                                                                เคยไม่มาตามนัด{{ $report->appointment->is_follow_up ? ', นัดติดตามอาการ' : '' }}
                                                            @else
                                                                {{ $report->appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                            @endif

                                                            <!-- แสดงข้อมูลวันที่ -->

                                                        </td>


                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- กล่องสำหรับตาราง missedTable -->
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px; display: none;"
                                        id="missedTableContainer">
                                        <table class="modern-table data-table" id="missedTable">
                                            <thead>

                                                <tr>
                                                    <th>ชื่อ</th>
                                                    <th>ข้อมูลทหาร</th>

                                                    <th>อาการ</th>
                                                    <th>ข้อมูลนัดหมาย</th>
                                                    <th>สถานะ</th>
                                                    <th>จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicalReports as $report)
                                                    @if (!($report->appointment && $report->appointment->status === 'missed'))
                                                        @continue
                                                    @endif
                                                    <tr class="report-row" data-status="missed">
                                                        <td class="fw-bold">
                                                            {{ $report->soldier->first_name }}
                                                            {{ $report->soldier->last_name }}
                                                        </td>
                                                        <td class="text-start">
                                                            <div class="text-start">
                                                                <div class="mb-2 ">
                                                                    <strong>หน่วยต้นสังกัด:</strong>
                                                                    {{ $report->soldier->affiliated_unit ?? '-' }}
                                                                </div>
                                                                <div class="mt-2">
                                                                    <strong>หน่วยฝึก:</strong>
                                                                    {{ $report->soldier->trainingUnit->unit_name ?? '-' }}
                                                                </div>

                                                                <div class="mt-2">
                                                                    <strong>ผลัด:</strong>
                                                                    {{ $report->soldier->rotation->rotation_name ?? '-' }}
                                                                </div>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                {{ $report->symptom_description ?? '-' }}
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <strong>วัน:</strong>
                                                            {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') }}<br>
                                                            <strong>เวลา:</strong>
                                                            {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('H:i') }}<br>
                                                            <strong>สถานที่:</strong>
                                                            {{ $report->appointment->appointment_location }}<br>
                                                            <strong>ประเภทเคส:</strong>
                                                            {{ $report->appointment->case_type === 'normal' ? 'ปกติ' : 'วิกฤติ' }}<br>
                                                            <strong>หมายเหตุ:</strong>
                                                            {{ $report->appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                        </td>
                                                        <td>
                                                            <span class="status-badge status-missed"><span
                                                                    class="status-dot"></span>ไม่มาตามนัด</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-warning btn-sm btn-edit-appointment"
                                                                data-id="{{ $report->appointment->id }}">
                                                                <i class="fas fa-edit me-1"></i> แก้ไข
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- กล่องสำหรับตาราง todayStatusTable -->
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px; display: none;"
                                        id="todayStatusTableContainer">
                                        <table class="modern-table data-table" id="todayStatusTable">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ</th>
                                                    <th>ข้อมูลทหาร</th>
                                                    <th>อาการ</th>
                                                    <th>รายละเอียดการนัดหมาย</th>
                                                    <th>สถานะวันนี้</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($medicalReports as $report)
                                                    @php
                                                        $appointment = $report->appointment;

                                                        if (!$appointment) {
                                                            $todayStatus = 'ไม่มีข้อมูลนัดหมาย';
                                                        } else {
                                                            $checkin = $appointment->checkin ?? null;
                                                            $treatment = $checkin->treatment ?? null;

                                                            // ✅ รับวันที่ที่กำลังดูจาก URL หรือใช้วันนี้
                                                            $viewingDate = request()->get('date') ?
                                                                \Carbon\Carbon::parse(request()->get('date'))->format('Y-m-d') :
                                                                \Carbon\Carbon::today()->format('Y-m-d');

                                                            $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                                                            $hasMissed = $appointment->was_missed && $appointment->missed_appointment_date;
                                                            $missedDate = $hasMissed ? \Carbon\Carbon::parse($appointment->missed_appointment_date)->format('Y-m-d') : null;

                                                            // ✅ ประกาศตัวแปรที่ขาดหายไป
                                                            $isViewingMissedDate = $hasMissed && ($viewingDate === $missedDate);
                                                            $isViewingAppointmentDate = ($viewingDate === $appointmentDate);

                                                            // ✅ เช็คว่าวันนัดกับวันขาดนัดเป็นวันเดียวกันหรือไม่
                                                            $isSameDate = false;
                                                            if ($appointment->appointment_date && $appointment->missed_appointment_date) {
                                                                $appointmentDateOnly = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                                                                $missedDateOnly = \Carbon\Carbon::parse($appointment->missed_appointment_date)->format('Y-m-d');
                                                                $isSameDate = ($appointmentDateOnly === $missedDateOnly);
                                                            }

                                                            if ($appointment->status === 'missed' && $isSameDate && $appointment->was_missed && $appointment->missed_appointment_date) {
                                                                // กรณี missed และเป็นวันเดียวกัน และมีข้อมูล missed จริง
                                                                $todayStatus = '<span class="status-badge status-missed"><span class="status-dot"></span>ไม่มาตามนัด</span>';

                                                            } elseif ($isViewingMissedDate && !$isSameDate && $appointment->was_missed && $appointment->missed_appointment_date) {
                                                                // วันที่ขาดนัด และไม่เป็นวันเดียวกับวันนัด และมีข้อมูล missed จริง
                                                                $todayStatus = '<span class="status-badge status-missed"><span class="status-dot"></span>ไม่มาตามนัด<br>นัดหมายใหม่แล้ว</span>';

                                                            } elseif ($isViewingAppointmentDate) {
                                                                // วันนัดหมาย → สถานะตามการรักษาจริง
                                                                if ($appointment->status === 'completed' && optional($checkin)->checkin_status === 'checked-in' && optional($treatment)->treatment_status === 'treated') {
                                                                    $todayStatus = '<span class="status-badge status-treated"><span class="status-dot"></span>รักษาสำเร็จ</span>';
                                                                } elseif ($appointment->status === 'scheduled' && optional($checkin)->checkin_status === 'checked-in' && optional($treatment)->treatment_status === 'not-treated') {
                                                                    $todayStatus = '<span class="status-badge status-treating"><span class="status-dot"></span>อยู่ระหว่างการรักษา</span>';
                                                                } elseif ($appointment->status === 'scheduled' && optional($checkin)->checkin_status === 'not-checked-in') {
                                                                    $todayStatus = '<span class="status-badge status-not-treated"><span class="status-dot"></span>ยังไม่ได้ทำการรักษา</span>';
                                                                } elseif ($appointment->status === 'missed' && $appointment->was_missed && $appointment->missed_appointment_date) {
                                                                    $todayStatus = '<span class="status-badge status-missed"><span class="status-dot"></span>ไม่มาตามนัด</span>';
                                                                } else {
                                                                    // ค่าเริ่มต้นสำหรับวันนัดหมายใหม่
                                                                    $todayStatus = '<span class="status-badge status-treated"><span class="status-dot"></span>รักษาสำเร็จ</span>';
                                                                }

                                                            } else {
                                                                // วันอื่นๆ → สถานะปกติ
                                                                if ($appointment->status === 'completed' && optional($checkin)->checkin_status === 'checked-in' && optional($treatment)->treatment_status === 'treated') {
                                                                    $todayStatus = '<span class="status-badge status-scheduled"><span class="status-dot"></span>นัดหมายสำเร็จ</span>';
                                                                } elseif ($appointment->status === 'scheduled' && optional($checkin)->checkin_status === 'checked-in' && optional($treatment)->treatment_status === 'not-treated') {
                                                                    $todayStatus = '<span class="status-badge status-treating"><span class="status-dot"></span>อยู่ระหว่างการรักษา</span>';
                                                                } elseif ($appointment->status === 'scheduled' && optional($checkin)->checkin_status === 'not-checked-in') {
                                                                    $todayStatus = '<span class="status-badge status-not-treated"><span class="status-dot"></span>ยังไม่ได้ทำการรักษา</span>';
                                                                } elseif ($appointment->status === 'missed' && $appointment->was_missed && $appointment->missed_appointment_date) {
                                                                    $todayStatus = '<span class="status-badge status-missed"><span class="status-dot"></span>ไม่มาตามนัด</span>';
                                                                } else {
                                                                    $todayStatus = 'ไม่ทราบสถานะ';
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <!-- ส่วนแสดงผลในตาราง -->
                                                    <tr>
                                                        <td>{{ $report->soldier->first_name }}
                                                            {{ $report->soldier->last_name }}
                                                        </td>
                                                        <td class="text-start">
                                                            <div class="text-start">
                                                                <div class="mb-2 ">
                                                                    <strong>หน่วยต้นสังกัด:</strong>
                                                                    {{ $report->soldier->affiliated_unit ?? '-' }}
                                                                </div>
                                                                <div class="mt-2">
                                                                    <strong>หน่วยฝึก:</strong>
                                                                    {{ $report->soldier->trainingUnit->unit_name ?? '-' }}
                                                                </div>

                                                                <div class="mt-2">
                                                                    <strong>ผลัด:</strong>
                                                                    {{ $report->soldier->rotation->rotation_name ?? '-' }}
                                                                </div>

                                                            </div>
                                                        </td>


                                                        <td>
                                                            <button
                                                                class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                {{ $report->symptom_description ?? '-' }}
                                                            </button>
                                                        </td>
                                                        <td>
                                                            @if ($appointment)
                                                                @php
                                                                    // ✅ รับวันที่ที่กำลังดูจาก URL หรือใช้วันนี้
                                                                    $viewingDate = request()->get('date') ?
                                                                        \Carbon\Carbon::parse(request()->get('date'))->format('Y-m-d') :
                                                                        \Carbon\Carbon::today()->format('Y-m-d');

                                                                    $appointmentDate = \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d');
                                                                    $hasMissed = $appointment->was_missed && $appointment->missed_appointment_date;
                                                                    $missedDate = $hasMissed ? \Carbon\Carbon::parse($appointment->missed_appointment_date)->format('Y-m-d') : null;

                                                                    // ✅ ตรวจสอบว่าวันที่กำลังดูเป็นวันไหน
                                                                    $isViewingMissedDate = $hasMissed && ($viewingDate === $missedDate);
                                                                    $isViewingAppointmentDate = ($viewingDate === $appointmentDate);

                                                                    // ✅ กำหนดข้อมูลที่จะแสดงตามวันที่ที่กำลังดู
                                                                    if ($isViewingMissedDate) {
                                                                        // กำลังดูวันที่ขาดนัด → แสดงข้อมูลวันที่ขาดนัด
                                                                        $displayDate = $appointment->missed_appointment_date;
                                                                        $displayMode = 'missed_day';
                                                                    } elseif ($isViewingAppointmentDate) {
                                                                        // กำลังดูวันนัดหมาย → แสดงข้อมูลวันนัดหมาย
                                                                        $displayDate = $appointment->appointment_date;
                                                                        $displayMode = 'appointment_day';
                                                                    } else {
                                                                        // วันอื่นๆ → แสดงข้อมูลปัจจุบัน
                                                                        $displayDate = $appointment->appointment_date;
                                                                        $displayMode = 'other_day';
                                                                    }
                                                                @endphp

                                                                {{-- ✅ แสดงข้อมูลตามวันที่ที่กำลังดู --}}
                                                                <strong>วัน:</strong>
                                                                {{ \Carbon\Carbon::parse($displayDate)->format('d/m/Y') }}
                                                                <br>

                                                                <strong>เวลา:</strong>
                                                                {{ \Carbon\Carbon::parse($displayDate)->format('H:i') }}
                                                                <br>

                                                                <strong>สถานที่:</strong>
                                                                {{ $appointment->appointment_location ?? '-' }}<br>
                                                                <strong>ประเภทเคส:</strong>
                                                                {{ $appointment->case_type === 'normal' ? 'ปกติ' : 'วิกฤติ' }}<br>
                                                                <strong>หมายเหตุ:</strong>
                                                                @if($displayMode === 'missed_day')
                                                                    - {{-- ไม่แสดงหมายเหตุสำหรับวันที่ขาดนัด --}}
                                                                @elseif($displayMode === 'appointment_day' && $hasMissed)
                                                                    {{-- วันนัดจริงและเคยขาดนัด --}}
                                                                    เคยไม่มาตามนัด{{ $appointment->is_follow_up ? ', นัดติดตามอาการ' : '' }}
                                                                @elseif($displayMode === 'appointment_day' && !$hasMissed)
                                                                    {{-- วันนัดจริงแต่ไม่เคยขาดนัด --}}
                                                                    {{ $appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                                @else
                                                                    {{-- กรณีอื่นๆ (วันปกติ) --}}
                                                                    {{ $appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                                @endif

                                                                {{-- แสดงข้อมูลการกรองวันที่ --}}
                                                            @else
                                                                <span class="text-muted">ไม่มีข้อมูลนัดหมาย</span>
                                                            @endif
                                                        </td>
                                                        <td>{!! $todayStatus !!}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-5 mb-3" style="width: 90%; max-width: 900px; display: none;"
                                        id="todaymakeappointmenttodayTableContainer">

                                        <!-- ✅ ย้ายมาตรงนี้ -->

                                        <table class="modern-table data-table" id="todaymakeappointmenttodayTable">
                                            <thead>
                                                <tr>
                                                    <th>ชื่อ - นามสกุล</th>
                                                    <th>เลขประจำตัว</th>
                                                    <th>ผลัด</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>ระดับเสี่ยง</th>
                                                    <th>สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- เพิ่มใน HTML -->

                                </div>
                            </div>


                            <div class="col-3">
                                <div class="container_static_today p-3 border mb-3">
                                    <div id="customLegendRight" class="custom-legend-right"></div>

                                </div>
                                <div class="container_waitappiontment p-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 fw-bold" style="font-size: 1.2rem;">รายการรอนัดหมาย</h6>
                                        </div>
                                        <span id="patientCount" class="badge rounded-pill px-3 py-2"
                                            style="background-color: #77B2C9; color: white; box-shadow: 2px 2px 5px rgba(0,0,0,0.2);">0
                                            ราย</span>

                                    </div>
                                    <div id="patientList">
                                        <div class="card mb-3" id="patientTemplate" style="display: none;">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start ">
                                                    <div class="fw-bold patient-name"></div>
                                                    <span class="btn btn-light btn-sm patient-risk"></span>
                                                </div>
                                                <div class="text-muted small patient-symptom"></div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-muted patient-date"
                                                        style="font-size: 11px;"></span>

                                                </div>
                                            </div>
                                        </div>
                                        <div id="noData" class="text-center justify-content-center text-muted"
                                            style="display: none;">
                                            ไม่มีรายงานรอนัดหมาย</div>
                                    </div>
                                    <div class="mt-auto mb-2"> <!-- mt-auto เพื่อดันปุ่มลงล่าง + mb-2 สำหรับระยะห่าง -->
                                        <button class="btn text-white px-3 py-2 w-100 fs-7" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                                            border: none;
                                            border-radius: 8px;
                                            font-size: 0.8rem;">
                                            ดูทั้งหมด
                                        </button>
                                    </div>
                                </div>



                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            นัดหมายทหาร
                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="container">
                            <h3><strong>พลฯ</strong> <span id="soldierName"></span></h3>
                            <p><strong>หน่วยต้นสังกัด:</strong> <span id="soldierUnit"></span> |
                                <strong>ผลัด:</strong> <span id="soldierRotation"></span> |
                                <strong>หน่วยฝึก:</strong> <span id="soldierTraining"></span>
                            </p>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อุณหภูมิ</small>
                                        <h5 id="soldierTemp">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ความดันโลหิต</small>
                                        <h5 id="soldierBP">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อัตราการเต้นของหัวใจ</small>
                                        <h5 id="soldierHeartRate">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ระดับความเจ็บปวด</small>
                                        <h5 id="soldierPain">-</h5>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">อาการ</h5>
                            <p id="soldierSymptom"></p>
                            <h5 class="mt-4">ระดับความเสี่ยง</h5>
                            <p id="soldierRiskLevel"></p>
                            <h5 class="mt-4">ผลตรวจ ATK</h5>
                            <div id="atkImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <h5 class="mt-4">รูปอาการ</h5>
                            <div id="symptomImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <!-- Add risk level display -->


                        </div>
                    </div>




                    <div class="modal-body">
                        <form id="appointmentForm">
                            <input type="hidden" id="singleReportId" name="medical_report_ids[]">

                            <div class="mb-3"> <!-- ✅ เพิ่มช่องว่างให้ input -->
                                <label for="appointment_date" class="form-label fw-bold">
                                    วันและเวลา
                                </label>
                                <input type="datetime-local" id="appointment_date" name="appointment_date"
                                    class="form-control" required>
                            </div>

                            <!-- ✅ แบ่งเป็น Row แยกเพื่อเพิ่มระยะห่าง -->
                            <div class="row g-3 ">
                                <!-- Dropdown สถานที่ -->
                                <div class="col-md-6">
                                    <label for="appointment_location" class="form-label fw-bold">
                                        สถานที่
                                    </label>
                                    <select id="appointment_location" name="appointment_location" class="form-select">
                                        <option value="OPD">OPD</option>
                                        <option value="ER">ER</option>
                                        <option value="IPD">IPD</option>
                                        <option value="ARI clinic">ARI Clinic</option>
                                        <option value="กองทันตกรรม">กองทันตกรรม</option>
                                    </select>
                                </div>

                                <!-- Dropdown ประเภทผู้ป่วย -->
                                <div class="col-md-6">
                                    <label for="case_type" class="form-label fw-bold">
                                        ประเภทผู้ป่วย
                                    </label>
                                    <select id="case_type" name="case_type" class="form-select">
                                        <option value="normal">ปกติ</option>
                                        <option value="critical">วิกฤติ</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ✅ ปุ่มยืนยันการนัดหมาย อยู่ตรงกลาง -->
                            <div class="d-flex justify-content-center mt-4">
                                <button type="button" id="confirmAppointment" class="btn btn-success px-4 py-2">
                                    ยืนยันการนัดหมาย
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>



        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            รายละเอียดผู้ป่วย
                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="container">
                            <h3><strong>พลฯ</strong> <span id="soldierName"></span></h3>
                            <p><strong>หน่วยต้นสังกัด:</strong> <span id="soldierUnit"></span> |
                                <strong>ผลัด:</strong> <span id="soldierRotation"></span> |
                                <strong>หน่วยฝึก:</strong> <span id="soldierTraining"></span>
                            </p>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อุณหภูมิ</small>
                                        <h5 id="soldierTemp">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ความดันโลหิต</small>
                                        <h5 id="soldierBP">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อัตราการเต้นของหัวใจ</small>
                                        <h5 id="soldierHeartRate">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ระดับความเจ็บปวด</small>
                                        <h5 id="soldierPain">-</h5>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">อาการ</h5>
                            <p id="soldierSymptom"></p>
                            <h5 class="mt-4">ระดับความเสี่ยง</h5>
                            <p id="soldierRiskLevel"></p>
                            <h5 class="mt-4">ผลตรวจ ATK</h5>
                            <div id="atkImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <h5 class="mt-4">รูปอาการ</h5>
                            <div id="symptomImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <!-- Add risk level display -->


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content shadow">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title fw-bold">ตัวกรองข้อมูล</h5>
                    </div>
                    <div class="modal-body">
                        <form id="filterForm">
                            <div class="mb-3 d-none" id="dateFilterGroup">
                                <label for="dateFilterModal" class="form-label">วันที่</label>
                                <input type="date" class="form-control" id="dateFilterModal">
                            </div>
                            <div class="mb-3 d-none" id="caseTypeFilterGroup">
                                <label for="caseTypeFilterModal" class="form-label">ประเภทผู้ป่วย</label>
                                <select class="form-select" id="caseTypeFilterModal">
                                    <option value="all">ทุกประเภท</option>
                                    <option value="normal">ปกติ</option>
                                    <option value="critical">วิกฤติ</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="rotationFilterModal" class="form-label">ผลัด</label>
                                <select class="form-select" id="rotationFilterModal">
                                    <option value="">ทุกผลัด</option>
                                    @foreach($rotations as $rotation)
                                        <option value="{{ $rotation->id }}">{{ $rotation->rotation_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="trainingUnitFilterModal" class="form-label">หน่วยฝึก</label>
                                <select class="form-select" id="trainingUnitFilterModal">
                                    <option value="">ทุกหน่วยฝึก</option>
                                    @foreach($trainingUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="todayStatusGroup">
                                <label for="todayStatusFilterModal" class="form-label">สถานะวันนี้</label>
                                <select class="form-select" id="todayStatusFilterModal">
                                    <option value="all">ทุกสถานะ</option>
                                    <option value="ยังไม่ได้ทำการรักษา">ยังไม่ได้ทำการรักษา</option>
                                    <option value="อยู่ระหว่างการรักษา">อยู่ระหว่างการรักษา</option>
                                    <option value="รักษาสำเร็จ">รักษาสำเร็จ</option>
                                    <option value="ไม่มาตามนัด">ไม่มาตามนัด</option>
                                </select>
                            </div>

                        </form>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-success" id="applyFilter">ยืนยัน</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Modal สำหรับแก้ไขนัดหมาย -->
        <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content shadow">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title fw-bold">
                            แก้ไขข้อมูลนัดหมาย
                        </h5>
                    </div>

                    <div class="modal-body">
                        <form id="editAppointmentForm">
                            <!-- เก็บ appointment_id -->
                            <input type="hidden" id="editAppointmentId" name="edit_appointment_id">

                            <!-- วันและเวลา -->
                            <div class="mb-3">
                                <label for="edit_appointment_date" class="form-label fw-bold">วันและเวลา</label>
                                <input type="datetime-local" id="edit_appointment_date" name="appointment_date"
                                    class="form-control" required>
                            </div>

                            <!-- สถานที่ -->
                            <div class="mb-3">
                                <label for="edit_appointment_location" class="form-label fw-bold">สถานที่</label>
                                <select id="edit_appointment_location" name="appointment_location" class="form-select">
                                    <option value="OPD">OPD</option>
                                    <option value="ER">ER</option>
                                    <option value="IPD">IPD</option>
                                    <option value="ARI clinic">ARI Clinic</option>
                                    <option value="กองทันตกรรม">กองทันตกรรม</option>
                                </select>
                            </div>

                            <!-- ประเภทเคส -->
                            <div class="mb-3">
                                <label for="edit_case_type" class="form-label fw-bold">ประเภทผู้ป่วย</label>
                                <select id="edit_case_type" name="case_type" class="form-select">
                                    <option value="normal">ปกติ</option>
                                    <option value="critical">วิกฤติ</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" id="submitEditAppointment" class="btn btn-warning">
                                    บันทึกการแก้ไข
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



</body>

</html>
<style>
    .info-box {
        background-color: #fff;
        border: 2px solid #dee2e6;
        padding: 15px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        /* ✅ จัดเรียงเนื้อหาเป็นแนวตั้ง */
        align-items: center;
        justify-content: center;
        min-height: 100px;
        transition: 0.3s;
    }

    .info-box small {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }

    .info-box h5 {
        font-size: 22px;
        font-weight: 700;
        margin-top: 8px;
        /* ✅ เว้นระยะห่างจากข้อความด้านบน */
    }

    .info-box:hover {
        background-color: #f8f9fa;
    }

    .image-wrapper {
        width: 70%;
        aspect-ratio: 1/1;
        /* ทำให้รูปเป็นสี่เหลี่ยมจัตุรัส */
        overflow: hidden;
        border-radius: 8px;
        /* มุมโค้งมน */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        /* สีพื้นหลัง */
        margin-bottom: 5px;
        /* ลดระยะห่างระหว่างรูป */
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* ป้องกันรูปบีบผิดสัดส่วน */
        border-radius: 8px;
        padding: 2px;
        /* ลด padding เพื่อให้รูปดูไม่ห่างกันมาก */
    }

    /* ✅ ป้ายสถานะ */
    .status-label {
        display: inline-flex;
        align-items: center;
        font-weight: bold;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 12px;
        border: 1px solid #ddd;
        background-color: white;
        /* เปลี่ยนจากสีเหลืองเป็นขาว */
        color: black;
        box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* ✅ จุดสีหน้าข้อความ */
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
        display: inline-block;
    }

    /* ✅ จุดสีแดง */
    .dot-red {
        background-color: red;
    }

    /* ✅ จุดสีเหลือง */
    .dot-yellow {
        background-color: #FFC107;
    }

    /* ✅ ปรับขนาดปุ่มให้ดูดีขึ้น */
    #confirmAppointment {
        font-size: 16px;
        border-radius: 8px;
    }

    /* ✅ ปรับ input ให้ดูสวยงาม */
    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 5px;
    }

    /* ✅ ปรับขนาด Modal ให้สมส่วนขึ้น */
    .modal-md {
        max-width: 500px;
    }

    /* ✅ เพิ่มช่องว่างระหว่าง Input และ Dropdown */
    #appointment_date {
        margin-bottom: 15px;
    }

    /* Update the select box to make it appear with rounded corners and with a consistent layout */
    /* Container for both dropdowns to be in the same row */
    .row.g-3.align-items-center {
        display: flex;
        gap: 20px;
        /* Adjust the space between dropdowns */
        justify-content: space-between;
        /* This ensures they are spaced evenly */
    }

    /* Ensure both dropdowns take equal width */
    .form-select {
        width: 100%;
        padding: 10px;
        /* Make sure there's enough padding for each dropdown */
        border-radius: 10px;
        border: 1px solid #ccc;
        /* Border to make it consistent */
    }

    /* Optional: Add some space for better readability and focus */
    #appointment_date {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
    }

    /* เพิ่มสไตล์สำหรับสถานะ Scheduled */
    .dot-green {
        background-color: #28a745;
    }

    .status-label.scheduled {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    /* ซ่อนปุ่มนัดหมายเมื่ออยู่ในโหมด Scheduled */
    #scheduleAppointment {
        display: block;
    }

    /* กำหนดสไตล์ให้กับ #soldierRiskLevel */
    #soldierRiskLevel {
        display: inline-block;
        /* ทำให้ p แสดงเป็นบล็อกในบรรทัดเดียว */
        padding: 8px 16px;
        /* เพิ่มพื้นที่ภายในรอบๆ ข้อความ */
        border: 2px solid #ccc;
        /* กรอบสีเทา */
        border-radius: 12px;
        /* มุมโค้งมน */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* เงาอ่อนๆ */
        font-weight: bold;
        /* ทำให้ข้อความหนา */
        font-size: 14px;
        /* ขนาดข้อความ */
        color: #333;
        /* สีข้อความ */
        background-color: #f9f9f9;
        /* สีพื้นหลัง */
    }

    /* สไตล์เพิ่มเติม */
    .filter-item {
        min-width: 150px;
    }

    .form-select-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        height: calc(1.5em + 0.5rem + 2px);
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .input-group-text {
        padding: 0.35rem 0.5rem;
    }

    /* ระยะห่างระหว่างองค์ประกอบ */
    .gap-3>* {
        margin-right: 0.75rem;
    }

    .gap-3>*:last-child {
        margin-right: 0;
    }


    /* ลบ !important ออก */
</style>

@include('themes.script')
<!-- ใน HTML ตรวจสอบว่ามี script tag นี้มั้ย -->
<script src="{{ asset('js/daily-treatment.js') }}"></script>
<script src="{{ asset('js/makeappointmenttotoday.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ฟังก์ชันสำหรับโหลดข้อมูลใหม่โดยใช้พารามิเตอร์จาก URL
    function reloadAppointmentData() {
        const urlParams = new URLSearchParams(window.location.search);
        const params = {
            date: urlParams.get('date'),
            case_type: urlParams.get('case_type'),
            rotation_id: urlParams.get('rotation_id'),
            training_unit_id: urlParams.get('training_unit_id')
        };

        // สร้าง URL สำหรับเรียก API
        let apiUrl = '/medical-reports/soldier-info?status=approved';
        Object.entries(params).forEach(([key, value]) => {
            if (value) apiUrl += `&${key}=${value}`;
        });

        // เรียกฟังก์ชัน loadData ในไฟล์ JS ด้วย URL ใหม่
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                // เรียกฟังก์ชัน fillTable ที่มีอยู่ในไฟล์ JS
                fillTable(data.data, data.summary);
            })
            .catch(error => {
                console.error('Error:', error);
                showError();
            });
    }

    // เมื่อเปลี่ยนตัวกรอง ให้โหลดข้อมูลใหม่
    document.getElementById('applyFilter').addEventListener('click', function () {
        reloadAppointmentData();
    });

    // โหลดข้อมูลครั้งแรกเมื่อหน้าเว็บโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.search.includes('status=todaymakeappointmenttoday')) {
            reloadAppointmentData();
        }
    });
</script>

<script>
    // แทนที่จะเรียก loadOnlyLegend() ให้เรียกตรงๆ
    document.addEventListener('DOMContentLoaded', function () {
        // ใช้ข้อมูล demo หรือ API
        loadApiData().then(data => {
            createCustomLegend(data, 'customLegendRight');
        });
    });
</script>

<script>
    $(document).ready(function () {
        loadPatientData();
    });

    function loadPatientData() {
        $.ajax({
            url: '/medical-reports/soldier-info',
            type: 'GET',
            data: { status: 'sent' }, // เอาแค่สถานะ sent
            success: function (response) {
                if (response.success) {
                    displayPatients(response.data);
                    updateTitle(response.summary.sent_count);
                }
            },
            error: function () {
                $('#patientList').html('<div class="text-center text-muted">ไม่สามารถโหลดข้อมูลได้</div>');
            }
        });
    }

    function displayPatients(patients) {
        const template = $('#patientTemplate');
        const container = $('#patientList');
        const noData = $('#noData');

        // ลบการ์ดเก่าทั้งหมด (ยกเว้น template)
        container.find('.card:not(#patientTemplate)').remove();

        if (patients.length === 0) {
            noData.show();
            return;
        }

        noData.hide();
        const sortedPatients = patients.sort((a, b) => {
            const riskOrder = { 'red': 1, 'yellow': 2, 'green': 3 };
            const aOrder = riskOrder[a.risk_level] || 4;
            const bOrder = riskOrder[b.risk_level] || 4;
            return aOrder - bOrder;
        });

        // แสดงแค่ 5 รายการแรก
        const limitedPatients = sortedPatients.slice(0, 5);

        limitedPatients.forEach(function (patient) {
            // Clone template
            const newCard = template.clone();
            newCard.removeAttr('id').show();

            let dotColor = '#6c757d';
            let riskText = 'ไม่ระบุ';

            if (patient.risk_level === 'red') {
                dotColor = '#dc3545';
                riskText = 'เสี่ยงสูง';
            } else if (patient.risk_level === 'yellow') {
                dotColor = '#ffc107';
                riskText = 'เฝ้าระวัง';
            } else if (patient.risk_level === 'green') {
                dotColor = '#198754';
                riskText = 'ปกติ';
            }

            // แปลงวันที่
            let reportDate = new Date(patient.report_date).toLocaleDateString('th-TH', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            // เติมข้อมูลลงใน template
            newCard.find('.patient-name').text(`${patient.first_name} ${patient.last_name}`);
            newCard.find('.patient-id').text(patient.soldier_id_card);
            newCard.find('.patient-risk')
                .removeClass()
                .addClass('btn btn-light btn-sm border shadow-sm')
                .css({
                    'background-color': '#f9f9f9',
                    'font-size': '11px'
                })
                .html(`<span class="risk-dot" style="background-color: ${dotColor};"></span>${riskText}`);
            newCard.find('.patient-symptom').text(`อาการ: ${patient.symptom_description || 'ไม่ระบุอาการ'}`);

            newCard.find('.patient-date').text(`ส่งเมื่อ: ${reportDate}`);
            newCard.find('.patient-appointment-btn').on('click', function () {
                makeAppointment(patient.medical_report_id);
            });

            // เพิ่มเข้าไปใน container
            container.append(newCard);
        });
    }

    function updateTitle(count) {
        $('#patientCount').text(`${count} ราย`);
    }

    function makeAppointment(reportId) {
        alert('เปิดหน้านัดหมายสำหรับ Report ID: ' + reportId);
        // ใส่โค้ดเปิด modal หรือไปหน้านัดหมายที่นี่
    }
</script>

<script>
    $(document).ready(function () {
        // ✅ เก็บค่าพารามิเตอร์จาก URL
        const getQueryParam = (param) => new URLSearchParams(window.location.search).get(param);
        const statusFromUrl = getQueryParam('status') || 'sent'; // ถ้าไม่มี status ให้ใช้ 'sent'

        // ✅ ตั้งค่า dropdown ให้ตรงกับ URL
        $('#statusFilter').val(statusFromUrl);

        // ✅ ถ้าไม่มี URL parameter เลย ให้ redirect ไปค่า default
        if (!window.location.search) {
            const params = new URLSearchParams();
            params.set('status', 'sent');
            window.history.replaceState({}, '', window.location.pathname + '?' + params.toString());
        }

        // อัพเดท UI
        updateStatusUI(statusFromUrl);

        // ✅ ฟังก์ชัน updateStatusUI ที่แก้ไขแล้ว
        function updateStatusUI(status) {
            // รับ parameter วันที่จาก URL
            const urlParams = new URLSearchParams(window.location.search);
            const selectedDate = urlParams.get('date');

            // แปลงวันที่เป็นรูปแบบไทย
            let dateText = '';
            if (selectedDate) {
                const date = new Date(selectedDate + 'T00:00:00');
                dateText = ` (${date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                })})`;
            }

            // ✅ แก้ไข titleMap ให้แสดงตามที่ต้องการ
            const titleMap = {
                "sent": "รายการยังไม่ได้ทำการนัดหมาย",
                "scheduled": selectedDate ? `รายการที่นัดหมายสำเร็จ${dateText}` : "รายการที่นัดหมายสำเร็จ (ทั้งหมด)",
                "missed": selectedDate ? `รายการที่ไม่มาตามนัด${dateText}` : "รายการที่ไม่มาตามนัด (ทั้งหมด)",
                "today-status": `ติดตามสถานะการรักษารายวัน${selectedDate ? dateText : 'วันนี้'}`,
                "todaymakeappointmenttoday": `ประวัติการบันทึกนัดหมาย${selectedDate ? dateText : 'วันนี้'}`,
                "scheduledComplete": `รายการนัดหมายประจำวัน${selectedDate ? dateText : 'วันนี้'}`
            };

            $("#statusTitle").text(titleMap[status] || "รายการยังไม่ได้ทำการนัดหมาย");

            // ซ่อนทุก container ก่อน
            $("#sentTableContainer").hide();
            $("#scheduledTableContainer").hide();
            $("#missedTableContainer").hide();
            $("#todayStatusTableContainer").hide();
            $("#todaymakeappointmenttodayTableContainer").hide();
            $("#scheduledCompleteTableContainer").hide();

            // แสดง container ที่ถูกต้อง
            if (status === 'sent') {
                $("#sentTableContainer").show();
            } else if (status === 'scheduled') {
                $("#scheduledTableContainer").show();
            } else if (status === 'missed') {
                $("#missedTableContainer").show();
            } else if (status === 'today-status') {
                $("#todayStatusTableContainer").show();
            } else if (status === 'todaymakeappointmenttoday') {
                $("#todaymakeappointmenttodayTableContainer").show();
            } else if (status === 'scheduledComplete') {
                $("#scheduledCompleteTableContainer").show();
            }
        }

        // ✅ เมื่อเปลี่ยนสถานะ -> reload หน้าใหม่ (แก้ไขแล้ว)
        $('#statusFilter').change(function () {
            const newStatus = $(this).val();
            const params = new URLSearchParams(); // ✅ สร้างใหม่ ไม่เอาค่าเดิม
            params.set('status', newStatus);

            // ✅ เพิ่มค่า default ตามหมวดที่เลือก
            const today = new Date().toISOString().split('T')[0];

            if (newStatus === 'scheduledComplete' || newStatus === 'todaymakeappointmenttoday' || newStatus === 'today-status') {
                params.set('date', today); // set วันนี้
            } else if (newStatus === 'scheduled' || newStatus === 'missed') {
                // ไม่ต้องเพิ่ม date parameter เพื่อแสดงทั้งหมด
            }

            // ✅ ไม่เอาตัวกรองอื่นๆ มาด้วย - reset เป็นค่า default
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        // ✅ การจัดการปุ่มนัดหมาย
        $(document).on('click', '.btn-assign', function () {
            const reportId = $(this).data('id');
            const name = $(this).data('name');

            $('#singleReportId').val(reportId);
            $('#singleSoldierName').text(name);

            // โหลดข้อมูลผู้ป่วย
            $.ajax({
                url: `/medical/get-report/${reportId}`,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!data.success) {
                        Swal.fire("ผิดพลาด", data.message, "error");
                        return;
                    }

                    $('#soldierName').text(data.soldier_name);
                    $('#soldierUnit').text(data.soldier_unit);
                    $('#soldierRotation').text(data.soldier_rotation);
                    $('#soldierTraining').text(data.soldier_training);
                    $('#soldierTemp').text(data.temperature + "°C");
                    $('#soldierBP').text(data.blood_pressure);
                    $('#soldierHeartRate').text(data.heart_rate + " BPM");
                    $('#soldierPain').text(data.pain_score + "/10");
                    $('#soldierSymptom').text(data.symptom_description);

                    let riskCode = data.risk_level;
                    if (riskCode === 'yellow') riskCode = 'warning';
                    else if (riskCode === 'red') riskCode = 'critical';
                    else if (riskCode === 'green') riskCode = 'normal';

                    const riskMap = {
                        critical: '🔴 ฉุกเฉิน',
                        warning: '🟡 เฝ้าระวัง',
                        normal: '🟢 ปกติ'
                    };
                    $('#soldierRiskLevel').text(riskMap[riskCode] || '-');

                    function loadImages(images, containerId) {
                        const container = $(`#${containerId}`);
                        container.empty();
                        if (!images.length) {
                            container.html('<p class="text-muted">ไม่มีรูปภาพ</p>');
                            return;
                        }
                        images.forEach(img => {
                            container.append(`
                        <div class="col-md-4 mb-2">
                            <div class="image-wrapper">
                                <img src="${img}" class="img-fluid" alt="รูป">
                            </div>
                        </div>
                    `);
                        });
                    }

                    loadImages(data.images.atk, 'atkImages');
                    loadImages(data.images.symptom, 'symptomImages');

                    // เปิด modal หลังโหลดข้อมูล
                    new bootstrap.Modal(document.getElementById('appointmentModal')).show();
                },
                error: () => Swal.fire("ผิดพลาด", "ไม่สามารถโหลดข้อมูลได้", "error")
            });
        });

        // ✅ ส่งข้อมูลนัดหมาย
        $("#confirmAppointment").click(function () {
            const $btn = $(this);
            $btn.prop('disabled', true); // ป้องกันการคลิกซ้ำ

            let singleId = $("#singleReportId").val();
            let selectedIds = singleId ? [singleId] : $(".selectRow:checked").map(function () {
                return $(this).data("id");
            }).get();

            selectedIds = [...new Set(selectedIds)]; // ลบค่าซ้ำ

            if (selectedIds.length === 0) {
                Swal.fire("กรุณาเลือกทหารที่ต้องการนัดหมาย", "", "warning");
                $btn.prop('disabled', false);
                return;
            }

            const date = $("#appointment_date").val();
            const location = $("#appointment_location").val();
            const type = $("#case_type").val();

            if (!date || !location || !type) {
                Swal.fire("กรุณากรอกข้อมูลให้ครบ", "", "warning");
                $btn.prop('disabled', false);
                return;
            }

            const data = {
                _token: "{{ csrf_token() }}",
                "medical_report_ids[]": selectedIds,
                appointment_date: date,
                appointment_location: location,
                case_type: type
            };

            $.ajax({
                url: "{{ route('appointments.store') }}",
                type: "POST",
                dataType: "json",
                data: data,
                success: () => {
                    Swal.fire("การนัดหมายสำเร็จ", "", "success").then(() => {
                        const baseUrl = window.location.pathname;
                        const url = `${baseUrl}?status=scheduled`;
                        window.location.href = url;
                    });
                },
                error: (xhr) => {
                    console.error(xhr.responseText);
                    Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้", "error");
                    $btn.prop('disabled', false);
                }
            });
        });

        // ✅ เปิด Modal รายละเอียดผู้ป่วย
        $('.btn-detail').click(function () {
            const reportId = $(this).data('id');
            if (!reportId) {
                Swal.fire("เกิดข้อผิดพลาด", "ไม่พบ ID ของผู้ป่วย", "error");
                return;
            }

            $.ajax({
                url: `/medical/get-report/${reportId}`,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (!data.success) {
                        Swal.fire("ผิดพลาด", data.message, "error");
                        return;
                    }

                    // กรอกข้อมูลลงใน detailModal
                    $('#detailModal #soldierName').text(data.soldier_name);
                    $('#detailModal #soldierUnit').text(data.soldier_unit);
                    $('#detailModal #soldierRotation').text(data.soldier_rotation);
                    $('#detailModal #soldierTraining').text(data.soldier_training ?? '-');
                    $('#detailModal #soldierTemp').text(data.temperature + "°C");
                    $('#detailModal #soldierBP').text(data.blood_pressure);
                    $('#detailModal #soldierHeartRate').text(data.heart_rate + " BPM");
                    $('#detailModal #soldierPain').text(data.pain_score + "/10");
                    $('#detailModal #soldierSymptom').text(data.symptom_description);

                    let riskCode = data.risk_level;
                    if (riskCode === 'yellow') riskCode = 'warning';
                    else if (riskCode === 'red') riskCode = 'critical';
                    else if (riskCode === 'green') riskCode = 'normal';

                    const riskMap = {
                        critical: '🔴 ฉุกเฉิน',
                        warning: '🟡 เฝ้าระวัง',
                        normal: '🟢 ปกติ'
                    };
                    $('#detailModal #soldierRiskLevel').text(riskMap[riskCode] || '-');

                    function loadImages(images, containerId) {
                        const container = $(`#detailModal #${containerId}`);
                        container.empty();
                        if (!images.length) {
                            container.html('<p class="text-muted">ไม่มีรูปภาพ</p>');
                            return;
                        }
                        images.forEach(img => {
                            container.append(`
                        <div class="col-md-4 mb-2">
                            <div class="image-wrapper">
                                <img src="${img}" class="img-fluid" alt="รูป">
                            </div>
                        </div>
                    `);
                        });
                    }

                    loadImages(data.images.atk, 'atkImages');
                    loadImages(data.images.symptom, 'symptomImages');

                    // เปิด modal ที่ถูกต้อง
                    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                    detailModal.show();
                },
                error: () => Swal.fire("ผิดพลาด", "ไม่สามารถโหลดข้อมูลได้", "error")
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        // เมื่อเลือกผลัดหรือหน่วยฝึก
        $('#rotationFilter, #trainingUnitFilter').change(function () {
            filterData();
        });

        // ฟังก์ชันสำหรับกรองข้อมูล
        function filterData() {
            const status = $('#statusFilter').val();
            const caseType = $('#caseTypeFilter').val();
            const date = $('#dateFilter').val();
            const rotationId = $('#rotationFilter').val();
            const trainingUnitId = $('#trainingUnitFilter').val();

            // ส่ง request ไปยังเซิร์ฟเวอร์พร้อมพารามิเตอร์ใหม่
            window.location.href = window.location.pathname +
                '?status=' + status +
                '&case_type=' + caseType +
                '&date=' + date +
                '&rotation_id=' + rotationId +
                '&training_unit_id=' + trainingUnitId;
        }
    });
</script>

<script>
    // ✅ แก้ไข JavaScript สำหรับ Filter Modal
    $(document).ready(function () {
        const filterModal = new bootstrap.Modal(document.getElementById('filterModal'));

        $('#openFilterModal').click(function () {
            const status = $('#statusFilter').val();

            // ซ่อนทุก filter ก่อน
            $('#dateFilterGroup').addClass('d-none');
            $('#caseTypeFilterGroup').addClass('d-none');
            $('#todayStatusGroup').addClass('d-none');

            // แสดง filter ตาม status
            if (status === 'scheduled') {
                $('#dateFilterGroup').removeClass('d-none');
                $('#caseTypeFilterGroup').removeClass('d-none');
            } else if (status === 'scheduledComplete' || status === 'missed' || status === 'todaymakeappointmenttoday') {
                $('#dateFilterGroup').removeClass('d-none');
                $('#caseTypeFilterGroup').removeClass('d-none');
            } else if (status === 'today-status') {
                $('#dateFilterGroup').removeClass('d-none');
                $('#caseTypeFilterGroup').removeClass('d-none');
                $('#todayStatusGroup').removeClass('d-none');
            }

            // ✅ ตั้งค่า default ในฟอร์ม filter
            const urlParams = new URLSearchParams(window.location.search);

            // วันที่ - ใช้จาก URL หรือค่า default
            const currentDate = urlParams.get('date');
            if (currentDate) {
                $('#dateFilterModal').val(currentDate);
            } else {
                // ✅ ถ้าไม่มีในURL ให้ใช้ค่าdefault ตามหมวด
                if (status === 'scheduledComplete' || status === 'todaymakeappointmenttoday' || status === 'today-status') {
                    const today = new Date().toISOString().split('T')[0];
                    $('#dateFilterModal').val(today);
                } else {
                    $('#dateFilterModal').val(''); // ไม่มีวันที่
                }
            }

            // ✅ ประเภทผู้ป่วย - reset เป็น 'all'
            const currentCaseType = urlParams.get('case_type') || 'all';
            $('#caseTypeFilterModal').val(currentCaseType);

            // ✅ ผลัด - reset เป็นค่าว่าง
            const currentRotation = urlParams.get('rotation_id') || '';
            $('#rotationFilterModal').val(currentRotation);

            // ✅ หน่วยฝึก - reset เป็นค่าว่าง
            const currentUnit = urlParams.get('training_unit_id') || '';
            $('#trainingUnitFilterModal').val(currentUnit);

            // ✅ สถานะวันนี้ - reset เป็น 'all'
            const currentTodayStatus = urlParams.get('today_status') || 'all';
            $('#todayStatusFilterModal').val(currentTodayStatus);

            filterModal.show();
        });

        // ✅ อัพเดท Apply Filter
        $('#applyFilter').click(function () {
            const status = $('#statusFilter').val();
            const date = $('#dateFilterModal').val();
            const caseType = $('#caseTypeFilterModal').val();
            const rotation = $('#rotationFilterModal').val();
            const unit = $('#trainingUnitFilterModal').val();
            const todayStatus = $('#todayStatusFilterModal').val();

            let url = window.location.pathname + '?status=' + status;

            // เพิ่ม parameters ตามหมวด
            if (status === 'scheduled' || status === 'scheduledComplete' || status === 'missed' || status === 'todaymakeappointmenttoday') {
                if (date) {
                    url += '&date=' + date;
                }
                if (caseType && caseType !== 'all') {
                    url += '&case_type=' + caseType;
                }
            } else if (status === 'today-status') {
                if (date) {
                    url += '&date=' + date;
                }
                if (caseType && caseType !== 'all') {
                    url += '&case_type=' + caseType;
                }
                if (todayStatus && todayStatus !== 'all') {
                    url += '&today_status=' + todayStatus;
                }
            }

            // เพิ่ม parameters ที่แสดงเสมอ
            if (rotation) url += '&rotation_id=' + rotation;
            if (unit) url += '&training_unit_id=' + unit;

            filterModal.hide();
            window.location.href = url;
        });
    });
</script>

<script>
    // ฟังเหตุการณ์ตอน modal ปิด
    $('#filterModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    $('#appointmentModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });

    $('#detailModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
    });
</script>

<script>
    $(document).ready(function () {
        // เมื่อกด checkbox "เลือกทั้งหมด"
        $('#selectAll').change(function () {
            var isChecked = $(this).prop('checked');
            $('.selectRow').prop('checked', isChecked);
        });
    });
</script>

<script>
    $(document).on('click', '.btn-edit-appointment', function () {
        const id = $(this).data('id');

        $.get(`/appointments/${id}/edit`, function (data) {
            $('#editAppointmentId').val(data.id);
            $('#edit_appointment_date').val(data.appointment_date);
            $('#edit_appointment_location').val(data.appointment_location);
            $('#edit_case_type').val(data.case_type);

            const modal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
            modal.show();
        });
    });

    $('#submitEditAppointment').click(function () {
        const id = $('#editAppointmentId').val();
        const data = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            appointment_date: $('#edit_appointment_date').val(),
            appointment_location: $('#edit_appointment_location').val(),
            case_type: $('#edit_case_type').val(),
        };

        $.ajax({
            url: `/appointments/${id}`,
            type: 'POST',
            data: data,
            success: function (res) {
                Swal.fire('สำเร็จ', res.message, 'success').then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                console.error(xhr.responseText);
            }
        });
    });
</script>