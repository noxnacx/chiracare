/**
 * Legend Component
 * สำหรับสร้างและจัดการ Custom Legend
 */

class Legend {
    constructor(options = {}) {
        this.containerId = options.containerId || 'customLegendRight';
        this.onDetailClick = options.onDetailClick || this.defaultDetailClick;

        // Status color mapping
        this.statusColors = {
            'waiting_checkin': '#ffc107',
            'waiting_treatment': '#17a2b8',
            'completed_treatment': '#28a745',
            'missed': '#dc3545'
        };
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

    showLoading() {
        const container = document.getElementById(this.containerId);
        if (container) {
            container.innerHTML = `
                <div class="loading-container">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            `;
        }
    }

    createLegendHeader(data) {
        const total = data.summary.total_appointments;
        const header = document.createElement('div');
        header.className = 'legend-header';
        header.innerHTML = `
            <div class="legend-title">สถิติการรักษารายวัน</div>
            <div class="legend-subtitle">นัดหมายวันที่ ${data.date_info.thai_date} จำนวน ${total} ราย</div>
        `;
        return header;
    }

    createLegendContent(data) {
        const content = document.createElement('div');
        content.className = 'legend-content';

        data.summary.status_breakdown.forEach(item => {
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';

            const color = this.statusColors[item.status] || '#6c757d';

            legendItem.innerHTML = `
                <div class="legend-color" style="background-color: ${color};"></div>
                <span class="legend-text">${item.status_label}</span>
                <span class="legend-value">${item.count} ราย (${item.percentage}%)</span>
            `;

            // Add click event for legend item
            legendItem.addEventListener('click', () => {
                this.onLegendItemClick(item, data);
            });

            content.appendChild(legendItem);
        });

        return content;
    }

    createLegendFooter(data) {
        const total = data.summary.total_appointments;
        const isAllZero = total === 0 || data.summary.status_breakdown.every(item => item.count === 0);

        const footer = document.createElement('div');
        footer.className = 'legend-footer';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn detail-btn';
        button.innerHTML = `
            <i class="fas fa-chart-line"></i>
            ดูข้อมูลเชิงลึก
        `;

        if (isAllZero) {
            button.disabled = true;
        } else {
            button.addEventListener('click', () => {
                this.onDetailClick(data);
            });
        }

        footer.appendChild(button);
        return footer;
    }

    render(data) {
        const container = document.getElementById(this.containerId);
        if (!container) {
            console.error('Legend container not found');
            return;
        }

        container.innerHTML = '';

        // Create legend sections
        const header = this.createLegendHeader(data);
        const content = this.createLegendContent(data);
        const footer = this.createLegendFooter(data);

        // Append all sections
        container.appendChild(header);
        container.appendChild(content);
        container.appendChild(footer);

        // Add custom legend class
        container.classList.add('custom-legend-right');
    }

    async loadAndRender() {
        try {
            this.showLoading();
            const data = await this.loadApiData();
            this.render(data);
            return data;
        } catch (error) {
            console.error('Error loading and rendering legend:', error);
            const demoData = this.getDemoData();
            this.render(demoData);
            return demoData;
        }
    }

    // Event handlers
    onLegendItemClick(item, data) {
        console.log('Legend item clicked:', item);
        // Override this method for custom behavior
    }

    defaultDetailClick(data) {
        console.log('Detail button clicked:', data);
        // Override this method for custom behavior
        if (typeof viewDetailedReport === 'function') {
            viewDetailedReport();
        }
    }

    // Update methods
    updateLegendData(newData) {
        this.render(newData);
    }

    // Utility methods
    setDetailClickHandler(handler) {
        this.onDetailClick = handler;
    }

    setLegendItemClickHandler(handler) {
        this.onLegendItemClick = handler;
    }

    // Static method for backward compatibility
    static async create(options = {}) {
        const legend = new Legend(options);
        await legend.loadAndRender();
        return legend;
    }
}

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Legend;
}

// Global availability
window.Legend = Legend;