@extends('admin.index')
@section('title')
    My profile
@endsection
{{-- CSS--}}
@section('css')
    <style type="text/css">
        body {
            margin-top: 20px;
            color: #9b9ca1;
        }

        .bg-secondary-soft {
            background-color: rgba(208, 212, 217, 0.1) !important;
        }

        .rounded {
            border-radius: 5px !important;
        }

        .py-5 {
            padding-top: 3rem !important;
            padding-bottom: 3rem !important;
        }

        .px-4 {
            padding-right: 1.5rem !important;
            padding-left: 1.5rem !important;
        }

        .file-upload .square {
            height: 250px;
            width: 250px;
            margin: auto;
            vertical-align: middle;
            border: 1px solid #e5dfe4;
            background-color: #fff;
            border-radius: 5px;
        }

        .text-secondary {
            --bs-text-opacity: 1;
            color: rgba(208, 212, 217, 0.5) !important;
        }

        .btn-success-soft {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .btn-danger-soft {
            color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }

        .form-control {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            font-size: 0.9375rem;
            font-weight: 400;
            line-height: 1.6;
            color: #29292e;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e5dfe4;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 5px;
            -webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;

        .square {
            height: 250px;
            width: 250px;
            margin: auto;
            vertical-align: middle;
            border: 1px solid #e5dfe4;
            background-color: #fff;
            border-radius: 5px;
            position: relative; /* Đảm bảo icon và ảnh nền đều nằm đúng vị trí */
        }
    </style>
@endsection
{{--JS--}}

@section('js')
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#customFile').change(function() {
                var file = this.files[0];
                var fileType = file.type;
                var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if(!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]))) {
                    alert('Sorry, only JPG, JPEG, PNG, GIF, & SVG files are allowed to upload.');
                    $('#customFile').val('');
                    return false;
                }
            });
        });
        $(document).ready(function() {
            $('#customFile').change(function(e) {
                var file = e.target.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Cập nhật ảnh nền cho div và ẩn icon
                    $('#avatarContainer').css({
                        'background-image': 'url(' + e.target.result + ')',
                        'background-size': 'cover', // Đảm bảo ảnh phủ kín khung
                        'background-position': 'center center' // Căn ảnh giữa khung
                    });
                    $('#defaultAvatarIcon').hide(); // Ẩn icon
                };
                reader.readAsDataURL(file);
            });
            // Sự kiện click cho nút "Remove"
            // Định nghĩa đường dẫn avatar mặc định
            var defaultAvatarPath = "{{ asset('images/avatars/avatarDefault.png') }}";

            $('#removeAvatar').click(function() {
                // Cập nhật giá trị của trường ẩn để biết avatar cần được xóa
                $('#removeAvatarFlag').val('1');
                // Reset trạng thái của input file
                $('#customFile').val('');
                // Cập nhật ảnh nền cho div với avatar mặc định và hiển thị lại icon
                $('#avatarContainer').css({
                    'background-image': 'url(' + defaultAvatarPath + ')',
                    'background-size': 'cover', // Đảm bảo ảnh phủ kín khung
                    'background-position': 'center center' // Căn ảnh giữa khung
                });
                $('#defaultAvatarIcon').hide(); // Ẩn icon vì avatar mặc định sẽ được hiển thị
            });
        });

        $(document).ready(function() {
            $('#deleteProfileBtn').click(function() {
                if (confirm('Are you sure you want to delete your profile? This action cannot be undone.')) {
                    $.ajax({
                        url: '{{ route("profile.delete", Auth::user()->id) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            alert('Your profile has been deleted successfully.');
                            window.location.href = '/'; // Redirect to homepage or login page
                        },
                        error: function(xhr) {
                            alert('An error occurred while deleting your profile.');
                        }
                    });
                }
            });

            $('#deleteProfileBtn').click(function() {
                if (confirm('Are you sure you want to delete your profile? This action cannot be undone.')) {
                    $.ajax({
                        url: '{{ route("profile.delete", Auth::user()->id) }}',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            alert(response.success);
                            window.location.href = '/'; // Redirect to homepage or login page
                        },
                        error: function(xhr) {
                            alert(xhr.responseJSON.error);
                        }
                    });
                }
            });
        });
    </script>
