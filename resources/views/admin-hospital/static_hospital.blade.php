<!DOCTYPE html>
<html lang="th">
@include('themes.head')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* ... (CSS เดิมทั้งหมด) ... */
    body {
        font-family: 'Sarabun', sans-serif;
        background-color: #f4f4f4;
        padding: 10px;
    }

    .container-wrapper {
        position: relative;
        border: 1px solid #dee2e6;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 5px;
        margin-top: 40px;
        margin-bottom: 30px;
        width: 95%;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }

    .header {
        text-align: center;
        font-size: 18px;
        margin-bottom: 15px;
    }

    .table-responsive {
        max-height: 350px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .table th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 2;
        border: 1px solid #dee2e6;
        padding: 8px;
    }

    .table td {
        border: 1px solid #dee2e6;
        padding: 8px;
    }

    .footer-left {
        text-align: left;
        margin-top: 15px;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .header-controls {
        position: absolute;
        top: -35px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        z-index: 3;
    }

    .print-btn {
        /* Styles are now handled by Bootstrap btn-sm */
    }

    .stat-text {
        font-weight: bold;
        font-size: 16px;
        padding-top: 5px;
    }

    /* ส่วนของกราฟและตารางสถิติ */
    .stats-section {
        margin-top: 30px;
        background-color: white;
        border-radius: 5px;
        padding: 20px;
        border: 1px solid #dee2e6;
    }

    .stats-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .chart-container {
        width: 100%;
        height: 400px;
        margin-bottom: 30px;
    }

    .disease-table {
        width: 100%;
        margin-top: 20px;
    }

    .treatment-chart-container {
        width: 100%;
        height: 450px;
        margin: 30px 0;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        body {
            padding: 0;
            background-color: white;
        }

        .container-wrapper {
            border: none;
            padding: 0;
            margin-top: 0;
            width: 100%;
            page-break-after: avoid;
        }

        .table-responsive {
            max-height: none;
            overflow: visible;
            border: none;
            page-break-inside: avoid;
        }

        .table {
            width: 100%;
        }

        .table thead th {
            position: static;
        }

        .table td,
        .table th {
            border: 1px solid #ddd;
        }

        .stats-section {
            page-break-inside: avoid;
        }

        .chart-container,
        .treatment-chart-container {
            height: auto;
            page-break-inside: avoid;
        }
    }

    /* เพิ่มสไตล์สำหรับตัวกรอง */
    .filter-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border: 1px solid #dee2e6;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-btn-group {
        display: flex;
        gap: 10px;
    }

    @media print {
        .filter-container {
            display: none !important;
        }
    }
