/**
 * PatientSearch Module
 * File: public/js/daily-treatment.js
 * Description: ระบบค้นหาผู้ป่วยและประวัติการรักษา (Patient Search & Medical History System)
 */

class PatientSearch {
    constructor() {
        this.searchInput = document.getElementById('patientSearchInput');
        this.patientCard = document.getElementById('patientCard');
        this.noResults = document.getElementById('patientNoResults');
        this.loading = document.getElementById('patientLoading');
        this.initialState = document.getElementById('patientInitialState');

        this.init();
    }

    init() {
        if (!this.searchInput) {
            console.error('Patient search input not found');
            return;
        }

        this.searchInput.addEventListener('input', (e) => this.handleSearch(e));

        // Handle clear search button
        document.addEventListener('click', (e) => {
            if (e.target && (e.target.id === 'clearPatientSearch' || e.target.closest('#clearPatientSearch'))) {
                this.clearSearch();
            }
        });

        this.addMedicalHistoryStyles();
        this.showInitialState();

        console.log('PatientSearch module initialized');
    }

    handleSearch(e) {
        const query = e.target.value.trim();

        if (query.length > 0) {
            if (this.initialState) this.initialState.style.display = 'none';
        } else {
            this.showInitialState();
            this.clearMedicalHistoryContent();
            return;
        }

        if (query.length < 2) {
            this.hideAllStates();
            this.clearMedicalHistoryContent();
            return;
        }

        this.showLoading();
        this.clearMedicalHistoryContent();
        this.searchPatient(query);
    }

    showInitialState() {
        if (this.initialState) this.initialState.style.display = 'block';
        if (this.patientCard) this.patientCard.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
        if (this.loading) this.loading.style.display = 'none';
    }

    showLoading() {
        if (this.loading) this.loading.style.display = 'block';
        if (this.patientCard) this.patientCard.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
    }

    hideAllStates() {
        if (this.patientCard) this.patientCard.style.display = 'none';
        if (this.noResults) this.noResults.style.display = 'none';
        if (this.loading) this.loading.style.display = 'none';
    }

    clearSearch() {
        this.searchInput.value = '';
        this.showInitialState();
        this.clearMedicalHistoryContent();
        this.searchInput.focus();
    }

    clearMedicalHistoryContent() {
        const medicalHistoryElement = document.getElementById('medicalHistory');
        if (medicalHistoryElement) {
            medicalHistoryElement.innerHTML = '';
        }
    }

