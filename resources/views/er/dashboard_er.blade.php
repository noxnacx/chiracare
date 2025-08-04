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

    .col-6,
    .col-md-4,
    .col-lg-2 {
        flex-grow: 1;
        /* ทำให้คอลัมน์ยืดเต็มพื้นที่ */
        flex-basis: 0;
        /* ควบคุมขนาดเริ่มต้น */
        min-width: 220px;
        /* กำหนดขนาดขั้นต่ำ */
        max-width: 250px;
        /* กำหนดขนาดสูงสุด */
    }

    /* เมื่อหน้าจอเล็กลง จะจัดให้คอลัมน์ยืดเต็มความกว้าง */
    @media (max-width: 768px) {
        .col-6 {
            width: 100%;
        }
    }

    /* ปรับขนาดความยาวของคอลัมน์ */
    .custom-card {
        height: 100%;
        /* ให้คอลัมน์มีความสูงเต็ม */
    }

    .custom-card-icon i {
        font-size: 20px;
    }
</style>


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')

        @include('themes.er.menuer')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-3 flex-wrap gap-2">
                                <h2 class="fw-bold mb-0" style="color: #2c3e50;">
                                    แดชบอร์ดER
                                </h2>


                                <a href="/er/patients" class="btn btn-success">
                                    วินิจฉัยโรค
                                </a>

                            </div>
                            <div class="row mt-2">
                                <!-- แถวแรก -->
                                <div class="col-6 col-md-4 col-lg-2 mb-3">
                                    <a href="{{ url('er/today?filter_status=all&risk_level=all') }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card h-90">
                                            <h5>ผู้ป่วย ER วันนี้</h5>
                                            <h3>{{ $appointment_today_count }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span></h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-users" style="color:rgb(255, 0, 0);"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-6 col-md-4 col-lg-2 mb-3">
                                    <a href="{{ route('er.today', ['filter_status' => 'all', 'risk_level' => 'green']) }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card h-100">
                                            <h5>ความเสี่ยงปกติ</h5>
                                            <h3>{{ $green_count }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span></h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-user-md" style="color: #28a745;"></i> <!-- ปกติ -->
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-6 col-md-4 col-lg-2 mb-3">
                                    <a href="{{ route('er.today', ['filter_status' => 'all', 'risk_level' => 'yellow']) }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card h-100">
                                            <h5>เร่งด่วน</h5>
                                            <h3>{{ $yellow_count }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span></h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-ambulance" style="color: #f39c12;"></i>
                                                <!-- เร่งด่วน -->
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-6 col-md-4 col-lg-2 mb-3">
                                    <a href="{{ route('er.today', ['filter_status' => 'all', 'risk_level' => 'red']) }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card h-100">
                                            <h5>วิกฤติ </h5>
                                            <h3>{{ $red_count }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span></h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                                                <!-- ฉุกเฉิน -->
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-6 col-md-4 col-lg-2 mb-3">
                                    <a href="{{ url('/er/diagnosis-stats?status=all&date_filter=today&start_date=' . now()->format('Y-m-d') . '&end_date=' . now()->format('Y-m-d')) }}"
                                        class="text-decoration-none text-dark">
                                        <div class="card shadow-sm custom-card h-100">
                                            <h5>รักษาเสร็จสิ้น</h5>
                                            <h3>{{ $completed_in_er_count }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span></h3>
                                            <div class="custom-card-icon">
                                                <i class="fas fa-check-circle" style="color:rgb(54, 158, 6);"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>


                                <!-- แถวที่สอง -->







                            </div>
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="card p-3 shadow-sm"
                                        style="background-color: #f8f9fa; border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="fw-bold">
                                                ผู้ป่วยERวันนี้
                                                <span class="text-primary fw-bold">{{ $appointment_today_count }}
                                                    คน</span>
                                            </h5>
                                            <a href="{{ url('/er/form') }}" class="btn btn-info btn-sm">
                                                ดูทั้งหมด
                                            </a>
                                        </div>
                                    </div>

                                    <div class="table-wrapper">
                                        <table id="appointmentTable" class="table table-striped table-bordered mt-3">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>เลขบัตรประชาชน</th>
                                                    <th>ชื่อ - นามสกุล</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>ผลัด</th>
                                                    <th>เวลา</th>
                                                    <th>สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($appointments as $appointment)
                                                    <tr>
                                                        <td>{{ $appointment->soldier_id_card }}</td>
                                                        <td>{{ $appointment->first_name }} {{ $appointment->last_name }}
                                                        </td>
                                                        <td>{{ $appointment->training_unit_name ?? '-' }}</td>
                                                        <td>{{ $appointment->rotation_name ?? '-' }}</td>
                                                        <td>
                                                            <strong>เวลา:</strong>
                                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                            น.<br>
                                                            <strong>สถานที่:</strong>
                                                            {{ $appointment->appointment_location }}
                                                        </td>
                                                        <td>
                                                            <!-- สถานะการเสี่ยง -->
                                                            @if($appointment->risk_level == 'red')
                                                                <span class="badge custom-badge bg-white shadow">🔴
                                                                    ความเสี่ยงวิกฤติ</span>
                                                            @elseif($appointment->risk_level == 'yellow')
                                                                <span class="badge custom-badge bg-white shadow">🟡
                                                                    ความเสี่ยงเร่งด่วน</span>
                                                            @else
                                                                <span class="badge custom-badge bg-white shadow">🟢
                                                                    ความเสี่ยงปกติ</span>
                                                            @endif

                                                            <!-- สถานะการรักษา -->
                                                            @if($appointment->treatment_status == 'not-treated')
                                                                <span class="badge custom-badge bg-white shadow">🟡
                                                                    ยังไม่ได้เข้ารับการรักษา</span>
                                                            @elseif($appointment->treatment_status == 'treated')
                                                                <span class="badge custom-badge bg-white shadow">🟢
                                                                    รักษาเสร็จสิ้น</span>
                                                            @else
                                                                <button class="btn btn-outline-secondary shadow-sm">
                                                                    <span class="badge-circle bg-light"></span> สถานะไม่ระบุ
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">ไม่มีนัดหมายในวันนี้</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            @include('themes.script')

</body>

</html>