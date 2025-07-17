<!DOCTYPE html>
<html lang="en">
@include('themes.head')
<!-- Inline CSS -->
<style>
    .custom-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        background: #fff;
        position: relative;
        text-align: left;
    }

    .custom-card h5 {
        margin-bottom: 5px;
    }

    .custom-card h3 {
        font-weight: bold;
    }

    .custom-card-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        background: transparent;
        padding: 5px;
        border-radius: 50%;
    }

    .custom-card-icon i {
        font-size: 20px;
    }



    .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .flex-grow-1 {
        flex-grow: 1;
    }


    .critical {
        background-color: rgb(255, 255, 255);
        border-left: 5px solid #dc3545;
        margin-bottom: 3px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Adds a soft shadow */
    }

    .form-label {
        font-weight: bold;
    }

    .card-body {
        padding: 20px;
    }

    .form-control {
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }

    .dropdown-menu {
        padding: 15px;
    }

    .form-label {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .date-preset {
        font-size: 12px;
        padding: 5px 10px;
    }

    #applyDateRange {
        padding: 5px 15px;
        font-size: 14px;
    }

    #selectedDateRange {
        font-weight: 500;
    }
</style>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- Main Sidebar Container -->
        @include('themes.training.menutraining')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                            <h2 class="fw-bold mb-0">
                                แดชบอร์ด หน่วยฝึก : <span class="text-primary">{{ $unit->unit_name }}</span>
                            </h2>

                            <a href="{{ route('medicalReport.create', ['id' => $unit->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> เพิ่มการส่งป่วย
                            </a>

                        </div>


                        <div class="row mt-3">
                            <!-- จำนวนทหารในหน่วย -->

                            <!-- นัดหมายสำเร็จ -->
                            <div class="col-md-3">
                                <a href="#" class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>จำนวนทหารในหน่วย</h5>
                                        <h3>
                                            {{ $totalSoldiers }} <span
                                                style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-sync" style="color: #10b981;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('wait_appointment', ['status' => 'sent']) }}"
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>ยังไม่ได้ทำการนัดหมาย</h5>
                                        <h3>
                                            @if(isset($sentCount))
                                                {{ $sentCount }} <span
                                                    style="font-size: 16px; font-weight: normal;">คน</span>
                                            @else
                                                <span style="color: red;">Error: ค่าไม่ถูกต้อง</span>
                                            @endif
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-sync" style="color: #10b981;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            @if(isset($errorMessage))
                                <div class="alert alert-danger">
                                    <strong>เกิดข้อผิดพลาด:</strong> {{ $errorMessage }}
                                </div>
                            @endif

                            <div class="col-md-3">
                                <a href="{{ route('wait_appointment', ['status' => 'pending']) }}"
                                    class="text-decoration-none text-dark">

                                    <div class="card shadow-sm custom-card">
                                        <h5>ยังไม่ได้ส่งป่วย
                                        </h5>
                                        <h3>
                                            {{ $pendingCount ?? 0  }} <span
                                                style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-sync" style="color: #10b981;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <!-- ปุ่ม รอนัดหมายจาก รพ. (status = sent) -->


                            <!-- ปุ่ม ยังไม่ได้นัดหมาย (status = pending) -->

                            <!-- ปุ่ม นัดหมายสำเร็จแล้ว (status = scheduled) -->



                            <div class="col-md-3">
                                <a href="{{ route('wait_appointment', ['status' => 'scheduled']) }}"
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>นัดหมายสำเร็จ
                                        </h5>
                                        <h3>
                                            {{ $approvedCount ?? 0 }} <span
                                                style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-sync" style="color: #10b981;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>

                        </div>
                        <!-- นัดหมายของวันนี้: เต็มความกว้าง -->
                        <div class="row mt-3">
                            <!-- การ์ดนัดหมายวันนี้ -->
                            <div class="col-md-6 d-flex">
                                <div class="card shadow-sm w-100 d-flex flex-column">
                                    <div class="card-body d-flex flex-column flex-grow-1">
                                        <div class="card p-3 shadow-sm"
                                            style="background-color: #f8f9fa; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold">
                                                    นัดหมายปกติวันนี้
                                                    <span
                                                        class="text-primary fw-bold">({{ $appointments->where('case_type', 'normal')->count() }}
                                                        คน)</span>
                                                </h5>
                                                <a href="{{ route('wait_appointment', [
    'status' => 'scheduled',
    'case_type' => 'normal',
    'date' => \Carbon\Carbon::now()->format('d/m/Y')
]) }}" class="btn btn-info btn-sm">
                                                    ดูทั้งหมด
                                                </a>
                                            </div>
                                        </div>

                                        <table id="appointmentTable" class="table table-striped table-bordered mt-3">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ชื่อ - นามสกุล</th>
                                                    <th>นัดหมาย</th>
                                                    <th>สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $normalAppointments = $appointments->where('case_type', 'normal')->take(3);
                                                @endphp

                                                @if($normalAppointments->count() > 0)
                                                    @foreach ($normalAppointments as $appointment)
                                                        <tr>
                                                            <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <strong>เวลา :</strong>
                                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                                น.
                                                                <br>
                                                                <strong>สถานที่ :</strong>
                                                                {{ $appointment->appointment_location ?? 'ไม่ระบุ' }}
                                                            </td>
                                                            <td>
                                                                @if (!is_null($appointment->checkin) && $appointment->checkin->checkin_status === 'checked-in')
                                                                    <span class="badge custom-badge bg-white shadow">                                                            🟢เข้ารับการรักษาแล้ว
                                                                    </span>
                                                                @else
                                                                    <span class="badge custom-badge bg-white shadow">🟠ยังไม่ได้เข้ารับการรักษา</span>
                                                                @endif
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center text-danger">❌
                                                            ไม่มีเคสปกติในวันนี้</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <!-- High-Risk Watchlist -->
                            <div class="col-md-6 d-flex">
                                <div class="card shadow-sm w-100 d-flex flex-column">
                                    <div class="card-body d-flex flex-column flex-grow-1">
                                        <div class="card p-3 shadow-sm"
                                            style="background-color: #f8d7da; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold text-danger mb-0">
                                                    เคสวิกฤติ
                                                    <span
                                                        class="text-primary fw-bold">({{ $appointments->where('case_type', 'critical')->count() }}
                                                        คน)</span>
                                                </h5>
                                                <!-- ปุ่มดูทั้งหมด -->
                                                <a href="{{ route('wait_appointment', [
    'status' => 'scheduled',
    'case_type' => 'critical',
    'date' => \Carbon\Carbon::now()->format('d/m/Y')
]) }}" class="btn btn-danger btn-sm">
                                                    ดูทั้งหมด
                                                </a>
                                            </div>
                                        </div>
                                        <div class="mt-3 flex-grow-1">
                                            @php
                                                $criticalCases = $appointments->where('case_type', 'critical')->take(3);
                                            @endphp

                                            @if($criticalCases->count() > 0)
                                                @foreach ($criticalCases as $appointment)
                                                    <div class="p-2 critical border-bottom">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <p class="fw-bold mb-1">
                                                                    @if($appointment->medicalReport && $appointment->medicalReport->soldier)
                                                                        {{ $appointment->medicalReport->soldier->first_name }}
                                                                        {{ $appointment->medicalReport->soldier->last_name }}
                                                                    @else
                                                                        <span class="text-danger">ข้อมูลทหารไม่พบ</span>
                                                                    @endif
                                                                </p>
                                                                <div class="d-flex gap-3">
                                                                    <p class="mb-0">
                                                                        <span class="text-muted">สถานที่:</span>
                                                                        <span>{{ $appointment->appointment_location ?? 'ไม่ระบุ' }}</span>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <span class="text-muted">เวลา:</span>
                                                                        <span
                                                                            class="fw-bold">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                                            น.</span>
                                                                    </p>
                                                                </div>
                                                            </div>


                                                            <div class="text-end">
    <span class="badge rounded-0
        @if($appointment->checkin && $appointment->checkin->checkin_status === 'checked-in')
            bg-white text-dark
        @else
        @endif"
        style="border: 2px solid white; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); padding: 8px 16px; display: inline-block; min-width: 80px; text-align: center; border-radius: 8px;">

        @if($appointment->checkin && $appointment->checkin->checkin_status === 'checked-in')
            🟢เข้ารับการรักษาแล้ว
        @else
            🟠ยังไม่ได้เข้ารับการรักษา
        @endif
    </span>
