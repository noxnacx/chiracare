<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติเคสสุขภาพจิต</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Source Sans Pro', sans-serif; }
        .container-fluid { padding: 2rem; }
        .card { border: none; }
        .timeline-card .card-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; font-weight: 600; }
    </style>
</head>
<body class="hold-transition">
<div class="wrapper">
    <div class="content-wrapper" style="margin-left: 0 !important;">
        <div class="container-fluid py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">ประวัติเคสสุขภาพจิต</h1>
                    <p class="mb-0 text-muted">ของ: พลฯ {{ $soldier->first_name }} {{ $soldier->last_name }}</p>
                </div>
            </div>
            {{-- ✅ เพิ่มปุ่มดาวน์โหลด PDF --}}
<div class="d-flex justify-content-end mb-3">
    {{-- ✅ บรรทัดที่แก้ไขแล้ว --}}
<a href="{{ route('mental-health.history.download', ['soldier_id' => $soldier->id]) }}" class="btn btn-success">
    <i class="fas fa-file-pdf me-1"></i> ดาวน์โหลดประวัติ (PDF)
</a>
</div>
            <div class="card shadow-sm">
                <div class="card-body">
                    {{-- ✅ 1. เพิ่ม Array สำหรับแปลภาษา --}}
                    @php
                        $typeNames = [
                            'depression' => 'ภาวะซึมเศร้า',
                            'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย',
                            'smoking' => 'การสูบบุหรี่',
                            'alcohol' => 'การดื่มสุรา',
                            'drug_use' => 'การใช้สารเสพติด',
                        ];
                    @endphp

                    @forelse ($history as $case)
                        <div class="card shadow-sm mb-4 timeline-card">
                           <div class="card-header d-flex justify-content-between">
                                <div><strong>เคสวันที่:</strong> {{ $case->created_at->format('d M Y') }} @if($case->risk_type == 'at_risk')<span class="badge bg-warning text-dark ms-2">จากผลประเมิน</span>@else<span class="badge bg-info text-dark ms-2">มีประวัติเดิม</span>@endif</div>
                                <span class="badge {{ $case->status == 'completed' ? 'bg-success' : 'bg-secondary' }}">{{ $case->status == 'completed' ? 'พบแพทย์สำเร็จ' : 'ปิดโดยระบบ' }}</span>
                            </div>
                            <div class="card-body">
                                @if($case->risk_type == 'at_risk' && $case->assessmentScore)
                                    <div class="alert alert-light">
                                        {{-- ✅ 2. แก้ไขการแสดงผลให้เป็นภาษาไทย --}}
                                        @php
                                            $typeKey = optional($case->assessmentScore)->assessment_type;
                                            $typeName = $typeNames[$typeKey] ?? ucfirst(str_replace('_', ' ', $typeKey));
                                        @endphp
                                        <strong>ผลประเมินที่เป็นสาเหตุ:</strong> {{ $typeName }} (คะแนน: {{ optional($case->assessmentScore)->total_score }}, ระดับ: {{ optional($case->assessmentScore)->risk_level }})
                                    </div>
                                @endif
                                @if($case->appointments->isNotEmpty())
                                    <h6 class="mb-3">ประวัติการนัดหมายและการรักษา:</h6>
                                    @foreach($case->appointments as $appointment)
                                        <div class="border-start border-3 border-primary ps-3 mb-3">
                                            <p class="mb-1"><strong>นัดหมายวันที่:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }} เวลา {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }} น.</p>
                                            <p class="mb-1"><strong>สถานที่:</strong> {{ $appointment->appointment_location }}</p>
                                            @if($appointment->treatment)
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <p class="mb-2 fw-bold">ผลการรักษา:</p>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-1"><strong>แพทย์ผู้รักษา:</strong> {{ $appointment->treatment->doctor_name }}</li>
                                                    <li class="mb-1"><strong>ยาที่รักษา:</strong> {{ $appointment->treatment->medicine_name ?? '-' }}</li>
                                                    <li><strong>ข้อมูลเพิ่มเติม:</strong> {{ $appointment->treatment->notes ?? '-' }}</li>
                                                </ul>
                                            </div>
                                            @else
                                            <div class="mt-3 p-2 bg-light-subtle rounded text-muted"><small>ยังไม่มีการบันทึกผลการรักษาสำหรับการนัดหมายนี้</small></div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">ไม่มีข้อมูลการนัดหมายสำหรับเคสนี้</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center card py-5">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-3x text-muted"></i>
                                <h4 class="mt-3">ไม่พบประวัติเคสที่เสร็จสิ้น</h4>
                                <p class="text-muted">ยังไม่มีประวัติการส่งป่วยพิเศษที่เสร็จสิ้นสำหรับทหารนายนี้</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="card-footer d-flex align-items-center">
                    <form method="GET" action="{{ route('mental-health.history', $soldier->id) }}" class="d-flex align-items-center" id="perPageForm-history">
                        <label for="per_page_history" class="me-2 text-nowrap">แสดง</label>
                        <select name="per_page" id="per_page_history" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="5" @if($perPage == 5) selected @endif>5</option>
                            <option value="10" @if($perPage == 10) selected @endif>10</option>
                            <option value="15" @if($perPage == 15) selected @endif>15</option>
                            <option value="20" @if($perPage == 20) selected @endif>20</option>
                            <option value="25" @if($perPage == 25) selected @endif>25</option>
                            <option value="50" @if($perPage == 50) selected @endif>50</option>
                        </select>
                        <span class="ms-2 text-nowrap">รายการ</span>
                    </form>

                    @if ($history->hasPages())
                        <div class="ms-auto" style="flex-shrink: 0;">
                            {{ $history->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
