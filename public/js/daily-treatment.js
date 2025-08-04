// Global variables
let doughnutChart = null;
let apiData = null;

// Status color mapping
const statusColors = {
    'waiting_checkin': '#ffc107',
    'waiting_treatment': '#17a2b8',
    'completed_treatment': '#28a745',
    'missed': '#dc3545'
};

function createDynamicStyles() {
    const styleElement = document.createElement('style');
    styleElement.textContent = `
        .chart-labels {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 15;
        }

        .label-item {
            position: absolute;
            display: flex;
            align-items: center;
            font-size: 11px !important;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
            z-index: 15;
            pointer-events: none;
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .label-text {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 2px solid #ddd !important;
            border-radius: 8px !important;
            padding: 6px 10px !important;
            font-size: 10px !important;
            font-weight: 600;
            color: #333;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
            white-space: nowrap;
            min-width: 80px;
            transition: all 0.3s ease;
        }

        /* สีขอบตามสีของกราฟ */
        .label-item[data-color="#ffc107"] .label-text {
            border-color: #ffc107 !important;
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 255, 255, 0.9)) !important;
        }

        .label-item[data-color="#17a2b8"] .label-text {
            border-color: #17a2b8 !important;
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(255, 255, 255, 0.9)) !important;
        }

        .label-item[data-color="#28a745"] .label-text {
            border-color: #28a745 !important;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(255, 255, 255, 0.9)) !important;
        }

        .label-item[data-color="#dc3545"] .label-text {
            border-color: #dc3545 !important;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(255, 255, 255, 0.9)) !important;
        }

        /* Hover Effect */
        .label-item:hover .label-text {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        @media (max-width: 991px) {
            .label-item {
                font-size: 10px !important;
            }

            .label-text {
                padding: 4px 8px !important;
                font-size: 9px !important;
                min-width: 70px;
            }
        }

        @media (max-width: 768px) {
            .label-item {
                font-size: 9px !important;
            }

            .label-text {
                padding: 3px 6px !important;
                font-size: 8px !important;
                min-width: 60px;
            }
        }

        /* Custom Legend Styles */
        .custom-legend-right {
            padding: 12px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
            height: fit-content;
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            z-index: 5;
        }

        .legend-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .legend-title {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .legend-subtitle {
            font-size: 10px;
            color: #6c757d;
            font-weight: 500;
            line-height: 1.2;
        }

        .legend-content {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 8px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .legend-item:hover {
            background: #e9ecef;
            transform: translateX(1px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            flex-shrink: 0;
            border: 2px solid white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .legend-text {
            font-size: 11px;
            font-weight: 500;
            color: #2c3e50;
            text-align: left;
            margin-right: auto;
        }

        .legend-value {
            font-size: 10px;
            font-weight: 600;
            color: #495057;
            background: white;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            white-space: nowrap;
        }

        .legend-footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }

        .detail-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
            width: 100%;
        }

        .detail-btn:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 123, 255, 0.25);
            color: white;
        }

        .detail-btn i {
            margin-right: 4px;
        }

        @media (max-width: 991px) {
            .custom-legend-right {
                padding: 10px;
                max-width: 250px;
            }

            .legend-title {
                font-size: 13px;
            }

            .legend-subtitle {
                font-size: 9px;
            }

            .legend-text {
                font-size: 10px;
            }

            .legend-value {
                font-size: 9px;
                padding: 2px 5px;
            }
        }

        @media (max-width: 768px) {
            .custom-legend-right {
                padding: 8px;
                max-width: 220px;
            }

            .legend-content {
                gap: 5px;
            }

            .legend-item {
                padding: 5px 6px;
            }

            .legend-color {
                width: 10px;
                height: 10px;
                margin-right: 6px;
            }

            .detail-btn {
                padding: 5px 10px;
                font-size: 9px;
            }
        }
    `;
    document.head.appendChild(styleElement);
}

// Apply dynamic styles
function applyDynamicStyles() {
    createDynamicStyles();
}

// 🔥 1. โมดูลโหลดข้อมูล (แยกใช้ได้)
async function loadApiData() {
    try {
        const response = await fetch('/daily-treatment/status');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('API Response:', data);
        return data;
    } catch (error) {
        console.error('Error loading data:', error);
        return getDemoData();
    }
}

// 🔥 2. โมดูลข้อมูลสำรอง (แยกใช้ได้)
function getDemoData() {
    return {
        summary: {
            total_appointments: 0,
            status_breakdown: [
                {
                    status: 'waiting_checkin',
                    status_label: 'รอเช็คอิน',
                    count: 0,
                    percentage: '0.0'
                },
                {
                    status: 'waiting_treatment',
                    status_label: 'รอรักษา',
                    count: 0,
                    percentage: '0.0'
                },
                {
                    status: 'completed_treatment',
                    status_label: 'รักษาเสร็จ',
                    count: 0,
                    percentage: '0.0'
                },
                {
                    status: 'missed',
                    status_label: 'ไม่มา',
                    count: 0,
                    percentage: '0.0'
                }
            ]
        },
        date_info: {
            thai_date: 'วันที่ไม่ระบุ',
            day_of_week: 'วัน'
        }
    };
}

