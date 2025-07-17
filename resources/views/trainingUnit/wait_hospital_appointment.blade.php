<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- Main Sidebar Container -->
        @include('themes.admin.menuadmin')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">


                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <h2 class="fw-bold">รายชื่อทหาร</h2>
                            <h5 class="text-secondary">วันที่ {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}
                            </h5>
                        </div>

                        <!-- ✅ ตัวกรองสถานะ -->
                        <div class="mb-3">
                            <label for="statusFilter" class="fw-bold">กรองตามสถานะ:</label>
                            <select id="statusFilter" class="form-select w-auto d-inline-block">
                                <option value="all">ทั้งหมด</option>
                                <option value="pending">ยังไม่ได้นัดหมาย</option>
                                <option value="sent">รอนัดหมายจาก รพ.</option>
                            </select>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <!-- ✅ ตารางรวม -->
                        <div class="table-container bg-white p-4 rounded shadow-sm border">
                            <table id="medicalTable" class="table table-bordered text-center">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาการ</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalReports as $report)
                                        <tr class="report-row" data-status="{{ $report->status }}">
                                            <td class="fw-bold">{{ $report->soldier->first_name }}
                                                {{ $report->soldier->last_name }}</td>
                                            <td>{{ $report->soldier->affiliated_unit }}</td>
                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-detail" data-id="{{ $report->id }}"
                                                    data-name="{{ $report->soldier->first_name }} {{ $report->soldier->last_name }}"
                                                    data-unit="{{ $report->soldier->affiliated_unit }}"
                                                    data-rotation="{{ $report->soldier->rotation->rotation_name ?? '-' }}"
                                                    data-training="{{ $report->soldier->training_unit }}"
                                                    data-symptom="{{ $report->symptom_description ?? 'ไม่มีข้อมูล' }}"
                                                    data-temp="{{ optional($report->vitalSign)->temperature ?? 'ไม่มีข้อมูล' }}"
                                                    data-bp="{{ optional($report->vitalSign)->blood_pressure ?? 'ไม่มีข้อมูล' }}"
                                                    data-heart-rate="{{ optional($report->vitalSign)->heart_rate ?? 'ไม่มีข้อมูล' }}"
                                                    data-pain="{{ $report->pain_score ?? 'ไม่มีข้อมูล' }}"
                                                    data-toggle="modal" data-target="#detailModal">
                                                    เพิ่มเติม
                                                </button>
                                            </td>
                                            <td>
                                                @if ($report->status === 'pending')
                                                    <span class="badge bg-danger text-white px-3 py-2">ยังไม่ได้นัดหมาย</span>
                                                @elseif ($report->status === 'sent')
                                                    <span class="badge bg-warning text-white px-3 py-2">รอนัดหมายจาก รพ.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>





                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>


    <!-- Modal แสดงรายละเอียดทหาร -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-md"></i> รายละเอียดผู้ป่วย
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="container">
                        <h3><strong>พลฯ</strong> <span id="soldierName"></span></h3>
                        <p><strong>หน่วยต้นสังกัด:</strong> <span id="soldierUnit"></span> |
                            <strong>ผลัด:</strong> <span id="soldierRotation"></span> |
                            <strong>หน่วยฝึก:</strong> <span id="soldierTraining"></span>
                        </p>

                        <!-- ✅ เพิ่มกรอบรอบ Vital Signs -->
                        <!-- Modal แสดงรายละเอียดทหาร -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small>อุณหภูมิ</small>
                                    <h5 id="soldierTemp">31.0°C</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small>ความดันโลหิต</small>
                                    <h5 id="soldierBP">90/120 mmHg</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small>อัตราการเต้นของหัวใจ</small>
                                    <h5 id="soldierHeartRate">140 BPM</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <small>ระดับความเจ็บปวด</small>
                                    <h5 id="soldierPain">1/10</h5>
                                </div>
                            </div>
                        </div>

                        <!-- ✅ เพิ่มแสดง Risk Level -->
                        <h5 class="mt-4">ระดับความเสี่ยง (Risk Level)</h5>
                        <p id="soldierRiskLevel" class="fw-bold text-danger"></p>

                        <!-- อาการ -->
                        <h5 class="mt-4">อาการ</h5>
                        <p id="soldierSymptom"></p>

                        <!-- รูปอาการ -->
                        <h5 class="mt-4">รูปอาการ</h5>
                        <img id="soldierImage" src="https://via.placeholder.com/400"
                            class="img-fluid rounded border shadow-sm" alt="รูปอาการ">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
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
    </style>


    @include('themes.script')


    <script>
        $(document).ready(function () {
            $('.btn-detail').on('click', function () {
                $('#soldierName').text($(this).data('name'));
                $('#soldierUnit').text($(this).data('unit'));
                $('#soldierRotation').text($(this).data('rotation'));
                $('#soldierTraining').text($(this).data('training'));
                $('#soldierTemp').text($(this).data('temp') + "°C");
                $('#soldierBP').text($(this).data('bp') + " mmHg");
                $('#soldierHeartRate').text($(this).data('heart-rate') + " BPM");
                $('#soldierPain').text($(this).data('pain') + "/10");
                $('#soldierSymptom').text($(this).data('symptom'));

                // ✅ ดึงค่า Risk Level
                let riskLevel = $(this).data('risk-level');
                console.log("Risk Level:", riskLevel);  // 🔴 ตรวจสอบค่า risk_level ใน Console

                $('#soldierRiskLevel').text(riskLevel);

                // ✅ เปลี่ยนสีข้อความตามระดับความเสี่ยง
                if (riskLevel === 'red') {
                    $('#soldierRiskLevel').addClass('text-danger').removeClass('text-warning text-success');
                } else if (riskLevel === 'yellow') {
                    $('#soldierRiskLevel').addClass('text-warning').removeClass('text-danger text-success');
                } else {
                    $('#soldierRiskLevel').addClass('text-success').removeClass('text-danger text-warning');
                }

                let imageUrl = $(this).data('symptom-image');
                $('#soldierImage').attr('src', imageUrl ? imageUrl : 'https://via.placeholder.com/400');

                $('#detailModal').modal('show');
            });
        });


    </script>



</body>

</html>