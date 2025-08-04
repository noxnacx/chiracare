<!DOCTYPE html>
<html lang="th">

<head>
    @include('themes.head')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ✅ ดึง CSRF Token -->
    <style>
        /* ✅ CSS ที่ขยายขนาดกล่อง */
        .center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content-box {
            width: 80%;
            max-width: 900px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .form-control,
        .btn {
            width: 100%;
            font-size: 1.2rem;
            padding: 12px;
        }

        .table {
            font-size: 1.1rem;
        }
    </style>
</head>

<body class="hold-transition layout-fixed">
    <div class="center-container">
        <div class="content-box">
            <h2 class="mb-4 text-center text-success">
                เช็คอินทหารด้วยบัตรประชาชน
            </h2>

            <!-- ✅ ฟอร์มเช็คอินด้วยบัตรประชาชน -->
            <div class="mb-4">
                <label for="id_card" class="fw-bold">
                    ใส่หมายเลขบัตรประชาชน:
                </label>
                <input type="text" id="id_card" class="form-control border-primary rounded-3 shadow-sm"
                    placeholder="กรอกหมายเลขบัตรประชาชน" maxlength="13">
                <button id="checkInBtn" class="btn btn-success mt-3" data-route="{{ route('checkin.idcard') }}">
                    yเช็คอิน
                </button>
            </div>


        </div>
    </div>

    <script>
        document.getElementById('checkInBtn').addEventListener('click', async function () {
            let idCard = document.getElementById('id_card').value.trim();
            let routeUrl = this.getAttribute("data-route"); // ✅ ดึง route URL
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content"); // ✅ ดึง CSRF Token

            if (idCard.length !== 13 || isNaN(idCard)) {
                Swal.fire({
                    title: "❌ ข้อมูลไม่ถูกต้อง",
                    text: "กรุณากรอกหมายเลขบัตรประชาชนให้ครบ 13 หลัก",
                    icon: "warning",
                    confirmButtonText: "ตกลง"
                });
                return;
            }

            try {
                let response = await fetch(routeUrl, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ soldier_id_card: idCard })
                });

                let data = await response.json();

                if (response.ok) {
                    Swal.fire({
                        title: "✅ เช็คอินสำเร็จ",
                        text: data.message,
                        icon: "success",
                        confirmButtonText: "ตกลง"
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: "❌ ไม่สามารถเช็คอินได้",
                        text: data.message,
                        icon: "error",
                        confirmButtonText: "ตกลง"
                    });
                }

            } catch (error) {
                Swal.fire({
                    title: "❌ ข้อผิดพลาดเซิร์ฟเวอร์",
                    text: "กรุณาลองใหม่ หรือแจ้งผู้ดูแลระบบ",
                    icon: "error",
                    confirmButtonText: "ตกลง"
                });
            }
        });
    </script>
    <script>
        async function updateTreatmentStatus(checkinId) {
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            Swal.fire({
                title: "ยืนยันการรักษา?",
                text: "ต้องการเปลี่ยนสถานะเป็น 'รักษาแล้ว' หรือไม่?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "✅ ใช่",
                cancelButtonText: "❌ ยกเลิก"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        let response = await fetch("/api/treatment/update-status", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ checkin_id: checkinId })
                        });

                        let data = await response.json();

                        if (response.ok) {
                            Swal.fire("สำเร็จ!", "สถานะการรักษาถูกอัปเดตแล้ว", "success")
                                .then(() => location.reload());
                        } else {
                            Swal.fire("เกิดข้อผิดพลาด", data.message, "error");
                        }

                    } catch (error) {
                        Swal.fire("❌ ข้อผิดพลาดเซิร์ฟเวอร์", "กรุณาลองใหม่ หรือแจ้งผู้ดูแลระบบ", "error");
                    }
                }
            });
        }
    </script>


    @include('themes.script')

</body>

</html>