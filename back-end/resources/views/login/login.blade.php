<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login </title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{asset('css/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/vendors/base/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{asset('css/css/style.css')}}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />
    <style>
        .custom-toast .toast-header {
            background-color: #007bff; /* Màu nền của header, ví dụ màu xanh */
            color: #ffffff; /* Màu chữ */
        }
        .custom-toast .toast-body {
            background-color: #e9ecef; /* Màu nền của body */
        }
    </style>
</head>

<body>
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="main-panel">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                <img src="{{asset('images/logo.svg')}}" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            <form class="pt-3" name="form" action="{{route('postLogin')}}" method="post" enctype="multipart/form-data">
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {!!   session('error') !!}
                                    </div>
                                @endif
                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {!!   session('success') !!}
                                        </div>
                                    @endif
                                    @if($errors->any())
                                        <div class="text-danger">
                                            <ul>
                                                <li>{{ $errors->first() }}</li>
                                            </ul>
                                        </div>
                                    @endif
                                    @if(session('passwordChanged'))
                                        <script>
                                            document.addEventListener('DOMContentLoaded', (event) => {
                                                var toastEl = document.getElementById('passwordChangedToast');
                                                var toast = new bootstrap.Toast(toastEl);
                                                toast.show();
                                            });
                                        </script>
                                    @endif
                                <div class="form-group">
                                    <label for="email" style="font-size: 1.125rem;">Email: <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                                </div>
                                @csrf
                                <div class="form-group">
                                    <label for="password" style="font-size: 1.125rem;">Password: <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password">
                                </div>
                                <div class="mt-3">
                                    <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" href="javascript:$('form').submit()">SIGN IN</a>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input" name="remember" id="remember" >
                                            Keep me signed in
                                        </label>
                                    </div>
                                    <a href="{{route('forgotpassword')}}" class="auth-link text-black">Forgot password?</a>
                                </div>
{{--                                <div class="mb-2">--}}
{{--                                    <button type="button" class="btn btn-block btn-facebook auth-form-btn">--}}
{{--                                        <i class="mdi mdi-facebook me-2"></i>Connect using facebook--}}
{{--                                    </button>--}}
{{--                                </div>--}}
                                <div class="text-center mt-4 font-weight-light">
                                    Don't have an account? <a href="{{route('register')}}" class="text-primary">Create</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- base:js -->
<script src="{{asset('js/vendors/base/vendor.bundle.base.js')}}"></script>
<!-- endinject -->
<!-- inject:js -->
<script src="{{asset('js/js/template.js')}}"></script>
<!-- endinject -->

@if(session('passwordChanged'))
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 11; width: auto;">
        <div id="passwordChangedToast" class="toast custom-toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Password Changed</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('passwordChanged') }}
            </div>
        </div>
    </div>
@endif
</body>


