<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | Dashboard</title>

    @yield('css')
    <!-- Google Font: IBM Plex Sans Thai, Kanit, Noto Sans Thai -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+Thai:wght@100..900&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ URL::asset('dist/css/adminlte.min.css') }}">

    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Kanit', 'IBM Plex Sans Thai', 'Noto Sans Thai', sans-serif;
        }


        /* Style for the table container */
        .table-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            margin-top: 20px;
            overflow-x: auto;
            /* Add horizontal scrolling */
        }

        /* Style for the table */
        .table {
            width: 100%;
            border-radius: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
        }

        /* Style for the table header */


        /* Style for the table header cells */
        .table th {
            padding: 12px;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        /* Style for table rows */
        .table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        /* Style for table data cells */
        .table td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        /* Style for table stripes */
        .table tbody tr:nth-child(even) {
            background-color: rgb(249, 249, 249);
            /* Light gray background for even rows */
        }

        /* Style for hover effect */
        .table tbody tr:hover {
            background-color: #f1f1f1;
            /* Light hover effect */
        }

        /* Style for checkbox input */
        .table input[type="checkbox"] {
            margin-left: 10px;
        }

        /* Optional: Style for modal and button actions */
        button {
            font-size: 14px;
            padding: 10px 20px;
        }


        /* เปลี่ยนสีพื้นหลังของแถวเมื่อ hover */
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* เปลี่ยนสีพื้นหลังของคอลัมน์หัวตาราง */
        .table thead th {
            background-color: rgb(93, 178, 211);
            color: white;
        }

        /* เปลี่ยนสีพื้นหลังของแถวในตาราง */
        .table tbody tr:nth-child(odd) {
            background-color: rgb(247, 252, 255);
            /* สีฟ้าอ่อน */
        }

        .table tbody tr:nth-child(even) {
            background-color: #FFFFFF;
            /* สีขาว */
        }
    </style>
</head>