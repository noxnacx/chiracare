<!DOCTYPE html>
<html lang="th">

@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin-hospital.navbarhospital')
        @include('themes.admin-hospital.menuhospital')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">

                        <!-- Statistics Row -->
                        <div class="row">
                            <!-- Cumulative Statistics -->
                            <div class="col-md-6">
                                <div class="card">
                                <div class="card-header text-center" style="background-color:rgb(76, 142, 167); color: white;">

                                        <h4>ยอดเจ็บป่วยสะสม</h4>
                                    </div>
                                    <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover table-no-datatable table-fixed">
                                    <thead class="thead">
                                                <tr>
                                                    <th class="text-center">ประเภท</th>
                                                    <th class="text-center">จำนวน</th>
                                                    <th class="text-center">รายละเอียด</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(['opd', 'er', 'ipd'] as $department)
                                                                                                    @php
                                                                                                        $total = $patientsStatistics->where('department_type', $department)->sum('total_patients');
                                                                                                    @endphp
                                                                                                    <tr>
                                                                                                    <td class="text-center" style="vertical-align: middle;">{{ strtoupper($department) }}</td>

                                                                                                    <td class="text-center align-middle">

                                                                                                            <a href="{{ url()->current() }}?department={{ $department }}&status=all&date_filter=all#patient-details">
                                                                                                                {{ $total > 0 ? $total : '0' }}
                                                                                                            </a>
                                                                                                        </td>
                                                                                                        <td>
    <ul class="list-unstyled mb-0">
        @foreach(['Discharge' => 'จำหน่าย', 'Follow-up' => 'นัดติดตาม', 'Admit' => 'Admit', 'Refer' => 'Refer'] as $status => $label)
            @php
                $count = $patientsStatistics->where('department_type', $department)
                                         ->where('treatment_status', $status)
                                         ->sum('total_patients');
                $displayCount = $count > 0 ? number_format($count) : '0';
            @endphp
            <li class="d-flex align-items-center mb-2">
                <span class="text-nowrap me-2 fw-medium" style="width: 100px; font-size: 0.95rem; color: #555;">{{ $label }}:</span>
                <a href="{{ url()->current() }}?department={{ $department }}&status={{ $status }}&date_filter=all#patient-details"
                class="badge bg-light rounded-pill px-3 py-1 text-decoration-none border-0">
                <span class="d-inline-block text-center fw-bold" style="min-width: 35px; font-size: 1.1rem; color: #1a73e8;">
                    <span class="d-inline-block text-center fw-bold" style="min-width: 30px; font-size: 1.05rem; color: #1a73e8;">{{ $displayCount }}</span>
                    <span class="ms-1 fw-medium" style="font-size: 0.9rem; color: #000;">ราย</span>
                </a>
            </li>
        @endforeach
    </ul>
</td>
                                                                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Daily Statistics -->
                            <div class="col-md-6">
                                <div class="card">
                                <div class="card-header text-center" style="background-color:rgb(76, 142, 167); color: white;">
                                <h4>ยอดเจ็บป่วยรายวัน</h4>
</div>

                                    <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover table-fixed">
                                    <thead class="thead">
                                                <tr>
                                                    <th class="text-center">ประเภท</th>
                                                    <th class="text-center">จำนวน</th>
                                                    <th class="text-center">รายละเอียด</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(['opd', 'er', 'ipd'] as $department)
                                                                                                    @php
                                                                                                        $total = $patientsStatisticsDaily->where('department_type', $department)->sum('total_patients');
                                                                                                    @endphp
                                                                                                    <tr>
                                                                                                    <td class="text-center" style="vertical-align: middle;">{{ strtoupper($department) }}</td>

                                                                                                    <td class="text-center align-middle">

                                                                                                            <a href="{{ url()->current() }}?department={{ $department }}&status=all&date_filter=today#patient-details">
                                                                                                                {{ $total > 0 ? $total : '0' }}
                                                                                                            </a>
                                                                                                        </td>
                                                                                                        <td>
    <ul class="list-unstyled mb-0">
        @foreach(['Discharge' => 'จำหน่าย', 'Follow-up' => 'นัดติดตาม', 'Admit' => 'Admit', 'Refer' => 'Refer'] as $status => $label)
            @php
                $count = $patientsStatisticsDaily->where('department_type', $department)
                                              ->where('treatment_status', $status)
                                              ->sum('total_patients');
                $displayCount = $count > 0 ? number_format($count) : '0';
            @endphp
            <li class="d-flex align-items-center mb-2">
                <span class="text-nowrap me-2 fw-medium" style="width: 90px; font-size: 0.95rem;">{{ $label }}:</span>
                <a href="{{ url()->current() }}?department={{ $department }}&status={{ $status }}&date_filter=today#patient-details"
                   class="badge bg-light rounded-pill px-3 py-1 text-decoration-none border-0">
                    <span class="d-inline-block text-center fw-bold" style="min-width: 35px; font-size: 1.1rem; color: #1a73e8;">{{ $displayCount }}</span>
                    <span class="ms-1 fw-medium" style="font-size: 0.95rem;">ราย</span>
                </a>
            </li>
        @endforeach
    </ul>
