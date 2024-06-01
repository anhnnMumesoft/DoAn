{{-- CSS--}}
<style>
    /* CSS cụ thể cho Popup View */
    #userDetailModal .modal-content {
        background-color: #f8f9fa;
    }

    .user-avatar img {
        max-width: 100px;
        border-radius: 50%;
    }

    .modal-header, .modal-footer {
        border-bottom: 1px solid #dee2e6; /* Thêm đường viền cho header và footer */
        background-color: #e9ecef; /* Màu nền cho header và footer */
    }

    .modal-title {
        color: #495057; /* Màu sắc cho tiêu đề */
        font-size: 24px; /* Tăng kích thước font cho tiêu đề */
    }

    .modal-footer .btn {
        font-size: 18px; /* Tăng kích thước font cho nút */
    }

    .card-title {
        font-size: 20px; /* Tăng kích thước font cho tiêu đề card */
    }

    .user-avatar {
        margin-bottom: 15px;
    }

    .modal.fade .modal-dialog {
        transition: transform .3s ease-out;
        transform: translateY(-50%);
    }

    .modal.show .modal-dialog {
        transform: translateY(0);
    }

    .card-text {
        display: flex; /* Use Flexbox for alignment */
        align-items: center; /* Vertically center the items in the flex container */
        font-size: 18px; /* Adjust the font size as needed */
    }

    .card-text i {
        margin-right: 8px; /* Add some space between the icon and the text */
    }

    .inline-edit-input {
        max-width: 100%; /* Đảm bảo input không vượt quá container */
        width: auto; /* Cho phép input điều chỉnh chiều rộng dựa trên nội dung */
        display: inline-block; /* Hiển thị input inline với text xung quanh */
    }

    .custom-select-role {
        -webkit-appearance: menulist-button; /* Ghi đè cho Webkit browsers */
        -moz-appearance: menulist-button; /* Ghi đè cho Firefox */
        appearance: menulist-button; /* Ghi đè cho các trình duyệt hỗ trợ */
        color: black !important; /* Đảm bảo màu chữ là màu đen */
    }
    .form-label {
        font-size: 18px; /* Tăng kích thước font */
        font-weight: bold; /* Làm đậm chữ */
    }

    .form-control {
        font-size: 16px; /* Tăng kích thước font cho input */
    }

    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 86, 179, .5); /* Thay đổi màu shadow khi input được focus */
    }

    /* Tăng lề cho input "Add Role" */
    #roleName ,#roleType {
        margin-left: 20px; /* Tăng lề bên trái */
    }

    /* Tăng lề cho các permission */
    .form-check {
        margin-left: 40px; /* Tăng lề bên trái cho mỗi permission */
    }
</style>

{{--JS--}}
<script>
    $(document).ready(function() {
        $('#saveRoleButton').click(function() {
            var roleName = $('#roleName').val();
            var permissions = [];
            $('input[name="permissions[]"]:checked').each(function() {
                permissions.push($(this).val());
            });
            var primaryRole = $('#primaryRole').val(); // Lấy giá trị primaryRole

            $.ajax({
                url: '{{ route("roles.store") }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "roleName": roleName,
                    "permissions": permissions,
                    "primaryRole": primaryRole, // Gửi primaryRole
                },
                success: function(response) {
                    // Xử lý response
                    alert(response.message);
                    // Đóng modal và làm mới trang hoặc cập nhật danh sách roles
                    $('#userDetailModal').modal('hide');
                    location.reload(); // Hoặc cập nhật danh sách roles mà không cần tải lại trang
                },
                error: function(error) {
                    // Xử lý lỗi
                    console.log(error);
                }
            });
        });
    });
</script>
<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title" id="userDetailModalLabel">Add New Role</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addRoleForm">
                    <div class="mb-3">
                        <label for="primaryRole" class="form-label">Primary Role</label>
                        <select class="form-control" id="primaryRole" name="primaryRole" style="height: 130%">
                            <option value="1">Admin</option>
                            <option value="2">User</option>
                            <!-- Thêm các giá trị khác tùy theo cấu trúc của hệ thống của bạn -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleName" name="roleName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        {{-- Kiểm tra giá trị của primary_role --}}
                        @if(auth()->user()->role->primary_role == 0)
                            {{-- Nếu primary_role là 0, hiển thị tất cả permissions --}}
                            @foreach($permissions as $type => $actions)
                                <div><strong>{{ $type }}</strong></div>
                                <div class="row">
                                    @foreach($actions as $index => $action)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $action['id'] }}" id="permission{{ $action['id'] }}" name="permissions[]">
                                                <label class="form-check-label" for="permission{{ $action['id'] }}">
                                                    {{ $action['name'] }}
                                                </label>
                                            </div>
                                        </div>
                                        @if($index % 2 == 1)
                                </div><div class="row">
                                    @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @elseif(auth()->user()->role->primary_role == 1)
                            {{-- Nếu primary_role là 1, chỉ hiển thị permissions cho role ADMIN và USER --}}
                            @foreach($permissions as $type => $actions)
                                @if($type == 'Users' || $type == 'History'||$type == 'Role')
                                    <div><strong>{{ $type }}</strong></div>
                                    <div class="row">
                                        @foreach($actions as $index => $action)
                                            {{-- Khi primary_role là 1 và type là Users, chỉ hiển thị view và add --}}
                                            @if(!($type == 'Users' && !in_array($action['action'], ['View', 'Add'])))
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="{{ $action['id'] }}" id="permission{{ $action['id'] }}" name="permissions[]">
                                                        <label class="form-check-label" for="permission{{ $action['id'] }}">
                                                            {{ $action['name'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                                @if($index % 2 == 1)
                                    </div><div class="row">
                                        @endif
                                        @endif
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveRoleButton">Save</button>
            </div>
        </div>
    </div>
</div>