    async searchPatient(query) {
        try {
            // API call for patient search
            const response = await fetch(`/search-patient?query=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (this.loading) this.loading.style.display = 'none';

            if (data.status === 'found') {
                this.updatePatientCard(data.patient, data.medical_history);
                if (this.patientCard) this.patientCard.style.display = 'block';
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

    updatePatientCard(patient, medicalHistory) {
        try {
            if (!patient || typeof patient !== 'object') {
                console.error('Invalid patient data');
                return;
            }

            const profileContainer = document.getElementById('uniqueProfileContainer');
            if (profileContainer) {
                // เคลียร์และลบทุกอย่าง
                while (profileContainer.firstChild) {
                    profileContainer.removeChild(profileContainer.firstChild);
                }

                if (patient.soldier_image) {
                    let filename = patient.soldier_image.split('/').pop();
                    let imageUrl = `/uploads/soldiers/${filename}`;

                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.style.cssText = 'width: 80px; height: 80px; object-fit: cover;';
                    img.alt = 'Patient Image';
                    img.onerror = function () {
                        this.remove();
                        const placeholder = document.createElement('div');
                        placeholder.className = 'd-flex align-items-center justify-content-center text-secondary';
                        placeholder.style.cssText = 'background-color: rgba(108, 117, 125, 0.1); width: 80px; height: 80px; font-size: 32px;';
                        placeholder.innerHTML = '<i class="fas fa-user"></i>';
                        profileContainer.appendChild(placeholder);
                    };
                    profileContainer.appendChild(img);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'd-flex align-items-center justify-content-center text-secondary';
                    placeholder.style.cssText = 'background-color: rgba(108, 117, 125, 0.1); width: 80px; height: 80px; font-size: 32px;';
                    placeholder.innerHTML = '<i class="fas fa-user"></i>';
                    profileContainer.appendChild(placeholder);
                }
            }

            // Update patient name
            const nameElement = document.getElementById('patientName');
            if (nameElement) {
                nameElement.textContent = `${patient.first_name || ''} ${patient.last_name || ''}`.trim() || 'ไม่ระบุชื่อ';
            }

            // Update soldier ID
            const idElement = document.getElementById('patientId');
            if (idElement) {
                idElement.textContent = `เลขบัตรประชาชน: ${patient.soldier_id_card || 'ไม่ระบุ'}`;
            }

            // Update rotation badge
            const rotationBadge = document.getElementById('rotationBadge');
            if (rotationBadge) {
                rotationBadge.innerHTML = `<i class="fas fa-users"></i> ${patient.rotation_name || 'ผลัด'}`;
            }

            // Update basic patient information
            const fieldsToUpdate = [
                { id: 'trainingUnit', value: patient.training_unit_name || '-' },
                { id: 'affiliatedUnit', value: patient.affiliated_unit || '-' },
                { id: 'serviceDuration', value: patient.service_duration ? `${patient.service_duration} เดือน` : '-' },
                { id: 'chronicDisease', value: patient.underlying_diseases || 'ไม่มี' },
                { id: 'weight', value: patient.weight_kg ? `${patient.weight_kg} kg.` : '-' },
                { id: 'height', value: patient.height_cm ? `${patient.height_cm} cm.` : '-' },
                { id: 'drugAllergy', value: patient.medical_allergy_food_history || 'ไม่มี' }
            ];

            fieldsToUpdate.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.textContent = field.value;
                }
            });

            // Update medical history
            this.updateMedicalHistoryVariables(medicalHistory);

        } catch (error) {
            console.error('Error updating patient card:', error);
        }
    }

    updateMedicalHistoryVariables(medicalHistory) {
        const medicalHistoryElement = document.getElementById('medicalHistory');

        if (!medicalHistoryElement) return;

        if (medicalHistory && medicalHistory.length > 0) {
            let historyHTML = `
<div class="card border-0 shadow-sm mb-4" style="border: 2px solid #dee2e6; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;">
   <div class="card-header bg-light border-0" style="border-radius: 16px 16px 0 0; background: white !important;">
       <div class="d-flex justify-content-between align-items-center">
           <div>
               <h6  style="color: var(--text-color);">ประวัติการรักษา</h6>
               <small class="text-muted">ประวัติการรักษารายบุคคล</small>
           </div>
           <span class="badge" style="background-color: var(--accent-color); border-radius: 12px; padding: 6px 12px; font-weight: 600; color: #7c3aed;">
   ${medicalHistory.length} รายการ
</span>
       </div>
   </div>
</div>

<div class="history-list" style="padding: 0 2px; border-radius: 0 0 16px 16px;">
       `;

            medicalHistory.forEach((record, index) => {
                const diagnosisDate = record.diagnosis_date ?
                    new Date(record.diagnosis_date).toLocaleDateString('th-TH') : 'ไม่ระบุ';
                const isLatest = index === 0;
                const collapseId = `historyItem${index}`;

                // ฟังก์ชันสำหรับแสดง vital signs
                const formatVitalSigns = (record) => {
                    if (!record.temperature && !record.blood_pressure && !record.heart_rate) {
                        return '';
                    }

                    return `
<div class="card mb-4 border border-light-subtle shadow-sm rounded-3">
        <div class="card-body">
          <h6 class="section-title fw-bold  mb-3">
            สัญญาณชีพ
          </h6>
         <div class="row g-3">
  <!-- อุณหภูมิ -->
  <div class="col-md-4">
    <div class="p-2 bg-white rounded-3 text-center d-flex flex-column justify-content-center shadow-sm"
         style="height: 100px; border: 1px solid #e0e0e0; border-radius: 12px;">
      <div class="fw-bold mb-1 text-muted" style="font-size: 0.9rem;">อุณหภูมิ</div>
      <div class="h5 fw-bold mt-1">${record.temperature || '-'}°C</div>
    </div>
  </div>

  <!-- ความดันโลหิต -->
  <div class="col-md-4">
    <div class="p-2 bg-white rounded-3 text-center d-flex flex-column justify-content-center shadow-sm"
         style="height: 100px; border: 1px solid #e0e0e0; border-radius: 12px;">
      <div class="fw-bold mb-1 text-muted" style="font-size: 0.9rem;">ความดันโลหิต</div>
      <div class="h5 fw-bold mt-1">${record.blood_pressure || '-'}</div>
    </div>
  </div>

  <!-- อัตราการเต้นหัวใจ -->
  <div class="col-md-4">
    <div class="p-2 bg-white rounded-3 text-center d-flex flex-column justify-content-center shadow-sm"
         style="height: 100px; border: 1px solid #e0e0e0; border-radius: 12px;">
      <div class="fw-bold mb-1 text-muted" style="font-size: 0.9rem;">อัตราการเต้นหัวใจ</div>
      <div class="h5 fw-bold mt-1">${record.heart_rate || '-'} bpm</div>
    </div>
  </div>
</div>
          ${record.vital_recorded_at ? `
          <div class="mt-2 text-center">
            <small class="text-muted">
              <i class="fas fa-clock me-1"></i>
              บันทึกเมื่อ: ${new Date(record.vital_recorded_at).toLocaleString('th-TH')}
            </small>
          </div>
          ` : ''}
        </div>
      </div>
    `;
                };

                historyHTML += `

        <div class="card mb-3 rounded-3 shadow-sm ${isLatest ? 'border-primary' : 'border-0'}"
            style="border-left: 4px solid ${isLatest ? 'var(--primary-color)' : 'var(--secondary-color)'} !important; border-radius: 15px !important;">
            <div class="card-header bg-white border-0 p-0 rounded-3 shadow-sm" style="border-radius: 15px !important;">
                <button class="btn btn-outline-light w-100 text-start p-3 border-0 rounded-3" type="button"
                    data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false"
                    style="background: #fff !important; color: var(--text-color); border-radius: 15px;"
                    onclick="patientSearchInstance.toggleChevron(this)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">

                            <div>
                                <h6 class="mb-1">วันที่เข้ารักษา : <span class="fw-bold">${diagnosisDate}</span></h6>

                                <small class="text-muted">${isLatest ? 'ประวัติล่าสุด •สถานะการรักษา :' : 'สถานะการรักษา :'} ${record.treatment_status || 'ไม่ระบุ'}</small>
                            </div>
                        </div>
                        <div class=" gap-2">

                            ${this.createDepartmentBadge(record.department_type)}
  <i class="fas fa-chevron-down chevron-icon text-primary ms-2"></i> <!-- เพิ่ม margin-left -->
                        </div>
                    </div>
                </button>
            </div>

            <div class="collapse" id="${collapseId}">
                <div class="card-body rounded-3" style="border-radius: 15px !important;">
                    ${isLatest ? '<div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">ประวัติการรักษาล่าสุด</div>' : ''}
                    ${formatVitalSigns(record)}

    <div class="card mb-4 border border-light-subtle shadow-sm rounded-3">
  <div class="card-body">
    <h6 class="section-title mb-2 pb-2 ">ข้อมูลการรักษา:</h6>

    <div class="row row-cols-md-3 g-4">
      <!-- แผนก -->
      <div class="col mb-3">  <!-- Added mb-3 for bottom margin -->
        <div class="d-flex align-items-baseline h-100 p-2 ">  <!-- Added padding and background -->
          <div class="fw-semibold text-nowrap" style="width: 120px;">แผนก:</div>
          <div class="text-muted ms-2">${this.getDepartmentDisplayName(record.department_type)}</div>
        </div>
      </div>

      <!-- แพทย์ผู้รักษา -->
      <div class="col mb-3">
        <div class="d-flex align-items-baseline h-100 p-2 ">
          <div class="fw-semibold text-nowrap" style="width: 120px;">แพทย์ผู้รักษา:</div>
          <div class="text-muted ms-2">${record.doctor_name || 'ไม่ระบุ'}</div>
        </div>
      </div>

      <!-- สถานะการรักษา -->
      <div class="col mb-3">
        <div class="d-flex align-items-baseline h-100 p-2">
          <div class="fw-semibold text-nowrap" style="width: 120px;">สถานะการรักษา:</div>
          <div class="text-muted ms-2">${record.treatment_status || 'ไม่ระบุ'}</div>
        </div>
      </div>

      <!-- อาการ -->
      <div class="col mb-3">
        <div class="d-flex align-items-baseline h-100 p-2 ">
          <div class="fw-semibold text-nowrap" style="width: 120px;">อาการ:</div>
          <div class="text-muted ms-2">${record.symptom_description || 'ไม่ระบุ'}</div>
        </div>
      </div>

      <!-- คำแนะนำการฝึก -->
      <div class="col mb-3">
        <div class="d-flex align-items-baseline h-100 p-2 ">
          <div class="fw-semibold text-nowrap" style="width: 120px;">คำแนะนำการฝึก:</div>
          <div class="text-muted ms-2">${record.training_instruction || 'ไม่ระบุ'}</div>
        </div>

      </div>

    </div>
  </div>

</div>


    <div class="card mb-4 border border-light-subtle shadow-sm rounded-3">
  <div class="card-body">
    <h6 class="section-title mb-3 pb-2 ">วินิจฉัยโรค:</h6>

 ${record.icd10_codes || record.disease_names ? `

                            ${record.disease_names ? `

                           <div class="medical-info p-3 bg-white rounded shadow-sm border border-gray-200">
                               <h6 class="pb-2 ">ชื่อโรค:</h6>

    ${(() => {
                                const codes = record.icd10_codes?.split(',').map(c => c.trim()) || [];
                                const diseases = record.disease_names?.split(',').map(d => d.trim()) || [];

                                return codes.map((code, index) => `
            <div class="flex items-center ${index < codes.length - 1 ? 'mb-1' : ''}">
                <span class="border-l border-gray-300 h-4 mr-2"></span>
                <span class="font-bold">${code}</span> : ${diseases[index] || ''}
            </div>
        `).join('');
                            })()}
</div>
                            </div>
                            ` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>

    </div>
  </div>


                        `;
            });

            historyHTML += '</div>';
            medicalHistoryElement.innerHTML = historyHTML;
        } else {
            medicalHistoryElement.innerHTML = `
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <h6 class="card-title" style="color: var(--text-color);">ไม่มีประวัติการรักษา</h6>
                        <p class="card-text text-muted mb-0">ยังไม่มีข้อมูลประวัติการรักษาของผู้ป่วยรายนี้</p>
                    </div>
                </div>
            `;
        }
    }

