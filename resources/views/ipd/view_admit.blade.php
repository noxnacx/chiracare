<!DOCTYPE html>
<html lang="th">
@include('themes.head')


<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')
        <!-- Main Sidebar Container -->
        @include('themes.ipd.menuipd')

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="container mt-4">

                    <!-- Filter Form -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>รายชื่อผู้ป่วย IPD</h2>
                        <button id="filterButton" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> กรองข้อมูล
                        </button>
                    </div>

                    <!-- Filter Modal -->
                    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="filterModalLabel">กรอกข้อมูลกรอง</h5>
                                    <!-- Custom Close Button with Blue Background and White Icon -->

                                </div>

                                <div class="modal-body">
                                    <form method="GET" action="{{ url()->current() }}" class="d-flex flex-column">
                                        <!-- ฟอร์มกรองสถานะ -->


                                        <!-- ฟอร์มกรองหน่วย -->
                                        <div class="form-group mb-3">
                                            <label for="unitFilter" class="form-label">หน่วย:</label>
                                            <select name="unit" id="unitFilter" class="form-control">
                                                <option value="all" {{ request('unit') == 'all' ? 'selected' : '' }}>
                                                    ทั้งหมด</option>
                                                <option value="unit_1" {{ request('unit') == 'unit_1' ? 'selected' : '' }}>หน่วยที่ 1</option>
                                                <option value="unit_2" {{ request('unit') == 'unit_2' ? 'selected' : '' }}>หน่วยที่ 2</option>
                                            </select>
                                        </div>

                                        <!-- ฟอร์มกรองผลัด -->
                                        <div class="form-group mb-3">
                                            <label for="rotationFilter" class="form-label">ผลัด:</label>
                                            <select name="rotation" id="rotationFilter" class="form-control">
                                                <option value="all" {{ request('rotation') == 'all' ? 'selected' : '' }}>
                                                    ทั้งหมด</option>
                                                <option value="morning" {{ request('rotation') == 'morning' ? 'selected' : '' }}>ผลัดเช้า</option>
                                                <option value="evening" {{ request('rotation') == 'evening' ? 'selected' : '' }}>ผลัดบ่าย</option>
                                            </select>
                                        </div>

                                        <!-- ฟอร์มกรองวันที่ -->
                                        <div class="form-group mb-3">
                                            <label for="dateFilter" class="form-label">เลือกวันที่:</label>
                                            <select name="date_filter" id="dateFilter" class="form-control">
                                                <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>วันนี้</option>
                                                <option value="custom" {{ request('date_filter') == 'custom' ? 'selected' : '' }}>ระหว่างวันที่</option>
                                            </select>
                                            <div id="dateRangeSection"
                                                style="display: {{ request('date_filter') == 'custom' ? 'inline-block' : 'none' }}; margin-top: 10px;">
                                                <input type="date" name="start_date" class="form-control"
                                                    value="{{ request('start_date') }}">
                                                <input type="date" name="end_date" class="form-control"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">ปิด</button>
                                            <button type="submit" class="btn btn-success">กรอง</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table for Patient Details -->
                    <div class="table-container bg-white p-4 rounded shadow-sm border">
                        <table class="table table-striped table-bordered data-table">
                            <thead class="table-info">
                                <tr>
                                    <th>ชื่อ - นามสกุล</th>
                                    <th>เลข 13 หลัก</th>
                                    <th>ผลัด</th>
                                    <th>หน่วยฝึก</th>
                                    <th>หน่วยต้นสังกัด</th>
                                    <th>การวินิคฉัย</th>
                                    <th>วันที่ Admit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($patientDetails as $patient)
                                    <tr>
                                        <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                        <td>{{ $patient->soldier_id_card }}</td>
                                        <td>{{ $patient->rotation_name }}</td>
                                        <td>{{ $patient->training_unit_name }}</td>
                                        <td>{{ $patient->affiliated_unit }}</td>
                                        <td>
                                            @php
                                                $codes = explode(',', $patient->icd10_codes);
                                                $names = explode(',', $patient->disease_names);

                                            @endphp
                                            <ul class="pl-3 mb-0">
                                                @foreach($codes as $index => $code)
                                                    {{ trim($code) }}: {{ $names[$index] ?? '-' }}
                                                    <br>
                                                @endforeach

                                            </ul>
                                        </td>
                                        <td>
                                            @php
                                                $dt = \Carbon\Carbon::parse($patient->diagnosis_date);
                                            @endphp
                                            วันที่ {{ $dt->format('d/m/Y') }}<br>เวลา
                                            {{ $dt->format('H:i') }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-danger">ไม่พบข้อมูลผู้ป่วยตามเงื่อนไขที่กรอง
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Display Total Stats -->



                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
</body>

</html>