<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
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
                                    <label for="statusFilter"
                                        class="form-label text-muted mb-1 fw-semibold">สถานะ:</label>
                                    <select id="statusFilter" class="form-select form-select-sm"
                                        style="min-width: 200px; border-radius: 8px;">
                                        <option value="sent" selected>🟡 ยังไม่ได้นัดหมาย</option>
                                        <option value="scheduled">🟢 นัดหมายสำเร็จ</option>
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
                                <div class="d-flex align-items-end">
                                    <a href="{{ route('appointments.downloadAll', request()->query()) }}"
                                        class="btn btn-danger btn-sm px-3" style="height: 32px; border-radius: 8px;"
                                        target="_blank">
                                        ดาวน์โหลด PDF
                                    </a>
                                </div>
                            </div>

                        </div>





                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-container bg-white p-4 rounded shadow-sm border" id="sentTable">
                            <table class="table table-striped table-bordered data-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"> เลือกทั้งหมด</th>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึก</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาการ</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalReports as $report)
                                        @if ($report->status !== 'sent')
                                            @continue
                                        @endif
                                        <tr class="report-row" data-status="sent">

                                            <td><input type="checkbox" class="selectRow" data-id="{{ $report->id }}"></td>
                                            <td class="fw-bold">{{ $report->soldier->first_name }}
                                                {{ $report->soldier->last_name }}
                                            </td>
                                            <td>{{ $report->soldier->trainingUnit->unit_name ?? 'ไม่ระบุ' }}</td>
                                            <!-- แสดงหน่วยฝึก -->

                                            <td>{{ $report->soldier->affiliated_unit }}</td>
                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-detail"
                                                    data-id="{{ $report->id }}">เพิ่มเติม</button>
                                            </td>
                                            <td><span class="status-label sent">
                                                    <span class="dot dot-yellow"></span>
                                                    ยังไม่ได้ทำการนัดหมาย
                                                </span>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-container bg-white p-4 rounded shadow-sm border" id="scheduledTable"
                            style="display: none;">
                            <table class="table table-striped table-bordered data-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึก</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาการ</th>
                                        <th>ข้อมูลนัดหมาย</th>
                                        <th>สถานะ</th>
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
                                            <td>{{ $report->soldier->trainingUnit->unit_name ?? 'ไม่ระบุ' }}</td>
                                            </td> <!-- แสดงหน่วยฝึก -->

                                            <td>{{ $report->soldier->affiliated_unit }}</td>
                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-info btn-sm btn-detail"
                                                    data-id="{{ $report->id }}">เพิ่มเติม</button>

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
                                                @if($report->appointment->is_follow_up == 1)
                                                    นัดติดตามอาการ
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>
                                                <span class="status-label scheduled">
                                                    <span class="dot dot-green"></span>
                                                    นัดหมายสำเร็จ
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- ปุ่มเปิด Modal นัดหมาย -->
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success px-4 py-2 shadow-sm mt-3"
                                id="scheduleAppointment">นัดหมาย</button>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"> สร้างการนัดหมาย</h5>

                </div>

                <div class="modal-body">
                    <form id="appointmentForm">
                        <input type="hidden" id="medicalReportIds" name="medical_report_ids">

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
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-success" id="applyFilter">ยืนยัน</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // ✅ เก็บค่าพารามิเตอร์จาก URL
        const getQueryParam = (param) => new URLSearchParams(window.location.search).get(param);
        const statusFromUrl = getQueryParam('status');
        if (statusFromUrl) $('#statusFilter').val(statusFromUrl);
        updateStatusUI($('#statusFilter').val());

        // ✅ เมื่อเปลี่ยนสถานะ -> reload หน้าใหม่
        $('#statusFilter').change(function () {
            const newStatus = $(this).val();
            const params = new URLSearchParams(window.location.search);
            params.set('status', newStatus);
            window.location.href = window.location.pathname + '?' + params.toString();
        });

        // ✅ เปลี่ยน UI ตาม status ที่เลือก
        function updateStatusUI(status) {
            const titleMap = {
                "sent": "รายการยังไม่ได้ทำการนัดหมาย",
                "scheduled": "รายการที่นัดหมายสำเร็จ"
            };
            $("#statusTitle").text(titleMap[status] || "รายการยังไม่ได้ทำการนัดหมาย");

            if (status === 'scheduled') {
                $("#filtersContainer").show();
                $("#sentTable").hide();
                $("#scheduledTable").show();
                $("#scheduleAppointment").hide();
            } else {
                $("#filtersContainer").hide();
                $("#sentTable").show();
                $("#scheduledTable").hide();
                $("#scheduleAppointment").show();
            }
        }

        // ✅ เปิด Modal นัดหมายเมื่อเลือกทหาร
        $("#scheduleAppointment").click(function () {
            let selectedIds = $(".selectRow:checked").map(function () {
                return $(this).data("id");
            }).get();

            // ลบค่าซ้ำออก
            selectedIds = [...new Set(selectedIds)];

            if (selectedIds.length === 0) {
                Swal.fire("กรุณาเลือกทหารก่อนนัดหมาย", "", "warning");
                return;
            }

            $("#medicalReportIds").val(selectedIds.join(","));
            new bootstrap.Modal(document.getElementById('appointmentModal')).show();
        });

        // ✅ ส่งข้อมูลนัดหมาย
        $("#confirmAppointment").click(function () {
            const $btn = $(this);
            $btn.prop('disabled', true); // 🔒 ป้องกันการคลิกซ้ำ

            let selectedIds = $(".selectRow:checked").map(function () {
                return $(this).data("id");
            }).get();

            selectedIds = [...new Set(selectedIds)];

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
                        // ✅ เปลี่ยนหน้าด้วย status=scheduled
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

        // ✅ เปิด Modal ตัวกรอง
        $('#openFilterModal').click(function () {
            const status = $('#statusFilter').val();
            $('#dateFilterGroup').toggleClass('d-none', status !== 'scheduled');
            $('#caseTypeFilterGroup').toggleClass('d-none', status !== 'scheduled');
            new bootstrap.Modal(document.getElementById('filterModal')).show();
        });

        // ✅ ยืนยันการกรอง
        $('#applyFilter').click(function () {
            const status = $('#statusFilter').val();
            const date = $('#dateFilterModal').val();
            const caseType = $('#caseTypeFilterModal').val();
            const rotation = $('#rotationFilterModal').val();
            const unit = $('#trainingUnitFilterModal').val();

            let url = window.location.pathname + '?status=' + status;
            if (status === 'scheduled') {
                url += '&date=' + date + '&case_type=' + caseType;
            }
            url += '&rotation_id=' + rotation + '&training_unit_id=' + unit;

            window.location.href = url;
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

                    $('#soldierName').text(data.soldier_name);
                    $('#soldierUnit').text(data.soldier_unit);
                    $('#soldierRotation').text(data.soldier_rotation);
                    $('#soldierTraining').text(data.soldier_training);
                    $('#soldierTemp').text(data.temperature + "°C");
                    $('#soldierBP').text(data.blood_pressure);
                    $('#soldierHeartRate').text(data.heart_rate + " BPM");
                    $('#soldierPain').text(data.pain_score + "/10");
                    $('#soldierSymptom').text(data.symptom_description);

                    const riskElement = $('#soldierRiskLevel');
                    const riskMap = {
                        critical: '🔴 ฉุกเฉิน',
                        warning: '🟡 เฝ้าระวัง',
                        normal: '🟢 ปกติ'
                    };
                    riskElement.text(riskMap[data.risk_level] || '-');

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

                    $('#detailModal').modal('show');
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
    $(document).ready(function () {
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        const statusFromUrl = getQueryParam('status');
        if (statusFromUrl) {
            $('#statusFilter').val(statusFromUrl);
        }

        const currentStatus = $('#statusFilter').val();
        updateStatusUI(currentStatus);

        // ✅ เปลี่ยนตรงนี้ ให้ reload หน้า
        $('#statusFilter').change(function () {
            const newStatus = $(this).val();
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('status', newStatus);

            const newUrl = window.location.pathname + '?' + currentParams.toString();
            window.location.href = newUrl;
        });

        function updateStatusUI(status) {
            let title = "รายการยังไม่ได้ทำการนัดหมาย";
            const titleMap = {
                "sent": "รายการยังไม่ได้ทำการนัดหมาย",
                "scheduled": "รายการที่นัดหมายสำเร็จ"
            };

            if (titleMap[status]) {
                $("#statusTitle").text(titleMap[status]);
            } else {
                $("#statusTitle").text(title);
            }

            if (status === 'scheduled') {
                $("#filtersContainer").show();
                $("#sentTable").hide();
                $("#scheduledTable").show();
                $("#scheduleAppointment").hide();
            } else if (status === 'sent') {
                $("#filtersContainer").hide();
                $("#sentTable").show();
                $("#scheduledTable").hide();
                $("#scheduleAppointment").show();
            }

            if (status === 'scheduled') {
                filterScheduledRows?.();
            }
        }
    });

</script>








<script>
    $(document).ready(function () {
        const filterModal = new bootstrap.Modal(document.getElementById('filterModal'));

        $('#openFilterModal').click(function () {
            const status = $('#statusFilter').val();

            if (status === 'scheduled') {
                $('#dateFilterGroup').removeClass('d-none');
                $('#caseTypeFilterGroup').removeClass('d-none');
            } else {
                $('#dateFilterGroup').addClass('d-none');
                $('#caseTypeFilterGroup').addClass('d-none');
            }

            filterModal.show();
        });

        $('#applyFilter').click(function () {
            const status = $('#statusFilter').val();
            const date = $('#dateFilterModal').val();
            const caseType = $('#caseTypeFilterModal').val();
            const rotation = $('#rotationFilterModal').val();
            const unit = $('#trainingUnitFilterModal').val();

            let url = window.location.pathname + '?status=' + status;
            if (status === 'scheduled') {
                url += '&date=' + date + '&case_type=' + caseType;
            }
            url += '&rotation_id=' + rotation + '&training_unit_id=' + unit;

            // ✅ ปิด Modal ก่อน redirect
            filterModal.hide();

            window.location.href = url;
        });
    });

</script>


<script>
    // ฟังเหตุการณ์ตอน modal ปิด
    $('#filterModal').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open'); // ลบ class ที่ล็อค scroll
        $('.modal-backdrop').remove(); // ลบฉากหลังที่ค้างอยู่
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
            // ตรวจสอบว่า checkbox "เลือกทั้งหมด" ถูกเลือกหรือไม่
            var isChecked = $(this).prop('checked');

            // เลือกหรือไม่เลือกทุก checkbox ในตาราง
            $('.selectRow').prop('checked', isChecked);
        });
    });

</script>