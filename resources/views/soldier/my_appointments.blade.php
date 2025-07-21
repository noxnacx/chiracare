<!DOCTYPE html>
<html lang="th">

@include('themes.head')

<style>
    :root {
        --appointment-bg: #ffffff;
        --appointment-shadow: rgba(0, 0, 0, 0.08);
        --appointment-border: #e9ecef;
        --text-primary: #343a40;
        --text-secondary: #6c757d;
        --icon-color: #495057;
        --mental-health-color: #6f42c1; /* สีสำหรับสุขภาพจิต */
    }
    .content-wrapper {
        background-color: #f4f6f9;
    }
    .appointment-card {
        background-color: var(--appointment-bg);
        border: 1px solid var(--appointment-border);
        border-radius: 12px;
        box-shadow: 0 4px 12px var(--appointment-shadow);
        margin-bottom: 1.5rem;
        transition: all 0.3s ease-in-out;
        border-left: 5px solid;
    }
    .appointment-card.physical { border-left-color: var(--primary-color); }
    .appointment-card.mental { border-left-color: var(--mental-health-color); }

    .appointment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px var(--appointment-shadow);
    }
    .card-header.appointment-header {
        background-color: transparent;
        border-bottom: 1px solid var(--appointment-border);
        font-weight: 600;
        color: var(--text-primary);
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
        color: var(--icon-color);
        margin-right: 12px;
        margin-top: 3px;
        width: 20px;
        text-align: center;
    }
    .appointment-label {
        font-weight: 600;
        color: var(--text-primary);
        min-width: 120px;
    }
    .appointment-value {
        color: var(--text-secondary);
    }
</style>


<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>นัดหมายของฉัน</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">

                    @forelse($allAppointments as $appointment)
                        <div class="appointment-card {{ $appointment->type == 'สุขภาพกาย' ? 'physical' : 'mental' }}">
                            {{-- ⬇️⬇️⬇️ [จุดที่แก้ไข] เอา display:flex ออกเพื่อให้วันที่อยู่ตรงกลาง ⬇️⬇️⬇️ --}}
                            <div class="card-header appointment-header">
                                <div>
                                    <span>
                                        <i class="fas fa-calendar-day me-2 {{ $appointment->type == 'สุขภาพกาย' ? 'text-primary' : 'text-purple' }}"></i>
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

                                {{-- ⬇️⬇️⬇️ [จุดที่แก้ไข] ลบส่วนของ Status badge ออก ⬇️⬇️⬇️ --}}

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
                                    <span class="appointment-value">-</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">คุณยังไม่มีประวัติการนัดหมาย</h4>
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
