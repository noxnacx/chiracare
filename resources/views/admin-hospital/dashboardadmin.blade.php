<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        /* 🧹 Cleaned CSS - ลบส่วนที่ไม่ใช้แล้ว */

        :root {
            --primary-color: #77B2C9;
            --secondary-color: #D6E7EE;
            --accent-color: #f3e8ff;
            --text-color: #222429;
            --gray-color: rgb(172, 172, 172);
            --white-color: #FFFFFF;
            --snow-color: #f9f9f9;
        }

        /* Layout & Grid */
        .container-fluid {
            max-width: 1200px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            grid-template-rows: auto auto auto auto;
            gap: 15px;
            row-gap: 10px;
            padding-bottom: 30px;
        }

        .grid-1 {
            grid-column: 1/4;
            grid-row: 1;
        }

        .grid-2 {
            grid-column: 4/7;
            grid-row: 1;
        }

        .grid-3 {
            grid-column: 7/10;
            grid-row: 1;
        }

        .grid-4 {
            grid-column: 10/13;
            grid-row: 1;
        }

        .grid-5 {
            grid-column: 1/10;
            grid-row: 2;
        }

        .grid-7 {
            grid-column: 10/13;
            grid-row: 2;
        }

        .grid-8 {
            grid-column: 10/13;
            grid-row: 3;
            margin-top: -150px;
        }

        .grid-9 {
            grid-column: 1/10;
            grid-row: 3;
            margin-bottom: 30px;
        }

        .grid-item {
            border-radius: 10px;
        }

        /* Cards */
        .simple-card {
            background: var(--white-color);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 14px;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 6px;
        }

        /* Stats */
        .stats-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 12px;
            flex: 1;
        }

        .stat-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            padding: 6px 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            min-height: 32px;
        }

        .stat-line:hover {
            background: #f1f3f4;
            transform: translateX(1px);
        }

        .stat-line.missed-line {
            border-left-color: #dc3545;
            background: #fff8f8;
        }

        .stat-line.waiting-line {
            border-left-color: var(--primary-color);
            background: #f8fbff;
        }

        .stat-label {
            color: var(--text-color);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
        }

        .stat-number {
            font-weight: 700;
            font-size: 14px;
            min-width: 20px;
            text-align: center;
            padding: 1px 6px;
            border-radius: 10px;
            background: white;
        }

        .stat-number.missed {
            color: #dc3545;
            background: #ffe6e6;
        }

        .stat-number.waiting {
            color: var(--primary-color);
            background: #e6f3ff;
        }

        /* Icons */
        .title-icon {
            color: var(--primary-color);
            font-size: 14px;
        }

        .stat-icon {
            width: 12px;
            height: 12px;
            font-size: 10px;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Buttons */
        .action-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s ease;
            margin-top: auto;
        }

        /* Tab System - Grid-9 */
        .top-card {
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            height: 50px;
            background-color: var(--gray-color);
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            gap: 8px;
            padding: 8px;
        }

        .top-card-item {
            flex: 1;
            background-color: var(--white-color);
            border-radius: 4px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .top-card-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        .top-card-item.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 2px 6px rgba(119, 178, 201, 0.3);
        }

        .content-area {
            display: none;
            margin-top: 66px;
            height: calc(100% - 66px);
            padding: 15px;
            background-color: var(--white-color);
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .content-area.active {
            display: block;
        }

        /* Tab System - Grid-5 */
        .grid-5 .grid-5-top-card {
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            height: 50px;
            background-color: var(--gray-color);
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            gap: 8px;
            padding: 8px;
            z-index: 10;
        }

        .grid-5 .grid-5-tab-item {
            flex: 1;
            background-color: var(--white-color);
            border-radius: 4px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .grid-5 .grid-5-tab-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        .grid-5 .grid-5-tab-item.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 2px 6px rgba(119, 178, 201, 0.3);
        }

        .grid-5 .grid-5-content-area {
            display: none;
            margin-top: 66px;
            height: calc(100% - 66px);
            padding: 15px;
            background-color: var(--white-color);
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .grid-5 .grid-5-content-area.active {
            display: block !important;
        }

        /* Chart & Progress Components */
        .grid-5-content-area {
            height: calc(100% - 66px);
            padding: 0;
            margin-top: 66px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .chart-content-container {
            flex: 1;
            height: 100%;
            overflow: hidden;
            padding: 8px;
        }

        .chart-canvas {
            height: 100% !important;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0 8px;
        }

        .chart-canvas::-webkit-scrollbar {
            width: 6px;
        }

        .progress-item {
            margin-bottom: 0.8rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.001rem;
            font-weight: 300;
            font-size: 16px;
            color: #495057;
        }

        .custom-progress {
            height: 6px;
            background-color: var(--gray-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress-bar-custom {
            height: 100%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 10px;
            transition: width 1.5s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-custom::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg,
                    transparent 25%,
                    rgba(255, 255, 255, 0.1) 25%,
                    rgba(255, 255, 255, 0.1) 50%,
                    transparent 50%,
                    transparent 75%,
                    rgba(255, 255, 255, 0.1) 75%);
            background-size: 20px 20px;
            animation: progressStripes 1s linear infinite;
        }

        @keyframes progressStripes {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 20px 0;
            }
        }

        .count-text {
            color: #6c757d;
            font-size: 0.6rem;
        }

        /* Loading */
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            min-height: 200px;
            text-align: center;
            background: linear-gradient(135deg, var(--white-color) 0%, var(--snow-color) 100%);
            border-radius: 16px;
            border: 1px solid var(--gray-color);
            box-shadow: 0 4px 12px rgba(119, 178, 201, 0.1);
        }

        .loading .spinner-border {
            color: var(--primary-color);
            width: 3rem;
            height: 3rem;
            border-width: 0.25rem;
        }

        .loading p {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0;
        }

        .loading-spinner {
            border: 4px solid var(--secondary-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Info Sections */
        .info-section {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .info-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-title {
            color: #495057;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 12px;
            padding-left: 8px;
            border-left: 3px solid #B19CD9;
        }

        .d-flex.gap-2 {
            padding: 6px 0;
            align-items: center;
        }

        .d-flex.gap-2 strong {
            color: #495057;
            font-weight: 600;
            min-width: 120px;
            font-size: 14px;
        }

        .d-flex.gap-2 span {
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
        }

        /* Grid-5 Content Area Specific */
        .grid-5-content-area {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .grid-5-content-area .card {
            border: none;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
        }

        .grid-5-content-area .card-body {
            padding: 30px;
        }

        .col-4 .card {
            height: 285px !important;
            min-height: 285px !important;
            max-height: 285px !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .col-4 .card .card-body {
            padding: 0 !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
        }

        /* Responsive Design */
        @media (max-width: 767.98px) {
            .main-grid {
                grid-template-columns: 1fr;
            }

            .grid-1,
            .grid-2,
            .grid-3,
            .grid-4,
            .grid-5,
            .grid-7,
            .grid-8,
            .grid-9 {
                grid-column: 1 / -1 !important;
                grid-row: auto !important;
                margin-top: 0 !important;
                margin-bottom: 15px !important;
            }

            .header-content {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .header-title {
                font-size: 16px;
                justify-content: center;
            }

            .table-container {
                overflow-x: auto;
            }

            .modern-table {
                min-width: 800px;
            }

            .modern-table thead th,
            .modern-table tbody td {
                padding: 12px 8px;
                font-size: 11px;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .main-grid {
                grid-template-columns: repeat(6, 1fr);
                grid-template-rows: auto auto auto auto auto;
            }

            .grid-1 {
                grid-column: 1/4;
                grid-row: 1;
            }

            .grid-2 {
                grid-column: 4/7;
                grid-row: 1;
            }

            .grid-3 {
                grid-column: 1/4;
                grid-row: 2;
            }

            .grid-4 {
                grid-column: 4/7;
                grid-row: 2;
            }

            .grid-5 {
                grid-column: 1/7;
                grid-row: 3;
            }

            .grid-7 {
                grid-column: 1/7;
                grid-row: 4;
                height: 120px;
            }

            .grid-8 {
                grid-column: 1/7;
                grid-row: 5;
                margin-top: 0;
            }

            .grid-9 {
                grid-column: 1/6;
                grid-row: 6;
                margin-bottom: 30px;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            .grid-5 {
                grid-column: 1/10;
            }

            .grid-7 {
                grid-column: 10/13;
                grid-row: 2;
                height: 120px;
            }

            .grid-8 {
                grid-column: 10/13;
                grid-row: 3;
            }

            .grid-9 {
                grid-column: 1/10;
                grid-row: 3;
                margin-bottom: 30px;
            }
        }

        @media (min-width: 1200px) {
            .grid-5 {
                grid-column: 1/10;
            }

            .grid-7 {
                grid-column: 10/13;
                grid-row: 2;
                height: 120px;
            }

            .grid-8 {
                grid-column: 10/13;
                grid-row: 3;
            }

            .grid-9 {
                grid-column: 1/10;
                grid-row: 3;
                margin-bottom: 30px;
            }
        }
    </style>
</head>

<body class="hold-transition layout-fixed sidebar-collapse">
    <div class="wrapper">
        @include('themes.admin-hospital.navbarhospital')
        @include('themes.admin-hospital.menuhospital')

        <div class="content-wrapper mt-4">
            <div class="container-fluid">
                <div class="main-grid">

                    <!-- Card 1 -->
                    <div class="grid-item grid-1">
                        <x-stat-card icon="user-md" title="OPD ยอดสะสมรายวัน" :value="$opdCount" />
                    </div>

                    <div class="grid-item grid-2">
                        <x-stat-card icon="ambulance" title="ER ยอดสะสมรายวัน" :value="$erCount" />
                    </div>

                    <div class="grid-item grid-3">
                        <x-stat-card icon="procedures" title="IPD ยอดสะสมรายวัน" :value="$ipdCount" />
                    </div>

                    <div class="grid-item grid-4">
                        <x-stat-card icon="brain" title="จิตเวช นัดหมายวันนี้" value="56" />
                    </div>

                    <!-- Other grids -->
                    <!-- แก้ไขส่วน Grid-5 -->
                    <div class="grid-item large grid-5" style="height: 400px;">
                        <div class="card bg-white rounded-3 shadow-sm p-2"
                            style="height: 100%; display: flex; flex-direction: column; overflow: hidden;">

                            <div class="grid-5-top-card">
                                <div class="grid-5-tab-item active" data-grid5-content="grid5-content-ranking-chart">
                                    สถิติการรักษารายวัน
                                </div>
                                <div class="grid-5-tab-item  " data-grid5-content="grid5-content-static-today">
                                    สถิติโรคประจำเดือน<span id="currentMonth"></span>

                                </div>
                                <div class="grid-5-tab-item" data-grid5-content="grid5-content-enter-icd">
                                    ระบุโรคเฝ้าระวัง
                                </div>
                            </div>

                            <x-chartdonut-box />



                            <div id="grid5-content-static-today" class="grid-5-content-area">
                                <x-progressbarmonth />

                            </div>

                            <div id="grid5-content-enter-icd" class="grid-5-content-area">
                                <!-- Header Tabs -->


                                <!-- Main Content Area -->
                                <x-barchartselect />


                            </div>
                        </div>
                    </div>



                    <div class="grid-item grid-7">
                        <div class="simple-card">
                            <!-- Title -->
                            <h5 class="card-title">
                                <i class="fas fa-user-shield title-icon"></i>
                                นัดหมายทหาร
                            </h5>

                            <!-- Stats -->
                            <div class="stats-row">
                                <div class="stat-line missed-line">
                                    <span class="stat-label">
                                        <i class="fas fa-exclamation-triangle stat-icon"></i>
                                        ไม่มาตามนัด
                                    </span>
                                    <span class="stat-number missed">{{ $missedCount }}</span>
                                </div>

                                <div class="stat-line waiting-line">
                                    <span class="stat-label">
                                        <i class="fas fa-clock stat-icon"></i>
                                        รอการนัดหมาย
                                    </span>
                                    <span class="stat-number waiting">{{ $sentCount }}</span>
                                </div>
                                <div class="stat-line waiting-line">
                                    <span class="stat-label">
                                        <i class="fas fa-clock stat-icon"></i>
                                        รอการนัดหมาย
                                    </span>
                                    <span class="stat-number waiting">{{ $sentCount }}</span>
                                </div>

                            </div>

                            <!-- Divider -->

                            <!-- Button -->
                            <a href="{{ url('hospital/appointments') }}" class="action-btn">
                                <i class="fas fa-calendar-plus"></i>
                                จัดการนัดหมาย
                            </a>
                        </div>
                    </div>

                    <!-- Grid-8: กล่องนัดหมาย 3 ประเภท -->
                    <div class="grid-item grid-8">
                        <div class="card bg-white rounded-3 shadow-sm p-3" style="height: 95%;">
                            <!-- Title -->

                            <x-appointmenttoday :critical-appointments="$criticalAppointments"
                                :appointments="$appointments" />
                        </div>
                    </div>
                    <!-- แก้ไขส่วน Grid-9 -->
                    <div class="grid-item extra-large grid-9" style="min-height: 680px; max-height: 680px;">
                        <div class="card bg-white rounded-3 shadow-sm p-2" style="
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    ">
                            <div class="top-card">
                                <div class="top-card-item active" data-content="content1">ติดตามผู้ป่วย Admit</div>
                                <div class="top-card-item" data-content="content2">รายการบันทึกนัดหมายวันนี้</div>
                                <div class="top-card-item" data-content="content3">ค้นหานัดหมาย</div>
                                <div class="top-card-item" data-content="content4">ค้นหาผู้ป่วย</div>
                            </div>

                            <!-- Content Areas - แก้ไขให้มีเพียง content1 เท่านั้นที่ active -->
                            <div id="content1" class="content-area active">
                                <x-followadmittable :patients="$admitPatients" title="รายชื่อผู้ป่วย IPD วันนี้"
                                    viewAllUrl="/admin/patient/ipd" :maxRows="5" :showViewAll="true" />
                            </div>

                            <div id="content2" class="content-area">
                                <x-makeappointmenttoday />
                            </div>

                            <!-- ❌ ลบ class "active" ออกจาก content3 -->
                            <div id="content3" class="content-area"
                                style="flex: 1; overflow-y: auto; overflow-x: hidden;padding: 15px; height: 0;">
                                <x-searchappointment />
                            </div>

                            <!-- ❌ ลบ class "active" ออกจาก content4 -->
                            <div id="content4" class="content-area" style="
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 15px;
            height: 0;
        ">
                                <x-searchpatient />
                            </div>
                        </div>
                    </div>

                    @include('themes.scriptnotable')
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>


<!-- ก่อนปิด </body> -->
<script src="/js/components/searchappointment.js"></script>

<script>
    // JavaScript for Tab Functionality
    document.addEventListener('DOMContentLoaded', function () {
        const tabItems = document.querySelectorAll('.top-card-item');
        const contentAreas = document.querySelectorAll('.content-area');

        tabItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove active class from all tabs
                tabItems.forEach(tab => tab.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Hide all content areas
                contentAreas.forEach(content => content.classList.remove('active'));

                // Show selected content area
                const targetContent = document.getElementById(this.dataset.content);
                if (targetContent) {
                    targetContent.classList.add('active');
                }

                // Optional: Call different functions based on selection
                handleTabChange(this.dataset.content);
            });
        });
    });

</script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ระบบ Tab สำหรับ Grid-5
        const grid5TabItems = document.querySelectorAll('[data-grid5-content]');
        const grid5ContentAreas = document.querySelectorAll('.grid-5-content-area');

        grid5TabItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove active class from all Grid-5 tabs
                grid5TabItems.forEach(tab => tab.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Hide all Grid-5 content areas
                grid5ContentAreas.forEach(content => {
                    content.classList.remove('active');
                    content.style.display = 'none';
                });

                // Show selected Grid-5 content area
                const targetContentId = this.getAttribute('data-grid5-content');
                const targetContent = document.getElementById(targetContentId);
                if (targetContent) {
                    targetContent.classList.add('active');
                    targetContent.style.display = 'block';
                }

                console.log('Grid-5 tab clicked:', targetContentId);
            });
        });

        // ระบบ Tab สำหรับ Grid-9 (ใช้ชื่อเดิม)
        const grid9TabItems = document.querySelectorAll('[data-content]');
        const grid9ContentAreas = document.querySelectorAll('.content-area');

        grid9TabItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove active class from all Grid-9 tabs
                grid9TabItems.forEach(tab => tab.classList.remove('active'));

                // Add active class to clicked tab
                this.classList.add('active');

                // Hide all Grid-9 content areas
                grid9ContentAreas.forEach(content => {
                    content.classList.remove('active');
                    content.style.display = 'none';
                });

                // Show selected Grid-9 content area
                const targetContentId = this.getAttribute('data-content');
                const targetContent = document.getElementById(targetContentId);
                if (targetContent) {
                    targetContent.classList.add('active');
                    targetContent.style.display = 'block';
                }

                console.log('Grid-9 tab clicked:', targetContentId);
            });
        });
    });
</script>