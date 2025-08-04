{{--
Progress Bar Monthly Component

Props:
- $title: Chart title (default: 'สถิติโรคประจำเดือน')
- $loadingText: Loading message (default: 'กำลังโหลดข้อมูล...')
- $errorTitle: Error title (default: 'เกิดข้อผิดพลาด')
- $errorMessage: Error message (default: 'ไม่สามารถโหลดข้อมูลได้')
- $retryText: Retry button text (default: 'ลองใหม่')
- $loadFunction: JavaScript function name for loading data (default: 'loadData')
--}}

@props([
    'title' => 'สถิติโรคประจำเดือน',
    'loadingText' => 'กำลังโหลดข้อมูล...',
    'errorTitle' => 'เกิดข้อผิดพลาด',
    'errorMessage' => 'ไม่สามารถโหลดข้อมูลได้',
    'retryText' => 'ลองใหม่',
    'loadFunction' => 'loadData'
])

<!-- Loading State -->
<div id="loadingState" class="text-center py-3">
    <div class="loading-spinner"></div>
    <p class="mt-2 text-muted small">{{ $loadingText }}</p>
</div>

<!-- Error State -->
<div id="errorState" class="text-center py-3 d-none">
    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
    <h6 class="mt-2 text-muted">{{ $errorTitle }}</h6>
    <p class="text-muted small" id="errorMessage">{{ $errorMessage }}</p>
    <button class="btn btn-outline-primary btn-sm" onclick="{{ $loadFunction }}()">
        <i class="fas fa-refresh me-1"></i>
        {{ $retryText }}
    </button>
</div>

<!-- Chart Content -->
<h5><span id="monthTabTitle">{{ $title }}</span></h5>

<div id="chartContent" class="d-none chart-content-container">
    <div id="progressChart" class="chart-canvas">
        <!-- Progress bars will be generated here -->
    </div>
</div>

<script src="js/components/progressbarmonth.js"></script>
