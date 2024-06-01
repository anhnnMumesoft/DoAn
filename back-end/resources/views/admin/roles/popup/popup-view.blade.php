{{-- CSS--}}
<style>
    /* CSS cụ thể cho Popup View */
    #roleDetailModal .modal-content {
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
    #roleName {
        margin-left: 20px; /* Tăng lề bên trái */
    }

    /* Tăng lề cho các permission */
    .form-check {
        margin-left: 40px; /* Tăng lề bên trái cho mỗi permission */
    }
</style>

{{--JS--}}
<script>
    $(document).ready(function () {
        // Sự kiện khi nhấn nút "Edit"
        $(document).on('click', '#editButton', function () {
            // Kích hoạt các trường để cho phép chỉnh sửa
            $('input[name="roleNameUpdate').prop('disabled', false);
            $('input[name="permissions1[]"]').prop('disabled', false);

            // Thay đổi nút Edit thành Save và cập nhật id của nút
            $(this).text('Save').attr('id', 'saveButton');
        });

        // Sự kiện khi nhấn nút "Save"
        $(document).off('click', '#saveButton').on('click', '#saveButton', function () {
            var roleId = $('input[name="roleId"]').val();
            var roleNameUpdate = $('input[name="roleNameUpdate"]').val();
            // // Thu thập chỉ các permissions được tích
            var permissions = '';
            permissions = $('input[name="permissions1[]"]:checked').map(function () {
                return $(this).val();
            }).get();
            console.log(permissions);

            $.ajax({
                url: '/roles/' + roleId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    roleName: roleNameUpdate,
                    permissions: permissions,
                    _method: 'PUT'
                },
                success: function (response) {
                    alert('Role updated successfully!');
                    $('#roleDetailModal').modal('hide');
                    // Cập nhật UI hoặc làm mới trang tại đây nếu cần
                },
                error: function (error) {
                    console.log(error);
                    alert('An error occurred. Please try again.');
                }
            });

            // Chuyển trạng thái các trường và nút về ban đầu
            $('input[name="roleNameUpdate"]').prop('disabled', true);
            $('input[name="permissions1[]"]').prop('disabled', true);
            $(this).text('Edit').attr('id', 'editButton');
        });

        $(document).on('click', '.view-role', function () {
            var roleId = $(this).data('role-id'); // Lấy ID của role được click

            $.ajax({
                url: '/roles/' + roleId, // URL tới phương thức xử lý trong controller
                type: 'GET',
                success: function (response) {
                    // Điền thông tin vào modal
                    console.log(response.role.name)
                    $('input[name="roleNameUpdate').val(response.role.name);
                    $('input[name="roleId').val(response.role.id);
                    $('input[name="roleName').prop('disabled', true);
                    $('input[name="permissions1[]"]').each(function () {
                        $(this).prop('checked', response.assignedPermissions.includes(parseInt($(this).val())));
                        $(this).prop('disabled', true); // Không cho phép chỉnh sửa
                    });

                    var currentUserPrimaryRole = @json(auth()->user()->role->primary_role);
                    // Giả sử data.user.role.primary_role là primary_role của người dùng được xem
                    if (currentUserPrimaryRole == 0 || (currentUserPrimaryRole == 1 && response.role.primary_role == 2)) {
                        // Hiển thị nút Edit
                        $('#editButton').show();
                    } else {
                        // Ẩn nút Edit
                        $('#editButton').hide();
                    }

                    // Hiển thị modal
                    $('#roleDetailModal').modal('show');
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });


        var isEditMode = false;
        // Sự kiện khi modal được hiển thị
        $('#roleDetailModal').on('shown.bs.modal', function () {
            if (isEditMode) {
                // Tự động chuyển sang chế độ "Edit" nếu isEditMode = true
                $('#editButton').click(); // Giả sử nút "Edit" có id là "editButton"

            }
        });

        // Sự kiện khi modal bắt đầu đóng
        $('#roleDetailModal').on('hide.bs.modal', function () {
            isEditMode = false; // Reset trạng thái khi modal đóng
            $('#saveButton').text('Edit').attr('id', 'editButton');
        });
        // Sự kiện khi nhấn nút "Edit" trong modal
        $(document).on('click', '.edit-role', function () {

            var roleId = $(this).data('role-id'); // Lấy ID của role được click
            console.log(roleId);
            isEditMode = true; // Cập nhật trạng thái sang chế độ "Edit"
            // Tìm và kích hoạt click cho .view-user tương ứng với userId
            // Giả sử bạn có cách nào đó để kích hoạt click dựa trên userId
            $('.view-role[data-role-id="' + roleId + '"]').click();
        });
    });

</script>
<!-- User Detail Modal -->
<div class="modal fade" id="roleDetailModal" tabindex="-1" aria-labelledby="roleDetailModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="roleDetailModal">Add New Role</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addRoleForm">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="roleNameUpdate" name="roleNameUpdate" required>
                        <input type="text" name="roleId" id="roleId" value="" hidden disabled>

                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        @foreach($permissions as $type => $actions)
                            <div><strong>{{ $type }}</strong></div>
                            <div class="row">
                                @foreach($actions as $index => $action)
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input checkbox-js" type="checkbox"
                                                   value="{{ $action['id'] }}" id="permission{{ $action['id'] }}"
                                                   name="permissions1[]">
                                            <label class="form-check-label" for="permission{{ $action['id'] }}">
                                                {{ $action['name'] }}
                                            </label>
                                        </div>
                                    </div>
                                    @if($index % 2 == 1)
                            </div>
                            <div class="row">
                                @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @can('edit-roles')
                <button type="button" class="btn btn-primary" id="editButton">Edit</button>
                @endcan
            </div>
        </div>
    </div>
</div>
