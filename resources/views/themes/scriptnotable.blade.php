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


<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<!-- jQuery (AdminLTE) -->
<script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>

<!-- Bootstrap 5 (Updated) -->
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
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<!--<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

 Select2 CSS -->

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>