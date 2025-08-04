{{--
Patient Search Component
Basic structure for patient search functionality
--}}

<!-- Search Section -->
<div class="search-section">
    <h2 class="search-title">ค้นหาผู้ป่วยจากชื่อทหาร</h2>
    <div class="search-container">
        <input type="text" class="search-input" placeholder="กรอกชื่อ-นามสกุล หรือ เลขประจำตัวทหาร..."
            id="patientSearchInput">
    </div>
</div>

<!-- Loading -->
<div id="patientLoading" style="display: none;">

    <p class="mt-2">กำลังค้นหาข้อมูล...</p>
</div>

<!-- No Results -->
<!-- Initial State - แสดงก่อนที่จะมีการค้นหา -->
<!-- Initial State - แสดงก่อนที่จะมีการค้นหา -->
<div id="patientInitialState" class="initial-state text-center text-muted p-5 mt-4" style="
       background: var( --snow-color);
       border: 2px dashed var(  --secondary-color);
       border-radius: 20px;
   ">
    <div class="initial-content">
        <i class="fas fa-search fa-4x mb-4" style="
                opacity: 0.6;
                color: var(--primary-color);
                animation: pulse 2s ease-in-out infinite;
            "></i>



        <h5 class="fw-bold" style="color: var(--text-color);">
            ยังไม่ได้กรอกข้อมูลสำหรับค้นหา
        </h5>
        <p class="mb-4 fs-6" style="color: #6c757d;">
            กรุณากรอก <strong>เลขบัตรประชาชน</strong> หรือ <strong>ชื่อทหาร</strong>
            ในช่องค้นหาด้านบน
        </p>

        <!-- คำแนะนำการใช้งาน -->



    </div>
</div>

<!-- Loading -->
<div id="loading" class="loading text-center p-4 mt-4" style="display: none;">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="spinner-border text-primary mb-3" role="status" style="color: var(--primary-color) !important;">
                <span class="visually-hidden">กำลังค้นหา...</span>
            </div>
            <h6 class="card-title" style="color: var(--text-color);">
                กำลังค้นหาข้อมูล...
            </h6>
            <small class="text-muted">กรุณารอสักครู่</small>
        </div>
    </div>
</div>

<!-- No Results -->
<div id="patientNoResults" class="no-results text-center p-4 mt-4" style="display: none;">
    <div class="card border-danger border-2 border-opacity-25">
        <div class="card-body p-4">
            <h6 class="card-title text-danger">ไม่พบข้อมูลผู้ป่วย</h6>
            <p class="card-text text-muted mb-3">กรุณาตรวจสอบชื่อ-นามสกุล หรือ
                เลขประจำตัวทหารอีกครั้ง</p>

            <!-- ปุ่มล้างการค้นหา -->

        </div>
    </div>
</div>

<!-- Patient Result -->
<div class="patient-card" id="patientCard" style="display: none;">
    <div class="patient-header">
        <div class="patient-profile">
            <!-- รูปทางซ้าย -->


            <!-- ข้อมูลทางขวา -->
            <div class="patient-info">
                <div class="patient-basic">
                    <!-- ใช้ margin แทน -->
                    <div class="patient-main-info d-flex align-items-center p-3">

                        <!-- ต้องมีแค่อันเดียว -->
                        <div id="uniqueProfileContainer" class="rounded-3 border border-3 border-black shadow"
                            style="width: 80px; height: 80px; overflow: hidden; margin-right: 1rem; border-radius: 16px;">
                        </div>
                        <!-- Patient Info -->
                        <div class="flex-grow-1">
                            <h3 class="patient-name-history mb-2" id="patientName">
                                {{ $patient->first_name ?? '' }}
                                {{ $patient->last_name ?? '' }}
                            </h3>
                            <div class="patient-id text-muted" id="patientId">
                                <i class="fas fa-id-card me-2"></i>บัตรประชาชน:
                                {{ $patient->soldier_id_card ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="patient-info-sections mt-2">
                    <!-- ส่วนบน: ข้อมูลส่วนตัว -->
                    <div class="info-section">
                        <h6 class="section-title">ข้อมูลส่วนตัว</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>หน่วยฝึก:</strong>
                                    <span id="trainingUnit">{{ $patient->training_unit_name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ผลัด:</strong>
                                    <span id="rotation">{{ $patient->rotation_name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>หน่วยต้นสังกัด:</strong>
                                    <span id="affiliatedUnit">{{ $patient->affiliated_unit ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ระยะเวลา:</strong>
                                    <span id="serviceDuration">{{ $patient->service_duration ?? '-' }}
                                        เดือน</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนล่าง: ข้อมูลสุขภาพ -->
                    <div class="info-section">
                        <h6 class="section-title">ข้อมูลสุขภาพ</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>น้ำหนัก:</strong>
                                    <span id="weight">{{ $patient->weight_kg ?? '-' }}
                                        kg.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ส่วนสูง:</strong>
                                    <span id="height">{{ $patient->height_cm ?? '-' }}
                                        cm.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>โรคประจำตัว:</strong>
                                    <span id="chronicDisease">{{ $patient->underlying_diseases ?? 'ไม่มี' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ประวัติแพ้ยา/อาหาร:</strong>
                                    <span
                                        id="drugAllergy">{{ $patient->medical_allergy_food_history ?? 'ไม่มี' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Medical History Section -->

</div>
<div class="history-section mb-3">

    <div class="history-content" id="medicalHistory">
        <!-- JavaScript จะสร้าง dropdown ให้อัตโนมัติ -->
    </div>
</div>

{{-- Include component styles and scripts --}}
<link rel="stylesheet" href="{{ asset('css/components/searchpatientandappointment.css') }}">

<script src="{{ asset('js/components/searchpatient.js') }}"></script>