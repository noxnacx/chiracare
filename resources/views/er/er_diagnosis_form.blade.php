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
                        <h4 class="text-primary fw-bold text-center mb-4">ฟอร์มรวมข้อมูลผู้ป่วยและวินิจฉัย (ER)</h4>

                        <div class="alert alert-danger d-none" id="error-alert">ไม่พบข้อมูลทหาร</div>
                        <div class="alert alert-success d-none" id="success-alert">บันทึกข้อมูลเรียบร้อย</div>

                        <form id="erDiagnosisForm" method="POST" action="{{ route('er.storeWithDiagnosis') }}">
                            @csrf

                            {{-- 🔹 ชื่อ-นามสกุลทหาร --}}
                            <div class="mb-3">
                                <label for="soldier_fullname">ชื่อ-นามสกุลทหาร</label>
                                <input type="text" id="soldier_fullname" class="form-control" required>
                            </div>

                            {{-- 🔹 ข้อมูลทหารที่โหลดอัตโนมัติ --}}
                            <div id="soldier-info" class="mb-3 p-3 bg-light rounded d-none">
                                <label>เลขบัตรประชาชน</label>
                                <input type="text" id="soldier_id_card" name="soldier_id_card" class="form-control"
                                    readonly required>
                                <label>ผลัด</label>
                                <input type="text" id="soldier_rotation" class="form-control mb-2" readonly>
                                <label>หน่วยฝึก</label>
                                <input type="text" id="soldier_training_unit" class="form-control" readonly>
                            </div>

                            {{-- 🔹 รายละเอียดอาการ --}}
                            <div class="mb-3">
                                <label for="symptom_description">คำอธิบายอาการ</label>
                                <textarea name="symptom_description" id="symptom_description" class="form-control"
                                    rows="3" required></textarea>
                            </div>

                            {{-- 🔹 สัญญาณชีพ --}}
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>อุณหภูมิ (°C)</label>
                                    <input type="number" name="temperature" id="temperature" class="form-control"
                                        step="0.1" min="30" max="45" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>ความดันโลหิต (SYS/DIA)</label>
                                    <input type="text" name="blood_pressure" id="blood_pressure" class="form-control"
                                        pattern="\d{2,3}/\d{2,3}" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>อัตราการเต้นหัวใจ</label>
                                    <input type="number" name="heart_rate" id="heart_rate" class="form-control" min="40"
                                        max="180" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>ระดับความเสี่ยง</label>
                                    <input type="text" id="risk_level_display" class="form-control" readonly>
                                    <input type="hidden" name="risk_level" id="risk_level">
                                </div>
                            </div>

                            {{-- 🔹 ระดับความเจ็บปวด --}}
                            <div class="mb-3">
                                <label for="pain_score">ระดับความเจ็บปวด (1-10)</label>
                                <input type="number" name="pain_score" id="pain_score" class="form-control" min="1"
                                    max="10" required>
                            </div>

                            {{-- 🔹 แพทย์วินิจฉัย --}}
                            <div class="mb-3">
                                <label for="doctor_name">ชื่อแพทย์ผู้วินิจฉัย</label>
                                <input type="text" name="doctor_name" id="doctor_name" class="form-control" required>
                            </div>

                            {{-- 🔹 ICD10 + คำอธิบาย --}}
                            <div class="mb-3">
                                <label>รหัสโรค (ICD10)</label>
                                <input type="text" name="icd10_code" id="icd10_code" class="form-control"
                                    placeholder="เช่น J18.9,E11.9" required>
                            </div>
                            <div class="mb-3">
                                <label>คำอธิบายโรค</label>
                                <input type="text" id="diseaseDescription" class="form-control" readonly
                                    style="background-color: #f8f9fa;">
                            </div>

                            {{-- 🔹 หมายเหตุ --}}
                            <div class="mb-3">
                                <label>หมายเหตุเพิ่มเติม</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                            </div>

                            {{-- 🔹 สถานะการรักษา --}}
                            <div class="mb-3">
                                <label>สถานะการรักษา</label>
                                <select name="treatment_status" id="treatment_status" class="form-control" required>
                                    <option value="">-- เลือกสถานะ --</option>
                                    <option value="Admit">Admit (รับไว้รักษา)</option>
                                    <option value="Refer">Refer (ส่งต่อ)</option>
                                    <option value="Discharge">Discharge (จำหน่าย)</option>
                                    <option value="Follow-up">Follow-up (ติดตามอาการ)</option>
                                </select>
                            </div>
                            {{-- 🔹 นัดติดตาม (เฉพาะ Follow-up) --}}
                            <div id="follow-up-fields" style="display: none;">
                                <div class="mb-3">
                                    <label>วัน-เวลานัดหมายติดตาม</label>
                                    <input type="datetime-local" name="appointment_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>สถานที่นัดหมาย</label>
                                    <select name="appointment_location" class="form-control">
                                        <option value="OPD">OPD</option>
                                        <option value="ER">ER</option>
                                        <option value="IPD">IPD</option>
                                        <option value="กองพันทหารราบ">กองพันทหารราบ</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>ประเภทผู้ป่วย</label>
                                    <select name="case_type" class="form-control">
                                        <option value="normal">ปกติ</option>
                                        <option value="critical">วิกฤติ</option>
                                    </select>
                                </div>
                            </div>
                            {{-- 🔹 คำสั่งฝึก (เฉพาะ Follow-up และ Discharge) --}}
                            <div id='training-instruction-field' class="form-group mt-3" style="display: none;">
                                <label for="training_instruction_option">คำสั่งฝึก:</label>
                                <select class="form-select rounded-pill mb-2" id="training_instruction_option">
                                    <option value="">-- เลือกคำสั่งฝึก --</option>
                                    <option value="normal">ฝึกได้ปกติ</option>
                                    <option value="skip">งดฝึก</option>
                                </select>
                                <div id="trainingDayDiv" style="display: none;">
                                    <label for="training_day_count">จำนวนวันที่งดฝึก (วัน):</label>
                                    <input type="number" id="training_day_count" class="form-control rounded-pill"
                                        min="1" placeholder="เช่น 5">
                                </div>
                                <input type="hidden" name="training_instruction" id="training_instruction"> {{--
                                ค่าที่จะส่งจริง --}}
                            </div>
                            {{-- 🔹 ปุ่ม Submit --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-5">บันทึก</button>
                            </div>
                        </form>
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

                    </style>
</body>

</html>