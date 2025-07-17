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
            <div class="content-header">
                <div class="container-fluid">

                    <div class="container mt-5">
                        <h4>รายชื่อผู้ป่วย ER</h4>
                        <div class="table-container bg-white p-4 rounded shadow-sm border">

                            <table id="medicalTable" class="table table-striped table-bordered">
                                <thead class="table-dark">

                                    <tr>
                                        <th>เลขบัตรประชาชน</th>
                                        <th>ชื่อทหาร</th>
                                        <th>อาการ</th>
                                        <th>วัน-เวลา & สถานที่นัดหมาย</th>
                                        <th>สถานะเช็คอิน</th>
                                        <th>สถานะการรักษา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($treatments as $treatment)
                                        <tr>
                                            <td>{{ $treatment->checkin->appointment->medicalReport->soldier->soldier_id_card }}
                                            </td>
                                            <td>{{ $treatment->checkin->appointment->medicalReport->soldier->first_name }}
                                                {{ $treatment->checkin->appointment->medicalReport->soldier->last_name }}
                                            </td>
                                            <td>{{ $treatment->checkin->appointment->medicalReport->symptom_description }}
                                            </td>
                                            <td>{{ $treatment->checkin->appointment->appointment_date }}<br>
                                                <span class="badge bg-danger">ER</span>
                                            </td>
                                            <td>
                                                @if($treatment->checkin->checkin_status == 'checked-in')
                                                    <button class="btn btn-custom-checked-in">🟢 มาแล้ว</button>
                                                @else
                                                    <button class="btn btn-custom-not-checked-in">🟠 ยังไม่ได้เช็คอิน</button>
                                                @endif

                                            </td>
                                            <td>
                                                @if ($treatment->checkin && $treatment->checkin->treatment)
                                                    @if ($treatment->checkin->treatment->treatment_status === 'treated')
                                                        <span>🟢 รักษาแล้ว</span>
                                                    @else
                                                        <button class="btn btn-custom-not-treated btn-sm"
                                                            onclick="openDiagnosisModal({{ $treatment->checkin->treatment->id }}, {{ $treatment->checkin->appointment->medicalReport->vital_signs_id ?? '0' }})">
                                                            🟠 ยังไม่ได้รักษา
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">ไม่พบข้อมูล</span>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- แสดงผลกรณีไม่มีข้อมูล -->
                            @if($treatments->isEmpty())
                                <div class="alert alert-danger text-center">
                                    ไม่พบข้อมูลผู้ป่วยที่มีสถานะ "in ER"
                                </div>
                            @endif
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="treatmentModal" tabindex="-1" aria-labelledby="diagnosisModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="treatmentModalLabel">กรอกข้อมูลวินิจฉัย</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="diagnosisForm">
                                            <input type="hidden" id="treatmentId">

                                            <div class="mb-3">
                                                <label for="doctorName" class="form-label">ชื่อแพทย์:</label>
                                                <input type="text" class="form-control" id="doctorName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="temperature" class="form-label">อุณหภูมิ (°C):</label>
                                                <input type="number" step="0.1" class="form-control" id="temperature">
                                            </div>
                                            <div class="mb-3">
                                                <label for="bloodPressure" class="form-label">ความดันโลหิต:</label>
                                                <input type="text" class="form-control" id="bloodPressure">
                                            </div>
                                            <div class="mb-3">
                                                <label for="heartRate" class="form-label">อัตราการเต้นของหัวใจ:</label>
                                                <input type="number" class="form-control" id="heartRate">
                                            </div>
                                            <div class="mb-3">
                                                <label for="icd10Code" class="form-label">รหัสโรค (ICD10):</label>
                                                <input type="text" class="form-control" id="icd10Code" name="icd10_code"
                                                    placeholder="กรอกรหัสโรค , " oninput="fetchDiseaseInfo(this.value)"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="diseaseDescription" class="form-label">คำอธิบายโรค:</label>
                                                <input type="text" class="form-control" id="diseaseDescription"
                                                    readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="treatmentStatus" class="form-label">สถานะการรักษา:</label>
                                                <select class="form-select" id="treatmentStatus" required>
                                                    <option value="">-- เลือกสถานะ --</option>
                                                    <option value="Admit">Admit (รับไว้รักษา)</option>
                                                    <option value="Refer">Refer (ส่งต่อ)</option>
                                                    <option value="Discharge">Discharge (จำหน่ายออก)</option>
                                                    <option value="Follow-up">Follow-up (ติดตามอาการ)</option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </div>
</body>

<style>
    body {
        background-color: #f4f7fc;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    h4 {
        color: #2b9b6a;
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 30px;
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

<script>
    async function updateTreatmentStatus(treatmentId) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        try {
            // ส่งคำขอเพื่ออัปเดตสถานะการรักษาเป็น "รักษาแล้ว"
            let response = await fetch(`/treatments/${treatmentId}/update-status`, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    treatment_id: treatmentId,
                    treatment_status: "treated"  // เปลี่ยนสถานะเป็น 'treated'
                })
            });

            let data = await response.json();

            if (response.ok) {
                Swal.fire("สำเร็จ!", "สถานะการรักษาถูกอัปเดตแล้ว", "success")
                    .then(() => location.reload()); // หรือทำการอัปเดต UI ตรงจุดนี้แทนการโหลดใหม่ทั้งหน้า
            } else {
                Swal.fire("เกิดข้อผิดพลาด", data.message, "error");
            }

        } catch (error) {
            Swal.fire("❌ ข้อผิดพลาดเซิร์ฟเวอร์", "กรุณาลองใหม่ หรือแจ้งผู้ดูแลระบบ", "error");
        }
    }

