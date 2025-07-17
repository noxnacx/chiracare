<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin.navbaradmin')
        @include('themes.admin.menuadmin')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <h2 class="mb-4">รอนัดหมายจากโรงพยาบาล</h2>
                        <form id="appointmentForm">

                            <table class="table table-bordered">
                                <thead class="table-dark">

                                    <tr>
                                        <th><input type="checkbox" id="select_all"></th>
                                        <th>ชื่อ</th>
                                        <th>หน่วยฝึกต้นสังกัด</th>
                                        <th>ผลัด</th>
                                        <th>อาก</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicalReports as $report)
                                        <tr>
                                            <td><input type="checkbox" class="select_item" name="medical_report_ids[]"
                                                    value="{{ $report->id }}"></td>
                                            <td>{{ $report->soldier->first_name ?? '-' }}
                                                {{ $report->soldier->last_name ?? '-' }}
                                            </td>
                                            <td>{{ $report->soldier->affiliated_unit ?? '-' }}</td>
                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm btn-detail"
                                                    data-name="{{ $report->soldier->first_name }} {{ $report->soldier->last_name }}"
                                                    data-unit="{{ $report->soldier->affiliated_unit }}"
                                                    data-rotation="{{ $report->soldier->rotation->rotation_name ?? '-' }}"
                                                    data-training="{{ $report->soldier->training_unit ?? '-' }}"
                                                    data-symptom="{{ $report->symptom_description ?? 'ไม่มีข้อมูล' }}"
                                                    data-temp="{{ $report->vitalSign->temperature ?? '-' }}"
                                                    data-bp="{{ $report->vitalSign->blood_pressure ?? '-' }}"
                                                    data-heart-rate="{{ $report->vitalSign->heart_rate ?? '-' }}"
                                                    data-pain="{{ $report->pain_score ?? 'ไม่มีข้อมูล' }}"
                                                    data-symptom-image="{{ $report->symptom_image ? asset('storage/' . $report->symptom_image) : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal">
                                                    เพิ่มเติม
                                                </button>
                                            </td>
                                            <td><span class="badge bg-warning">ยังไม่ได้นัดหมาย</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mb-3">
                                <label for="appointment_date" class="form-label">วันที่นัด</label>
                                <input type="datetime-local" id="appointment_date" name="appointment_date"
                                    class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="appointment_location" class="form-label">สถานที่</label>
                                <select id="appointment_location" name="appointment_location" class="form-control"
                                    required>
                                    <option value="OPD">OPD</option>
                                    <option value="ER">ER</option>
                                    <option value="IPD">IPD</option>
                                    <option value="ARI clinic">ARI clinic</option>
                                    <option value="กองพันทหารราบ">กองพันทหารราบ</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="case_type" class="form-label">ประเภทเคส</label>
                                <select id="case_type" name="case_type" class="form-control" required>
                                    <option value="normal">ปกติ</option>
                                    <option value="critical">ฉุกเฉิน</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success">นัดหมาย</button>
                        </form>
                    </div>



                    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content shadow-lg border-0">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold">
                                        <i class="fas fa-user-md"></i> รายละเอียดผู้ป่วย
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="container">
                                        <h3><strong>พลฯ</strong> <span id="soldierName"></span></h3>
                                        <p><strong>หน่วยต้นสังกัด:</strong> <span id="soldierUnit"></span> |
                                            <strong>ผลัด:</strong> <span id="soldierRotation"></span> |
                                            <strong>หน่วยฝึก:</strong> <span id="soldierTraining"></span>
                                        </p>

                                        <!-- ✅ เพิ่มกรอบรอบ Vital Signs -->
                                        <!-- Modal แสดงรายละเอียดทหาร -->
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="info-box">
                                                    <small>อุณหภูมิ</small>
                                                    <h5 id="soldierTemp">31.0°C</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box">
                                                    <small>ความดันโลหิต</small>
                                                    <h5 id="soldierBP">90/120 mmHg</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box">
                                                    <small>อัตราการเต้นของหัวใจ</small>
                                                    <h5 id="soldierHeartRate">140 BPM</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-box">
                                                    <small>ระดับความเจ็บปวด</small>
                                                    <h5 id="soldierPain">1/10</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- อาการ -->
                                        <h5 class="mt-4">อาการ</h5>
                                        <p id="soldierSymptom"></p>

                                        <!-- รูปอาการ -->
                                        <h5 class="mt-4">รูปอาการ</h5>
                                        <img id="soldierImage" src="https://via.placeholder.com/400"
                                            class="img-fluid rounded border shadow-sm" alt="รูปอาการ">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                    <script>
                        document.getElementById('appointmentForm').addEventListener('submit', function (event) {
                            event.preventDefault();
                            const formData = new FormData(this);
                            fetch("{{ route('appointments.store') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                },
                                body: formData
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === "success") {
                                        alert("นัดหมายสำเร็จ");
                                        location.reload();
                                    } else {
                                        alert("เกิดข้อผิดพลาด");
                                    }
                                })
                                .catch(error => console.error("Error:", error));
                        });

                        document.querySelectorAll(".btn-detail").forEach(button => {
                            button.addEventListener("click", function () {
                                document.getElementById("soldierName").innerText = this.getAttribute("data-name");
                                document.getElementById("soldierUnit").innerText = this.getAttribute("data-unit");
                                document.getElementById("soldierRotation").innerText = this.getAttribute("data-rotation");
                                document.getElementById("soldierTraining").innerText = this.getAttribute("data-training");
                                document.getElementById("soldierSymptom").innerText = this.getAttribute("data-symptom");

                                document.getElementById("soldierTemp").innerText = this.getAttribute("data-temp") + "°C";
                                document.getElementById("soldierBP").innerText = this.getAttribute("data-bp") + " mmHg";
                                document.getElementById("soldierHeartRate").innerText = this.getAttribute("data-heart-rate") + " BPM";
                                document.getElementById("soldierPain").innerText = this.getAttribute("data-pain") + "/10";

                                let symptomImage = this.getAttribute("data-symptom-image");
                                document.getElementById("soldierImage").src = symptomImage ? symptomImage : "https://via.placeholder.com/400";
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</body>

</html>















<!DOCTYPE html>
<html lang="th">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin.navbaradmin')
        @include('themes.admin.menuadmin')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">

                    </div>



                </div>
            </div>
        </div>
    </div>
</body>

</html>