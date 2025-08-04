<!DOCTYPE html>
<html lang="en">
@include('themes.head')


<style>

</style>


</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')

        @include('themes.opd.menuopd')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">


                            <div class="container">
                                <div class="d-flex justify-content-between align-items-center my-4">
                                    <h2 class="mb-0">รายการนัดหมายวันนี้
                                        ({{ \Carbon\Carbon::today()->format('d/m/Y') }})</h2>
                                    <button id="filterButton" class="btn"
                                        style="background-color: white; color: black; border: 2px solid black;">
                                        <i class="fas fa-filter me-2"></i> กรองข้อมูล
                                    </button>
                                </div>

                                <div class="card shadow-sm w-100">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered data-table">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>ชื่อ</th>
                                                        <th>หน่วยฝึก</th>
                                                        <th>หน่วยฝึกต้นสังกัด</th>
                                                        <th>ผลัด</th>
                                                        <th>อาการ</th>
                                                        <th>ข้อมูลนัดหมาย</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($appointments as $appointment)
                                                        <tr>
                                                            <td>
                                                                {{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                                {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                            </td>
                                                            <td>{{ $appointment->medicalReport->soldier->trainingUnit->unit_name ?? '-' }}
                                                            </td>
                                                            <td>{{ $appointment->medicalReport->soldier->affiliated_unit ?? '-' }}
                                                            </td>
                                                            <td>{{ $appointment->medicalReport->soldier->rotation->rotation_name ?? '-' }}
                                                            </td>
                                                            <td>{{ $appointment->medicalReport->symptom_description ?? '-' }}
                                                            </td>
                                                            <td>
                                                                <strong>วัน:</strong>
                                                                {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') : '-' }}<br>
                                                                <strong>เวลา:</strong>
                                                                {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i') : '-' }}<br>
                                                                <strong>สถานที่:</strong>
                                                                {{ $appointment->appointment_location }}
                                                            </td>
                                                            <td class="text-center">
                                                                @if($appointment->checkin_status === 'checked-in' && $appointment->treatment_status == 'not-treated')
                                                                    <span
                                                                        class="badge bg-light text-success shadow-sm px-3 py-2"
                                                                        style="border-radius: 15px;">
                                                                        <span class="dot"
                                                                            style="background-color:rgb(226, 216, 20); width: 10px; height: 10px; display: inline-block; border-radius: 50%; margin-right: 5px;"></span>
                                                                        อยู่ระหว่างการรักษา
                                                                    </span>
                                                                @elseif($appointment->treatment_status == 'treated')
                                                                    <span
                                                                        class="badge bg-light text-primary shadow-sm px-3 py-2"
                                                                        style="border-radius: 15px;">
                                                                        <span class="dot"
                                                                            style="background-color:rgb(7, 213, 4); width: 10px; height: 10px; display: inline-block; border-radius: 50%; margin-right: 5px;"></span>
                                                                        รักษาเสร็จสิ้น
                                                                    </span>
                                                                @elseif($appointment->checkin_status === 'not-checked-in')
                                                                    <span
                                                                        class="badge bg-light text-warning shadow-sm px-3 py-2"
                                                                        style="border-radius: 15px;">
                                                                        <span class="dot"
                                                                            style="background-color:rgb(255, 0, 0); width: 10px; height: 10px; display: inline-block; border-radius: 50%; margin-right: 5px;"></span>
                                                                        ยังไม่ได้เข้ารับการรักษา
                                                                    </span>
                                                                @endif
                                                            </td>






                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">ไม่มีนัดหมายวันนี้</td>
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


                        <!-- ฟอร์มกรอง case_type -->
                        <div class="form-group mb-3">
                            <label>ประเภทเคส:</label>
                            <select name="case_type" class="form-control">
                                <option value="all">ทั้งหมด</option>
                                <option value="normal" {{ request('case_type') == 'normal' ? 'selected' : '' }}>
                                    ปกติ
                                </option>
                                <option value="critical" {{ request('case_type') == 'critical' ? 'selected' : '' }}>วิกฤติ
                                </option>
                            </select>
                        </div>

                        <!-- ฟอร์มกรอง appointment_location -->
                        <div class="form-group mb-3">
                            <label>สถานที่นัดหมาย:</label>
                            <select name="location" class="form-control">
                                <option value="all">ทั้งหมด</option>
                                <option value="OPD" {{ request('location') == 'OPD' ? 'selected' : '' }}>OPD
                                </option>
                                <option value="ARI clinic" {{ request('location') == 'ARI clinic' ? 'selected' : '' }}>ARI
                                    clinic</option>
                                <option value="กองทันตกรรม" {{ request('location') == 'กองทันตกรรม' ? 'selected' : '' }}>
                                    กองทันตกรรม</option>
                            </select>
                        </div>

                        <!-- ฟอร์มกรอง checkin_status -->
                        <div class="form-group mb-3">
                            <label>สถานะ:</label>
                            <select name="filter" class="form-control">
                                <option value="all">ทั้งหมด</option>
                                <option value="checked-in" {{ request('filter') == 'checked-in' ? 'selected' : '' }}>
                                    อยู่ระหว่างการรักษา</option>
                                <option value="not-checked-in" {{ request('filter') == 'not-checked-in' ? 'selected' : '' }}>ยังไม่ได้เข้ารับการรักษา</option>
                            </select>
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