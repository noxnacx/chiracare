<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <style>
        /* --- Theme Colors --- */
        :root {
            /* Renamed for clarity */
            --theme-info-color: #A9C5C8; /* Muted Blue-Gray */
            --theme-info-darker: #9ab8bb;
            --theme-info-darkest: #8ba8ac;
            --theme-info-focus-ring: rgba(169, 197, 200, 0.5);

            /* Accent Purple Color */
            --theme-accent-color: #8E44AD;
            --theme-accent-darker: #7D3C98;
            --theme-accent-darkest: #6C3483;
            --theme-accent-focus-ring: rgba(142, 68, 173, 0.25);

            --theme-secondary-bg: #F8F9FA;
            --theme-text-dark: #343a40;
            --theme-text-light: #6c757d;
            --theme-card-bg: #FFFFFF;
            --theme-list-hover-bg: #f1f3f5;
        }

        /* --- General Styling --- */
        body {
            background-color: var(--theme-secondary-bg);
            font-family: 'Sarabun', sans-serif;
        }
        .content-wrapper { background-color: transparent; }
        h1, h3, h5, h6 {
            color: var(--theme-text-dark);
            font-weight: bold;
        }

        /* --- Card Styling --- */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem !important;
            overflow: hidden;
        }

        /* --- ACCENT Button Styling (Purple) --- */
        .btn-theme-accent {
            background-color: var(--theme-accent-color);
            border-color: var(--theme-accent-color);
            color: #fff;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease-in-out;
        }
        .btn-theme-accent:hover {
            background-color: var(--theme-accent-darker);
            border-color: var(--theme-accent-darker);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .btn-theme-accent:focus {
            color: #fff;
            background-color: var(--theme-accent-darker);
            border-color: var(--theme-accent-darker);
            box-shadow: 0 0 0 0.25rem var(--theme-accent-focus-ring);
        }
        .btn-theme-accent:active {
            color: #fff;
            background-color: var(--theme-accent-darkest);
            border-color: var(--theme-accent-darkest);
            transform: translateY(1px);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        /* --- Tab Styling --- */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            padding: 0 1rem;
        }
        .nav-tabs .nav-link {
            border: none;
            color: var(--theme-text-light);
            font-weight: 500;
            margin-bottom: -2px;
            transition: color 0.2s ease-in-out;
        }
        .nav-tabs .nav-link:not(.active):hover {
            color: var(--theme-text-dark);
        }
        .nav-tabs .nav-link.active {
            color: var(--theme-text-dark);
            background-color: transparent;
            border-bottom: 2px solid var(--theme-info-color);
        }

        /* --- Profile Card --- */
        .profile-header { color: var(--theme-text-dark); }
        .profile-image {
            border: 4px solid var(--theme-info-color) !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* --- History List --- */
        .list-group-flush .list-group-item {
            background-color: transparent;
            border-color: #e9ecef;
            transition: background-color 0.2s ease-in-out;
        }
        .list-group-flush .list-group-item:hover {
            background-color: var(--theme-list-hover-bg);
        }
        .view-details-btn {
            border: 1px solid var(--theme-info-color);
            color: var(--theme-info-color);
            background-color: transparent;
            transition: all 0.2s ease-in-out;
        }
        .view-details-btn:hover {
             background-color: var(--theme-info-color);
             color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-card { text-align: center; }
            .profile-card img { margin-bottom: 15px; }
            .nav-tabs { padding: 0; }
        }
    </style>

    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">

                    <h1 class="m-0 mb-4">หน้าแรก (โปรไฟล์ทหาร)</h1>
                    <div class="container">
                        <div class="card p-4">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center order-md-2 profile-card">
                                    @if($soldier->soldier_image)
                                        <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}"
                                             alt="Soldier Image"
                                             class="rounded-circle profile-image img-fluid"
                                             style="width: 180px; height: 180px; object-fit: cover;">
                                    @endif
                                </div>

                                <div class="col-md-9 order-md-1">
                                    <h3 class="fw-bold profile-header text-md-start ">
                                        พลฯ {{ $soldier->first_name }} {{ $soldier->last_name }}
                                    </h3>
                                    <p class="mb-2"><strong>เลขบัตรประชาชน:</strong> {{ $soldier->soldier_id_card }}<br class="d-md-none">
                                        <strong class="ms-md-4">การคัดเลือก:</strong> {{ $soldier->selection_method }}<br class="d-md-none">
                                        <strong class="ms-md-4">ผลัด:</strong> {{ $soldier->rotation->rotation_name ?? 'N/A' }}
                                    </p>
                                    <p class="mb-2"><strong>หน่วยฝึก:</strong> {{ $soldier->trainingUnit->unit_name ?? 'N/A' }}<br class="d-md-none">
                                        <strong class="ms-md-4">หน่วยต้นสังกัด:</strong> {{ $soldier->affiliated_unit ?? 'N/A' }}<br class="d-md-none">
                                        <strong class="ms-md-4">ระยะเวลารับราชการ:</strong> {{ $soldier->service_duration }} เดือน
                                    </p>
                                    <p class="mb-2"><strong>โรคประจำตัว:</strong> {{ $soldier->underlying_diseases ?? 'ไม่มี' }}<br class="d-md-none">
                                        <strong class="ms-md-4">ประวัติแพ้ยา/อาหาร:</strong> {{ $soldier->medical_allergy_food_history ?? 'ไม่มี' }}
                                    </p>
                                    <p class="mb-0"><strong>น้ำหนัก:</strong> {{ $soldier->weight_kg }} kg<br class="d-md-none">
                                        <strong class="ms-md-4">ส่วนสูง:</strong> {{ $soldier->height_cm }} cm<br class="d-md-none">
                                        <strong class="ms-md-4">BMI:</strong> {{ number_format($soldier->weight_kg / (($soldier->height_cm / 100) ** 2), 1) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center mt-4 px-3">
                        <a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}"
                           class="btn btn-theme-accent mx-2 mb-2 w-100"
                           style="max-width: 400px;"
                           id="assessmentButton" disabled>
                            ทำแบบประเมิน <i class="fas fa-edit ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            @yield('content')

            <div class="container-fluid mt-4">
                <ul class="nav nav-tabs" id="historyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="physical-history-tab" data-bs-toggle="tab" data-bs-target="#physical-history-pane" type="button" role="tab" aria-controls="physical-history-pane" aria-selected="true">
                            <i class="fas fa-file-medical-alt me-1"></i> ประวัติการรักษา (ร่างกาย)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mental-history-tab" data-bs-toggle="tab" data-bs-target="#mental-history-pane" type="button" role="tab" aria-controls="mental-history-pane" aria-selected="false">
                            <i class="fas fa-brain me-1"></i> ประวัติการรักษา (สุขภาพจิต)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="assessment-tab" data-bs-toggle="tab" data-bs-target="#assessment-pane" type="button" role="tab" aria-controls="assessment-pane" aria-selected="false">
                            <i class="fas fa-poll-h me-1"></i> สรุปผลการประเมิน
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3" id="historyTabsContent">
                    <div class="tab-pane fade show active" id="physical-history-pane" role="tabpanel" aria-labelledby="physical-history-tab" tabindex="0">
                        <div class="card">
                           <div class="list-group list-group-flush">
                            @php
                                $statusColors = [ 'Discharge' => 'success', 'Admit' => 'primary', 'Refer' => 'info', 'Follow-up' => 'warning' ];
                            @endphp
                            @forelse($medicalHistory as $history)
                                <div class="list-group-item px-md-3 py-3">
                                    <div class="d-flex w-100 justify-content-between flex-wrap">
                                        <p class="history-title mb-1 me-2">
                                            <span class="text-primary fw-bold">{{ strtoupper($history->department_type) }}</span> - {{ \Carbon\Carbon::parse($history->diagnosis_date)->format('j M Y') }}
                                        </p>
                                        <span class="badge bg-{{ $statusColors[$history->treatment_status] ?? 'secondary' }} rounded-pill align-self-start">{{ $history->treatment_status }}</span>
                                    </div>
                                    <p class="history-diagnosis my-1">
                                        <strong>การวินิจฉัย:</strong> {{ Str::limit($history->disease_names, 80) ?? 'N/A' }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="history-doctor mb-0 text-muted">แพทย์ผู้ตรวจ: {{ $history->doctor_name ?? 'N/A' }}</p>
                                        <button class="btn btn-sm view-details-btn" data-id="{{ $history->id }}">
                                            ดูรายละเอียด
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center p-4">
                                    <p class="text-muted mb-0">ยังไม่มีประวัติการรักษา (ร่างกาย)</p>
                                </div>
                            @endforelse
                        </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="mental-history-pane" role="tabpanel" aria-labelledby="mental-history-tab" tabindex="0">
                       <div class="card">
                        <div class="list-group list-group-flush">
                            @php
                                $mentalHealthTreatments = $soldier->mentalHealthTracking->flatMap(function ($case) {
                                    return $case->appointments;
                                })->filter(function ($appointment) {
                                    return $appointment->treatment;
                                })->sortByDesc('treatment.treatment_date')->take(3);
                            @endphp

                            @forelse($mentalHealthTreatments as $appointment)
                                 <div class="list-group-item px-md-3 py-3">
                                     <div class="d-flex w-100 justify-content-between flex-wrap">
                                          <p class="history-title mb-1 me-2">
                                              <strong>พบแพทย์เมื่อ:</strong> {{ \Carbon\Carbon::parse($appointment->treatment->treatment_date)->format('j M Y') }}
                                          </p>
                                     </div>
                                     <div class="ps-2 mt-2 border-start border-2 border-info">
                                         <p class="mb-1"><strong>แพทย์ผู้รักษา:</strong> {{ $appointment->treatment->doctor_name }}</p>
                                         <p class="mb-1"><strong>ยาที่ได้รับ:</strong> {{ $appointment->treatment->medicine_name ?: '-' }}</p>
                                         <p class="mb-0"><strong>บันทึกเพิ่มเติม/วินิจฉัย:</strong> {{ $appointment->treatment->notes ?: '-' }}</p>
                                     </div>
                                 </div>
                            @empty
                                 <div class="list-group-item text-center p-4">
                                     <p class="text-muted mb-0">ยังไม่มีประวัติการรักษา (สุขภาพจิต)</p>
                                 </div>
                            @endforelse
                        </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="assessment-pane" role="tabpanel" aria-labelledby="assessment-tab" tabindex="0">
                        <div class="card">
                            <div class="card-body">
                                @php
                                    $assessmentLabels = [
                                        'smoking' => 'การสูบบุหรี่',
                                        'alcohol' => 'การดื่มแอลกอฮอล์',
                                        'drug_use' => 'การใช้สารเสพติด',
                                        'depression' => 'ภาวะซึมเศร้า',
                                        'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย',
                                    ];
                                    $riskBadges = [
                                        'ต่ำ' => 'bg-success',
                                        'ปานกลาง' => 'bg-warning text-dark',
                                        'สูง' => 'bg-danger',
                                    ];
                                @endphp
                                <div class="list-group list-group-flush">
                                    @forelse($recentHistories as $history)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $assessmentLabels[optional($history->assessmentType)->assessment_type] ?? 'ไม่ระบุ' }}</h6>
                                                <small class="text-muted">ทำเมื่อ: {{ \Carbon\Carbon::parse($history->assessment_date)->format('j M Y, H:i') }} น.</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge {{ $riskBadges[$history->risk_level] ?? 'bg-secondary' }} rounded-pill">{{ $history->risk_level }}</span>
                                                <div class="text-muted small mt-1">คะแนน: {{ $history->total_score }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="list-group-item text-center">
                                            <p class="text-muted mb-0">ยังไม่มีประวัติการทำแบบประเมิน</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="control-sidebar control-sidebar-dark"></aside>

        <div class="modal fade" id="historyDetailModal" tabindex="-1" aria-labelledby="historyDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="historyDetailModalLabel">รายละเอียดการรักษา</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card mb-3">
                            <div class="card-header fw-bold">ข้อมูลการรักษา</div>
                            <div class="card-body">
                                <div class="row"><div class="col-md-3"><strong class="text-muted">แผนก:</strong></div><div class="col-md-9" id="detail_department">-</div></div><hr class="my-2">
                                <div class="row"><div class="col-md-3"><strong class="text-muted">แพทย์ผู้รักษา:</strong></div><div class="col-md-9" id="detail_doctor">-</div></div><hr class="my-2">
                                <div class="row"><div class="col-md-3"><strong class="text-muted">สถานะ:</strong></div><div class="col-md-9" id="detail_status">-</div></div><hr class="my-2">
                                <div class="row"><div class="col-md-3"><strong class="text-muted">อาการ:</strong></div><div class="col-md-9" id="detail_symptom">-</div></div><hr class="my-2">
                                <div class="row"><div class="col-md-3"><strong class="text-muted">คำแนะนำการฝึก:</strong></div><div class="col-md-9" id="detail_instruction">-</div></div>
                            </div>
                        </div>
                        <div class="card">
                             <div class="card-header fw-bold">วินิจฉัยโรค</div>
                             <div class="card-body">
                                  <ul class="list-unstyled mb-0" id="detail_diseases_list"></ul>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="consentModal" tabindex="-1" aria-labelledby="consentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="consentModalLabel">ยืนยันสิทธิ์ในการเก็บข้อมูล</h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            <strong>สิทธิในการเข้าถึงข้อมูลส่วนบุคคล (right of access):</strong><br>
                            ท่านมีสิทธิในการเข้าถึงข้อมูลส่วนบุคคลของท่านและขอให้มูลนิธิทำสำเนาข้อมูลส่วนบุคคลดังกล่าว รวมถึงขอให้มูลนิธิเปิดเผยการได้มาซึ่งข้อมูลส่วนบุคคลที่ท่านไม่ได้ให้ความยินยอมต่อมูลนิธิให้แก่ท่านได้
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="declineConsent">ไม่ยินยอม</button>
                        <button type="button" class="btn btn-success" id="acceptConsent">ยินยอม</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const consentModalEl = document.getElementById('consentModal');
        const assessmentButton = document.getElementById("assessmentButton");

        @if($soldier->consent_accepted)
            if (assessmentButton) {
                assessmentButton.removeAttribute("disabled");
            }
        @else
            if (consentModalEl && assessmentButton) {
                const consentModal = new bootstrap.Modal(consentModalEl);
                const acceptConsentBtn = document.getElementById("acceptConsent");
                const declineConsentBtn = document.getElementById("declineConsent");

                consentModal.show();

                if (declineConsentBtn) {
                    declineConsentBtn.addEventListener("click", function() {
                        consentModal.hide();
                    });
                }

                if (acceptConsentBtn) {
                    acceptConsentBtn.addEventListener("click", function() {
                        fetch("{{ route('soldier.accept_consent', ['id' => $soldier->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                assessmentButton.removeAttribute("disabled");
                                consentModal.hide();
                                window.location.href = "{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}";
                            } else {
                                alert('เกิดข้อผิดพลาดในการบันทึกการยินยอม');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                        });
                    });
                }

                assessmentButton.addEventListener("click", function(event) {
                    if (assessmentButton.hasAttribute('disabled')) {
                        event.preventDefault();
                        consentModal.show();
                    }
                });
            }
        @endif

        const historyDetailModalEl = document.getElementById('historyDetailModal');
        if (historyDetailModalEl) {
            const historyDetailModal = new bootstrap.Modal(historyDetailModalEl);
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const diagnosisId = this.dataset.id;
                    document.getElementById('detail_department').textContent = 'Loading...';
                    document.getElementById('detail_doctor').textContent = '-';
                    document.getElementById('detail_status').textContent = '-';
                    document.getElementById('detail_symptom').textContent = '-';
                    document.getElementById('detail_instruction').textContent = '-';
                    document.getElementById('detail_diseases_list').innerHTML = '';

                    fetch(`/medical-diagnosis/details/${diagnosisId}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            document.getElementById('detail_department').textContent = data.department_type?.toUpperCase() || '-';
                            document.getElementById('detail_doctor').textContent = data.doctor_name || '-';
                            document.getElementById('detail_status').textContent = data.treatment_status || '-';
                            document.getElementById('detail_symptom').textContent = data.symptom_description || '-';
                            document.getElementById('detail_instruction').textContent = data.training_instruction || '-';

                            const diseaseList = document.getElementById('detail_diseases_list');
                            if (data.diseases && data.diseases.length > 0) {
                                data.diseases.forEach(disease => {
                                    const li = document.createElement('li');
                                    li.textContent = disease;
                                    diseaseList.appendChild(li);
                                });
                            } else {
                                const li = document.createElement('li');
                                li.textContent = 'ไม่พบข้อมูลการวินิจฉัย';
                                li.className = 'text-muted';
                                diseaseList.appendChild(li);
                            }
                            historyDetailModal.show();
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
                        });
                });
            });
        }
    });
</script>

@if(session('assessment_result'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var myModal = new bootstrap.Modal(document.getElementById("assessmentResultModal"));
            myModal.show();
        });
    </script>
@endif

</body>

</html>
