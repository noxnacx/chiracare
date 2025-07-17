<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <style>
        @media (max-width: 768px) {
            .profile-card {
                text-align: center;
            }
            .profile-card img {
                margin-bottom: 15px;
            }
        }
    </style>
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.soldier.navbarsoldier')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('themes.soldier.menusoldier')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">

                    <!-- ✅ โปรไฟล์ทหาร -->
                    <style>
                        @media (max-width: 768px) {
                            .profile-card {
                                text-align: center;
                            }
                            .profile-card img {
                                margin-bottom: 15px;
                            }
                        }
                    </style>
                    <h1 class="m-0 mb-4">หน้าแรก(โปรไฟล์ทหาร)</h1>
                    <div class="container">
                        <div class="card shadow-sm p-4">
                            <div class="row align-items-center">
                                <!-- ✅ ทำให้ responsive -->
                                <div class="col-md-3 text-center order-md-2 profile-card">
                                    @if($soldier->soldier_image)
                                        <img src="{{ asset('uploads/soldiers/' . basename($soldier->soldier_image)) }}"
                                            alt="Soldier Image"
                                            class="rounded border border-success img-fluid"
                                            style="max-width: 220px; height: auto;">
                                    @endif
                                </div>


                                <div class="col-md-9 order-md-1">
                                    <h3 class="fw-bold text-success text-md-start ">
                                        พลฯ {{ $soldier->first_name }} {{ $soldier->last_name }}
                                    </h3>

                                    <p><strong>เลขบัตรประชาชน:</strong> {{ $soldier->soldier_id_card }}
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">การคัดเลือก:</strong> {{ $soldier->selection_method }}
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">ผลัด:</strong> {{ $soldier->rotation->rotation_name ?? 'N/A' }}
                                    </p>

                                    <p><strong>หน่วยฝึก:</strong> {{ $soldier->trainingUnit->unit_name ?? 'N/A' }}
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">หน่วยต้นสังกัด:</strong> {{ $soldier->affiliated_unit ?? 'N/A' }}
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">ระยะเวลารับราชการ:</strong> {{ $soldier->service_duration }} เดือน
                                    </p>

                                    <p><strong>โรคประจำตัว:</strong> {{ $soldier->underlying_diseases ?? 'ไม่มี' }}
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">ประวัติแพ้ยา/อาหาร:</strong> {{ $soldier->medical_allergy_food_history ?? 'ไม่มี' }}
                                    </p>

                                    <p><strong>น้ำหนัก:</strong> {{ $soldier->weight_kg }} kg
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">ส่วนสูง:</strong> {{ $soldier->height_cm }} cm
                                        <br class="d-md-none">
                                        <strong class="ms-md-4">BMI:</strong> {{ number_format($soldier->weight_kg / (($soldier->height_cm / 100) ** 2), 1) }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ✅ ปุ่มต่าง ๆ ให้รองรับมือถือ -->
                    <div class="d-flex flex-wrap justify-content-center mt-4">
                        <a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}"
                            class="btn btn-success mx-2 mb-2 flex-grow-1"
                            id="assessmentButton" disabled>
                            ทำแบบประเมิน <i class="fas fa-edit"></i>
                        </a>

                    </div>



                </div><!-- /.container-fluid -->
            </div>
            <!-- Main content -->
            @yield('content')
            <!-- /.content -->
        <!-- ✅ แท็บ Medical History / Assessment History -->
        <div class="mt-4">
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
                            <button class="btn btn-sm btn-outline-primary view-details-btn" data-id="{{ $history->id }}">
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

        <div class="tab-pane fade" id="mental-history-pane" role="tabpanel" aria-labelledby="mental-history-tab" tabindex="0">
            <div class="list-group list-group-flush">
                @php
                    $mentalHealthTreatments = $soldier->mentalHealthTracking->flatMap(function ($case) {
                        return $case->appointments;
                    })->filter(function ($appointment) {
                        return $appointment->treatment;
                    })->sortByDesc('treatment.treatment_date')->take(3); // <-- เพิ่ม .take(3) ตรงนี้
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

        <div class="tab-pane fade" id="assessment-pane" role="tabpanel" aria-labelledby="assessment-tab" tabindex="0">
            <div class="card shadow-sm">
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
                        <div class="row">
                            <div class="col-md-3"><strong class="text-muted">แผนก:</strong></div>
                            <div class="col-md-9" id="detail_department">-</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-3"><strong class="text-muted">แพทย์ผู้รักษา:</strong></div>
                            <div class="col-md-9" id="detail_doctor">-</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-3"><strong class="text-muted">สถานะ:</strong></div>
                            <div class="col-md-9" id="detail_status">-</div>
                        </div>
                        <hr class="my-2">
                         <div class="row">
                            <div class="col-md-3"><strong class="text-muted">อาการ:</strong></div>
                            <div class="col-md-9" id="detail_symptom">-</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-3"><strong class="text-muted">คำแนะนำการฝึก:</strong></div>
                            <div class="col-md-9" id="detail_instruction">-</div>
                        </div>
                    </div>
                </div>
                <div class="card">
                     <div class="card-header fw-bold">วินิจฉัยโรค</div>
                     <div class="card-body">
                         <ul class="list-unstyled mb-0" id="detail_diseases_list">
                         </ul>
                     </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
                        </div>
                    </div>

        </div>
        <!-- /.content-wrapper -->
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>

    <!-- ✅ Popup ยืนยันสิทธิ์ในการเก็บข้อมูล -->
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

    @include('themes.script')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // =================================================================
        // ส่วนที่ 1: จัดการ Consent Modal (โค้ดปรับปรุงใหม่)
        // =================================================================
        const consentModalEl = document.getElementById('consentModal');
        const assessmentButton = document.getElementById("assessmentButton");

        // ✅ ตรวจสอบค่าจากฐานข้อมูลโดยตรงผ่าน Blade
        // ถ้า consent_accepted เป็น 1 (true) ให้เปิดปุ่มเลย
        @if($soldier->consent_accepted)
            if (assessmentButton) {
                assessmentButton.removeAttribute("disabled");
            }
        @else
            // ถ้า consent_accepted เป็น 0 (false) ให้จัดการแสดง Modal
            if (consentModalEl && assessmentButton) {
                const consentModal = new bootstrap.Modal(consentModalEl);
                const acceptConsentBtn = document.getElementById("acceptConsent");
                const declineConsentBtn = document.getElementById("declineConsent");

                // แสดง Modal ขึ้นมาเลย
                consentModal.show();

                // จัดการการคลิกปุ่ม "ไม่ยินยอม"
                if (declineConsentBtn) {
                    declineConsentBtn.addEventListener("click", function() {
                        consentModal.hide();
                    });
                }

                // จัดการการคลิกปุ่ม "ยินยอม"
                if (acceptConsentBtn) {
                    acceptConsentBtn.addEventListener("click", function() {
                        // ส่ง request กลับไปอัปเดตฐานข้อมูล
                        fetch("{{ route('soldier.accept_consent', ['id' => $soldier->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}' // สำคัญมากสำหรับ Laravel
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
                                // เมื่อสำเร็จ ให้เปิดปุ่ม, ซ่อน Modal, และพาไปหน้าทำแบบประเมิน
                                assessmentButton.removeAttribute("disabled");
                                consentModal.hide();
                                // ไม่ต้องใช้ localStorage แล้ว เพราะเราเช็คจากฐานข้อมูลโดยตรง
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

                // กรณีที่ผู้ใช้พยายามกดปุ่ม "ทำแบบประเมิน" ทั้งที่ยังไม่ได้ยินยอม
                assessmentButton.addEventListener("click", function(event) {
                    if (assessmentButton.hasAttribute('disabled')) {
                        event.preventDefault();
                        consentModal.show();
                    }
                });
            }
        @endif


        // =================================================================
        // ส่วนที่ 2: จัดการ History Detail Modal (โค้ดเดิม)
        // =================================================================
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

{{-- ส่วนนี้คือ Modal ที่แสดงผลลัพธ์การประเมิน (ถ้ามี) --}}
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


