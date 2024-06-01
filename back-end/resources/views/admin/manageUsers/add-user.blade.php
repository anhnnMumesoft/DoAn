@extends('admin.index')
@section('title')
    Add user
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
        .required-star {
            color: red;
        }

    </style>
@endsection
{{--JS--}}

@section('js')
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
    </script>
@endsection

{{--content--}}
@section('content')

    <div class="col-12 ">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">    Add User</h4>

                <form class="file-upload"  name="form" action="{{ route('admin.addUser') }}" method="POST"  enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row mb-5 gx-5">

                        <div class="col-xxl-8 mb-5 mb-xxl-0">
                            <div class="bg-secondary-soft px-4 py-5 rounded">
                                <div class="row g-3">
                                    @if(session('success'))
                                        <div class="text-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-user me-2 "></i>Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your name " aria-label="Name" name="name" value="{{ old('name') }}">
                                            @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-globe me-2 "></i>Country</label>
                                            <input type="text" class="form-control" placeholder="Enter your country" aria-label="Country" name="country" value="{{ old('country') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-phone-alt me-2 "></i>Phone number <span style="color: red">*</span></label>
                                            <input type="text" class="form-control @error('number_phone') is-invalid @enderror" placeholder="Enter your phone number " aria-label="Phone number" name="number_phone" value="{{ old('number_phone') }}">
                                            @error('number_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-envelope me-2 "></i>Email <span style="color: red">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email " id="email" name="email" value="{{ old('email') }}">
                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-lock me-2 "></i>Password <span style="color: red">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" id="password" name="password" {{ old('password') }}>
                                            @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label"><i class="fas fa-lock me-2 "></i>Confirm Password <span style="color: red">*</span></label>
                                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password" id="password_confirmation" name="password_confirmation">
                                            @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 ">
                                            <label for="role_id" class="form-label"><i class="fas fa-user-tag me-2 "></i>Role <span style="color: red">*</span>:</label>
                                            <select id="role_id" class="form-select" style="height: 38px;" name="role_id">
                                                @if(auth()->user()->role->primary_role == 0)
                                                    {{-- Nếu người dùng có primary_role == 0, hiển thị tất cả các role --}}
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                @elseif(auth()->user()->role->primary_role == 1)
                                                    {{-- Nếu người dùng có primary_role == 1, chỉ hiển thị các role có primary_role == 1 hoặc == 0 --}}
                                                    @foreach($roles as $role)
                                                        @if($role->primary_role !=0)
                                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">Active: </span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="activeSwitch" name="is_active" checked>
                                                    <label class="form-check-label" for="activeSwitch"></label>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>
                        </div>


                        <div class="col-xxl-4">
                            <div class="bg-secondary-soft px-4 py-5 rounded">
                                <div class="row g-3">
                                    <h4 class="mb-4 mt-0">Avatar</h4>
                                    <div class="text-center">

                                        <div class="square position-relative display-2 mb-3" id="avatarContainer" style="background-image: url('{{ asset('images/avatars/avatarDefault.png') }}'); background-size: cover; background-position: center center;">
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
                        <button type="button"  href="{{route('admin.users')}}"  class="btn btn-danger btn-lg">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-lg">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
