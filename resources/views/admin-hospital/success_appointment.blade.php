<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')

        @include('themes.admin-hospital.menuhospital')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="row">
                            <div class="d-flex justify-content-between w-100">
                                <!-- Title Section -->
                                <h2 class="mb-4 ">
                                    รายการนัดหมายสำเร็จ
                                </h2>

                                <!-- ตัวกรองสถานะ -->
                                <form method="GET" action="{{ route('appointments.success') }}" class="mb-4">
                                    <div class="d-flex align-items-center">

                                        <select id="statusFilter" name="status" class="form-control d-inline"
                                            onchange="this.form.submit()">
                                            <option value="rescheduled" {{ $selectedStatus == 'rescheduled' ? 'selected' : '' }}>
                                                🟠ขอคำร้องเลื่อนนัดหมาย
                                            </option>
                                            <option value="scheduled" {{ $selectedStatus == 'scheduled' ? 'selected' : '' }}>
                                                🟢นัดหมายสำเร็จ
                                            </option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>



                        <div class="table-container bg-white p-4 rounded shadow-sm border">
                            <table id="medicalTable" class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <!-- เพิ่ม Checkbox เลือกทั้งหมด -->
                                        <th>ชื่อทหาร</th>
                                        <th>ผลัด</th> <!-- ผลัด -->
                                        <th>อาการ</th> <!-- อาการ -->
                                        <th>สถานะ</th>
                                        <th>ข้อมูลนัดหมาย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        @if ($appointment->status == 'rescheduled' || $appointment->status == 'scheduled')
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="selectRow" data-id="{{ $appointment->id }}" />
                                                </td>
                                                <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                    {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                </td>
                                                <td>{{ $appointment->medicalReport->soldier->rotation->rotation_name ?? '-' }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-info btn-sm btn-detail"
                                                        data-id="{{ $appointment->id }}">เพิ่มเติม</button>
                                                </td>
                                                <td>
                                                    <span class=" badge white-shadow-badge">

                                                        @if($appointment->status == 'scheduled')
                                                            🟢นัดหมายสำเร็จ
                                                        @elseif($appointment->status == 'rescheduled')
                                                            🟠ขอเลื่อนนัดหมาย
                                                        @else
                                                            {{ ucfirst($appointment->status) }}
                                                        @endif
                                                    </span>

                                                </td>
                                                <td>
                                                    <strong>วัน:</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}
                                                    <br>
                                                    <strong>เวลา:</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                    <br>
                                                    <strong>สถานที่:</strong> {{ $appointment->appointment_location }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <!-- ปุ่มนัดหมายใหม่ที่จะเปิดโมเดล -->
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#rescheduleModal">
                                นัดหมายใหม่
                            </button>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal: เพิ่มการนัดหมายใหม่ -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="rescheduleModalLabel">
                        <i class="fas fa-calendar-check"></i> อัปเดตการนัดหมาย
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('appointments.rescheduleToScheduled') }}"
                        id="updateAppointmentsForm">
                        @csrf
                        <input type="hidden" id="appointment_id" name="appointment_id">
                        <input type="hidden" id="soldier_id" name="soldier_id" value="{{ old('soldier_id') }}">

                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">วันที่นัด</label>
                            <input type="datetime-local" id="appointment_date" name="appointment_date"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="appointment_location" class="form-label">สถานที่</label>
                            <select id="appointment_location" name="appointment_location" class="form-control" required>
                                <option value="OPD">OPD</option>
                                <option value="ER">ER</option>
                                <option value="IPD">IPD</option>
                                <option value="ARI clinic">ARI clinic</option>
                                <option value="กองพันทหารราบ">กองพันทหารราบ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="case_type" class="form-label">ประเภทเคส</label>
                            <select id="case_type" name="case_type" class="form-control" required>
                                <option value="normal">ปกติ</option>
                                <option value="critical">ฉุกเฉิน</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">อัปเดตการนัดหมาย</button>
                    </form>

                </div>
            </div>
        </div>

    </div>
    <style>
        .white-shadow-badge {
            background-color: white;
            /* สีพื้นหลังเป็นสีขาว */
            color: black;
            /* เปลี่ยนสีข้อความให้เป็นสีดำ */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* เพิ่มเงา */
            padding: 5px 10px;
            /* เพิ่มระยะห่างภายใน */
            border-radius: 5px;
            /* ทำให้มุมโค้ง */
        }
    </style>
    <!-- Bootstrap JS (Popper.js is required for Bootstrap's dropdowns, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#updateAppointmentsForm').on('submit', function (e) {
                e.preventDefault(); // ยกเลิกการส่งฟอร์ม

                // เก็บ ID ของการนัดหมายที่ถูกเลือก
                let selectedIds = [];
                $(".selectRow:checked").each(function () {
                    selectedIds.push($(this).data('id')); // เพิ่ม `data-id` ไปใน selectedIds
                });

                console.log("Selected IDs:", selectedIds); // แสดง ID ที่เลือกในคอนโซล

                if (selectedIds.length === 0) {
                    alert("กรุณาเลือกทหารที่ต้องการอัปเดต");
                    return;
                }

                // สร้างข้อมูลฟอร์ม
                let formData = {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'appointment_ids': selectedIds,
                    'appointment_date': $('#appointment_date').val(),
                    'appointment_location': $('#appointment_location').val(),
                    'case_type': $('#case_type').val(),
                };

                console.log("Form data being sent:", formData); // ตรวจสอบข้อมูลก่อนส่ง

                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData, // ส่งข้อมูลเป็น Object
                    success: function (response) {
                        console.log("Response received:", response); // ดูผลลัพธ์ที่ได้รับจากเซิร์ฟเวอร์
                        if (response.status === 'success') {
                            alert("อัปเดตการนัดหมายสำเร็จ");
                            location.reload(); // รีเฟรชหน้า
                        } else {
                            alert("เกิดข้อผิดพลาด: " + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error); // แสดงข้อผิดพลาดในคอนโซล
                        alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                    }
                });
            });



            // Ensure modal works when the "Schedule New" button is clicked
            $('#rescheduleModal').on('show.bs.modal', function (e) {
                console.log("Modal opened for new appointment rescheduling."); // Debugging log
            });
        });




    </script>
    @include('themes.script') <!-- รวมสคริปต์ส่วนท้าย -->

</body>

</html>