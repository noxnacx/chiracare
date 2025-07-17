<!DOCTYPE html>
<html lang="en">
@include('themes.head') <!-- รวมส่วน head -->

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
                        <div class="flex-row mb-4">
                            <h2>
                                รายการนัดหมายสำเร็จ
                            </h2>

                            <!-- ฟอร์มกรองสถานะ -->
                            <form method="GET" action="{{ route('appointments.scheduledByUnit') }}"
                                class="form-container">
                                <label for="statusFilter" class="fw-bold">

                                </label>
                                <select id="statusFilter" name="status" class="form-control w-auto ms-2"
                                    onchange="this.form.submit()">
                                    <option value="scheduled" {{ $selectedStatus == 'scheduled' ? 'selected' : '' }}>
                                        🟢นัดหมายสำเร็จ
                                    </option>
                                    <option value="rescheduled" {{ $selectedStatus == 'rescheduled' ? 'selected' : '' }}>
                                        🟠ขอคำร้องเลื่อนนัดหมาย
                                    </option>
                                </select>
                            </form>
                        </div>

                        <div class="table-container bg-white p-4 rounded shadow-sm border">

                            <table id="medicalTable" class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <!-- เพิ่ม Checkbox เลือกทั้งหมด -->
                                        <th>ชื่อทหาร</th>
                                        <th>หน่วยฝึกต้นสังกัด</th> <!-- หน่วยฝึกต้นสังกัด -->
                                        <th>ผลัด</th> <!-- ผลัด -->
                                        <th>อาการ</th> <!-- อาการ -->
                                        <th>สถานะ</th>
                                        <th>ข้อมูลนัดหมาย</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($appointments as $appointment)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="selectRow" data-id="{{ $appointment->id }}" />
                                            </td>
                                            <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                            </td>
                                            <td>{{ $appointment->medicalReport->soldier->trainingUnit->name ?? '-' }}</td>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- ปุ่มส่งไปเปลี่ยนสถานะ -->
                        <div class="d-flex justify-content-end w-100 mt-3">
                            <button class="btn btn-warning px-4 py-2 shadow-sm" id="sendToRescheduled">
                                ส่งเพื่อเลื่อนนัดหมาย
                            </button>
                        </div>


                    </div>
                </div><!-- /.container-fluid -->
            </div>
        </div>
    </div>
    @include('themes.script')

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

        .flex-row {
            display: flex;
            justify-content: space-between;
            /* จัดการให้ฟอร์มอยู่ชิดซ้ายและข้อความอยู่ชิดขวา */
            align-items: center;
            /* จัดให้อยู่ตรงกลางในแนวตั้ง */
        }

        .form-container {
            display: flex;
            align-items: center;
        }
    </style>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $('#sendToRescheduled').on('click', function () {
            let selectedIds = [];
            $(".selectRow:checked").each(function () {
                selectedIds.push($(this).data("id"));
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    title: "แจ้งเตือน",
                    text: "กรุณาเลือกทหารอย่างน้อย 1 คน",
                    icon: "warning",
                    confirmButtonText: "ตกลง",
                    confirmButtonColor: "#007bff",
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการเลื่อนนัดหมาย?',
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "ตกลง",
                cancelButtonText: "ยกเลิก",
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#dc3545"
            }).then((result) => {

                $.ajax({
                    url: "{{ route('appointments.rescheduleStatus') }}", // Updated route
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        appointment_ids: selectedIds, // Ensure the appointment IDs are being sent
                        appointment_date: $('#appointment_date').val(),
                        appointment_location: $('#appointment_location').val(),
                        case_type: $('#case_type').val(),
                    },
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            });
                            location.reload();  // Refresh the page
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Unable to connect to the server", "error");
                    }
                });

            });
        });
    </script>

</body>

</html>