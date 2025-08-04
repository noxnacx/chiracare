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
</style>


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.er.navbarer')

        @include('themes.ipd.menuipd')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">


                            <div class="container">
                                <div class="container">
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-3 mb-3 flex-wrap gap-2">
                                        <h2 class="fw-bold mb-0" style="color: #2c3e50;">
                                            แดชบอร์ด IPD
                                        </h2>


                                        <a href="/ipd/admit" class="btn btn-success">
                                            วินิจฉัยโรค
                                        </a>

                                    </div>
                                    <div class="row mt-2">
                                        <!-- แถวแรก -->

                                        <div class="col-md-3">
                                            <a href="/ipd/diagnosis-stats" class="text-decoration-none text-dark">
                                                <div class="card shadow-sm custom-card">
                                                    <h5>Admit <span
                                                            style="font-size: 16px; font-weight: normal;">ยอดสะสมรายวัน</span>
                                                    </h5>
                                                    <h3>{{ $admitToday }}
                                                        <span style="font-size: 16px; font-weight: normal;"> คน</span>
                                                    </h3>
                                                    <div class="custom-card-icon">
                                                        <i class="fas fa-bed" style="color: #10b981;"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="col-md-3">
                                            <a href="/ipd/diagnosis-stats" class="text-decoration-none text-dark">
                                                <div class="card shadow-sm custom-card">
                                                    <h5>Admit <span
                                                            style="font-size: 16px; font-weight: normal;">ยอดทั้งหมด</span>
                                                    </h5>
                                                    <h3>{{ $totalAdmitIpd }}
                                                        <span style="font-size: 16px; font-weight: normal;"> คน</span>
                                                    </h3>
                                                    <div class="custom-card-icon">
                                                        <i class="fas fa-users" style="color: #10b981;"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>


                                        <div class="col-md-3">
                                            <a href="/ipd/diagnosis-stats" class="text-decoration-none text-dark">
                                                <div class="card shadow-sm custom-card">
                                                    <h5>จำหน่ายออก <span
                                                            style="font-size: 16px; font-weight: normal;">วันนี้</span>
                                                    </h5>
                                                    <h3>
                                                        {{ $dischargeToday }}<span
                                                            style="font-size: 16px; font-weight: normal;"> คน</span>
                                                    </h3>
                                                    <div class="custom-card-icon">
                                                        <i class="fas fa-sign-out-alt" style="color: #ff9800;"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>




                                    </div>


                                    <!-- แถวที่สอง -->







                                </div>
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="card p-3 shadow-sm"
                                            style="background-color: #f8f9fa; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold">
                                                    รายชื่อผู้ป่วย ADMIT
                                                    <span class="text-primary fw-bold">
                                                        {{ $admitTotal }} คน</span>
                                                </h5>
                                                <a href="/ipd/patient-details" class="btn btn-info btn-sm">
                                                    ดูทั้งหมด
                                                </a>
                                            </div>
                                        </div>

                                        <div class="table-wrapper">
                                            <table id="appointmentTable"
                                                class="table table-striped table-bordered mt-3">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>ชื่อ - นามสกุล</th>
                                                        <th>เลข 13 หลัก</th>
                                                        <th>ผลัด</th>
                                                        <th>หน่วยฝึก</th>
                                                        <th>หน่วยต้นสังกัด</th>
                                                        <th>การวินิคฉัย</th>
                                                        <th>วันที่ Admit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($latestAdmitPatients as $patient)
                                                        <tr>
                                                            <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                                            <td>{{ $patient->soldier_id_card }}</td>
                                                            <td>{{ $patient->rotation_name }}</td>
                                                            <td>{{ $patient->training_unit_name }}</td>
                                                            <td>{{ $patient->affiliated_unit }}</td>
                                                            <td>
                                                                @php
                                                                    $codes = explode(',', $patient->icd10_codes);
                                                                    $names = explode(',', $patient->disease_names);

                                                                @endphp
                                                                <ul class="pl-3 mb-0">
                                                                    @foreach($codes as $index => $code)
                                                                        {{ trim($code) }}: {{ $names[$index] ?? '-' }}
                                                                        <br>
                                                                    @endforeach

                                                                </ul>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $dt = \Carbon\Carbon::parse($patient->diagnosis_date);
                                                                @endphp
                                                                วันที่ {{ $dt->format('d/m/Y') }}<br>เวลา
                                                                {{ $dt->format('H:i') }}
                                                            </td>

                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-danger">
                                                                ไม่พบข้อมูลผู้ป่วย</td>
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
            </div>
        </div>
    </div>
    </div>


    <button id="filterButton" class="btn btn-primary">กรองข้อมูล</button>

    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 400px;"> <!-- เพิ่ม max-width: 600px -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">กรอกข้อมูลกรอง</h5>

                </div>
                <div class="modal-body">
                    <form method="GET" action="{{ url()->current() }}" class="d-flex flex-column">
                        <!-- ฟอร์มกรอง -->



                        <!-- ฟอร์มกรองสถานะการเช็กอิน / สถานะการรักษา -->
                        <div class="form-group mb-3">
                            <label for="filterStatus">สถานะการเช็กอิน / สถานะการรักษา</label>
                            <select name="filter_status" id="filterStatus" class="form-control">
                                <!-- เปลี่ยน class จาก form-select เป็น form-control -->
                                <option value="all" {{ request('filter_status') == 'all' ? 'selected' : '' }}>ทั้งหมด
                                </option>
                                <option value="checked-in" {{ request('filter_status') == 'checked-in' ? 'selected' : '' }}>ยังไม่ได้เข้ารับการรักษา</option>
                                <option value="not-checked-in" {{ request('filter_status') == 'not-checked-in' ? 'selected' : '' }}>กำลังเข้ารับการรักษา</option>
                                <option value="treated" {{ request('filter_status') == 'treated' ? 'selected' : '' }}>
                                    รักษาเสร็จสิ้น</option>
                            </select>
                        </div>


                        <!-- ฟอร์มกรอง checkin_status -->
                        <div class="form-group mb-3">
                            <label for="filterRiskLevel" class="form-label">ระดับความเสี่ยง</label>
                            <select name="risk_level" id="filterRiskLevel" class="form-control">
                                <option value="all" {{ request('risk_level') == 'all' ? 'selected' : '' }}>ทั้งหมด
                                </option>
                                <option value="red" {{ request('risk_level') == 'red' ? 'selected' : '' }}>วิกฤติ</option>
                                <option value="yellow" {{ request('risk_level') == 'yellow' ? 'selected' : '' }}>เร่งด่วน
                                </option>
                                <option value="green" {{ request('risk_level') == 'green' ? 'selected' : '' }}>ปกติ
                                </option>
                            </select>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-success">กรอง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @include('themes.script')


</body>

</html>