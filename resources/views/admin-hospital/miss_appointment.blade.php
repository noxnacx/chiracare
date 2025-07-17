<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin-hospital.navbarhospital')
        @include('themes.admin-hospital.menuhospital')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <h2 class="mb-4">📌 นัดหมายที่พลาด</h2>
                        <form id="appointmentForm" method="POST" action="{{ route('appointments.update-missed') }}">
                            @csrf

                            <table id="appointmentmissTable" class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="select_all"></th>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาการ</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missedAppointments as $appointment)
                                        <tr>
                                            <td><input type="checkbox" class="select_item" name="medical_report_ids[]"
                                                    value="{{ $appointment->medical_report_id }}">
                                            </td>
                                            <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                            </td>
                                            <td>{{ $appointment->medicalReport->soldier->affiliated_unit ?? '-' }}</td>
                                            <td>{{ $appointment->medicalReport->soldier->rotation->rotation_name ?? '-' }}
                                            </td>
                                            <td><button type="button"
                                                    class="btn btn-info btn-sm btn-detail">เพิ่มเติม</button></td>
                                            <td><span class="badge bg-danger">พลาดนัด</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">วันที่นัดใหม่</label>
                                <input type="datetime-local" id="appointment_date" name="appointment_date"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_location" class="form-label">สถานที่</label>
                                <select id="appointment_location" name="appointment_location" class="form-control"
                                    required>
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

                            <button type="submit" class="btn btn-success">นัดหมายใหม่</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('appointmentForm').addEventListener('submit', function (event) {
        event.preventDefault();



        let selected = Array.from(document.querySelectorAll('input[name="medical_report_ids[]"]:checked'))
            .map(checkbox => checkbox.value);

        // ถ้าไม่มีการเลือกเช็คบ็อกซ์ ให้แน่ใจว่า selected เป็นอาเรย์ว่าง
        if (!Array.isArray(selected)) {
            selected = [];
        }



        // ตรวจสอบว่ามีการเลือก checkbox หรือไม่
        if (selected.length === 0) {
            Swal.fire("❌ กรุณาเลือกผู้ป่วยที่ต้องการนัดหมาย", "", "warning");
            return;
        }

        let csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            console.error("CSRF token not found!");
            Swal.fire("❌ พบข้อผิดพลาด", "CSRF token ไม่พบในหน้าเว็บ", "error");
            return;
        }

        // สร้าง formData
        let formData = new FormData(this);
        formData.append('appointment_ids', selected); // เพิ่ม array ของ appointment_ids เข้าไปใน formData

        let routeUrl = "{{ route('appointments.update-missed') }}";

        fetch(routeUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfMeta.getAttribute("content"),
                "Accept": "application/json"
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire("✅ นัดหมายสำเร็จ!", "ข้อมูลถูกบันทึกเรียบร้อยแล้ว", "success")
                        .then(() => location.reload());
                } else {
                    Swal.fire("❌ เกิดข้อผิดพลาด", data.message || "โปรดตรวจสอบข้อมูล", "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire("❌ ข้อผิดพลาดเซิร์ฟเวอร์", "กรุณาลองใหม่ หรือแจ้งผู้ดูแลระบบ", "error");
            });
    });

</script>

</html>