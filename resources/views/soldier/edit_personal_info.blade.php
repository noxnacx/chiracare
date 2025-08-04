<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <style>
        /* --- Theme Colors & Interactions --- */
        :root {
            /* Original Theme */
            --theme-primary-color: #A9C5C8; /* Muted Blue-Gray */
            --theme-secondary-bg: #F8F9FA;  /* Light Gray Background */
            --theme-text-dark: #343a40;
            --theme-card-bg: #FFFFFF;

            /* New Accent Color (Purple) */
            --theme-accent-color: #8E44AD;   /* Vibrant Purple */
            --theme-accent-darker: #7D3C98;  /* For Hover */
            --theme-accent-darkest: #6C3483; /* For Active/Press */
        }

        /* --- General Styling --- */
        body {
            background-color: var(--theme-secondary-bg);
            font-family: 'Sarabun', sans-serif;
        }
        .content-wrapper {
            background-color: transparent;
        }
        h1, h3, h4, h5, h6 {
            color: var(--theme-text-dark);
            font-weight: bold;
        }

        /* --- Card Styling --- */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem !important;
            transition: box-shadow 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        /* --- ACCENT Button Styling (Purple) --- */
        .btn-theme-accent {
            background-color: var(--theme-accent-color);
            border-color: var(--theme-accent-color);
            color: #fff;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
        }
        .btn-theme-accent:hover {
            background-color: var(--theme-accent-darker);
            border-color: var(--theme-accent-darker);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .btn-theme-accent:focus {
            color: #fff;
            background-color: var(--theme-accent-darker);
            border-color: var(--theme-accent-darker);
            box-shadow: 0 0 0 0.25rem rgba(142, 68, 173, 0.5); /* Purple focus ring */
        }
        .btn-theme-accent:active {
            color: #fff;
            background-color: var(--theme-accent-darkest);
            border-color: var(--theme-accent-darkest);
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* --- Form Styling --- */
        .form-label {
            color: var(--theme-text-dark);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: .5rem .9rem;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .form-control:focus {
            border-color: var(--theme-accent-color); /* Focus color changed to purple */
            box-shadow: 0 0 0 0.25rem rgba(142, 68, 173, 0.5); /* Purple focus ring */
        }
        .image-preview {
            border: 2px dashed #dee2e6;
            padding: 8px;
        }
    </style>
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">

                    <div class="mb-4">
                        <h4 class="fw-bold">
                            <i class="fas fa-user-edit me-2" style="color: var(--theme-accent-color);"></i> แก้ไขข้อมูลส่วนตัว
                        </h4>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif

                    <div class="card">
                        <div class="card-body p-4">
                            <form action="{{ route('soldier.update_personal_info', $soldier->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ชื่อจริง</label>
                                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $soldier->first_name) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">นามสกุล</label>
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $soldier->last_name) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">น้ำหนัก (กก.)</label>
                                        <input type="number" step="0.1" name="weight_kg" class="form-control" value="{{ old('weight_kg', $soldier->weight_kg) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ส่วนสูง (ซม.)</label>
                                        <input type="number" name="height_cm" class="form-control" value="{{ old('height_cm', $soldier->height_cm) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">โรคประจำตัว</label>
                                        <input type="text" name="underlying_diseases" class="form-control" placeholder="ระบุ 'ไม่มี' หากไม่มีโรคประจำตัว" value="{{ old('underlying_diseases', $soldier->underlying_diseases) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ประวัติแพ้ยา / อาหาร</label>
                                        <input type="text" name="medical_allergy_food_history" class="form-control" placeholder="ระบุ 'ไม่มี' หากไม่มีประวัติแพ้" value="{{ old('medical_allergy_food_history', $soldier->medical_allergy_food_history) }}">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">อัปโหลดรูปภาพใหม่ (ถ้าต้องการ)</label>
                                        <input type="file" name="soldier_image" class="form-control">
                                        @if($soldier->soldier_image)
                                            <div class="mt-3">
                                                <label class="form-label d-block">รูปภาพปัจจุบัน:</label>
                                                <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}" alt="Current Image" class="mt-2 rounded image-preview" style="max-width: 150px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-end">
                                    <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left me-1"></i> ย้อนกลับ
                                    </a>
                                    <button type="submit" class="btn btn-theme-accent">
                                        <i class="fas fa-save me-1"></i> บันทึกข้อมูล
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('themes.soldier.footersoldier')
    </div>

    @include('themes.script')
</body>
</html>
