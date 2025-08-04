<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติรายบุคคล | {{ $soldier->first_name ?? 'N/A' }} {{ $soldier->last_name ?? '' }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Icons & Framework --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Modern Hospital Theme Styles --}}
    <style>
        :root {
            --bs-primary: #0a4d68;
            --font-family-sans-serif: 'IBM Plex Sans Thai', sans-serif;
            --background-color: #f0f2f5;
            --card-bg: #ffffff;
            --card-border-color: #e9ecef;
            --text-color: #343a40;
            --text-muted: #6c757d;
        }

        body { background-color: var(--background-color); font-family: var(--font-family-sans-serif); color: var(--text-color); }
        .content-wrapper { padding: 1.5rem; }
        .card { border: 1px solid var(--card-border-color); border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; background-color: var(--card-bg); }
        .card-header { background-color: transparent; border-bottom: 1px solid var(--card-border-color); padding: 1rem 1.25rem; font-weight: 600; }
        .btn { border-radius: 0.5rem; font-weight: 500; }
        .profile-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .profile-info dt { font-weight: 600; color: var(--text-color); }
        .profile-info dd { color: var(--text-muted); margin-left: 0; }

        .timeline-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bs-light);
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <div class="container-fluid">
        {{-- Profile Card --}}
        <div class="card">
            <div class="card-header profile-card-header">
                <h5 class="mb-0"><i class="fas fa-user-shield me-2 text-primary"></i>ข้อมูลประวัติผู้ป่วย</h5>
                <div class="btn-group">
                    <a href="{{ route('mental-health.history.download', $soldier->id) }}" class="btn btn-primary"><i class="fas fa-print me-2"></i>ดาวน์โหลด PDF</a>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>กลับ</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row profile-info">
                    <div class="col-md-4 col-lg-3">
                        <dt>ชื่อ-นามสกุล</dt>
                        <dd class="fs-5">{{ $soldier->first_name ?? 'N/A' }} {{ $soldier->last_name ?? '' }}</dd>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <dt>เลขประจำตัวประชาชน</dt>
                        <dd class="fs-5">{{ $soldier->soldier_id_card ?? 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <dt>ผลัด</dt>
                        <dd class="fs-5">{{ $soldier->rotation->rotation_name ?? 'N/A' }}</dd>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <dt>หน่วยฝึก</dt>
                        <dd class="fs-5">{{ $soldier->trainingUnit->unit_name ?? 'N/A' }}</dd>
                    </div>
                </div>
            </div>
        </div>

        {{-- History Timeline --}}
        <h4 class="mb-3">ไทม์ไลน์การรักษา</h4>
        @forelse ($history as $item)
            <div class="card timeline-card">
                <div class="card-header">
                    <span class="fw-bold"><i class="fas fa-calendar-alt me-2"></i>ปิดเคสเมื่อ: {{ \Carbon\Carbon::parse($item->updated_at)->thaidate('j F Y, H:i น.') }}</span>
                    @if($item->risk_type == 'at_risk')
                        <span class="badge bg-warning text-dark">จากผลประเมิน</span>
                    @else
                        <span class="badge bg-info text-dark">มีประวัติเดิม</span>
                    @endif
                </div>
                <div class="card-body">
                    @php
                        $appointment = $item->appointments->first();
                        $treatment = $appointment ? $appointment->treatment : null;
                    @endphp

                    @if($appointment)
                        <div class="row">
                            <div class="col-lg-6">
                                <h6><i class="fas fa-calendar-check text-info me-2"></i>ข้อมูลการนัดหมาย</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">วันที่นัด:</dt>
                                    <dd class="col-sm-8">{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->thaidate('j F Y') : '-' }}</dd>
                                    <dt class="col-sm-4">เวลา:</dt>
                                    <dd class="col-sm-8">{{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') . ' น.' : '-' }}</dd>
                                    <dt class="col-sm-4">โรงพยาบาล:</dt>
                                    <dd class="col-sm-8">{{ $appointment->appointment_location ?? '-' }}</dd>
                                </dl>
                            </div>
                            @if($treatment)
                            <div class="col-lg-6">
                                <h6><i class="fas fa-file-medical text-success me-2"></i>ข้อมูลการรักษา</h6>
                                <dl class="row">
                                    <dt class="col-sm-4">แพทย์ผู้รักษา:</dt>
                                    <dd class="col-sm-8">{{ $treatment->doctor_name ?? '-' }}</dd>
                                    <dt class="col-sm-4">ยาที่ได้รับ:</dt>
                                    <dd class="col-sm-8">{{ $treatment->medicine_name ?? 'ไม่มี' }}</dd>
                                    <dt class="col-sm-4">หมายเหตุ:</dt>
                                    <dd class="col-sm-8">{{ $treatment->notes ?? 'ไม่มี' }}</dd>
                                </dl>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">ไม่พบข้อมูลการนัดหมายและการรักษาสำหรับเคสนี้</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fs-1 text-muted mb-3"></i>
                    <h4>ไม่พบประวัติการรักษาที่เสร็จสิ้น</h4>
                </div>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if ($history->hasPages())
        <div class="d-flex justify-content-center">
            {{ $history->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
        @endif

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
