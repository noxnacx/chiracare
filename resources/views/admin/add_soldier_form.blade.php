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

                    @section('content')
                            <div class="container mt-2 mb-5"
                                style="background-color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; border-radius: 8px; border: 1px solid #ddd;">

                                <h2 class="mb-4">เพิ่มข้อมูลทหาร</h2>

                                <form action="{{ url('/soldier/store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name">ชื่อ</label>
                                            <input type="text" id="first_name" name="first_name" class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="last_name">นามสกุล</label>
                                            <input type="text" id="last_name" name="last_name" class="form-control" required>

                                        </div>

                                        <div class="col-md-6 mb-3">

                                            <label for="soldier_id_card">รหัสประจำตัวทหาร</label>
                                            <input type="text" id="soldier_id_card" name="soldier_id_card" class="form-control"
                                                required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="rotation_id">ผลัด</label>
                                            <select id="rotation_id" name="rotation_id" class="form-control" required>
                                                <option value="">-- เลือกผลัด --</option>
                                                @foreach($rotations as $rotation)
                                                    @if($rotation->status == 'active')
                                                        <option value="{{ $rotation->id }}">{{ $rotation->rotation_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="training_unit_id">หน่วยฝึก</label>
                                            <select id="training_unit_id" name="training_unit_id" class="form-control" required>
                                                <option value="">-- เลือกหน่วยฝึก --</option>
                                                @foreach($units as $unit)
                                                    @if($unit->status == 'active')
                                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="affiliated_unit">หน่วยสังกัด</label>
                                            <input type="text" id="affiliated_unit" name="affiliated_unit" class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="weight_kg">น้ำหนัก (kg)</label>
                                            <input type="number" id="weight_kg" name="weight_kg" class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="height_cm">ส่วนสูง (cm)</label>
                                            <input type="number" id="height_cm" name="height_cm" class="form-control">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="medical_allergy_food_history">ประวัติแพ้ยา/อาหาร</label>
                                            <textarea id="medical_allergy_food_history" name="medical_allergy_food_history"
                                                class="form-control"></textarea>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="underlying_diseases">โรคประจำตัว</label>
                                            <textarea id="underlying_diseases" name="underlying_diseases"
                                                class="form-control"></textarea>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="selection_method">วิธีคัดเลือก</label>
                                            <input type="text" id="selection_method" name="selection_method"
                                                class="form-control" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="service_duration">ระยะเวลารับราชการ (เดือน)</label>
                                            <input type="number" id="service_duration" name="service_duration"
                                                class="form-control" required>
                                        </div>


                                    </div>

                                    <button type="submit" class="btn btn-success">เพิ่มทหาร</button>
                                </form>
                            </div>
                        </div>
                    @endsection


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