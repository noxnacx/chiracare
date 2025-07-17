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

                    <div class="container">
                        <h3 class="text-primary fw-bold">
                            <i class="fas fa-user-edit"></i> แก้ไขข้อมูลทหาร
                        </h3>

                        <form action="{{ route('soldier.update_soldier', $soldier->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')


                            <div class="row">
                                <!-- ชื่อจริง -->
                                <div class="col-md-6">
                                    <label class="form-label">ชื่อจริง</label>
                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ $soldier->first_name }}" required>
                                </div>

                                <!-- นามสกุล -->
                                <div class="col-md-6">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" class="form-control" name="last_name"
                                        value="{{ $soldier->last_name }}" required>
                                </div>

                                <!-- ผลัด -->
                                <div class="col-md-6">
                                    <label class="form-label">ผลัด</label>
                                    <select class="form-control" name="rotation_id">
                                        <option value="">-- เลือกผลัด --</option>
                                        @foreach($rotations as $rotation)
                                            <option value="{{ $rotation->id }}" {{ $soldier->rotation_id == $rotation->id ? 'selected' : '' }}>
                                                {{ $rotation->rotation_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- หน่วยฝึก -->
                                <div class="col-md-6">
                                    <label class="form-label">หน่วยฝึก</label>
                                    <select class="form-control" name="training_unit_id">
                                        <option value="">-- เลือกหน่วยฝึก --</option>
                                        @foreach($training_units as $unit)
                                            <option value="{{ $unit->id }}" {{ $soldier->training_unit_id == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- หน่วยต้นสังกัด -->
                                <div class="col-md-6">
                                    <label class="form-label">หน่วยต้นสังกัด</label>
                                    <input type="text" class="form-control" name="affiliated_unit"
                                        value="{{ $soldier->affiliated_unit }}">
                                </div>

                                <!-- น้ำหนัก -->
                                <div class="col-md-6">
                                    <label class="form-label">น้ำหนัก (กก.)</label>
                                    <input type="number" step="0.1" class="form-control" name="weight_kg"
                                        value="{{ $soldier->weight_kg }}">
                                </div>

                                <!-- ส่วนสูง -->
                                <div class="col-md-6">
                                    <label class="form-label">ส่วนสูง (ซม.)</label>
                                    <input type="number" class="form-control" name="height_cm"
                                        value="{{ $soldier->height_cm }}">
                                </div>

                                <!-- โรคประจำตัว -->
                                <div class="col-md-6">
                                    <label class="form-label">โรคประจำตัว</label>
                                    <input type="text" class="form-control" name="underlying_diseases"
                                        value="{{ $soldier->underlying_diseases }}">
                                </div>

                                <!-- ประวัติแพ้ยา / อาหาร -->
                                <div class="col-md-6">
                                    <label class="form-label">ประวัติแพ้ยา / อาหาร</label>
                                    <input type="text" class="form-control" name="medical_allergy_food_history"
                                        value="{{ $soldier->medical_allergy_food_history }}">
                                </div>

                                <!-- วิธีการคัดเลือก -->
                                <div class="col-md-6">
                                    <label class="form-label">วิธีการคัดเลือก</label>
                                    <input type="text" class="form-control" name="selection_method"
                                        value="{{ $soldier->selection_method }}">
                                </div>

                                <!-- ระยะเวลาการฝึก (เดือน) -->
                                <div class="col-md-6">
                                    <label class="form-label">ระยะเวลาการฝึก (เดือน)</label>
                                    <input type="number" class="form-control" name="service_duration"
                                        value="{{ $soldier->service_duration }}">
                                </div>

                                <!-- รูปภาพโปรไฟล์ -->
                                <div class="col-md-12 text-center mt-4">
                                    <label class="form-label">โปรไฟล์ทหาร</label>
                                    <div>
                                    @if($soldier->soldier_image)
                                    <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}" alt="Soldier Image" width="150">
                                    @endif
                                    </div>
                                    <input type="file" class="form-control mt-2" name="soldier_image">
                                </div>  
                            </div>

                            <!-- ปุ่มบันทึก -->
                            <button type="submit" class="btn btn-success mt-4">
                                <i class="fas fa-save"></i> บันทึกข้อมูล
                            </button>
                        </form>
                    </div>
                </div>

            </div><!-- /.container-fluid -->
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
</html>
@include('themes.script')