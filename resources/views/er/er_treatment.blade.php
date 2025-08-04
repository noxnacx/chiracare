<!DOCTYPE html>
<html lang="th">
@include('themes.head')




<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')
        <!-- Main Sidebar Container -->
        @include('themes.er.menuer')
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="container">
                    <div class="container mt-4">


                        <div class="container">
                            <div class="d-flex justify-content-between align-items-center my-4">
                                <h4>รายชื่อผู้ป่วย ER</h4>
                            </div>
                            <div class="table-container bg-white p-4 rounded shadow-sm border">

                                <table class="table table-striped table-bordered data-table">
                                    <thead class="table-dark">

                                        <tr>
                                            <th>เลขบัตรประชาชน</th>
                                            <th>ชื่อทหาร</th>
                                            <th>อาการ</th>
                                            <th>วัน-เวลา & สถานที่นัดหมาย</th>
                                            <th>สถานะการรักษา</th>
                                            <th>วินิฉัยโรค</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->medicalReport->soldier->soldier_id_card ?? '-' }}
                                                </td>
                                                <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                    {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                </td>
                                                <td>
                                                    @if ($appointment->medicalReport)
                                                        <button class="btn btn-info btn-sm btn-detail" data-bs-toggle="modal"
                                                            data-bs-target="#detailModal"
                                                            data-id="{{ $appointment->medicalReport->id }}"
                                                            style="font-size: 14px; padding: 8px 15px;">
                                                            {{ $appointment->medicalReport->symptom_description ?? 'ไม่ระบุอาการ' }}
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled
                                                            style="font-size: 14px; padding: 8px 15px;">
                                                            ไม่มีข้อมูล
                                                        </button>
                                                    @endif
                                                </td>

                                                <td> <strong>วันที่:</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}<br>

                                                    <!-- แสดงเวลา -->
                                                    <strong>เวลา:</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                    น.<br>

                                                    <!-- แสดงสถานที่ -->
                                                    <strong>สถานที่:</strong> {{ $appointment->appointment_location }}<br>

                                                    <!-- แสดง ER Badge -->
                                                </td>
                                                <td>
                                                    @if (!is_null($appointment->checkin) && $appointment->checkin->checkin_status === 'checked-in')
                                                        <span
                                                            class="badge checked-in shadow bg-light text-dark">🟡กำลังเข้ารับการรักษา</span>
                                                    @else
                                                        <span class="badge not-checked-in shadow bg-light text-dark">
                                                            🟠ยังไม่ได้เข้ารับการรักษา</span>
                                                    @endif

                                                </td>
                                                <td>
                                                    @if(isset($appointment->checkin->treatment))
                                                        <a href="{{ route('er_diagnosis.page', ['treatmentId' => $appointment->checkin->treatment->id]) }}"
                                                            class="btn btn-primary">
                                                            กรอกข้อมูลวินิจฉัย
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">ไม่พบข้อมูล</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>

                                <!-- แสดงผลกรณีไม่มีข้อมูล -->
                                @if($appointments->isEmpty())
                                    <div class="alert alert-danger text-center">
                                        ไม่พบข้อมูลผู้ป่วยที่มีสถานะ "in ER"
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
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


                        <!-- Add risk level display -->


                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

</body>

