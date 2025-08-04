{{--
    Appointment Today Component

    Props:
    - $criticalAppointments: Collection of critical appointments
    - $appointments: Collection of normal appointments
--}}

@props([
    'criticalAppointments' => collect(),
    'appointments' => collect()
])

<div class="appointment-header-section mb-3">
    <!-- บรรทัดที่ 1: หัวข้อและลิงก์ดูทั้งหมด -->
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-dark mb-0">
            รายการหมายวันนี้
        </h5>
        <a href="/hospital/appointments?status=today-status" class="text-primary"
            style="font-size: 12px; text-decoration: none;">
            ดูทั้งหมด
        </a>
    </div>

    <!-- บรรทัดที่ 2: วันที่และจำนวนนัดหมาย -->
    <div class="appointment-summary mt-2">
        <div class="d-flex justify-content-between align-items-center">
            <div class="current-date">
                <span style="font-size: 11px; color: #6c757d;">
                    วัน{{ \Carbon\Carbon::now()->locale('th')->dayName }}ที่
                    {{ \Carbon\Carbon::now()->day }}
                    {{ \Carbon\Carbon::now()->locale('th')->monthName }}
                    {{ \Carbon\Carbon::now()->year + 543 }}
                </span>
            </div>
            <div class="total-appointments">
                <span class="appointment-badge">
                    <i class="fas fa-users me-1"></i>
                    <span>{{ $criticalAppointments->count() }} ราย</span>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- รายการนัดหมาย -->
