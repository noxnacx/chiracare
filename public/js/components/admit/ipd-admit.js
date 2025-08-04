class PatientDashboard {
    constructor() {
        this.apiUrl = '/admin/patient/admit';
        this.patients = [];
        this.allPatients = [];
        this.units = [];
        this.rotations = [];
        this.currentStatsFilter = 'all';

        // *** เพิ่มการตรวจสอบว่าต้องแสดงคอลัมน์การดำเนินการหรือไม่ ***
        this.showActionsColumn = this.checkShowActionsColumn();
        console.log('Constructor - showActionsColumn:', this.showActionsColumn);

        this.initEventListeners();
        this.loadInitialData();
        this.loadTodayStats();
    }

    // เพิ่มฟังก์ชันนี้
    checkShowActionsColumn() {
        console.log('=== CHECKING SHOW ACTIONS COLUMN ===');

        const table = document.getElementById('patientsTable');
        console.log('Table element:', table);

        if (table && table.hasAttribute('data-no-actions')) {
            console.log('Found data-no-actions attribute - HIDING actions column');
            return false;
        }

        if (document.body.classList.contains('no-actions-column')) {
            console.log('Found no-actions-column class - HIDING actions column');
            return false;
        }

        if (window.location.pathname.includes('/view-only')) {
            console.log('Found view-only in URL - HIDING actions column');
            return false;
        }

        console.log('No flags found - SHOWING actions column');
        return true;
    }

    async loadTodayStats() {
        try {
            console.log('=== LOADING TODAY STATS ===');

            // เรียก API 2 ครั้งแยกกัน
            const [admittedResponse, dischargedResponse] = await Promise.all([
                // คนเข้าวันนี้
                fetch(`${this.apiUrl}?format=json&date_filter=today`),
                // คนออกวันนี้
                fetch(`${this.apiUrl}?format=json&filter_status=Discharged&date_filter=today`)
            ]);

            const admittedData = await admittedResponse.json();
            const dischargedData = await dischargedResponse.json();

            console.log('Admitted API result:', admittedData);
            console.log('Discharged API result:', dischargedData);

            // ข้อมูลคนเข้าวันนี้
            const admittedToday = admittedData.success ? admittedData.data : [];

            // ข้อมูลคนออกวันนี้
            const dischargedToday = dischargedData.success ? dischargedData.data : [];

            console.log('Admitted today count:', admittedToday.length);
            console.log('Discharged today count:', dischargedToday.length);

            // อัพเดทตัวแปร
            this.todayAdmittedCount = admittedToday.length;
            this.todayDischargedCount = dischargedToday.length;
            this.todayAdmittedList = admittedToday;
            this.todayDischargedList = dischargedToday;

            // อัพเดทหน้าจอ
            this.updateTodayUI();

            console.log('Final Today Stats:', {
                admitted: this.todayAdmittedCount,
                discharged: this.todayDischargedCount
            });

            // แสดงข้อมูลผู้ป่วย
            if (admittedToday.length > 0) {
                console.log('Admitted patients:', admittedToday);
            }
            if (dischargedToday.length > 0) {
                console.log('Discharged patients:', dischargedToday);
            }

        } catch (error) {
            console.error('Error loading today stats:', error);
            this.showTodayError();
        }
    }

    showTodayError() {
        const admittedCountEl = document.getElementById('today-admitted-count');
        const dischargedCountEl = document.getElementById('today-discharged-count');
        const admittedContent = document.getElementById('today-admitted-content');
        const dischargedContent = document.getElementById('today-discharged-content');

        if (admittedCountEl) admittedCountEl.textContent = 'Error';
        if (dischargedCountEl) dischargedCountEl.textContent = 'Error';
        if (admittedContent) admittedContent.innerHTML = '<p class="text-danger text-center py-3">เกิดข้อผิดพลาด</p>';
        if (dischargedContent) dischargedContent.innerHTML = '<p class="text-danger text-center py-3">เกิดข้อผิดพลาด</p>';
    }

    testDischargeToday() {
        console.log('=== TEST DISCHARGE TODAY ===');
        const today = new Date().toDateString();
        console.log('Today:', today);

        if (this.allPatients && this.allPatients.length > 0) {
            console.log('Using allPatients:', this.allPatients.length);

            const withDischarge = this.allPatients.filter(p => p.discharge_date);
            console.log('Patients with discharge_date:', withDischarge.length);

            withDischarge.forEach(p => {
                const dischargeDate = new Date(p.discharge_date).toDateString();
                console.log(`${p.first_name} ${p.last_name}:`, {
                    discharge_date: p.discharge_date,
                    discharge_date_string: dischargeDate,
                    is_today: dischargeDate === today
                });
            });
        } else {
            console.log('No allPatients data available');
        }
    }

    updateTodayUI() {
        // อัพเดทจำนวน
        const admittedCountEl = document.getElementById('today-admitted-count');
        const dischargedCountEl = document.getElementById('today-discharged-count');

        if (admittedCountEl) {
            admittedCountEl.textContent = this.todayAdmittedCount;
        }
        if (dischargedCountEl) {
            dischargedCountEl.textContent = this.todayDischargedCount;
        }

        // อัพเดทรายชื่อ
        this.renderAdmittedList();
        this.renderDischargedList();
    }

    renderAdmittedList() {
        const container = document.getElementById('today-admitted-content');
        if (!container) return;

        if (this.todayAdmittedList.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3">ไม่มีผู้ป่วยเข้า admit วันนี้</p>';
            return;
        }

        let html = '';
        this.todayAdmittedList.slice(0, 2).forEach((patient, index) => {
            html += `
            <div class="patient-card mb-2 p-3 border rounded">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">${patient.first_name} ${patient.last_name}</h6>
                            <span class="badge" style="
                                background-color: white;
                                border: 2px solid rgb(200, 200, 200);
                                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                color: #333;
                                padding: 0.35em 0.65em;
                                border-radius: 0.25rem;
                                display: inline-block;
                            ">
                                กำลังรักษา
                            </span>
                        </div>
                        <small class="text-muted d-block">รหัส: ${patient.soldier_id_card}</small>
                        <small class="text-muted d-block">วันที่เข้า: ${this.formatThaiDate(patient.admit_date)}</small>
                    </div>
                </div>
            </div>
        `;
        });

        html += `
        <div class="pt-2">
            <button type="button"
                    class="btn btn-success w-100"
                    onclick="viewAllAdmittedPatients()"
                     style="
                        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                        border: none;
                        border-radius: 8px;
                        font-size: 0.8rem;
                        box-shadow: 0 2px 6px rgba(0,123,255,0.3);
                        transition: all 0.2s ease;
                        padding: 0.5rem 1rem;
                    "
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,123,255,0.3)'">
                ดูทั้งหมด
            </button>
        </div>
    `;

        container.innerHTML = html;
    }

    renderDischargedList() {
        const container = document.getElementById('today-discharged-content');
        if (!container) return;

        if (this.todayDischargedList.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3">ไม่มีผู้ป่วยออก admit วันนี้</p>';
            return;
        }

        let html = '';
        this.todayDischargedList.slice(0, 2).forEach((patient, index) => {
            html += `
            <div class="patient-card mb-2 p-3 border rounded">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">${patient.first_name} ${patient.last_name}</h6>
                            <span class="badge" style="
                                background-color: white;
                                border: 2px solid rgb(200, 200, 200);
                                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                color: #333;
                                padding: 0.35em 0.65em;
                                border-radius: 0.25rem;
                                display: inline-block;
                            ">
                                Admit ${patient.treatment_days} วัน
                            </span>
                        </div>
                        <small class="text-muted d-block">รหัส: ${patient.soldier_id_card}</small>
                        <small class="text-muted d-block">วันที่ออก: ${this.formatThaiDate(patient.discharge_date)}</small>
                    </div>
                </div>
            </div>
        `;
        });

        html += `
        <div class="pt-2">
            <button type="button"
                    class="btn btn-primary w-100"
                    onclick="viewAllDischargedPatients()"
                    style="
                        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                        border: none;
                        border-radius: 8px;
                        font-size: 0.8rem;
                        box-shadow: 0 2px 6px rgba(0,123,255,0.3);
                        transition: all 0.2s ease;
                        padding: 0.5rem 1rem;
                    "
                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,123,255,0.3)'">
                ดูทั้งหมด
            </button>
        </div>
    `;

        container.innerHTML = html;
    }

    formatThaiDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('th-TH', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    initEventListeners() {
        const statsCards = document.querySelectorAll('.stats-card[data-filter]');
        if (statsCards.length > 0) {
            statsCards.forEach(card => {
                card.addEventListener('click', () => {
                    const filter = card.getAttribute('data-filter');
                    this.setStatsFilter(filter);
                });
            });
        }

        document.getElementById('openFilterModal').addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('filterModal'));
            modal.show();
        });

        document.getElementById('apply-filters-btn').addEventListener('click', () => {
            this.loadPatients();
            bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
        });

        document.getElementById('clear-filters-btn').addEventListener('click', () => this.clearFilters());

        document.getElementById('status-filter').addEventListener('change', () => {
            this.loadPatients();
            this.updateHeaderTitle();
        });

        document.getElementById('unit-filter').addEventListener('change', () => this.loadPatients());
        document.getElementById('rotation-filter').addEventListener('change', () => this.loadPatients());

        document.getElementById('date-filter').addEventListener('change', () => {
            this.toggleCustomDateRow();
            this.loadPatients();
            this.updateHeaderTitle();
        });

        document.getElementById('start-date').addEventListener('change', () => {
            if (document.getElementById('date-filter').value === 'custom') {
                this.loadPatients();
                this.updateHeaderTitle();
            }
        });

        document.getElementById('end-date').addEventListener('change', () => {
            if (document.getElementById('date-filter').value === 'custom') {
                this.loadPatients();
                this.updateHeaderTitle();
            }
        });
    }

    updateHeaderTitle() {
        const header = document.getElementById('header-title');
        const statusFilter = document.getElementById('status-filter').value;
        const dateFilter = document.getElementById('date-filter').value;

        let statusText = '';
        let dateText = '';

        switch (statusFilter) {
            case 'Admit':
                statusText = 'ผู้ป่วยแอดมิทเข้า';
                break;
            case 'Discharged':
                statusText = 'ผู้ป่วยแอดมิทออกแล้ว';
                break;
            default:
                statusText = 'ผู้ป่วย ADMIT';
        }

        if (dateFilter === 'today') {
            const today = new Date().toLocaleDateString('th-TH');
            dateText = ` (${today})`;
        } else if (dateFilter === 'custom') {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            if (startDate && endDate) {
                dateText = ` (${this.formatDate(startDate)} - ${this.formatDate(endDate)})`;
            }
        }

        header.textContent = `ติดตาม${statusText}${dateText}`;
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'numeric', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('th-TH', options);
    }

    toggleCustomDateRow() {
        const dateFilter = document.getElementById('date-filter').value;
        const customRow = document.getElementById('custom-date-row');

        if (dateFilter === 'custom') {
            customRow.style.display = 'block';
            const today = new Date().toISOString().split('T')[0];
            if (!document.getElementById('start-date').value) {
                document.getElementById('start-date').value = today;
            }
            if (!document.getElementById('end-date').value) {
                document.getElementById('end-date').value = today;
            }
        } else {
            customRow.style.display = 'none';
            this.loadPatients();
        }
    }

    setStatsFilter(filter) {
        const statsCards = document.querySelectorAll('.stats-card[data-filter]');
        if (statsCards.length === 0) {
            return;
        }

        this.currentStatsFilter = filter;

        statsCards.forEach(card => {
            card.classList.remove('active');
        });

        if (filter !== 'all') {
            const selectedCard = document.querySelector(`[data-filter="${filter}"]`);
            if (selectedCard) {
                selectedCard.classList.add('active');
            }
        }

        this.applyStatsFilter();
    }

    applyStatsFilter() {
        let filteredPatients = [...this.allPatients];

        switch (this.currentStatsFilter) {
            case 'admitted':
                filteredPatients = this.allPatients.filter(p => p.admit_status === 'กำลังรักษา');
                break;
            case 'discharged':
                filteredPatients = this.allPatients.filter(p => p.admit_status !== 'กำลังรักษา');
                break;
            case 'all':
            default:
                filteredPatients = [...this.allPatients];
                break;
        }

        this.patients = filteredPatients;
        this.displayPatients();
    }

    clearFilters() {
        document.getElementById('status-filter').value = 'all';
        document.getElementById('unit-filter').value = 'all';
        document.getElementById('rotation-filter').value = 'all';
        document.getElementById('date-filter').value = 'all';
        document.getElementById('start-date').value = '';
        document.getElementById('end-date').value = '';
        document.getElementById('custom-date-row').style.display = 'none';

        const statsCards = document.querySelectorAll('.stats-card[data-filter]');
        if (statsCards.length > 0) {
            this.setStatsFilter('all');
        } else {
            this.currentStatsFilter = 'all';
        }

        this.loadPatients();
        this.updateHeaderTitle();
    }

    async loadInitialData() {
        try {
            const response = await fetch(`${this.apiUrl}?format=json&date_filter=all`);
            const data = await response.json();

            if (data.success) {
                const unitsFromPatients = [...new Set(data.data.map(p => p.training_unit_name))].filter(Boolean);
                const rotationsFromPatients = [...new Set(data.data.map(p => p.rotation_name))].filter(Boolean);

                this.units = unitsFromPatients.sort();
                this.rotations = rotationsFromPatients.sort();

                this.populateDropdowns();
                this.loadPatients();
            } else {
                throw new Error('ไม่สามารถโหลดข้อมูลได้');
            }
        } catch (error) {
            console.error('Error loading initial data:', error);
            this.showError('ไม่สามารถโหลดข้อมูลเริ่มต้นได้');
        }
    }

    populateDropdowns() {
        const unitFilter = document.getElementById('unit-filter');
        unitFilter.innerHTML = '<option value="all">ทุกหน่วยฝึก</option>';

        if (this.units.length === 0) {
            const noDataOption = document.createElement('option');
            noDataOption.value = 'no-data';
            noDataOption.textContent = 'ไม่มีข้อมูลหน่วยฝึก';
            noDataOption.disabled = true;
            unitFilter.appendChild(noDataOption);
        } else {
            this.units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unit;
                unitFilter.appendChild(option);
            });
        }

        const rotationFilter = document.getElementById('rotation-filter');
        rotationFilter.innerHTML = '<option value="all">ทุกผลัด</option>';

        if (this.rotations.length === 0) {
            const noDataOption = document.createElement('option');
            noDataOption.value = 'no-data';
            noDataOption.textContent = 'ไม่มีข้อมูลผลัด';
            noDataOption.disabled = true;
            rotationFilter.appendChild(noDataOption);
        } else {
            this.rotations.forEach(rotation => {
                const option = document.createElement('option');
                option.value = rotation;
                option.textContent = rotation;
                rotationFilter.appendChild(option);
            });
        }
    }

    async loadPatients() {
        this.showLoading();

        try {
            const params = this.buildQueryParams();
            const response = await fetch(`${this.apiUrl}?format=json&${params}`);
            const data = await response.json();

            if (data.success) {
                this.allPatients = data.data;
                this.patients = data.data;
                this.updateStats();
                this.applyStatsFilter();
                this.updateHeaderTitle();
            } else {
                throw new Error('API returned error');
            }
        } catch (error) {
            console.error('Error loading patients:', error);
            this.showError('ไม่สามารถโหลดข้อมูลผู้ป่วยได้');
        }
    }

    buildQueryParams() {
        const params = new URLSearchParams();

        const status = document.getElementById('status-filter').value;
        const unit = document.getElementById('unit-filter').value;
        const rotation = document.getElementById('rotation-filter').value;
        const dateFilter = document.getElementById('date-filter').value;

        if (status !== 'all') params.append('filter_status', status);
        if (unit !== 'all' && unit !== 'no-data') params.append('unit', unit);
        if (rotation !== 'all' && rotation !== 'no-data') params.append('rotation', rotation);

        if (dateFilter === 'custom') {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            if (startDate && endDate) {
                params.append('date_filter', 'custom');
                params.append('start_date', startDate);
                params.append('end_date', endDate);
            }
        } else if (dateFilter === 'today') {
            params.append('date_filter', 'today');
        } else {
            params.append('date_filter', 'all');
        }

        return params.toString();
    }

    updateStats() {
        const total = this.allPatients.length;
        const admitted = this.allPatients.filter(p => p.admit_status === 'กำลังรักษา').length;
        const discharged = total - admitted;
        const avgDays = total > 0 ? (this.allPatients.reduce((sum, p) => sum + parseInt(p.treatment_days), 0) / total).toFixed(1) : 0;

        const totalElement = document.getElementById('total-patients');
        if (totalElement) totalElement.textContent = total;

        const admittedElement = document.getElementById('admitted-patients');
        if (admittedElement) admittedElement.textContent = admitted;

        const dischargedElement = document.getElementById('discharged-patients');
        if (dischargedElement) dischargedElement.textContent = discharged;

        const avgElement = document.getElementById('avg-days');
        if (avgElement) avgElement.textContent = avgDays;
    }

    displayPatients() {
        const loading = document.getElementById('loading');
        const table = document.getElementById('patientsTable');
        const tbody = document.getElementById('patients-tbody');
        const noData = document.getElementById('no-data');

        loading.style.display = 'none';

        if (this.patients.length === 0) {
            table.style.display = 'none';
            noData.style.display = 'block';

            const unit = document.getElementById('unit-filter').value;
            const rotation = document.getElementById('rotation-filter').value;

            let message = 'ไม่มีข้อมูลผู้ป่วย';

            if (unit !== 'all' && unit !== 'no-data') {
                message = `ไม่มีผู้ป่วย admit ในหน่วย "${unit}"`;
            } else if (rotation !== 'all' && rotation !== 'no-data') {
                message = `ไม่มีผู้ป่วย admit ในผลัด "${rotation}"`;
            } else {
                message = 'ไม่มีผู้ป่วย admit ในระบบ';
            }

            noData.innerHTML = `<p class="text-muted">${message}</p>`;
            return;
        }

        noData.style.display = 'none';
        table.style.display = 'table';

        // *** เช็คว่าต้องแสดงคอลัมน์การดำเนินการหรือไม่ ***
        const hasActivePatients = this.patients.some(p => p.admit_status === 'กำลังรักษา');
        this.updateTableHeaders(hasActivePatients);

        tbody.innerHTML = '';

        this.patients.forEach((patient, index) => {
            const row = this.createPatientRow(patient, index + 1, hasActivePatients);
            if (row) {
                tbody.appendChild(row);
            }
        });
    }

    // *** เพิ่มฟังก์ชันใหม่สำหรับอัพเดท table headers ***
    updateTableHeaders(showActionsColumn) {
        console.log('=== UPDATE TABLE HEADERS ===');
        console.log('this.showActionsColumn:', this.showActionsColumn);

        const table = document.getElementById('patientsTable');
        const thead = table.querySelector('thead tr');

        const existingActionHeader = thead.querySelector('.actions-header');
        if (existingActionHeader) {
            existingActionHeader.remove();
        }

        // เพิ่มคอลัมน์เฉพาะเมื่อ this.showActionsColumn = true
        if (showActionsColumn && this.showActionsColumn) {
            const actionHeader = document.createElement('th');
            actionHeader.className = 'actions-header';
            actionHeader.style.width = '15%';
            actionHeader.textContent = 'การดำเนินการ';
            thead.appendChild(actionHeader);
        } else {
            console.log('NOT adding actions header - data-no-actions found');
        }
    }

    // *** แก้ไขฟังก์ชันนี้เพื่อรองรับการแสดง/ซ่อนคอลัมน์การดำเนินการ ***
    createPatientRow(patient, index, showActionsColumn = true) {
        const template = document.getElementById('patient-row-template');
        if (!template) {
            console.error('Template not found: patient-row-template');
            return this.createPatientRowFallback(patient, index, showActionsColumn);
        }

        const clone = template.content.cloneNode(true);

        const statusClass = patient.admit_status === 'กำลังรักษา' ? 'bg-success' : 'bg-warning';
        const statusText = patient.admit_status === 'กำลังรักษา' ? 'กำลังรักษา' : 'ออกจากadmitแล้ว';
        const diseases = patient.disease_names ?
            patient.disease_names.split(',').slice(0, 2).join('\n') : 'ไม่ระบุ';
        const unitName = patient.training_unit_name || '-';
        const rotationName = patient.rotation_name || '-';

        // Fill template with data
        clone.querySelector('.patient-name').textContent = `${patient.first_name} ${patient.last_name}`;
        clone.querySelector('.patient-id-card').textContent = patient.soldier_id_card;
        clone.querySelector('.patient-unit').textContent = unitName;
        clone.querySelector('.patient-rotation').textContent = rotationName;
        clone.querySelector('.patient-admit-date').textContent = patient.admit_date;

        // Handle disease display
        const diseaseSpan = clone.querySelector('.patient-diseases');
        diseaseSpan.innerHTML = '';

        if (patient.disease_names && patient.icd10_codes) {
            const diseaseNames = patient.disease_names.split(',');
            const icd10Codes = patient.icd10_codes.split(',');

            diseaseNames.slice(0, 2).forEach((disease, index) => {
                const diseaseBox = document.createElement('span');
                diseaseBox.className = 'border shadow p-2 d-inline-block mb-1 me-1';
                diseaseBox.style.fontSize = '0.8em';
                diseaseBox.style.backgroundColor = 'white';
                diseaseBox.style.borderRadius = '12px';
                diseaseBox.style.borderColor = '#6c757d';

                const icdCode = icd10Codes[index] ? icd10Codes[index].trim() : '';

                if (icdCode) {
                    const codeElement = document.createElement('strong');
                    codeElement.textContent = icdCode + ': ';
                    diseaseBox.appendChild(codeElement);

                    const diseaseText = document.createTextNode(disease.trim());
                    diseaseBox.appendChild(diseaseText);
                } else {
                    diseaseBox.textContent = disease.trim();
                }

                diseaseSpan.appendChild(diseaseBox);
            });
        } else if (patient.disease_names) {
            patient.disease_names.split(',').slice(0, 2).forEach(disease => {
                const diseaseBox = document.createElement('span');
                diseaseBox.className = 'border shadow p-2 d-inline-block mb-1 me-1';
                diseaseBox.style.fontSize = '0.8em';
                diseaseBox.style.backgroundColor = 'white';
                diseaseBox.style.borderRadius = '12px';
                diseaseBox.style.borderColor = '#6c757d';
                diseaseBox.textContent = disease.trim();
                diseaseSpan.appendChild(diseaseBox);
            });
        } else {
            const noDataSpan = document.createElement('span');
            noDataSpan.className = 'text-muted';
            noDataSpan.textContent = 'ไม่ระบุ';
            diseaseSpan.appendChild(noDataSpan);
        }

        diseaseSpan.title = diseases;

        // *** จัดการคอลัมน์การดำเนินการตามเงื่อนไข ***
        const actionsCell = clone.querySelector('.patient-actions');
        if (actionsCell) {
            if (showActionsColumn && patient.admit_status === 'กำลังรักษา') {
                // แสดงปุ่มสำหรับผู้ป่วยที่กำลังรักษา
                const diagnosisBtn = document.createElement('a');
                diagnosisBtn.href = `/ipd/diagnosis/${patient.treatment_id}`;
                diagnosisBtn.className = 'btn btn-sm'; // เอาคลาส btn-primary ออก
                diagnosisBtn.innerHTML = 'กรอกข้อมูลวินิจฉัย';
                diagnosisBtn.style.cssText = `
            background-color: #77B2C9;
            border-color: #77B2C9;
            color: white;
            font-size: 0.75rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        `;

                // เอฟเฟกต์เมื่อโฮเวอร์
                diagnosisBtn.addEventListener('mouseover', () => {
                    diagnosisBtn.style.backgroundColor = '#5E9CB8';
                    diagnosisBtn.style.transform = 'translateY(-1px)';
                    diagnosisBtn.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                });

                diagnosisBtn.addEventListener('mouseout', () => {
                    diagnosisBtn.style.backgroundColor = '#77B2C9';
                    diagnosisBtn.style.transform = 'translateY(0)';
                    diagnosisBtn.style.boxShadow = 'none';
                });

                diagnosisBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                });

                actionsCell.appendChild(diagnosisBtn);
            } else {
                // ลบคอลัมน์การดำเนินการทั้งหมด (ไม่ว่าจะเป็นกรณีไหน)
                actionsCell.remove();
            }
        }
        // Add click event สำหรับ row (เดิม)
        const row = clone.querySelector('.patient-row');
        row.setAttribute('data-id-card', patient.soldier_id_card);
        row.addEventListener('click', (e) => {
            // ตรวจสอบว่าไม่ได้คลิกที่ปุ่ม
            if (!e.target.closest('.patient-actions')) {
                this.showPatientDetails(patient.soldier_id_card);
            }
        });

        return clone;
    }

    // Fallback function สำหรับกรณีที่ template ไม่พบ
    createPatientRowFallback(patient, index, showActionsColumn = true) {
        const tr = document.createElement('tr');
        tr.className = 'patient-row';
        tr.style.cursor = 'pointer';
        tr.setAttribute('data-id-card', patient.soldier_id_card);

        const unitName = patient.training_unit_name || '-';
        const rotationName = patient.rotation_name || '-';
        const diseases = patient.disease_names || 'ไม่ระบุ';

        // สร้าง actions cell เฉพาะเมื่อจำเป็น
        let actionsHtml = '';
        if (showActionsColumn && patient.admit_status === 'กำลังรักษา') {
            actionsHtml = `<td class="patient-actions text-center">
                <a href="/ipd/diagnosis/${patient.treatment_id}"
                   class="btn btn-primary btn-sm"
                   style="font-size: 0.75rem; white-space: nowrap;"
                   onclick="event.stopPropagation();">
                   <i class="fas fa-stethoscope me-1"></i>กรอกข้อมูลวินิจฉัย
                </a>
            </td>`;
        }
        // ถ้าไม่ตรงเงื่อนไข จะไม่มี actionsHtml (ไม่มีคอลัมน์การดำเนินการ)

        tr.innerHTML = `
            <td><strong>${patient.first_name} ${patient.last_name}</strong></td>
            <td><span class="text-muted">${patient.soldier_id_card}</span></td>
            <td><small>${unitName}</small></td>
            <td><small>${rotationName}</small></td>
            <td><small>${patient.admit_date}</small></td>
            <td>
                <span class="border shadow p-2 d-inline-block"
                      style="font-size: 0.8em; background-color: white; border-radius: 12px;">
                    ${diseases}
                </span>
            </td>
            ${actionsHtml}
        `;

        tr.addEventListener('click', (e) => {
            if (!e.target.closest('.patient-actions')) {
                this.showPatientDetails(patient.soldier_id_card);
            }
        });

        return tr;
    }

    showPatientDetails(idCard) {
        const patient = this.patients.find(p => p.soldier_id_card === idCard);
        if (!patient) return;

        const template = document.getElementById('patient-details-template');
        if (!template) {
            console.error('Template not found: patient-details-template');
            return;
        }

        const clone = template.content.cloneNode(true);
        const modalContent = document.getElementById('modal-content');

        const unitName = patient.training_unit_name || '-';
        const rotationName = patient.rotation_name || '-';
        const affiliatedUnit = patient.affiliated_unit || '-';
        const dischargeDate = patient.discharge_date || 'ยังไม่ออก';
        const diseases = patient.disease_names || 'ไม่ระบุ';
        const icd10 = patient.icd10_codes || 'ไม่ระบุ';

        // Format disease and ICD-10 codes
        let diseaseAndIcdText = 'ไม่ระบุ';
        if (patient.disease_names && patient.icd10_codes) {
            const diseaseNames = patient.disease_names.split(',');
            const icd10Codes = patient.icd10_codes.split(',');

            diseaseAndIcdText = diseaseNames.map((disease, index) => {
                const icdCode = icd10Codes[index] ? icd10Codes[index].trim() : '';
                return icdCode ? `${icdCode}: ${disease.trim()}` : disease.trim();
            }).join('\n');
        } else if (patient.disease_names) {
            diseaseAndIcdText = patient.disease_names.split(',').map(disease => disease.trim()).join('\n');
        }

        // Fill template with data
        clone.querySelector('.patient-detail-name').textContent = `${patient.first_name} ${patient.last_name}`;
        clone.querySelector('.patient-detail-id').textContent = patient.soldier_id_card;
        clone.querySelector('.patient-detail-unit').textContent = unitName;
        clone.querySelector('.patient-detail-rotation').textContent = rotationName;
        clone.querySelector('.patient-detail-affiliated').textContent = affiliatedUnit;
        clone.querySelector('.patient-detail-admit-date').textContent = patient.admit_date;
        clone.querySelector('.patient-detail-discharge-date').textContent = dischargeDate;
        clone.querySelector('.patient-detail-days').textContent = `${patient.treatment_days} วัน`;
        clone.querySelector('.patient-detail-status').textContent = patient.admit_status;
        clone.querySelector('.patient-detail-diseases').textContent = diseaseAndIcdText;

        // Handle patient image
        const imageElement = clone.querySelector('.patient-detail-image');
        if (patient.soldier_image) {
            imageElement.src = `/${patient.soldier_image}`;
            imageElement.onerror = function () {
                this.style.display = 'none';
                const iconElement = document.createElement('i');
                iconElement.className = 'fas fa-user-circle';
                iconElement.style.fontSize = '80px';
                iconElement.style.color = '#6c757d';
                this.parentNode.appendChild(iconElement);
            };
        } else {
            imageElement.style.display = 'none';
            const iconElement = document.createElement('i');
            iconElement.className = 'fas fa-user-circle';
            iconElement.style.fontSize = '80px';
            iconElement.style.color = '#6c757d';
            imageElement.parentNode.appendChild(iconElement);
        }

        // Replace modal content
        modalContent.innerHTML = '';
        modalContent.appendChild(clone);

        const modal = new bootstrap.Modal(document.getElementById('patientModal'));
        modal.show();
    }

    showLoading() {
        document.getElementById('loading').style.display = 'block';
        document.getElementById('patientsTable').style.display = 'none';
        document.getElementById('no-data').style.display = 'none';
    }

    showError(message) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('patientsTable').style.display = 'none';
        document.getElementById('no-data').style.display = 'block';
        document.getElementById('no-data').innerHTML = `<p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i>${message}</p>`;
    }
}

