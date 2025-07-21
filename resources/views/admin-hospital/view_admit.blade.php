<!DOCTYPE html>
<html lang="th">

@include('themes.head')

<link rel="stylesheet" href="{{ asset('css/components/admit/view_admit.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/moderntable.css') }}">


<body class="hold-transition layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin-hospital.navbarhospital')
        <!-- Main Sidebar Container -->
        @include('themes.admin-hospital.menuhospital')


        <div class="content-wrapper">
            <div class="container-fluid p-4">
                <div class="container mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                        <h2 class="fw-bold mb-0" id="header-title">ติดตามผู้ป่วย ADMIT</h2>
                        <div class="d-flex flex-wrap align-items-end justify-content-end gap-3 mt-3">
                            <!-- กล่อง dropdown -->

                            <!-- ปุ่มตัวกรอง -->
                            <button class="btn btn-info btn-sm px-3" id="openFilterModal"
                                style="height: 38px; border-radius: 8px;">
                                <i class="fas fa-filter me-1"></i> ตัวกรอง
                            </button>




                        </div>

                    </div>
                    <div class="row g-3">
                        <div class="col-9">
                            <div class="box-admit border d-flex justify-content-center"
                                style="padding: 20px 15px 0 15px;">
                                <div class="table-responsive mb-3">
                                    <div id="loading" class="text-center py-4">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">กำลังโหลด...</span>
                                        </div>
                                    </div>

                                    <table id="patientsTable" class="modern-table data-table table-striped table-hover"
                                        data-no-actions style="display: none;">
                                        <thead>
                                            <tr>
                                                <th width="15%">ชื่อ-นามสกุล</th>
                                                <th width="10%">รหัสประจำตัว</th>
                                                <th width="12%">หน่วยฝึก</th>
                                                <th width="10%">ผลัด</th>
                                                <th width="10%">วันที่เข้า</th>
                                                <th width="20%">โรคที่วินิจฉัย</th>
                                            </tr>
                                        </thead>
                                        <tbody id="patients-tbody">
                                        </tbody>
                                    </table>

                                    <div id="no-data" class="text-center text-muted py-4" style="display: none;">
                                        <p>ไม่มีข้อมูลผู้ป่วย</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-3">

                            <div class="box-search border d-flex flex-column">
                                <!-- ครึ่งบน - คนเข้าวันนี้ -->
                                <div class="flex-fill mb-2">
                                    <div class="card h-100">
                                        <div class="card-header text-dark"
                                            style="background-color: var(--accent-color);">
                                            <h6 class="mb-0 d-flex justify-content-between">
                                                <span>รายการผู้ป่วยเข้า Admit วันนี้</span>
                                                <span>
                                                    <span id="today-admitted-count">0</span> ราย
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="card-body p-3" style="overflow-y: auto;">
                                            <div id="today-admitted-content">
                                                <div class="text-center py-3">
                                                    <div class="spinner-border spinner-border-sm text-success me-2">
                                                    </div>
                                                    <span class="text-muted">กำลังโหลดข้อมูล...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ครึ่งล่าง - คนออกวันนี้ -->
                                <div class="flex-fill mt-2">
                                    <div class="card h-100">
                                        <div class="card-header  text-dark"
                                            style="background-color: var(--accent-color);">
                                            <h6 class="mb-0 d-flex justify-content-between">
                                                <span>รายการผู้ป่วยออก Admit วันนี้</span>
                                                <span>

                                                    <span id="today-discharged-count">0</span> ราย
                                                </span>
                                            </h6>

                                        </div>
                                        <div class="card-body p-3" style="overflow-y: auto;">
                                            <div id="today-discharged-content">
                                                <div class="text-center py-3">
                                                    <div class="spinner-border spinner-border-sm text-warning me-2">
                                                    </div>
                                                    <span class="text-muted">กำลังโหลดข้อมูล...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal ตัวกรอง -->

        <!-- Modal ตัวกรอง -->
        <div class="modal fade" id="filterModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- ตัวกรองหลัก -->
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">สถานะ</label>
                                <select id="status-filter" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="Admit">กำลังรักษา</option>
                                    <option value="Discharged">ออกแล้ว</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">หน่วยฝึก</label>
                                <select id="unit-filter" class="form-select">
                                    <option value="all">ทุกหน่วยฝึก</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ผลัด</label>
                                <select id="rotation-filter" class="form-select">
                                    <option value="all">ทุกผลัด</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ช่วงวันที่</label>
                                <select id="date-filter" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="today">วันนี้</option>
                                    <option value="custom">กำหนดเอง</option>
                                </select>
                            </div>
                        </div>

                        <!-- วันที่กำหนดเอง -->
                        <div class="row mt-3" id="custom-date-row" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">วันที่เริ่มต้น</label>
                                <input type="date" id="start-date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่สิ้นสุด</label>
                                <input type="date" id="end-date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="w-100 d-flex justify-content-between">
                            <div>
                                <button id="clear-filters-btn" class="btn btn-outline-secondary">
                                    <i class="fas fa-eraser me-1"></i> ล้างตัวกรอง
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                    ปิด
                                </button>
                                <button id="apply-filters-btn" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> ค้นหา
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal รายละเอียดผู้ป่วย -->
        <div class="modal fade" id="patientModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">รายละเอียดผู้ป่วย</h5>
                    </div>
                    <div class="modal-body" id="modal-content" style="background-color: #f8f9fa;">
                        <!-- รายละเอียดจะแสดงที่นี่ -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template สำหรับ Patient Row -->
    <!-- Template สำหรับ Patient Row -->
    <template id="patient-row-template">
        <tr class="patient-row" style="cursor: pointer;">
            <td><strong class="patient-name"></strong></td>
            <td><span class="text-muted patient-id-card"></span></td>
            <td><small class="patient-unit"></small></td>
            <td><small class="patient-rotation"></small></td>
            <td><small class="patient-admit-date"></small></td>
            <td>
                <span class="disease-text patient-diseases"></span>
            </td>
            <!-- ลบ <td class="patient-actions"> ออก -->
        </tr>
    </template>

    <template id="patient-details-template">
        <div class="row">
            <!-- Profile Section -->
            <div class="col-md-12 mb-4">
                <div class="d-flex align-items-center p-3 bg-white shadow-sm" style="border-radius: 12px;">
                    <div class="col-md-auto" style="width: 100px;">
                        <img class="patient-detail-image rounded-3 border border-3 border-black shadow"
                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 16px;" alt="รูปผู้ป่วย">
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="patient-detail-name mb-1 fw-bold"></h4>
                        <div class="text-muted">
                            บัตรประชาชน: <span class="patient-detail-id"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลส่วนตัว -->
            <div class="col-md-12 mb-4">
                <h6 class="section-title">ข้อมูลส่วนตัว</h6>

                <div class="row ms-2">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">หน่วยฝึก
                                :
                            </div>
                            <div class="patient-detail-unit text-muted" style="font-weight: bold; font-size: 14px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">ผลัด :
                            </div>
                            <div class="patient-detail-rotation text-muted" style="font-weight: bold;font-size: 14px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">
                                หน่วยต้นสังกัด :</div>
                            <div class="patient-detail-affiliated text-muted"
                                style="font-weight: bold;font-size: 14px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลการรักษา Admit -->
            <div class="col-md-12">
                <h6 class="section-title">ข้อมูลการรักษา</h6>


                <div class="row ms-2">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px;color: #495057;  font-weight: bold; font-size: 14px;">
                                วันที่เข้า Admit :</div>
                            <div class="patient-detail-admit-date text-muted"
                                style="font-weight: bold;font-size: 14px;"></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px;color: #495057;  font-weight: bold; font-size: 14px;">
                                วันที่ออกจาก Admit :</div>
                            <div class="patient-detail-discharge-date text-muted"
                                style="font-weight: bold;font-size: 14px;"></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057;  font-weight: bold; font-size: 14px;">
                                จำนวนวันรักษา :</div>
                            <div class="patient-detail-days text-muted" style="font-weight: bold; font-size: 14px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">สถานะ :
                            </div>
                            <div class="patient-detail-status text-muted" style="font-weight: bold;font-size: 14px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">
                                โรคที่วินิจฉัย :</div>
                            <div class="patient-detail-diseases text-muted"
                                style="white-space: pre-line; font-weight: bold; font-size: 14px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @include('themes.script')
    <script src="{{ asset('js/components/admit/ipd-admit.js') }}"></script>


</body>

</html>