// ✅ Inject styles at the beginning
(function injectStyles() {
    const style = document.createElement('style');
    style.textContent = `
        .patient-id-cell {
            font-size: 12px;
            color: #6c757d;
        }

        .patient-name-cell {
            font-weight: 600;
            color: var(--text-color);
        }

        .unit-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin: 2px;
            border: 1px solid;
        }

        .rotation-badge {
            background: var(--secondary-color);
            color: var(--text-color);
            border-color: var(--primary-color);
        }

        .training-badge {
            background: linear-gradient(135deg, var(--accent-color), rgba(243, 232, 255, 0.5));
            color: #7c3aed;
            border-color: #a855f7;
        }

        .risk-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 0.85em;
            font-weight: 500;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .risk-badge:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        .risk-bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .risk-text {
            color: #333;
        }

        .green .risk-bullet {
            background-color: #27ae60;
        }

        .yellow .risk-bullet {
            background-color: #f39c12;
        }

        .red .risk-bullet {
            background-color: #e74c3c;
        }

        .medical-report-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 8px;
            border-radius: 16px;
            font-size: 0.85em;
            font-weight: 500;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .medical-report-status-badge:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        .status-bullet {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-text {
            color: #333;
        }

        .sent .status-bullet {
            background-color: #FF9800;
        }

        .approved .status-bullet {
            background-color: #4CAF50;
        }
    `;
    document.head.appendChild(style);
})();

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadData);

// Error display function
function showError() {
    const tableBody = document.getElementById('tableBody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่ภายหลัง
                </td>
            </tr>
        `;
    }
}

// Data fetching function
async function loadData() {
    try {
        const response = await fetch('/medical-reports/soldier-info?status=approved');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

        const data = result.data || [];
        const summary = result.summary || {};

        fillTable(data, summary);

    } catch (error) {
        console.error('Error loading data:', error);
        showError();
    }
}

// Table population function
function fillTable(data, summary) {
    const tableBody = document.getElementById('tableBody');
    const table = $('#todaymakeappointmenttodayTable');

    // Update summary count
    const totalCountElement = document.getElementById('totalCount_sent');
    if (totalCountElement) {
        totalCountElement.textContent = `${summary.total || 0} เคส`;
    }

    // Check if table body exists
    if (!tableBody) {
        console.warn('Table body element not found');
        return;
    }

    // Clear existing data
    tableBody.innerHTML = '';

    // Handle empty data case
    if (!data || data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td>
            </tr>
        `;

        // Destroy existing DataTable if any
        if ($.fn.DataTable.isDataTable(table)) {
            try {
                table.DataTable().destroy();
            } catch (e) {
                console.warn('Error destroying DataTable:', e);
            }
        }
        return;
    }

    // Populate table rows
    data.slice(0, 5).forEach((item) => {
        const row = document.createElement('tr');

        // Soldier ID
        const idCell = document.createElement('td');
        idCell.className = 'patient-id-cell';
        idCell.textContent = item.soldier_id_card || '-';

        // Full Name
        const nameCell = document.createElement('td');
        nameCell.className = 'patient-name-cell';
        nameCell.textContent = `${item.first_name || ''} ${item.last_name || ''}`.trim() || '-';

        // Rotation
        const rotationCell = document.createElement('td');
        const rotationBadge = document.createElement('span');
        rotationBadge.className = 'unit-badge rotation-badge';
        rotationBadge.textContent = item.rotation_name || '-';
        rotationCell.appendChild(rotationBadge);

        // Unit
        const unitCell = document.createElement('td');
        const unitBadge = document.createElement('span');
        unitBadge.className = 'unit-badge training-badge';
        unitBadge.textContent = item.unit_name || '-';
        unitCell.appendChild(unitBadge);

        // Risk Level
        const riskCell = document.createElement('td');
        const riskBadge = document.createElement('span');

        const riskLevel = (item.risk_level || 'green').toLowerCase();
        let riskText = '';

        switch (riskLevel) {
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

        riskBadge.className = `risk-badge ${riskLevel}`;
        riskBadge.innerHTML = `
            <span class="risk-bullet"></span>
            <span class="risk-text">${riskText}</span>
        `;
        riskCell.appendChild(riskBadge);

        // Medical Report Status
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');

        const status = (item.medical_report_status || 'sent').toLowerCase();
        let statusText = '';

        switch (status) {
            case 'sent':
                statusText = 'รอการอนุมัติ';
                break;
            case 'approved':
                statusText = 'อนุมัติแล้ว';
                break;
            default:
                statusText = 'ไม่ระบุสถานะ';
        }

        statusBadge.className = `medical-report-status-badge ${status}`;
        statusBadge.innerHTML = `
            <span class="status-bullet"></span>
            <span class="status-text">${statusText}</span>
        `;
        statusCell.appendChild(statusBadge);

        // Append all cells to the row
        row.appendChild(nameCell);
        row.appendChild(idCell);
        row.appendChild(rotationCell);
        row.appendChild(unitCell);
        row.appendChild(riskCell);
        row.appendChild(statusCell);

        // Append row to table
        tableBody.appendChild(row);
    });

    // Initialize DataTable if not already initialized
    if (!$.fn.DataTable.isDataTable(table)) {
        try {
            table.DataTable({
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