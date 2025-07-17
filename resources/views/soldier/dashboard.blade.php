<!DOCTYPE html>
<html lang="th">
@include('themes.head')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">แดชบอร์ดทหาร</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h4>สุขภาพร่างกาย</h4>
                                    @php
                                        $bmi = $soldier->height_cm ? $soldier->weight_kg / (($soldier->height_cm / 100) ** 2) : null;
                                    @endphp
                                    @if ($bmi)
                                        <p>
                                            @if ($bmi < 18.5)
                                                ค่า BMI ของท่านอยู่ในระดับน้อยกว่าเกณฑ์
                                            @elseif ($bmi >= 18.5 && $bmi <= 22.9)
                                                ค่า BMI ของท่านอยู่ในระดับตามเกณฑ์
                                            @elseif ($bmi >= 23 && $bmi <= 24.9)
                                                ค่า BMI ของท่านอยู่ในระดับมากกว่าเกณฑ์
                                            @elseif ($bmi >= 25 && $bmi <= 29.9)
                                                ค่า BMI ของท่านอยู่ในระดับโรคอ้วนระดับที่ 1
                                            @elseif ($bmi >= 30 && $bmi <= 40)
                                                ค่า BMI ของท่านอยู่ในระดับโรคอ้วนระดับที่ 2
                                            @else
                                                ค่า BMI ของท่านอยู่ในระดับโรคอ้วนระดับที่ 3
                                            @endif
                                        </p>
                                    @else
                                        <p>ไม่สามารถคำนวณ BMI ได้</p>
                                    @endif
                                </div>
                                <div class="icon">
                                    <i class="fas fa-heartbeat"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h4>{{ \Carbon\Carbon::parse($nextAppointment)->format('d M Y') }}</h4>
                                    <p>นัดหมายครั้งถัดไป</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h4>{{ \Carbon\Carbon::parse($lastCheckup)->format('d M Y') }}</h4>
                                    <p>เข้ารับการรักษาล่าสุด</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">การนัดหมายที่กำลังจะมาถึง</h5>
                                <a href="#" class="btn btn-sm btn-outline-primary">ดูเพิ่มเติม</a>
                            </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>ตรวจร่างกายประจำปี</strong><br>
                                        <small class="text-muted">30 เม.ย. 2025 - 09:00 น.</small>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>ตรวจสุขภาพช่องปาก</strong><br>
                                        <small class="text-muted">22 พ.ค. 2025 - 14:30 น.</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">ประวัติการรักษา</h5>
                                    <a href="#" class="btn btn-sm btn-outline-primary">ดูเพิ่มเติม</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>การรักษาอาการบาดเจ็บที่ไหล่</strong><br>
                                            <small class="text-muted">5 เม.ย. 2025 — นัดทำแผลตามปกติ</small>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>การฉีดวัคซีนประจำปี</strong><br>
                                            <small class="text-muted">15 ธ.ค. 2024 — การฉีดวัคซีนตามปกติ</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @php
                            $typeLabels = [
                                'smoking' => 'แบบประเมินการสูบบุหรี่',
                                'alcohol' => 'แบบประเมินการดื่มแอลกอฮอล์',
                                'drug_use' => 'แบบประเมินการใช้สารเสพติด',
                                'depression' => 'แบบประเมินภาวะซึมเศร้า',
                                'suicide_risk' => 'แบบประเมินความเสี่ยงต่อการฆ่าตัวตาย',
                            ];

                            $maxScores = [
                                'smoking' => 10,
                                'alcohol' => 40,
                                'drug_use' => 36,
                                'depression' => 27,
                                'suicide_risk' => 16,
                            ];
                        @endphp

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">ประวัติการทำแบบประเมิน</h5>
                                    <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="btn btn-sm btn-outline-primary">ดูเพิ่มเติม</a>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @forelse ($recentHistories as $item)
                                            @php
                                                // 1. ดึงชื่อประเภท (type) ออกมาจาก relationship ก่อน
                                                $type = optional($item->assessmentType)->assessment_type;

                                                // 2. นำตัวแปร $type ไปใช้หาค่า label และ max score
                                                $label = $typeLabels[$type] ?? $type;
                                                $max = $maxScores[$type] ?? 10;
                                            @endphp
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>{{ $label }}</span>
                                                <span class="fw-bold">{{ number_format($item->total_score, 1) }}/{{ $max }}</span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-muted text-center">ยังไม่มีข้อมูล</li>
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
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    @include('themes.script')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-5px)';
                    this.style.transition = 'transform 0.3s ease';
                    this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
                });
                card.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '';
                });
            });

            const currentPath = window.location.pathname;
            const menuItems = document.querySelectorAll('.nav-sidebar .nav-link');
            menuItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href)) {
                    item.classList.add('active');
                    const parentItem = item.closest('.nav-item');
                    if (parentItem) {
                        parentItem.classList.add('menu-open');
                    }
                }
            });
        });
    </script>
</body>
</html>
