<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<style>
    /* ✅ ปรับแต่งตาราง */
    .container-box {
        background: white;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #f8f9fa;
    }

    .table {
        font-size: 1rem;
        width: 100%;
        text-align: center;
    }

    .badge {
        font-size: 0.9rem;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .checked-in {
        background-color: green;
        color: white;
    }

    .not-checked-in {
        background-color: red;
        color: white;
    }

    .treated {
        background-color: blue;
        color: white;
    }

    .not-treated {
        background-color: orange;
        color: white;
    }

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

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        /* ลดระยะห่างระหว่างจุดกับข้อความ */
        padding: 4px 8px;
        /* ลดขนาดขอบของป้าย */
        border: 1px solid #ddd;
        /* ลดความหนาของเส้นขอบ */
        border-radius: 5px;
        /* ลดความโค้งของมุม */
        background-color: #fff;
        /* พื้นหลังสีขาว */
        box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.1);
        /* ลดเงาลงให้ดูเล็กลง */
        font-size: 12px;
        /* ลดขนาดตัวอักษร */
    }

    .status-badge i {
        font-size: 14px;
        /* ลดขนาดจุด */
    }

    .btn-light.shadow {
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        /* ปรับระดับเงาตามต้องการ */
    }

    .badge.bg-light.shadow {
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        /* ปรับระดับเงาตามต้องการ */
    }
</style>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Title: ตารางการรักษาทหาร -->
                                <h2 class="fw-bold">ตารางการรักษา</h2>
                                <!-- Title: วันที่ -->
                                <h5 class="text-muted">วันที่ <span class="fw-bold">{{ now()->format('d/m/Y') }}</span>
                                </h5>
                            </div>
                            <div class="container-box p-3 mt-3">
                                <table id="checkinTable" class="table table-striped table-bordered data-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>เลขบัตรประชาชน</th>
                                            <th>ชื่อทหาร</th>
                                            <th>อาการ</th>
                                            <th>วัน-เวลา & สถานที่นัดหมาย</th>
                                            <th>สถานะการรักษา</th>
                                            <th>วินิฉัยโรค</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($appointments as $appointment)
                                            <tr>
                                                <td>{{ $appointment->medicalReport->soldier->soldier_id_card ?? '-' }}</td>
                                                <td>{{ $appointment->medicalReport->soldier->first_name ?? '-' }}
                                                    {{ $appointment->medicalReport->soldier->last_name ?? '-' }}
                                                </td>
                                                <td>
                                                    @if (isset($report))
                                                        <button class="btn btn-info btn-sm btn-detail"
                                                            data-id="{{ $report->id }}">เพิ่มเติม</button>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm" disabled>ไม่มีข้อมูล</button>
                                                    @endif

                                                </td>
                                                <td><strong>วัน :</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Y-m-d') }}<br>
                                                    <strong>เวลา :</strong>
                                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('H:i:s') }}
                                                    น.<br>

                                                    <strong>สถานที่ :</strong> {{ $appointment->appointment_location }}
                                                </td>
                                                <td>
                                                    @if (!is_null($appointment->checkin) && $appointment->checkin->checkin_status === 'checked-in')
                                                        <span
                                                            class="badge checked-in shadow bg-light text-dark">🟢เข้ารับการรักษาแล้ว</span>
                                                    @else
                                                        <span class="badge not-checked-in shadow bg-light text-dark">
                                                            🟠ยังไม่ได้เข้ารับการรักษา</span>
                                                    @endif

                                                </td>
                                                <td>
                                                    @if ($appointment->checkin && $appointment->checkin->treatment)
                                                        @if ($appointment->checkin->treatment->treatment_status === 'treated')
                                                            <span
                                                                class="badge treated shadow bg-light text-dark">🟢เข้ารับการรักษาแล้ว</span>
                                                        @else
                                                            <a href="{{ route('diagnosis.form', ['treatmentId' => $appointment->checkin->treatment->id]) }}"
                                                                class="btn btn-primary">
                                                                กรอกข้อมูลวินิจฉัย
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">⏳ ไม่พบข้อมูล</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-user-md"></i> รายละเอียดผู้ป่วย
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        <h5 class="mt-4">ผลตรวจ ATK</h5>
                        <div id="atkImages" class="row row-cols-2 row-cols-md-3 g-1"></div>

                        <h5 class="mt-4">รูปอาการ</h5>
                        <div id="symptomImages" class="row row-cols-2 row-cols-md-3 g-1"></div>



                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    @include('themes.script')

</body>

</html>