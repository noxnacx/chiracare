<!DOCTYPE html>
<html lang="en">
@include('themes.head')


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')
        <!-- Main Sidebar Container -->
        @include('themes.er.menuer')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">


                    <div class="container mt-4">

                        <div class="card shadow-lg rounded overflow-hidden">
                            <div class="form-header">
                                <h4>บันทึกข้อมูลผู้ป่วยและวินิจฉัย (ER)
                                </h4>
                            </div>
                            <div class="card-body">

                                <div class="alert alert-danger d-none" id="error-alert"> ไม่พบข้อมูลทหาร</div>
                                <div class="alert alert-success d-none" id="success-alert">บันทึกข้อมูลเรียบร้อย
                                </div>

                                <form id="erDiagnosisForm" method="POST" action="{{ route('er.storeWithDiagnosis') }}">
                                    @csrf

                                    <!-- 🔹 ชื่อ -->
                                    <div class="mb-4">
                                        <label for="soldier_fullname"
                                            class="form-label fw-bold">ชื่อ-นามสกุลทหาร</label>
                                        <input type="text" id="soldier_fullname" class="form-control" required
                                            placeholder="เช่น สมชาย ใจดี">
                                    </div>

                                    <!-- 🔹 ข้อมูลอัตโนมัติ -->
                                    <div id="soldier-info" class="bg-light p-3 rounded border mb-4 ">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">เลขบัตรประชาชน</label>
                                                <input type="text" id="soldier_id_card" name="soldier_id_card"
                                                    class="form-control" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">ผลัด</label>
                                                <input type="text" id="soldier_rotation" class="form-control" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">หน่วยฝึก</label>
                                                <input type="text" id="soldier_training_unit" class="form-control"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🔹 รายละเอียดอาการ -->
                                    <div class="mb-4">
                                        <label for="symptom_description"
                                            class="form-label fw-bold">คำอธิบายอาการ</label>
                                        <textarea name="symptom_description" id="symptom_description"
                                            class="form-control" rows="3" required
                                            placeholder="ระบุอาการของผู้ป่วย..."></textarea>
                                    </div>

                                    <!-- 🔹 สัญญาณชีพ -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-3">
                                            <label class="form-label">อุณหภูมิ (°C)</label>
                                            <input type="number" name="temperature" id="temperature"
                                                class="form-control" step="0.1" min="30" max="45" required
                                                placeholder="36.5">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">ความดันโลหิต (SYS/DIA)</label>
                                            <input type="text" name="blood_pressure" id="blood_pressure"
                                                class="form-control" pattern="\d{2,3}/\d{2,3}" required
                                                placeholder="120/80">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">อัตราการเต้นหัวใจ</label>
                                            <input type="number" name="heart_rate" id="heart_rate" class="form-control"
                                                min="40" max="180" required placeholder="75">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">ระดับความเสี่ยง</label>
                                            <input type="text" id="risk_level_display" class="form-control bg-white"
                                                readonly>
                                            <input type="hidden" name="risk_level" id="risk_level">
                                        </div>
                                    </div>

                                    <!-- 🔹 ระดับความเจ็บปวด -->


                                    <!-- 🔹 แพทย์วินิจฉัย -->
                                    <div class="mb-4">
                                        <label for="doctor_name" class="form-label">ชื่อแพทย์ผู้วินิจฉัย</label>
                                        <input type="text" name="doctor_name" id="doctor_name" class="form-control"
                                            required placeholder="นพ. สุขใจ ดีงาม">
                                    </div>

                                    <!-- 🔹 ICD10 -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">รหัสโรค (ICD10)</label>
                                            <input type="text" name="icd10_code" id="icd10_code" class="form-control"
                                                placeholder="เช่น J18.9,E11.9" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">คำอธิบายโรค</label>
                                            <input type="text" id="diseaseDescription" class="form-control bg-white"
                                                readonly>
                                        </div>
                                    </div>

                                    <!-- 🔹 หมายเหตุ -->
                                    <div class="mb-4">
                                        <label for="notes" class="form-label">หมายเหตุเพิ่มเติม</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="2"
                                            placeholder="เช่น ผู้ป่วยมีประวัติแพ้ยา..."></textarea>
                                    </div>

                                    <!-- 🔹 สถานะการรักษา -->
                                    <div class="mb-4">
                                        <label for="treatment_status" class="form-label fw-bold">สถานะการรักษา</label>
                                        <select name="treatment_status" id="treatment_status" class="custom-dropdown"
                                            required>
                                            <option value="">-- เลือกสถานะ --</option>
                                            <option value="Admit">Admit (รับไว้รักษา)</option>
                                            <option value="Refer">Refer (ส่งต่อ)</option>
                                            <option value="Discharge">Discharge (จำหน่าย)</option>
                                            <option value="Follow-up">Follow-up (ติดตามอาการ)</option>
                                        </select>
                                    </div>



                                    <!-- 🔹 ข้อมูลนัดหมายติดตาม -->
                                    <div id="follow-up-fields" class="mb-4" style="display: none;">
                                        <div class="p-4 rounded border shadow-sm bg-white">
                                            <div class="mb-3 border-bottom pb-2 d-flex align-items-center">
                                                <i class="bi bi-calendar3 text-primary me-2"></i>
                                                <h6 class="m-0 text-primary fw-bold">ข้อมูลนัดหมายติดตาม</h6>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">วัน-เวลานัดหมาย</label>
                                                    <input type="datetime-local" name="appointment_date"
                                                        class="form-control">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">สถานที่นัดหมาย</label>
                                                    <select name="appointment_location" class="form-select">
                                                        <option value="OPD">OPD</option>
                                                        <option value="ER">ER</option>
                                                        <option value="IPD">IPD</option>
                                                        <option value="กองพันทหารราบ">กองพันทหารราบ</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">ประเภทผู้ป่วย</label>
                                                    <select name="case_type" class="form-select">
                                                        <option value="normal">ปกติ</option>
                                                        <option value="critical">วิกฤติ</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 🔹 คำสั่งฝึก -->
                                    <div id="training-instruction-field" class="mb-4" style="display: none;">
                                        <div class="p-4 rounded border shadow-sm bg-white">
                                            <div class="mb-3 border-bottom pb-2 d-flex align-items-center">
                                                <i class="bi bi-activity text-success me-2"></i>
                                                <h6 class="m-0 text-success fw-bold">คำสั่งฝึก</h6>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">สถานะคำสั่งฝึก</label>
                                                    <select class="form-select" id="training_instruction_option">
                                                        <option value="">-- เลือกคำสั่งฝึก --</option>
                                                        <option value="normal">ฝึกได้ปกติ</option>
                                                        <option value="skip">งดฝึก</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6" id="trainingDayDiv" style="display: none;">
                                                    <label class="form-label">จำนวนวันที่งดฝึก (วัน)</label>
                                                    <input type="number" id="training_day_count" class="form-control"
                                                        min="1" placeholder="เช่น 5">
                                                </div>
                                            </div>

                                            <input type="hidden" name="training_instruction" id="training_instruction">
                                        </div>
                                    </div>


                                    <!-- 🔹 Submit -->
                                    <div class="d-flex justify-content-end mt-4">

                                        <button type="submit" class="btn btn-success px-5 py-2 rounded-1 shadow
