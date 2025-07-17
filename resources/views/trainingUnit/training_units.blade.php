<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหน่วยฝึก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">รายการหน่วยฝึก</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @foreach($units as $unit)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $unit->unit_name }}</span>
                            <a href="{{ url('/dashboard/training-unit/' . $unit->id) }}" class="btn btn-sm btn-primary">
                                ไปที่แดชบอร์ด
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</body>

</html>