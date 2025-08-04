// ===== GLOBAL VARIABLES =====
let diseaseChart = null;
let diseaseCodes = [];

// ===== GLOBAL COLORS =====
const GRAPH_COLORS = [
    '#FF6B6B',  // 1. สีแดงสด 🔴
    '#A855F7',  // 2. สีม่วงสว่าง 💜
    '#06B6D4',  // 4. สีฟ้าใสสว่าง 🌊
    '#FDE047',  // 5. สีเหลืองสว่าง 🟡
    '#F472B6',  // 6. สีชมพูสว่าง 💖
    '#FB923C',  // 9. สีส้มสว่าง 🧡
    '#34D399'   // 10. สีเขียวสว่าง 🌿
];

// ===== DOM ELEMENTS =====
const tagInput = document.getElementById('diseaseCodes');
const tagPreview = document.getElementById('diseaseTagPreview');

// ===== INITIALIZATION =====
window.onload = function () {
    loadSavedData();
    fetchSelectedDiseases();
    initializeEventListeners();
};

// ===== LOAD SAVED DATA =====
function loadSavedData() {
    const savedCodes = localStorage.getItem('savedDiseaseCodes');
    const savedOption = localStorage.getItem('savedDateOption');
    const savedStart = localStorage.getItem('savedStartDate');
    const savedEnd = localStorage.getItem('savedEndDate');

    if (savedCodes) {
        diseaseCodes = savedCodes.split(',');
        renderTags();
    }

    if (savedOption) {
        const dateOption = document.getElementById('dateOption');
        if (dateOption) {
            dateOption.value = savedOption;
            const dateRangeInputs = document.getElementById('dateRangeInputs');
            if (dateRangeInputs) {
                dateRangeInputs.style.display = savedOption === 'range' ? 'flex' : 'none';
            }
        }
    }

    if (savedStart) {
        const startDate = document.getElementById('startDate');
        if (startDate) startDate.value = savedStart;
    }
    if (savedEnd) {
        const endDate = document.getElementById('endDate');
        if (endDate) endDate.value = savedEnd;
    }
}

// ===== EVENT LISTENERS INITIALIZATION =====
function initializeEventListeners() {
    // Tag Input
    if (tagInput) {
        tagInput.addEventListener('keydown', handleTagInput);
    }

    // Date Option
    const dateOption = document.getElementById('dateOption');
    if (dateOption) {
        dateOption.addEventListener('change', handleDateOptionChange);
    }

    // Modal Controls
    const openPopup = document.getElementById('openPopup');
    if (openPopup) {
        openPopup.addEventListener('click', openModal);
    }

    const fetchDataBtn = document.getElementById('fetchData');
    if (fetchDataBtn) {
        fetchDataBtn.addEventListener('click', handleFetchData);
    }

    const clearTagsBtn = document.getElementById('clearTagsBtn');
    if (clearTagsBtn) {
        clearTagsBtn.addEventListener('click', clearAllData);
    }

    // Tab Functionality
    initializeTabFunctionality();
}

// ===== TAG MANAGEMENT =====
function handleTagInput(event) {
    if (['Enter', ' ', ','].includes(event.key)) {
        event.preventDefault();
        const value = tagInput.value.trim().toUpperCase();
        if (value && !diseaseCodes.includes(value)) {
            diseaseCodes.push(value);
            renderTags();
        }
        tagInput.value = '';
    }
}

function renderTags() {
    if (!tagPreview) return;

    tagPreview.innerHTML = '';
    diseaseCodes.forEach((code, index) => {
        const tag = document.createElement('span');
        tag.className = 'tag';
        tag.innerHTML = `${code} <span class="remove-tag" data-index="${index}">&times;</span>`;
        tagPreview.appendChild(tag);
    });

    document.querySelectorAll('.remove-tag').forEach(btn => {
        btn.addEventListener('click', function () {
            const i = this.getAttribute('data-index');
            diseaseCodes.splice(i, 1);
            renderTags();
        });
    });
}

function getDiseaseCodeString() {
    return diseaseCodes.join(',');
}

// ===== DATE HANDLING =====
function handleDateOptionChange() {
    const dateRangeInputs = document.getElementById('dateRangeInputs');
    if (dateRangeInputs) {
        dateRangeInputs.style.display = this.value === 'range' ? 'flex' : 'none';
    }
}

// ===== MODAL CONTROLS =====
function openModal() {
    const modal = new bootstrap.Modal(document.getElementById('diseaseModal'));
    modal.show();
}

async function handleFetchData() {
    await fetchSelectedDiseases();
    const modal = bootstrap.Modal.getInstance(document.getElementById('diseaseModal'));
    if (modal) modal.hide();
}

