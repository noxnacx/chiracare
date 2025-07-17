<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('themes.admin.menuadmin')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">

                    <!-- ✅ โปรไฟล์ทหาร -->
                    <div class="container">
                        <div class="card shadow-sm p-4">
                            <div class="row align-items-center">
                                <!-- ข้อมูลส่วนตัว -->
                                <div class="col-md-9">
                                    <h3 class="fw-bold text-success">พลฯ {{ $soldier->first_name }}
                                        {{ $soldier->last_name }}
                                    </h3>

                                    <p><strong>เลขบัตรประชาชน:</strong> {{ $soldier->soldier_id_card }}
                                        <strong class="ms-4">การคัดเลือก:</strong> {{ $soldier->selection_method }}
                                        <strong class="ms-4">ผลัด:</strong>
                                        {{ $soldier->rotation->rotation_name ?? 'N/A' }}
                                    </p>

                                    <p><strong>หน่วยฝึก:</strong> {{ $soldier->trainingUnit->unit_name ?? 'N/A' }}
                                        <strong class="ms-4">หน่วยต้นสังกัด:</strong>
                                        {{ $soldier->affiliated_unit ?? 'N/A' }}
                                        <strong class="ms-4">ระยะเวลารับราชการ:</strong>
                                        {{ $soldier->service_duration }} เดือน
                                    </p>

                                    <p><strong>โรคประจำตัว:</strong> {{ $soldier->underlying_diseases ?? 'ไม่มี' }}
                                        <strong class="ms-4">ประวัติแพ้ยา/อาหาร:</strong>
                                        {{ $soldier->medical_allergy_food_history ?? 'ไม่มี' }}
                                    </p>

                                    <p><strong>น้ำหนัก:</strong> {{ $soldier->weight_kg }} kg
                                        <strong class="ms-4">ส่วนสูง:</strong> {{ $soldier->height_cm }} cm
                                        <strong class="ms-4">BMI:</strong>
                                        {{ number_format($soldier->weight_kg / (($soldier->height_cm / 100) ** 2), 1) }}
                                    </p>
                                </div>

                                <!-- รูปภาพ -->
                                <div class="col-md-3 text-center">
                                    @if($soldier->soldier_image)
                                        <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}"
                                            alt="Soldier Image" width="180" class="soldier-image">
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ✅ Treatment History & Mental Health -->
                    <div class="container mt-4">
                        <div class="card p-4">
                            <!-- Navigation Tabs -->
                            <ul class="nav nav-tabs" id="profileTabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#treatmentHistory">Treatment
                                        History</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#mentalHealth">Mental Health
                                        Assessment</a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-3">
                                <!-- Treatment History -->
                                <div class="tab-pane fade show active" id="treatmentHistory">
                                    <div>


                                        <div class="container mt-4">
                                            <div class="card p-4">

                                                <ul class="list-unstyled">
                                                    @foreach($soldier->medicalDiagnoses as $diagnosis)
                                                        <li class="card mb-3 p-3 border">
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <strong>ชื่อหมอ:</strong>
                                                                    {{ $diagnosis->doctor_name }}<br>
                                                                    <strong>ชื่อโรค:</strong>
                                                                    @foreach($diagnosis->diseases as $disease)
                                                                        <span
                                                                            class="badge bg-info">{{ $disease->disease_name_en }}</span><br>
                                                                    @endforeach

                                                                </div>
                                                                <div>
                                                                    <button type="button" class="btn btn-info"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#treatmentModal{{ $diagnosis->id }}">
                                                                        ดูรายละเอียดการรักษา
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Mental Health Assessment -->
                                <div class="tab-pane fade" id="mentalHealth">
                                    <div class="list-group">
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="fw-bold">Psychological Evaluation</h5>
                                                <p class="mb-1">Dr. Emily White</p>
                                            </div>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                            <small class="text-muted">Upcoming</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /.container-fluid -->
            </div>
            <!-- Main content -->
            @yield('content')
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        @include('themes.admin.footeradmin')
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
</body>

<!-- Modal -->
@foreach($soldier->medicalDiagnoses as $diagnosis)
    <div class="modal fade" id="treatmentModal{{ $diagnosis->id }}" tabindex="-1"
        aria-labelledby="treatmentModalLabel{{ $diagnosis->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="treatmentModalLabel{{ $diagnosis->id }}">ข้อมูลการรักษาของทหาร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- เพิ่มข้อมูลการรักษา -->
                    <strong>รหัสการรักษา:</strong> {{ $diagnosis->treatment_id }}<br>
                    <strong>ชื่อหมอ:</strong> {{ $diagnosis->doctor_name }}<br>
                    <strong>สถานะการรักษา:</strong> {{ $diagnosis->treatment_status }}<br>
                    <strong>วันที่วินิจฉัย:</strong> {{ $diagnosis->diagnosis_date }}<br>
                    <strong>ชื่อโรค:</strong>
                    @foreach($diagnosis->diseases as $disease)
                        {{ $disease->disease_name_en }}<br>
                    @endforeach
                    <strong>สัญญาณชีพ:</strong><br>
                    @if($diagnosis->vitalSigns)
                        <strong>อุณหภูมิ:</strong> {{ $diagnosis->vitalSigns->temperature }} °C<br>
                        <strong>ความดันโลหิต:</strong> {{ $diagnosis->vitalSigns->blood_pressure }}<br>
                        <strong>อัตราการเต้นของหัวใจ:</strong> {{ $diagnosis->vitalSigns->heart_rate }} bpm<br>
                    @else
                        <p>ไม่มีข้อมูลสัญญาณชีพ</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
<style>
    .soldier-image {
        border-radius: 15px;
        /* ทำให้มุมโค้ง */
        border: 2px solid #fff;
        /* เพิ่มเส้นขอบรอบรูป */
        padding: 5px;
        /* เพิ่มระยะห่างระหว่างกรอบและรูป */
    }
</style>



@include('themes.script')

</html>