/**
 * DonutChart Component
 * สำหรับสร้างและจัดการ Donut Chart พร้อม Custom Labels
 */

class DonutChart {
    constructor(options = {}) {
        this.chartInstance = null;
        this.canvasId = options.canvasId || 'doughnutChart';
        this.centerTotalId = options.centerTotalId || 'centerTotal';
        this.chartLabelsId = options.chartLabelsId || 'chartLabels';
        this.onComplete = options.onComplete || null;

        // Status color mapping
        this.statusColors = {
            'waiting_checkin': '#ffc107',
            'waiting_treatment': '#17a2b8',
            'completed_treatment': '#28a745',
            'missed': '#dc3545'
        };

        // Bind methods
        this.handleResize = this.handleResize.bind(this);
        this.init();
    }

    init() {
        // Add resize event listener
        window.addEventListener('resize', this.handleResize);
    }

    async loadApiData() {
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
            return this.getDemoData();
        }
    }

    getDemoData() {
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

    updatePageElements(data) {
        const total = data.summary.total_appointments;

        // Update center total
        const centerTotalElement = document.getElementById(this.centerTotalId);
        if (centerTotalElement) {
            centerTotalElement.textContent = total;
        }

        // Update total count
        const totalCountElement = document.getElementById('totalCount');
        if (totalCountElement) {
            totalCountElement.textContent = `รวม ${total} ราย`;
        }

        // Update date info
        const dateInfoElement = document.getElementById('dateInfo');
        if (dateInfoElement && data.date_info) {
            dateInfoElement.textContent = `${data.date_info.day_of_week}ที่ ${data.date_info.thai_date}`;
        }

        // Update today static container
        this.updateTodayStaticContainer(data);
    }

    updateTodayStaticContainer(data) {
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

    clearCustomLabels() {
        const chartLabels = document.getElementById(this.chartLabelsId);
        if (chartLabels) {
            chartLabels.innerHTML = '';
        }
    }

    createCustomLabels(filteredChartData) {
        const chartLabels = document.getElementById(this.chartLabelsId);
        if (!chartLabels || !filteredChartData || filteredChartData.length === 0) return;

        chartLabels.innerHTML = '';

        const chartRect = this.chartInstance.canvas.getBoundingClientRect();
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
            labelItem.setAttribute('data-color', item.color);
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

    showLoading(containerId = 'chartContainer') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="loading-container">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            `;
        }
    }

    prepareChartContainer(containerId = 'chartContainer') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="chart-container">
                    <canvas id="${this.canvasId}" class="w-100 h-100"></canvas>
                    <div class="center-text">
                        <div class="center-total" id="${this.centerTotalId}">0</div>
                        <div class="center-label">ราย</div>
                    </div>
                    <div class="chart-labels" id="${this.chartLabelsId}"></div>
                </div>
            `;
        }
    }

    createChart(data) {
        if (!data || !data.summary) {
            console.error('No data available for chart');
            return;
        }

        const canvasElement = document.getElementById(this.canvasId);
        if (!canvasElement) {
            console.error('Canvas element not found');
            return;
        }

        const ctx = canvasElement.getContext('2d');

        // Transform data to chart format
        const chartData = data.summary.status_breakdown.map(item => ({
            label: item.status_label,
            value: item.count,
            color: this.statusColors[item.status] || '#6c757d',
            status: item.status
        }));

        const total = data.summary.total_appointments;
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

        // Update page elements
        this.updatePageElements(data);

        // Destroy existing chart
        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Create new chart
        this.chartInstance = new Chart(ctx, {
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
                    onComplete: () => {
                        if (!isAllZero) {
                            this.createCustomLabels(chartData.filter(item => item.value > 0));
                        } else {
                            this.clearCustomLabels();
                        }

                        // Execute callback if provided
                        if (this.onComplete) {
                            this.onComplete(data);
                        }
                    }
                },
                cutout: '60%'
            }
        });

        return this.chartInstance;
    }

    handleResize() {
        if (this.chartInstance && this.chartInstance.data) {
            this.chartInstance.resize();

            // Recreate labels after resize
            setTimeout(() => {
                const hasData = this.chartInstance.data.datasets[0].data.some(value => value > 0);
                if (hasData && this.chartInstance.data.labels[0] !== 'ไม่มีข้อมูล') {
                    const chartData = this.chartInstance.data.labels.map((label, index) => ({
                        label: label,
                        value: this.chartInstance.data.datasets[0].data[index],
                        color: this.chartInstance.data.datasets[0].backgroundColor[index]
                    }));
                    this.createCustomLabels(chartData);
                } else {
                    this.clearCustomLabels();
                }
            }, 100);
        }
    }

    async render(containerId = 'chartContainer') {
        try {
            this.showLoading(containerId);

            const data = await this.loadApiData();

            this.prepareChartContainer(containerId);

            // Small delay to ensure DOM is ready
            setTimeout(() => {
                this.createChart(data);
            }, 100);

            return data;
        } catch (error) {
            console.error('Error rendering chart:', error);
            const demoData = this.getDemoData();
            this.prepareChartContainer(containerId);
            setTimeout(() => {
                this.createChart(demoData);
            }, 100);
            return demoData;
        }
    }

    destroy() {
        if (this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }
        window.removeEventListener('resize', this.handleResize);
    }

    // Static method for backward compatibility
    static async create(options = {}) {
        const chart = new DonutChart(options);
        await chart.render(options.containerId);
        return chart;
    }
}

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DonutChart;
}

// Global availability
window.DonutChart = DonutChart;