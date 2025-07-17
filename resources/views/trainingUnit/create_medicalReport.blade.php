<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- Main Sidebar Container -->
        @include('themes.training.menutraining')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="card shadow-lg p-4">
                            <h4 class="mb-4 fw-bold text-succes">ส่งป่วยประจำวัน</h4>
                            <div class="d-flex justify-content-between">
                                <p style="font-size: 20px; font-weight: bold;">
                                    <label>หน่วยฝึก:</label>
                                    <span class="text-success"
                                        style="font-size: 20px; font-weight: 600;">{{ $unit->unit_name }}</span>
                                </p>
                                <p class="text-muted" style="font-size: 18px;">
                                    ณ วันที่ {{ \Carbon\Carbon::now()->setTimezone('Asia/Bangkok')->format('d/m/Y') }}
                                </p>




                            </div>


                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <form id="medicalForm" action="{{ route('medical.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf



                                <!-- 🔹 เลือกทหาร -->
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">เลือกทหาร</label>
                                        <select id="soldier_select" name="soldier_id" class="form-select" required>
                                            <option value="">-- เลือกทหาร --</option>
                                            @foreach ($soldiers as $soldier)
                                                <option value="{{ $soldier->id }}">{{ $soldier->first_name }}
                                                    {{ $soldier->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">คำอธิบายอาการ</label>
                                        <textarea name="symptom_description" class="form-control" rows="3"
                                            required></textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">ระดับความเจ็บปวด (0-10)</label>
                                        <input type="number" name="pain_score" class="form-control" min="0" max="10">
                                    </div>
                                </div>


                                <!-- 🔹 ค่าชีวิต -->
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">อุณหภูมิร่างกาย (°C)</label>
                                        <input type="number" name="temperature" class="form-control" step="0.1" min="30"
                                            max="45">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ความดันโลหิต (SYS/DIA)</label>
                                        <input type="text" name="blood_pressure" class="form-control"
                                            pattern="\d{2,3}/\d{2,3}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">อัตราการเต้นของหัวใจ (bpm)</label>
                                        <input type="number" name="heart_rate" class="form-control" min="40" max="180">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label fw-bold">ระดับความเสี่ยง</label>
                                        <div class="position-relative">
                                            <span id="risk-icon" class="risk-icon"></span>
                                            <input type="text" id="risk_level"
                                                class="form-control text-center fw-bold ps-4" readonly>
                                        </div>
                                    </div>


                                </div>

                                <!-- 🔹 เพิ่มอัปโหลดรูปภาพ -->
                                <div class="row mt-4">

                                    <!-- อัปโหลดรูปผลตรวจ ATK (หลายรูป) -->
                                    <!-- ✅ อัปโหลดรูปผลตรวจ ATK (หลายรูป) -->
                                    <!-- อัปโหลดรูปผลตรวจ ATK (หลายรูป) -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">ผลตรวจ ATK:</label>
                                        <div class="custom-upload"
                                            onclick="document.getElementById('atk_test_results').click();">
                                            <!-- ✅ ใช้ name="atk_test_results[]" เพื่อส่งหลายรูป -->
                                            <input type="file" id="atk_test_results" name="atk_test_results[]"
                                                accept="image/*" multiple
                                                onchange="previewMultipleImages(event, 'atkPreviewContainer')">
                                            <div class="upload-content">
                                                <i class="bi bi-camera upload-icon"></i>
                                                <p class="text-muted">อัพโหลดรูปภาพ</p>
                                            </div>
                                            <!-- ✅ ใช้ container สำหรับพรีวิวรูป -->
                                            <div id="atkPreviewContainer" class="preview-container"></div>
                                        </div>
                                    </div>

                                    <!-- อัปโหลดรูปอาการ (หลายรูป) -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">รูปภาพอาการ:</label>
                                        <div class="custom-upload"
                                            onclick="document.getElementById('symptom_images').click();">
                                            <input type="file" id="symptom_images" name="symptom_images[]"
                                                accept="image/*" multiple
                                                onchange="previewMultipleImages(event, 'symptomPreviewContainer')">
                                            <div class="upload-content">
                                                <i class="bi bi-camera upload-icon"></i>
                                                <p class="text-muted">อัพโหลดรูปภาพ</p>
                                            </div>
                                            <div id="symptomPreviewContainer" class="preview-container"></div>
                                        </div>
                                    </div>



                                    <!-- 🔹 ปุ่มบันทึก ใช้ text-end -->
                                    <!-- ✅ ใช้ w-100 บังคับให้ div เต็มพื้นที่ -->
                                    <div class="d-flex justify-content-end w-100 mt-4">
                                        <button type="submit" class="btn  btn-success">บันทึกข้อมูล</button>
                                    </div>



                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </div>
    </div>

    @include('themes.script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ โหลด Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tempInput = document.querySelector('input[name="temperature"]');
            const bpInput = document.querySelector('input[name="blood_pressure"]');
            const riskLevelField = document.getElementById('risk_level');
            const riskIcon = document.getElementById('risk-icon'); // ⬅️ อ้างอิง span ไอคอนจุดสี


            function calculateRiskLevel() {
                let temperature = parseFloat(tempInput.value) || 0;
                let bpValue = bpInput.value;

                // ✅ ตรวจสอบว่าค่าความดันถูกต้องก่อน
                let bpParts = bpValue.split("/");
                if (bpParts.length !== 2) {
                    riskLevelField.value = "ข้อมูลไม่ถูกต้อง";
                    riskLevelField.className = "form-control text-center fw-bold text-dark";
                    return;
                }

                let systolic = parseInt(bpParts[0]) || 0;
                let diastolic = parseInt(bpParts[1]) || 0;

                let riskLevel;

                // ✅ ตรวจสอบอุณหภูมิร่างกายก่อน
                if (temperature > 40) {
                    riskLevel = 'red'; // 🔴 ฉุกเฉิน ไข้สูงเกิน 40°C
                } else if (temperature > 38) {
                    riskLevel = 'yellow'; // 🟡 เฝ้าระวัง ไข้สูง
                } else {
                    // ✅ ตรวจสอบค่าความดันโลหิต
                    if (systolic >= 180 || diastolic >= 120) {
                        riskLevel = 'red'; // 🔴 ฉุกเฉิน Hypertensive Crisis
                    } else if (systolic >= 140 || diastolic >= 90) {
                        riskLevel = 'red'; // 🔴 อันตราย Hypertension Stage 2
                    } else if (systolic >= 121 || diastolic >= 81) {
                        riskLevel = 'yellow'; // 🟡 เฝ้าระวัง Hypertension Stage 1
                    } else if (systolic < 90 || diastolic < 60) {
                        riskLevel = 'yellow'; // 🟡 ความดันต่ำ
                    } else {
                        riskLevel = 'green'; // 🟢 ปกติ
                    }
                }

                // ✅ แสดงระดับความเสี่ยงในฟิลด์ พร้อมสี
                riskLevelField.value = riskLevel.toUpperCase();

                if (riskLevel === 'red') {
                    riskLevelField.value = "ฉุกเฉิน"; // แสดงข้อความ
                    riskIcon.innerHTML = "🔴"; // แสดงจุดสีแดง
                } else if (riskLevel === 'yellow') {
                    riskLevelField.value = "เฝ้าระวัง"; // แสดงข้อความ
                    riskIcon.innerHTML = "🟡"; // แสดงจุดสีเหลือง
                } else {
                    riskLevelField.value = "ปกติ"; // แสดงข้อความ
                    riskIcon.innerHTML = "🟢"; // แสดงจุดสีเขียว
                }

            }

            // ✅ ตรวจจับการเปลี่ยนแปลงค่าของ input และคำนวณความเสี่ยง
            tempInput.addEventListener('input', calculateRiskLevel);
            bpInput.addEventListener('input', calculateRiskLevel);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("Script loaded"); // ตรวจสอบว่าถูกโหลดจริงหรือไม่

            let atkFiles = []; // เก็บไฟล์ทั้งหมดที่เลือก
            let symptomFiles = []; // เก็บไฟล์ทั้งหมดที่เลือก

            function previewMultipleImages(event, containerId, fileList, inputId) {
                const container = document.getElementById(containerId);
                const files = event.target.files;

                // ✅ ถ้ามีไฟล์ใหม่ ให้เพิ่มไฟล์ใหม่เข้าไปในอาร์เรย์
                Array.from(files).forEach(file => fileList.push(file));

                // ✅ เคลียร์ภาพเก่าทั้งหมดก่อนแสดงใหม่
                container.innerHTML = '';

                // ✅ แสดงพรีวิวรูปทั้งหมดที่มีในอาร์เรย์
                fileList.forEach((file, index) => {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        let imgWrapper = document.createElement('div');
                        imgWrapper.classList.add('image-preview-wrapper');

                        let img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('preview-img');

                        let removeBtn = document.createElement('button');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.classList.add('remove-image-btn');
                        removeBtn.onclick = function () {
                            fileList.splice(index, 1); // ลบรูปออกจากอาร์เรย์
                            previewMultipleImages({ target: { files: [] } }, containerId, fileList, inputId);
                        };

                        imgWrapper.appendChild(img);
                        imgWrapper.appendChild(removeBtn);
                        container.appendChild(imgWrapper);
                    };
                    reader.readAsDataURL(file);
                });

                // ✅ อัปเดต input file ใหม่
                let input = document.getElementById(inputId);
                let dataTransfer = new DataTransfer();
                fileList.forEach(file => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
            }

            // ✅ เชื่อมโยงกับ input
            document.getElementById('atk_test_results').addEventListener('change', (event) => {
                previewMultipleImages(event, 'atkPreviewContainer', atkFiles, 'atk_test_results');
            });

            document.getElementById('symptom_images').addEventListener('change', (event) => {
                previewMultipleImages(event, 'symptomPreviewContainer', symptomFiles, 'symptom_images');
            });

            // ✅ ส่งข้อมูลไปยัง Laravel
            document.querySelector("form").addEventListener("submit", function (event) {
                event.preventDefault();

                let formData = new FormData(this);

                // ✅ ลบไฟล์เก่าก่อนเพิ่มใหม่
                formData.delete("atk_test_results[]");
                formData.delete("symptom_images[]");

                // ✅ เพิ่มไฟล์ที่ผู้ใช้เลือกทั้งหมด
                atkFiles.forEach(file => formData.append("atk_test_results[]", file));
                symptomFiles.forEach(file => formData.append("symptom_images[]", file));

                // ✅ ส่งข้อมูลไปยัง Laravel

            });

        });





    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("medicalForm").addEventListener("submit", function (event) {
                event.preventDefault(); // ป้องกันการส่งฟอร์มอัตโนมัติ

                // ดึงชื่อทหารจาก dropdown
                let soldierSelect = document.getElementById("soldier_select");
                let soldierName = soldierSelect.options[soldierSelect.selectedIndex].text.trim();

                if (!soldierSelect.value) {
                    Swal.fire("แจ้งเตือน", "กรุณาเลือกทหารก่อนส่งข้อมูล!", "warning");
                    return;
                }

                Swal.fire({
                    title: "ยืนยันต้องการส่งป่วยของ",
                    html: `<span style="color: green; font-size: 23px; font-weight: normal;">พลฯ</span>
           <span style="font-size: 23px; font-weight: normal;">${soldierName} ?</span>`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "ตกลง",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#dc3545"
                })
                    .then((result) => {
                        if (result.isConfirmed) {
                            let formData = new FormData(document.getElementById("medicalForm"));

                            fetch("{{ route('medical.store') }}", {
                                method: "POST",
                                body: formData
                            })
                                .then(response => {
                                    return response.headers.get("content-type")?.includes("application/json")
                                        ? response.json()
                                        : Promise.reject("Server response is not JSON");
                                })
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: `ส่งป่วยของ <span style="color: green; font-weight: bold;">พลฯ</span> ${soldierName} <span style="color: green; font-weight: bold;">สำเร็จ!</span>`,
                                            icon: "success",
                                            showConfirmButton: true,
                                            confirmButtonText: "ตกลง",
                                            confirmButtonColor: "#28a745",
                                            customClass: {
                                                title: 'small-bold-title' // ✅ ใช้คลาสที่กำหนดเอง
                                            }
                                        })
                                            .then(() => {
                                                console.log("Redirecting to:", data.redirect);
                                                window.location.href = data.redirect; // ✅ ไปหน้า wait_appointment
                                            });
                                    } else {
                                        throw new Error("Response does not contain success flag");
                                    }
                                })
                                .catch(error => {
                                    console.error("Error:", error);
                                    Swal.fire("เกิดข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้", "error");
                                });
                        }
                    });
            });
        });
    </script>


    <script>
        $(document).ready(function () {
            $('#soldier_select').select2({
                placeholder: "-- เลือกทหาร --",
                allowClear: true,
                width: '100%' // ทำให้เต็มความกว้าง
            });
        });
    </script>

    <style>
        .preview-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .image-preview-wrapper {
            position: relative;
            display: inline-block;
        }

        .preview-img {
            width: 120px;
            /* 🔹 ปรับขนาดความกว้างให้เท่ากัน */
            height: 120px;
            /* 🔹 ปรับขนาดความสูงให้เท่ากัน */
            border-radius: 5px;
            border: 1px solid #ccc;
            object-fit: cover;
            /* 🔹 ทำให้รูปไม่บีบเสียสัดส่วน */
        }

        .remove-image-btn {
            position: absolute;
            top: -4px;
            /* ✅ ปรับให้อยู่มุมบนขวา */
            right: -4px;
            background: red;
            color: white;
            border: none;
            width: 24px;
            /* ✅ ขยายปุ่มให้พอดี */
            height: 24px;
            border-radius: 50%;
            font-size: 16px;
            /* ✅ ปรับขนาด X ให้เหมาะสม */
            cursor: pointer;

            /* ✅ จัดกึ่งกลาง X ในปุ่ม */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            line-height: 0;
            padding: 0;
        }



        .custom-upload {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            position: relative;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        .custom-upload:hover {
            background-color: #eef1f4;
        }

        .custom-upload input {
            display: none;
        }

        .upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .upload-icon {
            font-size: 40px;
            color: #6c757d;
        }

        .risk-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
        }

        .small-bold-title {
            font-size: 20px !important;
            /* ✅ ปรับขนาดตัวอักษรให้เล็กลง */
            /* ✅ ทำให้ตัวอักษรหนา */
        }
    </style>
</body>

</html>