<!DOCTYPE html>
<html lang="en">
@include('themes.head')




</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.opd.navbaropd')

        @include('themes.opd.menuopd')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container">
                        <div class="container mt-4">


                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3 mb-4">
                                <!-- หัวข้อ -->
                                <h2 id="statusTitle" class="fw-bold mb-0">รายการที่นัดหมายสำเร็จ OPD</h2>

                                <!-- กล่องตัวเลือก + ปุ่มตัวกรอง -->
                                <div class="d-flex align-items-center">
                                    <!-- dropdown + label -->
                                    <div class="d-flex align-items-center gap-2" style="margin-right: 12px;">
                                        <label for="viewFilter"
                                            class="form-label text-muted mb-0 fw-semibold">สถานะ:</label>
                                        <select id="viewFilter" class="form-select form-select-sm"
                                            style="min-width: 200px; border-radius: 8px;">
                                            <option value="today">📅 นัดหมายวันนี้</option>
                                            <option value="all">📋 นัดหมายทั้งหมด (OPD)</option>
                                        </select>
                                    </div>

                                    <!-- ปุ่มตัวกรอง -->
                                    <button class="btn btn-info btn-sm px-3" data-bs-toggle="modal"
                                        data-bs-target="#filterModal" style="height: 32px; border-radius: 8px;">
                                        <i class="fas fa-filter me-1"></i> ตัวกรอง
                                    </button>
                                </div>



                            </div>




                            <div class="card shadow-sm today-section">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered data-table">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>ชื่อทหาร</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>ผลัด</th>
                                                    <th>อาการ</th>
                                                    <th>รายละเอียดนัดหมาย</th>
                                                    <th>สถานะการรักษา</th>

                                                </tr>
                                            </thead>


                                            <tbody>
                                                @forelse ($todayReports as $index => $report)
                                                                                            @php
                                                                                                $a = $report->appointment;
                                                                                                $c = $a->checkin ?? null;
                                                                                                $t = $c->treatment ?? null;
                                                                                                $status = 'ไม่ทราบสถานะ';

                                                                                                if (
                                                                                                    $a->status === 'scheduled' && optional($c)->checkin_status ===
                                                                                                    'not-checked-in'
                                                                                                ) {
                                                                                                    $status = '<span class="status-label">🟠 ยังไม่ได้ทำการรักษา</span>';
                                                                                                } elseif (
                                                                                                    $a->status === 'scheduled' && optional($c)->checkin_status ===
                                                                                                    'checked-in' && optional($t)->treatment_status === 'not-treated'
                                                                                                ) {
                                                                                                    $status = '<span class="status-label">🟡 อยู่ระหว่างการรักษา</span>';
                                                                                                } elseif (
                                                                                                    $a->status === 'completed' && optional($c)->checkin_status ===
                                                                                                    'checked-in' && optional($t)->treatment_status === 'treated'
                                                                                                ) {
                                                                                                    $status = '<span class="status-label">🟢 รักษาสำเร็จ</span>';
                                                                                                } elseif ($a->status === 'missed') {
                                                                                                    $status = '<span class="status-label">🔴 ไม่มาตามนัด</span>';
                                                                                                }
                                                                                            @endphp
                                                                                            <tr>
                                                                                                <td>{{ $report->soldier->first_name }} {{
                                                    $report->soldier->last_name }}</td>
                                                                                                <td>{{ $report->soldier->trainingUnit->unit_name ?? '-' }}</td>
                                                                                                <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                                                                                <td>
                                                                                                    <button
                                                                                                        class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                                                        style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                                                        {{ $report->symptom_description ?? '-' }}
                                                                                                    </button>
                                                                                                </td>

                                                                                                <td>
                                                                                                    <strong> วันที่:</strong>
                                                                                                    {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') ?? '-' }}<br>
                                                                                                    <strong> เวลา:</strong>
                                                                                                    {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('H:i') ?? '-' }}
                                                                                                    น.<br>
                                                                                                    <strong>สถานที่:</strong>
                                                                                                    {{ $report->appointment->appointment_location ?? '-' }}<br>
                                                                                                    <strong>ประเภท:</strong>
                                                                                                    {{ $report->appointment->case_type === 'critical' ? 'ฉุกเฉิน' : 'ปกติ' }}<br>
                                                                                                    <strong> หมายเหตุ:
                                                                                                    </strong>{{ $report->appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                                                                </td>

                                                                                                <td>{!! $status !!}</td>
                                                                                            </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted">
                                                            ไม่มีนัดหมายวันนี้
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-4 all-section" style="display: none;">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered data-table">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>ชื่อทหาร</th>
                                                    <th>หน่วยฝึก</th>
                                                    <th>ผลัด</th>
                                                    <th>อาการ</th>
                                                    <th>รายละเอียดนัดหมาย</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($opdReports as $index => $report)
                                                    @if(optional($report->appointment)->status === 'scheduled')
                                                        <tr>
                                                            <td>{{ $report->soldier->first_name }}
                                                                {{ $report->soldier->last_name }}</td>
                                                            <td>{{ $report->soldier->trainingUnit->unit_name ?? '-' }}</td>
                                                            <td>{{ $report->soldier->rotation->rotation_name ?? '-' }}</td>
                                                            <td>
                                                                <button
                                                                    class="btn btn-info btn-sm btn-detail text-truncate w-100"
                                                                    style="max-width: 130px;" data-id="{{ $report->id }}">
                                                                    {{ $report->symptom_description ?? '-' }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <strong>วันที่:</strong>
                                                                {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('d/m/Y') ?? '-' }}<br>
                                                                <strong>เวลา:</strong>
                                                                {{ \Carbon\Carbon::parse($report->appointment->appointment_date)->format('H:i') ?? '-' }}
                                                                น.<br>
                                                                <strong>สถานที่:</strong>
                                                                {{ $report->appointment->appointment_location ?? '-' }}<br>
                                                                <strong>ประเภท:</strong>
                                                                {{ $report->appointment->case_type === 'critical' ? 'ฉุกเฉิน' : 'ปกติ' }}<br>
                                                                <strong>หมายเหตุ:</strong>
                                                                {{ $report->appointment->is_follow_up ? 'นัดติดตามอาการ' : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            ไม่มีนัดหมายทั้งหมด
                                                        </td>
                                                    </tr>
                                                @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="GET" action="{{ route('opd.todayAppointments') }}">
                        <input type="hidden" name="status" value="{{ request('status', 'today') }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterModalLabel">ตัวกรอง</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body row g-3">
                            <div class="col-md-3">
                                <label class="form-label">ประเภทเคส</label>
                                <select name="case_type" class="form-select">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="normal" {{ request('case_type') == 'normal' ? 'selected' : '' }}>
                                        ปกติ
                                    </option>
                                    <option value="critical" {{ request('case_type') == 'critical' ? 'selected' : '' }}>
                                        ฉุกเฉิน</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">หมุนเวียน</label>
                                <select name="rotation_id" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($rotations as $rotation)
                                                                    <option value="{{ $rotation->id }}" {{ request('rotation_id') == $rotation->id ?
                                        'selected' : '' }}>
                                                                        {{ $rotation->rotation_name }}
                                                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">หน่วยฝึก</label>
                                <select name="training_unit_id" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    @foreach ($trainingUnits as $unit)
                                                                    <option value="{{ $unit->id }}" {{ request('training_unit_id') == $unit->id ?
                                        'selected' :
                                        '' }}>
                                                                        {{ $unit->unit_name }}
                                                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @if(request('status') == 'today' || !request('status'))
                                                    <div class="col-md-3">
                                                        <label class="form-label">สถานะในวันนี้</label>
                                                        <select name="today_status" class="form-select">
                                                            <option value="all">ทั้งหมด</option>
                                                            <option value="ยังไม่ได้ทำการรักษา" {{
                                request('today_status') == 'ยังไม่ได้ทำการรักษา' ? 'selected' : '' }}>
                                                                ยังไม่ได้ทำการรักษา</option>
                                                            <option value="อยู่ระหว่างการรักษา" {{
                                request('today_status') == 'อยู่ระหว่างการรักษา' ? 'selected' : '' }}>
                                                                อยู่ระหว่างการรักษา</option>
                                                            <option value="รักษาสำเร็จ" {{ request('today_status') == 'รักษาสำเร็จ' ? 'selected'
                                : '' }}>รักษาสำเร็จ</option>
                                                            <option value="ไม่มาตามนัด" {{ request('today_status') == 'ไม่มาตามนัด' ? 'selected'
                                : '' }}>ไม่มาตามนัด</option>
                                                        </select>
                                                    </div>
                            @endif
                            @if(request('status') == 'all')
                                <div class="col-md-3">
                                    <label class="form-label">วันที่นัดหมาย</label>
                                    <input type="date" name="appointment_date" class="form-control"
                                        value="{{ request('appointment_date') }}">
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            รายละเอียดผู้ป่วย
                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="container">
                            <h3><strong>พลฯ</strong> <span id="soldierName"></span></h3>
                            <p><strong>หน่วยต้นสังกัด:</strong> <span id="soldierUnit"></span> |
                                <strong>ผลัด:</strong> <span id="soldierRotation"></span> |
                                <strong>หน่วยฝึก:</strong> <span id="soldierTraining"></span>
                            </p>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อุณหภูมิ</small>
                                        <h5 id="soldierTemp">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ความดันโลหิต</small>
                                        <h5 id="soldierBP">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>อัตราการเต้นของหัวใจ</small>
                                        <h5 id="soldierHeartRate">-</h5>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <small>ระดับความเจ็บปวด</small>
                                        <h5 id="soldierPain">-</h5>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4">อาการ</h5>
                            <p id="soldierSymptom"></p>
                            <h5 class="mt-4">ระดับความเสี่ยง</h5>
                            <p id="soldierRiskLevel"></p>
                            <h5 class="mt-4">ผลตรวจ ATK</h5>
                            <div id="atkImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <h5 class="mt-4">รูปอาการ</h5>
                            <div id="symptomImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                            <!-- Add risk level display -->


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>

        @include('themes.script')

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>


            document.addEventListener('DOMContentLoaded', function () {
                const params = new URLSearchParams(window.location.search);
                const status = params.get('status') || 'today';
                document.getElementById('viewFilter').value = status;
                updateStatusUI(status);

                document.getElementById('viewFilter').addEventListener('change', function () {
                    const newStatus = this.value;
                    const searchParams = new URLSearchParams(window.location.search);
                    searchParams.set('status', newStatus);
                    window.location.href = window.location.pathname + '?' + searchParams.toString();
                });
            });

            // ✅ เหลือไว้แค่ตัวนี้เท่านั้น
            function updateStatusUI(status) {
                document.querySelector('.today-section').style.display = (status === 'today') ? 'block' : 'none';
                document.querySelector('.all-section').style.display = (status === 'all') ? 'block' : 'none';

                const title = document.getElementById('statusTitle');
                if (status === 'today') {
                    title.textContent = 'รายการนัดหมายวันนี้';
                } else if (status === 'all') {
                    title.textContent = 'รายการที่นัดหมายทั้งหมด (OPD)';
                }
            }

            $(document).on('click', '.btn-detail', function () {
                const reportId = $(this).data('id');

                if (!reportId) {
                    Swal.fire("ผิดพลาด", "ไม่พบรหัสรายงาน", "error");
                    return;
                }

                $.ajax({
                    url: `/medical/get-report/${reportId}`,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        if (!data.success) {
                            Swal.fire("ผิดพลาด", data.message, "error");
                            return;
                        }

                        $('#soldierName').text(data.soldier_name);
                        $('#soldierUnit').text(data.soldier_unit);
                        $('#soldierRotation').text(data.soldier_rotation);
                        $('#soldierTraining').text(data.soldier_training);
                        $('#soldierTemp').text(data.temperature + "°C");
                        $('#soldierBP').text(data.blood_pressure);
                        $('#soldierHeartRate').text(data.heart_rate + " BPM");
                        $('#soldierPain').text(data.pain_score + "/10");
                        $('#soldierSymptom').text(data.symptom_description);

                        let riskCode = data.risk_level;
                        if (riskCode === 'yellow') riskCode = 'warning';
                        else if (riskCode === 'red') riskCode = 'critical';
                        else if (riskCode === 'green') riskCode = 'normal';

                        const riskMap = {
                            critical: '🔴 ฉุกเฉิน',
                            warning: '🟡 เฝ้าระวัง',
                            normal: '🟢 ปกติ'
                        };
                        $('#soldierRiskLevel').text(riskMap[riskCode] || '-');

                        function loadImages(images, containerId) {
                            const container = $(`#${containerId}`);
                            container.empty();
                            if (!images || !images.length) {
                                container.html('<p class="text-muted">ไม่มีรูปภาพ</p>');
                                return;
                            }
                            images.forEach(img => {
                                container.append(`
                        <div class="col-md-4 mb-2">
                            <div class="image-wrapper">
                                <img src="${img}" class="img-fluid" alt="รูป">
                            </div>
                        </div>
                    `);
                            });
                        }

                        loadImages(data.images.atk, 'atkImages');
                        loadImages(data.images.symptom, 'symptomImages');

                        // ✅ เปิด modal
                        new bootstrap.Modal(document.getElementById('detailModal')).show();
                    },
                    error: () => Swal.fire("ผิดพลาด", "ไม่สามารถโหลดข้อมูลได้", "error")
                });
            });





        </script>


</body>

</html>

<style>
    .info-box {
        background-color: #fff;
        border: 2px solid #dee2e6;
        padding: 15px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        /* ✅ จัดเรียงเนื้อหาเป็นแนวตั้ง */
        align-items: center;
        justify-content: center;
        min-height: 100px;
        transition: 0.3s;
    }

    .info-box small {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }

    .info-box h5 {
        font-size: 22px;
        font-weight: 700;
        margin-top: 8px;
        /* ✅ เว้นระยะห่างจากข้อความด้านบน */
    }

    .info-box:hover {
        background-color: #f8f9fa;
    }

    .image-wrapper {
        width: 70%;
        aspect-ratio: 1/1;
        /* ทำให้รูปเป็นสี่เหลี่ยมจัตุรัส */
        overflow: hidden;
        border-radius: 8px;
        /* มุมโค้งมน */
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        /* สีพื้นหลัง */
        margin-bottom: 5px;
        /* ลดระยะห่างระหว่างรูป */
    }

    .image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* ป้องกันรูปบีบผิดสัดส่วน */
        border-radius: 8px;
        padding: 2px;
        /* ลด padding เพื่อให้รูปดูไม่ห่างกันมาก */
    }

    /* ✅ ป้ายสถานะ */
    .status-label {
        display: inline-flex;
        align-items: center;
        font-weight: bold;
        font-size: 12px;
        padding: 8px 14px;
        border-radius: 12px;
        border: 1px solid #ddd;
        background-color: white;
        /* เปลี่ยนจากสีเหลืองเป็นขาว */
        color: black;
        box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.15);
    }

    /* ✅ จุดสีหน้าข้อความ */
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
        display: inline-block;
    }

    /* ✅ จุดสีแดง */
    .dot-red {
        background-color: red;
    }

    /* ✅ จุดสีเหลือง */
    .dot-yellow {
        background-color: #FFC107;
    }

    /* ✅ ปรับขนาดปุ่มให้ดูดีขึ้น */
    #confirmAppointment {
        font-size: 16px;
        border-radius: 8px;
    }

    /* ✅ ปรับ input ให้ดูสวยงาม */
    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 5px;
    }

    /* ✅ ปรับขนาด Modal ให้สมส่วนขึ้น */
    .modal-md {
        max-width: 500px;
    }

    /* ✅ เพิ่มช่องว่างระหว่าง Input และ Dropdown */
    #appointment_date {
        margin-bottom: 15px;
    }

    /* Update the select box to make it appear with rounded corners and with a consistent layout */
    /* Container for both dropdowns to be in the same row */
    .row.g-3.align-items-center {
        display: flex;
        gap: 20px;
        /* Adjust the space between dropdowns */
        justify-content: space-between;
        /* This ensures they are spaced evenly */
    }

    /* Ensure both dropdowns take equal width */
    .form-select {
        width: 100%;
        padding: 10px;
        /* Make sure there's enough padding for each dropdown */
        border-radius: 10px;
        border: 1px solid #ccc;
        /* Border to make it consistent */
    }

    /* Optional: Add some space for better readability and focus */
    #appointment_date {
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ccc;
    }

    /* เพิ่มสไตล์สำหรับสถานะ Scheduled */
    .dot-green {
        background-color: #28a745;
    }

    .status-label.scheduled {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    /* ซ่อนปุ่มนัดหมายเมื่ออยู่ในโหมด Scheduled */
    #scheduleAppointment {
        display: block;
    }

    /* กำหนดสไตล์ให้กับ #soldierRiskLevel */
    #soldierRiskLevel {
        display: inline-block;
        /* ทำให้ p แสดงเป็นบล็อกในบรรทัดเดียว */
        padding: 8px 16px;
        /* เพิ่มพื้นที่ภายในรอบๆ ข้อความ */
        border: 2px solid #ccc;
        /* กรอบสีเทา */
        border-radius: 12px;
        /* มุมโค้งมน */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* เงาอ่อนๆ */
        font-weight: bold;
        /* ทำให้ข้อความหนา */
        font-size: 14px;
        /* ขนาดข้อความ */
        color: #333;
        /* สีข้อความ */
        background-color: #f9f9f9;
        /* สีพื้นหลัง */
    }

    /* สไตล์เพิ่มเติม */
    .filter-item {
        min-width: 150px;
    }

    .form-select-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        height: calc(1.5em + 0.5rem + 2px);
    }

    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
    }

    .input-group-text {
        padding: 0.35rem 0.5rem;
    }

    /* ระยะห่างระหว่างองค์ประกอบ */
    .gap-3>* {
        margin-right: 0.75rem;
    }

    .gap-3>*:last-child {
        margin-right: 0;
    }


    /* ลบ !important ออก */
</style>