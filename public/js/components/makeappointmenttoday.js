/**
 * Make Appointment Today Component
 * จัดการการแสดงผลรายการนัดหมายวันนี้
 */

class MakeAppointmentToday {
    constructor() {
        this.tableBody = null;
        this.totalCountElement = null;
        this.table = null;
        this.init();
    }

    init() {
        // รอให้ DOM โหลดเสร็จก่อน
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initElements());
        } else {
            this.initElements();
        }
    }

    initElements() {
        this.tableBody = document.getElementById('tableBody');
        this.totalCountElement = document.getElementById('totalCount_sent');
        this.table = $('#todaymakeappointmenttodayTable');

        // เริ่มโหลดข้อมูล
        this.loadData();
    }

    /**
     * แสดงข้อความ Error
     */
    showError() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่ภายหลัง
                    </td>
                </tr>
            `;
        }
    }

    /**
     * โหลดข้อมูลจาก API
     */
    async loadData() {
        try {
            const response = await fetch('/medical-reports/soldier-info?status=approved');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            const data = result.data || [];
            const summary = result.summary || {};

            this.fillTable(data, summary);

        } catch (error) {
            console.error('Error loading data:', error);
            this.showError();
        }
    }

    /**
     * เติมข้อมูลในตาราง
     */
    fillTable(data, summary) {
        // อัปเดตจำนวนเคส
        if (this.totalCountElement) {
            this.totalCountElement.textContent = `${summary.total || 0} เคส`;
        }

        // ตรวจสอบว่ามี table body หรือไม่
        if (!this.tableBody) {
            console.warn('Table body element not found');
            return;
        }

        // ล้างข้อมูลเก่า
        this.tableBody.innerHTML = '';

        // กรณีไม่มีข้อมูล
        if (!data || data.length === 0) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
                </tr>
            `;
            this.destroyDataTable();
            return;
        }

        // เติมข้อมูลในตาราง (แสดง 5 แถวแรก)
        data.slice(0, 5).forEach((item) => {
            const row = this.createTableRow(item);
            this.tableBody.appendChild(row);
        });

        // เริ่มต้น DataTable
        this.initializeDataTable();
    }

    /**
     * สร้างแถวในตาราง
     */
    createTableRow(item) {
        const row = document.createElement('tr');

        // ชื่อ-นามสกุล
        const nameCell = document.createElement('td');
        nameCell.className = 'patient-name-cell';
        nameCell.textContent = `${item.first_name || ''} ${item.last_name || ''}`.trim() || '-';

        // เลขประจำตัว
        const idCell = document.createElement('td');
        idCell.className = 'patient-id-cell';
        idCell.textContent = item.soldier_id_card || '-';

        // ผลัด
        const rotationCell = document.createElement('td');
        const rotationBadge = document.createElement('span');
        rotationBadge.className = 'unit-badge rotation-badge';
        rotationBadge.textContent = item.rotation_name || '-';
        rotationCell.appendChild(rotationBadge);

        // หน่วยฝึก
        const unitCell = document.createElement('td');
        const unitBadge = document.createElement('span');
        unitBadge.className = 'unit-badge training-badge';
        unitBadge.textContent = item.unit_name || '-';
        unitCell.appendChild(unitBadge);

        // ระดับเสี่ยง
        const riskCell = document.createElement('td');
        const riskBadge = this.createRiskBadge(item.risk_level);
        riskCell.appendChild(riskBadge);

        // สถานะรายงานทางการแพทย์
        const statusCell = document.createElement('td');
        const statusBadge = this.createStatusBadge(item.medical_report_status);
        statusCell.appendChild(statusBadge);

        // เพิ่มเซลล์ทั้งหมดในแถว
        row.appendChild(nameCell);
        row.appendChild(idCell);
        row.appendChild(rotationCell);
        row.appendChild(unitCell);
        row.appendChild(riskCell);
        row.appendChild(statusCell);

        return row;
    }

    /**
     * สร้าง Badge สำหรับระดับเสี่ยง
     */
    createRiskBadge(riskLevel) {
        const badge = document.createElement('span');
        const level = (riskLevel || 'green').toLowerCase();

        let riskText = '';
        switch (level) {
            case 'green':
                riskText = 'ความเสี่ยงต่ำ';
                break;
            case 'yellow':
                riskText = 'เฝ้าระวัง';
                break;
            case 'red':
                riskText = 'ความเสี่ยงฉุกเฉิน';
                break;
            default:
                riskText = 'ไม่ระบุ';
        }

        badge.className = `risk-badge ${level}`;
        badge.innerHTML = `
            <span class="risk-bullet"></span>
            <span class="risk-text">${riskText}</span>
        `;

        return badge;
    }

    /**
     * สร้าง Badge สำหรับสถานะรายงานทางการแพทย์
     */
    createStatusBadge(status) {
        const badge = document.createElement('span');
        const statusLevel = (status || 'sent').toLowerCase();

        let statusText = '';
        switch (statusLevel) {
            case 'sent':
                statusText = 'รอการอนุมัติ';
                break;
            case 'approved':
                statusText = 'อนุมัติแล้ว';
                break;
            default:
                statusText = 'ไม่ระบุสถานะ';
        }

        badge.className = `medical-report-status-badge ${statusLevel}`;
        badge.innerHTML = `
            <span class="status-bullet"></span>
            <span class="status-text">${statusText}</span>
        `;

        return badge;
    }

    /**
     * เริ่มต้น DataTable
     */
    initializeDataTable() {
        if (this.table && !$.fn.DataTable.isDataTable(this.table)) {
            try {
                this.table.DataTable({
                    language: {
                        emptyTable: "ไม่มีรายการนัดหมายวันนี้"
                    },
                    destroy: true,
                    searching: false,
                    paging: false,
                    info: false,
                    ordering: false,
                    responsive: true
                });
            } catch (e) {
                console.warn('DataTable initialization error:', e);
            }
        }
    }

    /**
     * ทำลาย DataTable
     */
    destroyDataTable() {
        if (this.table && $.fn.DataTable.isDataTable(this.table)) {
            try {
                this.table.DataTable().destroy();
            } catch (e) {
                console.warn('Error destroying DataTable:', e);
            }
        }
    }

    /**
     * รีเฟรชข้อมูล
     */
    refresh() {
        this.loadData();
    }
}

// เริ่มต้น component เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function () {
    window.makeAppointmentToday = new MakeAppointmentToday();
});

// Export สำหรับการใช้งานภายนอก
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MakeAppointmentToday;
}