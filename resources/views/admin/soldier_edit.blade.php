<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<head>
    <style>
        /* สไตล์เพิ่มเติม */
        .form-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
            border: 1px solid #e0e0e0;
        }

        .form-header {
            border-bottom: 2px solid #3a7bd5;
            padding-bottom: 15px;
            margin-bottom: 25px;
            color: #2c5282;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #3a7bd5;
            box-shadow: 0 0 0 0.2rem rgba(58, 123, 213, 0.25);
        }

        .profile-image-container {
            border: 2px dashed #3a7bd5;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        .profile-image {
            max-width: 200px;
            border-radius: 5px;
            border: 3px solid #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: #38a169;
            border-color: #38a169;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-success:hover {
            background-color: #2f855a;
            transform: translateY(-2px);
        }

        .form-label {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .section-border {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: #f8fafc;
        }

        .section-title {
            color: #2c5282;
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
</head>

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
                        <div class="form-container">
                            <div class="form-header">
                                <h3 class="text-primary fw-bold">
                                    แก้ไขข้อมูลทหาร
                                </h3>
                            </div>

                            <form action="{{ route('soldier.update_soldier', $soldier->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="section-border">
                                    <h4 class="section-title">ข้อมูลส่วนตัว</h4>
                                    <div class="row">
                                        <!-- ชื่อจริง -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">ชื่อจริง</label>
                                            <input type="text" class="form-control" name="first_name"
                                                value="{{ $soldier->first_name }}" required>
                                        </div>

                                        <!-- นามสกุล -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">นามสกุล</label>
                                            <input type="text" class="form-control" name="last_name"
                                                value="{{ $soldier->last_name }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-border">
                                    <h4 class="section-title"></i>ข้อมูลการฝึก</h4>
                                    <div class="row">
                                        <!-- ผลัด -->
                                        <div class="col-md-6 form-group">
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
                                        <div class="col-md-6 form-group">
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
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">หน่วยต้นสังกัด</label>
                                            <input type="text" class="form-control" name="affiliated_unit"
                                                value="{{ $soldier->affiliated_unit }}">
                                        </div>

                                        <!-- วิธีการคัดเลือก -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">วิธีการคัดเลือก</label>
                                            <input type="text" class="form-control" name="selection_method"
                                                value="{{ $soldier->selection_method }}">
                                        </div>

                                        <!-- ระยะเวลาการฝึก (เดือน) -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">ระยะเวลาการฝึก (เดือน)</label>
                                            <input type="number" class="form-control" name="service_duration"
                                                value="{{ $soldier->service_duration }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-border">
                                    <h4 class="section-title">ข้อมูลสุขภาพ</h4>
                                    <div class="row">
                                        <!-- น้ำหนัก -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">น้ำหนัก (กก.)</label>
                                            <input type="number" step="0.1" class="form-control" name="weight_kg"
                                                value="{{ $soldier->weight_kg }}">
                                        </div>

                                        <!-- ส่วนสูง -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">ส่วนสูง (ซม.)</label>
                                            <input type="number" class="form-control" name="height_cm"
                                                value="{{ $soldier->height_cm }}">
                                        </div>

                                        <!-- โรคประจำตัว -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">โรคประจำตัว</label>
                                            <input type="text" class="form-control" name="underlying_diseases"
                                                value="{{ $soldier->underlying_diseases }}">
                                        </div>

                                        <!-- ประวัติแพ้ยา / อาหาร -->
                                        <div class="col-md-6 form-group">
                                            <label class="form-label">ประวัติแพ้ยา / อาหาร</label>
                                            <input type="text" class="form-control" name="medical_allergy_food_history"
                                                value="{{ $soldier->medical_allergy_food_history }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="section-border">
                                    <h4 class="section-title">รูปภาพโปรไฟล์</h4>
                                    <div class="profile-image-container">
                                        <label class="form-label">โปรไฟล์ทหาร</label>
                                        <div>
                                            @if($soldier->soldier_image)
                                                <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}"
                                                    class="profile-image" alt="Soldier Image">
                                            @else
                                                <div class="text-muted">
                                                    <p class="mt-2">ไม่มีรูปภาพ</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control mt-3" name="soldier_image"
                                            style="max-width: 300px; margin: 0 auto;">
                                    </div>
                                </div>

                                <!-- ปุ่มบันทึก -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success">
                                        บันทึกข้อมูล
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-wrapper -->
        @include('themes.admin.footeradmin')
    </div>
</body>

</html>
@include('themes.script')