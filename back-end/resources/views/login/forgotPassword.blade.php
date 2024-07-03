<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password</title>
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
                            <h4>Forgot password</h4>
                            <h6 class="font-weight-light">Please enter verification information</h6>
                            <form class="pt-3" name="form" action="{{route('forgotpassword')}}" method="post" enctype="multipart/form-data">
                                @if(session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger">
                                        {!! session('error')  !!}
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
                                <div class="form-group">
                                    <label for="email" style="font-size: 1.125rem;">Email: <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                                </div>
                                    <div class="mt-3">
                                        <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn " href="javascript:$('form').submit()" >Submit</a>
                                    </div>

                                    <div class="text-center mt-4 font-weight-light">
                                        Already have an account? <a href="{{route('login')}}" class="text-primary">Login</a>
                                        <br>
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
</body>


