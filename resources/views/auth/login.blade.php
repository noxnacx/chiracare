<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - User Management System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .login-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .login-header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            font-size: 1.1em;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
            background: white;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
            z-index: 1;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .welcome-section {
            display: none;
            text-align: center;
            padding: 30px;
            animation: fadeIn 0.5s ease;
        }

        .welcome-section.show {
            display: block;
        }

        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
        }

        .user-info h3 {
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .info-item i {
            margin-right: 12px;
            width: 20px;
        }

        .role-badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            margin-left: 10px;
        }

        .role-admin { background: #e91e63; }
        .role-opd { background: #2196F3; }
        .role-ipd { background: #9C27B0; }
        .role-er { background: #F44336; }
        .role-training_unit { background: #4CAF50; }

        .btn-logout {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
        }

        .btn-dashboard {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33, 150, 243, 0.3);
            color: white;
            text-decoration: none;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Header -->
        <div class="login-header">
            <h1><i class="fas fa-lock"></i> เข้าสู่ระบบ</h1>
            <p>User Management System</p>
        </div>

        <!-- Login Form -->
        <div class="login-body" id="login-form-section">
            <div class="alert success" id="success-alert">
                <i class="fas fa-check-circle"></i> เข้าสู่ระบบสำเร็จ!
            </div>

            <div class="alert error" id="error-alert">
                <i class="fas fa-exclamation-circle"></i> เกิดข้อผิดพลาด!
            </div>

            <form id="login-form" onsubmit="handleLogin(event)">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" required placeholder="กรอก Username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="กรอก Password">
                    </div>
                </div>

                <button type="submit" class="btn-login" id="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span id="login-text">เข้าสู่ระบบ</span>
                    <i class="fas fa-spinner loading" id="login-loading"></i>
                </button>
            </form>
        </div>

        <!-- Welcome Section (Hidden by default) -->
        <div class="welcome-section" id="welcome-section">
            <h2><i class="fas fa-check-circle" style="color: #4CAF50;"></i> เข้าสู่ระบบสำเร็จ!</h2>
            
            <div class="user-info" id="user-info">
                <h3><i class="fas fa-user-circle"></i> ข้อมูลผู้ใช้</h3>
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <span>Username: <strong id="display-username"></strong></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Role: <span id="display-role" class="role-badge"></span></span>
                </div>
                <div class="info-item" id="training-unit-info" style="display: none;">
                    <i class="fas fa-building"></i>
                    <span>Training Unit: <strong id="display-training-unit"></strong></span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="/admin/users" class="btn-dashboard">
                    <i class="fas fa-tachometer-alt"></i> ไปยัง Dashboard
                </a>
                <button onclick="handleLogout()" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </button>
            </div>
        </div>
    </div>

    <script>
        // Check if user is already logged in
        document.addEventListener('DOMContentLoaded', function() {
    // checkAuthStatus(); // ✅ Comment ออก
         console.log('Login page ready');
    });

      // ✅ checkAuthStatus และ handleLogout ใช้ API routes เดิม
function checkAuthStatus() {
    fetch('/api/check-auth')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.authenticated) {
                // ✅ ถ้า user login อยู่แล้ว redirect ไป dashboard
                const dashboardUrl = getDashboardUrl(data.user.role);
                window.location.href = dashboardUrl;
            }
        })
        .catch(error => {
            console.error('Error checking auth:', error);
        });
    }

        // Handle login form submission
        function handleLogin(event) {
    event.preventDefault();
    
    const loginBtn = document.getElementById('login-btn');
    const loginText = document.getElementById('login-text');
    const loginLoading = document.getElementById('login-loading');
    
    // Show loading
    loginBtn.disabled = true;
    loginText.textContent = 'กำลังเข้าสู่ระบบ...';
    loginLoading.classList.add('show');
    
    const formData = new FormData(event.target);
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (csrfToken) {
        formData.append('_token', csrfToken.getAttribute('content'));
    }

    fetch('/api/login', { // ✅ ใช้ API route เดิม
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            
            // ✅ Redirect ไปหน้า dashboard ทันที
            setTimeout(() => {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    // Fallback
                    window.location.href = getDashboardUrl(data.user.role);
                }
            }, 1000);
        } else {
            showAlert('error', data.message);
            resetLoginButton();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ!');
        resetLoginButton();
    });
    }

        // Reset login button
        function resetLoginButton() {
            const loginBtn = document.getElementById('login-btn');
            const loginText = document.getElementById('login-text');
            const loginLoading = document.getElementById('login-loading');
            
            loginBtn.disabled = false;
            loginText.textContent = 'เข้าสู่ระบบ';
            loginLoading.classList.remove('show');
        }

        // Show welcome section
        function showWelcomeSection(user) {
            document.getElementById('login-form-section').style.display = 'none';
            document.getElementById('welcome-section').classList.add('show');
            
            // Display user information
            document.getElementById('display-username').textContent = user.username;
            
            const roleElement = document.getElementById('display-role');
            roleElement.textContent = getRoleDisplayName(user.role);
            roleElement.className = `role-badge role-${user.role}`;
            
            // Show training unit if applicable
            if (user.training_unit) {
                document.getElementById('training-unit-info').style.display = 'flex';
                document.getElementById('display-training-unit').textContent = user.training_unit;
            } else {
                document.getElementById('training-unit-info').style.display = 'none';
            }
        }

        // Get role display name
        function getRoleDisplayName(role) {
            const roleNames = {
                'admin': 'ผู้ดูแลระบบ',
                'opd': 'OPD',
                'ipd': 'IPD', 
                'er': 'ER',
                'training_unit': 'หน่วยฝึกอบรม'
            };
            return roleNames[role] || role;
        }

        function handleLogout() {
    if (confirm('คุณต้องการออกจากระบบหรือไม่?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        fetch('/api/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'เกิดข้อผิดพลาดในการออกจากระบบ!');
        });
    }
    }

        // Show alert
        function showAlert(type, message) {
            const alert = document.getElementById(`${type}-alert`);
            if (alert) {
                alert.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
                alert.style.display = 'block';

                setTimeout(() => {
                    alert.style.display = 'none';
                }, 3000);
            }
        }

        // Enter key support
        document.addEventListener('keypress', function(event) {
            if (event.key === 'Enter' && document.getElementById('login-form-section').style.display !== 'none') {
                event.preventDefault();
                document.getElementById('login-form').dispatchEvent(new Event('submit'));
            }
        });

        // ✅ ฟังก์ชันช่วยสำหรับ fallback
    function getDashboardUrl(role) {
    const dashboardUrls = {
        'admin': '/dashboard-admin',
        'er': '/er/dashboard',
        'ipd': '/ipd/dashboard', 
        'opd': '/opd/dashboard',
        'training_unit': '/training/dashboard'
    };
    return dashboardUrls[role] || '/dashboard';
    }

    </script>
</body>
</html>