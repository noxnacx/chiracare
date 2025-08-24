<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'รายงาน' }}</title>
    <style>
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
            word-wrap: break-word; /* ช่วยตัดคำขึ้นบรรทัดใหม่ */
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
        .header p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        {{-- แสดงหัวข้อรายงานและวันที่แบบไดนามิก --}}
        <h2>{{ $title ?? 'รายงานสรุป' }}</h2>
        <p>ข้อมูล ณ วันที่ {{ $date ?? '' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ชื่อ-สกุล</th>
                <th>ผลัด</th>
                <th>หน่วยฝึก</th>
                <th>ประเภทความเสี่ยง</th>
                <th>ชื่อแบบประเมิน</th>
                <th>วันที่ได้รับข้อมูล</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cases as $item)
                <tr>
                    <td>{{ optional($item->soldier)->first_name }} {{ optional($item->soldier)->last_name }}</td>
                    <td>{{ optional($item->soldier->rotation)->rotation_name ?? '-' }}</td>
                    <td>{{ optional($item->soldier->trainingUnit)->unit_name ?? '-' }}</td>
                    <td>
                        @if($item->risk_type == 'at_risk')
                            จากผลประเมิน
                        @else
                            มีประวัติเดิม
                        @endif
                    </td>
                    <td>
                        @php
                            $typeNames = [
                                'depression'     => 'ภาวะซึมเศร้า',
                                'suicide_risk'   => 'ความเสี่ยงฆ่าตัวตาย'
                            ];
                            $assessmentTypeKey = optional($item->assessmentScore)->assessment_type;
                        @endphp
                        {{ $typeNames[$assessmentTypeKey] ?? $assessmentTypeKey ?? '-' }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->thaidate('j M Y') }}</td>
                    <td>
                        @if($item->status == 'required')
                            รอส่งป่วย
                        @elseif($item->status == 'scheduled')
                            นัดหมายสำเร็จ
                        @else
                            {{ $item->status }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">ไม่พบข้อมูลตามเงื่อนไขที่กำหนด</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
