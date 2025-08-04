<!DOCTYPE html>
<html lang="th">
@include('themes.head')
<Style>
    :root {
        --primary-color: #77B2C9;
        --secondary-color: #D6E7EE;
        --accent-color: #f3e8ff;
        --text-color: #222429;
        --gray-color: rgb(232, 232, 232);
        --white-color: #FFFFFF;
        --snow-color: #f9f9f9;
    }

    .content-box {
        background-color: white;
        /* เปลี่ยนพื้นหลังเป็นสีขาว */
        border-radius: 8px;
        min-height: 200px;

        color: #333;
        /* เปลี่ยนสีตัวอักษรให้เข้มขึ้นเพื่อให้อ่านง่ายบนพื้นขาว */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงา */
    }

    .header-box {
        min-height: 80px;
    }

    .sidebar-box {
        min-height: 400px;
    }

    .main-content-box {
        min-height: 400px;
    }

    .custom-underline {
        border-bottom: 1px solid #ddd;
        /* สีและความหนาตามต้องการ */
        padding-bottom: 15px;
        /* ระยะห่าง */
    }
</Style>

<body class="hold-transition layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')
        <!-- Main Sidebar Container -->
        @include('themes.ipd.menuipd')

        <div class="content-wrapper">
            <div class="container-fluid">

                <!-- Filter Form -->

                <div class="container py-4">


                    <!-- Main Content Section -->
                    <div class="row">
                        <!-- Sidebar -->
                        <div class="col-12 col-md-3 pe-2">
                            <div class="content-box sidebar-box">
                                <div class="w-100 p-3">
                                    <!-- Profile Header -->
                                    <div class="d-flex align-items-center mb-4 custom-underline">
                                        <h5 class="mb-0"><strong>ข้อมูลผู้ป่วย</strong></h5>
                                    </div>

                                    <!-- Patient Information -->
                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">ชื่อ-สกุล</label>
                                        <div class="text-black"><strong>{{ $soldierName }}</strong></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">เลขบัตรประชาชน</label>
                                        <div class="text-black"><strong>{{ $soldierIdCard }}</strong></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">ผลัด</label>
                                        <div class="text-black"><strong>{{ $soldierRotation }}</strong></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">หน่วยฝึก</label>
                                        <div class="text-black"><strong>{{ $soldierTraining }}</strong></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">หน่วยต้นสังกัด</label>
                                        <div class="text-black"><strong>{{ $soldierUnit }}</strong></div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">น้ำหนัก</label>
                                        <div class="text-black"><strong>{{ $soldierWeight }}</strong> กก.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">ส่วนสูง</label>
                                        <div class="text-black"><strong>{{ $soldierHeight}}</strong> ซม.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">โรคประจำตัว</label>
                                        <div class="text-black"><strong>{{ $soldierAllergies }}</strong></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label mb-0"
                                            style="font-weight: 400; color: #495057">ประวัติแพ้ยา/อาหาร</label>
                                        <div class="text-black"><strong>{{ $soldierUnderlyingDiseases }}</strong></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="col-12 col-md-9 ps-2">
                            <!-- Top Box -->
                            <div class="content-box mb-3 p-3"
                                style="min-height: 100px; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h5 class="mb-3 custom-underline"><strong>ประวัติการรักษา Admit</strong></h5>

                                <div class="patient-history" style="margin-left: 15px;">
                                    @foreach($previousDiagnoses as $diag)
                                        <div class="history-item mb-3">
                                            <div class="d-flex flex-wrap align-items-start mb-3">
                                                <!-- กลุ่มวันที่ -->
                                                <div class="me-4" style="margin-right: 10rem;">
                                                    <div style="font-weight: 400; color: #495057">วันที่เข้า Admit</div>
                                                    <div class="text-black">
                                                        <strong>{{ \Carbon\Carbon::parse($diag->diagnosis_date)->format('d/m/Y') }}</strong>
                                                    </div>
                                                </div>

                                                <!-- กลุ่มเวลา -->
                                                <div class="me-4" style="margin-right: 10rem;">
                                                    <div style="font-weight: 400; color: #495057">เวลา</div>
                                                    <div class="text-black">
                                                        <strong>{{ \Carbon\Carbon::parse($diag->diagnosis_date)->format('H:i') }}</strong>
                                                        น.
                                                    </div>
                                                </div>

                                                <!-- กลุ่มแพทย์ -->
                                                <div>
                                                    <div style="font-weight: 400; color: #495057">ชื่อแพทย์</div>
                                                    <div class="text-black">
                                                        <strong>{{ $diag->doctor_name }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="history-item mb-3">

                                                <div style="font-weight: 400; color: #495057">โรคที่ได้รับการวินิจฉัย</div>
                                                <ul style="list-style-type: none; padding-left: 20px;">
                                                    @foreach($diag->diseases as $disease)
                                                        <div class="text-black">
                                                            <strong>
                                                                <li>&ndash; {{ $disease->icd10_code }}
                                                            </strong> : {{ $disease->disease_name_en }}
                                                            </li>
                                                        </div>

                                                    @endforeach
                                                </ul>
                                            </div>

                                            <p class="mb-0">
                                                <span style="font-weight: 400; color: #495057">หมายเหตุ :</span>
                                                <span class="text-black"><strong>{{ $diag->notes ?? '-' }}</strong></span>
                                            </p>
                                        </div>
                                        @if(!$loop->last)
                                            <hr class="my-2">
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <!-- Main Content Box -->
                            <div class="content-box main-content-box  p-3">
                                <form id="diagnosisForm" action="{{ route('ipd.storeDiagnosis', $treatmentId) }}"
                                    method="POST">
                                    @csrf
                                    <h5 class="mb-3 custom-underline"><strong>การวินิจฉัยและการรักษา</strong></h5>
                                    <div class="row mb-3">
                                        <div class="col">
                                            <label for="temperature" class="form-label">อุณหภูมิ (°C)</label>
                                            <input type="number" step="0.1" class="form-control" id="temperature"
                                                name="temperature"
                                                value="{{ old('temperature', $vitalSigns->temperature ?? '') }}">
                                        </div>
                                        <div class="col">
                                            <label for="blood_pressure" class="form-label">ความดันโลหิต</label>
                                            <input type="text" class="form-control" id="blood_pressure"
                                                name="blood_pressure"
                                                value="{{ old('blood_pressure', $vitalSigns->blood_pressure ?? '') }}">
                                        </div>
                                        <div class="col">
                                            <label for="heart_rate" class="form-label">ชีพจร</label>
                                            <input type="number" class="form-control" id="heart_rate" name="heart_rate"
                                                value="{{ old('heart_rate', $vitalSigns->heart_rate ?? '') }}">
                                        </div>


                                    </div>

                                    <div class="mb-3">
                                        <label for="icd10_code" class="form-label">รหัสโรค (ICD10)</label>
                                        <input type="text" class="form-control" id="icd10_code" name="icd10_code"
                                            value="{{ old('icd10_code', implode(', ', $latestDiagnosis->diseases->pluck('icd10_code')->toArray() ?? [])) }}"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="disease_name" class="form-label">คำอธิบายโรค</label>
                                        <input type="text" class="form-control" id="disease_name" name="disease_name"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">หมายเหตุเพิ่มเติม</label>
                                        <textarea class="form-control" id="notes" name="notes"
                                            rows="2">{{ old('notes', $latestDiagnosis->notes ?? '') }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="treatment_status" class="form-label">สถานะการรักษา</label>
                                        <select class="form-select" id="treatment_status" name="treatment_status"
                                            required>
                                            <option value="Admit" {{ old('treatment_status', $latestDiagnosis->treatment_status
    ?? '') == 'Admit' ? 'selected' : '' }}>
                                                Admit</option>
                                            <option value="Discharge" {{ old('treatment_status', $latestDiagnosis->
    treatment_status ?? '') == 'Discharge' ? 'selected' : '' }}>
                                                Discharge</option>
                                            <option value="Refer" {{ old('treatment_status', $latestDiagnosis->treatment_status
    ?? '') == 'Refer' ? 'selected' : '' }}>
                                                Refer</option>
                                            <option value="Follow-up" {{ old('treatment_status', $latestDiagnosis->
    treatment_status ?? '') == 'Follow-up' ? 'selected' : '' }}>
                                                Follow-up</option>
                                        </select>
                                    </div>

                                    <!-- ✅ กรอบนัดหมายติดตาม -->
                                    <div id="follow_up_fields" class="bg-light border rounded p-3 mb-3"
                                        style="display: none;">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="appointment_date">วัน-เวลานัดหมายติดตาม</label>
                                                <input type="datetime-local" name="appointment_date"
                                                    id="appointment_date" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="appointment_location">สถานที่นัดหมาย</label>
                                                <select name="appointment_location" id="appointment_location"
                                                    class="form-select">
                                                    <option value="OPD">OPD</option>
                                                    <option value="ER">ER</option>
                                                    <option value="IPD">IPD</option>
                                                    <option value="ARI clinic">ARI clinic</option>
                                                    <option value="กองทันตกรรม'">กองทันตกรรม'</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="case_type">ประเภทผู้ป่วย</label>
                                                <select name="case_type" id="case_type" class="form-select">
                                                    <option value="normal">ปกติ</option>
                                                    <option value="critical">วิกฤติ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- ✅ กรอบคำสั่งฝึก -->
                                    <div id="training_instruction_container" class="bg-light border rounded p-3 mb-3"
                                        style="display: none;">
                                        <label for="training_instruction_option">คำสั่งฝึก:</label>
                                        <select class="form-select  mb-2" id="training_instruction_option">
                                            <option value="">-- เลือกคำสั่งฝึก --</option>
                                            <option value="normal">ฝึกได้ปกติ</option>
                                            <option value="skip">งดฝึก</option>
                                        </select>
                                        <div id="trainingDayDiv" style="display: none;">
                                            <label for="training_day_count">จำนวนวันที่งดฝึก (วัน):</label>
                                            <input type="number" id="training_day_count" class="form-control " min="1"
                                                placeholder="เช่น 5">
                                        </div>
                                        <input type="hidden" name="training_instruction" id="training_instruction">
                                    </div>

                                    <div class="d-flex justify-content-end pe-0 mt-4">
                                        <button type="submit" class="btn btn-success">บันทึกวินิจฉัยใหม่</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
</body>


<script>
    document.getElementById('icd10_code').addEventListener('input', function () {
        const value = this.value;
        if (value.trim() !== '') {
            fetchDiseaseInfo(value);
        } else {
            document.getElementById('disease_name').value = '';
        }
    });

    async function fetchDiseaseInfo(codes) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const codeArray = codes.split(',');
        const diseaseDescriptions = await Promise.all(codeArray.map(async code => {
            const res = await fetch(`/diseases/ipd/${code.trim()}`, {
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            if (res.ok) {
                const data = await res.json();
                return data.diseases?.[0]?.disease_name ?? `ไม่พบข้อมูล: ${code}`;
            }
            return `ไม่พบข้อมูล: ${code}`;
        }));

        // แสดงผลแบบรายการ bullet points
        const formattedList = diseaseDescriptions.map(disease => `- ${disease}`).join('\n');
        const diseaseField = document.getElementById('disease_name');

        // เปลี่ยน input เป็น textarea หากยังไม่ได้เปลี่ยน
        if (diseaseField.tagName.toLowerCase() === 'input') {
            const textarea = document.createElement('textarea');
            textarea.id = diseaseField.id;
            textarea.className = diseaseField.className;
            textarea.rows = 3;
            textarea.readOnly = true;
            textarea.style.resize = 'vertical';
            diseaseField.parentNode.replaceChild(textarea, diseaseField);
            textarea.value = formattedList;
        } else {
            diseaseField.value = formattedList;
        }
    }

    const treatmentStatusSelect = document.getElementById('treatment_status');
    const trainingInstructionField = document.getElementById('training_instruction_container');
    const followUpFields = document.getElementById('follow_up_fields');

    function toggleConditionalFields() {
        const selected = treatmentStatusSelect.value;
        trainingInstructionField.style.display = (selected === 'Discharge' || selected === 'Follow-up') ? 'block' : 'none';
        followUpFields.style.display = (selected === 'Follow-up') ? 'block' : 'none';
    }

    treatmentStatusSelect.addEventListener('change', toggleConditionalFields);
    document.addEventListener('DOMContentLoaded', toggleConditionalFields);

    document.getElementById('training_instruction_option').addEventListener('change', function () {
        const selected = this.value;
        const trainingDayDiv = document.getElementById('trainingDayDiv');
        const hiddenInput = document.getElementById('training_instruction');

        if (selected === 'normal') {
            trainingDayDiv.style.display = 'none';
            hiddenInput.value = 'ฝึกได้ปกติ';
        } else if (selected === 'skip') {
            trainingDayDiv.style.display = 'block';
            hiddenInput.value = '';
        } else {
            trainingDayDiv.style.display = 'none';
            hiddenInput.value = '';
        }
    });

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

</html>




<style>
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