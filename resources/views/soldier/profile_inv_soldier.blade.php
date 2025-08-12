<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ทหาร - {{ $soldier->first_name }} {{ $soldier->last_name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Custom Theme with Green Accent --- */
        :root {
            --bs-body-font-family: 'Sarabun', sans-serif;
            --bs-body-bg: #f8fafc;
            --bs-secondary-bg: #ffffff;
            --bs-tertiary-bg: #f1f5f9;
            --bs-border-color: #e2e8f0;
            --bs-body-color: #334155;
            --bs-heading-color: #1e293b;
            --bs-secondary-color: #64748b;
            --bs-primary: #10b981; /* Emerald Green */
            --bs-primary-rgb: 16, 185, 129;
            --bs-primary-hover: #059669; /* Darker Green */
            --bs-border-radius: 0.5rem;
        }

        /* --- Layout --- */
        .main-container { display: flex; height: 100vh; min-height: 100vh; }
        .sidebar { width: 256px; flex-shrink: 0; background-color: var(--bs-secondary-bg); box-shadow: 0 0 15px rgba(0,0,0,0.1); transition: transform 0.3s ease-in-out; }
        .main-content { flex-grow: 1; overflow-y: auto; }
        @media (max-width: 767.98px) {
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1040; transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }

        /* --- Sidebar & Navbar --- */
        .sidebar .nav-link { color: #475569; font-weight: 500; margin-bottom: 0.25rem; display: flex; align-items: center; }
        .sidebar .nav-link .nav-icon { width: 30px; text-align: center; }
        .sidebar .nav-link:hover { background-color: var(--bs-tertiary-bg); }
        .sidebar .nav-link.active { background-color: var(--bs-primary); color: #fff; }
        .navbar { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .brand-link { display: flex; align-items: center; justify-content: center; text-decoration: none; }

        /* --- Components --- */
        .card { border: none; border-radius: 0.75rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .profile-image { width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--bs-tertiary-bg); }
        .list-group-item { border-color: var(--bs-border-color); }

        /* --- Responsive Tabs Styling --- */
        .nav-tabs { border-bottom-width: 1px; }
        .nav-tabs .nav-link { color: var(--bs-secondary-color); font-weight: 500; border-bottom-width: 2px; padding: 0.75rem 0.5rem; }
        .nav-tabs .nav-link.active { color: var(--bs-primary); border-color: var(--bs-primary) var(--bs-primary) #fff; }

        @media (max-width: 575.98px) {
            .nav-tabs .nav-link { font-size: 0.75rem; }
            .nav-tabs .nav-link .tab-icon { font-size: 1.25rem; }
        }

        /* --- Buttons & Modals --- */
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-primary:hover { background-color: var(--bs-primary-hover); border-color: var(--bs-primary-hover); }
        .btn-light { background-color: var(--bs-tertiary-bg); border-color: var(--bs-border-color); color: var(--bs-body-color); }
        .btn-light:hover { background-color: #e2e8f0; border-color: #cbd5e1; }
        .modal-content { border: none; border-radius: 0.75rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="main-container">
        <aside class="sidebar d-flex flex-column" id="sidebar">
            <div class="p-3 border-bottom h-auto">
                <a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="brand-link">
                    <img src="{{ URL::asset('dist/img/AdminLTELogo.png')}}" alt="Chiracare Logo" class="rounded-circle me-2" style="width: 32px; height: 32px;">
                    <span class="h5 mb-0 fw-bold text-dark">Chiracare</span>
                </a>
            </div>
            <div class="flex-grow-1 p-3">
                 <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('profile.inv.soldier', ['id' => $soldier->id]) }}" class="nav-link active"><i class="nav-icon fas fa-user-circle"></i><p class="ms-2">หน้าแรก (โปรไฟล์)</p></a></li>
                    <li class="nav-item"><a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p class="ms-2">Dashboard</p></a></li>
                    <li class="nav-item"><a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}" id="assessmentLink" class="nav-link"><i class="nav-icon fas fa-clipboard-list"></i><p class="ms-2">ทำแบบประเมิน</p></a></li>
                    <li class="nav-item"><a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-clipboard-check"></i><p class="ms-2">ประวัติการทำแบบประเมิน</p></a></li>
                    @if(isset($soldier))
                    <li class="nav-item"><a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p class="ms-2">นัดหมายของฉัน</p></a></li>
                    @endif
                    <li class="nav-item"><a href="{{ route('soldier.edit_personal_info', ['id' => $soldier->id]) }}" class="nav-link"><i class="nav-icon fas fa-user-edit"></i><p class="ms-2">แก้ไขข้อมูลส่วนตัว</p></a></li>
                 </ul>
            </div>
            <div class="p-3 border-top h-auto mt-auto">
                <a href="{{ route('soldier.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="nav-icon fas fa-sign-out-alt"></i><p class="ms-2">ออกจากระบบ</p></a>
            </div>
        </aside>
        <div class="main-content d-flex flex-column">
            <nav class="navbar bg-white">
                <div class="container-fluid">
                    <button class="btn border-0 d-md-none" type="button" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <div class="ms-auto fw-medium">พลฯ {{ $soldier->first_name }} {{ $soldier->last_name }}</div>
                </div>
            </nav>
            <div class="flex-grow-1 p-3 p-md-4">
                <div class="container-fluid">
                    <h2 class="h4 fw-bold mb-4"><i class="fas fa-user-circle me-2 text-primary"></i>หน้าแรก (โปรไฟล์)</h2>

                    <div class="card mb-4"><div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                @if($soldier->soldier_image)
                                    <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}" alt="Soldier Image" class="profile-image rounded-circle">
                                @else
                                    <div class="profile-image rounded-circle bg-light d-flex align-items-center justify-content-center"><i class="fas fa-user fa-3x text-secondary"></i></div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h3 class="fw-bold">{{ $soldier->first_name }} {{ $soldier->last_name }}</h3>

                                {{-- RESTORED DATA SECTION --}}
                                <div class="row mt-3 text-secondary" style="font-size: 0.95rem;">
                                    <div class="col-md-4 mb-2"><strong>เลขบัตร ปชช.:</strong> {{ $soldier->soldier_id_card }}</div>
                                    <div class="col-md-4 mb-2"><strong>การคัดเลือก:</strong> {{ $soldier->selection_method }}</div>
                                    <div class="col-md-4 mb-2"><strong>ผลัด:</strong> {{ $soldier->rotation->rotation_name ?? 'N/A' }}</div>

                                    <div class="col-md-4 mb-2"><strong>หน่วยฝึก:</strong> {{ $soldier->trainingUnit->unit_name ?? 'N/A' }}</div>
                                    <div class="col-md-4 mb-2"><strong>หน่วยต้นสังกัด:</strong> {{ $soldier->affiliated_unit ?? 'N/A' }}</div>
                                    <div class="col-md-4 mb-2"><strong>ระยะเวลาประจำการ:</strong> {{ $soldier->service_duration }} เดือน</div>

                                    <div class="col-md-12 mb-2"><strong>โรคประจำตัว:</strong> {{ $soldier->underlying_diseases ?? 'ไม่มี' }}</div>
                                    <div class="col-md-12 mb-2"><strong>ประวัติแพ้ยา/อาหาร:</strong> {{ $soldier->medical_allergy_food_history ?? 'ไม่มี' }}</div>

                                    <div class="col-md-4 mb-2"><strong>น้ำหนัก:</strong> {{ $soldier->weight_kg }} kg</div>
                                    <div class="col-md-4 mb-2"><strong>ส่วนสูง:</strong> {{ $soldier->height_cm }} cm</div>
                                    <div class="col-md-4 mb-2"><strong>BMI:</strong> {{ number_format($soldier->weight_kg / (($soldier->height_cm / 100) ** 2), 1) }}</div>
                                </div>
                                {{-- END OF RESTORED DATA SECTION --}}
                            </div>
                        </div>
                    </div></div>

                    <div class="d-grid mb-4">
                        <a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}" class="btn btn-primary btn-lg" id="assessmentButton"><i class="fas fa-edit me-2"></i> ทำแบบประเมิน</a>
                    </div>

                    <div class="card"><div class="card-body p-4">
                        <ul class="nav nav-tabs nav-fill mb-3" id="historyTabs" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link active d-flex flex-column flex-sm-row align-items-center justify-content-center" id="physical-tab" data-bs-toggle="tab" data-bs-target="#physical-pane" type="button" role="tab"><i class="fas fa-file-medical-alt tab-icon mb-1 mb-sm-0 me-sm-2"></i><span>ร่างกาย</span></button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link d-flex flex-column flex-sm-row align-items-center justify-content-center" id="mental-tab" data-bs-toggle="tab" data-bs-target="#mental-pane" type="button" role="tab"><i class="fas fa-brain tab-icon mb-1 mb-sm-0 me-sm-2"></i><span>สุขภาพจิต</span></button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link d-flex flex-column flex-sm-row align-items-center justify-content-center" id="assessment-tab" data-bs-toggle="tab" data-bs-target="#assessment-pane" type="button" role="tab"><i class="fas fa-poll-h tab-icon mb-1 mb-sm-0 me-sm-2"></i><span>ผลประเมิน</span></button></li>
                        </ul>
                        <div class="tab-content" id="historyTabsContent">
                            <div class="tab-pane fade show active" id="physical-pane" role="tabpanel">
                                <div class="list-group list-group-flush">
                                    @forelse($medicalHistory as $history)
                                        <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                            <div class="mb-2 mb-sm-0">
                                                <p class="fw-bold mb-0">{{ strtoupper($history->department_type) }} - {{ \Carbon\Carbon::parse($history->diagnosis_date)->format('j M Y') }}</p>
                                                <small class="text-muted">การวินิจฉัย: {{ Str::limit($history->disease_names, 80) ?? 'N/A' }}</small>
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm view-details-btn" data-id="{{ $history->id }}">ดูรายละเอียด</button>
                                        </div>
                                    @empty
                                        <p class="text-center text-muted p-3">ยังไม่มีประวัติการรักษา (ร่างกาย)</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="mental-pane" role="tabpanel">
                                @php $mentalHealthTreatments = $soldier->mentalHealthTracking->flatMap(fn($case) => $case->appointments)->filter(fn($apt) => $apt->treatment)->sortByDesc('treatment.treatment_date')->take(3); @endphp
                                <div class="list-group list-group-flush">
                                    @forelse($mentalHealthTreatments as $appointment)
                                        <div class="list-group-item">
                                            <p class="fw-bold mb-1">พบแพทย์เมื่อ: {{ \Carbon\Carbon::parse($appointment->treatment->treatment_date)->format('j M Y') }}</p>
                                            <small class="text-muted d-block">แพทย์ผู้รักษา: {{ $appointment->treatment->doctor_name }}</small>
                                            <small class="text-muted d-block">ยาที่ได้รับ: {{ $appointment->treatment->medicine_name ?: '-' }}</small>
                                        </div>
                                    @empty
                                        <p class="text-center text-muted p-3">ยังไม่มีประวัติการรักษา (สุขภาพจิต)</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="tab-pane fade" id="assessment-pane" role="tabpanel">
                                @php $assessmentLabels = [ 'smoking' => 'การสูบบุหรี่', 'alcohol' => 'การดื่มแอลกอฮอล์', 'drug_use' => 'การใช้สารเสพติด', 'depression' => 'ภาวะซึมเศร้า', 'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย' ]; $riskBadges = [ 'ต่ำ' => 'bg-success-subtle text-success-emphasis', 'ปานกลาง' => 'bg-warning-subtle text-warning-emphasis', 'สูง' => 'bg-danger-subtle text-danger-emphasis' ]; @endphp
                                <div class="list-group list-group-flush">
                                    @forelse($recentHistories as $history)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="fw-bold mb-0">{{ $assessmentLabels[optional($history->assessmentType)->assessment_type] ?? 'ไม่ระบุ' }}</p>
                                                <small class="text-muted">ทำเมื่อ: {{ \Carbon\Carbon::parse($history->assessment_date)->format('j M Y, H:i') }} น.</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge rounded-pill {{ $riskBadges[$history->risk_level] ?? 'bg-secondary' }}">{{ $history->risk_level }}</span>
                                                <div class="small text-muted mt-1">คะแนน: {{ $history->total_score }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-muted p-3">ยังไม่มีประวัติการทำแบบประเมิน</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div></div>
                </div>
            </div>
            </div>
    </div>

    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" class="d-none">@csrf</form>
    <div class="modal fade" id="consentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">ยืนยันสิทธิ์ในการเก็บข้อมูล</h5></div><div class="modal-body"><p><strong class="d-block">สิทธิในการเข้าถึงข้อมูลส่วนบุคคล (right of access):</strong>ท่านมีสิทธิในการเข้าถึงข้อมูลส่วนบุคคลของท่านและขอให้มูลนิธิทำสำเนาข้อมูลส่วนบุคคลดังกล่าว รวมถึงขอให้มูลนิธิเปิดเผยการได้มาซึ่งข้อมูลส่วนบุคคลที่ท่านไม่ได้ให้ความยินยอมต่อมูลนิธิให้แก่ท่านได้</p></div><div class="modal-footer"><button type="button" class="btn btn-secondary" id="declineConsent" data-bs-dismiss="modal">ไม่ยินยอม</button><button type="button" class="btn btn-success" id="acceptConsent">ยินยอม</button></div></div></div></div>
    <div class="modal fade" id="historyDetailModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">รายละเอียดการรักษา</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="historyDetailContent" class="p-2"></div></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sidebar Toggle Logic ---
        const menuButton = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        if (menuButton && sidebar) {
            menuButton.addEventListener('click', () => sidebar.classList.toggle('active'));
        }

        // --- Consent Modal Logic ---
        const consentModalEl = document.getElementById('consentModal');
        if (consentModalEl) {
            const consentModal = new bootstrap.Modal(consentModalEl, { backdrop: 'static', keyboard: false });
            const assessmentElements = [ document.getElementById("assessmentLink"), document.getElementById("assessmentButton") ];
            const soldierId = "{{ $soldier->id }}";
            const userConsentKey = "userConsent_" + soldierId;
            const checkConsent = () => localStorage.getItem(userConsentKey) === "accepted";
            assessmentElements.forEach(element => {
                if (element) {
                    element.addEventListener("click", function(event) {
                        if (!checkConsent()) {
                            event.preventDefault();
                            consentModal.show();
                        }
                    });
                }
            });
            const acceptBtn = document.getElementById("acceptConsent");
            if(acceptBtn) acceptBtn.addEventListener("click", function() {
                localStorage.setItem(userConsentKey, "accepted");
                consentModal.hide();
                if(assessmentElements[0]) window.location.href = assessmentElements[0].href;
            });
        }

        // --- History Detail Modal Logic ---
        const historyDetailModalEl = document.getElementById('historyDetailModal');
        if(historyDetailModalEl) {
            const historyModal = new bootstrap.Modal(historyDetailModalEl);
            const historyDetailContent = document.getElementById('historyDetailContent');
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const diagnosisId = this.dataset.id;
                    historyDetailContent.innerHTML = '<p class="text-center py-5">Loading...</p>';
                    historyModal.show();
                    fetch(`/medical-diagnosis/details/${diagnosisId}`)
                        .then(response => response.ok ? response.json() : Promise.reject('Network error'))
                        .then(data => {
                            let content = `
                                <dl class="row">
                                    <dt class="col-sm-3">แผนก</dt><dd class="col-sm-9">${data.department_type?.toUpperCase() || '-'}</dd>
                                    <dt class="col-sm-3">แพทย์ผู้รักษา</dt><dd class="col-sm-9">${data.doctor_name || '-'}</dd>
                                    <dt class="col-sm-3">สถานะ</dt><dd class="col-sm-9">${data.treatment_status || '-'}</dd>
                                    <dt class="col-sm-3">อาการ</dt><dd class="col-sm-9">${data.symptom_description || '-'}</dd>
                                    <dt class="col-sm-3">คำแนะนำการฝึก</dt><dd class="col-sm-9">${data.training_instruction || '-'}</dd>
                                </dl><hr><h6>วินิจฉัยโรค</h6>`;
                            if (data.diseases && data.diseases.length > 0) {
                                content += '<ul class="list-unstyled mb-0">';
                                data.diseases.forEach(disease => { content += `<li>- ${disease}</li>`; });
                                content += '</ul>';
                            } else {
                                content += '<p class="text-muted">ไม่พบข้อมูลการวินิจฉัย</p>';
                            }
                            historyDetailContent.innerHTML = content;
                        })
                        .catch(error => {
                            console.error('Fetch Error:', error);
                            historyDetailContent.innerHTML = '<p class="text-center text-danger py-5">เกิดข้อผิดพลาดในการดึงข้อมูล</p>';
                        });
                });
            });
        }
    });
    </script>
</body>
</html>