// Global functions for button interactions
function viewAllAdmittedPatients() {
    document.getElementById('status-filter').value = 'all';
    document.getElementById('date-filter').value = 'today';
    document.getElementById('unit-filter').value = 'all';
    document.getElementById('rotation-filter').value = 'all';
    document.getElementById('custom-date-row').style.display = 'none';

    if (window.dashboard) {
        window.dashboard.loadPatients();
        window.dashboard.updateHeaderTitle();
    }
}

function viewAllDischargedPatients() {
    document.getElementById('status-filter').value = 'Discharged';
    document.getElementById('date-filter').value = 'today';
    document.getElementById('unit-filter').value = 'all';
    document.getElementById('rotation-filter').value = 'all';
    document.getElementById('custom-date-row').style.display = 'none';

    if (window.dashboard) {
        window.dashboard.loadPatients();
        window.dashboard.updateHeaderTitle();
    }
}

function resetFiltersToDefault() {
    document.getElementById('status-filter').value = 'all';
    document.getElementById('date-filter').value = 'all';
    document.getElementById('unit-filter').value = 'all';
    document.getElementById('rotation-filter').value = 'all';
    document.getElementById('custom-date-row').style.display = 'none';

    if (window.dashboard) {
        window.dashboard.loadPatients();
        window.dashboard.updateHeaderTitle();
    }
}

function viewAllAdmittedPatientsWithFeedback() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...';
    button.disabled = true;

    document.getElementById('status-filter').value = 'all';
    document.getElementById('date-filter').value = 'today';
    document.getElementById('unit-filter').value = 'all';
    document.getElementById('rotation-filter').value = 'all';
    document.getElementById('custom-date-row').style.display = 'none';

    if (window.dashboard) {
        window.dashboard.loadPatients().then(() => {
            window.dashboard.updateHeaderTitle();

            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1000);
        });
    }
}

function viewAllDischargedPatientsWithFeedback() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังโหลด...';
    button.disabled = true;

    document.getElementById('status-filter').value = 'Discharged';
    document.getElementById('date-filter').value = 'today';
    document.getElementById('unit-filter').value = 'all';
    document.getElementById('rotation-filter').value = 'all';
    document.getElementById('custom-date-row').style.display = 'none';

    if (window.dashboard) {
        window.dashboard.loadPatients().then(() => {
            window.dashboard.updateHeaderTitle();

            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            }, 1000);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    window.dashboard = new PatientDashboard();
});