<style>
    body {
        background-color: #f4f7fc;
        color: #333;
    }



    h4 {
        color: rgb(0, 0, 0);
        font-size: 28px;
        font-weight: bold;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
    }

    .table {
        border: 1px solid #ddd;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ddd;
    }


    .btn {
        font-size: 14px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-warning {
        background-color: #ffc107;
        color: white;
        border: none;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .alert {
        margin-top: 20px;
        font-size: 1.2rem;
        color: rgb(255, 255, 255);
    }

    .alert-success {
        color: #28a745;
    }

    /* ปรับสไตล์สำหรับปุ่ม "มาแล้ว" */
    .btn-custom-checked-in {
        background-color: white;
        /* สีพื้นหลังเป็นสีขาว */

        /* กรอบปุ่มสีเขียว */
        color: #28a745;
        /* ตัวหนังสือเป็นสีเขียว */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้ปุ่ม */
        font-size: 14px;
        /* ขนาดตัวอักษร */
        padding: 8px 15px;
        /* เพิ่มขนาดปุ่ม */
        transition: all 0.3s ease;
        /* เพิ่มการเปลี่ยนแปลงเมื่อ Hover */
    }

    .btn-custom-checked-in:hover {
        background-color: #28a745;
        /* เปลี่ยนสีพื้นหลังเมื่อ Hover */
        color: white;
        /* ตัวหนังสือเป็นสีขาว */
    }

    /* ปรับสไตล์สำหรับปุ่ม "ยังไม่ได้เช็คอิน" */
    .btn-custom-not-checked-in {
        background-color: white;
        /* สีพื้นหลังเป็นสีขาว */
        border: 1px solid #ffc107;
        /* กรอบปุ่มสีเหลือง */
        color: rgb(0, 0, 0);
        /* ตัวหนังสือเป็นสีเหลือง */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้ปุ่ม */
        font-size: 14px;
        /* ขนาดตัวอักษร */
        padding: 8px 15px;
        /* เพิ่มขนาดปุ่ม */
        transition: all 0.3s ease;
        /* เพิ่มการเปลี่ยนแปลงเมื่อ Hover */
    }

    .btn-custom-not-checked-in:hover {
        background-color: rgb(0, 0, 0);
        /* เปลี่ยนสีพื้นหลังเมื่อ Hover */
        color: white;
        /* ตัวหนังสือเป็นสีขาว */
    }

    /* ปรับสไตล์สำหรับปุ่ม "ยังไม่ได้รักษา" */
    /* ปรับสไตล์สำหรับปุ่ม "ยังไม่ได้รักษา" */
    .btn-custom-not-treated {
        background-color: white;
        /* สีพื้นหลังเป็นสีขาว */
        border: 1px solidrgb(255, 255, 255);
        /* กรอบปุ่มเป็นสีเหลือง */
        color: #ffc107;
        /* ตัวหนังสือเป็นสีเหลือง */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        /* เพิ่มเงาให้ปุ่ม */
        font-size: 14px;
        /* ขนาดตัวอักษร */
        padding: 8px 15px;
        /* เพิ่มขนาดปุ่ม */
        transition: all 0.3s ease;
        /* เพิ่มการเปลี่ยนแปลงเมื่อ Hover */
    }

    /* ปรับเงาและพื้นหลังเมื่อ hover */
    .btn-custom-not-treated:hover {
        background-color: #ffc107;
        /* เปลี่ยนสีพื้นหลังเมื่อ Hover */
        color: black;
        /* ตัวหนังสือเป็นสีขาว */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        /* เพิ่มเงาเข้มขึ้นเมื่อ hover */
    }
</style>

</script>
@include('themes.script')

</html>


<script>
    $(document).ready(function () {
        // เมื่อคลิกปุ่มดูรายละเอียด
        $('.btn-detail').click(function () {
            var reportId = $(this).data('id');

            // ส่ง AJAX request เพื่อขอข้อมูล
            $.ajax({
                url: '/medical-report/' + reportId,
                method: 'GET',
                success: function (response) {
                    // เติมข้อมูลลงใน Modal
                    $('#soldierName').text(response.soldier.first_name + ' ' + response.soldier.last_name);
                    $('#soldierUnit').text(response.soldier.affiliated_unit || '-');
                    $('#soldierRotation').text(response.soldier.rotation.rotation_name || '-');
                    $('#soldierTraining').text(response.soldier.trainingUnit.unit_name || '-');

                    // เติมข้อมูลสัญญาณชีพ
                    $('#soldierTemp').text(response.vital_signs.temperature ? response.vital_signs.temperature + ' °C' : '-');
                    $('#soldierBP').text(response.vital_signs.blood_pressure || '-');
                    $('#soldierHeartRate').text(response.vital_signs.heart_rate ? response.vital_signs.heart_rate + ' bpm' : '-');
                    $('#soldierPain').text(response.vital_signs.pain_score ? response.vital_signs.pain_score + '/10' : '-');

                    // เติมข้อมูลอาการและระดับความเสี่ยง
                    $('#soldierSymptom').text(response.symptom_description || 'ไม่ระบุอาการ');

                    // แสดงระดับความเสี่ยง
                    var riskLevel = response.vital_signs.risk_level;
                    var riskText = '';
                    var riskClass = '';

                    if (riskLevel === 'red') {
                        riskText = 'วิกฤติ (สีแดง)';
                        riskClass = 'badge bg-danger';
                    } else if (riskLevel === 'yellow') {
                        riskText = 'เร่งด่วน (สีเหลือง)';
                        riskClass = 'badge bg-warning';
                    } else if (riskLevel === 'green') {
                        riskText = 'ปกติ (สีเขียว)';
                        riskClass = 'badge bg-success';
                    } else {
                        riskText = 'ไม่ระบุ';
                        riskClass = 'badge bg-secondary';
                    }

                    $('#soldierRiskLevel').html('<span class="' + riskClass + '">' + riskText + '</span>');

                    // แสดง Modal
                    $('#detailModal').modal('show');
                },
                error: function () {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถโหลดข้อมูลได้',
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>