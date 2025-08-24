<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานประวัติการปิดเคส</title>
    <style>
        /* เพิ่ม font ภาษาไทยสำหรับ PDF */
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>รายงานประวัติการปิดเคส (ข้อมูล ณ วันที่ {{ now()->thaidate('j F Y') }})</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ชื่อ-สกุล</th>
                <th>ผลัด</th>
                <th>หน่วยฝึก</th>
                <th>ประเภทความเสี่ยง</th>
                <th>ชื่อแบบประเมิน</th>
                <th>วันที่ปิดเคสล่าสุด</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ optional($item->soldier)->first_name }} {{ optional($item->soldier)->last_name }}</td>
                    <td>{{ optional(optional($item->soldier)->rotation)->rotation_name ?? '-' }}</td>
                    <td>{{ optional(optional($item->soldier)->trainingUnit)->unit_name ?? '-' }}</td>

                    {{-- ✅ 1. คอลัมน์ "ประเภทความเสี่ยง" (โค้ดเดิมที่ถูกต้อง) --}}
                    <td>
                        @if($item->risk_type == 'at_risk')
                            จากผลประเมิน
                        @elseif($item->risk_type == 'prior_history')
                            มีประวัติเดิม
                        @else
                            -
                        @endif
                    </td>

                    {{-- ✅ 2. คอลัมน์ "ชื่อแบบประเมิน" (โค้ดใหม่) --}}
                    <td>
                        @php
                            $typeNames = [
                                'depression'     => 'ภาวะซึมเศร้า',
                                'suicide_risk'   => 'ความเสี่ยงฆ่าตัวตาย',
                                'alcohol'        => 'การดื่มสุรา',
                                'smoking'        => 'การสูบบุหรี่',
                                'drug_use'       => 'การใช้สารเสพติด',
                            ];
                            $assessmentTypeKey = optional($item->assessmentScore)->assessment_type;
                        @endphp
                        {{ $typeNames[$assessmentTypeKey] ?? $assessmentTypeKey ?? '-' }}
                    </td>

                    <td>{{ \Carbon\Carbon::parse($item->updated_at)->thaidate('j M Y H:i') }}</td>
                    <td><span style="color: green;">ปิดเคส</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">ไม่พบข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