function clearAllData() {
    diseaseCodes = [];
    renderTags();
    if (tagInput) tagInput.value = '';

    // Clear localStorage
    ['savedDiseaseCodes', 'savedDateOption', 'savedStartDate', 'savedEndDate']
        .forEach(key => localStorage.removeItem(key));

    // ล้างข้อมูลใน diseaseList ก่อน
    const diseaseList = document.getElementById('diseaseList');
    if (diseaseList) {
        diseaseList.innerHTML = '';
    }

    // Reset UI
    showEmptyState();
    showSurveillanceEmptyState();
    showDiseaseListEmptyState();

    const noDiseaseMessage = document.getElementById('noDiseaseMessage');
    if (noDiseaseMessage) noDiseaseMessage.style.display = 'block';

    if (diseaseChart) {
        diseaseChart.destroy();
        diseaseChart = null;
    }
}

// ===== MAIN DATA FETCHING =====
async function fetchSelectedDiseases() {
    const codes = getDiseaseCodeString();
    const dateOption = document.getElementById('dateOption');
    const dateOptionValue = dateOption ? dateOption.value : 'all';

    let startDate = '';
    let endDate = '';

    if (!codes) {
        showNoDataState();
        return;
    }

    // Get date range
    if (dateOptionValue === 'today') {
        const today = new Date().toISOString().split('T')[0];
        startDate = endDate = today;
    } else if (dateOptionValue === 'range') {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        startDate = startDateInput ? startDateInput.value : '';
        endDate = endDateInput ? endDateInput.value : '';

        if (!startDate || !endDate) {
            alert('กรุณาระบุวันที่เริ่มต้นและสิ้นสุด');
            return;
        }
    }

    // Save to localStorage
    saveToLocalStorage(codes, dateOptionValue, startDate, endDate);

    // Show graph
    const noDiseaseMessage = document.getElementById('noDiseaseMessage');
    const diseaseGraph = document.getElementById('diseaseGraph');
    if (noDiseaseMessage) noDiseaseMessage.style.display = 'none';
    if (diseaseGraph) diseaseGraph.style.display = 'block';

    try {
        const queryParams = new URLSearchParams({ codes, start: startDate, end: endDate });
        const response = await fetch(`/get-diseases-data?${queryParams.toString()}`);

        if (!response.ok) {
            throw new Error('เกิดข้อผิดพลาดในการดึงข้อมูล');
        }

        const data = await response.json();
        updateStatistics(data, dateOptionValue, startDate, endDate);
        createDiseaseChart(data);

    } catch (error) {
        console.error('Error fetching disease data:', error);
        alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
    }
}

// ===== HELPER FUNCTIONS =====
function showNoDataState() {
    const noDiseaseMessage = document.getElementById('noDiseaseMessage');
    const diseaseGraph = document.getElementById('diseaseGraph');

    if (noDiseaseMessage) noDiseaseMessage.style.display = 'block';
    if (diseaseGraph) diseaseGraph.style.display = 'none';

    showEmptyState();
    showSurveillanceEmptyState();
    showDiseaseListEmptyState();
}

function showDiseaseListEmptyState() {
    const diseaseList = document.getElementById('diseaseList');
    if (diseaseList) {
        diseaseList.innerHTML = '';
        diseaseList.innerHTML = `
            <div class="empty-state text-center p-4">
                <i class="fas fa-virus empty-state-icon"></i>
                <h6>ยังไม่มีข้อมูลโรคเฝ้าระวัง</h6>
                <p class="small mb-0">กรุณาเลือกรหัสโรคเพื่อดูสถิติ</p>
            </div>
        `;
    }
}

function saveToLocalStorage(codes, dateOption, startDate, endDate) {
    localStorage.setItem('savedDiseaseCodes', codes);
    localStorage.setItem('savedDateOption', dateOption);
    localStorage.setItem('savedStartDate', startDate);
    localStorage.setItem('savedEndDate', endDate);
}

