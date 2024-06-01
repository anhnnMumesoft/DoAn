<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- title -->
    <title>  @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('../css/vendors/feather1/feather.css')}}">
    <link rel="stylesheet" href="{{asset('../css/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('../css/vendors/ti-icons1/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('../css/vendors/typicons/typicons.css')}}">
    <link rel="stylesheet" href="{{asset('../css/vendors/simple-line-icons1/css/simple-line-icons.css')}}">
    <link rel="stylesheet" href="{{asset('../css/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{asset('../css/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('../css/select.dataTables.min.css')}}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('../css/css/vertical-layout-light/style.css')}}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{asset('../images/favicon.png')}}"/>
    <!-- css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


</head>

<body class="with-welcome-text">
<div class="container-scroller">
    @section('header')
        @include('admin.layouts.header')
    @show
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_settings-panel.html -->
        @section('setting-header.blade')
            @include('admin.layouts.setting-header')
        @show
        @section('sidebar')
            @include('admin.layouts.sidebar')
        @show
        <!-- partial -->

        <div class="main-panel">
            <div class="content-wrapper " style=" padding-top: 0; ">
                <div class="row mt-4">
                    @yield('content')

                </div>
            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            @section('_footer')
                @include('admin.layouts.footer')
            @show
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->

<!-- plugins:js -->


@yield('js')
<script src="{{asset('../css/vendors/js/vendor.bundle.base.js')}}"></script>
<script src="{{asset('../css/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{asset('../css/vendors/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('../css/vendors/progressbar.js/progressbar.min.js')}}"></script>
<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{asset('../js/off-canvas.js')}}"></script>
<script src="{{asset('../js/hoverable-collapse.js')}}"></script>
<script src="{{asset('../js/js/template.js')}}"></script>
<script src="{{asset('../js/settings.js')}}"></script>
<script src="{{asset('../js/todolist.js')}}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{asset('../js/jquery.cookie.js')}}"> type="text/javascript"></script>
<script src="{{asset('../js/dashboard.js')}}"></script>
<!-- <script src="../../assets/js/Chart.roundedBarCharts.js"></script> -->
<!-- End custom js for this page-->
</body>

</html>
