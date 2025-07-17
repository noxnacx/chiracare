<!DOCTYPE html>
<html lang="en">
@include('themes.head')
<style>
    body {
        background-color: #f4f7fc;
        color: #333;
    }

    .container {
        max-width: 900px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h4 {
        color: #2b9b6a;
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
    }

    .form-label {
        font-weight: bold;
        color: #444;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 10px;
        width: 100%;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .form-control:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 5px;
        font-size: 18px;
        cursor: pointer;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .mb-3 {
        margin-bottom: 20px;
    }

    #soldier-info {
        margin-top: 20px;
        display: none;
    }

    .alert {
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
        display: none;
    }

    #risk_level {
        background-color: #f2f2f2;
        text-align: center;
        font-weight: bold;
        color: #000;
    }

    .d-flex {
        display: flex;
        justify-content: flex-end;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 5px;
        margin-top: 20px;
        display: none;
    }
</style>
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.er.navbarer')
        <!-- Main Sidebar Container -->
        @include('themes.er.menuer')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <h4>ฟอร์มกรอกข้อมูลผู้ป่วย ER</h4>
                        <div class="alert alert-danger" id="error-alert">
                            ไม่พบข้อมูลทหาร
                        </div>
                        <div class="alert alert-success" id="success-alert">
                            ข้อมูลถูกบันทึกเรียบร้อย
                        </div>
                        <form id="erForm" action="{{ route('er.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- กรอกเลขบัตรประชาชน -->
                            <div class="mb-3">
                                <label for="soldier_id_card" class="form-label">กรอกเลขบัตรประชาชนทหาร</label>
                                <input type="text" class="form-control" id="soldier_id_card" name="soldier_id_card"
                                    required>
                            </div>

                            <!-- แสดงชื่อทหาร -->
                            <div id="soldier-info" class="mb-3" style="display:none;">
                                <label class="form-label">ชื่อทหาร</label>
                                <input type="text" id="soldier_name" class="form-control" readonly>
                            </div>

                            <!-- คำอธิบายอาการ -->
                            <div class="mb-3">
                                <label for="symptom_description" class="form-label">คำอธิบายอาการ</label>
                                <textarea name="symptom_description" id="symptom_description" class="form-control"
                                    rows="3" required></textarea>
                            </div>

                            <!-- ระดับความเจ็บปวด -->
                            <div class="mb-3">
                                <label for="pain_score" class="form-label">ระดับความเจ็บปวด (1-10)</label>
                                <input type="number" name="pain_score" id="pain_score" class="form-control" min="1"
                                    max="10" required>
                            </div>

                            <!-- อุณหภูมิร่างกาย -->
                            <div class="mb-3">
                                <label for="temperature" class="form-label">อุณหภูมิร่างกาย (°C)</label>
                                <input type="number" name="temperature" id="temperature" class="form-control" step="0.1"
                                    min="30" max="45" required>
                            </div>

                            <!-- ความดันโลหิต -->
                            <div class="mb-3">
                                <label for="blood_pressure" class="form-label">ความดันโลหิต (SYS/DIA)</label>
                                <input type="text" name="blood_pressure" id="blood_pressure" class="form-control"
                                    pattern="\d{2,3}/\d{2,3}" required>
                            </div>

                            <!-- อัตราการเต้นของหัวใจ -->
                            <div class="mb-3">
                                <label for="heart_rate" class="form-label">อัตราการเต้นของหัวใจ (bpm)</label>
                                <input type="number" name="heart_rate" id="heart_rate" class="form-control" min="40"
                                    max="180" required>
                            </div>

                            <!-- ระดับความเสี่ยง -->
                            <!-- ฟิลด์แสดงผลระดับความเสี่ยง -->
                            <div class="mb-3">
                                <label for="risk_level_display" class="form-label">ระดับความเสี่ยง</label>
                                <input type="text" id="risk_level_display" class="form-control" readonly>
                            </div>
                            <!-- ฟิลด์ hidden สำหรับ risk_level -->
                            <input type="hidden" id="risk_level" name="risk_level" value="">

                            <!-- ตั้งค่าสถานะเป็น "in ER" โดยไม่สามารถแก้ไขได้ -->
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ</label>
                                <input type="text" name="status" id="status" value="in ER" class="form-control"
                                    readonly>
                            </div>

                            <!-- ปุ่มบันทึก -->
                            <div class="d-flex">
                                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </div><!-- /.container-fluid -->
    </div>

    @include('themes.script')

    <script>
        $(document).ready(function () {
            // เมื่อกรอกเลขบัตรประชาชนทหาร
            $('#soldier_id_card').on('input', function () {
                let soldierIdCard = $(this).val();
                if (soldierIdCard.length >= 13) {  // ตรวจสอบว่าเลขบัตรประชาชนครบ 13 หลัก
                    $.ajax({
                        url: "{{ route('soldier.getByIdCard') }}",  // ตรวจสอบ URL
                        method: "GET",
                        data: { id_card: soldierIdCard },
                        success: function (data) {
                            if (data.success) {
                                $('#soldier_name').val(data.soldier.first_name + ' ' + data.soldier.last_name);
                                $('#soldier-info').show();
                            } else {
                                $('#soldier-info').hide();
                                alert('ไม่พบข้อมูลทหาร');
                            }
                        }
                    });
                }
            });

            // เมื่อฟอร์มถูกส่ง (submit)
            $('#erForm').submit(function (event) {
                event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ

                // สร้าง FormData จากฟอร์ม
                let formData = new FormData(this);

                // ส่งข้อมูลไปยัง server
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // เมื่อบันทึกข้อมูลสำเร็จ แสดง popup แจ้งเตือน
                        if (response.success) {
                            Swal.fire({
                                title: 'บันทึกข้อมูลสำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'ตกลง'
                            }).then(() => {
                                window.location.href = response.redirect; // เปลี่ยนเส้นทางหลังจากแสดง popup
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                });
            });
        });
        // คำนวณระดับความเสี่ยงเมื่อกรอกข้อมูลในฟอร์ม
        $(document).ready(function () {
            $('#temperature, #blood_pressure, #heart_rate').on('input', function () {
                let temperature = parseFloat($('#temperature').val());
                let bp = $('#blood_pressure').val().split('/');
                let systolic = parseInt(bp[0]) || 0;
                let diastolic = parseInt(bp[1]) || 0;
                let heartRate = parseInt($('#heart_rate').val());

                let riskLevel = '';

                // คำนวณระดับความเสี่ยง
                if (temperature > 40) {
                    riskLevel = 'red';  // ค่าที่ตรงกับ enum
                } else if (temperature > 38) {
                    riskLevel = 'yellow';  // ค่าที่ตรงกับ enum
                } else {
                    if (systolic >= 180 || diastolic >= 120) {
                        riskLevel = 'red';  // ค่าที่ตรงกับ enum
                    } else if (systolic >= 140 || diastolic >= 90) {
                        riskLevel = 'red';  // ค่าที่ตรงกับ enum
                    } else if (systolic >= 121 || diastolic >= 81) {
                        riskLevel = 'yellow';  // ค่าที่ตรงกับ enum
                    } else if (systolic < 90 || diastolic < 60) {
                        riskLevel = 'yellow';  // ค่าที่ตรงกับ enum
                    } else {
                        riskLevel = 'green';  // ค่าที่ตรงกับ enum
                    }
                }

                // ตั้งค่าระดับความเสี่ยงในฟอร์ม
                $('#risk_level').val(riskLevel);  // ส่งค่าผ่าน hidden input

                // แสดงระดับความเสี่ยงในฟอร์ม
                $('#risk_level_display').val(riskLevel);  // แสดงค่าที่คำนวณใน input ที่แสดงผล
            });
        });

    </script>
</body>

</html>







@include('themes.script')