</script>
<script>
    async function openDiagnosisModal(treatmentId) {
        console.log("เปิด Modal สำหรับ Treatment ID:", treatmentId);
        document.getElementById('treatmentId').value = treatmentId;

        // ส่งคำขอไปยัง API ที่จะดึงข้อมูล vital_signs_id จาก treatment_id
        const response = await fetch(`/api/vital-signs/from-treatment/${treatmentId}`);
        const data = await response.json();

        if (response.ok) {
            // หากดึงข้อมูลสำเร็จให้แสดงข้อมูลในฟอร์ม
            document.getElementById('temperature').value = data.temperature || '';
            document.getElementById('bloodPressure').value = data.blood_pressure || '';
            document.getElementById('heartRate').value = data.heart_rate || '';
        } else {
            console.error("ไม่พบข้อมูล Vital Signs");
            Swal.fire("❌ ข้อผิดพลาด", "ไม่พบข้อมูล Vital Signs หรือเกิดข้อผิดพลาดในการดึงข้อมูล", "error");
        }

        // เปิด Modal ด้วยตัวเลือก backdrop static
        var modal = new bootstrap.Modal(document.getElementById('treatmentModal'), {
            backdrop: 'static',  // ป้องกันการปิดด้วยการคลิกนอก Modal
            keyboard: false      // ป้องกันการปิดด้วยปุ่ม Escape
        });

        modal.show();  // แสดง Modal
    }

</script>
<script>
    document.getElementById('diagnosisForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        let treatmentId = document.getElementById('treatmentId').value;
        let doctorName = document.getElementById('doctorName').value;
        let temperature = document.getElementById('temperature').value;
        let bloodPressure = document.getElementById('bloodPressure').value;
        let heartRate = document.getElementById('heartRate').value;
        let icd10Code = document.getElementById('icd10Code').value; // รับค่ารหัสโรคหลายตัว
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        let treatmentStatus = document.getElementById('treatmentStatus').value;


        // ตรวจสอบค่ารหัสโรค
        if (!icd10Code) {
            Swal.fire("❌ ข้อผิดพลาด", "กรุณากรอกรหัสโรค (ICD10)", "error");
            return;
        }

        // แยกรหัสโรคที่ผู้ใช้กรอก (โดยใช้จุลภาค)
        let codesArray = icd10Code.split(',');

        try {
            let response = await fetch("/treatment/add-diagnosis", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    treatment_id: treatmentId,
                    doctor_name: doctorName,
                    temperature,
                    blood_pressure: bloodPressure,
                    heart_rate: heartRate,
                    icd10_code: icd10Code,
                    treatment_status: treatmentStatus   // ส่งรหัสโรคหลายตัว
                })
            });

            let data = await response.json();

            if (response.ok) {
                // เรียกใช้ฟังก์ชันเพื่ออัปเดตสถานะการรักษา
                const statusUpdated = await updateTreatmentStatus(treatmentId);

                if (statusUpdated) {
                    Swal.fire("สำเร็จ!", "บันทึกข้อมูลและอัปเดตสถานะการรักษาเรียบร้อยแล้ว", "success")
                        .then(() => location.reload());
                } else {
                    Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถอัปเดตสถานะการรักษาได้", "error");
                }
            } else {
                Swal.fire("เกิดข้อผิดพลาด", data.message || "ไม่ทราบข้อผิดพลาด", "error");
            }
        } catch (error) {
            Swal.fire("❌ ข้อผิดพลาดเซิร์ฟเวอร์", `รายละเอียด: ${error.message || 'ไม่ทราบข้อผิดพลาด'}`, "error");
        }
    });

    async function fetchDiseaseInfo(codes) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const codeArray = codes.split(',');  // แยกรหัสโรคจากคอมมา (,)

        let diseaseDescriptions = [];  // ตัวแปรสำหรับเก็บคำอธิบายโรค

        try {
            // ส่งคำขอไปยัง API พร้อมกันทั้งหมดโดยใช้ Promise.all
            const requests = codeArray.map(async (code) => {
                const response = await fetch(`/diseases/${code.trim()}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    console.log(`API Response for ${code.trim()}:`, data);  // ตรวจสอบคำตอบจาก API

                    // ตรวจสอบว่า data.diseases[0] มีข้อมูลหรือไม่
                    if (data.diseases && data.diseases.length > 0) {
                        return data.diseases[0].disease_name || `ไม่พบชื่อโรคสำหรับรหัส: ${code.trim()}`;  // ปรับให้ดึงชื่อโรคจาก field ที่ถูกต้อง
                    } else {
                        return `ไม่พบคำอธิบายสำหรับรหัสโรค: ${code.trim()}`;
                    }
                } else {
                    return `ไม่พบข้อมูลโรคสำหรับรหัส: ${code.trim()}`;
                }
            });

            // รอคำขอทั้งหมดให้เสร็จและเก็บผลลัพธ์ใน diseaseDescriptions
            diseaseDescriptions = await Promise.all(requests);

            console.log('Disease Descriptions:', diseaseDescriptions);  // ตรวจสอบค่า diseaseDescriptions

            // แสดงคำอธิบายโรคทั้งหมดในฟิลด์
            document.getElementById('diseaseDescription').value = diseaseDescriptions.join(', ');

        } catch (error) {
            console.error('เกิดข้อผิดพลาด: ', error);
            Swal.fire("❌ ข้อผิดพลาด", "ไม่สามารถดึงข้อมูลโรคได้", "error");
        }

        return diseaseDescriptions;
    }


</script>
@include('themes.script')

</html>