    getDepartmentDisplayName(departmentType) {
        const departments = {
            'opd': 'แผนกOPD',
            'er': 'แผนกER',
            'ipd': 'แผนกIPD'
        };
        return departments[departmentType] || 'ไม่ระบุแผนก';
    }

    createDepartmentBadge(departmentType) {
        const departmentConfig = {
            'opd': {
                name: 'OPD',
                color: '#ffffff',
                bg: '#28a745'
            },
            'er': {
                name: 'ER',
                color: '#ffffff',
                bg: '#dc3545'
            },
            'ipd': {
                name: 'IPD',
                color: '#ffffff',
                bg: '#007bff'
            }
        };

        const config = departmentConfig[departmentType] || {
            name: 'ไม่ระบุ',
            color: '#ffffff',
            bg: '#6c757d'
        };

        return `
        <span class="badge department-badge" style="
            background: ${config.bg};
            color: ${config.color};
            border-radius: 12px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 11px;
        ">
            แผนก : ${config.name}
        </span>
    `;
    }

    toggleChevron(button) {
        const chevron = button.querySelector('.chevron-icon');
        const target = button.getAttribute('data-bs-target');
        const collapseElement = document.querySelector(target);

        if (!collapseElement) return;

        collapseElement.addEventListener('shown.bs.collapse', function () {
            if (chevron) {
                chevron.style.transform = 'rotate(180deg)';
                chevron.style.color = '#5a9fd4';
            }
        }, { once: true });

        collapseElement.addEventListener('hidden.bs.collapse', function () {
            if (chevron) {
                chevron.style.transform = 'rotate(0deg)';
                chevron.style.color = 'var(--primary-color)';
            }
        }, { once: true });
    }

