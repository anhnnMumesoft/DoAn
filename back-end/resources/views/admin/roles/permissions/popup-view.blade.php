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

            $.ajax({
                url: '{{ route("roles.store") }}',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "roleName": roleName,
                    "permissions": permissions
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
                <h5 class="modal-title" id="userDetailModalLabel">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addRoleForm">
                    <div class="mb-3">
                        <label for="roleName" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="roleName" name="roleName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div>
                            {{-- Giả sử bạn có một mảng $permissions chứa tất cả quyền có sẵn --}}
                            @foreach($permissions as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" id="permission{{ $permission->id }}" name="permissions[]">
                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
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