// ===== CHART CREATION =====
function createDiseaseChart(data) {
    const allLabels = diseaseCodes.map(code => {
        const found = data.find(d => d.disease_code === code);
        return found ? found.disease_code : code;
    });

    const allValues = diseaseCodes.map(code => {
        const found = data.find(d => d.disease_code === code);
        return found ? found.count : 0;
    });

    const allColors = diseaseCodes.map((code, i) => {
        const found = data.find(d => d.disease_code === code);
        const count = found ? found.count : 0;
        return count === 0 ? '#9aa0a6' : GRAPH_COLORS[i % GRAPH_COLORS.length];
    });

    if (diseaseChart) {
        diseaseChart.destroy();
    }

    const ctx = document.getElementById('diseaseGraph');
    if (!ctx) return;

    diseaseChart = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: allLabels,
            datasets: [{
                data: allValues,
                backgroundColor: allColors,
                borderColor: allColors,
                borderWidth: 1,
                borderRadius: 5,
                barPercentage: 0.8,
                categoryPercentage: 0.8,
                hoverBackgroundColor: allColors.map((color, index) => {
                    return color === '#9aa0a6' ? color : shadeColor(color, -15);
                })
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 20, bottom: 10, left: 15, right: 15 } },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    borderColor: 'rgba(0,0,0,0.1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    padding: 12,
                    titleColor: '#2c3e50',
                    bodyColor: '#6c757d',
                    callbacks: {
                        label: function (tooltipItem) {
                            const label = tooltipItem.label;
                            const value = tooltipItem.raw;
                            const disease = data.find(d => d.disease_code === label);
                            const diseaseName = disease ? disease.name : 'ไม่มีข้อมูล';

                            if (value === 0) {
                                return [`${diseaseName}`, `รหัส: ${label}`, `ไม่มีข้อมูลในช่วงเวลานี้`];
                            } else {
                                return [`${diseaseName}`, `รหัส: ${label}`, `จำนวน: ${value} ราย`];
                            }
                        },
                        labelColor: function (context) {
                            return {
                                borderColor: 'transparent',
                                backgroundColor: allColors[context.dataIndex],
                                borderRadius: 4
                            };
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#6c757d', font: { size: 12 } },
                    grid: { display: false, drawBorder: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#6c757d',
                        stepSize: 1,
                        callback: value => Math.floor(value) + (value > 0 ? ' ราย' : '')
                    },
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }
                }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });
}

// ===== STATISTICS UPDATE =====
function updateStatistics(data, dateOption, startDate, endDate) {
    showStatisticsContent();

    const actualData = data;
    const totalCount = actualData.reduce((sum, item) => sum + item.count, 0);
    const diseaseTypes = actualData.length;

    // Update numbers
    const totalElement = document.getElementById('totalCount');
    const typesElement = document.getElementById('diseaseTypes');

    if (totalElement) totalElement.textContent = totalCount;
    if (typesElement) typesElement.textContent = diseaseTypes;

    // Update filter text
    let filterText = '';
    if (dateOption === 'today') {
        filterText = 'วันนี้';
    } else if (dateOption === 'range' && startDate && endDate) {
        filterText = `${formatDate(startDate)} - ${formatDate(endDate)}`;
    } else {
        filterText = 'ทั้งหมด';
    }

    const filterElement = document.getElementById('filterText');
    if (filterElement) filterElement.textContent = filterText;

    updateDiseaseList(actualData);
    updateSurveillanceCard(actualData, dateOption, startDate, endDate);
}

function updateDiseaseList(data) {
    const diseaseListElement = document.getElementById('diseaseList');
    if (!diseaseListElement) return;

    diseaseListElement.innerHTML = '';

    if (!data) {
        return;
    }

    const sortedData = [...data].sort((a, b) => {
        if (a.count === 0 && b.count === 0) return 0;
        if (a.count === 0) return 1;
        if (b.count === 0) return -1;
        return b.count - a.count;
    });

    const totalCount = sortedData.reduce((sum, item) => sum + item.count, 0);

    sortedData.forEach((item, index) => {
        const percentage = totalCount > 0 ? Math.round((item.count / totalCount) * 100) : 0;
        const isZeroCount = item.count === 0;
        const originalIndex = diseaseCodes.indexOf(item.disease_code);
        const color = isZeroCount ? '#9aa0a6' : GRAPH_COLORS[originalIndex % GRAPH_COLORS.length];

        const diseaseBox = document.createElement('div');
        diseaseBox.className = 'disease-box-compact mb-2';
        diseaseBox.classList.add(isZeroCount ? 'zero-count' : 'has-count');

        diseaseBox.innerHTML = `
            <div class="disease-info-compact">
                <div class="disease-color-dot" style="background: ${color};"></div>
                <div class="disease-code">${item.disease_code}</div>
                ${isZeroCount ? '<span class="no-data-text">(ไม่มีข้อมูล)</span>' : ''}
            </div>
            <div class="disease-count ${isZeroCount ? 'zero-count' : 'has-count'}">
                ${item.count} ราย
            </div>
        `;

        if (!isZeroCount) {
            diseaseBox.addEventListener('mouseenter', function () {
                this.style.transform = 'translateX(2px)';
                this.style.boxShadow = `0 2px 8px ${color}40`;
            });

            diseaseBox.addEventListener('mouseleave', function () {
                this.style.transform = 'translateX(0)';
                this.style.boxShadow = 'none';
            });
        }

        diseaseListElement.appendChild(diseaseBox);
    });
}