</div>

                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="p-3 text-center text-danger">
                                                    ❌ ไม่มีเคสวิกฤติในขณะนี้
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- กราฟ 2 อัน แสดงในแถวเดียวกัน -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="fw-bold">Top 5 โรคที่พบทหารในหน่วยฝึกนี้มากที่สุด</h5>
                                        <div style="height: 300px;">
                                            <canvas id="topDiseasesChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <!-- Title for the Card (Optional) -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold mb-3">รายงานผลการรักษาทหารใหม่</h5>
                                            <!-- Dropdown สำหรับเลือกช่วงเวลา -->
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    id="dateRangeDropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <span id="selectedDateRange">วันนี้</span>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dateRangeDropdown"
                                                    style="padding: 15px; width: 300px;">
                                                    <li>
                                                        <div class="mb-2">
                                                            <label class="form-label">จาก</label>
                                                            <input type="date" class="form-control" id="fromDate"
                                                                value="">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="mb-2">
                                                            <label class="form-label">ถึง</label>
                                                            <input type="date" class="form-control" id="toDate"
                                                                value="">
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            <button class="btn btn-outline-primary date-preset w-100"
                                                                data-range="today" style="padding: 10px 20px;">
                                                                วันนี้
                                                            </button>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <div class="d-flex justify-content-end">
                                                            <button class="btn btn-primary btn-sm"
                                                                id="applyDateRange">นำไปใช้</button>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Card for chart display -->
                                        <div class=" p-2 mt-3">
                                            <canvas id="unitChart" height="200"></canvas>
                                        </div>
                                    </div>

                                </div>



                            </div>


                        </div>
                    </div>

                    <!-- ปุ่มย้อนกลับ -->

                </div>
            </div>
        </div>
        </div>



        @include('themes.scriptnotable')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var ctx = document.getElementById('topDiseasesChart').getContext('2d');

                var diseaseLabels = ["ไข้หวัด", "ปวดท้อง", "ไข้เลือดออก", "ท้องเสีย", "ภูมิแพ้"];
                var diseaseCounts = [11, 12, 8, 10, 11];

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: diseaseLabels,
                        datasets: [{
                            label: 'จำนวนผู้ป่วย',
                            data: diseaseCounts,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                            borderColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // ✅ ปรับให้กราฟอยู่ในพื้นที่ที่กำหนด
                        scales: {
                            y: {
                                beginAtZero: true  // ทำให้กราฟเริ่มจาก 0
                            }
                        }

                    }
                });
            });
        </script>
        <script>
            const ctx = document.getElementById('unitChart').getContext('2d');

            const unitChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['หน่วยฝึก A'], // แสดงหน่วยเดียว
                    datasets: [
                        {
                            label: 'จำหน่าย',
                            data: [210],
                            backgroundColor: '#ffa500' // สีส้ม
                        },
                        {
                            label: 'นัดแล้ว',
                            data: [12],
                            backgroundColor: '#ffff66' // เหลือง
                        },
                        {
                            label: 'Admit',
                            data: [8],
                            backgroundColor: '#99ff66' // เขียวอ่อน
                        },
                        {
                            label: 'Refer',
                            data: [3],
                            backgroundColor: '#996633' // น้ำตาล
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'จำนวนคน'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dayFilter = document.getElementById('dayFilter');
                const specificDateSection = document.getElementById('specificDateSection');
                const selectedDateInput = document.getElementById('selectedDate');

                // ตั้งค่าวันที่เริ่มต้นเป็นวันปัจจุบัน
                const today = new Date();
                const formattedToday = today.toISOString().split('T')[0];
                selectedDateInput.value = formattedToday;

                // ตรวจสอบค่าเริ่มต้นเมื่อโหลดหน้า
                if (dayFilter.value === 'specific') {
                    specificDateSection.style.display = 'block';
                }

                dayFilter.addEventListener('change', function () {
                    if (this.value === 'specific') {
                        specificDateSection.style.display = 'block';
                        // อัปเดตกราฟเมื่อเปลี่ยนมาเลือกวันที่เฉพาะ
                        updateChart('specific', selectedDateInput.value);
                    } else {
                        specificDateSection.style.display = 'none';
                        updateChart(this.value);
                    }
                });

                selectedDateInput.addEventListener('change', function () {
                    if (dayFilter.value === 'specific') {
                        updateChart('specific', this.value);
                    }
                });

                function updateChart(dayType, specificDate) {
                    let dayText = '';

                    switch (dayType) {
                        case 'today':
                            dayText = 'วันนี้';
                            break;
                        case 'yesterday':
                            dayText = 'เมื่อวาน';
                            break;
                        case 'specific':
                            dayText = formatDate(specificDate);
                            break;
                    }

                    console.log(`อัปเดตกราฟสำหรับ: ${dayText}`);
                    // เพิ่มโค้ดสำหรับอัปเดตกราฟที่นี่
                }

                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const options = { day: 'numeric', month: 'short', year: 'numeric' };
                    return date.toLocaleDateString('th-TH', options);
                }
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ตั้งค่าวันที่เริ่มต้น
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);

                document.getElementById('fromDate').valueAsDate = today;
                document.getElementById('toDate').valueAsDate = today;

                // ปุ่ม preset วันที่
                document.querySelectorAll('.date-preset').forEach(button => {
                    button.addEventListener('click', function () {
                        const range = this.getAttribute('data-range');
                        let fromDate, toDate;

                        switch (range) {
                            case 'today':
                                fromDate = today;
                                toDate = today;
                                break;
                            case 'yesterday':
                                fromDate = yesterday;
                                toDate = yesterday;
                                break;
                            case 'thisWeek':
                                fromDate = new Date(today);
                                fromDate.setDate(today.getDate() - today.getDay());
                                toDate = new Date(today);
                                toDate.setDate(today.getDate() + (6 - today.getDay()));
                                break;
                            case 'lastWeek':
                                fromDate = new Date(today);
                                fromDate.setDate(today.getDate() - today.getDay() - 7);
                                toDate = new Date(today);
                                toDate.setDate(today.getDate() - today.getDay() - 1);
                                break;
                            case 'thisMonth':
                                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                break;
                            case 'lastMonth':
                                fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                                toDate = new Date(today.getFullYear(), today.getMonth(), 0);
                                break;
                        }

                        document.getElementById('fromDate').valueAsDate = fromDate;
                        document.getElementById('toDate').valueAsDate = toDate;
                    });
                });

                // ปุ่มนำไปใช้
                document.getElementById('applyDateRange').addEventListener('click', function () {
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;

                    if (!fromDate || !toDate) {
                        alert('กรุณาเลือกทั้งวันที่เริ่มต้นและวันที่สิ้นสุด');
                        return;
                    }

                    if (new Date(fromDate) > new Date(toDate)) {
                        alert('วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด');
                        return;
                    }

                    // อัปเดตข้อความบนปุ่ม dropdown
                    const fromText = formatDate(fromDate);
                    const toText = formatDate(toDate);
                    document.getElementById('selectedDateRange').textContent =
                        (fromDate === toDate) ? fromText : `${fromText} - ${toText}`;

                    // ปิด dropdown
                    const dropdown = new bootstrap.Dropdown(document.getElementById('dateRangeDropdown'));
                    dropdown.hide();

                    // อัปเดตกราฟ
                    updateChart(fromDate, toDate);
                });

                function updateChart(fromDate, toDate) {
                    console.log(`อัปเดตกราฟจาก ${formatDate(fromDate)} ถึง ${formatDate(toDate)}`);
                    // เพิ่มโค้ดสำหรับอัปเดตกราฟที่นี่
                }

                function formatDate(dateString) {
                    const date = new Date(dateString);
                    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
                    return date.toLocaleDateString('th-TH', options).replace(/\//g, '.');
                }
            });
        </script>

</body>

</html>