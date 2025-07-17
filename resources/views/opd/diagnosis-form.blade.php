<!DOCTYPE html>
<html lang="en">
@include('themes.head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')
        @include('themes.opd.menuopd')

        <div class="content-wrapper" style="background-color: #f8f9fa;">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="fw-bold text-primary">
                            กรอกข้อมูลวินิจฉัย
                        </h2>
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div class="container">
                    <!-- แสดงข้อมูลทหาร -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h4 class="mb-0">ข้อมูลทหาร</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>ชื่อ-สกุล:</strong> <span class="text-primary"
                                            id="soldierName">{{ $soldierName }}</span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>หน่วยต้นสังกัด:</strong> <span
                                            id="soldierUnit">{{ $soldierUnit }}</span></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-2"><strong>ผลัด:</strong> <span
                                            id="soldierRotation">{{ $soldierRotation }}</span></p>
                                </div>
                            </div>
                            <p class="mb-0"><strong>หน่วยฝึก:</strong> <span
                                    id="soldierTraining">{{ $soldierTraining }}</span></p>
                        </div>
                    </div>

                    <form id="diagnosisForm" action="{{ route('diagnosis.save') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="treatmentId" name="treatment_id" value="{{ $treatmentId }}">

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h4 class="mb-0">ข้อมูลแพทย์</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="doctorName" class="form-label"><i
                                            class="fas fa-signature mr-2"></i>ชื่อแพทย์:</label>
                                    <input type="text" class="form-control rounded-pill" id="doctorName"
                                        name="doctor_name" required>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h4 class="mb-0">สัญญาณชีพ</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="temperature" class="form-label">อุณหภูมิ (°C):</label>
                                            <input type="number" step="0.1" class="form-control rounded-pill"
                                                id="temperature" name="temperature" value="{{ $temperature }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bloodPressure" class="form-label">ความดันโลหิต:</label>
                                            <input type="text" class="form-control rounded-pill" id="bloodPressure"
                                                value="{{ $bloodPressure }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="heartRate" class="form-label">อัตราการเต้นของหัวใจ:</label>
                                            <input type="number" class="form-control rounded-pill" id="heartRate"
                                                value="{{ $heartRate }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h4 class="mb-0">การวินิจฉัยโรค</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="icd10Code" class="form-label">รหัสโรค
                                        (ICD10):</label>
                                    <input type="text" class="form-control rounded-pill" id="icd10Code"
                                        name="icd10_code" placeholder="กรอกรหัสโรค เช่น J18.9, E11.9"
                                        oninput="fetchDiseaseInfo(this.value)" required>
                                    <small class="form-text text-muted">กรุณากรอกรหัสโรคคั่นด้วยเครื่องหมายจุลภาค (,)
                                        หากมีหลายโรค</small>
                                </div>
                                <div class="form-group">
                                    <label for="diseaseDescription" class="form-label">คำอธิบายโรค:</label>
                                    <input type="text" class="form-control rounded-pill" id="diseaseDescription"
                                        readonly style="background-color: #f8f9fa;">
                                </div>
                                <div class="form-group">
                                    <label for="notes" class="form-label">หมายเหตุ:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="กรอกหมายเหตุเพิ่มเติม..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h4 class="mb-0">สถานะการรักษา</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="treatmentStatus" class="form-label">สถานะการรักษา:</label>
                                    <select class="form-select rounded-pill" id="treatmentStatus" required>
                                        <option value="">-- เลือกสถานะ --</option>
                                        <option value="Admit">Admit (รับไว้รักษา)</option>
                                        <option value="Refer">Refer (ส่งต่อ)</option>
                                        <option value="Discharge">Discharge (จำหน่ายออก)</option>
                                        <option value="Follow-up">Follow-up (ติดตามอาการ)</option>
                                    </select>
                                </div>

                                <!-- ฟิลด์เลือกวันที่นัดหมายใหม่ -->
                                <div class="form-group mt-3" id="followUpDateDiv" style="display: none;">
                                    <label for="followUpDate" class="form-label"><i
                                            class="far fa-calendar-alt mr-2"></i>วันและเวลานัดหมายใหม่:</label>
                                    <input type="datetime-local" class="form-control rounded-pill" id="followUpDate"
                                        name="follow_up_date">
                                </div>

                                <!-- ฟิลด์เลือกสถานที่และประเภทผู้ป่วย -->
                                <div class="row mt-3" id="appointmentLocationDiv" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="appointmentLocation" class="form-label"><i
                                                    class="fas fa-map-marker-alt mr-2"></i>สถานที่:</label>
                                            <select class="form-select rounded-pill" id="appointmentLocation"
                                                name="appointment_location" required>
                                                <option value="OPD">OPD</option>
                                                <option value="ER">ER</option>
                                                <option value="IPD">IPD</option>
                                                <option value="ARI clinic">ARI clinic</option>
                                                <option value="กองพันทหารราบ">กองพันทหารราบ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patientType" class="form-label"><i
                                                    class="fas fa-user-injured mr-2"></i>ประเภทผู้ป่วย:</label>
                                            <select class="form-select rounded-pill" id="patientType"
                                                name="patient_type" required>
                                                <option value="normal">ปกติ</option>
                                                <option value="critical">วิกฤติ</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
                                <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* สไตล์เพิ่มเติม */
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header {
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
            background-color: #fff;
            border-radius: 15px 15px 0 0 !important;
        }

        .form-control,
        .form-select {
            border-radius: 50px !important;
            padding: 10px 20px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        textarea.form-control {
            border-radius: 15px !important;
        }

        .btn-lg {
            padding: 10px 30px;
            font-size: 1.1rem;
            box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .rounded-pill {
            border-radius: 50px !important;
        }

        .text-primary {
            color: #4e73df !important;
        }

        .bg-white {
            background-color: #fff !important;
        }

        body {
            background-color: #f8f9fa;
        }

        .content-wrapper {
            padding: 20px;
        }

        h2,
        h4 {
            color: #4e73df;
        }

        label {
            font-weight: 500;
            color: #495057;
        }
    </style>

</body>

</html>
<script>
    document.getElementById('treatmentStatus').addEventListener('change', function () {
        const treatmentStatus = this.value;
        const followUpDateDiv = document.getElementById('followUpDateDiv');
        const appointmentLocationDiv = document.getElementById('appointmentLocationDiv');
        const patientTypeDiv = document.getElementById('patientTypeDiv');

        if (treatmentStatus === 'Follow-up') {
            // ถ้าเลือก Follow-up ให้แสดงช่องเลือกวันที่
            followUpDateDiv.style.display = 'block';
            appointmentLocationDiv.style.display = 'block'; // แก้ไขการพิมพ์ผิด
            patientTypeDiv.style.display = 'block'; // แก้ไขการพิมพ์ผิด
        } else {
            // ถ้าเลือกสถานะอื่นๆ ให้ซ่อนช่องเลือกวันที่
            followUpDateDiv.style.display = 'none';
            appointmentLocationDiv.style.display = 'none';
            patientTypeDiv.style.display = 'none';
        }
    });
    // ฟังก์ชันอัปเดต VitalSign
    async function updateVitalSign(treatmentId, temperature, bloodPressure, heartRate, csrfToken) {
        return await fetch(`/treatment/update-vital-sign/${treatmentId}`, {
            method: "PUT",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                temperature,
                blood_pressure: bloodPressure,
                heart_rate: heartRate
            })
        });
    }

    // ฟังก์ชันบันทึกข้อมูลการวินิจฉัย
    async function addDiagnosis(treatmentId, doctorName, temperature, bloodPressure, heartRate, icd10Code, treatmentStatus, notes, csrfToken) {
        return await fetch("/treatment/add-diagnosis", {
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
                icd10_code: icd10Code.join(','),
                treatment_status: treatmentStatus,
                notes: notes,
            })
        });
    }
    document.getElementById('diagnosisForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        let treatmentId = document.getElementById('treatmentId').value;
        console.log(`Treatment ID: ${treatmentId}`);
        let doctorName = document.getElementById('doctorName').value;
        let temperature = document.getElementById('temperature').value;
        let bloodPressure = document.getElementById('bloodPressure').value;
        let heartRate = document.getElementById('heartRate').value;
        let icd10Code = document.getElementById('icd10Code').value;// รับค่ารหัสโรคหลายตัว
        let notes = document.getElementById('notes').value;
        console.log('Notes:', notes);
        let followUpDate = document.getElementById('followUpDate').value; // ดึงค่าของวันนัดหมายใหม่
        console.log(followUpDate);
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
            // 1. เรียกฟังก์ชันอัปเดต VitalSign ก่อน
            let vitalSignResponse = await updateVitalSign(treatmentId, temperature, bloodPressure, heartRate, csrfToken);

            // 2. ถ้าอัปเดต VitalSign สำเร็จ, ให้ทำการบันทึกการวินิจฉัย
            if (vitalSignResponse.ok) {
                let diagnosisResponse = await addDiagnosis(treatmentId, doctorName, temperature, bloodPressure, heartRate, codesArray, treatmentStatus, notes, csrfToken);

                if (diagnosisResponse.ok) {
                    Swal.fire("สำเร็จ!", "บันทึกข้อมูลการวินิจฉัยและอัปเดต VitalSign สำเร็จ", "success")
                        .then(async () => {
                            // 4. ถ้าเป็น Follow-up ให้สร้างนัดหมายใหม่
                            if (treatmentStatus === "Follow-up") {
                                // ถ้าเป็น Follow-up ให้สร้าง Medical Report ใหม่และ Appointment ใหม่
                                let followUpResponse = await createFollowUpMedicalReportAndAppointment(treatmentId, csrfToken);

                                if (followUpResponse.ok) {
                                    Swal.fire("สำเร็จ!", "นัดหมายการติดตามผลถูกสร้างเรียบร้อยแล้ว", "success")
                                        .then(() => {
                                            window.location.replace("/opd/view-checkin");
                                        });
                                } else {
                                    Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถสร้างนัดหมายการติดตามผลได้", "error");
                                }
                            } else {
                                // 5. ถ้าไม่ใช่ Follow-up ให้แค่เปลี่ยนสถานะเป็น treated
                                updateTreatmentStatus(treatmentId).then(statusUpdated => {
                                    if (statusUpdated) {
                                        window.location.replace("/opd/view-checkin");
                                    } else {
                                        Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถอัปเดตสถานะการรักษาได้", "error");
                                    }
                                });
                            }
                        });
                } else {
                    Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลการวินิจฉัยได้", "error");
                }
            } else {
                Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถอัปเดต VitalSign ได้", "error");
            }
        } catch (error) {
            Swal.fire("❌ ข้อผิดพลาดเซิร์ฟเวอร์", `รายละเอียด: ${error.message || 'ไม่ทราบข้อผิดพลาด'}`, "error");
        }
    });
    async function fetchDiseaseInfo(codes) {
        console.log("กำลังดึงข้อมูลโรคสำหรับรหัส: ", codes); // เพิ่ม log เพื่อตรวจสอบว่าเรียกฟังก์ชันนี้ไหม

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
                        return data.diseases[0].disease_name || `ไม่พบชื่อโรคสำหรับรหัส: ${code.trim()}`;
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
    async function updateTreatmentStatus(treatmentId) {
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        console.log(`กำลังอัปเดตสถานะของ treatmentId: ${treatmentId}`); // ตรวจสอบว่า treatmentId ถูกต้องหรือไม่

        try {
            let response = await fetch(`/treatments/${treatmentId}/update-status`, {
                method: "PUT",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    treatment_id: treatmentId,
                    treatment_status: "treated"
                })
            });

            let data = await response.json();

            console.log("การตอบกลับจาก API:", data);  // ตรวจสอบการตอบกลับจาก API

            if (response.ok) {
                return true;
            } else {
                console.log("เกิดข้อผิดพลาดในการอัปเดตสถานะการรักษา:", data.message);
                return false;
            }
        } catch (error) {
            console.error("ข้อผิดพลาดในการอัปเดตสถานะการรักษา:", error);
            return false;
        }
    }
    async function createFollowUpMedicalReportAndAppointment(treatmentId, csrfToken) {
        const appointmentDate = document.getElementById('followUpDate').value;
        const appointmentLocation = document.getElementById('appointmentLocation').value;
        const caseType = document.getElementById('patientType').value;
        const notes = document.getElementById('notes').value;

        return await fetch(`/treatment/create-follow-up-medical-report-and-appointment/${treatmentId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                appointment_date: appointmentDate,
                appointment_location: appointmentLocation,
                case_type: caseType,
                notes: notes
            })
        });
    }
</script>

</body>

</html>