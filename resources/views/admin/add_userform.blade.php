<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 3px solid #f0f0f0;
        }

        .tab {
            padding: 15px 30px;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            color: #666;
        }

        .tab.active {
            color: #4CAF50;
            border-bottom-color: #4CAF50;
            font-weight: bold;
        }

        .tab:hover {
            background: #f8f9fa;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 2px;
            white-space: nowrap;
        }

        .btn-form {
            padding: 12px 30px;
            font-size: 16px;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 67, 54, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 152, 0, 0.3);
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            min-width: 800px;
        }

        .users-table th,
        .users-table td {
            padding: 15px 10px;
            text-align: left;
            white-space: nowrap;
        }

        .users-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .users-table td {
            border-bottom: 1px solid #f0f0f0;
        }

        .users-table tr:hover {
            background: #f8f9fa;
        }

        .role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .role-admin { background: #e91e63; }
        .role-opd { background: #2196F3; }
        .role-ipd { background: #9C27B0; }
        .role-er { background: #F44336; }
        .role-training_unit { background: #4CAF50; }
        .role-adminhospital { background:rgb(192, 189, 16); }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
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

        .training-unit-field {
            display: none;
        }

        .training-unit-field.show {
            display: block;
        }

        .search-box {
            margin-bottom: 20px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
        }

        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5em;
        }

        .modal-body {
            padding: 30px;
        }

        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users"></i> User Management System</h1>
            <p>จัดการผู้ใช้งานในระบบ Training Unit</p>
        </div>

        <div class="content">
            <div class="tabs">
                <button class="tab active" onclick="showTab('users-list')">
                    <i class="fas fa-list"></i> รายการผู้ใช้
                </button>
                <button class="tab" onclick="showTab('add-user')">
                    <i class="fas fa-user-plus"></i> เพิ่มผู้ใช้ใหม่
                </button>
                <button class="tab" onclick="showTab('statistics')">
                    <i class="fas fa-chart-bar"></i> สถิติ
                </button>
            </div>

            <div class="alert success" id="success-alert">
                <i class="fas fa-check-circle"></i> ดำเนินการสำเร็จ!
            </div>

            <div class="alert error" id="error-alert">
                <i class="fas fa-exclamation-circle"></i> เกิดข้อผิดพลาด!
            </div>

            <!-- Tab: Users List -->
            <div id="users-list" class="tab-content active">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-input" placeholder="ค้นหาผู้ใช้..." onkeyup="searchUsers()">
                </div>

                <div class="table-container">
                    <table class="users-table" id="users-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Username</th>
                                <th><i class="fas fa-shield-alt"></i> Role</th>
                                <th><i class="fas fa-building"></i> Training Unit</th>
                                <th><i class="fas fa-calendar"></i> สร้างเมื่อ</th>
                                <th><i class="fas fa-cogs"></i> จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td><span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                <td>{{ $user->trainingUnit->unit_name ?? '-' }}</td>
                                <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <button class="btn btn-warning" onclick="editUser({{ $user->id }})">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Add User -->
            <div id="add-user" class="tab-content">
                <form id="user-form" onsubmit="addUser(event)">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">
                                <i class="fas fa-user"></i> Username *
                            </label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">
                                <i class="fas fa-lock"></i> Password *
                            </label>
                            <input type="password" id="password" name="password" required minlength="8">
                        </div>

                        <div class="form-group">
                            <label for="role">
                                <i class="fas fa-shield-alt"></i> Role *
                            </label>
                            <select id="role" name="role" required onchange="toggleTrainingUnit()">
                                <option value="">เลือก Role</option>
                                <option value="admin">Admin</option>
                                <option value="adminhospital">AdminHospital</option>
                                <option value="opd">OPD</option>
                                <option value="ipd">IPD</option>
                                <option value="er">ER</option>
                                <option value="training_unit">Training Unit</option>
                            </select>
                        </div>

                        <div class="form-group training-unit-field" id="training-unit-field">
                            <label for="training_unit_id">
                                <i class="fas fa-building"></i> Training Unit *
                            </label>
                            <select id="training_unit_id" name="training_unit_id">
                                <option value="">เลือก Training Unit</option>
                                @foreach($trainingUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-form">
                        <i class="fas fa-plus"></i> เพิ่มผู้ใช้
                    </button>
                </form>
            </div>

            <!-- Tab: Statistics -->
            <div id="statistics" class="tab-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3 id="total-users">{{ $users->count() }}</h3>
                        <p><i class="fas fa-users"></i> ผู้ใช้ทั้งหมด</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="admin-count">{{ $users->where('role', 'admin')->count() }}</h3>
                        <p><i class="fas fa-user-shield"></i> Admin</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="training-unit-count">{{ $users->where('role', 'training_unit')->count() }}</h3>
                        <p><i class="fas fa-graduation-cap"></i> Training Unit</p>
                    </div>
                    <div class="stat-card">
                        <h3 id="other-roles-count">{{ $users->whereNotIn('role', ['admin', 'training_unit'])->count() }}</h3>
                        <p><i class="fas fa-user-friends"></i> Other Roles</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2><i class="fas fa-edit"></i> แก้ไขผู้ใช้</h2>
            </div>
            <div class="modal-body">
                <form id="edit-user-form" onsubmit="updateUser(event)">
                    <input type="hidden" id="edit-user-id" name="user_id">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit-username">
                                <i class="fas fa-user"></i> Username *
                            </label>
                            <input type="text" id="edit-username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-password">
                                <i class="fas fa-lock"></i> รหัสผ่านใหม่ (เว้นว่างถ้าไม่ต้องการเปลี่ยน)
                            </label>
                            <input type="password" id="edit-password" name="password" minlength="8">
                        </div>

                        <div class="form-group">
                            <label for="edit-role">
                                <i class="fas fa-shield-alt"></i> Role *
                            </label>
                            <select id="edit-role" name="role" required onchange="toggleEditTrainingUnit()">
                                <option value="">เลือก Role</option>
                                <option value="admin">Admin</option>
                                <option value="adminhospital">AdminHospital</option>
                                <option value="opd">OPD</option>
                                <option value="ipd">IPD</option>
                                <option value="er">ER</option>
                                <option value="training_unit">Training Unit</option>
                            </select>
                        </div>

                        <div class="form-group training-unit-field" id="edit-training-unit-field">
                            <label for="edit-training-unit-id">
                                <i class="fas fa-building"></i> Training Unit *
                            </label>
                            <select id="edit-training-unit-id" name="training_unit_id">
                                <option value="">เลือก Training Unit</option>
                                @foreach($trainingUnits as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="margin-right: 10px; background: #6c757d;">
                            <i class="fas fa-times"></i> ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-warning btn-form">
                            <i class="fas fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/Hide tabs
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Show selected tab content
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        // Toggle training unit field
        function toggleTrainingUnit() {
            const role = document.getElementById('role').value;
            const trainingUnitField = document.getElementById('training-unit-field');
            const trainingUnitSelect = document.getElementById('training_unit_id');

            if (role === 'training_unit') {
                trainingUnitField.classList.add('show');
                trainingUnitSelect.required = true;
            } else {
                trainingUnitField.classList.remove('show');
                trainingUnitSelect.required = false;
                trainingUnitSelect.value = '';
            }
        }

        // Add user function
        function addUser(event) {
            event.preventDefault();

            const formData = new FormData(event.target);

            // ใช้ CSRF token จาก meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            fetch('/api/users', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'เพิ่มผู้ใช้สำเร็จ!');
                    event.target.reset();
                    toggleTrainingUnit();

                    // รีเฟรชหน้าหลังจาก 1 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    let errorMessage = 'เกิดข้อผิดพลาด!';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        if (Array.isArray(firstError)) {
                            errorMessage = firstError[0];
                        }
                    }
                    showAlert('error', errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ!');
            });
        }

        // Search users
        function searchUsers() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const rows = document.querySelectorAll('#users-tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Edit user function
        function editUser(userId) {
            // ดึงข้อมูล user จาก API
            fetch(`/api/users/${userId}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;

                        // เติมข้อมูลลงใน form
                        document.getElementById('edit-user-id').value = user.id;
                        document.getElementById('edit-username').value = user.username;
                        document.getElementById('edit-password').value = ''; // เคลียร์รหัสผ่าน
                        document.getElementById('edit-role').value = user.role;

                        // จัดการ training unit
                        if (user.role === 'training_unit') {
                            document.getElementById('edit-training-unit-field').classList.add('show');
                            document.getElementById('edit-training-unit-id').required = true;
                            document.getElementById('edit-training-unit-id').value = user.training_unit_id || '';
                        } else {
                            document.getElementById('edit-training-unit-field').classList.remove('show');
                            document.getElementById('edit-training-unit-id').required = false;
                            document.getElementById('edit-training-unit-id').value = '';
                        }

                        // แสดง modal
                        document.getElementById('editModal').style.display = 'block';
                    } else {
                        showAlert('error', 'ไม่สามารถดึงข้อมูลผู้ใช้ได้!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ!');
                });
        }

        // Toggle training unit field in edit modal
        function toggleEditTrainingUnit() {
            const role = document.getElementById('edit-role').value;
            const trainingUnitField = document.getElementById('edit-training-unit-field');
            const trainingUnitSelect = document.getElementById('edit-training-unit-id');

            if (role === 'training_unit') {
                trainingUnitField.classList.add('show');
                trainingUnitSelect.required = true;
            } else {
                trainingUnitField.classList.remove('show');
                trainingUnitSelect.required = false;
                trainingUnitSelect.value = '';
            }
        }

        // Update user function
        function updateUser(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const userId = formData.get('user_id');

            // ลบ user_id ออกจาก formData เพราะไม่ต้องส่งไปกับ API
            formData.delete('user_id');

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
                formData.append('_method', 'PUT'); // สำหรับ Laravel PUT method
            }

            fetch(`/api/users/${userId}`, {
                method: 'POST', // ใช้ POST แต่มี _method=PUT
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'แก้ไขผู้ใช้สำเร็จ!');
                    closeEditModal();

                    // รีเฟรชหน้าหลังจาก 1 วินาที
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    let errorMessage = 'เกิดข้อผิดพลาด!';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0];
                        if (Array.isArray(firstError)) {
                            errorMessage = firstError[0];
                        }
                    }
                    showAlert('error', errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ!');
            });
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('edit-user-form').reset();
            document.getElementById('edit-training-unit-field').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }

        // Delete user function
        function deleteUser(userId) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');

                fetch(`/api/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : '',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'ลบผู้ใช้สำเร็จ!');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('error', 'ไม่สามารถลบผู้ใช้ได้!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ!');
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

       // Initialize
       document.addEventListener('DOMContentLoaded', function() {
            // ตรวจสอบสิทธิ์การเข้าถึง
            fetch('/api/check-auth')
                .then(response => response.json())
                .then(data => {
                    if (!data.success || !data.authenticated) {
                        // ไม่ได้ login
                        window.location.href = '/login';
                    } else if (data.user.role !== 'admin') {
                        // ไม่ใช่ admin
                        alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะ Admin เท่านั้น)');
                        window.location.href = '/login';
                    }
                })
                .catch(error => {
                    console.error('Error checking auth:', error);
                    window.location.href = '/login';
                });

            console.log('Page loaded successfully');
        });
    </script>
</body>
</html>