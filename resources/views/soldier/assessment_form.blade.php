<!DOCTYPE html>
<html lang="en">
@include('themes.head')

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
                            $assessmentName = $assessmentTitles[$type] ?? 'แบบประเมิน';
                        @endphp

                        <h2 class="fw-bold text-center">{{ $assessmentName }}</h2>

                        {{-- ✅ ซ่อนฟอร์มหลักไว้ก่อน --}}
                        <div class="card shadow-lg p-4" id="mainAssessmentForm" style="display: none;">
                            <form
                                action="{{ route('assessment.submit', ['soldier_id' => $soldier->id, 'type' => $type]) }}"
                                method="POST">
                                @csrf
                                @foreach($questions as $index => $question)
                                    <div class="mb-4 pb-3" style="border-bottom: 1px solid #ddd;">
                                        <p class="fw-bold">{{ $index + 1 }}. {{ $question->question_text }}</p>
                                        @foreach($question->options as $option)
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="answers[{{ $question->id }}]"
                                                    value="{{ $option->id }}" required>
                                                <label class="form-check-label">{{ $option->option_text }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-dark btn-lg px-5">ส่งแบบประเมิน</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('themes.soldier.footersoldier')
    </div>

    {{-- ✅ HTML ของ Popup --}}
    <div class="modal fade" id="assessmentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="assessmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assessmentModalLabel">คำถามคัดกรอง</h5>
                </div>
                <div class="modal-body text-center">
                    <p id="assessmentQuestion" class="fs-5"></p>
                    <div class="d-grid gap-2 col-10 mx-auto">
                        <button class="btn btn-outline-primary" onclick="setAssessmentStatus(0)"></button>
                        <button class="btn btn-primary" onclick="setAssessmentStatus(1)"></button>
                        <button class="btn btn-outline-primary" onclick="setAssessmentStatus(2)"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('themes.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ✅ JavaScript สำหรับควบคุม Popup และ Flow --}}
    <script>
        // ใช้ตัวแปรจาก PHP Blade
        const currentType = @json($type);
        const soldierId = @json($soldier->id);
        const typesWithPopup = ['smoking', 'alcohol', 'drug_use'];
        const mainForm = document.getElementById('mainAssessmentForm');
        let assessmentModalInstance = null; // เก็บ instance ของ modal

        function openAssessmentPopup(type) {
            let questionText = "";
            let option1 = ""; // ไม่
            let option2 = ""; // ใช่
            let option3 = ""; // เคยแต่เลิกแล้ว

            if (type === 'smoking') {
                questionText = "คุณสูบบุหรี่หรือไม่?";
                option1 = "ไม่";
                option2 = "ใช่";
                option3 = "เคยสูบ แต่เลิกแล้ว (เกิน 1 เดือน)";
            } else if (type === 'alcohol') {
                questionText = "คุณดื่มเครื่องดื่มแอลกอฮอล์หรือไม่?";
                option1 = "ไม่";
                option2 = "ใช่";
                option3 = "เคยดื่ม แต่เลิกแล้ว (เกิน 1 เดือน)";
            } else if (type === 'drug_use') {
                questionText = "คุณเคยใช้สารเสพติดหรือไม่?";
                option1 = "ไม่";
                option2 = "ใช่";
                option3 = "เคยใช้ แต่เลิกแล้ว (เกิน 1 เดือน)";
            }

            document.getElementById('assessmentQuestion').innerText = questionText;
            const buttons = document.querySelectorAll("#assessmentModal .btn");
            buttons[0].innerText = option1;
            buttons[1].innerText = option2;
            buttons[2].innerText = option3;

            // สร้างและแสดง Modal
            const modalEl = document.getElementById('assessmentModal');
            if (modalEl) {
                assessmentModalInstance = new bootstrap.Modal(modalEl);
                assessmentModalInstance.show();
            }
        }

        function setAssessmentStatus(status) {
            // status: 0=ไม่, 1=ใช่, 2=เคยแต่เลิกแล้ว

            if (status === 0) {
                // ถ้าตอบ 'ไม่' ให้ไปที่ Route 'skip'
                const skipUrl = `{{ url('/assessment') }}/${soldierId}/${currentType}/skip`;
                window.location.href = skipUrl;
            } else {
                // ถ้าตอบอย่างอื่น ให้ซ่อน Modal และแสดงฟอร์มหลัก
                if (assessmentModalInstance) {
                    assessmentModalInstance.hide();
                }
                mainForm.style.display = 'block';
            }
        }

        // เมื่อหน้าเว็บโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            if (typesWithPopup.includes(currentType)) {
                // ถ้าเป็นประเภทที่ต้องมี Popup ให้เรียกฟังก์ชัน
                openAssessmentPopup(currentType);
            } else {
                // ถ้าไม่ใช่ ให้แสดงฟอร์มหลักเลย
                mainForm.style.display = 'block';
            }
        });
    </script>
</body>
</html>
