<div class="search-section">
    <h2 class="search-title">ค้นหานัดหมายจากชื่อทหาร</h2>
    <div class="search-container">
        <input type="text" class="search-input" placeholder="กรอกชื่อ-นามสกุล หรือ เลขประจำตัวทหาร..."
            id="appointmentSearchInput">
    </div>
</div>


<!-- Initial State -->
<div id="appointmentInitialState" class="initial-state text-center text-muted p-5 mt-4" style="
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
    <!-- Results Container -->

</div>
<div id="appointmentResultsContainer" style="display: none;">
    <!-- Dynamic content will be inserted here -->
</div>

<!-- Loading -->
<div id="appointmentLoading"" style=" display: none;">
    <p class="mt-2">กำลังค้นหาข้อมูล...</p>

</div>

<!-- No Results -->
<div id="appointmentNoResults" class="no-results" style="display: none;">
    <div class="card border-danger border-2 border-opacity-25">
        <div class="card-body p-5">
            <i class="fas fa-calendar-times fa-4x mb-4 text-danger opacity-75"></i>
            <h5 class="text-danger">ไม่พบข้อมูลการนัดหมาย</h5>
            <p class="text-muted mb-4">กรุณาตรวจสอบชื่อ-นามสกุล หรือ
                เลขประจำตัวทหารอีกครั้ง</p>

        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/components/searchpatientandappointment.css') }}">

<script src="{{ asset('js/components/searchappointment.js') }}"></script>