<!DOCTYPE html>
<html lang="en">
@include('themes.head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --primary-color: #3498db;
        --secondary-color: #2ecc71;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --info-color: #1abc9c;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
    }

    .dashboard-header {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .stat-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-card .card-body {
        padding: 20px;
    }

    .stat-card h5 {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .stat-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0;
    }

    .stat-card .icon {
        font-size: 2rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }

    .appointment-card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .appointment-card .card-header {
        border-radius: 10px 10px 0 0 !important;
        font-weight: 600;
        background-color: var(--primary-color);
        color: white;
    }

    .critical-case {
        border-left: 4px solid var(--danger-color);
        background-color: rgba(231, 76, 60, 0.05);
        margin-bottom: 10px;
        border-radius: 5px;
        padding: 10px;
        transition: all 0.3s ease;
    }

    .critical-case:hover {
        background-color: rgba(231, 76, 60, 0.1);
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .badge-checked-in {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--secondary-color);
    }

    .badge-pending {
        background-color: rgba(243, 156, 18, 0.1);
        color: var(--warning-color);
    }
</style>
</head>


<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')
        @include('themes.opd.menuopd')
        <div class="wrapper">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0">แดชบอร์ดโรงพยาบาล</h2>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">อัปเดตล่าสุด: วันนี้ 10:30 น.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <!-- Statistics Cards Row -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5>OPD <small>ยอดสะสมรายวัน</small></h5>
                                <h3>24 <small>คน</small></h3>
                                <i class="fas fa-procedures icon text-primary"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5>ER <small>ยอดสะสมรายวัน</small></h5>
                                <h3>8 <small>คน</small></h3>
                                <i class="fas fa-ambulance icon text-danger"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5>IPD <small>ยอดสะสมรายวัน</small></h5>
                                <h3>15 <small>คน</small></h3>
                                <i class="fas fa-bed icon text-info"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h5>นัดหมายจิตเวชวันนี้</h5>
                                <h3>3 <small>คน</small></h3>
                                <i class="fas fa-brain icon text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Section -->
                <div class="row mb-4">
                    <!-- Normal Appointments -->
                    <div class="col-md-6 mb-4">
                        <div class="card appointment-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>นัดหมายปกติวันนี้ <strong>(12 คน)</strong></span>
                                <a href="#" class="btn btn-sm btn-light">ดูทั้งหมด</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ-สกุล</th>
                                                <th>นัดหมาย</th>
                                                <th>สถานะ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>พล.ท. สมชาย ใจดี</td>
                                                <td>
                                                    <small>เวลา: 09:30 น.</small><br>
                                                    <small>สถานที่: OPD</small>
                                                </td>
                                                <td><span
                                                        class="badge-status badge-checked-in">เข้ารับการรักษาแล้ว</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>พล.ต. เอกชัย สุขใจ</td>
                                                <td>
                                                    <small>เวลา: 10:15 น.</small><br>
                                                    <small>สถานที่: OPD</small>
                                                </td>
                                                <td><span
                                                        class="badge-status badge-checked-in">เข้ารับการรักษาแล้ว</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>พล.ต.ต. วีระชัย เก่งดี</td>
                                                <td>
                                                    <small>เวลา: 13:45 น.</small><br>
                                                    <small>สถานที่: OPD</small>
                                                </td>
                                                <td><span
                                                        class="badge-status badge-pending">ยังไม่เข้ารับการรักษา</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Critical Cases -->
                    <div class="col-md-6 mb-4">
                        <div class="card appointment-card">
                            <div
                                class="card-header d-flex justify-content-between align-items-center bg-danger text-white">
                                <span>เคสวิกฤติ <strong>(3 เคส)</strong></span>
                                <a href="#" class="btn btn-sm btn-light">ดูทั้งหมด</a>
                            </div>
                            <div class="card-body">
                                <div class="critical-case">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>พล.อ.ต. สุทธิพงษ์ จิตดี</strong>
                                            <div class="text-muted small">สถานที่: ER</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">10:30 น.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="critical-case">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>พล.ท. ชัยวัฒน์ สุขสันต์</strong>
                                            <div class="text-muted small">สถานที่: IPD</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">11:45 น.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="critical-case">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>พล.ต.อ. อนันต์ ใจกว้าง</strong>
                                            <div class="text-muted small">สถานที่: ER</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">14:20 น.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-4">
                        <div class="card appointment-card">
                            <div class="card-header">
                                สถิติโรคที่ต้องเฝ้าระวัง
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="topDiseasesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card appointment-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>กราฟโรคที่ต้องการ</span>
                                <button class="btn btn-sm btn-primary" id="openDiseaseModal">
                                    กรอกรหัสโรค
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="diseaseGraph"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disease Code Modal -->
        <div class="modal fade" id="diseaseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">กรอกรหัสโรค</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="diseaseCodes" class="form-label">รหัสโรค (คั่นด้วยเครื่องหมายจุลภาค)</label>
                            <input type="text" class="form-control" id="diseaseCodes" placeholder="เช่น J18, E11, S72">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-primary" id="updateChart">อัปเดตกราฟ</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Initialize charts
            document.addEventListener('DOMContentLoaded', function () {
                // Top Diseases Chart
                const topDiseasesCtx = document.getElementById('topDiseasesChart').getContext('2d');
                const topDiseasesChart = new Chart(topDiseasesCtx, {
                    type: 'bar',
                    data: {
                        labels: ['J18 (ปอดบวม)', 'E11 (เบาหวาน)', 'S72 (กระดูกสะโพกหัก)', 'I10 (ความดันโลหิตสูง)', 'J06 (ไข้หวัด)'],
                        datasets: [{
                            label: 'จำนวนเคส',
                            data: [15, 12, 8, 7, 5],
                            backgroundColor: [
                                'rgba(52, 152, 219, 0.7)',
                                'rgba(46, 204, 113, 0.7)',
                                'rgba(155, 89, 182, 0.7)',
                                'rgba(241, 196, 15, 0.7)',
                                'rgba(231, 76, 60, 0.7)'
                            ],
                            borderColor: [
                                'rgba(52, 152, 219, 1)',
                                'rgba(46, 204, 113, 1)',
                                'rgba(155, 89, 182, 1)',
                                'rgba(241, 196, 15, 1)',
                                'rgba(231, 76, 60, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Disease Graph (initially empty)
                const diseaseCtx = document.getElementById('diseaseGraph').getContext('2d');
                let diseaseChart = new Chart(diseaseCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'จำนวนเคส',
                            data: [],
                            backgroundColor: 'rgba(52, 152, 219, 0.2)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 2,
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Modal handling
                document.getElementById('openDiseaseModal').addEventListener('click', function () {
                    const modal = new bootstrap.Modal(document.getElementById('diseaseModal'));
                    modal.show();
                });

                document.getElementById('updateChart').addEventListener('click', function () {
                    const codes = document.getElementById('diseaseCodes').value;

                    // Simulate data based on input codes
                    const codeArray = codes.split(',').map(code => code.trim()).filter(code => code);

                    if (codeArray.length > 0) {
                        // Update chart with simulated data
                        diseaseChart.data.labels = codeArray.map(code => `${code} (โรคตัวอย่าง)`);
                        diseaseChart.data.datasets[0].data = codeArray.map(() => Math.floor(Math.random() * 20) + 1);
                        diseaseChart.update();

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('diseaseModal'));
                        modal.hide();
                    } else {
                        alert('กรุณากรอกรหัสโรคอย่างน้อย 1 รหัส');
                    }
                });
            });
        </script>
</body>

</html>