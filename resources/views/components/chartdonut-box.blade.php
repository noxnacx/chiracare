@props(['title' => 'สถิติการรักษารายวัน'])

<div id="grid5-content-ranking-chart" class="grid-5-content-area active">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Chart Container with 2 Columns Layout -->
            <div class="row align-items-center justify-content-center" style="min-height: 200px;">

                <!-- Chart Column (Left Side) -->
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="chart-container">
                        <canvas id="doughnutChart" class="w-100 h-100"></canvas>
                        <!-- Center Text -->
                        <div class="center-text">
                            <div class="center-total" id="centerTotal">6</div>
                            <div class="center-label">ราย</div>
                        </div>
                        <!-- Custom Labels รอบๆ Chart -->
                        <div class="chart-labels" id="chartLabels">
                            <!-- Labels จะถูกสร้างด้วย JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Legend Column (Right Side) -->
                <div class="col-md-6 d-flex justify-content-center">
                    <div class="custom-legend-right" id="customLegendRight">
                        <!-- Legend จะถูกสร้างด้วย JavaScript -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="/css/components/chart-new.css">

<!-- Chart Components -->
<script src="js/components/chartdonut.js"></script>
<script src="js/components/legend.js"></script>

<script>
    // Global variables for components
    let donutChart = null;
    let legendComponent = null;

    // Custom detail click handler
    function viewDetailedReport() {
        alert('แสดงรายงานเชิงลึก');
        console.log('View detailed report clicked');
    }

    // Initialize components when page loads
    document.addEventListener('DOMContentLoaded', async function () {
        try {
            // Initialize DonutChart with callback
            donutChart = new DonutChart({
                canvasId: 'doughnutChart',
                centerTotalId: 'centerTotal',
                chartLabelsId: 'chartLabels',
                onComplete: function (data) {
                    console.log('Chart completed with data:', data);
                    // Update legend when chart completes
                    if (legendComponent) {
                        legendComponent.updateLegendData(data);
                    }
                }
            });

            // Initialize Legend with custom handlers
            legendComponent = new Legend({
                containerId: 'customLegendRight',
                onDetailClick: viewDetailedReport
            });

            // Set custom legend item click handler
            legendComponent.setLegendItemClickHandler(function (item, data) {
                console.log('Legend item clicked:', item.status_label);
                alert(`คลิกที่: ${item.status_label} (${item.count} ราย)`);
            });

            // Render both components
            await Promise.all([
                donutChart.render('chartContainer'),
                legendComponent.loadAndRender()
            ]);

            console.log('All components initialized successfully');

        } catch (error) {
            console.error('Error initializing components:', error);
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function () {
        if (donutChart) {
            donutChart.destroy();
        }
    });

    // Example of updating components with new data
    function updateComponentsWithNewData(newData) {
        if (donutChart) {
            donutChart.updatePageElements(newData);
            donutChart.createChart(newData);
        }
        if (legendComponent) {
            legendComponent.updateLegendData(newData);
        }
    }

    // Example of refreshing data
    async function refreshData() {
        if (donutChart) {
            await donutChart.render('chartContainer');
        }
        if (legendComponent) {
            await legendComponent.loadAndRender();
        }
    }
</script>