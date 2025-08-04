<!DOCTYPE html>
<html lang="en">
@include('themes.head')


<style>
    .custom-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        background: #fff;
        position: relative;
        text-align: left;
    }

    .custom-card h5 {
        margin-bottom: 5px;
    }

    .custom-card h3 {
        font-weight: bold;
    }

    .custom-card-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        padding: 5px;
        border-radius: 50%;
    }

    .custom-card-icon i {
        font-size: 20px;
    }

    .critical-box {
        border-left: 4px solid #dc3545;
        /* แดง */
        border-radius: 6px;
        transition: 0.2s;
    }

    .critical-box:hover {
        background-color: #fdf1f1;
    }
</style>


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')

        @include('themes.opd.menuopd')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-3 flex-wrap gap-2">
                                <h2 class="fw-bold mb-0" style="color: #2c3e50;">
                                    แดชบอร์ดOPD
                                </h2>


                                <a href="{{ url('hospital/appointments') }}" class="btn btn-success">
                                    วินิจฉัยโรค
                                </a>

                            </div>
                            <div class="row mt-2">
                                <!-- จำนวนทหารในหน่วย -->

                                <!-- นัดหมายสำเร็จ -->
                                <div class="col-md-3">
                                    <a href={{ url('/opd/appointments') }} class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card">
                                            <h5>ผู้ป่วยตามนัดวันนี้
                                            </h5>
                                            <h3>
                                                {{ $totalAppointmentsToday }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span>
                                            </h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-users" style="color: #10b981;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href={{ url('/opd/appointments?status=today&case_type=all&rotation_id=&training_unit_id=&today_status=ยังไม่ได้ทำการรักษา') }}
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card">
                                            <h5>ยังไม่ได้เข้ารับการรักษา
                                            </h5>
                                            <h3>
                                                {{ $patientsNotCheckedInToday }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span>
                                            </h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-exclamation-circle" style="color: #dc3545;"></i></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>


                                <div class="col-md-3">
                                    <a href="{{ url('/opd/appointments?status=today&case_type=all&rotation_id=&training_unit_id=&today_status=อยู่ระหว่างการรักษา') }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card">
                                            <h5>อยู่ระหว่างการรักษา</h5>
                                            <h3>
                                                {{ $patientsCheckedInToday }}
                                                <span style="font-size: 16px; font-weight: normal;">คน</span>
                                            </h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-stethoscope" style="color: #6f42c1;"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>





                                <div class="col-md-3">
                                     <a href="{{ url('/opd/appointments?status=today&case_type=all&rotation_id=&training_unit_id=&today_status=รักษาสำเร็จ') }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card">
                                            <h5>รักษาเสร็จสิ้น</h5>
                                            <h3>
                                                {{ $patientsTreatmentCompletedToday }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span>
                                            </h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-check-circle" style="color:rgb(40, 167, 69);"></i>
                                            </div>

                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <!-- Today's Appointments -->
                                <div class="col-md-6 d-flex align-items-stretch">
                                    <div class="card shadow-sm w-100">
                                        <div class="card-body">
                                            <div class="card p-3 shadow-sm"
                                                style="background-color: #f8f9fa; border-radius: 8px;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="fw-bold">
                                                        นัดหมายปกติวันนี้
                                                        <span class="text-primary fw-bold">
                                                            {{ $normalAppointmentsToday->count() }} คน
                                                        </span>
                                                    </h5>

                                                    <a href="{{ url('/opd/appointments?status=today&case_type=normal&rotation_id=&training_unit_id=&today_status=all') }}"
                                                        class="btn btn-info btn-sm">
                                                        ดูทั้งหมด
                                                    </a>


                                                </div>
                                            </div>

                                            <table class="table table-striped table-bordered mt-3">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>ชื่อ - นามสกุล</th>
                                                        <th>นัดหมาย</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($normalAppointmentsToday->take(3) as $appointment)
                                                        <tr>
                                                            <td>
                                                                {{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <strong>เวลา:</strong>
                                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                                น.<br>
                                                                <strong>สถานที่:</strong>
                                                                {{ $appointment->appointment_location ?? '-' }}
                                                            </td>
                                                            <td class="text-center">
                                                                @php
                                                                    $checkin = $appointment->checkin;
                                                                    $treatmentStatus = $checkin && $checkin->treatment ? $checkin->treatment->treatment_status : null;
                                                                @endphp

                                                                @if ($appointment->status === 'missed')
                                                                    <span class="badge custom-badge bg-white shadow">🔴
                                                                        ไม่มาตามนัด</span>
                                                                @elseif (is_null($checkin) || $checkin->checkin_status === 'not-checked-in')
                                                                    <span class="badge custom-badge bg-white shadow">🟠
                                                                        ยังไม่ได้เข้ารับการรักษา</span>
                                                                @elseif ($checkin->checkin_status === 'checked-in' && $treatmentStatus === 'not-treated')
                                                                    <span class="badge custom-badge bg-white shadow">🟡
                                                                        อยู่ระหว่างการรักษา</span>
                                                                @elseif ($checkin->checkin_status === 'checked-in' && $treatmentStatus === 'treated')
                                                                    <span class="badge custom-badge bg-white shadow">🟢
                                                                        รักษาเสร็จสิ้น</span>
                                                                @else
                                                                    <span class="badge custom-badge bg-white shadow">⚪
                                                                        สถานะไม่ระบุ</span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-danger">❌
                                                                ไม่มีเคสปกติในวันนี้</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>


                                        </div>



                                    </div>
                                </div>

                                <div class="col-md-6 d-flex align-items-stretch">
                                    <div class="card shadow-sm w-100">
                                        <div class="card-body">
                                            <div class="card p-3 shadow-sm"
                                                style="background-color: #f8d7da; border-radius: 8px;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="fw-bold">
                                                        เคสวิกฤติวันนี้
                                                        <span class="text-danger fw-bold">
                                                            {{ $criticalCount ?? 0 }} คน
                                                        </span>
                                                    </h5>

                                                    <a href="{{ url('/opd/appointments?status=today&case_type=critical&rotation_id=&training_unit_id=&today_status=all') }}"
                                                        class="btn btn-info btn-sm">
                                                        ดูทั้งหมด
                                                    </a>


                                                </div>
                                            </div>

                                            <table class="table table-striped table-bordered mt-3">
                                                <thead class="table-primary text-center">
                                                    <tr>
                                                        <th>ชื่อ - นามสกุล</th>
                                                        <th>นัดหมาย</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($criticalAppointments->take(3) as $appointment)
                                                        <tr>
                                                            <td>
                                                                {{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <strong>เวลา:</strong>
                                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                                น.<br>
                                                                <strong>สถานที่:</strong>
                                                                {{ $appointment->appointment_location ?? '-' }}
                                                            </td>
                                                            <td class="text-center">
                                                                @php
                                                                    $checkin = $appointment->checkin;
                                                                    $treatmentStatus = $checkin && $checkin->treatment ? $checkin->treatment->treatment_status : null;
                                                                @endphp

                                                                <!-- ตรวจสอบสถานะ checkin และ treatment -->
                                                                @if ($checkin && $checkin->checkin_status === 'checked-in' && $treatmentStatus !== 'treated')
                                                                    <span
                                                                        class="badge bg-light text-success shadow-sm px-3 py-2">

                                                                        🟡 อยู่ระหว่างการรักษา
                                                                    </span>
                                                                @elseif ($treatmentStatus === 'treated')
                                                                    <span
                                                                        class="badge bg-light text-primary shadow-sm px-3 py-2">

                                                                        🟢 รักษาเสร็จสิ้น
                                                                    </span>
                                                                @elseif ($checkin && $checkin->checkin_status === 'not-checked-in')
                                                                    <span
                                                                        class="badge bg-light text-primary shadow-sm px-3 py-2">

                                                                        🟠 ยังไม่ได้เข้ารับการรักษา
                                                                    </span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-danger">❌
                                                                ไม่มีเคสปกติในวันนี้</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>


                                        </div>



                                    </div>
                                </div>







                                @include('themes.script')

</body>

</html>