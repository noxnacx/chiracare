<!DOCTYPE html>
<html lang="th">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>รายงานการนัดหมาย</title>
    <style>
        * {
            font-family: "Sarabun", DejaVu Sans, sans-serif;
        }

        body {
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            page-break-inside: auto;
        }

        thead th {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
        }

        td,
        th {
            word-break: keep-all;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <h2>รายงานการนัดหมาย</h2>

    <table>
        <thead>
            <tr>
                <th width="18%">ชื่อ - นามสกุล</th>
                <th width="12%">ผลัด</th>
                <th width="18%">หน่วยฝึก</th>
                <th width="15%">วัน/เวลา</th>
                <th width="15%">สถานที่</th>
                <th width="10%">ประเภทเคส</th>
                <th width="12%">หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($appointments as $a)
                <tr>
                    <td>{{ $a->medicalReport->soldier->first_name ?? '-' }}
                        {{ $a->medicalReport->soldier->last_name ?? '-' }}
                    </td>
                    <td>{{ $a->medicalReport->soldier->rotation->rotation_name ?? '-' }}</td>
                    <td>{{ $a->medicalReport->soldier->training_unit->unit_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->appointment_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $a->appointment_location }}</td>
                    <td>{{ $a->case_type === 'normal' ? 'ปกติ' : 'วิกฤติ' }}</td>
                    <td>{{ $a->is_follow_up ? 'นัดติดตามอาการ' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        พิมพ์เมื่อ: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>