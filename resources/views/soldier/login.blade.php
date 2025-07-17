<!DOCTYPE html>
<html lang="th">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>เข้าสู่ระบบทหาร</title>
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
 <style>
 html,
 body {
 height: 100%;
 }

 body {
 background-color: #f0f4ef;
 font-family: "Tahoma", sans-serif;
 display: flex;
 align-items: center;
 justify-content: center;
 }

 .login-container { /* [ เพิ่ม ] -> div ครอบเพื่อจัดตำแหน่งข้อความ */
 width: 100%;
 max-width: 400px; /* [ ปรับ ] -> กำหนดขนาดสูงสุดของ Container */
 padding: 15px;
 }

 .card-military {
 background-color: #e0e6db;
 border: 1px solid #a3b18a;
 border-radius: 10px;
 padding: 2rem;
 box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
 width: 100%;
 }

 .btn-military {
 background-color: #4f6f52;
 color: #fff;
 border: none;
 }

 .btn-military:hover {
 background-color: #3e5e41;
 }

 h1 { /* [ เพิ่ม ] -> สำหรับข้อความ "เข้าสู่ระบบ" */
 color: #2d4739;
 font-weight: bold;
 text-align: center;
 margin-bottom: 1.5rem;
 }

 h2 {
 color: #2d4739;
 font-weight: bold;
 text-align: center;
 margin-bottom: 1.5rem; /* [ ปรับ ] -> ลด margin */
 }

 label {
 color: #374c3c;
 }
 </style>
</head>

<body>
 <div class="container">
 <div class="row justify-content-center">
 <div class="col-md-6 col-lg-5 col-xl-4">
 <div class="login-container">
 <h1>เข้าสู่ระบบ(ทหาร)</h1>
 <div class="card-military">
 <h2 class="text-center mb-4">กรอกเลขบัตรประชาชน 13 หลัก</h2>

 @if(session('error'))
 <div class="alert alert-danger">
 {{ session('error') }}
 </div>
 @endif

 <form action="{{ route('soldier.authenticate') }}" method="POST">
 @csrf
 <div class="mb-3">
 <label for="soldier_id_card" class="form-label">เลขบัตรประชาชน:</label>
 <input type="text" name="soldier_id_card" id="soldier_id_card" class="form-control"
 maxlength="13" required>
 </div>
 <button type="submit" class="btn btn-military w-100">เข้าสู่ระบบ</button>
 </form>
 </div>
 </div>
 </div>
 </div>
 </div>
</body>

</html>
