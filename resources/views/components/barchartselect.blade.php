{{--
    Bar Chart Select Component

    Props:
    - $title: Chart title (optional)
    - $rightTitle: Right panel title (optional)
--}}

@props([
    'title' => 'กราฟแสดงโรคเฝ้าระวัง',
    'rightTitle' => 'รายงานโรคเฝ้าระวัง'
])

<div class="row">
    <!-- Left Column - Chart -->
    <div class="col-8">
        <div class="card border-warning border-3"
             style="height: 285px; min-height: 285px; max-height: 285px;">
            <div class="card-body p-3 h-100 d-flex flex-column">
                <!-- Header Section (ความสูงคงที่) -->
                <div class="d-flex justify-content-between align-items-center mb-3"
                     style="height: 40px; flex-shrink: 0;">
                    <h5 class="text-dark mb-0">{{ $title }}</h5>
                </div>

                <!-- Chart Container (เต็มพื้นที่ที่เหลือ) -->
                <div class="flex-grow-1 position-relative d-flex align-items-center justify-content-center"
                     style="height: calc(100% - 64px); overflow: hidden;">
                    <canvas id="diseaseGraph"
                            style="max-width: 100%; max-height: 100%;"></canvas>

                    <!-- No Data Message -->
                    <div id="noDiseaseMessage"
                         class="position-absolute top-50 start-50 translate-middle text-center"
                         style="display: none;">
                        กรุณาเลือกรหัสโรคเพื่อแสดงกราฟ
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Disease List -->
    <div class="col-4">
        <div class="card border-warning border-3"
             style="height: 285px; min-height: 285px; max-height: 285px;">
            <div class="d-flex flex-column" style="padding: 0;">
                <!-- Header Section -->
                <div class="card-header-custom" style="padding: 12px 16px 8px 16px;">
                    <h6 class="card-title-custom" style="margin-bottom: 4px;">
                        {{ $rightTitle }}
                    </h6>
                    <p class="card-subtitle-custom" id="dateRange" style="margin-bottom: 0;">
                        <strong>เวลา:</strong> <span id="filterText">ทั้งหมด</span>
                    </p>
                </div>

                <!-- Disease List -->
                <div class="disease-list" id="diseaseList"
                     style="flex: 1; overflow-y: auto; padding: 0 12px 8px 12px;">
                    <!-- Disease items will be populated here by JS -->
                </div>

                <!-- Button -->
                <div class="fixed-bottom-button-container">
                    <button id="openPopup" class="detail-button">ระบุรหัสโรค</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับตั้งค่ารหัสโรคและวันที่ -->
<div class="modal fade" id="diseaseModal" tabindex="-1" aria-labelledby="diseaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="diseaseModalLabel">กรุณากรอกรหัสโรคและเลือกช่วงวันที่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- รหัสโรค -->
                <div class="mb-3">
                    <label for="diseaseCodes" class="form-label fw-bold">กรอกรหัสโรค</label>
                    <input type="text" class="form-control mb-2" id="diseaseCodes"
                           placeholder="พิมพ์รหัสแล้วกด Enter หรือ Space">
                    <!-- Tag preview -->
                    <div id="diseaseTagPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>

                <!-- ตัวเลือกช่วงวันที่ -->
                <div class="mb-3">
                    <label for="dateOption" class="form-label fw-semibold text-dark">เลือกวันที่:</label>
                    <select id="dateOption" class="form-select">
                        <option value="today">วันนี้</option>
                        <option value="range" selected>ระหว่างวันที่</option>
                        <option value="all">ทั้งหมด</option>
                    </select>
                </div>

                <!-- กล่องช่วงวันที่ -->
                <div id="dateRangeInputs" class="row g-2 mb-3" style="display: flex;">
                    <div class="col">
                        <label class="form-label small">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>
                    <div class="col-auto d-flex align-items-end">
                        <span class="mb-2">ถึง</span>
                    </div>
                    <div class="col">
                        <label class="form-label small">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" id="clearTagsBtn">
                    <i class="fas fa-trash me-1"></i>ล้างรหัสทั้งหมด
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" id="fetchData">
                    <i class="fas fa-chart-bar me-1"></i>แสดงข้อมูล
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset(path: 'css/components/barchartselect.css') }}">
<script src="{{ asset('js/components/barchartselect.js') }}"></script>