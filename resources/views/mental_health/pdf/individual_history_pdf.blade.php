<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ประวัติการรักษารายบุคคล</title>
    <style>
        /* ... CSS เหมือนเดิม ... */
        @font-face {
            font-family: 'THSarabunNew'; font-style: normal; font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew'; font-style: normal; font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        body { font-family: 'THSarabunNew', sans-serif; font-size: 16px; line-height: 1.6; }
        .header h2 { text-align: center; margin: 0; margin-bottom: 20px;}
        .soldier-info table { width: 100%; border-collapse: collapse; margin-bottom: 15px;}
        .soldier-info td { padding: 5px; }
        .treatment-card { border: 1px solid #ccc; border-radius: 5px; padding: 15px; margin-bottom: 15px; page-break-inside: avoid; }
        .treatment-card h3 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; font-weight: bold;}
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ประวัติการนัดหมายและการรักษา</h2>
    </div>

    @if(isset($soldier))
        <div class="soldier-info">
            {{-- ... ข้อมูลทหาร เหมือนเดิม ... --}}
            <table>
                <tr>
                    <td width="50%"><span class="label">ชื่อ-สกุล:</span> {{ $soldier->first_name }} {{ $soldier->last_name }}</td>
                    <td width="50%"><span class="label">เลขประจำตัวประชาชน:</span> {{ $soldier->soldier_id_card ?? '-' }}</td>
                </tr>
                <tr>
                    <td><span class="label">ผลัด:</span> {{ optional($soldier->rotation)->rotation_name ?? '-' }}</td>
                    <td><span class="label">หน่วยฝึก:</span> {{ optional($soldier->trainingUnit)->unit_name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <hr>

        @forelse ($treatments as $tracking_item)
            {{-- ✅✅✅ [แก้ไข] วนลูปการนัดหมายที่อยู่ภายใต้เคสอีกชั้นหนึ่ง ✅✅✅ --}}
            @foreach ($tracking_item->appointments as $appointment)
                <div class="treatment-card">
                    <h3>ประวัติการนัดหมายและการรักษา</h3>
                    <p><span class="label">วันที่บันทึกเคส:</span> {{ \Carbon\Carbon::parse($appointment->appointment_date)->thaidate('j F Y เวลา H:i น.') }}</p>
                    <p><span class="label">สถานที่:</span> {{ $appointment->appointment_location ?? '-' }}</p>

                    @php
                        $typeNames = [
                            'depression' => 'ภาวะซึมเศร้า', 'suicide_risk' => 'ความเสี่ยงฆ่าตัวตาย',
                            'alcohol' => 'การดื่มสุรา', 'smoking' => 'การสูบบุหรี่', 'drug_use' => 'การใช้สารเสพติด',
                        ];
                        $assessmentTypeKey = optional($tracking_item->assessmentScore)->assessment_type;
                    @endphp
                    <p><span class="label">ผลประเมินที่เป็นสาเหตุ:</span> {{ $typeNames[$assessmentTypeKey] ?? ($tracking_item->risk_type == 'prior_history' ? 'มีประวัติเดิม' : 'ไม่ระบุ') }}</p>

                    <h3 style="margin-top: 20px;">ผลการรักษา</h3>
                    {{-- ✅✅✅ [แก้ไข] ดึงข้อมูลจาก $appointment->treatment ✅✅✅ --}}
                    <p><span class="label">แพทย์ผู้รักษา:</span> {{ optional($appointment->treatment)->doctor_name ?? '-' }}</p>
                    <p><span class="label">ยาที่รักษา:</span> {{ optional($appointment->treatment)->medicine_name ?? '-' }}</p>
                    <p><span class="label">ข้อมูลเพิ่มเติม:</span> {{ optional($appointment->treatment)->notes ?? '-' }}</p>
                </div>
            @endforeach
        @empty
            <div class="treatment-card" style="text-align: center;">
                <p>ไม่พบประวัติการนัดหมายและการรักษา</p>
            </div>
        @endforelse
    @else
        <p style="text-align: center;">ไม่พบข้อมูลทหาร</p>
    @endif
</body>
</html>
