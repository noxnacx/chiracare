<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        /* ✅ เพิ่มระยะห่างระหว่างลูกศรและตัวเลข */
    }

    .custom-pagination .btn {
        border: 1px solid #ccc;
        background-color: #f8f9fa;
        color: #333;
        transition: all 0.3s;
        padding: 5px 10px;
        /* ✅ ปรับขนาดปุ่มให้สมดุล */
    }

    .custom-pagination .btn:hover {
        background-color: #007bff;
        color: white;
    }

    .custom-pagination .btn:disabled {
        background-color: #ddd;
        color: #999;
    }

    .custom-pagination span {
        font-size: 16px;
        font-weight: bold;
        min-width: 50px;
        /* ✅ บังคับให้ตัวเลขมีพื้นที่ว่าง */
        text-align: center;
    }
</style>

<!-- ลำดับการโหลดที่ถูกต้อง -->
<!-- jQuery (AdminLTE) -->
<script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE -->
<script src="{{ asset('dist/js/adminlte.min.js')}}"></script>

<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js')}}"></script>

<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        if ($('.data-table').length) {
            let table = $('.data-table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "dom": '<"d-flex justify-content-between align-items-center mb-3"' +
                    '<"d-flex align-items-center gap-3"l>' +
                    '<"d-flex align-items-center"f>>' +
                    't' +
                    '<"d-flex justify-content-between align-items-center mt-2"' +
                    '<"d-flex align-items-center"i>' +
                    '<"d-flex align-items-center justify-content-end custom-pagination"p>>',
                "lengthMenu": [[5, 10, 20, 50, 100, -1], [5, 10, 20, 50, 100, "All"]],
                "language": {
                    "search": "ค้นหา:",
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "info": "แสดง _PAGE_ จาก _PAGES_",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });

            // ปรับแต่ง CSS เพิ่มเติม
            $('.dataTables_length label').addClass('d-flex align-items-center gap-2 m-0');
            $('.dataTables_filter label').addClass('d-flex align-items-center gap-2 m-0');
            $('.dataTables_length select').addClass('form-control mx-2').css('width', '80px');
            $('.dataTables_filter input').addClass('form-control').css('width', '180px');

            // ปรับแต่ง pagination
            function updatePaginationInfo() {
                let pageInfo = table.page.info();
                let currentPage = pageInfo.page + 1;
                let totalPages = pageInfo.pages;
                $(".custom-pagination").html(`
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light btn-sm prev-page">&lt;</button>
                    <span class="fw-bold">${currentPage} / ${totalPages}</span>
                    <button class="btn btn-light btn-sm next-page">&gt;</button>
                </div>
            `);

                // ปิดปุ่มเมื่ออยู่ที่หน้าแรกหรือหน้าสุดท้าย
                if (currentPage === 1) {
                    $(".prev-page").prop("disabled", true);
                } else {
                    $(".prev-page").prop("disabled", false);
                }
                if (currentPage === totalPages) {
                    $(".next-page").prop("disabled", true);
                } else {
                    $(".next-page").prop("disabled", false);
                }
            }

            // เรียกใช้งานครั้งแรก
            updatePaginationInfo();

            // อัปเดตเมื่อเปลี่ยนหน้า
            table.on('draw', function () {
                updatePaginationInfo();
            });

            // เพิ่ม event handler ให้ปุ่ม pagination ที่กำหนดเอง
            $(document).on("click", ".prev-page", function () {
                table.page("previous").draw("page");
            });

            $(document).on("click", ".next-page", function () {
                table.page("next").draw("page");
            });
        }
    });

</script>