</td>
                                                                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
  <!-- Date Filter Form with Status Filter -->
  <div class="row">

                        </div>


                        <!-- Patient Details Table -->
                        <div class="row mt-1" id="patient-details">
                            <div class="col-md-12">




                                    <div class="card shadow-lg">
                                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color:rgb(76, 142, 167); color: white;">
    <h4 class="mb-0">ประวัติการรักษา</h4>
    <button id="filterButton" class="btn ml-auto" style="background-color: white; color: black; border: 2px solid black;">
    <i class="fas fa-filter me-2"></i> กรองข้อมูล
</button>


</div>
                                    <div class="card-body">
                                    <div class="table-responsive" style="background-color: #f7f9fc; padding: 15px; border-radius: 10px;">
    <table class="table table-striped table-bordered table-hover data-table">
        <thead class="thead">
                                                    <tr>
                                                        <th class="text-center">วันที่วินิจฉัย</th>
                                                        <th class="text-center">ชื่อ-สกุล</th>
                                                        <th class="text-center">เลขประจำตัว</th>
                                                        <th class="text-center">หน่วยฝึก</th>
                                                        <th class="text-center">หน่วยต้นสังกัด</th>
                                                        <th class="text-center">ผลัด</th>
                                                        <th class="text-center">การวินิจฉัย</th>
                                                        <th class="text-center">สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($patientDetails as $patient)
                                                                                                            <tr>
                                                                                                                <td class="text-center">{{ date('d/m/Y', strtotime($patient->diagnosis_date)) }}</td>
                                                                                                                <td class="text-center">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                                                                                                <td class="text-center">{{ $patient->soldier_id_card }}</td>
                                                                                                                <td class="text-center">{{ $patient->training_unit_name }}</td>
                                                                                                                <td class="text-center">{{ $patient->affiliated_unit }}</td>
                                                                                                                <td class="text-center">{{ $patient->rotation_name }}</td>
                                                                                                                <td class="text-center">
                                                                                                                    @php
                                                                                                                        $icdCodes = explode(',', $patient->icd10_codes);
                                                                                                                        $diseaseNames = explode(',', $patient->disease_names);
                                                                                                                    @endphp
                                                                                                                    @foreach($icdCodes as $index => $code)
                                                                                                                        {{ $diseaseNames[$index] ?? 'N/A' }} ({{ $code }})<br>
                                                                                                                    @endforeach
                                                                                                                </td>
                                                                                                                <td class="text-center">
                <span>{{ $patient->treatment_status }}</span>
            </td>
                                                                                                            </tr>



                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">ไม่พบข้อมูลการรักษา</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
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

