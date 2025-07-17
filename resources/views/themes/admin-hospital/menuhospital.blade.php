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
                    Admin Hospital
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->

        <!-- <li class="nav-item menu-open">
                    <a href="{{ route('appointments.missed') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>ขาดการรักษา</p>
                    </a>
                </li>
                <li class="nav-item menu-open">
                    <a href="{{ route('appointments.success') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>นัดหมายสำเร็จ</p>
                    </a>
                </li> -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- แดชบอร์ด -->
                <li class="nav-item menu-open">
                    <a href="{{ route('dashboard.admin') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>แดชบอร์ด</p>
                    </a>
                </li>

                <!-- นัดหมายทหาร -->
                <li class="nav-item menu-open">
                    <a href="{{ url('hospital/appointments') }}" class="nav-link">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>นัดหมายทหาร</p>
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
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas  fa-user-nurse"></i>
                        <p>ส่งป่วยพิเศษ</p>
                    </a>
                </li>
                <!-- สถิติและรายงาน -->

            </ul>
        </nav>

        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>