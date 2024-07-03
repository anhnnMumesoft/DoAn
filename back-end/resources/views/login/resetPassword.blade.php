<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password</title>
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
                            <h4>Change Password</h4>
                            <h6 class="font-weight-light">Please enter password</h6>
                            <form class="pt-3" name="form" action="{{route('changPassword')}}" method="post" enctype="multipart/form-data">
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                @if($errors->any())
                                    <div class="text-danger">
                                        <ul>
                                            <li>{{ $errors->first() }}</li>
                                        </ul>
                                    </div>
                                @endif
                                @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">
                                    <input type="hidden" name="email" value="{{ $email }}">
                                    <div class="form-group">
                                        <label for="password" style="font-size: 1.125rem;">Password<span class="text-danger">*</span>: </label>
                                        <input type="password" class="form-control form-control-lg" id="password"name="password" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation" style="font-size: 1.125rem;">Password Confirmation<span class="text-danger">*</span>: </label>
                                        <input type="password" class="form-control form-control-lg" id="password_confirmation"name="password_confirmation" placeholder="Password Confirmation">
                                    </div>
                                <div class="mt-3">
                                    <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn " href="javascript:$('form').submit()" >Submit</a>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    Already have an account? <a href="{{route('login')}}" class="text-primary">Login</a>
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
</body>


