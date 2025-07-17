<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<!-- เพิ่มในส่วน head หรือก่อนปิด body -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- Main Sidebar Container -->
        @include('themes.training.menutraining')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <h2 id="statusTitle" class="fw-bold">ตารางการนัดหมาย</h2>
                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                            <!-- ส่วน HTML สำหรับปุ่มกรอง -->
                            <div class="mb-3">

                                <!-- ตัวเลือกสถานะ -->
                                <select id="statusFilter" class="form-select d-inline-block w-auto">
                                    <option value="pending">🔴 ยังไม่ได้ส่งป่วย</option>
                                    <option value="sent">🟡 ยังไม่ได้ทำการนัดหมาย</option>
                                    <option value="scheduled" selected>🟢 นัดหมายสำเร็จ</option>
                                </select>

                                <!-- ตัวเลือกกรองประเภทเคส -->
                                <!-- ตัวเลือกกรองประเภทเคส -->
                                <select id="caseTypeFilter" class="form-select d-inline-block w-auto ms-3"
                                    style="display: none;">
                                    <option value="all" selected>ทั้งหมด</option>
                                    <option value="normal">เคสปกติ</option>
                                    <option value="critical">เคสฉุกเฉิน</option>
                                </select>

                                <!-- ตัวเลือกกรองวันที่ -->
                                <div id="dateFilterContainer" class="d-inline-block ms-3" style="display: none;">
                                    <div class="input-group date" style="width: 180px; display: inline-flex;">
                                        <input type="text" class="form-control datepicker" id="appointmentDatePicker"
                                            placeholder="เลือกวันที่">
                                        <span class="input-group-append">
                                            <span class="input-group-text bg-white d-block">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </span>
                                    </div>

                                </div>



                            </div>

                        </div>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif



                        <div class="table-container bg-white p-4 rounded shadow-sm border">
                            <table class="table table-striped table-bordered data-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;"><input type="checkbox" id="selectAll"></th>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาการ</th>
                                        <th>สถานะ</th>
                                        @if(
                                            $medicalReports->contains(function ($report) {
                                                return $report->appointment && $report->appointment->status === 'scheduled';
                                            })
                                        )
                                                                                    <th class="appointment-column">ข้อมูลนัดหมาย</th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($medicalReports as $report)
                                        <tr class="report-row" data-status="{{ $report->status }}"
                                            data-appointment-status="{{ $report->appointment->status ?? '' }}"
                                            data-case-type="{{ $report->appointment->case_type ?? '' }}"
                                            data-appointment-date="{{ $report->appointment ? \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') : '' }}">

                                            <td>
                                                @if ($report->status === 'pending')
                                                    <input type="checkbox" class="selectRow" data-id="{{ $report->id }}">
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $report->soldier->first_name }}
                                                {{ $report->soldier->last_name }}
                                            </td>
                                            <td>{{ $report->soldier->affiliated_unit }}</td>
                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td><button class="btn btn-info btn-sm btn-detail"
                                                    data-id="{{ $report->id }}">เพิ่มเติม</button></td>
                                            <td>
                                                @if ($report->status === 'pending')
                                                    <span class="status-badge shadow"><i
                                                            class="fas fa-circle text-danger"></i><span
                                                            class="text-dark fw-bold">ยังไม่ได้ส่งป่วย</span></span>
                                                @elseif ($report->status === 'sent')
                                                    <span class="status-badge shadow"><i
                                                            class="fas fa-circle text-warning"></i><span
                                                            class="text-dark fw-bold">ยังไม่ได้ทำการนัดหมาย</span></span>
                                                @elseif ($report->appointment && $report->appointment->status === 'scheduled')
                                                    <span class="status-badge shadow"><i
                                                            class="fas fa-circle text-success"></i><span
                                                            class="text-dark fw-bold">นัดหมายสำเร็จ</span></span>

                                                @endif
                                            </td>
                                            <td class="appointment-column">
                                                @if ($report->appointment && $report->appointment->status === 'scheduled')
                                                    <strong>วัน:</strong>
                                                    {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') }}<br>
                                                    <strong>เวลา:</strong>
                                                    {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('H:i') }}<br>
                                                    <strong>สถานที่:</strong>
                                                    {{ $report->appointment->appointment_location }}<br>

                                                    <!-- เช็คค่า is_follow_up -->
                                                    @if($report->appointment->is_follow_up == 1)
                                                        <strong>หมายเหตุ:</strong> นัดติดตามอาการ
                                                    @else
                                                        <strong>หมายเหตุ:</strong> -
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>





                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-success px-4 py-2 shadow-sm" id="sendToHospital">
                                ส่งข้อมูลไปโรงพยาบาล
                            </button>

                            <a href="{{ route('medicalReport.create', ['id']) }}"
                                class="btn btn-primary px-4 py-2 shadow-sm">
                                เพิ่มการส่งป่วย
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold">
                                รายละเอียดผู้ป่วย
                            </h5>
                        </div>

                        <div class="modal-body">
                            <div class="container">
                                <h3><strong style="color: #4CAF50;">พลฯ</strong>
                                    <span id="soldierName"></span>
                                </h3>
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

                                <h5>ระดับความเสี่ยง</h5>
                                <p id="soldierRiskLevel"></p>

                                <h5 class="mt-4">อาการ</h5>
                                <p id="soldierSymptom"></p>



                                <h5 class="mt-4">ผลตรวจ ATK</h5>
                                <div id="atkImages" class="image-container"></div>

                                <h5 class="mt-4">รูปอาการ</h5>
                                <div id="symptomImages" class="image-container"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ปิด</button>
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
                }

                .info-box:hover {
                    background-color: #f8f9fa;
                }

                .image-container {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 1px;
                    justify-content: flex-start;
                    align-items: flex-start;
                }

                .image-wrapper {
                    width: 120px;
                    height: 120px;
                    border-radius: 8px;
                    overflow: hidden;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #f8f9fa;
                    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
                }

                .image-wrapper img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 8px;
                }

                .status-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 3px;
                    padding: 6px 10px;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    background-color: #f8f9fa;
                    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.08);
                    font-size: 14px;
                    font-weight: 500;
                    color: #333;
                }

                .status-badge i {
                    font-size: 16px;
                }

                #statusFilter {
                    font-size: 14px;
                    padding: 6px 10px;
                    border-radius: 6px;
                    border: 1px solid #ccc;
                    background-color: #fff;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                }

                #statusFilter:hover {
                    border-color: #007bff;
                }

                #statusFilter:focus {
                    border-color: #0056b3;
                    outline: none;
                    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
                }

                #soldierRiskLevel {
                    display: inline-flex;
                    align-items: center;
                    padding: 5px 8px;
                    border-radius: 15px;
                    font-size: 1rem;
                    font-weight: bold;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                #soldierRiskLevel.critical {
                    background-color: #ff4c4c;
                    color: white;
                }

                #soldierRiskLevel.warning {
                    background-color: #ffcc00;
                    color: white;
                }

                #soldierRiskLevel.normal {
                    background-color: #4CAF50;
                    color: white;
                }

                /* ซ่อนคอลัมน์ข้อมูลนัดหมายทั้งหมด */
                /* ซ่อนหัวตารางและคอลัมน์โดย default */
                .appointment-header,
                .appointment-column {
                    display: none;
                }

                /* แสดงเฉพาะเมื่อมีแถว scheduled ในตาราง */
                table:has(.report-row[data-appointment-status="scheduled"]) .appointment-header,
                .report-row[data-appointment-status="scheduled"] .appointment-column {
                    display: table-cell;
                }

                /* สไตล์สำหรับ datepicker */
                .datepicker {
                    z-index: 9999 !important;
                }

                .datepicker-dropdown {
                    border-radius: 0.5rem;
                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                }

                .datepicker table tr td.active.active,
                .datepicker table tr td.active:hover {
                    background-color: #0d6efd;
                }

                .input-group.date {
                    width: auto;
                    display: inline-flex;
                }

                /* ปรับขนาดและระยะห่างของปุ่มกรอง */
                #dateFilterContainer {
                    vertical-align: middle;
                }

                #appointmentDatePicker {
                    width: 120px;
                    cursor: pointer;
                }

                #clearDateFilter {
                    height: 38px;
                }

                /* ซ่อนตัวกรองทั้งสองโดย default */
                #caseTypeFilter,
                #dateFilterContainer {
                    display: none !important;
                }

                /* แสดงเฉพาะเมื่ออยู่ในสถานะ scheduled */
                body.show-scheduled-filters #caseTypeFilter,
                body.show-scheduled-filters #dateFilterContainer {
                    display: inline-block !important;
                }


                #caseTypeFilter {
                    padding: 10px;
                    border-radius: 8px;
                    background-color: #f8f9fa;
                    border: 1px solid #ced4da;
                    font-size: 14px;
                    font-weight: 500;
                    color: #495057;
                    transition: all 0.3s ease;

                }

                #caseTypeFilter:focus {
                    border-color: #007bff;
                    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
                    outline: none;
                }

                #caseTypeFilter option {
                    padding: 10px;
                    font-size: 14px;
                    background-color: #ffffff;
                }

                #caseTypeFilter option:hover {
                    background-color: #f1f1f1;
                }

                #caseTypeFilter option:selected {
                    background-color: #007bff;
                    color: #ffffff;
                }
            </style>


        </div>
    </div>

    @include('themes.script')

    <script>
        $(document).ready(function () {
            // Select all checkbox
            $('#selectAll').change(function () {
                $('.selectRow').prop('checked', $(this).prop('checked'));
            });

            // Filter rows based on status

            // Filter rows based on status
            // Filter rows based on status
            function filterRows(status) {
                $(".report-row").each(function () {
                    const rowStatus = $(this).data("status");
                    const appointmentStatus = $(this).data("appointment-status"); // ตรวจสอบสถานะ appointment

                    // กรองตามสถานะ medical report และ appointment
                    if (status === 'scheduled') {
                        $(this).toggle(appointmentStatus === 'scheduled');
                    } else {
                        $(this).toggle(rowStatus === status);
                    }
                });
            }




            // Status filter change
            $("#statusFilter").change(function () {
                const status = $(this).val();
                filterRows(status);

                // Update title based on status
                let title = "รายการยังไม่ได้ส่งป่วย";
                const titleMap = {
                    "sent": "รายการยังไม่ได้รับการนัดหมาย",
                    "scheduled": "รายการนัดหมายสำเร็จ",
                    "all": "รายการทั้งหมด"
                };

                $("#statusTitle").text(titleMap[status] || title);
            });

            // อ่านค่า status จาก URL แล้วใช้เป็น default filter
            const urlParams = new URLSearchParams(window.location.search);
            const defaultStatus = urlParams.get('status') || 'pending';
            const defaultCaseType = urlParams.get('case_type') || 'all';
            const defaultDate = urlParams.get('date') || '';

            // ตั้งค่าเริ่มต้น
            $("#statusFilter").val(defaultStatus).trigger("change");

            // ถ้าเป็น scheduled ให้รอเล็กน้อยแล้วตั้งค่าตัวกรองเพิ่มเติม
            if (defaultStatus === 'scheduled') {
                setTimeout(() => {
                    $("#caseTypeFilter").val(defaultCaseType).trigger("change");
                    $("#appointmentDatePicker").val(defaultDate).datepicker('update').trigger("change");
                }, 200); // ให้เวลา JS แสดงฟิลด์ก่อนค่อยใส่ค่า
            }






            $('.btn-detail').on('click', function () {
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
                        if (data.success) {
                            // Update soldier info
                            $('#soldierName').text(data.soldier_name);
                            $('#soldierUnit').text(data.soldier_unit);
                            $('#soldierRotation').text(data.soldier_rotation);
                            $('#soldierTraining').text(data.soldier_training);

                            // Update vital signs
                            $('#soldierTemp').text(data.temperature + "°C");
                            $('#soldierBP').text(data.blood_pressure);
                            $('#soldierHeartRate').text(data.heart_rate + " BPM");
                            $('#soldierPain').text(data.pain_score + "/10");
                            $('#soldierSymptom').text(data.symptom_description);

                            // Update risk level
                            const riskLevel = data.risk_level;
                            let riskLevelDisplay = '';
                            const riskElement = $('#soldierRiskLevel');

                            if (riskLevel === 'critical') {
                                riskLevelDisplay = '🔴 ฉุกเฉิน';
                                riskElement.removeClass('warning normal').addClass('critical');
                            } else if (riskLevel === 'warning') {
                                riskLevelDisplay = '🟡 เฝ้าระวัง';
                                riskElement.removeClass('critical normal').addClass('warning');
                            } else {
                                riskLevelDisplay = '🟢 ปกติ';
                                riskElement.removeClass('critical warning').addClass('normal');
                            }
                            riskElement.text(riskLevelDisplay);

                            // Update appointment info
                            if (data.appointment) {
                                $('#appointmentDate').text(data.appointment.date || '-');
                                $('#appointmentLocation').text(data.appointment.location || '-');
                                $('#appointmentCaseType').text(data.appointment.case_type || '-');
                            } else {
                                $('#appointmentDate').text('-');
                                $('#appointmentLocation').text('-');
                                $('#appointmentCaseType').text('-');
                            }

                            // Load images
                            function loadImages(imagesArray, containerId) {
                                const container = $(`#${containerId}`);
                                container.empty();

                                if (imagesArray.length === 0) {
                                    container.html('<p class="text-muted">ไม่มีรูปภาพ</p>');
                                    return;
                                }

                                imagesArray.forEach(image => {
                                    const imageDiv = $(`
                        <div class="col-md-4 mb-2">
                            <div class="image-wrapper">
                                <img src="${image}" class="img-fluid" alt="รูป">
                            </div>
                        </div>
                    `);
                                    container.append(imageDiv);
                                });
                            }

                            loadImages(data.images.atk, 'atkImages');
                            loadImages(data.images.symptom, 'symptomImages');

                            // Show modal
                            $('#detailModal').modal('show');
                        } else {
                            Swal.fire("เกิดข้อผิดพลาด", data.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถโหลดข้อมูลได้", "error");
                    }
                });
            });


            // Send to hospital button
            $("#sendToHospital").click(function () {
                const selectedIds = $(".selectRow:checked").map(function () {
                    return $(this).data("id");
                }).get();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        title: "กรุณาเลือกข้อมูล",
                        text: "คุณต้องเลือกทหารอย่างน้อย 1 คนก่อนดำเนินการ",
                        icon: "warning",
                        confirmButtonText: "ตกลง",
                        confirmButtonColor: "#007bff",
                    });
                    return;
                }

                Swal.fire({
                    title: 'ยืนยันการส่งข้อมูล?',
                    html: `คุณกำลังจะส่งข้อมูลผู้ป่วย <strong>${selectedIds.length}</strong> คนไปยังโรงพยาบาล`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "ส่งข้อมูล",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#dc3545",
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "{{ route('medical.updateStatus') }}",
                            type: "POST",
                            dataType: "json",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: selectedIds,
                                status: "sent"
                            }
                        }).then(response => {
                            if (response.status !== "success") {
                                throw new Error(response.message || 'เกิดข้อผิดพลาด');
                            }
                            return response;
                        }).catch(error => {
                            Swal.showValidationMessage(
                                `ขออภัย: ${error.statusText || error.message}`
                            );
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "ดำเนินการสำเร็จ!",
                            text: "ส่งข้อมูลผู้ป่วยไปยังโรงพยาบาลเรียบร้อย",
                            icon: "success",
                            timer: 2000,
                            timerProgressBar: true,
                            willClose: () => {
                                location.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // ตั้งค่า Datepicker
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                language: 'th',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom',
            });

            // ฟังก์ชันแสดง/ซ่อนตัวกรองตามสถานะ
            function toggleFilters() {
                const status = $("#statusFilter").val();

                // ตรวจสอบว่าเป็นสถานะ "scheduled" หรือไม่
                if (status === 'scheduled') {
                    $('body').addClass('show-scheduled-filters'); // แสดงตัวกรอง
                    $('#appointmentDatePicker').datepicker('setDate', null); // รีเซ็ตค่า date picker เมื่อแสดงตัวกรอง
                } else {
                    $('body').removeClass('show-scheduled-filters'); // ซ่อนตัวกรอง
                    $("#appointmentDatePicker").val('').datepicker('update'); // ล้างค่ากรองวันที่
                    $("#caseTypeFilter").val('all'); // รีเซ็ตกรองประเภทเคส
                }
            }

            // เมื่อเปลี่ยนสถานะ
            $("#statusFilter").change(function () {
                const selectedStatus = $(this).val();
                toggleFilters(); // เรียกใช้ฟังก์ชันแสดง/ซ่อนตัวกรอง
                filterRows(selectedStatus); // กรองแถวตามสถานะ
            });

            // เมื่อเลือกวันที่
            $("#appointmentDatePicker").change(function () {
                if ($("#statusFilter").val() === 'scheduled') {
                    filterByDate($(this).val()); // กรองแถวตามวันที่
                }
            });

            // เมื่อเลือกประเภทเคส
            $("#caseTypeFilter").change(function () {
                if ($("#statusFilter").val() === 'scheduled') {
                    filterByCaseType($(this).val()); // กรองแถวตามประเภทเคส
                }
            });

            // ฟังก์ชันกรองตามวันที่ (เฉพาะ scheduled)
            // ฟังก์ชันกรองตามวันที่ (เฉพาะ scheduled)
            function filterByDate(date) {
                if (!date) return;

                // แปลงวันที่ที่เลือกจาก datepicker ให้เป็นวันที่ในรูปแบบ dd/mm/yyyy
                const dateParts = date.split('/');
                const selectedDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

                $(".report-row[data-appointment-status='scheduled']").each(function () {
                    const appointmentDateText = $(this).data("appointment-date");

                    // แปลงวันที่ใน data-appointment-date ให้อยู่ในรูปแบบ Date object
                    const dateMatch = appointmentDateText.match(/(\d{2})\/(\d{2})\/(\d{4})/);

                    if (dateMatch) {
                        const rowDate = new Date(dateMatch[3], dateMatch[2] - 1, dateMatch[1]);
                        // เปรียบเทียบวันที่และแสดงหรือซ่อนแถวตามการกรอง
                        $(this).toggle(rowDate.getTime() === selectedDate.getTime());
                    }
                });
            }


            // ฟังก์ชันกรองตามประเภทเคส (เฉพาะ scheduled)
            function filterByCaseType(caseType) {
                $(".report-row[data-appointment-status='scheduled']").each(function () {
                    const rowCaseType = $(this).data("case-type");
                    $(this).toggle(caseType === 'all' || rowCaseType === caseType);
                });
            }

            // ฟังก์ชันกรองแถวตามสถานะ
            function filterRows(status) {
                $(".report-row").each(function () {
                    const rowStatus = $(this).data("status");
                    const appointmentStatus = $(this).data("appointment-status");

                    if (status === 'scheduled') {
                        $(this).toggle(appointmentStatus === 'scheduled');
                    } else {
                        $(this).toggle(rowStatus === status);
                    }
                });
            }

            // เรียกใช้ครั้งแรกเมื่อโหลดหน้า
            toggleFilters();
        });

    </script>

</body>

</html>