<!DOCTYPE html>
<html lang="en">
@include('themes.head')


<style>

</style>


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.er.navbarer')

        @include('themes.er.menuer')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">


                            <div class="row mb-4">
                                @php
                                    $statusMapping = [
                                        'admit' => [
                                            'label' => 'Admit',
                                            'status_value' => 'Admit', // ✅ ใช้ใน URL
                                            'icon' => 'fas fa-hospital-user',
                                            'color' => 'text-primary'
                                        ],
                                        'refer' => [
                                            'label' => 'Refer',
                                            'status_value' => 'Refer',
                                            'icon' => 'fas fa-sign-out-alt',
                                            'color' => 'text-warning'
                                        ],
                                        'follow_up' => [
                                            'label' => 'ติดตามอาการ',
                                            'status_value' => 'Follow-up', // ✅ ต้องตรง enum DB
                                            'icon' => 'fas fa-clipboard-check',
                                            'color' => 'text-info'
                                        ],
                                        'discharge' => [
                                            'label' => 'จำหน่ายออก',
                                            'status_value' => 'Discharge',
                                            'icon' => 'fas fa-house-user',
                                            'color' => 'text-success'
                                        ],
                                    ];
                                @endphp


                                @foreach ($statusMapping as $key => $info)
                                    <div class="col-md-3">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="mb-0 fw-bold fs-5">{{ $info['label'] }}</h5>
                                                    <i class="{{ $info['icon'] }} {{ $info['color'] }} fa-2x"></i>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <div class="text-muted fs-6">วันนี้</div>
                                                    <div class="fw-bold fs-5">
                                                        <a href="{{ url()->current() }}?status={{ $info['status_value'] }}&date_filter=today"
                                                            class="text-decoration-none {{ $info['color'] }}">
                                                            {{ $todayStats[$key] ?? 0 }} <span class="text-muted">ราย</span>
                                                        </a>
                                                    </div>


                                                </div>
                                                <div class="d-flex justify-content-between">

                                                    <div class="text-muted fs-6">สะสม</div>
                                                    <div class="fw-bold fs-5">
                                                        <a href="{{ url()->current() }}?status={{ $info['status_value'] }}&date_filter=all"
                                                            class="text-decoration-none {{ $info['color'] }}">
                                                            {{ $totalStats[$key] ?? 0 }} <span class="text-muted">ราย</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>





                            <!-- Patient Details Table -->
                            <div class="row mt-1" id="patient-details">
                                <div class="col-md-12">




                                    <div class="card shadow-lg">
                                        <div class="card-header text-white d-flex justify-content-between align-items-center"
                                            style="background-color:rgb(76, 142, 167); color: white;">
                                            <h4 class="mb-0">ประวัติการรักษา</h4>
                                            <button id="filterButton" class="btn ml-auto"
                                                style="background-color: white; color: black; border: 2px solid black;">
                                                <i class="fas fa-filter me-2"></i> กรองข้อมูล
                                            </button>


                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive"
                                                style="background-color: #f7f9fc; padding: 15px; border-radius: 10px;">
                                                <table
                                                    class="table table-striped table-bordered table-hover data-table">
                                                    <thead class="table-info">
                                                        <tr>
                                                            <th>วันที่วินิจฉัย</th>
                                                            <th>ชื่อ–สกุล</th>
                                                            <th>เลขประจำตัว</th>
                                                            <th>หน่วยฝึก</th>
                                                            <th>หน่วยต้นสังกัด</th>
                                                            <th>ผลัด</th>
                                                            <th>การวินิจฉัย</th>
                                                            <th>สถานะ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($patientDetails as $patient)
                                                            <tr>
                                                                <td>{{ \Carbon\Carbon::parse($patient->diagnosis_date)->format('d/m/Y') }}
                                                                </td>
                                                                <td>{{ $patient->first_name ?? '-' }}
                                                                    {{ $patient->last_name ?? '-' }}
                                                                </td>
                                                                <td>{{ $patient->soldier_id_card ?? '-' }}</td>
                                                                <td>{{ $patient->training_unit_name ?? '-' }}</td>
                                                                <td>{{ $patient->affiliated_unit ?? '-' }}</td>
                                                                <td>{{ $patient->rotation_name ?? '-' }}</td>
                                                                <td>
                                                                    @if ($patient->disease_names)
                                                                        {!! nl2br(e(implode(', ', explode(',', $patient->disease_names)))) !!}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $patient->treatment_status ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center text-danger">
                                                                    ไม่พบข้อมูลผู้ป่วยวันนี้
                                                                </td>
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
    </div>





    <!-- ปุ่มกรอง -->
    <button id="filterButton" class="btn btn-primary">กรองข้อมูล</button>
    <!-- Modal ฟอร์มกรอง -->
    <!-- Modal ฟอร์มกรอง -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 400px;"> <!-- เพิ่ม max-width: 600px -->
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
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>ทั้งหมด
                                </option>
                                <option value="Follow-up" {{ request('status') == 'Follow-up' ? 'selected' : '' }}>
                                    การติดตาม</option>
                                <option value="Admit" {{ request('status') == 'Admit' ? 'selected' : '' }}>
                                    การรับเข้ารักษา</option>
                                <option value="Refer" {{ request('status') == 'Refer' ? 'selected' : '' }}>
                                    การส่งต่อ</option>
                                <option value="Discharge" {{ request('status') == 'Discharge' ? 'selected' : '' }}>
                                    จำหน่าย
                                </option>
                            </select>
                        </div>

                        <!-- ฟอร์มกรองแผนก -->
                        <!-- ฟอร์มกรองวันที่ -->
                        <div class="form-group mb-3">
                            <label for="dateFilterOption" class="mr-2">เลือกวันที่:</label>
                            <select name="date_filter" class="form-control mr-2" id="dateFilterOption"
                                onchange="toggleDateRange(this.value)">
                                <option value="today" {{ request('date_filter', 'today') == 'today' ? 'selected' : '' }}>
                                    วันนี้</option>
                                <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>
                                    ระหว่างวันที่</option>
                                <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>
                                    ทั้งหมด</option>
                            </select>

                            <div id="dateRangeSection"
                                style="{{ request('date_filter') == 'custom' ? 'display:inline-block' : 'display:none' }}; margin-top: 10px;">
                                <div class="d-flex align-items-center">
                                    <input type="date" name="start_date" class="form-control mr-2"
                                        value="{{ request('start_date', now()->subDays(7)->format('Y-m-d')) }}">
                                    <span class="mr-2">ถึง</span>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date', now()->format('Y-m-d')) }}">
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
    @include('themes.script')


</body>

</html>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtn = document.getElementById('filterButton');
        const filterModal = new bootstrap.Modal(document.getElementById('filterModal'));

        filterBtn.addEventListener('click', function () {
            filterModal.show();
        });

        // Toggle custom date range section
        window.toggleDateRange = function (value) {
            const section = document.getElementById('dateRangeSection');
            section.style.display = value === 'custom' ? 'block' : 'none';
        };
    });
</script>