    showNoResults() {
        if (this.patientCard) this.patientCard.style.display = 'none';
        if (this.noResults) {
            this.noResults.style.display = 'block';
            this.noResults.innerHTML = `
                <div class="card border-danger border-2 border-opacity-25">
                    <div class="card-body p-4 text-center">
                        <h6 class="card-title text-danger">ไม่พบข้อมูลผู้ป่วย</h6>
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
                        <button id="clearPatientSearch" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>
                            ลองใหม่อีกครั้ง
                        </button>
                    </div>
                </div>
            `;
            this.noResults.style.display = 'block';
        }
    }

    addMedicalHistoryStyles() {
        if (!document.getElementById('medicalHistoryStyles')) {
            const styleElement = document.createElement('style');
            styleElement.id = 'medicalHistoryStyles';
            styleElement.innerHTML = `
                :root {
                    --primary-color: #77B2C9;
                    --secondary-color: #D6E7EE;
                    --accent-color: #f3e8ff;
                    --text-color: #222429;
                    --gray-color: rgb(239, 239, 239);
                    --white-color: #FFFFFF;
                    --snow-color: #f9f9f9;
                }

                .department-badge {
                    transition: all 0.2s ease;
                }

                .chevron-icon {
                    transition: transform 0.3s ease;
                }

                @media(max-width: 768px) {
                    .badge {
                        font-size: 0.7em!important;
                        padding: 4px 8px!important;
                    }

                    .department-badge {
                        font-size: 0.65em!important;
                        padding: 3px 6px!important;
                    }
                }
            `;
            document.head.appendChild(styleElement);
        }
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PatientSearch;
}

// Global instance variable
let patientSearchInstance;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    try {
        patientSearchInstance = new PatientSearch();
        console.log('PatientSearch component loaded successfully');
    } catch (error) {
        console.error('Failed to initialize PatientSearch:', error);
    }
});