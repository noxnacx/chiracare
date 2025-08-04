<!-- themes/admin-hospital/navbarhospital.blade.php -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('/admin-hospital/dashboard') }}" class="nav-link">หน้าหลัก</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- 🚨 ปรับปรุง Notification Bell แสดงสรุปผู้ป่วย -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" id="notificationBell">
                <i class="far fa-bell"></i>
                @php
                    $totalUnread = Auth::user()->today_unread_patients_count + Auth::user()->customUnreadNotifications()->where('type', '!=', 'new_patient')->count();
                @endphp
                @if($totalUnread > 0)
                    <span class="badge badge-danger navbar-badge" id="notificationCount">
                        {{ $totalUnread }}
                    </span>
                @endif
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- Header สรุป -->
                <span class="dropdown-item dropdown-header">
                    <strong>การแจ้งเตือนวันนี้ ({{ $totalUnread }})</strong>
                </span>

                <div class="dropdown-divider"></div>

                <!-- สรุปผู้ป่วยตาม Risk Level -->
                @if(Auth::user()->today_patient_summary->count() > 0)
                    <div class="dropdown-item-text">
                        <h6 class="mb-2"><i class="fas fa-user-injured mr-1"></i> <strong>ผู้ป่วยใหม่วันนี้</strong></h6>
                    </div>

                    @foreach(Auth::user()->today_patient_summary as $riskLevel => $summary)
                        <div class="dropdown-item notification-summary-item" data-risk="{{ $riskLevel }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($riskLevel === 'red')
                                        <span class="badge badge-danger mr-2">🔴</span>
                                        <span class="text-danger"><strong>ฉุกเฉิน</strong></span>
                                    @elseif($riskLevel === 'yellow')
                                        <span class="badge badge-warning mr-2">🟡</span>
                                        <span class="text-warning"><strong>เฝ้าระวัง</strong></span>
                                    @elseif($riskLevel === 'green')
                                        <span class="badge badge-success mr-2">🟢</span>
                                        <span class="text-success"><strong>ปกติ</strong></span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge badge-secondary">{{ $summary['count'] }} คน</span>
                                </div>
                            </div>

                            <!-- แสดงรายชื่อล่าสุด -->
                            @if($summary['latest'])
                                @php $latestData = $summary['latest']->data; @endphp
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-user mr-1"></i>
                                    ล่าสุด: {{ $latestData['soldier_name'] ?? 'ไม่ระบุชื่อ' }}
                                    <span class="float-right">{{ $summary['latest']->created_at->format('H:i') }}</span>
                                </small>
                            @endif
                        </div>
                    @endforeach

                    <div class="dropdown-divider"></div>
                @endif

                <!-- การแจ้งเตือนอื่น ๆ -->
                @if(Auth::user()->other_notifications->count() > 0)
                    <div class="dropdown-item-text">
                        <h6 class="mb-2"><i class="fas fa-bell mr-1"></i> <strong>การแจ้งเตือนอื่น ๆ</strong></h6>
                    </div>

                    @foreach(Auth::user()->other_notifications as $notification)
                        <a href="#" class="dropdown-item notification-item" data-id="{{ $notification->id }}"
                            data-priority="{{ $notification->priority }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 mr-2">
                                    @if($notification->type === 'new_appointment')
                                        <i class="fas fa-calendar-plus text-info"></i>
                                    @elseif($notification->type === 'patient_admit')
                                        <i class="fas fa-hospital text-danger"></i>
                                    @elseif($notification->type === 'treatment_completed')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-info-circle text-muted"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="dropdown-item-title mb-1">
                                        {{ Str::limit($notification->title, 30) }}
                                    </h6>
                                    <p class="text-sm mb-1">{{ Str::limit($notification->message, 45) }}</p>
                                    <p class="text-xs text-muted mb-0">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                @endif

                @if($totalUnread === 0)
                    <div class="dropdown-item text-center text-muted">
                        <i class="fas fa-check-circle mr-1"></i>
                        ไม่มีการแจ้งเตือนใหม่
                    </div>
                    <div class="dropdown-divider"></div>
                @endif

                <!-- Footer Actions -->
                <div class="dropdown-item dropdown-footer d-flex justify-content-between">
                    <a href="/notifications" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye mr-1"></i> ดูทั้งหมด
                    </a>
                    @if($totalUnread > 0)
                        <button class="btn btn-sm btn-outline-success" onclick="markAllAsRead()">
                            <i class="fas fa-check-double mr-1"></i> อ่านทั้งหมด
                        </button>
                    @endif
                </div>
            </div>
        </li>

        <!-- User Account Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user"></i>
                {{ Auth::user()->username ?? 'ผู้ใช้' }}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                <a href="/admin-hospital/dashboard" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> โปรไฟล์
                </a>
                <div class="dropdown-divider"></div>
                <a href="/logout" class="dropdown-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> ออกจากระบบ
                </a>
                <form id="logout-form" action="/logout" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>

