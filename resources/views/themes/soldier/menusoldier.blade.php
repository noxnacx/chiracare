<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="brand-link">
        <img src="{{ URL::asset('dist/img/AdminLTELogo.png')}}" alt="Chiracare Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Chiracare</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- หน้าแรก (โปรไฟล์) -->
                <li class="nav-item">
                    <a href="{{ route('profile.inv.soldier', ['id' => $soldier->id]) }}" class="nav-link">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>หน้าแรก (โปรไฟล์)</p>
                    </a>
                </li>

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('soldier.dashboard', ['id' => $soldier->id]) }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ทำแบบประเมิน -->
                <li class="nav-item">
                        <a href="{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}"class="nav-link" id="assessmentButton">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>ทำแบบประเมิน</p>
                    </a>
                </li>

                <!-- ประวัติแบบประเมิน -->
                <li class="nav-item">
                <a href="{{ route('assessment.history', ['soldierId' => $soldier->id]) }}" class="nav-link">
                    <i class="nav-icon fas fa-clipboard-check"></i>
                    <p>ประวัติการทำแบบประเมิน</p>
                    </a>
                </li>

                {{-- ตรวจสอบก่อนว่ามีตัวแปร $soldier อยู่หรือไม่ --}}
                @if(isset($soldier))
                <li class="nav-item">
                    <a href="{{ route('soldier.my_appointments', ['id' => $soldier->id]) }}" class="nav-link {{ request()->is('soldier/*/my-appointments') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>นัดหมายของฉัน</p>
                    </a>
                    </li>
                @endif

                <!-- แก้ไขข้อมูลส่วนตัว -->
                <li class="nav-item">
                    <a href="{{ route('soldier.edit_personal_info', ['id' => $soldier->id]) }}" class="nav-link">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>แก้ไขข้อมูลส่วนตัว</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                <a href="{{ route('soldier.logout') }}" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>ออกจากระบบ</p>
                    </a>
                    <form id="logout-form" action="{{ route('soldier.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
var consentModal = new bootstrap.Modal(document.getElementById('consentModal'));
var assessmentButton = document.getElementById("assessmentButton");
var soldierId = "{{ $soldier->id }}"; // ใช้ soldier_id ของทหารแต่ละคน
var userConsentKey = "userConsent_" + soldierId; // เก็บค่าการยอมรับแยกตาม soldier_id

// ตรวจสอบว่าผู้ใช้เคยกดยอมรับไปแล้วหรือยัง
if (localStorage.getItem(userConsentKey) === "accepted") {
    assessmentButton.removeAttribute("disabled"); // ปลดล็อคปุ่มทำแบบประเมิน
} else {
    // แสดง popup ทันทีเมื่อเข้าหน้าเว็บ (ถ้ายังไม่เคยกดยอมรับ)
    consentModal.show();

    // ถ้ายังไม่กดยอมรับ ให้แสดง popup เมื่อกดปุ่มทำแบบประเมินอีกครั้ง
    assessmentButton.addEventListener("click", function(event) {
        event.preventDefault(); // ป้องกันการไปยังลิงก์แบบประเมิน
        consentModal.show(); // แสดง popup ยืนยัน
    });
}

// เมื่อกดยอมรับ ให้บันทึก soldier_id ลงใน Local Storage และปลดล็อคปุ่ม
document.getElementById("acceptConsent").addEventListener("click", function() {
    localStorage.setItem(userConsentKey, "accepted");
    assessmentButton.removeAttribute("disabled"); // ปลดล็อคปุ่มทำแบบประเมิน
    consentModal.hide(); // ปิด popup
    window.location.href = "{{ route('soldier.view_assessment', ['id' => $soldier->id]) }}"; // ไปหน้าแบบประเมิน
});

// เมื่อกดไม่ยอมรับ ให้ปิด popup ไปก่อน (แต่จะยังถูกล็อคอยู่)
document.getElementById("declineConsent").addEventListener("click", function() {
    consentModal.hide();
});

// ปุ่มรีเซ็ตการยอมรับ (ลบค่าของ soldier_id เฉพาะคนนี้)
document.getElementById("resetConsent").addEventListener("click", function() {
    localStorage.removeItem(userConsentKey);
    alert("ล้างข้อมูลการยอมรับของทหารหมายเลข " + soldierId + " เรียบร้อยแล้ว! รีเฟรชหน้าเว็บเพื่อดูผลลัพธ์");
});
});


</script>
