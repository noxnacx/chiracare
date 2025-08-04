<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assessmentTitles[$type] ?? 'แบบประเมิน' }}</title>

    @include('themes.head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
    :root {
        --theme-secondary-bg: #F8F9FA;
        --theme-text-dark: #343a40;
        --theme-text-light: #6c757d;
        --theme-card-bg: #FFFFFF;
        --theme-border-color: #dee2e6;
        --theme-info-color: #A9C5C8;
        --theme-info-focus-ring: rgba(169, 197, 200, 0.5);
        --theme-accent-color: #8E44AD;
        --theme-accent-darker: #7D3C98;
    }

    body {
        background-color: var(--theme-secondary-bg);
        font-family: 'Sarabun', sans-serif;
    }
    .content-wrapper {
        background-color: transparent;
    }
    h2, h5 {
        color: var(--theme-text-dark);
        font-weight: bold;
    }
    .card {
        background-color: var(--theme-card-bg);
        border: 1px solid var(--theme-border-color);
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .assessment-question-block {
        border-bottom: 1px solid var(--theme-border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .assessment-question-block:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }
    .form-check-input:checked {
        background-color: var(--theme-accent-color);
        border-color: var(--theme-accent-color);
    }
    .form-check-input:focus {
        border-color: var(--theme-info-color);
        box-shadow: 0 0 0 0.25rem var(--theme-info-focus-ring);
    }
    .btn-theme-accent {
        background-color: var(--theme-accent-color);
        border-color: var(--theme-accent-color);
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        padding: .75rem 1.5rem;
    }
    .btn-theme-accent:hover {
        background-color: var(--theme-accent-darker);
        border-color: var(--theme-accent-darker);
        color: #fff;
    }

    /* Screening Modal styles */
    #assessmentModal .modal-content {
        border-radius: 12px;
        border: none;
    }
    #assessmentModal .modal-header {
        border-bottom: none;
        padding: 1.5rem 1.5rem 0 1.5rem;
    }
     #assessmentModal .modal-body {
        padding: 1rem 2rem 2rem 2rem;
     }

</style>
</head>

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.soldier.navbarsoldier')
        @include('themes.soldier.menusoldier')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="container mt-4">

                        @php
                            $assessmentTitles = [
                                'suicide_risk' => 'แบบประเมินความเสี่ยงการฆ่าตัวตาย',
                                'alcohol' => 'แบบประเมินการดื่มแอลกอฮอล์',
                                'smoking' => 'แบบประเมินการสูบบุหรี่',
                                'depression' => 'แบบประเมินอาการซึมเศร้า',
                                'drug_use' => 'แบบประเมินการใช้สารเสพติด',
                            ];
                            $assessmentIcons = [
                                'suicide_risk' => 'fa-heart-broken',
                                'alcohol' => 'fa-wine-glass-alt',
                                'smoking' => 'fa-smoking',
                                'depression' => 'fa-theater-masks',
                                'drug_use' => 'fa-pills',
                            ];
                            $assessmentName = $assessmentTitles[$type] ?? 'แบบประเมิน';
                            $assessmentIcon = $assessmentIcons[$type] ?? 'fa-clipboard-list';
                        @endphp

                        <h2 class="fw-bold text-center mb-4">
                            <i class="fas {{ $assessmentIcon }} me-2" style="color: var(--theme-info-color);"></i>
                            {{ $assessmentName }}
                        </h2>

                        <div class="card p-4 mx-auto" id="mainAssessmentForm" style="display: none; max-width: 800px;">
                            <form
                                action="{{ route('assessment.submit', ['soldier_id' => $soldier->id, 'type' => $type]) }}"
                                method="POST">
                                @csrf
                                @foreach($questions as $index => $question)
                                    <div class="assessment-question-block">
                                        <p class="fw-bold fs-5">{{ $index + 1 }}. {{ $question->question_text }}</p>
                                        @foreach($question->options as $option)
                                            <div class="form-check fs-6 ps-4">
                                                <input type="radio" class="form-check-input" id="q{{$question->id}}_o{{$option->id}}" name="answers[{{ $question->id }}]"
                                                    value="{{ $option->id }}" required>
                                                <label class="form-check-label" for="q{{$question->id}}_o{{$option->id}}">{{ $option->option_text }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-lg btn-theme-accent">ส่งแบบประเมิน</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('themes.soldier.footersoldier')
    </div>

    {{-- Screening Popup Modal --}}
    <div class="modal fade" id="assessmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="assessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assessmentModalLabel">คำถามคัดกรอง</h5>
                </div>
                <div class="modal-body text-center">
                    <p id="assessmentQuestion" class="fs-5"></p>
                    <div class="d-grid gap-3 col-10 mx-auto mt-4">
                        <button class="btn btn-lg" onclick="setAssessmentStatus(1)"></button> {{-- Yes --}}
                        <button class="btn btn-lg" onclick="setAssessmentStatus(2)"></button> {{-- Used to, but quit --}}
                        <button class="btn btn-lg" onclick="setAssessmentStatus(0)"></button> {{-- No --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const currentType = @json($type);
        const soldierId = @json($soldier->id);
        const typesWithPopup = ['smoking', 'alcohol', 'drug_use'];
        const mainForm = document.getElementById('mainAssessmentForm');
        let assessmentModalInstance = null;

        function openAssessmentPopup(type) {
            let questionText = "";
            let option1 = ""; // Yes
            let option2 = ""; // Used to, but quit
            let option3 = ""; // No

            if (type === 'smoking') {
                questionText = "คุณสูบบุหรี่หรือไม่?";
                option1 = "ใช่, ปัจจุบันยังสูบอยู่";
                option2 = "เคยสูบ แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยสูบ";
            } else if (type === 'alcohol') {
                questionText = "คุณดื่มเครื่องดื่มแอลกอฮอล์หรือไม่?";
                option1 = "ใช่, ปัจจุบันยังดื่มอยู่";
                option2 = "เคยดื่ม แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยดื่ม";
            } else if (type === 'drug_use') {
                questionText = "คุณเคยใช้สารเสพติดหรือไม่?";
                option1 = "ใช่, ปัจจุบันยังใช้อยู่";
                option2 = "เคยใช้ แต่เลิกแล้ว (เกิน 1 เดือน)";
                option3 = "ไม่เคยใช้";
            }

            document.getElementById('assessmentQuestion').innerText = questionText;
            const buttons = document.querySelectorAll("#assessmentModal .btn");

            buttons[0].innerText = option1;
            buttons[0].className = 'btn btn-lg btn-theme-accent'; // "Yes" button is purple

            buttons[1].innerText = option2;
            buttons[1].className = 'btn btn-lg btn-outline-secondary';

            buttons[2].innerText = option3;
            buttons[2].className = 'btn btn-lg btn-outline-secondary';


            const modalEl = document.getElementById('assessmentModal');
            if (modalEl) {
                assessmentModalInstance = new bootstrap.Modal(modalEl);
                assessmentModalInstance.show();
            }
        }

        function setAssessmentStatus(status) {
            // status: 0=No, 1=Yes, 2=Used to but quit
            if (status === 0 || status === 2) {
                const skipUrl = `{{ url('/assessment') }}/${soldierId}/${currentType}/skip?status=${status}`;
                window.location.href = skipUrl;
            } else {
                if (assessmentModalInstance) {
                    assessmentModalInstance.hide();
                }
                mainForm.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typesWithPopup.includes(currentType)) {
                openAssessmentPopup(currentType);
            } else {
                mainForm.style.display = 'block';
            }
        });
    </script>
</body>
</html>