// 🔥 3. โมดูลอัปเดตสถิติประจำวัน (แยกใช้ได้)
function updateTodayStaticContainer(data) {
    if (!data || !data.summary) return;

    // อัปเดตแต่ละสถานะ
    const statusBreakdown = data.summary.status_breakdown;
    statusBreakdown.forEach(item => {
        const statusElement = document.getElementById(item.status);
        if (statusElement) {
            statusElement.textContent = item.count;
        }
    });

    // อัปเดตวันที่
    const dateElement = document.getElementById('today_date');
    if (dateElement && data.date_info) {
        dateElement.textContent = `${data.date_info.thai_date} (${data.date_info.day_of_week})`;
    }

    // อัปเดตยอดรวม
    const totalElement = document.getElementById('total_appointments');
    if (totalElement) {
        totalElement.textContent = data.summary.total_appointments;
    }
}

// 🔥 4. โมดูลสร้าง Legend (แยกใช้ได้)
function createCustomLegend(data = null, targetElementId = 'customLegendRight') {
    const sourceData = data || apiData;
    const customLegend = document.getElementById(targetElementId);

    if (!customLegend || !sourceData) return;

    customLegend.innerHTML = '';

    const total = sourceData.summary.total_appointments;
    const isAllZero = total === 0 || sourceData.summary.status_breakdown.every(item => item.count === 0);

    // สร้าง Header
    const legendHeader = document.createElement('div');
    legendHeader.className = 'legend-header';
    legendHeader.innerHTML = `
        <div class="legend-title">สถิติการรักษารายวัน</div>
        <div class="legend-subtitle">นัดหมายวันที่ ${sourceData.date_info.thai_date} จำนวน ${total} ราย</div>
    `;
    customLegend.appendChild(legendHeader);

    // สร้าง Content Container
    const legendContent = document.createElement('div');
    legendContent.className = 'legend-content';

    // สร้าง Items
    sourceData.summary.status_breakdown.forEach(item => {
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';

        const color = statusColors[item.status] || '#6c757d';

        legendItem.innerHTML = `
            <div class="legend-color" style="background-color: ${color};"></div>
            <span class="legend-text">${item.status_label}</span>
            <span class="legend-value">${item.count} ราย (${item.percentage}%)</span>
        `;

        legendContent.appendChild(legendItem);
    });

    customLegend.appendChild(legendContent);

    // สร้าง Footer พร้อมปุ่ม
    const legendFooter = document.createElement('div');
    legendFooter.className = 'legend-footer';
    legendFooter.innerHTML = `
        <button type="button" class="btn detail-btn" onclick="viewDetailedReport()" ${isAllZero ? 'disabled' : ''}>
            <i class="fas fa-chart-line"></i>
            ดูข้อมูลเชิงลึก
        </button>
    `;
    customLegend.appendChild(legendFooter);
}

// 🔥 5. โมดูลสร้างกราฟ (แยกใช้ได้)
function createDoughnutChart(data = null) {
    const sourceData = data || apiData;
    if (!sourceData || !sourceData.summary) {
        console.error('No data available for chart');
        return;
    }

    const canvasElement = document.getElementById('doughnutChart');
    if (!canvasElement) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvasElement.getContext('2d');

    // Transform data to chart format
    const chartData = sourceData.summary.status_breakdown.map(item => ({
        label: item.status_label,
        value: item.count,
        color: statusColors[item.status] || '#6c757d',
        status: item.status
    }));

    const total = sourceData.summary.total_appointments;
    const isAllZero = total === 0 || chartData.every(item => item.value === 0);

    let labels, values, colors;

    if (isAllZero) {
        labels = ['ไม่มีข้อมูล'];
        values = [1];
        colors = ['#e0e0e0'];
    } else {
        const filteredData = chartData.filter(item => item.value > 0);
        labels = filteredData.map(item => item.label);
        values = filteredData.map(item => item.value);
        colors = filteredData.map(item => item.color);
    }

    // Update center total
    const centerTotalElement = document.getElementById('centerTotal');
    if (centerTotalElement) {
        centerTotalElement.textContent = total;
    }

    // Update page elements
    const totalCountElement = document.getElementById('totalCount');
    if (totalCountElement) {
        totalCountElement.textContent = `รวม ${total} ราย`;
    }

    const dateInfoElement = document.getElementById('dateInfo');
    if (dateInfoElement && sourceData.date_info) {
        dateInfoElement.textContent = `${sourceData.date_info.day_of_week}ที่ ${sourceData.date_info.thai_date}`;
    }

    // Destroy existing chart
    if (doughnutChart) {
        doughnutChart.destroy();
    }

    doughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverBorderWidth: 4,
                hoverOffset: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            animation: {
                animateRotate: true,
                duration: 1000,
                onComplete: function () {
                    if (!isAllZero) {
                        createCustomLabels(chartData.filter(item => item.value > 0));
                    } else {
                        clearCustomLabels();
                    }
                    createCustomLegend(sourceData);
                }
            },
            cutout: '60%'
        }
    });

    createCustomLegend(sourceData);
}