// ===== SURVEILLANCE CARD UPDATE =====
function updateSurveillanceCard(data, dateOption, startDate, endDate) {
    const diseaseItems = document.getElementById('diseaseItems');
    if (!diseaseItems) return;

    if (!data) {
        showSurveillanceEmptyState();
        return;
    }

    diseaseItems.innerHTML = '';
    const totalCount = data.reduce((sum, item) => sum + item.count, 0);

    // Update date range
    const dateRange = document.getElementById('dateRange');
    if (dateRange) {
        let dateText = '';
        if (dateOption === 'today') {
            dateText = `วันนี้ จำนวน ${totalCount} ราย`;
        } else if (dateOption === 'range' && startDate && endDate) {
            dateText = `วันที่ ${formatDate(startDate)} - ${formatDate(endDate)} จำนวน ${totalCount} ราย`;
        } else {
            dateText = `ข้อมูลทั้งหมด จำนวน ${totalCount} ราย`;
        }
        dateRange.textContent = dateText;
    }

    const sortedData = [...data].sort((a, b) => {
        if (a.count === 0 && b.count === 0) return 0;
        if (a.count === 0) return 1;
        if (b.count === 0) return -1;
        return b.count - a.count;
    });

    // Create disease rows
    sortedData.forEach((item, index) => {
        const percentage = totalCount > 0 ? Math.round((item.count / totalCount) * 100) : 0;
        const isZeroCount = item.count === 0;
        const originalIndex = diseaseCodes.indexOf(item.disease_code);
        const graphColor = isZeroCount ? '#9aa0a6' : GRAPH_COLORS[originalIndex % GRAPH_COLORS.length];

        const diseaseRow = document.createElement('div');
        diseaseRow.className = 'disease-row-compact';
        diseaseRow.classList.add(isZeroCount ? 'zero-count' : 'has-count');

        diseaseRow.innerHTML = `
            <div class="disease-info">
                <div class="disease-color-dot" style="background-color: ${graphColor};"></div>
                <div class="disease-code">
                    ${item.disease_code}
                    ${isZeroCount ? '<span class="no-data-text">(ไม่มีข้อมูล)</span>' : ''}
                </div>
            </div>
            <div class="disease-count">
                ${item.count} ราย
            </div>
        `;

        diseaseItems.appendChild(diseaseRow);
    });
}

// ===== UTILITY FUNCTIONS =====
function shadeColor(color, percent) {
    let R = parseInt(color.substring(1, 3), 16);
    let G = parseInt(color.substring(3, 5), 16);
    let B = parseInt(color.substring(5, 7), 16);
    R = Math.min(255, Math.round(R * (100 + percent) / 100));
    G = Math.min(255, Math.round(G * (100 + percent) / 100));
    B = Math.min(255, Math.round(B * (100 + percent) / 100));
    return "#" + [R, G, B].map(x => x.toString(16).padStart(2, '0')).join('');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('th-TH', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
}

// ===== STATE MANAGEMENT =====
function showStatisticsContent() {
    const emptyElement = document.getElementById('statsEmpty');
    const contentElement = document.getElementById('statsContent');

    if (emptyElement) emptyElement.classList.add('d-none');
    if (contentElement) contentElement.classList.remove('d-none');
}

function showEmptyState() {
    const emptyElement = document.getElementById('statsEmpty');
    const contentElement = document.getElementById('statsContent');

    if (emptyElement) emptyElement.classList.remove('d-none');
    if (contentElement) contentElement.classList.add('d-none');
}

function showSurveillanceEmptyState() {
    const diseaseItems = document.getElementById('diseaseItems');
    if (diseaseItems) {
        diseaseItems.innerHTML = `
            <div class="empty-state text-center p-4">
                <i class="fas fa-virus empty-state-icon"></i>
                <h6>ยังไม่มีข้อมูลโรคเฝ้าระวัง</h6>
                <p class="small mb-0">กรุณาเลือกรหัสโรคเพื่อดูสถิติ</p>
            </div>
        `;
    }
}

// ===== TAB FUNCTIONALITY =====
function initializeTabFunctionality() {
    document.addEventListener('DOMContentLoaded', function () {
        const tabItems = document.querySelectorAll('.top-card-item');
        const contentAreas = document.querySelectorAll('.content-area');

        tabItems.forEach(item => {
            item.addEventListener('click', function () {
                tabItems.forEach(tab => tab.classList.remove('active'));
                this.classList.add('active');

                contentAreas.forEach(content => content.classList.remove('active'));
                const targetContent = document.getElementById(this.dataset.content);
                if (targetContent) {
                    targetContent.classList.add('active');
                }

                if (typeof handleTabChange === 'function') {
                    handleTabChange(this.dataset.content);
                }
            });
        });
    });
}

// ===== EXTERNAL FUNCTIONS =====
function viewDetails() {
    alert('นำทางไปยังหน้ารายงานเชิงลึก');
}