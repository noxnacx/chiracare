const DiseaseReport = {
    API_URL: '/disease-report/current-month',
    primaryColor: '#77B2C9',
    secondaryColor: '#f3e8ff',

    init: function () {
        this.loadData();
        // รีเฟรชข้อมูลทุก 5 นาที
        setInterval(() => this.loadData(), 5 * 60 * 1000);
    },

    loadData: async function () {
        try {
            this.showLoading();

            const response = await fetch(this.API_URL);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const responseData = await response.json();

            const tabTitle = document.getElementById('monthTabTitle');
            if (responseData.auto_detected_period?.thai_month && tabTitle) {
                tabTitle.textContent = `สถิติโรคประจำเดือน ${responseData.auto_detected_period.thai_month}`;
            }

            let rawData = responseData.data || [];
            if (!Array.isArray(rawData)) {
                throw new Error('ข้อมูลที่ได้จาก API ไม่ถูกต้อง (ไม่ใช่ Array)');
            }

            if (rawData.length === 0) {
                this.showNoData('ยังไม่มีการวินิจฉัยโรคในเดือนนี้');
                return;
            }

            const totalCases = rawData.reduce((sum, item) => sum + (item.total_cases || 0), 0);
            if (totalCases === 0) {
                this.showNoData('ยังไม่มีการวินิจฉัยโรคในเดือนนี้');
                return;
            }

            const data = rawData.map(item => {
                const count = item.total_cases || item.count || 0;
                const percentage = totalCases > 0 ? Math.round((count / totalCases) * 100) : 0;

                return {
                    name: item.disease_name || item.name || 'ไม่ระบุ',
                    count: count,
                    percentage: percentage
                };
            });

            this.renderChart(data);
            this.showChart();

        } catch (error) {
            console.error('Disease Report - Error loading data:', error);
            this.showError(error.message || 'ไม่สามารถโหลดข้อมูลได้');
        }
    },

    renderChart: function (data) {
        const container = document.getElementById('progressChart');
        if (!container) return;

        container.innerHTML = '';

        data.forEach(item => {
            const progressItem = document.createElement('div');
            progressItem.className = 'progress-item';
            progressItem.innerHTML = `
    <div class="progress-label">
        <span>${item.name}</span>
        <div class="d-flex align-items-center gap-2">
            <span class="count-text">${item.count} ราย (${item.percentage}%)</span>
        </div>
    </div>
    <div class="custom-progress">
        <div class="progress-bar-custom" style="width: 0%" data-width="${item.percentage}%"></div>
    </div>
    `;
            container.appendChild(progressItem);
        });

        // Animate progress bars
        setTimeout(() => {
            const progressBars = container.querySelectorAll('.progress-bar-custom');
            progressBars.forEach(bar => {
                const width = bar.getAttribute('data-width');
                bar.style.width = width;
            });
        }, 300);
    },

    showLoading: function () {
        const loadingElement = document.getElementById('diseaseLoadingState') || document.getElementById('loadingState');
        const errorElement = document.getElementById('diseaseErrorState') || document.getElementById('errorState');
        const chartElement = document.getElementById('diseaseChartContent') || document.getElementById('chartContent');

        if (loadingElement) loadingElement.classList.remove('d-none');
        if (errorElement) errorElement.classList.add('d-none');
        if (chartElement) chartElement.classList.add('d-none');
    },

    showError: function (message) {
        const loadingElement = document.getElementById('diseaseLoadingState') || document.getElementById('loadingState');
        const errorElement = document.getElementById('diseaseErrorState') || document.getElementById('errorState');
        const chartElement = document.getElementById('diseaseChartContent') || document.getElementById('chartContent');
        const errorMessageElement = document.getElementById('diseaseErrorMessage') || document.getElementById('errorMessage');

        if (loadingElement) loadingElement.classList.add('d-none');
        if (errorElement) errorElement.classList.remove('d-none');
        if (chartElement) chartElement.classList.add('d-none');
        if (errorMessageElement) errorMessageElement.textContent = message;
    },

    showChart: function () {
        const loadingElement = document.getElementById('diseaseLoadingState') || document.getElementById('loadingState');
        const errorElement = document.getElementById('diseaseErrorState') || document.getElementById('errorState');
        const chartElement = document.getElementById('diseaseChartContent') || document.getElementById('chartContent');

        if (loadingElement) loadingElement.classList.add('d-none');
        if (errorElement) errorElement.classList.add('d-none');
        if (chartElement) chartElement.classList.remove('d-none');
    },

    showNoData: function (message) {
        const container = document.getElementById('progressChart');
        if (!container) return;

        container.innerHTML = `
    <div class="no-data-container" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
                text-align: center;
                color: #6c757d;
                background-color: #f8f9fa;
                border-radius: 12px;
                border: 1px solid #e0e0e0;
            ">
        <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;">📊</div>
        <h5 style="color: #495057; margin-bottom: 8px; font-weight: 500;">${message}</h5>
        <p style="color: #6c757d; margin: 0; font-size: 14px;">ข้อมูลจะแสดงเมื่อมีการวินิจฉัยโรคแล้ว</p>
    </div>
    `;

        this.showChart();
    }
};

// เรียกใช้เมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('progressChart')) {
        DiseaseReport.init();
    }
});

