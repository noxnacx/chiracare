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
                    Admin
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="/" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>แดชบอร์ด</p>
                    </a>
                </li>


                <li class="nav-item menu-open">
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>ดูรายชื่อทหารทั้งหมด</p>
                    </a>
                </li>



                <li class="nav-item menu-open">
                    <a href="/soldier/create" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>เพิ่มข้อมูลทหาร</p>
                    </a>
                </li>

                <li class="nav-item menu-open">
                    <a href="/rotation_training" class="nav-link">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>เพิ่มผลัด/หน่วย</p>
                    </a>
                </li>

                <!-- สถิติและรายงาน -->
                <li class="nav-item menu-open">
                    <a href="/add/users" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>เพิ่มแก้ไขUser</p>
                    </a>
                </li>
            </ul>

        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>