<!-- 🚨 CSS สำหรับ Patient Summary -->
<style>
    .notification-summary-item {
        background-color: #f8f9fa;
        border-left: 4px solid;
        margin-bottom: 2px;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }

    .notification-summary-item[data-risk="red"] {
        border-left-color: #dc3545;
        background-color: #f8d7da;
    }

    .notification-summary-item[data-risk="yellow"] {
        border-left-color: #ffc107;
        background-color: #fff3cd;
    }

    .notification-summary-item[data-risk="green"] {
        border-left-color: #28a745;
        background-color: #d4edda;
    }

    .notification-summary-item:hover {
        opacity: 0.8;
        cursor: pointer;
    }

    .notification-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
        padding: 8px 12px;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        border-left-color: #007bff;
    }

    .dropdown-menu {
        max-width: 400px;
        min-width: 350px;
        max-height: 500px;
        overflow-y: auto;
    }

    .dropdown-item-title {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    /* Badge Animation */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .navbar-badge {
        animation: pulse 2s infinite;
    }

    /* Risk Level Badges */
    .badge {
        font-size: 11px;
    }
</style>

<!-- 🚨 JavaScript สำหรับ Patient Summary -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // แก้ไขปัญหา Dropdown
        $('#notificationBell').on('click', function (e) {
            e.preventDefault();
            $(this).next('.dropdown-menu').toggle();
        });

        // ปิด dropdown เมื่อคลิกข้างนอก
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').hide();
            }
        });

        // คลิกดู detail ของผู้ป่วยแต่ละ risk level
        $('.notification-summary-item').on('click', function () {
            const riskLevel = $(this).data('risk');
            // Redirect ไปหน้าที่แสดงผู้ป่วยตาม risk level
            window.location.href = `/hospital/appointments`;
        });

        // ตรวจสอบการแจ้งเตือนใหม่ทุก 30 วินาที
        setInterval(checkNewNotifications, 30000);

        // Event listener สำหรับคลิกการแจ้งเตือน
        $('.notification-item').on('click', function (e) {
            e.preventDefault();
            const notificationId = $(this).data('id');
            markAsRead(notificationId);
        });
    });

    // ฟังก์ชันตรวจสอบการแจ้งเตือนใหม่
    async function checkNewNotifications() {
        try {
            const response = await fetch('/api/notifications/patient-summary');
            if (!response.ok) return;

            const data = await response.json();

            // อัปเดตจำนวนรวม
            updateNotificationCount(data.counts.grand_total);

            // แสดง toast ถ้ามีผู้ป่วยใหม่
            if (data.today_patient_summary && Object.keys(data.today_patient_summary).length > 0) {
                showPatientSummaryToast(data.today_patient_summary);
            }

        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    // แสดง Toast สำหรับสรุปผู้ป่วย
    function showPatientSummaryToast(summary) {
        let message = 'ผู้ป่วยใหม่:\n';

        Object.entries(summary).forEach(([riskLevel, data]) => {
            const emoji = riskLevel === 'red' ? '🔴' :
                riskLevel === 'yellow' ? '🟡' : '🟢';
            const text = riskLevel === 'red' ? 'ฉุกเฉิน' :
                riskLevel === 'yellow' ? 'เฝ้าระวัง' : 'ปกติ';
            message += `${emoji} ${text}: ${data.count} คน\n`;
        });

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '📋 สรุปผู้ป่วยวันนี้',
                text: message,
                icon: 'info',
                position: 'top-end',
                showConfirmButton: false,
                timer: 8000,
                timerProgressBar: true,
                toast: true
            });
        }
    }

    // อัปเดตจำนวนการแจ้งเตือน
    function updateNotificationCount(count) {
        const badge = $('#notificationCount');
        if (count > 0) {
            if (badge.length) {
                badge.text(count);
            } else {
                $('#notificationBell').append(`<span class="badge badge-danger navbar-badge" id="notificationCount">${count}</span>`);
            }
        } else {
            badge.remove();
        }
    }

    // ทำเครื่องหมายอ่านการแจ้งเตือนเดี่ยว
    async function markAsRead(notificationId) {
        try {
            const response = await fetch(`/api/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.ok) {
                $(`.notification-item[data-id="${notificationId}"]`).css('opacity', '0.6');
                location.reload(); // Reload เพื่อ update count
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // ทำเครื่องหมายอ่านทั้งหมด
    async function markAllAsRead() {
        if (!confirm('ต้องการทำเครื่องหมายอ่านการแจ้งเตือนทั้งหมดหรือไม่?')) return;

        try {
            const response = await fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }
</script>