<!-- ปุ่มกรอง -->
<button id="filterButton" class="btn btn-primary">กรองข้อมูล</button>
<!-- Modal ฟอร์มกรอง -->
<!-- Modal ฟอร์มกรอง -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
<div class="modal-dialog" style="max-width: 400px;">  <!-- เพิ่ม max-width: 600px -->
<div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">กรอกข้อมูลกรอง</h5>

            </div>
            <div class="modal-body">
                <form method="GET" action="{{ url()->current() }}" class="d-flex flex-column">
                    <!-- ฟอร์มกรอง -->

                    <!-- ฟอร์มกรองสถานะ -->
                    <div class="form-group mb-3">
                        <label for="statusFilter" class="mr-2">สถานะ:</label>
                        <select name="status" class="form-control" id="statusFilter">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="Follow-up" {{ request('status') == 'Follow-up' ? 'selected' : '' }}>การติดตาม</option>
                            <option value="Admit" {{ request('status') == 'Admit' ? 'selected' : '' }}>การรับเข้ารักษา</option>
                            <option value="Refer" {{ request('status') == 'Refer' ? 'selected' : '' }}>การส่งต่อ</option>
                            <option value="Discharge" {{ request('status') == 'Discharge' ? 'selected' : '' }}>จำหน่าย</option>
                        </select>
                    </div>

                    <!-- ฟอร์มกรองแผนก -->
                    <div class="form-group mb-3">
                        <label for="departmentFilter" class="mr-2">แผนก:</label>
                        <select name="department" class="form-control" id="departmentFilter">
                            <option value="all" {{ request('department') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                            <option value="opd" {{ request('department') == 'opd' ? 'selected' : '' }}>OPD</option>
                            <option value="ipd" {{ request('department') == 'ipd' ? 'selected' : '' }}>IPD</option>
                            <option value="er" {{ request('department') == 'er' ? 'selected' : '' }}>ER</option>
                        </select>
                    </div>

                    <!-- ฟอร์มกรองวันที่ -->
                    <div class="form-group mb-3">
                        <label for="dateFilterOption" class="mr-2">เลือกวันที่:</label>
                        <select name="date_filter" class="form-control mr-2" id="dateFilterOption" onchange="toggleDateRange(this.value)">
                            <option value="today" {{ request('date_filter', 'today') == 'today' ? 'selected' : '' }}>วันนี้</option>
                            <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>ระหว่างวันที่</option>
                            <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                        </select>

                        <div id="dateRangeSection" style="{{ request('date_filter') == 'custom' ? 'display:inline-block' : 'display:none' }}; margin-top: 10px;">
    <div class="d-flex align-items-center">
        <input type="date" name="start_date" class="form-control mr-2" value="{{ request('start_date', now()->subDays(7)->format('Y-m-d')) }}">
        <span class="mr-2">ถึง</span>
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}">
    </div>
</div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-success">กรอง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .table-fixed th:nth-child(1),
.table-fixed td:nth-child(1) {
    width: 30%;
}

.table-fixed th:nth-child(2),
.table-fixed td:nth-child(2) {
    width: 30%;
}

.table-fixed th:nth-child(3),
.table-fixed td:nth-child(3) {
    width: 40%;
}



</style>

</style>
<!-- JavaScript -->


    @include('themes.script')

    <!-- JavaScript -->


    <script>
        document.getElementById('filterButton').addEventListener('click', function () {
    // เปิด modal เมื่อกดปุ่มกรอง
    var filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
    filterModal.show();
});

function toggleDateRange(value) {
    const dateRangeSection = document.getElementById('dateRangeSection');
    dateRangeSection.style.display = value === 'custom' ? 'inline-block' : 'none';
}

    </script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const filterModalEl = document.getElementById("filterModal");
        const filterModal = new bootstrap.Modal(filterModalEl);

        // เปิด Modal
        document.getElementById("filterButton").addEventListener("click", function () {
            filterModal.show();
        });

        // เมื่อปิด Modal สำเร็จจาก bootstrap
        filterModalEl.addEventListener('hidden.bs.modal', function () {
            // ✅ ลบ backdrop ถ้ายังหลงเหลือ
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(b => b.remove());

            // ✅ ปิด scroll
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = ''; // เผื่อ Bootstrap เพิ่ม padding
        });

        // กรณีกดปุ่ม “ปิด”
        filterModalEl.querySelector('.btn-close')?.addEventListener('click', () => filterModal.hide());
        filterModalEl.querySelector('.btn.btn-secondary')?.addEventListener('click', () => filterModal.hide());

        // Toggle date range
        window.toggleDateRange = function (value) {
            const dateRangeSection = document.getElementById('dateRangeSection');
            dateRangeSection.style.display = value === 'custom' ? 'inline-block' : 'none';
        };
    });
</script>






</body>

</html>