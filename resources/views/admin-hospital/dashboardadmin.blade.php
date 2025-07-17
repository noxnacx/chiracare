<!DOCTYPE html>
<html lang="th">
@include('themes.head')
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
</style>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin-hospital.navbarhospital')
        <!-- Main Sidebar Container -->
        @include('themes.admin-hospital.menuhospital')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">

                        <div class="d-flex justify-content-between align-items-center mt-3 mb-3 flex-wrap gap-2">
                            <h2 class="fw-bold mb-0" style="color: #2c3e50;">
                                แดชบอร์ดแอดมินโรงพยาบาล
                            </h2>


                            <a href="{{ url('hospital/appointments') }}" class="btn btn-success">
                                นัดหมายทหาร
                            </a>

                        </div>

                        <div class="row mt-3">
                            <!-- จำนวนทหารในหน่วย -->

                            <!-- นัดหมายสำเร็จ -->
                            <div class="col-md-3">
                                <a href={{ url('/hospital/statistics?status=all&department=opd&date_filter=today') }}
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>OPD <span style="font-size: 16px; font-weight: normal;">ยอดสะสมรายวัน</span>
                                        </h5>
                                        <h3>
                                            {{ $opdCount }} <span
                                                style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-users" style="color: #10b981;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href={{ url('/hospital/statistics?status=all&department=er&date_filter=today') }}
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>ER <span style="font-size: 16px; font-weight: normal;">ยอดสะสมรายวัน</span>
                                        </h5>
                                        <h3>
                                            {{ $erCount }} <span style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-ambulance" style="color: #dc3545;"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>


                            <div class="col-md-3">
                                <a href={{ url('/hospital/statistics?status=all&department=ipd&date_filter=today') }}
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>IPD <span style="font-size: 16px; font-weight: normal;">ยอดสะสมรายวัน</span>
                                        </h5>
                                        <h3>
                                            {{ $ipdCount }} <span
                                                style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-procedures" style="color: #6f42c1;"></i>
                                            <!-- สีม่วงสุขภาพดี -->
                                        </div>
                                    </div>
                                </a>
                            </div>




                            <div class="col-md-3">
                                <a href="{{ route('appointments.scheduledByUnit') }}"
                                    class="text-decoration-none text-dark">
                                    <div class="card shadow-sm custom-card">
                                        <h5>นัดหมายจิตเวชวันนี้</h5>
                                        <h3>
                                            3 <span style="font-size: 16px; font-weight: normal;">คน</span>
                                        </h3>
                                        <div class="custom-card-icon">
                                            <i class="fas fa-brain" style="color:rgb(229, 160, 12);"></i>
                                            <!-- สีม่วงเข้ม สื่อถึงจิตใจ -->
                                        </div>
                                    </div>
                                </a>
                            </div>

                        </div>

                        <!-- Today's Appointments Section -->
                        <div class="row mt-3">
                            <!-- Today's Appointments -->
                            <div class="col-md-6 d-flex align-items-stretch">
                                <div class="card shadow-sm w-100">
                                    <div class="card-body">
                                        <div class="card p-3 shadow-sm"
                                            style="background-color: #f8f9fa; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold">
                                                    นัดหมายปกติวันนี้
                                                    <span
                                                        class="text-primary fw-bold">({{ $appointments->where('case_type', 'normal')->count() }}
                                                        คน)</span>
                                                </h5>
                                                <a href="{{ url('hospital/appointments') }}?status=scheduled&case_type=normal&date={{ \Carbon\Carbon::now()->format('Y-m-d') }}&rotation_id=&training_unit_id="
                                                    class="btn btn-info btn-sm">
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
                                                                    <span class="badge custom-badge bg-white shadow">🟢
                                                                        เข้ารับการรักษาแล้ว</span>
                                                                @else
                                                                    <span class="badge custom-badge bg-white shadow">🟠
                                                                        ยังไม่ได้เข้ารับการรักษา</span>
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
                            <div class="col-md-6 d-flex align-items-stretch">
                                <div class="card shadow-sm w-100">
                                    <div class="card-body">
                                        <div class="card p-3 shadow-sm"
                                            style="background-color: #f8d7da; border-radius: 8px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="fw-bold mb-0">
                                                    เคสวิกฤติ
                                                    <span class="text-danger ms-2">{{ $criticalAppointments->count() }}
                                                        เคส</span>
                                                </h5>
                                                <!-- ปุ่มดูทั้งหมด -->
                                                <a href="{{ url('hospital/appointments') }}?status=scheduled&case_type=critical&date={{ \Carbon\Carbon::now()->format('Y-m-d') }}&rotation_id=&training_unit_id="
                                                    class="btn btn-danger btn-sm">
                                                    ดูทั้งหมด
                                                </a>

                                            </div>
                                        </div>
                                        @foreach ($criticalAppointments as $appointment)
                                            <div class="p-2 critical">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <p><strong>
                                                                @if($appointment->medicalReport && $appointment->medicalReport->soldier)
                                                                    {{ $appointment->medicalReport->soldier->first_name }}
                                                                    {{ $appointment->medicalReport->soldier->last_name }}
                                                                @else
                                                                    <span class="text-danger">ข้อมูลทหารไม่พบ</span>
                                                                @endif
                                                            </strong></p>
                                                        <p><span>
                                                                สถานที่:
                                                                {{ $appointment->appointment_location }}
                                                            </span></p>
                                                    </div>

                                                    <div>
                                                        <p class="fw-bold">
                                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') }}
                                                            น.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Graph 1: จำนวนผู้ป่วยที่ต้องเฝ้าระวัง -->
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="fw-bold">สถิติโรคที่ต้องเฝ้าระวัง</h5>
                                        <div
                                            style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                            <canvas id="topDiseasesChart"
                                                style="max-height: 100%; width: 100%;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Graph 2: กราฟโรคที่ต้องการ -->
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="fw-bold d-flex justify-content-between align-items-center">
                                            กราฟโรคที่ต้องการ
                                            <!-- Button to open the popup for disease code -->
                                            <button class="btn btn-info" id="openPopup">กรุณากรอกรหัสโรค</button>
                                        </h5>

                                        <!-- Message to show when no disease code is entered -->
                                        <div id="noDiseaseMessage" class="text-center text-muted"
                                            style="display: flex; justify-content: center; align-items: center; height: 100%;">
                                            <p>ยังไม่ระบุรหัสโรค</p>
                                        </div>

                                        <div
                                            style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                            <!-- The chart canvas, initially hidden -->
                                            <canvas id="diseaseGraph" style="max-height: 100%;; width: 100%;"></canvas>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>





                </div>


            </div>
        </div>
    </div>

    <!-- วันที่และรหัสโรค Modal -->
    <div class="modal fade" id="diseaseModal" tabindex="-1" aria-labelledby="diseaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="diseaseModalLabel">กรุณากรอกรหัสโรคและเลือกช่วงวันที่</h5>
                </div>

                <div class="modal-body">
                    <!-- รหัสโรค -->
                    <!-- Input สำหรับกรอกรหัสโรค -->
                    <label for="diseaseCodes" class="form-label fw-bold">กรอกรหัสโรค</label>
                    <input type="text" class="form-control mb-2" id="diseaseCodes"
                        placeholder="พิมพ์รหัสแล้ว Enter หรือ Space">
                    <!-- Tag preview -->
                    <div id="diseaseTagPreview" class="d-flex flex-wrap gap-2"></div>


                    <!-- ตัวเลือกช่วงวันที่ -->
                    <div class="mb-3">
                        <label for="dateOption" class="form-label fw-semibold text-dark">เลือกวันที่:</label>
                        <select id="dateOption" class="form-select custom-select">
                            <option value="today">วันนี้</option>
                            <option value="range" selected>ระหว่างวันที่</option>
                            <option value="all">ทั้งหมด</option>
                        </select>
                    </div>


                    <!-- กล่องช่วงวันที่ -->
                    <div id="dateRangeInputs" class="row g-2 mb-3">
                        <div class="col">
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-auto d-flex align-items-center">
                            <span>ถึง</span>
                        </div>
                        <div class="col">
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" id="clearTagsBtn">ล้างรหัสทั้งหมด</button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" id="fetchData">แสดงข้อมูล</button>
                </div>

            </div>
        </div>
    </div>



    <!-- Include Bootstrap JS -->


    @include('themes.scriptnotable')
    <!-- Additional CSS Styling -->
    <style>
        .critical {
            background-color: rgb(255, 255, 255);
            border-left: 5px solid #dc3545;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Adds a soft shadow */
        }


        .critical p {
            margin: 0;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            margin-bottom: 10px;
        }

        .appointment-list {
            padding: 10px;
        }

        .appointment-list .fw-bold {
            font-size: 16px;
            color: #333;
        }

        .appointment-list .text-muted {
            color: rgb(28, 74, 114);
        }

        .appointment-list .text-primary {
            color: #007bff;
        }

        .appointment-list .text-warning {
            color: #ffc107;
        }

        .appointment-list .border-bottom {
            border-bottom: 1px solid #ddd;
        }

        #dateRangeInputs span {
            padding: 0 10px;
            font-weight: bold;
            color: #333;
        }

        .custom-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            box-shadow: none;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .custom-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            font-weight: 600;
            font-size: 15px;
        }


        #diseaseTagPreview .tag {
            background-color: #e0f2f1;
            color: #00695c;
            border-radius: 20px;
            padding: 5px 10px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        #diseaseTagPreview .tag .remove-tag {
            margin-left: 8px;
            cursor: pointer;
            color: #dc3545;
            font-weight: bold;
        }
    </style>




