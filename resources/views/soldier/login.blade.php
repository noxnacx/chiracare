<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบทหาร</title>
    @include('themes.head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        /* --- Theme Colors --- */
        :root {
            --theme-secondary-bg: #F8F9FA;
            --theme-text-dark: #343a40;
            --theme-card-bg: #FFFFFF;
            --theme-border-color: #dee2e6;
            --theme-info-color: #A9C5C8;
            --theme-accent-color: #8E44AD;
            --theme-accent-darker: #7D3C98;
            --theme-accent-focus-ring: rgba(142, 68, 173, 0.25);
        }

        /* --- Page Layout --- */
        html,
        body {
            height: 100%;
        }

        body {
            background-color: var(--theme-secondary-bg);
            font-family: "Sarabun", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        /* --- Themed Card --- */
        .login-card {
            background-color: var(--theme-card-bg);
            border: 1px solid var(--theme-border-color);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.07);
        }

        /* --- Themed Typography --- */
        h1 {
            color: var(--theme-text-dark);
            font-weight: bold;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }
        h2 {
            color: var(--theme-text-dark);
            font-weight: 500;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        label {
            color: var(--theme-text-dark);
            font-weight: 500;
        }

        /* --- Themed Form & Button --- */
        .form-control {
            border-radius: 8px;
            border-color: var(--theme-border-color);
            padding: .6rem 1rem;
        }
        .form-control:focus {
            border-color: var(--theme-accent-color);
            box-shadow: 0 0 0 0.25rem var(--theme-accent-focus-ring);
        }
        .btn-theme-accent {
            background-color: var(--theme-accent-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .75rem;
            font-weight: bold;
            transition: background-color 0.2s ease-in-out;
        }
        .btn-theme-accent:hover {
            background-color: var(--theme-accent-darker);
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>
            <i class="fas fa-shield-alt me-2" style="color: var(--theme-info-color);"></i>
            เข้าสู่ระบบ (สำหรับทหาร)
        </h1>
        <div class="login-card">
            <h2 class="text-center mb-4">กรุณากรอกเลขบัตรประชาชน 13 หลัก</h2>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('soldier.authenticate') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="soldier_id_card" class="form-label">เลขบัตรประชาชน:</label>
                    <input type="text" name="soldier_id_card" id="soldier_id_card" class="form-control form-control-lg"
                           maxlength="13" required pattern="\d{13}" title="กรุณากรอกเลขบัตรประชาชน 13 หลักให้ถูกต้อง"
                           placeholder="xxxxxxxxxxxxx">
                </div>
                <button type="submit" class="btn btn-theme-accent w-100">เข้าสู่ระบบ</button>
            </form>
        </div>
    </div>
</body>

</html>