">
                                            บันทึกข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @include('themes.script')

                    <script>
                        // โหลดข้อมูลทหารจากชื่อ
                        document.getElementById('soldier_fullname').addEventListener('input', function () {
                            let parts = this.value.trim().split(/\s+/);
                            if (parts.length >= 2) {
                                fetch(`{{ route('soldier.getByName') }}?first_name=${parts[0]}&last_name=${parts.slice(1).join(' ')}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            document.getElementById('soldier_id_card').value = data.soldier.soldier_id_card;
                                            document.getElementById('soldier_rotation').value = data.soldier.rotation_name;
                                            document.getElementById('soldier_training_unit').value = data.soldier.training_unit_name;

                                            document.getElementById('soldier-info').classList.remove('d-none');
                                            document.getElementById('error-alert').classList.add('d-none');
                                        } else {
                                            document.getElementById('error-alert').classList.remove('d-none');
                                            document.getElementById('soldier-info').classList.add('d-none');
                                        }
                                    });
                            }
                        });

                        // คำนวณ risk level
                        document.querySelectorAll('#temperature, #blood_pressure, #heart_rate').forEach(el => {
                            el.addEventListener('input', function () {
                                let temp = parseFloat(document.getElementById('temperature').value);
                                let [sys, dia] = document.getElementById('blood_pressure').value.split('/').map(x => parseInt(x));
                                let risk = 'green';
                                if (temp > 40 || sys >= 180 || dia >= 120 || sys >= 140 || dia >= 90) risk = 'red';
                                else if (temp > 38 || sys >= 121 || dia >= 81 || sys < 90 || dia < 60) risk = 'yellow';
                                document.getElementById('risk_level_display').value = risk;
                                document.getElementById('risk_level').value = risk;
                            });
                        });

                        // แสดงฟิลด์ follow-up เฉพาะกรณี follow-up
                        document.getElementById('treatment_status').addEventListener('change', function () {
                            const status = this.value;
                            // ✅ แสดง follow-up fields
                            document.getElementById('follow-up-fields').style.display = (status === 'Follow-up') ? 'block' : 'none';
                            // ✅ แสดง training instruction เฉพาะ Follow-up หรือ Discharge
                            document.getElementById('training-instruction-field').style.display =
                                (status === 'Follow-up' || status === 'Discharge') ? 'block' : 'none';
                        });

                        // ดึงชื่อโรคจาก ICD10
                        document.getElementById('icd10_code').addEventListener('input', function () {
                            let codes = this.value;
                            fetch(`/diseases/${codes}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.diseases && data.diseases.length > 0) {
                                        document.getElementById('diseaseDescription').value = data.diseases.map(d => d.disease_name).join(', ');
                                    } else {
                                        document.getElementById('diseaseDescription').value = 'ไม่พบข้อมูล';
                                    }
                                });
                        });
                        document.getElementById('training_instruction_option').addEventListener('change', function () {
                            const selected = this.value;
                            const trainingDayDiv = document.getElementById('trainingDayDiv');
                            const hiddenInput = document.getElementById('training_instruction');

                            if (selected === 'normal') {
                                trainingDayDiv.style.display = 'none';
                                hiddenInput.value = 'ฝึกได้ปกติ';
                            } else if (selected === 'skip') {
                                trainingDayDiv.style.display = 'block';
                                hiddenInput.value = ''; // รอ user กรอกวันแล้วค่อยอัปเดต
                            } else {
                                trainingDayDiv.style.display = 'none';
                                hiddenInput.value = '';
                            }
                        });

                        // อัปเดตค่าจริงเมื่อ user กรอกจำนวนวัน
                        document.getElementById('training_day_count').addEventListener('input', function () {
                            const day = this.value;
                            const hiddenInput = document.getElementById('training_instruction');
                            if (day) {
                                hiddenInput.value = `งดฝึก(${day} วัน)`;
                            } else {
                                hiddenInput.value = '';
                            }
                        });
                    </script>
                    <style>
                        .form-header {
                            background: linear-gradient(135deg, #b71c1c, #d32f2f);
                            /* ER สีแดงเข้ม -> แดงสด */
                            /* ปรับสีได้ */
                            color: white;
                            padding: 1.5rem;
                            text-align: center;
                            margin: 0;
                        }

                        .form-header h4 {
                            font-weight: 600;
                            margin: 0;
                            letter-spacing: 0.5px;
                        }

                        .card {
                            border-radius: 12px;
                            overflow: hidden;
                            /* สำคัญมาก เพื่อให้ header ไม่เกินกรอบ */
                        }


                        .custom-dropdown {
                            appearance: none;
                            -webkit-appearance: none;
                            -moz-appearance: none;
                            background-color: rgb(255, 255, 255);
                            border: 1px solid #d1d5db;
                            padding: 10px 14px;
                            font-size: 1rem;
                            border-radius: 6px;
                            width: 20%;
                            font-family: 'Segoe UI', sans-serif;
                            background-image: url("data:image/svg+xml;utf8,<svg fill='navy' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
                            background-repeat: no-repeat;
                            background-position: right 10px center;
                            background-size: 20px;
                        }

                        .form-select {
                            appearance: none;
                            -webkit-appearance: none;
                            -moz-appearance: none;
                            background-color: rgb(255, 255, 255);
                            border: 1px solid #d1d5db;
                            padding: 10px 14px;
                            font-size: 1rem;
                            border-radius: 6px;
                            width: 100%;
                            font-family: 'Segoe UI', sans-serif;
                            background-image: url("data:image/svg+xml;utf8,<svg fill='navy' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
                            background-repeat: no-repeat;
                            background-position: right 10px center;
                            background-size: 20px;
                        }
                    </style>


</body>

</html>