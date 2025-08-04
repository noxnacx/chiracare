/**
 * AppointmentSearch Module
 * File: public/js/components/searchappointment.js
 * Description: ระบบค้นหาการนัดหมาย (Appointment Search System)
 */

class AppointmentSearch {
    constructor() {
        this.searchInput = document.getElementById('appointmentSearchInput');
        this.initialState = document.getElementById('appointmentInitialState');
        this.loading = document.getElementById('appointmentLoading');
        this.noResults = document.getElementById('appointmentNoResults');
        this.resultsContainer = document.getElementById('appointmentResultsContainer');

        this.init();
    }

    init() {
        if (!this.searchInput) {
            console.error('Appointment search input not found');
            return;
        }

        // Add event listeners
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));

        // Handle clear search button
        document.addEventListener('click', (e) => {
            if (e.target && (e.target.id === 'clearAppointmentSearch' || e.target.closest('#clearAppointmentSearch'))) {
                this.clearSearch();
            }
        });

        // Add component-specific styles
        this.addAppointmentStyles();
        this.showInitialState();

        console.log('AppointmentSearch module initialized');
    }

    handleSearch(e) {
        const query = e.target.value.trim();

        if (query.length > 0) {
            if (this.initialState) this.initialState.style.display = 'none';
        } else {
            this.showInitialState();
            this.clearAppointmentResults();
            return;
        }

        if (query.length < 2) {
            this.hideAllStates();
            this.clearAppointmentResults();
            return;
        }

        this.showLoading();
        this.clearAppointmentResults();
        this.searchAppointments(query);
    }

    showInitialState() {
        if (this.initialState) this.initialState.style.display = 'block';
        if (this.resultsContainer) this.resultsContainer.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
        if (this.loading) this.loading.style.display = 'none';
    }

    showLoading() {
        if (this.loading) this.loading.style.display = 'block';
        if (this.resultsContainer) this.resultsContainer.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
    }

    hideAllStates() {
        if (this.resultsContainer) this.resultsContainer.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
        if (this.loading) this.loading.style.display = 'none';
    }

    clearSearch() {
        this.searchInput.value = '';
        this.showInitialState();
        this.clearAppointmentResults();
        this.searchInput.focus();
    }

    clearAppointmentResults() {
        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = '';
        }
    }

    async searchAppointments(query) {
        try {
            // API call for appointment search
            const response = await fetch(`/search-appointments?query=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (this.loading) this.loading.style.display = 'none';

            if (data.status === 'found') {
                this.updateAppointmentResults(data.data);
                if (this.resultsContainer) this.resultsContainer.style.display = 'block';
                if (this.noResults) this.noResults.style.display = 'none';
            } else {
                this.showNoResults();
            }
        } catch (error) {
            if (this.loading) this.loading.style.display = 'none';
            console.error('Error:', error);
            this.showError(error.message);
        }
    }

    updateAppointmentResults(data) {
        try {
            if (!data || !Array.isArray(data)) {
                console.error('Invalid appointment data');
                return;
            }
            this.displayResults(data);
        } catch (error) {
            console.error('Error updating appointment results:', error);
        }
    }

    displayResults(data) {
        let html = '';
        data.forEach(soldierData => {
            const soldier = soldierData.soldier_info;
            const appointments = soldierData.appointments;

            html += `
<div class="patient-card-appointment">
    <div class="patient-header-appointment">
        <div class="patient-profile-appointment">
            <!-- ข้อมูลทางขวา -->
            <div class="patient-info-appointment">
                <div class="patient-basic-appointment">
                    <!-- ใช้ margin แทน -->
                    <div class="patient-main-info-appointment d-flex align-items-center p-3">
                        <!-- รูปภาพ -->
                       <div class="rounded-3 border border-3 border-black shadow"
                             style="width: 80px; height: 80px; overflow: hidden; margin-right: 1rem; border-radius: 16px;">
                            ${soldier.soldier_image ? `
                                <img src="${soldier.soldier_image}"
                                     alt="รูปภาพทหาร"
                                     style="width: 100%; height: 100%; object-fit: cover;"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik00MCAyNEMzNiAyNCAzMyAyNyAzMyAzMUMzMyAzNSAzNiAzOCA0MCAzOEM0NCAzOCA0NyAzNSA0NyAzMUM0NyAyNyA0NCAyNCA0MCAyNFoiIGZpbGw9IiM2Yzc1N2QiLz4KPHBhdGggZD0iTTQwIDQyQzMzIDQyIDI3IDQ1IDI3IDUyVjU2SDUzVjUyQzUzIDQ1IDQ3IDQyIDQwIDQyWiIgZmlsbD0iIzZjNzU3ZCIvPgo8L3N2Zz4K'">
                            ` : `
                                <div style="width: 80px; height: 80px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 16px;">
                                    <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="#6c757d"/>
                                    </svg>
                                </div>
                            `}
                        </div>

                        <!-- Patient Info -->
                        <div class="flex-grow-1">
                            <h3 class="patient-name-appointment mb-2">
                                ${soldier.name}
                            </h3>
                            <div class="patient-id-appointment text-muted">
                                บัตรประชาชน: ${soldier.soldier_id_card || 'ไม่ระบุ'}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="patient-info-sections-appointment mt-2">
                    <!-- ส่วนบน: ข้อมูลส่วนตัว -->
                    <div class="info-section-appointment">
                        <h6 class="section-title-appointment">ข้อมูลส่วนตัว</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>หน่วยฝึก:</strong>
                                    <span>${soldier.training_unit_name || 'ไม่ระบุ'}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ผลัด:</strong>
                                    <span>${soldier.rotation_name || 'ไม่ระบุ'}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>หน่วยต้นสังกัด:</strong>
                                    <span>${soldier.affiliated_unit || 'ไม่ระบุ'}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ระยะเวลา:</strong>
                                    <span>${soldier.service_info?.service_duration || 'ไม่ระบุ'} เดือน</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ส่วนล่าง: ข้อมูลสุขภาพ -->
                    <div class="info-section-appointment">
                        <h6 class="section-title-appointment">ข้อมูลสุขภาพ</h6>
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>น้ำหนัก:</strong>
                                    <span>${soldier.physical_info?.weight_kg || 'ไม่ระบุ'} kg.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ส่วนสูง:</strong>
                                    <span>${soldier.physical_info?.height_cm || 'ไม่ระบุ'} cm.</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>โรคประจำตัว:</strong>
                                    <span>${soldier.medical_info?.underlying_diseases || 'ไม่มีโรคประจำตัว'}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex gap-2">
                                    <strong>ประวัติแพ้ยา/อาหาร:</strong>
                                    <span>${soldier.medical_info?.allergy_food_history || 'ไม่มีประวัติแพ้'}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<h5><span style="font-size: 20px; font-weight: 600; color: #212529;">
                       รายการนัดหมาย </span>
                    </h5>
<div class="appointments-list-appointment">
    ${appointments && appointments.length > 0
                    ? appointments.map(appointment => this.createAppointmentHTML(appointment)).join('')
                    : this.createAppointmentHTML(null)
                }
</div>
            `;
        });

        if (this.resultsContainer) {
            this.resultsContainer.innerHTML = html;
        }
    }

    createAppointmentHTML(appointment) {
        if (!appointment || appointment === null || appointment === undefined || Object.keys(appointment).length === 0) {
            console.log('No appointment data, showing empty message');
            return `
            <div class="no-appointment-message-appointment text-center p-4" style="background-color: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 12px; margin: 1rem 0;">
                <div class="no-appointment-content-appointment">
                    <h5 style="color: #6c757d; margin-bottom: 0.5rem; font-weight: 600;">ยังไม่มีข้อมูลการนัดหมาย</h5>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.9rem;">ไม่พบการนัดหมายสำหรับผู้ป่วยรายนี้</p>
                </div>
            </div>
        `;
        }

        const isFollowUp = appointment.is_follow_up;
        const isCritical = appointment.case_type === 'critical';

        let itemClass = 'appointment-search-item';
        if (isCritical) itemClass += ' critical';
        if (isFollowUp) itemClass += ' follow-up';

        return `
        <div class="${itemClass}">
            <!-- บรรทัดแรก: นัดหมายวันที่ + เวลา | ประเภทเคส + สถานะ -->
            <div class="row align-items-center mb-2">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span><strong>นัดหมายวันที่</strong> ${appointment.appointment_date_thai.replace(' เวลา ', ' <strong>เวลา</strong> ')}</span>
                        ${isFollowUp ? '<span class="badge bg-warning text-dark ms-2"><i class="fas fa-star me-1"></i>นัดติดตาม</span>' : ''}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center gap-2">
                        <span class="badge ${appointment.case_type === 'critical' ? 'bg-danger' : 'bg-success'}">
                            ${appointment.case_type_thai}
                        </span>
                    </div>
                </div>
            </div>

            <!-- บรรทัดที่สอง: สถานที่ | ประเภทการนัด -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span><strong>สถานที่:</strong> ${appointment.appointment_location_thai}</span>
                    </div>
                </div>
                 <div class="col-md-6 text-md-end">
                    ${isFollowUp ? `
                        <div class="d-flex justify-content-md-end align-items-center">
                            <i class="fas fa-clipboard-list text-muted me-2"></i>
                            <span class="text-muted">${appointment.follow_up_text}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    }

    showNoResults() {
        if (this.resultsContainer) this.resultsContainer.style.display = 'none';
        if (this.noResults) {
            this.noResults.style.display = 'block';
            this.noResults.innerHTML = `
                <div class="card border-danger border-2 border-opacity-25">
                    <div class="card-body p-4 text-center">
                        <h6 class="card-title text-danger">ไม่พบข้อมูลการนัดหมาย</h6>
                        <p class="card-text text-muted mb-3">กรุณาตรวจสอบชื่อ-นามสกุล หรือ เลขประจำตัวทหารอีกครั้ง</p>
                    </div>
                </div>
            `;
        }
    }

    showError(message) {
        if (this.noResults) {
            this.noResults.innerHTML = `
                <div class="card border-danger border-2 border-opacity-25">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-danger fa-4x mb-3 opacity-75"></i>
                        <h6 class="card-title text-danger">เกิดข้อผิดพลาดในการค้นหา</h6>
                        <p class="card-text text-muted mb-3">${message}</p>
                        <button id="clearAppointmentSearch" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>
                            ลองใหม่อีกครั้ง
                        </button>
                    </div>
                </div>
            `;
            this.noResults.style.display = 'block';
        }
    }

    addAppointmentStyles() {
        if (!document.getElementById('appointmentSearchStyles')) {
            const styleElement = document.createElement('style');
            styleElement.id = 'appointmentSearchStyles';
            styleElement.innerHTML = `
                /* Appointment Search Specific Styles */
                .appointment-search-item {
                    border: 1px solid #e3e6ea;
                    border-radius: 12px;
                    padding: 16px;
                    margin-bottom: 12px;
                    background: white;
                    transition: all 0.2s ease;
                }

                .appointment-search-item:hover {
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }

                .appointment-search-item.critical {
                    border-left: 4px solid #dc3545;
                }

                .appointment-search-item.follow-up {
                    background-color: #fff8e1;
                }

                .patient-card-appointment {
                    border: 1px solid #dee2e6;
                    border-radius: 16px;
                    margin-bottom: 20px;
                    background: white;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                }

                .patient-header-appointment {
                    padding: 20px;
                    border-bottom: 1px solid #e9ecef;
                }

                .patient-main-info-appointment {
                    background: #f8f9fa;
                    border-radius: 12px;
                }

                .patient-name-appointment {
                    font-size: 1.25rem;
                    font-weight: 600;
                    color: #212529;
                    margin: 0;
                }

                .section-title-appointment {
                    font-weight: 600;
                    color: #495057;
                    margin-bottom: 12px;
                    padding-bottom: 8px;
                    border-bottom: 2px solid #e9ecef;
                }

                .info-section-appointment {
                    background: #f8f9fa;
                    padding: 16px;
                    border-radius: 12px;
                    margin-bottom: 16px;
                }

                .no-appointment-message-appointment {
                    text-align: center;
                    padding: 2rem;
                    background-color: #f8f9fa;
                    border: 2px dashed #dee2e6;
                    border-radius: 12px;
                    margin: 1rem 0;
                }

                @media (max-width: 768px) {
                    .patient-card-appointment {
                        margin-bottom: 15px;
                    }

                    .patient-header-appointment {
                        padding: 15px;
                    }

                    .appointment-search-item {
                        padding: 12px;
                    }
                }
            `;
            document.head.appendChild(styleElement);
        }
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AppointmentSearch;
}

// Global instance variable
let appointmentSearchInstance;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    try {
        appointmentSearchInstance = new AppointmentSearch();
        console.log('AppointmentSearch component loaded successfully');
    } catch (error) {
        console.error('Failed to initialize AppointmentSearch:', error);
    }
});