<!DOCTYPE html>
<html lang="th">

@include('themes.head')

<body class="hold-transition sidebar-mini layout-fixed">

    <style>
        /* --- Theme Colors & Interactions --- */
        :root {
            /* Primary Theme Colors */
            --theme-secondary-bg: #F8F9FA; /* Light Gray Background for the whole page */
            --theme-text-dark: #343a40;
            --theme-text-light: #6c757d;
            --theme-card-bg: #FFFFFF;
            --theme-border-color: #e9ecef;

            /* Info Color (Original Blue-Gray) for Physical Appointments */
            --theme-info-color: #A9C5C8;

            /* Accent Purple Color */
            --theme-accent-color: #8E44AD;
            --theme-accent-darker: #7D3C98;
            --theme-accent-darkest: #6C3483;
            --theme-accent-focus: rgba(142, 68, 173, 0.25);
        }

        /* --- General Styling --- */
        body {
            background-color: var(--theme-secondary-bg);
            font-family: 'Sarabun', sans-serif;
        }
        .content-wrapper {
            /* Removed the background color as requested */
            background-color: transparent;
        }
        h1, h4 {
            color: var(--theme-text-dark);
            font-weight: bold;
        }

        /* --- Base Card Styling --- */
        .card {
            background-color: var(--theme-card-bg);
            border: 1px solid var(--theme-border-color);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        /* --- Appointment Card Specific Styling --- */
        .appointment-card {
            border-left: 5px solid; /* The colored indicator border */
        }
        .appointment-card.physical {
            border-left-color: var(--theme-info-color); /* Blue-gray for physical */
        }
        .appointment-card.mental {
            border-left-color: var(--theme-accent-color); /* Purple for mental */
        }

        .card-header.appointment-header {
            background-color: transparent;
            border-bottom: 1px solid var(--theme-border-color);
            font-weight: 600;
            color: var(--theme-text-dark);
            padding: 1rem 1.25rem;
        }

        .appointment-body {
            padding: 1.25rem;
        }

        .appointment-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }
        .appointment-item:last-child {
            margin-bottom: 0;
        }

        .appointment-icon {
            color: var(--theme-text-light);
            margin-right: 12px;
            margin-top: 3px;
            width: 20px;
            text-align: center;
        }

        .appointment-label {
            font-weight: 600;
            color: var(--theme-text-dark);
            min-width: 120px;
        }

        .appointment-value {
            color: var(--theme-text-light);
        }

    </style>

    <div class="wrapper">

        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 fw-bold">
                                <i class="fas fa-calendar-check me-2" style="color: var(--theme-accent-color);"></i>นัดหมายของฉัน
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">

                    @forelse($allAppointments as $appointment)
                        <div class="card appointment-card {{ $appointment->type == 'สุขภาพกาย' ? 'physical' : 'mental' }}">
                            <div class="card-header appointment-header">
                                <div>
                                    <span>
                                        <i class="fas fa-calendar-day me-2"
                                           style="color: {{ $appointment->type == 'สุขภาพกาย' ? 'var(--theme-info-color)' : 'var(--theme-accent-color)' }}"></i>
                                        วันที่นัด: {{ \Carbon\Carbon::parse($appointment->appointment_date)->thaidate('j F Y') }}
                                    </span>
                                    <small class="d-block text-muted ms-4 ps-1">
                                        @if($appointment->type == 'สุขภาพกาย')
                                            นัดหมายทั่วไป
                                        @else
                                            นัดหมายจิตเวช
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="appointment-body">
                                <div class="appointment-item">
                                    <i class="fas fa-clock appointment-icon"></i>
                                    <span class="appointment-label">เวลา:</span>
                                    <span class="appointment-value">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }} น.
                                    </span>
                                </div>
                                <div class="appointment-item">
                                    <i class="fas fa-map-marker-alt appointment-icon"></i>
                                    <span class="appointment-label">สถานที่:</span>
                                    <span class="appointment-value">{{ $appointment->appointment_location ?? 'ไม่ระบุ' }}</span>
                                </div>
                                <div class="appointment-item">
                                    <i class="fas fa-briefcase-medical appointment-icon"></i>
                                    <span class="appointment-label">ประเภทเคส:</span>
                                    <span class="appointment-value">
                                        @if($appointment->case_type == 'critical')
                                            <span class="text-danger fw-bold">เคสเร่งด่วน</span>
                                        @else
                                            <span>เคสทั่วไป</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="appointment-item">
                                    <i class="fas fa-info-circle appointment-icon"></i>
                                    <span class="appointment-label">หมายเหตุ:</span>
                                    <span class="appointment-value">{{ $appointment->notes ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="card" style="background-color: #FFFFFFa0; border-style: dashed;">
                                <div class="card-body">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">คุณยังไม่มีประวัติการนัดหมาย</h4>
                                </div>
                            </div>
                        </div>
                    @endforelse

                </div>
            </section>
        </div>

        @include('themes.soldier.footersoldier')

    </div>

    @include('themes.script')

</body>
</html>
