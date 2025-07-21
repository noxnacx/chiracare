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
                        <div class="container-wrapper">
                            <div class="header-controls no-print">
                                <div class="stat-text">
                                    สถิติและรายงาน
                                </div>
                                <div class="print-btn">
                                    <button class="btn btn-primary btn-sm" onclick="window.print()">พิมพ์เอกสาร</button>
                                </div>
                            </div>

                            <div class="header">
                                <div class="d-flex justify-content-end mb-1">

                                    <button class="btn btn-primary mx-1" data-toggle="modal"
                                        data-target="#filterByDateModal" style="height: 32px; border-radius: 8px;">
                                        <i class="fas fa-filter me-1"></i> ตัวกรอง
                                    </button>
                                </div>
                                <h2 style="font-size: 20px;">แบบรายงานการรักษาผู้ป่วย</h2>
                                @php
                                    use Illuminate\Support\Carbon;

                                    // แปลงวันที่เป็น Carbon instance และแสดงเป็นภาษาไทยแบบเต็ม
                                    Carbon::setLocale('th');
                                    $carbonDate = Carbon::parse($date)->addYears(543); // แปลง ค.ศ. เป็น พ.ศ.
                                    $thaiFormattedDate = $carbonDate->translatedFormat('j F พ.ศ. Y'); // เช่น 27 มิถุนายน พ.ศ. 2567
                                @endphp

                                <p style="font-size: 16px;">
                                    ผลการรักษาประวัติ วันที่ {{ $thaiFormattedDate }}
                                </p>


                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 5%;">ลำดับ</th>
                                            <th style="width: 15%;">ชื่อ-สกุล</th>
                                            <th style="width: 12%;">หน่วยฝึก</th>
                                            <th style="width: 12%;">หน่วย</th>
                                            <th style="width: 12%;">อาการ</th>
                                            <th style="width: 18%;">การวินิจฉัยโรค</th>
                                            <th style="width: 10%;">แพทย์</th>
                                            <th style="width: 8%;">สถานการณ์จำแนก</th>
                                            <th style="width: 10%;">ระบุวันที่งดฝึก</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $seenAdmitKeys = []; @endphp
                                        @php $rowNumber = 1; @endphp

                                        @foreach($medicalDiagnoses as $index => $diagnosis)
                                            @php
                                                $isAdmit = $diagnosis->treatment_status === 'Admit';
                                                $key = $diagnosis->treatment_id . '|' . $diagnosis->diagnosis_date;

                                                if ($isAdmit && in_array($key, $seenAdmitKeys)) {
                                                    continue;
                                                }

                                                if ($isAdmit) {
                                                    $seenAdmitKeys[] = $key;
                                                    $departments = collect($unitDistinctTreatmentDisplay[
                                                        optional(optional($diagnosis->treatment->checkin->appointment->medicalReport->soldier)->trainingUnit)->unit_name
                                                    ])->filter(function ($depts) use ($diagnosis) {
                                                        return in_array($diagnosis->department_type, $depts);
                                                    })->flatten()->unique()->implode(', ');
                                                }
                                            @endphp

                                            <tr>
                                                <td>{{ $rowNumber++ }}</td>
                                                <td>{{ optional($diagnosis->treatment->checkin->appointment->medicalReport->soldier)->first_name }}
                                                    {{ optional($diagnosis->treatment->checkin->appointment->medicalReport->soldier)->last_name }}
                                                </td>
                                                <td>{{ optional(optional($diagnosis->treatment->checkin->appointment->medicalReport->soldier)->trainingUnit)->unit_name ?? '-' }}
                                                </td>
                                                <td>{{ optional($diagnosis->treatment->checkin->appointment->medicalReport->soldier)->affiliated_unit }}
                                                </td>
                                                <td>{{ optional($diagnosis->treatment->checkin->appointment->medicalReport)->symptom_description ?? '-' }}
                                                </td>
                                                <td>
                                                    @if($diagnosis->diseases->isEmpty()) - @else
                                                        @foreach($diagnosis->diseases as $disease)
                                                            {{ $disease->icd10_code }} [{{ $disease->disease_name_en }}]<br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>{{ $diagnosis->doctor_name }}</td>
                                                <td>
                                                    @php
                                                        $diagnosisDate = Carbon::parse($diagnosis->diagnosis_date)->format('Y-m-d H:i');
                                                        $followUp = collect($followUpAppointments)->first(function ($appointment) use ($diagnosisDate) {
                                                            return $appointment->is_follow_up == 1 &&
                                                                optional($appointment->created_at)->format('Y-m-d H:i') === $diagnosisDate;
                                                        });

                                                    @endphp
                                                    @if ($diagnosis->treatment_status === 'Follow-up')
                                                        Follow-up<br>
                                                        {{ $followUp ? \Carbon\Carbon::parse($followUp->appointment_date)->format('d/m/Y (H:i)') : '-' }}
                                                    @elseif ($isAdmit)
                                                        Admit<br>
                                                        ({{ $departments }})
                                                    @else
                                                        {{ $diagnosis->treatment_status }}
                                                    @endif

                                                </td>
                                                <td>{{ $diagnosis->training_instruction }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>
                            </div>

                            <div class="footer-left">

                                {{-- สรุปผลการตรวจรักษารายหน่วย --}}
                                <p><strong>สรุปผลการตรวจรักษารายหน่วย</strong></p>
                                @php $total = 0; @endphp
                                @foreach ($unitNames as $unit)
                                    @php
                                        $count = $unitFullPatientCount[$unit] ?? 0;
                                        $total += $count;
                                    @endphp
                                    หน่วยฝึก {{ $unit }} <u>{{ $count }}</u> นาย,
                                @endforeach
                                รวม <u>{{ $total }}</u> นาย

                                <p>สรุปการ Admit
                                    @php $totalAdmit = 0; @endphp
                                    @foreach($admitSummaries as $unit => $count)
                                        หน่วยฝึก {{ $unit }} <u>{{ $count }}</u> นาย,
                                        @php $totalAdmit += $count; @endphp
                                    @endforeach
                                    รวม <u>{{ $statusCounts['admit'] }}</u> นาย</p>

                                <p>สถานะการจำหน่าย
                                    Admit <u>{{ $statusCounts['admit'] ?? 0 }}</u> นาย,
                                    Discharge <u>{{ $statusCounts['discharge'] ?? 0 }}</u> นาย,
                                    Refer <u>{{ $statusCounts['refer'] ?? 0 }}</u> นาย,
                                    นัด F/U <u>{{ $statusCounts['followup'] ?? 0 }}</u> นาย</p>

                                <p><strong>หมายเหตุหน่วยฝึก มี {{ count($unitNames) }} หน่วยฝึก</strong> ได้แก่
                                    {{ implode(' / ', $unitNames) }}
                                </p>


                            </div>



                        </div>








                        <!-- ส่วนแสดงสถิติโรค OPD -->
                        <div class="container-wrapper stats-section">
                            <div class="d-flex justify-content-end mb-3">

                                <button class="btn btn-primary mx-1" data-toggle="modal"
                                    data-target="#filterByRangeAndDeptModal" style="height: 32px; border-radius: 8px;">
                                    <i class="fas fa-filter me-1"></i> ตัวกรอง
                                </button>
                            </div>
                            <div class="stats-header">
                                <h3 id="chartTitle">รายงานโรคที่พบมากที่สุด 10 อันดับของ OPD</h3>

                                <p id="dateRangeDisplay">ช่วงวันที่ -</p>
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
                            <!-- 🔍 ฟอร์มตัวกรอง -->
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-primary mx-1" data-toggle="modal"
                                    data-target="#filterByDateAndTypeModal" style="height: 32px; border-radius: 8px;">
                                    <i class="fas fa-filter me-1"></i> ตัวกรอง
                                </button>



                            </div>

                            <div class="stats-header">
                                <h3>รายงานผลการรักษาตามหน่วยฝึก</h3>
                                <p id="reportDate">ข้อมูล ณ วันที่ ...</p>
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

    <!-- Modal 1: กรองตามวันที่เดียว -->
    <div class="modal fade" id="filterByDateModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="GET" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">แบบรายงานการรักษาผู้ป่วย
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <label for="date" class="form-label fw-semibold">เลือกวันที่:</label>
                    <input type="date" name="date" id="date" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">กรอง</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: ช่วงวันที่ + ปุ่มแผนก -->
    <div class="modal fade" id="filterByRangeAndDeptModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">รายงานโรคที่พบมากที่สุด 10 อันดับ</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="filterFormModal">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date_modal">เริ่มวันที่:</label>
                                <input type="date" class="form-control" id="start_date_modal" required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date_modal">ถึงวันที่:</label>
                                <input type="date" class="form-control" id="end_date_modal" required>
                            </div>
                            <div class="col-md-4">
                                <label for="department_modal">เลือกแผนก:</label>
                                <select id="department_modal" class="form-control">
                                    <option value="opd">OPD</option>
                                    <option value="er">ER</option>
                                    <option value="ipd">IPD</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-primary" onclick="filterFromModal2()">
                                <i class="fas fa-search me-1"></i> ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal 3: วันที่ + dropdown แผนก -->

    <!-- ปุ่มเปิด Modal ตัวกรองที่ 3 -->

    <!-- Modal 3 -->
    <div class="modal fade" id="filterByDateAndTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">รายงานผลการรักษาตามหน่วยฝึก
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filterDate_modal">เลือกวันที่:</label>
                        <input type="date" id="filterDate_modal" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="departmentType_modal">เลือกแผนก:</label>
                        <select id="departmentType_modal" class="form-control">
                            <option value="">ทั้งหมด</option>
                            <option value="opd">OPD</option>
                            <option value="er">ER</option>
                            <option value="ipd">IPD</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="applyModal3Filter()">ค้นหา</button>
                </div>
            </div>
        </div>
    </div>


    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let topDiseasesData = {};
        let chartInstance = null;
        let currentDept = 'opd';

        function getTodayThaiDateISO() {
            const now = new Date();
            // เพิ่ม 7 ชั่วโมงเพื่อให้เป็นเวลาประเทศไทย
            now.setHours(now.getHours() + 7);
            return now.toISOString().split('T')[0];
        }
        // ✅ แปลงวันที่เป็นภาษาไทยแบบย่อ
        function formatDateThai(dateString) {
            const months = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            const date = new Date(dateString);
            const day = date.getDate();
            const month = months[date.getMonth() + 1];
            const year = date.getFullYear() + 543;
            return `${day} ${month} ${year}`;
        }

        // ✅ แปลงวันที่เป็นไทยแบบเต็ม
        function formatThaiDate(isoDate) {
            const months = [
                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];
            const d = new Date(isoDate);
            const day = d.getDate();
            const month = months[d.getMonth()];
            const year = d.getFullYear() + 543;
            return `${day} ${month} ${year}`;
        }

        // ✅ โหลดและแสดงโรคยอดนิยมจาก modal
        function filterFromModal2() {
            const startDate = document.getElementById('start_date_modal').value;
            const endDate = document.getElementById('end_date_modal').value;
            const department = document.getElementById('department_modal').value;

            if (!startDate || !endDate) {
                alert("กรุณาเลือกวันที่ให้ครบ");
                return;
            }

            const startText = formatThaiDate(startDate);
            const endText = formatThaiDate(endDate);
            document.getElementById('dateRangeDisplay').textContent = `ช่วงวันที่ ${startText} - ${endText}`;

            fetch(`/admin/hospital/staticgraph?start_date=${startDate}&end_date=${endDate}`)
                .then(res => res.json())
                .then(data => {
                    topDiseasesData = data.topDiseasesByDepartment;
                    filterByDepartment(department);
                })
                .catch(err => {
                    console.error("เกิดข้อผิดพลาด:", err);
                });

            $('#filterByRangeAndDeptModal').modal('hide');
        }

        // ✅ เปลี่ยนแผนกแสดงผล (OPD, IPD, ER)
        function filterByDepartment(dept) {
            currentDept = dept;
            const data = topDiseasesData[dept] || [];

            document.getElementById('chartTitle').textContent = `รายงานโรคที่พบมากที่สุด 10 อันดับของ ${dept.toUpperCase()}`;

            const diseaseTableBody = document.getElementById('diseaseTableBody');
            diseaseTableBody.innerHTML = '';

            if (data.length === 0) {
                diseaseTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted">ไม่พบข้อมูลโรคในแผนก ${dept.toUpperCase()}</td></tr>`;
            } else {
                data.forEach((item, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.code}</td>
                    <td>${item.name}</td>
                    <td>${item.count}</td>
                `;
                    diseaseTableBody.appendChild(row);
                });
            }

            const ctx = document.getElementById('topDiseasesChart').getContext('2d');
            if (chartInstance) chartInstance.destroy();

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => `${item.code} - ${item.name}`),
                    datasets: [{
                        label: 'จำนวนผู้ป่วย',
                        data: data.map(item => item.count),
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
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `จำนวนผู้ป่วย: ${context.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true },
                        y: {
                            ticks: { autoSkip: false, font: { size: 12 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // ✅ โหลดข้อมูลรักษาตามหน่วยฝึก
        function loadTreatmentStatistics(date, departmentType, callback = () => { }) {
            const reportDate = document.getElementById('reportDate');
            const displayDate = date;
            reportDate.innerText = `ข้อมูล ณ วันที่ ${formatThaiDate(displayDate)}`;

            const params = new URLSearchParams();
            if (date) params.append('date', date);
            if (departmentType) params.append('department_type', departmentType);

            fetch(`/admin/hospital/treatment-statistics?${params.toString()}`)
                .then(res => res.json())
                .then(result => {
                    const data = result.statisticsData;
                    const tableBody = document.getElementById('statisticsTable');
                    tableBody.innerHTML = '';

                    data.forEach(stat => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${stat.training_unit}</td>
                        <td>${stat.Admit}</td>
                        <td>${stat.Refer}</td>
                        <td>${stat.Discharged}</td>
                        <td>${stat["Follow-up"]}</td>
                    `;
                        tableBody.appendChild(row);
                    });

                    if (window.treatmentChartInstance) {
                        window.treatmentChartInstance.destroy();
                    }

                    const ctx = document.getElementById('treatmentChart').getContext('2d');
                    window.treatmentChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.training_unit),
                            datasets: [
                                {
                                    label: 'Admit',
                                    data: data.map(item => item.Admit),
                                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                                    borderColor: 'rgba(0, 123, 255, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Refer',
                                    data: data.map(item => item.Refer),
                                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                                    borderColor: 'rgba(255, 193, 7, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Discharged',
                                    data: data.map(item => item.Discharged),
                                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                    borderColor: 'rgba(40, 167, 69, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Follow-up',
                                    data: data.map(item => item["Follow-up"]),
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
                                y: { beginAtZero: true }
                            },
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });

                    callback();
                })
                .catch(error => {
                    console.error("เกิดข้อผิดพลาดในการโหลดข้อมูล:", error);
                    callback();
                });
        }

        // ✅ เรียกจาก modal ตัวที่ 3 (รักษาตามหน่วยฝึก)
        function applyModal3Filter() {
            const date = document.getElementById('filterDate_modal').value;
            const department = document.getElementById('departmentType_modal').value;

            if (!date) {
                alert("กรุณาเลือกวันที่");
                return;
            }

            loadTreatmentStatistics(date, department, () => {
                $('#filterByDateAndTypeModal').modal('hide');
            });
        }

        // ✅ โหลดข้อมูลทั้งหมดเมื่อเปิดหน้าเว็บ
        window.addEventListener('DOMContentLoaded', () => {
            const today = getTodayThaiDateISO();

            // ตั้งค่าวันให้ modal ทั้ง 2
            document.getElementById('start_date_modal').value = today;
            document.getElementById('end_date_modal').value = today;
            document.getElementById('department_modal').value = 'opd';

            document.getElementById('filterDate_modal').value = today;
            document.getElementById('departmentType_modal').value = '';

            // โหลดกราฟ top 10 โรค และผลรักษา
            filterFromModal2();
            loadTreatmentStatistics(today, '');
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>