<div class="appointment-list">

    {{-- นัดหมายวิกิด --}}
    <div class="appointment-category">
        <!-- Header Section -->
        <div class="category-header urgent">
            <div class="category-title">
                <i class="fas fa-exclamation-triangle"></i>
                <span>นัดหมายวิกิด</span>
            </div>
            <div class="category-actions">
                <span class="category-count urgent-count">
                    {{ $criticalAppointments->count() }} ราย
                </span>
                <a href="/hospital/appointments?status=today-status&case_type=critical&today_status=all"
                    class="category-view-all urgent-link">ดูทั้งหมด</a>
            </div>
        </div>

        @php
            $criticalLimited = $criticalAppointments->take(2);
        @endphp

        @if($criticalLimited->count() > 0)
            @foreach($criticalLimited as $appointment)
                <div class="appointment-item urgent-item">
                    <!-- Avatar -->
                    <div class="appointment-avatar urgent-bg">
                        @if($appointment->medicalReport->soldier->soldier_image)
                            <img src="{{ asset('uploads/soldiers/' . basename($appointment->medicalReport->soldier->soldier_image)) }}"
                                class="profile-image" alt="Soldier Image">
                        @else
                            <i class="fas fa-user-injured"></i>
                        @endif
                    </div>

                    <!-- Patient Details -->
                    <div class="appointment-details">
                        <!-- Patient Name -->
                        <div class="patient-name">
                            {{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                            {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                        </div>

                        <!-- Time and Location Row -->
                        <div class="appointment-info-row">
                            <div class="info-item">
                                <span class="info-label">เวลา:</span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                    น.
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">สถานที่:</span>
                                <span class="info-value">
                                    {{ $appointment->appointment_location ?? 'ไม่ระบุ' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="appointment-status-row">
                            @php
                                $status = '';
                                $statusClass = '';
                                if ($appointment->status === 'missed') {
                                    $status = 'ไม่มาตามนัด';
                                    $statusClass = 'status-missed';
                                } elseif (is_null($appointment->checkin) || $appointment->checkin->checkin_status === 'not-checked-in') {
                                    $status = 'ยังไม่ได้เข้ารับการรักษา';
                                    $statusClass = 'status-waiting';
                                } elseif (
                                    $appointment->checkin->checkin_status === 'checked-in' &&
                                    $appointment->checkin->treatment &&
                                    $appointment->checkin->treatment->treatment_status === 'not-treated'
                                ) {
                                    $status = 'อยู่ระหว่างการรักษา';
                                    $statusClass = 'status-treating';
                                } elseif (
                                    $appointment->checkin->checkin_status === 'checked-in' &&
                                    $appointment->checkin->treatment &&
                                    $appointment->checkin->treatment->treatment_status === 'treated'
                                ) {
                                    $status = 'รักษาเสร็จสิ้น';
                                    $statusClass = 'status-completed';
                                } else {
                                    $status = 'สถานะไม่ระบุ';
                                    $statusClass = 'status-unknown';
                                }
                            @endphp

                            <div class="status-container">
                                <span class="status-badge {{ $statusClass }}">
                                    <span class="status-dot"></span>
                                    {{ $status }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        @else
            <div class="empty-state">
                <span class="empty-text">ไม่มีเคสวิกิดในวันนี้</span>
            </div>
        @endif
    </div>

    {{-- นัดหมายปกติ --}}
    <div class="appointment-category">
        <div class="category-header normal">
            <div class="category-title">
                <span>นัดหมายปกติ</span>
            </div>
            <div class="category-actions">
                <span class="category-count normal-count">
                    @php
                        $normalToday = $appointments
                            ->where('case_type', 'normal')
                            ->filter(function ($appointment) {
                                return \Carbon\Carbon::parse($appointment->appointment_date)->isToday();
                            })
                            ->count();
                    @endphp
                    {{ $normalToday }} ราย
                </span>
                <a href="/hospital/appointments?status=today-status&case_type=normal&today_status=all&rotation_id=&training_unit_id="
                    class="category-view-all normal-link">ดูทั้งหมด</a>
            </div>
        </div>

        @php
            $normalAppointments = $appointments->where('case_type', 'normal')->take(3);
        @endphp

        @if($normalAppointments->count() > 0)
            @foreach($normalAppointments as $appointment)
                <div class="appointment-item normal-item">
                    <!-- Avatar -->
                    <div class="appointment-avatar normal-bg">
                        @if($appointment->medicalReport->soldier->soldier_image)
                            <img src="{{ asset('uploads/soldiers/' . basename($appointment->medicalReport->soldier->soldier_image)) }}"
                                class="profile-image" alt="Soldier Image">
                        @else
                            <i class="fas fa-stethoscope"></i>
                        @endif
                    </div>

                    <!-- Patient Details -->
                    <div class="appointment-details">
                        <!-- Patient Name -->
                        <div class="patient-name">
                            {{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                            {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                        </div>

                        <!-- Time and Location Row -->
                        <div class="appointment-info-row">
                            <div class="info-item">
                                <span class="info-label">เวลา:</span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                    น.
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">สถานที่:</span>
                                <span class="info-value">
                                    {{ $appointment->appointment_location ?? 'ไม่ระบุ' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="appointment-status-row">
                            @php
                                $status = '';
                                $statusClass = '';
                                if ($appointment->status === 'missed') {
                                    $status = 'ไม่มาตามนัด';
                                    $statusClass = 'status-missed';
                                } elseif (is_null($appointment->checkin) || $appointment->checkin->checkin_status === 'not-checked-in') {
                                    $status = 'ยังไม่ได้เข้ารับการรักษา';
                                    $statusClass = 'status-waiting';
                                } elseif (
                                    $appointment->checkin->checkin_status === 'checked-in' &&
                                    $appointment->checkin->treatment &&
                                    $appointment->checkin->treatment->treatment_status === 'not-treated'
                                ) {
                                    $status = 'อยู่ระหว่างการรักษา';
                                    $statusClass = 'status-treating';
                                } elseif (
                                    $appointment->checkin->checkin_status === 'checked-in' &&
                                    $appointment->checkin->treatment &&
                                    $appointment->checkin->treatment->treatment_status === 'treated'
                                ) {
                                    $status = 'รักษาเสร็จสิ้น';
                                    $statusClass = 'status-completed';
                                } else {
                                    $status = 'สถานะไม่ระบุ';
                                    $statusClass = 'status-unknown';
                                }
                            @endphp

                            <div class="status-container">
                                <span class="status-badge {{ $statusClass }}">
                                    <span class="status-dot"></span>
                                    {{ $status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Arrow -->
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <span class="empty-text">ไม่มีเคสปกติในวันนี้</span>
            </div>
        @endif
    </div>

    {{-- นัดหมายจิตเวช --}}
    <div class="appointment-category">
        <div class="category-header mental">
            <div class="category-title">
                <span>นัดหมายจิตเวช</span>
            </div>
            <div class="category-actions">
                <span class="category-count mental-count">2 ราย</span>
                <a href="#" class="category-view-all mental-link">ดูทั้งหมด</a>
            </div>
        </div>

        <div class="appointment-item mental-item">
            <div class="appointment-avatar mental-bg">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="appointment-details">
                <div class="patient-name">นายอนุชา สงบใจ</div>

                <!-- Time and Location Row -->
                <div class="appointment-info-row">
                    <div class="info-item">
                        <span class="info-label">เวลา:</span>
                        <span class="info-value">11:00 น.</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">สถานที่:</span>
                        <span class="info-value">แผนกจิตเวช</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="appointment-status-row">
                    <div class="status-container">
                        <span class="status-badge status-waiting">
                            <span class="status-dot"></span>
                            ยังไม่ได้เข้ารับการรักษา
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="appointment-item mental-item">
            <div class="appointment-avatar mental-bg">
                <i class="fas fa-comments"></i>
            </div>
            <div class="appointment-details">
                <div class="patient-name">นางสาวขวัญใจ มีสุข</div>

                <!-- Time and Location Row -->
                <div class="appointment-info-row">
                    <div class="info-item">
                        <span class="info-label">เวลา:</span>
                        <span class="info-value">14:00 น.</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">สถานที่:</span>
                        <span class="info-value">แผนกจิตวิทยา</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="appointment-status-row">
                    <div class="status-container">
                        <span class="status-badge status-treating">
                            <span class="status-dot"></span>
                            อยู่ระหว่างการรักษา
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<link rel="stylesheet" href="/css/components/appiontmenttoday.css">