</body>

</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    async function fetchTopDiseases() {
        // ส่งคำขอไปที่ API
        const response = await fetch('/all-top-diseases');

        // ตรวจสอบว่าการตอบกลับสำเร็จหรือไม่
        if (!response.ok) {
            console.error('API error:', response.statusText);
            return;
        }

        // ดึงข้อมูลจาก API
        const data = await response.json();

        // ตรวจสอบว่าได้รับข้อมูลแล้วหรือไม่
        const labels = Object.keys(data); // ใช้รหัสโรคเป็น label
        const values = Object.values(data).map(item => item.count); // จำนวนการพบโรค

        console.log(labels, values);  // ตรวจสอบว่า labels และ values ถูกต้อง

        // กำหนด Context สำหรับการแสดงผลใน Canvas
        const ctx = document.getElementById('topDiseasesChart').getContext('2d');

        // สร้างกราฟ
        new Chart(ctx, {
            type: 'bar',  // กราฟแบบแท่ง
            data: {
                labels: labels,  // รหัสโรค
                datasets: [{
                    label: 'Top 5 Diseases',  // ชื่อ Dataset
                    data: values,  // จำนวนการพบโรค
                    backgroundColor: [
                        'rgba(54, 162, 235, 1)',  // สีฟ้า (ทึบ)
                        'rgba(255, 99, 132, 1)',  // สีแดง (ทึบ)
                        'rgba(75, 192, 192, 1)',  // สีเขียว (ทึบ)
                        'rgba(153, 102, 255, 1)', // สีม่วง (ทึบ)
                        'rgba(255, 159, 64, 1)'   // สีส้ม (ทึบ)
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',  // สีฟ้า (ทึบ)
                        'rgba(255, 99, 132, 1)',  // สีแดง (ทึบ)
                        'rgba(75, 192, 192, 1)',  // สีเขียว (ทึบ)
                        'rgba(153, 102, 255, 1)', // สีม่วง (ทึบ)
                        'rgba(255, 159, 64, 1)'   // สีส้ม (ทึบ)
                    ],
                    borderWidth: 1  // ขนาดของเส้นขอบ
                }]
            },
            options: {
                responsive: true,  // ทำให้กราฟตอบสนองต่อขนาดหน้าจอ
                scales: {
                    y: {
                        beginAtZero: true,  // เริ่มต้นจากศูนย์
                        ticks: {
                            stepSize: 1  // กำหนดช่วงของค่าในแกน Y
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            // แสดงชื่อโรคเมื่อ hover
                            label: function (tooltipItem) {
                                const diseaseCode = tooltipItem.label;  // รหัสโรคที่ hover
                                const diseaseInfo = data[diseaseCode];  // ดึงข้อมูลจาก data ตามรหัสโรค

                                if (diseaseInfo) {
                                    const diseaseName = diseaseInfo.name;  // ชื่อโรค
                                    const count = diseaseInfo.count;  // จำนวน
                                    return `${diseaseName} (${diseaseCode}): ${count}`;  // แสดงผลใน tooltip
                                }
                                return "Unknown disease";  // กรณีไม่มีข้อมูล
                            }
                        }
                    }
                }
            }
        });
    }

    // เรียกฟังก์ชัน fetchTopDiseases เมื่อโหลดหน้า
    window.onload = function () {
        fetchTopDiseases();
    };

    let diseaseChart = null;
    let diseaseCodes = [];

    const tagInput = document.getElementById('diseaseCodes');
    const tagPreview = document.getElementById('diseaseTagPreview');

    // เพิ่ม tag เมื่อกด Enter / Space / Comma
    tagInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ' || event.key === ',') {
            event.preventDefault();
            const value = tagInput.value.trim().toUpperCase();
            if (value && !diseaseCodes.includes(value)) {
                diseaseCodes.push(value);
                renderTags();
            }
            tagInput.value = '';
        }
    });

    // สร้าง tag HTML
    function renderTags() {
        tagPreview.innerHTML = '';
        diseaseCodes.forEach((code, index) => {
            const tag = document.createElement('span');
            tag.className = 'tag';
            tag.innerHTML = `${code}<span class="remove-tag" data-index="${index}">&times;</span>`;
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

    // จัดการเลือกช่วงวันที่
    document.getElementById('dateOption').addEventListener('change', function () {
        const show = this.value === 'range';
        document.getElementById('dateRangeInputs').style.display = show ? 'flex' : 'none';
    });

    // เปิด Modal
    document.getElementById('openPopup').addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('diseaseModal'));
        modal.show();
    });

    // เมื่อกดปุ่มแสดงข้อมูล
    document.getElementById('fetchData').addEventListener('click', async function () {
        await fetchSelectedDiseases();
        const modal = bootstrap.Modal.getInstance(document.getElementById('diseaseModal'));
        modal.hide();
    });

    document.getElementById('clearTagsBtn').addEventListener('click', function () {
        diseaseCodes = [];
        renderTags();
        tagInput.value = '';
    });

    window.onload = function () {
        fetchTopDiseases();
    };

    // ฟังก์ชันดึงข้อมูลรหัสโรคที่เลือก
    async function fetchSelectedDiseases() {
        const codes = getDiseaseCodeString();
        const dateOption = document.getElementById('dateOption').value;

        let startDate = '';
        let endDate = '';

        if (!codes) {
            document.getElementById('noDiseaseMessage').style.display = 'block';
            document.getElementById('diseaseGraph').style.display = 'none';
            return;
        }

        if (dateOption === 'today') {
            const today = new Date().toISOString().split('T')[0];
            startDate = endDate = today;
        } else if (dateOption === 'range') {
            startDate = document.getElementById('startDate').value;
            endDate = document.getElementById('endDate').value;

            if (!startDate || !endDate) {
                alert('กรุณาระบุวันที่เริ่มต้นและสิ้นสุด');
                return;
            }
        }

        document.getElementById('noDiseaseMessage').style.display = 'none';
        document.getElementById('diseaseGraph').style.display = 'block';

        const queryParams = new URLSearchParams({
            codes: codes,
            start: startDate,
            end: endDate
        });

        const response = await fetch(`/get-diseases-data?${queryParams.toString()}`);
        if (!response.ok) {
            alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
            return;
        }

        const data = await response.json();

        // สร้าง labels, values, colors สำหรับกราฟ
        const allLabels = diseaseCodes.map(code => {
            const found = data.find(d => d.disease_code === code);
            return found ? found.disease_code : `${code}`;
        });

        const allValues = diseaseCodes.map(code => {
            const found = data.find(d => d.disease_code === code);
            return found ? found.count : 0; // ใช้จำนวนเต็ม
        });

        // กำหนดสีแบบสุ่มหรือตามลำดับ
        const colors = ['rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)', 'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)', 'rgba(255, 159, 64, 0.8)'];
        const allColors = diseaseCodes.map((code, index) => colors[index % colors.length]); // ใช้สีจาก array

        const allBorders = allColors.map(color => color.replace('0.8', '1'));

        if (diseaseChart) {
            diseaseChart.destroy();
        }

        const ctx = document.getElementById('diseaseGraph').getContext('2d');
        diseaseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: allLabels,
                datasets: [{
                    label: 'Top Diseases',
                    data: allValues,
                    backgroundColor: allColors,
                    borderColor: allBorders,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1, // กำหนดให้เป็นจำนวนเต็ม
                            callback: function (value) {
                                return Math.floor(value);  // ปัดเศษให้เป็นจำนวนเต็ม
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                const label = tooltipItem.label;
                                const value = tooltipItem.raw;
                                // แสดงชื่อโรค, รหัสโรค, และจำนวน
                                const disease = data.find(d => d.disease_code === label);
                                const diseaseName = disease ? disease.name : 'ไม่มีข้อมูล';
                                return `${diseaseName} (${label}): ${value}`;
                            }
                        }
                    }
                }
            }
        });
    }


</script>