</style>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin-hospital.navbarhospital')
        @include('themes.admin-hospital.menuhospital')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">

                        <!-- เพิ่มส่วนตัวกรอง -->
                        <div class="filter-container no-print">
                            <h4>ตัวกรองข้อมูล</h4>
                            <form id="reportFilterForm" method="GET" action="">
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="startDate">วันที่เริ่มต้น</label>
                                        <input type="date" id="startDate" name="start_date" class="form-control"
                                            value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="endDate">วันที่สิ้นสุด</label>
                                        <input type="date" id="endDate" name="end_date" class="form-control"
                                            value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                    </div>

                                    <div class="filter-group">
                                        <label for="department">แผนก</label>
                                        <select id="department" name="department" class="form-control">
                                            <option value="">ทั้งหมด</option>
                                            <option value="opd" {{ request('department') == 'opd' ? 'selected' : '' }}>OPD
                                            </option>
                                            <option value="ipd" {{ request('department') == 'ipd' ? 'selected' : '' }}>IPD
                                            </option>
                                            <option value="er" {{ request('department') == 'er' ? 'selected' : '' }}>ER
                                            </option>
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <label for="status">สถานะ</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="">ทั้งหมด</option>
                                            <option value="admit" {{ request('status') == 'admit' ? 'selected' : '' }}>
                                                Admit</option>
                                            <option value="refer" {{ request('status') == 'refer' ? 'selected' : '' }}>
                                                Refer</option>
                                            <option value="discharged" {{ request('status') == 'discharged' ? 'selected' : '' }}>Discharged</option>
                                            <option value="followup" {{ request('status') == 'followup' ? 'selected' : '' }}>Followup</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="filter-row mt-3">
                                    <div class="filter-btn-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> กรองข้อมูล
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                                            <i class="fas fa-redo"></i> ล้างตัวกรอง
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="printReport()">
                                            <i class="fas fa-print"></i> พิมพ์รายงาน
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="exportToPDF()">
                                            <i class="fas fa-file-pdf"></i> ส่งออก PDF
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="container">

                            <div class="container-wrapper">


                                <div class="header">
                                    <h2 style="font-size: 20px;">แบบรายงานการรักษาผู้ป่วย</h2>
                                    <p style="font-size: 16px;">ผลการรักษาประวัติ วันที่ 27 เดือน มิถุนายน พ.ศ. 2567
                                    </p>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 5%;">ลำดับ</th>
                                                <th style="width: 15%;">ชื่อ-สกุล</th>
                                                <th style="width: 12%;">หน่วยงาน</th>
                                                <th style="width: 10%;">หมอ</th>
                                                <th style="width: 12%;">อาการ</th>
                                                <th style="width: 18%;">การวินิจฉัยโรค</th>
                                                <th style="width: 10%;">แพทย์</th>
                                                <th style="width: 8%;">สถานการณ์จำแนก</th>
                                                <th style="width: 10%;">F/U</th>
                                                <th style="width: 10%;">ประเภทติดตาม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>พลทหาร ภูริชัย สาระสิทธิ์</td>
                                                <td>พล.พัน 3 ค่ายวาเล่</td>
                                                <td>พ.ท.พลอย</td>
                                                <td>ปวดข้อเข่า ข้อเท้า</td>
                                                <td>Z480 [Attention to surgical dressing and sutures
                                                    การดูแลแผลผ่าตัดและเย็บแผล]
                                                </td>
                                                <td>พ.ท.พลอย</td>
                                                <td>D/C</td>
                                                <td>28.06.67 (13:00)</td>
                                                <td>คัดได้ตามปกติ</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>พลทหาร ธนทัต ศรีพัฒนโชติ</td>
                                                <td>มทบ.31</td>
                                                <td>พ.ท.จิรพงษ์</td>
                                                <td>บวม บริเวณข้อเท้า บริเวณแขน</td>
                                                <td>L029 [Cutaneous abscess, furuncle and carbuncle of skin,
                                                    unspecified]
                                                </td>
                                                <td>พ.ท.จิรพงษ์</td>
                                                <td>D/C</td>
                                                <td>4 ก.ค. 67 (08:00)</td>
                                                <td>คัดได้ตามปกติ</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>พลทหาร กฤษฎา บุญพาณิชย์</td>
                                                <td>มทบ.31</td>
                                                <td>พ.ท.ณัฐพล</td>
                                                <td>มีไข้ เจ็บคอ</td>
                                                <td>J029 [ลำไส้อักเสบจาก Acute pharyngitis]</td>
                                                <td>พ.ท.ณัฐพล</td>
                                                <td>D/C</td>
                                                <td>5 พ.ย. 67 (08:00)</td>
                                                <td>คัดได้ตามปกติ</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="footer-left">
                                    <p>หมายเหตุ: บันทึกการดูแลนี้มีความสำคัญ
                                        และต้องเก็บรักษาไว้เพื่อการอ้างอิงในภายหลัง</p>
                                    <p>สรุปการรายงานการรักษาผู้ป่วย:</p>
                                    <p>สรุปผลการตรวจรักษาผู้ป่วย หน่วยที่ __ แผนก __ หมายเลขผู้ป่วย ___ ชื่อ-สกุล
                                        ___
                                        การประเมิน
                                        Admit __
                                        ลงทะเบียนที่ __ จำนวนนัด F/U __ หมายเหตุ: __ ป่วย __ ระดับความรุนแรง __
                                        สถานะการติดตาม
                                        __</p>
                                </div>
                            </div>
                        </div>

                        <!-- ส่วนแสดงสถิติโรค OPD -->
                        <div class="container-wrapper stats-section">
                            <div class="stats-header">
                                <h3>รายงานโรคที่พบมากที่สุด 10 อันดับของ OPD</h3>
                                <p>ช่วงวันที่ 1 พ.ย. 66 - 31 ม.ค. 67</p>
                            </div>

                            <!-- กราฟแสดงสถิติโรค OPD -->
                            <div class="chart-container">
                                <canvas id="topDiseasesChart"></canvas>
                            </div>

                            <!-- ตารางแสดงสถิติโรค OPD -->
                            <div class="table-responsive">
                                <table class="table table-bordered disease-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>รหัสโรค</th>
                                            <th>ชื่อโรค</th>
                                            <th>จำนวนผู้ป่วย</th>
                                        </tr>
                                    </thead>
                                    <tbody id="diseaseTableBody">
                                        <!-- ข้อมูลจะถูกเพิ่มโดย JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ส่วนแสดงสถิติการรักษาตามหน่วยฝึก -->
                        <div class="container-wrapper stats-section">
                            <div class="stats-header">
                                <h3>รายงานผลการรักษาตามหน่วยฝึก</h3>
                                <p>ข้อมูล ณ วันที่ 27 มิถุนายน 2567</p>
                            </div>

                            <!-- กราฟแสดงสถิติการรักษา -->
                            <div class="treatment-chart-container">
                                <canvas id="treatmentChart"></canvas>
                            </div>

                            <!-- ตารางแสดงสถิติการรักษา -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>หน่วยฝึก</th>
                                            <th>Admit</th>
                                            <th>Refer</th>
                                            <th>Discharged</th>
                                            <th>Followup</th>
                                        </tr>
                                    </thead>
                                    <tbody id="statisticsTable">
                                        <!-- ข้อมูลจะถูกเพิ่มโดย JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        // ฟังก์ชันสำหรับล้างตัวกรอง
        function resetFilter() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('department').value = '';
            document.getElementById('status').value = '';
            document.getElementById('reportFilterForm').submit();
        }

        // ฟังก์ชันสำหรับพิมพ์รายงาน
        function printReport() {
            window.print();
        }

        // ฟังก์ชันสำหรับส่งออกเป็น PDF
        function exportToPDF() {
            // ใช้ html2canvas และ jsPDF เพื่อสร้าง PDF
            const { jsPDF } = window.jspdf;

            // เลือก element ที่ต้องการแปลงเป็น PDF
            const element = document.querySelector('.container-wrapper');

            // ใช้ html2canvas เพื่อ capture หน้าเว็บ
            html2canvas(element, {
                scale: 2,
                logging: true,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgWidth = 210; // A4 width in mm
                const pageHeight = 295; // A4 height in mm
                const imgHeight = canvas.height * imgWidth / canvas.width;
                let heightLeft = imgHeight;
                let position = 0;

                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                // ถ้าเนื้อหายาวเกิน 1 หน้า ให้เพิ่มหน้าใหม่
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // บันทึกไฟล์ PDF
                pdf.save('รายงานการรักษาผู้ป่วย_' + new Date().toLocaleDateString('th-TH') + '.pdf');
            });
        }

        // ... (โค้ด JavaScript เดิมทั้งหมด) ...

        // ตัวอย่างการเชื่อมต่อกับ Backend (AJAX)
        function fetchReportData() {
            const formData = new FormData(document.getElementById('reportFilterForm'));

            fetch('/api/medical-reports', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    // อัปเดตข้อมูลในตารางและกราฟตามข้อมูลที่ได้จาก Backend
                    updateReportData(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                });
        }

        function updateReportData(data) {
            // อัปเดตตารางและกราฟตามข้อมูลที่ได้รับ
            // ตัวอย่าง:
            // document.getElementById('patientTableBody').innerHTML = ...;
            // updateCharts(data);
        }

        // โหลดข้อมูลครั้งแรกเมื่อหน้าเว็บโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function () {
            fetchReportData();
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ข้อมูลสถิติโรค OPD (ตัวอย่าง)
        const opdDiseasesData = {
            "J00": { name: "ไข้หวัดธรรมดา", count: 125 },
            "J029": { name: "ลำไส้อักเสบจาก Acute pharyngitis", count: 98 },
            "L029": { name: "ฝี, ผิวหนังอักเสบ", count: 76 },
            "Z480": { name: "การดูแลแผลผ่าตัดและเย็บแผล", count: 65 },
            "R51": { name: "ปวดหัว", count: 54 },
            "R10": { name: "ปวดท้อง", count: 48 },
            "S834": { name: "ข้อเท้าแพลง", count: 42 },
            "M545": { name: "ปวดหลังส่วนล่าง", count: 38 },
            "H100": { name: "เยื่อบุตาอักเสบ", count: 35 },
            "B349": { name: "การติดเชื้อไวรัสไม่ระบุรายละเอียด", count: 30 }
        };

        // ข้อมูลสถิติการรักษาตามหน่วยฝึก (ตัวอย่าง)
        const treatmentStatisticsData = [
            { training_unit: "มทบ.11", Admit: 12, Refer: 5, Discharged: 85, Followup: 23 },
            { training_unit: "มทบ.12", Admit: 8, Refer: 3, Discharged: 92, Followup: 18 },
            { training_unit: "มทบ.13", Admit: 15, Refer: 7, Discharged: 78, Followup: 30 },
            { training_unit: "มทบ.14", Admit: 10, Refer: 4, Discharged: 88, Followup: 25 },
            { training_unit: "มทบ.15", Admit: 5, Refer: 2, Discharged: 95, Followup: 15 }
        ];

        // เรียงลำดับโรคจากมากไปน้อย
        const sortedDiseases = Object.keys(opdDiseasesData)
            .map(code => ({
                code,
                name: opdDiseasesData[code].name,
                count: opdDiseasesData[code].count
            }))
            .sort((a, b) => b.count - a.count);

        // แสดงข้อมูลโรคในตาราง OPD
        const diseaseTableBody = document.getElementById('diseaseTableBody');
        sortedDiseases.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.code}</td>
                <td>${item.name}</td>
                <td>${item.count}</td>
            `;
            diseaseTableBody.appendChild(row);
        });

        // สร้างกราฟโรค OPD
        const opdCtx = document.getElementById('topDiseasesChart').getContext('2d');
        new Chart(opdCtx, {
            type: 'bar',
            data: {
                labels: sortedDiseases.map(item => `${item.code} - ${item.name}`),
                datasets: [{
                    label: 'จำนวนผู้ป่วย',
                    data: sortedDiseases.map(item => item.count),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `จำนวนผู้ป่วย: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 20,
                            callback: function (value) {
                                if (value % 20 === 0) {
                                    return value;
                                }
                            }
                        },
                        grid: {
                            display: true
                        }
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // เติมข้อมูลสถิติการรักษาในตาราง
        const statsTableBody = document.getElementById('statisticsTable');
        treatmentStatisticsData.forEach(stat => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${stat.training_unit}</td>
                <td>${stat.Admit}</td>
                <td>${stat.Refer}</td>
                <td>${stat.Discharged}</td>
                <td>${stat.Followup}</td>
            `;
            statsTableBody.appendChild(row);
        });

        // สร้างกราฟสถิติการรักษา
        const treatmentCtx = document.getElementById('treatmentChart').getContext('2d');
        new Chart(treatmentCtx, {
            type: 'bar',
            data: {
                labels: treatmentStatisticsData.map(item => item.training_unit),
                datasets: [
                    {
                        label: 'Admit',
                        data: treatmentStatisticsData.map(item => item.Admit),
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Refer',
                        data: treatmentStatisticsData.map(item => item.Refer),
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Discharged',
                        data: treatmentStatisticsData.map(item => item.Discharged),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Followup',
                        data: treatmentStatisticsData.map(item => item.Followup),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 20
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>