@endsection

{{--content--}}
@section('content')

        <div class="col-12 ">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">    My profile</h4>

            <form class="file-upload"  name="form" action="{{ route('profile_update', ['id' => Session::get('user')->id  ]) }}" method="POST"  enctype="multipart/form-data">

                @method('PUT')
                @csrf
                <div class="row mb-5 gx-5">

                    <div class="col-xxl-8 mb-5 mb-xxl-0">
                        <div class="bg-secondary-soft px-4 py-5 rounded">
                            <div class="row g-3">
                                @if($errors->any())
                                    <div class="text-danger">
                                        <ul>
                                            <li>{{ $errors->first() }}</li>
                                        </ul>
                                    </div>
                                @endif
                                @if(session('success'))
                                    <div class="text-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <label class="form-label">Name *</label>
                                    <input type="text" class="form-control" placeholder aria-label="Name" name="name"
                                           value="{{ Session::get('user')->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <input type="text" class="form-control" placeholder aria-label="Phone number"  name="country"
                                           value="{{ Session::get('user')->country }}">
                                </div>


                                <div class="col-md-6">
                                    <label class="form-label">Mobile number *</label>
                                    <input type="text" class="form-control" placeholder aria-label="Phone number"  name="number_phone"
                                           value="{{ Session::get('user')->number_phone }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ Session::get('user')->email }}">
                                </div>

{{--                                <div class="col-md-6">--}}
{{--                                    <label class="form-label">Skype </label>--}}
{{--                                    <input type="text" class="form-control" placeholder aria-label="Phone number"--}}
{{--                                           value="Scaralet D">--}}
{{--                                </div>--}}
                            </div>
                        </div>
                        <div class="col-xxl-12">
                            <div class="bg-secondary-soft px-4 py-3 rounded">
                                <div class="row g-3">
                                    <h4 class="my-4">Change Password</h4>

                                    <div class="col-md-6">
                                        <label for="old_password" class="form-label">Old password *</label>
                                        <input type="password" class="form-control" id="old_password" name="old_password">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New password *</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="new_password_confirmation" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="new_password_confirmation"name="new_password_confirmation">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xxl-4">
                        <div class="bg-secondary-soft px-4 py-5 rounded">
                            <div class="row g-3">
                                <h4 class="mb-4 mt-0">Upload your profile photo</h4>
                                <div class="text-center">

                                    <div class="square position-relative display-2 mb-3" id="avatarContainer" style="@if(Session::get('user')->avatar) background-image: url('{{ asset(Session::get('user')->avatar) }}'); background-size: cover; background-position: center center; @endif">
                                    </div>
{{--                                    <!-- Phần tử img để hiển thị ảnh preview hoặc ảnh mặc định -->--}}
{{--                                    <img id="avatarPreview" src="" alt="Avatar Preview" style="width: 100px; height: 100px; border-radius: 50%;"/>--}}

                                    <input type="file" id="customFile" name="avatar"  accept="image/png, image/jpeg, image/gif, image/svg+xml" hidden>
                                    <label class="btn btn-primary btn-block" for="customFile">Upload</label>
                                    <button type="button" class="btn btn-danger" id="removeAvatar" >Remove</button>
                                    <!-- Thêm trường ẩn này vào trong form -->
                                    <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">

                                    <p class="text-muted mt-3 mb-0"><span class="me-1">Note:</span>Minimum size 300px x
                                        300px</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gap-3 d-md-flex justify-content-md-end text-center">
                    <button type="button"  id="deleteProfileBtn"  class="btn btn-danger btn-lg">Delete profile</button>
                    <button type="submit" class="btn btn-primary btn-lg">Update profile</button>
                </div>
            </form>
                </div>
            </div>
        </div>
@endsection
