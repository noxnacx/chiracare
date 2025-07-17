<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">

                    {{-- หัวเรื่องด้านซ้าย --}}
                    <div class="mb-4">
                        <h4 class="fw-bold text-primary">
                            <i class="fas fa-user-edit me-2"></i> แก้ไขข้อมูลทหาร
                        </h4>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('soldier.update_personal_info', $soldier->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ชื่อจริง</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $soldier->first_name) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">นามสกุล</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $soldier->last_name) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">น้ำหนัก (กก.)</label>
                                <input type="number" step="0.1" name="weight_kg" class="form-control" value="{{ old('weight_kg', $soldier->weight_kg) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ส่วนสูง (ซม.)</label>
                                <input type="number" name="height_cm" class="form-control" value="{{ old('height_cm', $soldier->height_cm) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">โรคประจำตัว</label>
                                <input type="text" name="underlying_diseases" class="form-control" value="{{ old('underlying_diseases', $soldier->underlying_diseases) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">ประวัติแพ้ยา / อาหาร</label>
                                <input type="text" name="medical_allergy_food_history" class="form-control" value="{{ old('medical_allergy_food_history', $soldier->medical_allergy_food_history) }}">
                            </div>

                            {{-- ช่องอัปโหลดรูปภาพ --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold">อัปโหลดรูปภาพใหม่ (ถ้าต้องการ)</label>
                                <input type="file" name="soldier_image" class="form-control">
                                @if($soldier->soldier_image)
                                    <img src="{{ asset($soldier->soldier_image) }}" alt="Current Image" class="mt-2 border rounded" style="max-width: 150px;">
                                @endif
                            </div>
                        </div>

                        {{-- ปุ่มบันทึกด้านซ้าย --}}
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        @include('themes.soldier.footersoldier')
    </div>
</body>
</html>
@include('themes.script')
