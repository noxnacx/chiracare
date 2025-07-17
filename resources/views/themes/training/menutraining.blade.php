<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3" class="brand-link">
        <img src="{{ URL::asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Chiracare</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ URL::asset('dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    หน่วยฝึก
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- แดชบอร์ด -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>แดชบอร์ด</p>
                    </a>
                </li>

                <!-- รายชื่อทหาร -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>รายชื่อทหาร</p>
                    </a>
                </li>

                <!-- ตารางนัดหมาย -->
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>ตารางนัดหมาย</p>
                    </a>
                </li>

                <!-- ประวัติการรักษา -->
                <li class="nav-item menu-open">
                    <a href="{{ route('hospital.statistics') }}" class="nav-link">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>ประวัติการรักษา</p>
                    </a>
                </li>

                <!-- สถิติและรายงาน -->
                <li class="nav-item menu-open">
                    <a href="/admin/hospital/static" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>สถิติและรายงาน</p>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>