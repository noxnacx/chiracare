<!DOCTYPE html>
<html lang="th">

@include('themes.head')

{{-- CSS สำหรับดีไซน์ใหม่ --}}
<style>
    :root {
        --primary-color: #0d6efd;
        --success-color: #198754;
        --warning-color: #ffc107;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --text-muted: #6c757d;
        --mental-health-color: #6f42c1; /* สีสำหรับสุขภาพจิต */
    }
    .content-wrapper { background-color: #f4f6f9; }
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color), #0dcaf0);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }
    .custom-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: none;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .custom-card .card-body {
        flex-grow: 1;
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
        padding-left: 0;
        padding-right: 0;
    }
    .list-group-item:first-child { border-top: 0; }
    .list-group-item:last-child { border-bottom: 0; }
    .type-indicator {
        font-size: 0.75rem;
        font-weight: bold;
        padding: 0.2em 0.6em;
        border-radius: 0.25rem;
        color: #fff;
    }
</style>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <section class="content pt-3">
                <div class="container-fluid">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Profile Header --}}
                    <div class="profile-header text-center text-md-start">
                         <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1">ยินดีต้อนรับ, {{ $soldier->s_rank }} {{ $soldier->s_name }} {{ $soldier->s_surname }}</h2>
                                <p class="mb-0">ภาพรวมสุขภาพและนัดหมายของคุณ</p>
                            </div>
                            <div class="col-md-4 text-center text-md-end mt-3 mt-md-0">
                                <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="btn btn-light">
                                    <i class="fas fa-poll me-2"></i>ทำแบบประเมิน
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Main Content Row --}}
                    <div class="row">
                        {{-- All Appointments --}}
                        <div class="col-lg-4 mb-4">
                            <div class="card custom-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">การนัดหมายทั้งหมด</h5>
                                    <a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        {{-- ✅✅✅ จุดที่แก้ไข ✅✅✅ --}}
                                        @forelse($allAppointments as $app)
                                            <li class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">
                                                        @if($app->type == 'กาย')
                                                            <span class="type-indicator" style="background-color: var(--primary-color);">กาย</span>
                                                        @else
                                                            <span class="type-indicator" style="background-color: var(--mental-health-color);">จิตใจ</span>
                                                        @endif
                                                        {{ \Str::limit($app->description, 25) }}
                                                    </h6>
                                                    <small>{{ \Carbon\Carbon::parse($app->date)->thaidate('j M y') }}</small>
                                                </div>
                                                <small class="text-muted">สถานะ: {{ $app->status }}</small>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ไม่มีข้อมูลนัดหมาย</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Treatment History --}}
                        <div class="col-lg-4 mb-4">
                            <div class="card custom-card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">ประวัติการรักษาล่าสุด</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @forelse($treatmentHistory as $history)
                                            <li class="list-group-item">
                                                 <div class="d-flex w-100 justify-content-between">
                                                    <p class="mb-1">
                                                         @if($history->type == 'กาย')
                                                            <span class="type-indicator" style="background-color: var(--success-color);">กาย</span>
                                                        @else
                                                            <span class="type-indicator" style="background-color: var(--mental-health-color);">จิตใจ</span>
                                                        @endif
                                                        {{ \Str::limit($history->description, 25) }}
                                                    </p>
                                                     <small>{{ \Carbon\Carbon::parse($history->date)->thaidate('j M y') }}</small>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ไม่มีประวัติการรักษา</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Assessment History --}}
                        <div class="col-lg-4 mb-4">
                             <div class="card custom-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">ผลการประเมินล่าสุด</h5>
                                    <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="btn btn-sm btn-outline-primary">ดูประวัติ</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @php
                                            $typeLabels = ['smoking' => 'การสูบบุหรี่', 'alcohol' => 'การดื่มสุรา', 'drug_use' => 'การใช้สารเสพติด', 'depression' => 'ภาวะซึมเศร้า', 'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย'];
                                        @endphp
                                        @forelse ($recentHistories as $item)
                                            @php
                                                $type = optional($item->assessmentType)->assessment_type;
                                                $label = $typeLabels[$type] ?? $type;
                                            @endphp
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>{{ $label }}</span>
                                                <span class="fw-bold">{{ $item->assessment_level }}</span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">ยังไม่มีประวัติการทำแบบประเมิน</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>

        @include('themes.soldier.footersoldier')
    </div>
    @include('themes.script')
</body>
</html>
