<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Register</title>
  <!-- base:css -->
  <link rel="stylesheet" href="{{asset('css/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/vendors/base/vendor.bundle.base.css')}}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- JavaScript mới nhất được biên dịch -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{asset('css/css/style.css')}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" />
    <style>
        .modal-header, .modal-footer {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }

        .modal-title {
            color: #007bff;
        }

        .modal-body {
            font-size: 16px;
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
                  <img src="images/logo.svg" alt="logo">
                </div>
                <h4>New here?</h4>
                <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
                <form class="pt-3" name="form" action="{{route('postRegister')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif
                  <div class="form-group">
                      <label for="name" style="font-size: 1.125rem;">Name<span class="text-danger">*</span>: </label>
                      <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Name " value="{{ old('name') }}">
                      @if($errors->has('name'))
                          <div class="text-danger">
                              @foreach($errors->get('name') as $error)
                                  {{ $error }}<br>
                              @endforeach
                          </div>
                      @endif
                  </div>
                    @csrf
                  <div class="form-group">
                      <label for="email" style="font-size: 1.125rem;">Email<span class="text-danger">*</span>:</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                      @if($errors->has('email'))
                          <div class="text-danger">
                              @foreach($errors->get('email') as $error)
                                  {{ $error }}<br>
                              @endforeach
                          </div>
                      @endif
                  </div>
                  <div class="form-group">
                      <label for="country" style="font-size: 1.125rem;">Country:</label>
                    <input class="form-control form-control-lg" id="country" name="country" value="{{ old('country') }}" placeholder="Country">
                      @if($errors->has('country'))
                          <div class="text-danger">
                              @foreach($errors->get('country') as $error)
                                  {{ $error }}<br>
                              @endforeach
                          </div>
                      @endif
                  </div>
                  <div class="form-group">
                      <label for="password" style="font-size: 1.125rem;">Password<span class="text-danger">*</span>: </label>
                    <input type="password" class="form-control form-control-lg" id="password"name="password" placeholder="Password">
                      @if($errors->has('password'))
                          <div class="text-danger">
                              @foreach($errors->get('password') as $error)
                                  {{ $error }}<br>
                              @endforeach
                          </div>
                      @endif
                  </div>
                    <div class="form-group">
                        <label for="password_confirmation" style="font-size: 1.125rem;">Password Confirmation<span class="text-danger">*</span>: </label>
                        <input type="password" class="form-control form-control-lg" id="password_confirmation"name="password_confirmation" placeholder="Password Confirmation">
                        @if($errors->has('passwordConfirm'))
                            <div class="text-danger">
                                @foreach($errors->get('passwordConfirm') as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    {{-- role --}}
                    <input type="hidden"  class="form-control form-control-lg"name="role" value="1">

                  <div class="mb-4">
                      <div class="form-check">
                          <input type="checkbox" name="agree-checkbox" id="agree-checkbox">
                          <label for="agreeTerms">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#readModal"> Terms & Conditions</a></label>
                      </div>
                  </div>
                  <div class="mt-3">
                    <a class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn " onclick="validateForm()" >SIGN UP</a>
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
        <!-- Modal -->
        <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Please agree to the Terms & Conditions.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="readModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>Welcome to Our Website!</h5>
                        <p>By accessing or using our service, you agree to be bound by the terms and conditions set forth below. If you do not agree with all of these terms and conditions, you are prohibited from using or accessing this site.</p>

                        <h6>Use License</h6>
                        <p>Permission is granted to temporarily download one copy of the materials (information or software) on our website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license, you may not:</p>
                        <ul>
                            <li>Modify or copy the materials;</li>
                            <li>Use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
                            <li>Attempt to decompile or reverse engineer any software contained on our website;</li>
                            <li>Remove any copyright or other proprietary notations from the materials;</li>
                            <li>Transfer the materials to another person or "mirror" the materials on any other server.</li>
                        </ul>
                        <p>This license shall automatically terminate if you violate any of these restrictions and may be terminated by us at any time.</p>

                        <h6>Disclaimer</h6>
                        <p>The materials on our website are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>

                        <h6>Limitations</h6>
                        <p>In no event shall we or our suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website, even if we or an authorized representative has been notified orally or in writing of the possibility of such damage.</p>

                        <h6>Governing Law</h6>
                        <p>These terms and conditions are governed by and construed in accordance with the laws of our jurisdiction and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>

                        <p>We reserve the right to amend these terms and conditions at any time. By continuing to use the site after we post any changes, you accept the modified terms and conditions.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
  <script src="https://code.jquery.com/jquery-latest.js"></script>
    <script src="{{asset('js/login/login.js')}}"></script>
</body>

</html>
