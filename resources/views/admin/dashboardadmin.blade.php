<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        @include('themes.admin.navbaradmin')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('themes.admin.menuadmin')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <!-- First Card -->
                        <!-- ในไฟล์ dashboardadmin.blade.php -->

                        <!-- จำนวนทหารทั้งหมด -->
                        <div class="col-md-3">
                            <a href="#" class="text-decoration-none text-dark">
                                <div class="card shadow-sm custom-card">
                                    <h5>จำนวนทหารทั้งหมด</h5>
                                    <h3>
                                        {{ $totalSoldiers }} <span
                                            style="font-size: 16px; font-weight: normal;">คน</span>
                                    </h3>
                                    <div class="custom-card-icon">
                                        <i class="fas fa-users" style="color: #10b981;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- จำนวนผลัดทั้งหมด -->
                        <div class="col-md-3">
                            <a href="#" class="text-decoration-none text-dark">
                                <div class="card shadow-sm custom-card">
                                    <h5>จำนวนผลัดทั้งหมด</h5>
                                    <h3>
                                        {{ $totalRotations }} <span
                                            style="font-size: 16px; font-weight: normal;">ผลัด</span>
                                    </h3>
                                    <div class="custom-card-icon">
                                        <i class="fas fa-sync" style="color: #10b981;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-md-3">
                            <a href="{{ url('training_unit/total') }}" class="text-decoration-none text-dark">
                                <div class="card shadow-sm custom-card">
                                    <h5>จำนวนหน่วยฝึกทั้งหมด</h5>
                                    <h3>
                                        {{ $totalTrainingUnits }} <span
                                            style="font-size: 16px; font-weight: normal;">หน่วยฝึก</span>
                                    </h3>
                                    <div class="custom-card-icon">
                                        <i class="fas fa-chalkboard-teacher" style="color: #10b981;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>

                    </div><!-- /.row -->
                    <h1 class="card-title" style="font-size: 28px; font-weight: bold;">รายชื่อทหาร</h1>
                    <br>

                    <div class="row mt-4">
                        <div class="col-12">

                            <div class="card">

                                <div class="card-body">
                                    <table class="table table-striped table-bordered data-table">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ - นามสกุล</th>
                                                <th>เลขบัตรประชาชน</th>
                                                <th>ผลัด</th>
                                                <th>หน่วยฝึก</th>
                                                <th>หน่วยต้นสังกัด</th>
                                                <th>การจัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($soldiers as $soldier)
                                                <tr>
                                                    <td>{{ $soldier->first_name }} {{ $soldier->last_name }}</td>
                                                    <td>{{ $soldier->soldier_id_card }}</td>
                                                    <td>{{ $soldier->rotation ? $soldier->rotation->rotation_name : 'ไม่มีข้อมูล' }}
                                                    </td>
                                                    <td>{{ $soldier->trainingUnit ? $soldier->trainingUnit->unit_name : 'ไม่มีข้อมูล' }}
                                                    </td>
                                                    <td>{{ $soldier->affiliated_unit }}</td>

                                                    <td class="text-center">

                                                        <a href="{{ route('soldier.view', ['id' => $soldier->id]) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>



                                                        <a href="{{ route('soldier.edit_soldier', $soldier->id) }}"
                                                            class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-edit"></i>
                                                        </a>


                                                        <form action="{{ route('soldier.delete_soldier', $soldier->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('ยืนยันการลบข้อมูลนี้?')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>

                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>





                </div><!-- /.container-fluid -->
            </div>

            <!-- Main content -->
            @yield('content')
            <!-- /.content -->
        </div><!-- /.content-wrapper -->

        @include('themes.admin.footeradmin')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>

    <!-- Inline CSS -->
    <style>
        .custom-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            background: #fff;
            position: relative;
            text-align: left;
        }

        .custom-card h5 {
            margin-bottom: 5px;
        }

        .custom-card h3 {
            font-weight: bold;
        }

        .custom-card-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            padding: 5px;
            border-radius: 50%;
        }

        .custom-card-icon i {
            font-size: 20px;
        }
    </style>

</body>

</html>

@include('themes.script')