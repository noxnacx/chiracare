<!DOCTYPE html>
<html lang="th">

@include('themes.head')

<style>
    :root {
        --primary-color: #77B2C9;
        --secondary-color: #D6E7EE;
        --accent-color: #f3e8ff;
        --text-color: #222429;
        --gray-color: rgb(232, 232, 232);
        --white-color: #FFFFFF;
        --snow-color: #f9f9f9;
    }

    count-badge {
        /* ไม่มีพื้นหลัง (transparent) */
        border: 1px solid var(--accent-color, #f3e8ff);
        /* กรอบสีจาก --accent-color */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* เงา */
        padding: 0.15rem 0.5rem;
        /* ขนาดภายใน */
        border-radius: 0.25rem;
        /* มุมโค้ง */
        color: inherit;
        /* สีตาม parent */
        display: inline-flex;
        align-items: center;
    }

    .box-search {
        background-color: #fff;
        height: 900px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .box-search>.flex-fill {
        height: 50% !important;
        /* บังคับครึ่งเท่าๆ */
        flex: none !important;
    }

    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        padding: 0.75rem 1rem;
    }

    .card-header h6 {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .patient-card {
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.4rem;
    }

    .patient-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .card-body::-webkit-scrollbar {
        width: 6px;
    }

    .card-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }


    .box-admit {
        background-color: #fff;
        min-height: 900px;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: var(--white-color);
        margin: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-color, #e0e0e0);
    }

    .modern-table thead th {
        background: var(--gray-color);
        color: var(--text-color);
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        text-align: center;
        border: none;
    }

    .modern-table tbody tr:nth-child(odd) {
        background: var(--white-color);
    }

    .modern-table tbody tr:nth-child(even) {
        background: var(--snow-color);
    }

    .modern-table tbody tr:hover {
        background-color: var(--hover-color, #f5f5f5);
    }

    .modern-table tbody td {
        padding: 12px;
        /* ปรับ padding ให้เหมาะสม */
        vertical-align: middle;
        /* จัดกึ่งกลางแนวตั้ง */
        text-align: center;
        /* จัดกึ่งกลางแนวนอน */
        border: none;
        font-size: 13px;
        color: var(--text-color);
    }


    /* Section Titles */
    .section-title {
        color: #495057;
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 16px;
        padding-left: 8px;
        border-left: 3px solid #B19CD9;
    }

    .patient-info-sections {
        margin-top: 16px;
    }

    .section-box {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        height: calc((900px - 48px) / 3);
        /* (750px - margins) / 3 sections */
    }
</style>

<body class="hold-transition layout-fixed sidebar-collapse">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')

        @include('themes.er.menuer')

        <div class="content-wrapper">
            <div class="container-fluid p-4">
                <div class="container mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                        <h2 class="fw-bold mb-0" id="header-title">ติดตามผู้ป่วย ER</h2>
                        <div class="d-flex flex-wrap align-items-end justify-content-end gap-3 mt-3">
                            <!-- กล่อง dropdown -->

                            <!-- ปุ่มตัวกรอง -->
                            <button class="btn btn-info btn-sm px-3" id="openFilterModal"
                                style="height: 38px; border-radius: 8px;">
                                <i class="fas fa-filter me-1"></i> ตัวกรอง
                            </button>


                            <!-- แทนที่ส่วน box-search เดิม หรือใส่ตำแหน่งที่ต้องการ -->



                            <!-- ปุ่มดาวน์โหลด PDF -->

                        </div>

                    </div>
                    <div class="row g-3">
                        <div class="col-9">
                            <div class="box-admit border d-flex justify-content-center"
                                style="padding: 20px 15px 0 15px;">
                                <div id="loadingIndicator" class="loading">
                                    <i class="fas fa-spinner fa-spin"></i> กำลังโหลดข้อมูล...
                                </div>
                                <div id="tableContainer"></div>
                            </div>

                        </div>
                        <div class="col-3">

                            <div class="box-search border d-flex flex-column p-2">
                                <!-- ช่องที่ 1 - ความเสี่ยงสูง -->
                                <div class="section-box mb-2">
                                    <div
                                        class="bg-danger text-white p-2 rounded-top d-flex justify-content-between align-items-center">
                                        <span class="fw-bold" style="font-size: 14px;">รายการความเสี่ยงสูงวันนี้</span>
                                        <span class="badge bg-light text-danger" style="font-size: 12px;"
                                            id="high-risk-count">0 ราย</span>
                                    </div>
                                    <div class="section-content" id="high-risk-list"
                                        style="height: calc(100% - 48px); overflow-y: auto; padding: 0;">
                                        <div class="empty-state"
                                            style="text-align: center; color: #999; font-style: italic; padding: 40px 20px; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                            <i class="fas fa-user-injured"
                                                style="font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                            <div>กำลังโหลดข้อมูล...</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ช่องที่ 2 - ความเสี่ยงกลาง -->
                                <div class="section-box mb-2">
                                    <div class="text-white p-2 rounded-top d-flex justify-content-between align-items-center"
                                        style="background-color: rgb(228, 114, 0);">
                                        <span class="fw-bold" style="font-size: 14px;">รายการความเสี่ยงกลางวันนี้</span>
                                        <span class="badge bg-light" style="font-size: 12px; color: #cc6600;"
                                            id="medium-risk-count">0 ราย</span>
                                    </div>
                                    <div class="section-content" id="medium-risk-list"
                                        style="height: calc(100% - 48px); overflow-y: auto; padding: 0;">
                                        <div class="empty-state"
                                            style="text-align: center; color: #999; font-style: italic; padding: 40px 20px; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                            <i class="fas fa-exclamation-triangle"
                                                style="font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                            <div>กำลังโหลดข้อมูล...</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ช่องที่ 3 - ความเสี่ยงต่ำ -->
                                <div class="section-box">
                                    <div class="text-white p-2 rounded-top d-flex justify-content-between align-items-center"
                                        style="background-color: #28a745;">
                                        <span class="fw-bold" style="font-size: 14px;">รายการความเสี่ยงต่ำวันนี้</span>
                                        <span class="badge bg-light" style="font-size: 12px; color: #28a745;"
                                            id="low-risk-count">0 ราย</span>
                                    </div>
                                    <div class="section-content" id="low-risk-list"
                                        style="height: calc(100% - 48px); overflow-y: auto; padding: 0;">
                                        <div class="empty-state"
                                            style="text-align: center; color: #999; font-style: italic; padding: 40px 20px; height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                            <i class="fas fa-check-circle"
                                                style="font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                            <div>กำลังโหลดข้อมูล...</div>
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


    @include('themes.script')
    <script>
        // Patient Dashboard JavaScript - Complete Version
        // =======================================================

        // API configuration
        const API_BASE_URL = '/er';
        const API_ENDPOINTS = {
            appointments: `${API_BASE_URL}/appointments`,
            todayAppointments: `${API_BASE_URL}/today-appointments`
        };

        // Global variables
        let allPatients = [];
        let filteredPatients = [];
        let currentDataTable = null;

        // DOM elements (with null checks)
        let dateFilter, specificDate, riskLevelFilter;
        let loadingIndicator, tableContainer, statsContainer;
        let filterModal, patientModal, modalBody;

        // Initialize DOM elements safely
        function initializeDOMElements() {
            // Display elements
            loadingIndicator = document.getElementById('loadingIndicator');
            tableContainer = document.getElementById('tableContainer');

            // ลบ loading indicator ออกทันทีถ้ามี
            if (loadingIndicator) {
                loadingIndicator.remove();
                loadingIndicator = null;
            }

            statsContainer = document.getElementById('statsContainer');

            // Modal elements
            filterModal = document.getElementById('filterModal');
            patientModal = document.getElementById('patientModal');
            modalBody = document.getElementById('modalBody');

            console.log('DOM Elements initialized:', {
                loadingIndicator: !!loadingIndicator,
                tableContainer: !!tableContainer,
                statsContainer: !!statsContainer
            });
        }

        // Event listeners setup
        function setupEventListeners() {
            // Filter modal buttons
            const openFilterBtn = document.getElementById('openFilterModal');

            if (openFilterBtn) {
                openFilterBtn.addEventListener('click', showFilterModal);
            }

            // Global event listeners
            window.addEventListener('click', (event) => {
                if (patientModal && event.target === patientModal) {
                    closePatientModal();
                }
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (event) => {
                // Ctrl/Cmd + R: Refresh data
                if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
                    event.preventDefault();
                    loadData();
                }

                // Escape: Close modal
                if (event.key === 'Escape' && patientModal && patientModal.style.display === 'block') {
                    closePatientModal();
                }
            });
        }

        // Modal management functions
        function showFilterModal() {
            if (!filterModal) {
                createFilterModal();
            }

            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(filterModal);
                modal.show();
            } else {
                filterModal.style.display = 'block';
            }
        }

        function hideFilterModal() {
            if (typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getInstance(filterModal);
                if (modal) modal.hide();
            } else {
                filterModal.style.display = 'none';
            }
        }

        function createFilterModal() {
            const modalHtml = `
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">
                            <i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="filterForm">
                            <div class="row g-3">
                                <!-- กรองตามวันที่ -->
                                <div class="col-12">
                                    <label for="date-filter" class="form-label">
                                        <i class="fas fa-calendar me-1"></i><strong>กรองตามวันที่</strong>
                                    </label>
                                    <select class="form-select" id="date-filter">
                                        <option value="today">วันนี้</option>
                                        <option value="specific_date">เลือกวันที่</option>
                                    </select>
                                </div>

                                <!-- วันที่เฉพาะ (ซ่อนไว้) -->
                                <div class="col-12" id="specific-date-container" style="display: none;">
                                    <label for="specific-date" class="form-label">
                                        <i class="fas fa-calendar-day me-1"></i>เลือกวันที่
                                    </label>
                                    <input type="date" class="form-control" id="specific-date">
                                </div>

                                <!-- กรองตามความเสี่ยง -->
                                <div class="col-12">
                                    <label for="risk-filter" class="form-label">
                                        <i class="fas fa-exclamation-triangle me-1"></i><strong>ระดับความเสี่ยง</strong>
                                    </label>
                                    <select class="form-select" id="risk-filter">
                                        <option value="all">ทุกระดับ</option>
                                        <option value="red">สูง (แดง)</option>
                                        <option value="yellow">ปานกลาง (เหลือง)</option>
                                        <option value="green">ต่ำ (เขียว)</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="resetFiltersBtn">
                            <i class="fas fa-undo me-1"></i>รีเซ็ต
                        </button>
                        <button type="button" class="btn btn-primary" id="applyFiltersBtn">
                            <i class="fas fa-check me-1"></i>ใช้ตัวกรอง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            filterModal = document.getElementById('filterModal');

            // Initialize filter elements after creating modal
            dateFilter = document.getElementById('date-filter');
            specificDate = document.getElementById('specific-date');
            riskLevelFilter = document.getElementById('risk-filter');

            // Set default date to today
            if (specificDate) {
                specificDate.value = new Date().toISOString().split('T')[0];
            }

            // Setup event listeners for new elements
            if (dateFilter) {
                dateFilter.addEventListener('change', toggleDateInput);
            }

            const applyBtn = document.getElementById('applyFiltersBtn');
            const resetBtn = document.getElementById('resetFiltersBtn');

            if (applyBtn) {
                applyBtn.addEventListener('click', () => {
                    loadData();
                    hideFilterModal();
                });
            }

            if (resetBtn) {
                resetBtn.addEventListener('click', resetFilters);
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            initializeDOMElements();
            setupEventListeners();

            // Load initial data
            loadData();
        });

        // Toggle date input visibility
        function toggleDateInput() {
            const specificDateContainer = document.getElementById('specific-date-container');
            if (dateFilter && specificDateContainer) {
                if (dateFilter.value === 'specific_date') {
                    specificDateContainer.style.display = 'block';
                    if (specificDate && !specificDate.value) {
                        specificDate.value = new Date().toISOString().split('T')[0];
                    }
                } else {
                    specificDateContainer.style.display = 'none';
                }
            }
        }

        // Reset filters
        function resetFilters() {
            if (dateFilter) dateFilter.value = 'today';
            if (riskLevelFilter) riskLevelFilter.value = 'all';
            if (specificDate) specificDate.value = new Date().toISOString().split('T')[0];
            toggleDateInput();
        }

        // API call function
        async function callAPI() {
            try {
                const params = new URLSearchParams();

                // Add date filter
                if (dateFilter) {
                    if (dateFilter.value === 'today') {
                        params.append('date_filter', 'today');
                    } else if (dateFilter.value === 'specific_date' && specificDate && specificDate.value) {
                        params.append('date_filter', 'specific_date');
                        params.append('specific_date', specificDate.value);
                    }
                }

                // Add risk level filter
                if (riskLevelFilter && riskLevelFilter.value !== 'all') {
                    params.append('risk_level', riskLevelFilter.value);
                }

                // Build URL with parameters
                const url = `${API_ENDPOINTS.appointments}?${params.toString()}`;

                console.log('Calling API:', url);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();
                console.log('API Response:', data);

                return data;

            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }

        // Utility functions for handling API response data
        function validatePatientData(patient) {
            return {
                ...patient,
                soldier_name: patient.soldier_name || 'ไม่ระบุ',
                training_unit: patient.training_unit || 'ไม่ระบุ',
                rotation: patient.rotation || 'ไม่ระบุ',
                risk_level: patient.risk_level || 'not-assessed',
                diagnosis_treatment_status: patient.diagnosis_treatment_status || 'ไม่ระบุ',
                disease_codes: patient.disease_codes || 'ไม่ระบุ',
                disease_list: patient.disease_list || 'ไม่ระบุ',
                diseases: patient.diseases || [],
                doctor_name: patient.doctor_name || 'ไม่ระบุ',
                training_instruction: patient.training_instruction || 'ไม่ระบุ',
                soldier_image_url: patient.soldier_image_url || null,      // เพิ่มบรรทัดนี้
                affiliated_unit: patient.affiliated_unit || 'ไม่ระบุ',
                diagnosis_notes: patient.diagnosis_notes || 'ไม่ระบุ',
                temperature: patient.temperature || 'ไม่ระบุ',
                blood_pressure: patient.blood_pressure || 'ไม่ระบุ',
                heart_rate: patient.heart_rate || 'ไม่ระบุ'
            };
        }

        function processApiResponse(response) {
            if (response.data && response.data.appointments) {
                response.data.appointments = response.data.appointments.map(validatePatientData);
            }
            return response;
        }

        // Load data function
        async function loadData() {
            try {
                const response = await callAPI();
                const processedResponse = processApiResponse(response);

                if (processedResponse.success) {
                    allPatients = processedResponse.data.appointments;
                    applyFilters();
                    displayTable();
                } else {
                    throw new Error(processedResponse.message || 'Failed to load data');
                }

            } catch (error) {
                console.error('Error loading data:', error);
                if (tableContainer) {
                    tableContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                    <h5>เกิดข้อผิดพลาดในการโหลดข้อมูล</h5>
                    <p class="mb-2">${error.message}</p>
                    <button class="btn btn-primary" onclick="loadData()">
                        <i class="fas fa-redo me-1"></i> ลองใหม่
                    </button>
                </div>
            `;
                }
                if (statsContainer) {
                    statsContainer.remove();
                    statsContainer = null;
                }
            }
        }

        // Apply filters
        function applyFilters() {
            filteredPatients = allPatients.filter(patient => {
                // Risk level filter
                if (riskLevelFilter && riskLevelFilter.value !== 'all' && patient.risk_level !== riskLevelFilter.value) {
                    return false;
                }
                return true;
            });
        }

        // Display statistics (ปิดการใช้งาน)
        function displayStats(stats) {
            console.log('Statistics received but not displayed:', stats);
        }

        // ฟังก์ชันสำหรับจัดรูปแบบข้อมูลโรคแบบ badge
        function formatDiseaseInfo(diseaseCodes, diseaseList) {
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (!diseaseCodes || diseaseCodes === 'ไม่ระบุ' || !diseaseList || diseaseList === 'ไม่ระบุ') {
                return '<em class="text-muted">ยังไม่มีข้อมูลการวินิจฉัย</em>';
            }

            // แยกรหัสโรคและชื่อโรค (ถ้ามีหลายโรค)
            const codes = diseaseCodes.split(',').map(code => code.trim());
            const names = diseaseList.split(',').map(name => name.trim());

            // สร้างรายการโรคในรูปแบบ badge คู่
            const diseaseInfo = codes.map((code, index) => {
                const name = names[index] || 'ไม่ระบุชื่อโรค';
                return `
        <div class="d-flex align-items-center mb-2">
    <span style="
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 6px;
        background-color: white;
        color: #212529;
        border: 1px solid #6c757d;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: inline-block;
    ">
        <span style="font-weight: 600;">${code}</span> :
        <span style="font-weight: 400;">${name}</span>
    </span>
</div>
        `;
            });

            // รวมหลายโรคด้วยการขึ้นบรรทัดใหม่
            return diseaseInfo.join('');
        }

        // Display table with existing DataTable
        function displayTable() {
            if (!tableContainer) return;

            // Destroy existing DataTable if exists
            if (currentDataTable) {
                currentDataTable.destroy();
                currentDataTable = null;
            }

            if (filteredPatients.length === 0) {
                tableContainer.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                ไม่พบข้อมูลผู้ป่วยตามเงื่อนไขที่กรอง
            </div>
        `;
                return;
            }

            // Create table structure with data-table class
            const tableHtml = `
        <table class="modern-table data-table table table-striped table-hover w-100" style="cursor: pointer;">
            <thead>
                <tr>
                   <th style="width: 20%;">ชื่อ-นามสกุล</th>
                <th style="width: 15%;">หน่วยฝึก</th>
                <th style="width: 15%;">ผลัด</th>
                <th style="width: 15%;">ระดับความเสี่ยง</th>
                <th style="width: 15%;">สถานะการรักษา</th>
                <th style="width: 20%;">รหัสโรค : ชื่อโรค</th>
                </tr>
            </thead>
            <tbody>
                ${filteredPatients.map(patient => `
                    <tr onclick="showPatientDetails(${patient.id})" style="cursor: pointer;" title="คลิกเพื่อดูรายละเอียด">
                        <td>${patient.soldier_name}</td>
                        <td>${patient.training_unit}</td>
                        <td>${patient.rotation}</td>
                        <td>
                            ${createRiskBadge(patient.risk_level)}
                        </td>
                        <td>
                        ${patient.diagnosis_treatment_status}

                        </td>
                        <td>
                            ${formatDiseaseInfo(patient.disease_codes, patient.disease_list)}
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

            tableContainer.innerHTML = tableHtml;

            // รอให้ DOM อัปเดต แล้วเรียกใช้ DataTable script
            setTimeout(() => {
                if (typeof $ !== 'undefined' && $.fn.DataTable) {
                    if ($('.data-table').length && !$.fn.DataTable.isDataTable('.data-table')) {
                        currentDataTable = $('.data-table').DataTable({
                            "paging": true,
                            "lengthChange": true,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "autoWidth": false,
                            "responsive": true,
                            "dom": '<"d-flex justify-content-between align-items-center mb-3"' +
                                '<"d-flex align-items-center gap-3"l>' +
                                '<"d-flex align-items-center"f>>' +
                                't' +
                                '<"d-flex justify-content-between align-items-center mt-2"' +
                                '<"d-flex align-items-center"i>' +
                                '<"d-flex align-items-center justify-content-end custom-pagination"p>>',
                            "lengthMenu": [[5, 10, 20, 50, 100, -1], [5, 10, 20, 50, 100, "All"]],
                            "language": {
                                "search": "ค้นหา: ",
                                "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                                "info": "แสดง _PAGE_ จาก _PAGES_",
                                "infoEmpty": "แสดง 0 ถึง 0 จาก 0 รายการ",
                                "emptyTable": "ไม่มีข้อมูลในตาราง",
                                "zeroRecords": "<div style='text-align:center;'>ไม่พบข้อมูล</div>",
                                "paginate": {
                                    "previous": "<",
                                    "next": ">"
                                }
                            },
                            "columnDefs": [
                                {
                                    targets: [3, 4], // Risk level and status columns
                                    orderable: false
                                }
                            ],
                            "order": [[0, 'asc']]
                        });

                        // Apply existing DataTable styling
                        $('.dataTables_length label').addClass('d-flex align-items-center gap-2 m-0');
                        $('.dataTables_filter label').addClass('d-flex align-items-center gap-2 m-0');
                        $('.dataTables_length select').addClass('form-control mx-2').css('width', '80px');
                        $('.dataTables_filter input').addClass('form-control').css('width', '180px');

                        // Custom pagination function
                        function updatePaginationInfo() {
                            let pageInfo = currentDataTable.page.info();
                            let currentPage = pageInfo.page + 1;
                            let totalPages = pageInfo.pages;
                            $(".custom-pagination").html(`
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-light btn-sm prev-page">&lt;</button>
                            <span class="fw-bold">${currentPage} / ${totalPages}</span>
                            <button class="btn btn-light btn-sm next-page">&gt;</button>
                        </div>
                    `);

                            if (currentPage === 1) {
                                $(".prev-page").prop("disabled", true);
                            } else {
                                $(".prev-page").prop("disabled", false);
                            }
                            if (currentPage === totalPages) {
                                $(".next-page").prop("disabled", true);
                            } else {
                                $(".next-page").prop("disabled", false);
                            }
                        }

                        updatePaginationInfo();

                        currentDataTable.on('draw', function () {
                            updatePaginationInfo();
                        });

                        $(document).on("click", ".prev-page", function () {
                            currentDataTable.page("previous").draw("page");
                        });

                        $(document).on("click", ".next-page", function () {
                            currentDataTable.page("next").draw("page");
                        });
                    }
                }
            }, 100);
        }

        // Show patient details in modal
        function showPatientDetails(patientId) {
            const patient = allPatients.find(p => p.id === patientId);
            if (!patient) return;

            // Create modal if not exists
            if (!patientModal) {
                createPatientModal();
            }

            const modalContent = createModalContent(patient);
            if (modalBody) {
                modalBody.innerHTML = modalContent;
            }

            // Show modal
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(patientModal);
                modal.show();
            } else {
                patientModal.style.display = 'block';
            }
        }

        // Create patient modal dynamically
        function createPatientModal() {
            const modalHtml = `
        <div class="modal fade" id="patientModal" tabindex="-1" aria-labelledby="patientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-body" id="modalBody" style="background-color: #f8f9fa;">
                    </div>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            patientModal = document.getElementById('patientModal');
            modalBody = document.getElementById('modalBody');
        }

        // Close patient modal
        function closePatientModal() {
            if (patientModal) {
                if (typeof bootstrap !== 'undefined') {
                    const modal = bootstrap.Modal.getInstance(patientModal);
                    if (modal) modal.hide();
                } else {
                    patientModal.style.display = 'none';
                }
            }
        }

        // Create modal content
        function createModalContent(patient) {
            const appointmentDate = new Date(patient.appointment_date).toLocaleString('th-TH');
            const diagnosisDate = patient.diagnosis_date ? new Date(patient.diagnosis_date).toLocaleString('th-TH') : 'ไม่ระบุ';

            return `
        <div class="row mb-4">
  <div class="col-md-12 mb-4">
                <div class="d-flex align-items-center p-3 bg-white shadow-sm" style="border-radius: 12px;">
                    <div class="col-md-auto" style="width: 120px;">
                      <div class="rounded-3 border border-2 border-dark  shadow-lg me-5"
                 style="width: 100px; height: 100px; overflow: hidden; border-radius: 12px; flex-shrink: 0;">
                ${patient.soldier_image_url ? `
                    <img src="${patient.soldier_image_url}"
                         alt="รูปภาพผู้ป่วย"
                         style="width: 100%; height: 100%; object-fit: cover; display: block;"
                         onerror="this.parentElement.innerHTML='<div style=\\"width: 80px; height: 80px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 12px;\\"><svg width=\\"35\\" height=\\"35\\" viewBox=\\"0 0 24 24\\" fill=\\"none\\" xmlns=\\"http://www.w3.org/2000/svg\\"><path d=\\"M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\\" fill=\\"#6c757d\\"/></svg></div>
                ` : `
                    <div style="width: 100px; height: 100px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="#6c757d"/>
                        </svg>
                    </div>
                `}
            </div>
                    <div class="flex-grow-1">
                        <h4 class="patient-detail-name mb-1 fw-bold">${patient.soldier_name}
</h4>
                        <div class="text-muted">
                            บัตรประชาชน: ${patient.soldier_id_card || 'ไม่ระบุ'}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-4">
                <h6 class="section-title">ข้อมูลส่วนตัว</h6>

                <div class="row ms-2">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">หน่วยฝึก
                                :
                            </div>
                            <div class="text-muted" style="font-weight: bold; font-size: 14px;">
${patient.training_unit}                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">ผลัด :
                            </div>
                            <div class="text-muted" style="font-weight: bold;font-size: 14px;">
                            ${patient.rotation}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 140px;color: #495057;  font-weight: bold; font-size: 14px;">
                                หน่วยต้นสังกัด :</div>
                            <div class=" text-muted"
                                style="font-weight: bold;font-size: 14px;">
                                ${patient.affiliated_unit}
                                </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-md-12">
                <h6 class="section-title">ข้อมูลการรักษา</h6>


                <div class="row ms-2">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px;color: #495057;  font-weight: bold; font-size: 14px;">
                                วันที่เข้ารักษา :</div>
                            <div class="text-muted"
                                style="font-weight: bold;font-size: 14px;">${appointmentDate}</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px;color: #495057;  font-weight: bold; font-size: 14px;">
                                แพทย์ :</div>
                            <div class="text-muted"
                                style="font-weight: bold;font-size: 14px;">${patient.doctor_name}</div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057;  font-weight: bold; font-size: 14px;">
                                อุณหภูมิ :</div>
                            <div class=" text-muted" style="font-weight: bold; font-size: 14px;">
                            ${patient.temperature} °C
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">ความดันโลหิต :
                            </div>
                            <div class="patient-detail-status text-muted" style="font-weight: bold;font-size: 14px;">
                                                        ${patient.blood_pressure} bpm

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057;  font-weight: bold; font-size: 14px;">
                                อัตราการเต้นของหัวใจ :</div>
                            <div class="patient-detail-days text-muted" style="font-weight: bold; font-size: 14px;">
                                                        ${patient.heart_rate} mmHg

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">สถานะความเสี่ยง :
                            </div>
                            <div class=" text-muted" style="font-weight: bold;font-size: 14px;">
                             ${createRiskBadge(patient.risk_level)}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057;  font-weight: bold; font-size: 14px;">
                                สถานะการรักษา :</div>
                            <div class="text-muted" style="font-weight: bold; font-size: 14px;">
                            ${patient.diagnosis_treatment_status}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex">
                            <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">คำแนะนำการฝึก :
                            </div>
                            <div class=" text-muted" style="font-weight: bold;font-size: 14px;">
                             ${patient.training_instruction}
                            </div>
                        </div>
                    </div>
<div class="col-md-12 mb-3">
    <div class="d-flex align-items-start">
        <div style="min-width: 160px; color: #495057; font-weight: bold; font-size: 14px;">
            โรคที่วินิจฉัย :
        </div>
        <div style="font-weight: bold; font-size: 14px; line-height: 1.4;" class="text-muted">
            ${patient.diseases && patient.diseases.length > 0
                    ? patient.diseases.map((disease, index) =>
                        `${index === 0 ? '' : '<br>'}<span style="display: inline-block; min-width: 50px; margin-right: 8px;">${disease.icd10_code}</span>: ${disease.disease_name}`
                    ).join('')
                    : 'ยังไม่มีข้อมูลการวินิจฉัย'
                }
        </div>
    </div>
</div>
                </div>
            </div>

    `;
        }

        // Utility functions
        function getRiskLevelText(level) {
            const riskMap = {
                'red': 'ความเสี่ยงสูง',
                'yellow': 'ความเสี่ยงกลาง',
                'green': 'ความเสี่ยงต่ำ',
                'not-assessed': 'ไม่ได้ประเมิน'
            };
            return riskMap[level] || level;
        }

        function getRiskLevelColor(level) {
            const colorMap = {
                'red': 'danger',
                'yellow': 'warning',
                'green': 'success',
                'not-assessed': 'secondary'
            };
            return colorMap[level] || 'secondary';
        }

        // ฟังก์ชันสำหรับสร้าง Risk Badge แบบใหม่
        function createRiskBadge(level) {
            const text = getRiskLevelText(level);
            const dotColorMap = {
                'red': '#dc3545',      // สีแดง
                'yellow': '#ffc107',   // สีเหลือง
                'green': '#198754',    // สีเขียว
                'not-assessed': '#6c757d' // สีเทา
            };

            const dotColor = dotColorMap[level] || '#6c757d';

            return `
        <span class="badge bg-white text-dark border"
              style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 6px 12px; font-weight: 500;">
            <span style="display: inline-block; width: 8px; height: 8px; background-color: ${dotColor};
                         border-radius: 4px; margin-right: 6px; vertical-align: middle;"></span>
            ${text}
        </span>
    `;
        }

        function getStatusColor(status) {
            const colorMap = {
                'Admit': 'danger',
                'Refer': 'warning',
                'Discharge': 'success',
                'Follow-up': 'info'
            };
            return colorMap[status] || 'secondary';
        }

        function getStatusText(status) {
            const statusMap = {
                'Admit': 'Admit',
                'Refer': 'Refer',
                'Discharge': 'Discharge',
                'Follow-up': 'Follow-up'
            };
            return statusMap[status] || status;
        }

        function showLoading(show) {
            console.log(`Loading state: ${show ? 'loading' : 'completed'}`);
        }

        // Export functions for global use
        window.PatientDashboard = {
            loadData,
            getFilteredPatients: () => filteredPatients,
            getAllPatients: () => allPatients,
            showPatientDetails,
            resetFilters
        };
        // เพิ่มฟังก์ชันสำหรับอัปเดตหัวข้อตามตัวกรอง
        function updateHeaderTitle() {
            const headerElement = document.getElementById('header-title');
            if (!headerElement) return;

            let mainTitle = 'ติดตามผู้ป่วย ER';
            let dateInfo = '';
            let filterInfo = '';
            let countInfo = '';

            // เพิ่มข้อมูลวันที่
            if (dateFilter) {
                if (dateFilter.value === 'today') {
                    const today = new Date().toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    dateInfo = `วันที่ ${today}`;
                } else if (dateFilter.value === 'specific_date' && specificDate && specificDate.value) {
                    const selectedDate = new Date(specificDate.value).toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    dateInfo = `วันที่ ${selectedDate}`;
                }
            } else {
                // Default case - แสดงวันที่วันนี้
                const today = new Date().toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                dateInfo = `วันที่ ${today}`;
            }

            // เพิ่มข้อมูลระดับความเสี่ยง
            if (riskLevelFilter && riskLevelFilter.value !== 'all') {
                const riskText = {
                    'red': 'ความเสี่ยงสูง',
                    'yellow': 'ความเสี่ยงปานกลาง',
                    'green': 'ความเสี่ยงต่ำ'
                };
                if (riskText[riskLevelFilter.value]) {
                    filterInfo = `${riskText[riskLevelFilter.value]}`;
                }
            }

            // เพิ่มจำนวนผู้ป่วยที่พบ
            if (filteredPatients && filteredPatients.length > 0) {
                countInfo = `(${filteredPatients.length} ราย)`;
            }

            // สร้างหัวข้อแบบใหม่
            let titleHTML = `<span style="font-weight: 700; font-size: 1.5rem;">${mainTitle}</span>`;

            if (dateInfo) {
                titleHTML += ` <span style="font-weight: 600; font-size: 1.2rem; color: #495057;">- ${dateInfo}</span>`;
            }

            if (filterInfo) {
                titleHTML += ` <span style="font-weight: 400; font-size: 1rem; color: #6c757d;">- ${filterInfo}</span>`;
            }

            if (countInfo) {
                titleHTML += ` <span style="font-weight: 500; font-size: 1.1rem; color: #495057;">${countInfo}</span>`;
            }

            // อัปเดตหัวข้อ
            headerElement.innerHTML = titleHTML;
        }

        // เพิ่มฟังก์ชันสำหรับรีเซ็ตหัวข้อ
        function resetHeaderTitle() {
            const headerElement = document.getElementById('header-title');
            if (headerElement) {
                headerElement.innerHTML = '<span style="font-weight: 700; font-size: 1.5rem;">ติดตามผู้ป่วย ER</span>';
            }
        }

        // แก้ไขฟังก์ชัน loadData() เพื่อเรียกใช้การอัปเดตหัวข้อ
        async function loadData() {
            try {
                const response = await callAPI();
                const processedResponse = processApiResponse(response);

                if (processedResponse.success) {
                    allPatients = processedResponse.data.appointments;
                    applyFilters();
                    updateHeaderTitle(); // เพิ่มบรรทัดนี้
                    displayTable();
                } else {
                    throw new Error(processedResponse.message || 'Failed to load data');
                }

            } catch (error) {
                console.error('Error loading data:', error);
                resetHeaderTitle(); // รีเซ็ตหัวข้อเมื่อเกิดข้อผิดพลาด
                if (tableContainer) {
                    tableContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                    <h5>เกิดข้อผิดพลาดในการโหลดข้อมูล</h5>
                    <p class="mb-2">${error.message}</p>
                    <button class="btn btn-primary" onclick="loadData()">
                        <i class="fas fa-redo me-1"></i> ลองใหม่
                    </button>
                </div>
            `;
                }
                if (statsContainer) {
                    statsContainer.remove();
                    statsContainer = null;
                }
            }
        }

        // แก้ไขฟังก์ชัน resetFilters() เพื่อรีเซ็ตหัวข้อด้วย
        function resetFilters() {
            if (dateFilter) dateFilter.value = 'today';
            if (riskLevelFilter) riskLevelFilter.value = 'all';
            if (specificDate) specificDate.value = new Date().toISOString().split('T')[0];
            toggleDateInput();

            // รีเซ็ตหัวข้อและโหลดข้อมูลใหม่
            setTimeout(() => {
                loadData();
            }, 100);
        }

        // แก้ไขฟังก์ชัน applyFilters() เพื่อเรียกใช้การอัปเดตหัวข้อ
        function applyFilters() {
            filteredPatients = allPatients.filter(patient => {
                // Risk level filter
                if (riskLevelFilter && riskLevelFilter.value !== 'all' && patient.risk_level !== riskLevelFilter.value) {
                    return false;
                }
                return true;
            });

            // อัปเดตหัวข้อหลังจากกรอง
            updateHeaderTitle();
        }

        // เพิ่มการอัปเดตหัวข้อเมื่อเปิดแอป
        document.addEventListener('DOMContentLoaded', () => {
            initializeDOMElements();
            setupEventListeners();

            // ตั้งหัวข้อเริ่มต้น
            resetHeaderTitle();

            // Load initial data
            loadData();
        });


    </script>
    <script>
        // เพิ่มโค้ดนี้ในส่วน script ที่มีอยู่แล้ว

        // ฟังก์ชันสร้างปุ่มดูทั้งหมด
        function createViewAllButton(riskLevel, count) {
            const buttonStyles = {
                red: {
                    bgColor: '#dc3545',
                    textColor: 'white',
                    hoverBg: '#c82333'
                },
                yellow: {
                    bgColor: 'rgb(228, 114, 0)',
                    textColor: 'white',
                    hoverBg: 'rgb(200, 100, 0)'
                },
                green: {
                    bgColor: '#28a745',
                    textColor: 'white',
                    hoverBg: '#218838'
                }
            };

            const style = buttonStyles[riskLevel];

            return `
        <div class="text-center p-2" style="border-top: 1px solid #f0f0f0;">
            <button class="btn btn-sm w-100"
                    onclick="viewAllRisk('${riskLevel}')"
                    style="
                        background-color: ${style.bgColor};
                        color: ${style.textColor};
                        border: none;
                        font-size: 11px;
                        padding: 6px 12px;
                        transition: all 0.2s ease;
                    "
                    onmouseover="this.style.backgroundColor='${style.hoverBg}'"
                    onmouseout="this.style.backgroundColor='${style.bgColor}'"
                    title="กรองและแสดงผู้ป่วยระดับนี้">
               ดูทั้งหมด
            </button>
        </div>
    `;
        }

        // ฟังก์ชันเปิดตัวกรองพร้อมตั้งค่า (อัปเดตใหม่)
        function viewAllRisk(riskLevel) {
            // สร้าง filter modal ถ้ายังไม่มี (เพื่อให้มี element ที่จำเป็น)
            if (!filterModal) {
                createFilterModal();
            }

            // ตั้งค่าตัวกรองโดยไม่เปิด modal
            if (riskLevelFilter) {
                riskLevelFilter.value = riskLevel;
            }

            // เรียกใช้การกรองและแสดงผลทันที
            applyFilters();
            displayTable();

            console.log(`Filtered to show ${riskLevel} risk level patients`);
        }

        // อัปเดตฟังก์ชัน updatePatientRiskLists
        function updatePatientRiskLists() {
            console.log('updatePatientRiskLists called with:', allPatients ? allPatients.length : 0, 'patients');

            // ตรวจสอบว่ามีข้อมูลผู้ป่วยหรือไม่
            if (!allPatients || allPatients.length === 0) {
                // ถ้าไม่มีข้อมูล แสดง empty state ทุกช่อง
                document.getElementById('high-risk-list').innerHTML = createEmptyState('red');
                document.getElementById('medium-risk-list').innerHTML = createEmptyState('yellow');
                document.getElementById('low-risk-list').innerHTML = createEmptyState('green');

                document.getElementById('high-risk-count').textContent = '0 ราย';
                document.getElementById('medium-risk-count').textContent = '0 ราย';
                document.getElementById('low-risk-count').textContent = '0 ราย';
                return;
            }

            // กรองผู้ป่วยตามระดับความเสี่ยง
            const highRiskPatients = allPatients.filter(p => p.risk_level === 'red');
            const mediumRiskPatients = allPatients.filter(p => p.risk_level === 'yellow');
            const lowRiskPatients = allPatients.filter(p => p.risk_level === 'green');

            console.log('Risk levels:', {
                high: highRiskPatients.length,
                medium: mediumRiskPatients.length,
                low: lowRiskPatients.length
            });

            // อัปเดตจำนวนผู้ป่วย
            const highRiskCountElement = document.getElementById('high-risk-count');
            const mediumRiskCountElement = document.getElementById('medium-risk-count');
            const lowRiskCountElement = document.getElementById('low-risk-count');

            if (highRiskCountElement) {
                highRiskCountElement.textContent = `${highRiskPatients.length} ราย`;
            }
            if (mediumRiskCountElement) {
                mediumRiskCountElement.textContent = `${mediumRiskPatients.length} ราย`;
            }
            if (lowRiskCountElement) {
                lowRiskCountElement.textContent = `${lowRiskPatients.length} ราย`;
            }

            // อัปเดตรายการผู้ป่วยความเสี่ยงสูง
            const highRiskList = document.getElementById('high-risk-list');
            if (highRiskList) {
                if (highRiskPatients.length > 0) {
                    // แสดงแค่ 2 รายการแรก
                    const limitedPatients = highRiskPatients.slice(0, 2);
                    let content = `
                <div style="flex: 1; overflow-y: auto; padding: 0;">
                    ${limitedPatients.map(createPatientItem).join('')}
                </div>
            `;

                    // เพิ่มปุ่มดูทั้งหมดที่ท้ายสุด
                    content += createViewAllButton('red', highRiskPatients.length);

                    highRiskList.innerHTML = content;
                    highRiskList.style.display = 'flex';
                    highRiskList.style.flexDirection = 'column';
                } else {
                    highRiskList.innerHTML = createEmptyState('red');
                    highRiskList.style.display = 'flex';
                }
            }

            // อัปเดตรายการผู้ป่วยความเสี่ยงกลาง
            const mediumRiskList = document.getElementById('medium-risk-list');
            if (mediumRiskList) {
                if (mediumRiskPatients.length > 0) {
                    // แสดงแค่ 2 รายการแรก
                    const limitedPatients = mediumRiskPatients.slice(0, 2);
                    let content = `
                <div style="flex: 1; overflow-y: auto; padding: 0;">
                    ${limitedPatients.map(createPatientItem).join('')}
                </div>
            `;

                    // เพิ่มปุ่มดูทั้งหมดที่ท้ายสุด
                    content += createViewAllButton('yellow', mediumRiskPatients.length);

                    mediumRiskList.innerHTML = content;
                    mediumRiskList.style.display = 'flex';
                    mediumRiskList.style.flexDirection = 'column';
                } else {
                    mediumRiskList.innerHTML = createEmptyState('yellow');
                    mediumRiskList.style.display = 'flex';
                }
            }

            // อัปเดตรายการผู้ป่วยความเสี่ยงต่ำ
            const lowRiskList = document.getElementById('low-risk-list');
            if (lowRiskList) {
                if (lowRiskPatients.length > 0) {
                    // แสดงแค่ 2 รายการแรก
                    const limitedPatients = lowRiskPatients.slice(0, 2);
                    let content = `
                <div style="flex: 1; overflow-y: auto; padding: 0;">
                    ${limitedPatients.map(createPatientItem).join('')}
                </div>
            `;

                    // เพิ่มปุ่มดูทั้งหมดที่ท้ายสุด
                    content += createViewAllButton('green', lowRiskPatients.length);

                    lowRiskList.innerHTML = content;
                    lowRiskList.style.display = 'flex';
                    lowRiskList.style.flexDirection = 'column';
                } else {
                    lowRiskList.innerHTML = createEmptyState('green');
                    lowRiskList.style.display = 'flex';
                }
            }

            console.log('Updated risk lists completed');
        }

        // ฟังก์ชันสร้าง HTML สำหรับรายการผู้ป่วย (ใช้เดิม)
        function createPatientItem(patient) {
            return `
    <div class="patient-item" onclick="showPatientDetails(${patient.id})" style="
        margin: 6px 8px 8px 8px;
        padding: 10px 12px;
        background: white;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.2s ease;
    ">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="patient-name" style="font-weight: 600; font-size: 13px; color: #333;">
                ${patient.soldier_name}
            </div>
            <span class="badge bg-white text-dark" style="box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15); border: 1px solid #dee2e6; font-size: 10px;">
                ${patient.diagnosis_treatment_status || 'ไม่ระบุ'}
            </span>
        </div>
        <div class="patient-details" style="font-size: 11px; color: #666; line-height: 1.4;">
            <div style="margin-bottom: 2px;">${patient.rotation || 'ไม่ระบุ'}</div>
            <div>${patient.training_unit || 'ไม่ระบุ'}</div>
        </div>
    </div>
`;
        }

        // ฟังก์ชันสร้าง Empty State (แก้ไขแล้ว)
        function createEmptyState(riskLevel) {
            const emptyStates = {
                red: {
                    icon: 'fas fa-user-injured',
                    message: 'ไม่มีผู้ป่วยความเสี่ยงสูง'
                },
                yellow: {
                    icon: 'fas fa-exclamation-triangle',
                    message: 'ไม่มีผู้ป่วยความเสี่ยงกลาง'
                },
                green: {
                    icon: 'fas fa-check-circle',
                    message: 'ไม่มีผู้ป่วยความเสี่ยงต่ำ'
                }
            };

            const state = emptyStates[riskLevel];
            return `
        <div class="empty-state" style="
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
            box-sizing: border-box;
        ">
            <i class="${state.icon}" style="
                font-size: 2.5rem;
                margin-bottom: 15px;
                opacity: 0.4;
                display: block;
            "></i>
            <div style="
                font-size: 14px;
                line-height: 1.5;
                max-width: 200px;
                word-wrap: break-word;
            ">${state.message}</div>
        </div>
    `;
        }

        // อัปเดตฟังก์ชัน loadData และ applyFilters (เพิ่มการเรียกใช้ updatePatientRiskLists)
        // แทนที่ loadData function เดิม
        async function loadData() {
            try {
                const response = await callAPI();
                const processedResponse = processApiResponse(response);

                if (processedResponse.success) {
                    allPatients = processedResponse.data.appointments;
                    applyFilters();
                    updateHeaderTitle();
                    displayTable();

                    // เพิ่มบรรทัดนี้
                    updatePatientRiskLists();

                } else {
                    throw new Error(processedResponse.message || 'Failed to load data');
                }

            } catch (error) {
                console.error('Error loading data:', error);
                resetHeaderTitle();

                // แสดง empty state เมื่อเกิดข้อผิดพลาด
                updatePatientRiskLists();

                if (tableContainer) {
                    tableContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle mb-2" style="font-size: 2rem;"></i>
                    <h5>เกิดข้อผิดพลาดในการโหลดข้อมูล</h5>
                    <p class="mb-2">${error.message}</p>
                    <button class="btn btn-primary" onclick="loadData()">
                        <i class="fas fa-redo me-1"></i> ลองใหม่
                    </button>
                </div>
            `;
                }
            }
        }

        // แทนที่ applyFilters function เดิม
        function applyFilters() {
            filteredPatients = allPatients.filter(patient => {
                // Risk level filter
                if (riskLevelFilter && riskLevelFilter.value !== 'all' && patient.risk_level !== riskLevelFilter.value) {
                    return false;
                }
                return true;
            });

            // อัปเดตหัวข้อหลังจากกรอง
            updateHeaderTitle();

            // เพิ่มบรรทัดนี้
            updatePatientRiskLists();
        }
    </script>

</body>

</html>