// Show loading state
function showLoading() {
    const chartContainer = document.querySelector('.chart-container');
    const legendContainer = document.getElementById('customLegendRight');

    if (chartContainer) {
        chartContainer.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังโหลดข้อมูล...</p></div>';
    }
    if (legendContainer) {
        legendContainer.innerHTML = '<div class="text-center p-4">กำลังโหลด...</div>';
    }
}

// Show chart container
function showChart() {
    const chartContainer = document.querySelector('.chart-container');
    if (chartContainer && apiData) {
        chartContainer.innerHTML = `
            <canvas id="doughnutChart" class="w-100 h-100"></canvas>
            <div class="center-text">
                <div class="center-total" id="centerTotal">${apiData.summary.total_appointments}</div>
                <div class="center-label">ราย</div>
            </div>
            <div class="chart-labels" id="chartLabels"></div>
        `;
    }
}

// ฟังก์ชันล้าง custom labels
function clearCustomLabels() {
    const chartLabels = document.getElementById('chartLabels');
    if (chartLabels) {
        chartLabels.innerHTML = '';
    }
}

// Create Custom Labels รอบๆ Chart
function createCustomLabels(filteredChartData) {
    const chartLabels = document.getElementById('chartLabels');
    if (!chartLabels || !filteredChartData || filteredChartData.length === 0) return;

    chartLabels.innerHTML = '';

    const chartRect = doughnutChart.canvas.getBoundingClientRect();
    const centerX = chartRect.width / 2;
    const centerY = chartRect.height / 2;
    const outerRadius = Math.min(centerX, centerY) * 0.8;
    const labelRadius = outerRadius + 35;

    const totalFiltered = filteredChartData.reduce((sum, item) => sum + item.value, 0);

    filteredChartData.forEach((item, index) => {
        const percentage = ((item.value / totalFiltered) * 100).toFixed(1);

        let currentAngle = -Math.PI / 2;
        for (let i = 0; i < index; i++) {
            currentAngle += (filteredChartData[i].value / totalFiltered) * 2 * Math.PI;
        }
        const segmentAngle = (item.value / totalFiltered) * 2 * Math.PI;
        const midAngle = currentAngle + segmentAngle / 2;

        const labelX = centerX + Math.cos(midAngle) * labelRadius;
        const labelY = centerY + Math.sin(midAngle) * labelRadius;

        const labelItem = document.createElement('div');
        labelItem.className = 'label-item';
        labelItem.style.left = `${labelX}px`;
        labelItem.style.top = `${labelY}px`;
        labelItem.style.transform = 'translate(-50%, -50%)';

        labelItem.innerHTML = `
            <div class="label-text">
                ${item.label}<br>
                <small>${item.value} ราย (${percentage}%)</small>
            </div>
        `;

        chartLabels.appendChild(labelItem);
    });
}

// 🔥 รวมโหลดทุกอย่าง (แบบเดิม - สำหรับใช้พร้อมกัน)
async function loadData() {
    try {
        showLoading();

        apiData = await loadApiData();

        // อัปเดตทั้งสองส่วนพร้อมกัน
        updateTodayStaticContainer(apiData);

        showChart();
        setTimeout(() => {
            createDoughnutChart(apiData);
        }, 100);

    } catch (error) {
        console.error('Error loading data:', error);
        apiData = getDemoData();
        updateTodayStaticContainer(apiData);
        showChart();
        setTimeout(() => {
            createDoughnutChart(apiData);
        }, 100);
    }
}

// 🎯 ฟังก์ชันโหลดเฉพาะส่วน (ใหม่!)
async function loadOnlyStaticContainer() {
    const data = await loadApiData();
    updateTodayStaticContainer(data);
    return data;
}

async function loadOnlyLegend(targetElementId = 'customLegendRight') {
    const data = await loadApiData();
    createCustomLegend(data, targetElementId);
    return data;
}

async function loadOnlyChart() {
    const data = await loadApiData();
    apiData = data; // Set global data for chart functions
    showChart();
    setTimeout(() => {
        createDoughnutChart(data);
    }, 100);
    return data;
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function () {
    applyDynamicStyles();
    loadData(); // โหลดทุกอย่างพร้อมกัน (แบบเดิม)
});

// Handle window resize
window.addEventListener('resize', function () {
    if (doughnutChart && apiData) {
        doughnutChart.resize();

        const total = apiData.summary.total_appointments;
        const isAllZero = total === 0 || apiData.summary.status_breakdown.every(item => item.count === 0);

        if (!isAllZero) {
            const chartData = apiData.summary.status_breakdown
                .filter(item => item.count > 0)
                .map(item => ({
                    label: item.status_label,
                    value: item.count,
                    color: statusColors[item.status] || '#6c757d',
                    status: item.status
                }));
            setTimeout(() => createCustomLabels(chartData), 100);
        } else {
            clearCustomLabels();
        }
    }
});

// สำหรับฟังก์ชัน viewDetailedReport
function viewDetailedReport() {
    console.log('View detailed report clicked');
}