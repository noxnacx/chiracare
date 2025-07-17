<!DOCTYPE html>
<html lang="en">
@include('themes.head')

<body class="hold-transition layout-fixed">
    <div class="wrapper">
        @include('themes.admin.navbaradmin')
        @include('themes.admin.menuadmin')

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">

                    <!-- Section: ผลัด -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold m-0">ผลัด</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRotationModal">
                            เพิ่มผลัด
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="rotationTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ชื่อหน่วยฝึก</th>
                                            <th>สถานะ</th>
                                            <th class="text-center">การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rotations as $rotation)
                                            <tr>
                                                <td>{{ $rotation->rotation_name }}</td>
                                                <td>
                                                    <span
                                                        class="{{ $rotation->status == 'active' ? 'text-success' : 'text-danger' }}">
                                                        ●
                                                        {{ $rotation->status == 'active' ? 'พร้อมใช้งาน' : 'ไม่พร้อมใช้งาน' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- ปุ่มแก้ไข -->
                                                    <button class="btn btn-warning btn-sm"
                                                        onclick="editRotation('{{ $rotation->id }}')">
                                                        แก้ไข
                                                    </button>

                                                    <!-- ปุ่มลบ -->
                                                    <form action="{{ url('/rotation_training/delete/' . $rotation->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold m-0">หน่วยฝึก</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTrainingUnitModal">
                            + เพิ่มหน่วยฝึก
                        </button>
                    </div>

                    <div class="card mt-4">

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="unitTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ชื่อหน่วยฝึก</th>
                                            <th>สถานะ</th>
                                            <th class="text-center">การจัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($units as $unit)
                                            <tr>
                                                <td>{{ $unit->unit_name }}</td>
                                                <td>
                                                    <span
                                                        class="{{ $unit->status == 'active' ? 'text-success' : 'text-danger' }}">
                                                        ● {{ $unit->status == 'active' ? 'พร้อมใช้งาน' : 'ไม่พร้อมใช้งาน' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <!-- ปุ่มแก้ไข -->
                                                    <button class="btn btn-warning btn-sm"
                                                        onclick="editTrainingUnit('{{ $unit->id }}')">
                                                        แก้ไข
                                                    </button>

                                                    <!-- ปุ่มลบ -->
                                                    <form
                                                        action="{{ url('/rotation_training/delete-training/' . $unit->id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('ยืนยันการลบ?')">ลบ</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div><!-- /.container-fluid -->
            </div>
        </div>

        @include('themes.admin.footeradmin')

        <!-- Modal สำหรับเพิ่ม Rotation -->
        <div class="modal fade" id="addRotationModal" tabindex="-1" aria-labelledby="addRotationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRotationModalLabel">เพิ่มผลัด</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/rotation_training/store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>ชื่อผลัด</label>
                                <input type="text" class="form-control" name="rotation_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">เพิ่ม</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับแก้ไข Rotation -->
        <div class="modal fade" id="editRotationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขผลัด</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editRotationForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editRotationId" name="rotation_id">
                            <div class="mb-3">
                                <label>ชื่อผลัด</label>
                                <input type="text" class="form-control" id="editRotationName" name="rotation_name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>สถานะ</label>
                                <select class="form-control" id="editRotationStatus" name="status">
                                    <option value="active">พร้อมใช้งาน</option>
                                    <option value="inactive">ไม่พร้อมใช้งาน</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal สำหรับเพิ่ม Training Unit -->
        <div class="modal fade" id="addTrainingUnitModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">เพิ่มหน่วยฝึก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/rotation_training/store-training') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>ชื่อหน่วยฝึก</label>
                                <input type="text" class="form-control" name="unit_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">เพิ่ม</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal สำหรับแก้ไข Training Unit -->
        <div class="modal fade" id="editTrainingUnitModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขหน่วยฝึก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editTrainingUnitForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="editTrainingUnitId" name="trainingunit_id">
                            <div class="mb-3">
                                <label>ชื่อหน่วยฝึก</label>
                                <input type="text" class="form-control" id="editTrainingUnitName" name="unit_name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label>สถานะ</label>
                                <select class="form-control" id="editTrainingUnitStatus" name="status">
                                    <option value="active">พร้อมใช้งาน</option>
                                    <option value="inactive">ไม่พร้อมใช้งาน</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- รวมสคริปต์ DataTables และ AdminLTE -->
        @include('themes.script')

        <!-- Bootstrap 5 JS (ต้องโหลดหลัง jQuery) -->

        <!-- Script สำหรับแก้ไข Rotation -->
        <script>
            function editRotation(id) {
                $.get('/rotation_training/edit/' + id, function (data) {
                    $('#editRotationId').val(data.id);
                    $('#editRotationName').val(data.rotation_name);
                    $('#editRotationStatus').val(data.status);
                    $('#editRotationForm').attr('action', '/rotation_training/update/' + data.id);
                    $('#editRotationModal').modal('show');
                });
            }
        </script>
        <!-- Script สำหรับแก้ไข Training Unit -->
        <script>
            function editTrainingUnit(id) {
                $.get('/rotation_training/edit-training/' + id, function (data) {
                    if (!data || !data.id) {
                        alert("เกิดข้อผิดพลาด: ไม่พบข้อมูล");
                        return;
                    }

                    $('#editTrainingUnitId').val(data.id);
                    $('#editTrainingUnitName').val(data.unit_name);
                    $('#editTrainingUnitStatus').val(data.status);
                    $('#editTrainingUnitForm').attr('action', '/rotation_training/update-training/' + data.id);
                    $('#editTrainingUnitModal').modal('show');
                }).fail(function () {
                    alert("เกิดข้อผิดพลาด: ไม่สามารถดึงข้อมูลได้");
                });
            }
        </script>

    </div>

</body>

</html>