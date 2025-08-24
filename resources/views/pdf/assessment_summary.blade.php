<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานสรุปผลการทำแบบประเมินสุขภาพจิต</title>
    <style>
        /* [แก้ไข] เพิ่ม @font-face เพื่อให้ PDF รู้จักฟอนต์ภาษาไทย */
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

        /* [แก้ไข] กำหนด font-family ให้กับ body ทั้งหมด */
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            word-wrap: break-word; /* ช่วยตัดคำ */
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .soldier-info {
            text-align: left;
        }
        h1 {
            text-align: center;
            font-size: 20px;
        }
        p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>รายงานสรุปผลการทำแบบประเมินสุขภาพจิต</h1>
    <p>ข้อมูล ณ วันที่: {{ \Carbon\Carbon::now()->thaidate('j F Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>ข้อมูลกำลังพล</th>
                <th>ผลัด</th>
                <th>หน่วย</th>
                @foreach ($assessmentLabels as $label)
                    <th>{{ $label }}</th>
                @endforeach
                <th>สถานะการประเมิน</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assessmentData as $data)
            <tr>
                <td class="soldier-info">
                    {{ $data['soldier']->first_name }} {{ $data['soldier']->last_name }}<br>
                    <small>ID: {{ $data['soldier']->soldier_id_card }}</small>
                </td>
                <td>{{ $data['soldier']->rotation->rotation_name ?? 'N/A' }}</td>
                <td>{{ $data['soldier']->trainingUnit->unit_name ?? 'N/A' }}</td>
                @foreach ($assessmentTypes as $type)
                    <td>{{ $data['scores'][$type] ?? '-' }}</td>
                @endforeach
                <td>
                    @if ($data['soldier']->initial_assessment_complete)
                        ครบถ้วน
                    @else
                        ยังไม่ครบ
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 3 + count($assessmentTypes) + 1 }}" style="text-align: center;">ไม่พบข้อมูล</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
