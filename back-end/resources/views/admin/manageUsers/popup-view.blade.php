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
    function formatDate(dateString) {
        if (dateString) {
            const date = new Date(dateString);
            let day = date.getDate().toString().padStart(2, '0');
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let year = date.getFullYear().toString();
            let hours = date.getHours().toString().padStart(2, '0');
            let minutes = date.getMinutes().toString().padStart(2, '0');
            let seconds = date.getSeconds().toString().padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        }
        return '';
    }

    $(document).ready(function () {
        var roles;

        var currentUserRole = @json(auth()->user()->role->name);
        // Giả sử bạn truyền role của người dùng được xem qua biến $userRole trong view
        let viewedUserRole;

        $(document).on('click', '.view-user', function () {
            // Giả sử bạn đã truyền primary_role của người dùng đang đăng nhập vào view này
            var currentUserPrimaryRole = @json(auth()->user()->role->primary_role);

            var userId = $(this).data('id');
            $.ajax({
                url: '/user/' + userId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#userDetailModalLabel').text(data.user.name);
                    viewedUserRole = data.user.role.name;
                    var phoneNumber = data.user.number_phone ? data.user.number_phone : '';
                    roles = data.roles;
                    $('#userDetailModal .modal-body').html(`
                        <div class="card">
                            <div class="card-body">
                                <input type="text" name="userId" id="userId" value="${data.user.id}"  hidden disabled>

                                <div class="user-avatar text-center mb-3">
                                    <img id="userAvatar" src="../${data.user.avatar ? data.user.avatar : 'images/avatars/avatarDefault.png'}" alt="Avatar" class="img-fluid rounded-circle">
                                    <!-- Input file ẩn -->
                                <input type="file" id="avatarInput" name="avatar"  accept="image/png, image/jpeg, image/gif, image/svg+xml" style="display: none;">
                                </div>
                                <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="form-label"><i class="fas fa-user me-2 "></i>Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter your name " aria-label="Name" name="name" id="name" value=" ${data.user.name} " disabled>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="form-label"><i class="fas fa-globe me-2 "></i>Country</label>
                                            <input type="text" class="form-control" placeholder="Enter your country" aria-label="Country" name="country" id="country" value=" ${data.user.country} " disabled>
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <label class="form-label"><i class="fas fa-phone-alt me-2 "></i>Phone number <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" placeholder="Enter your phone number " aria-label="Phone number" name="number_phone" id="number_phone" value=" ${phoneNumber} " disabled >
                                        </div>

                                        <div class="col-md-6 mt-2">
                                            <label class="form-label"><i class="fas fa-envelope me-2 "></i>Email <span style="color: red">*</span></label>
                                            <input type="email" class="form-control" placeholder="Enter your email " id="email" name="email" value=" ${data.user.email} "disabled >
                                        </div>
                                   <div class="col-md-6 mt-2">
                                            <label for="role_id" class="form-label"><i class="fas fa-user-tag me-2"></i>Role <span style="color: red">*</span>:</label>
                                            <select id="role_id"class="form-select" style="height: 38px;" name="role_id">

                                            </select>
                                        </div>

                                <div class="col-md-6 mt-3">
                                <label for="status" class="form-label"><i class="fas fa-thermometer-half"></i> Status:</label>
                                <input type="text" id="status" class="form-control" value="${data.user.status}" disabled>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="emailVerifiedAt" class="form-label"><i class="fas fa-check-circle"></i> Email verified at:</label>
                                    <input type="text" id="emailVerifiedAt" name="emailVerifiedAt" class="form-control" value="${formatDate(data.user.email_verified_at)}" disabled>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="createdAt" class="form-label"><i class="fas fa-calendar-plus"></i> Created at:</label>
                                    <input type="text" id="createdAt" class="form-control" value="${formatDate(data.user.created_at)}" disabled>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label for="updatedAt" class="form-label"><i class="fas fa-calendar-check"></i> Updated at:</label>
                                    <input type="text" id="updatedAt" class="form-control" value="${formatDate(data.user.updated_at)}" disabled>
                                </div>
                                ${data.user.deleted_at ? `
                                <div class="col-md-6 mt-3">
                                    <label for="deletedAt" class="form-label"><i class="fas fa-trash-alt"></i> Deleted at:</label>
                                    <input type="text" id="deletedAt" class="form-control" value="${formatDate(data.user.deleted_at)}" disabled>
                                </div>
                                ` : ''}
                                <div class="col-md-6 mt-2">
                                            <label for="" class="form-label"><i class="fas fa-user-tag me-2"></i> Active: </label>
                                            <div class="form-check form-switch d-flex mt-0">
                                                <input class="form-check-input" type="checkbox" id="activeSwitch" name="is_active">
                                            </div>
                                </div>
                    `);

                    // Các dòng khác để cập nhật modal với thông tin người dùng
                    var currentUserPrimaryRole = @json(auth()->user()->role->primary_role);
                    // Giả sử data.user.role.primary_role là primary_role của người dùng được xem
                    if (currentUserPrimaryRole == 0 || (currentUserPrimaryRole == 1 && data.user.role.primary_role == 2)) {
                        // Hiển thị nút Edit
                        $('#editButton').show();
                    } else {
                        // Ẩn nút Edit
                        $('#editButton').hide();
                    }
                    // Cập nhật dropdown Role
                    var roleOptions = roles.map(function (role) {
                        var selected = data.user.role_id === role.id ? 'selected' : '';

                        return `<option value="${role.id}" ${selected}>${role.name}</option>`;

                    }).join('');
                    $('#role_id').html(roleOptions).prop('disabled', true); // Thêm .prop('disabled', true) để disable dropdown

                    // Cập nhật trạng thái Active
                    if (data.user.is_active) {
                        $('#activeSwitch').prop('checked', true);
                    } else {
                        $('#activeSwitch').prop('checked', false);
                    }
                    $('#userDetailModal').modal('show');
                },
                error: function (request, status, error) {
                    alert('An error occurred while retrieving user information');
                }
            });
        });

        function convertDateString(dateString) {
            // Kiểm tra xem dateString có tồn tại và không phải là chuỗi rỗng
            if (!dateString) {
                return ''; // Trả về chuỗi rỗng nếu dateString không hợp lệ hoặc là undefined
            }

            // Chia chuỗi ngày tháng thành các phần
            var parts = dateString.split(' ')[0].split('/'); // Lấy phần ngày tháng, bỏ qua thời gian
            var parts1 = dateString.split(' ')[0].split('-'); // Lấy phần ngày tháng, bỏ qua thời gian
            if (parts1[0].length === 4) {
                // Định dạng là yyyy-mm-dd hoặc yyyy/mm/dd
                return dateString; // Đã ở định dạng ISO, không cần chuyển đổi
            }

            var day = parts[0];
            var month = parts[1];
            var year = parts[2];


            // Tạo chuỗi ngày tháng mới theo định dạng yyyy-mm-dd
            var newDateString = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            console.log(newDateString);
            return newDateString;

        }

        // Reset trạng thái của modal và nút khi modal được mở
        $('#userDetailModal').on('shown.bs.modal', function () {
            // Đặt lại nút về trạng thái "Edit"
            $('#saveButton').text('Edit').attr('id', 'editButton');
            // Đảm bảo tất cả các trường input và select đều được disabled
            $(this).find('input, select').prop('disabled', true);
        });


        // Hàm cập nhật dropdown Role
        function updateRoleDropdown(selectedRoleId, currentUserPrimaryRole, roles) {
            var roleOptions = roles.filter(function (role) {
                if (currentUserPrimaryRole == 0) {
                    // Nếu người dùng đang đăng nhập có primary_role là 0, hiển thị tất cả các role
                    return true;
                } else {
                    // Nếu người dùng đang đăng nhập có primary_role khác 0, chỉ hiển thị các role có primary_role là 2
                    return role.primary_role == 2;
                }
            }).map(function (role) {
                var selected = selectedRoleId === role.id ? 'selected' : '';
                return `<option value="${role.id}" ${selected}>${role.name}</option>`;
            }).join('');

            $('#role_id').html(roleOptions);
        }
        // Khi nút Edit được nhấn
        $(document).on('click', '#editButton', function () {
            var currentUserPrimaryRole = @json(auth()->user()->role->primary_role);
            updateRoleDropdown($('#role_id').val(), currentUserPrimaryRole, roles);

            // Cho phép chỉnh sửa tất cả các trường nếu là Super Admin
                $('#userDetailModal .modal-body').find('input, select').prop('disabled', false);
                // Hiển thị input file
                $('#avatarInput').show();
                // Thay đổi trường "Email Verified At" thành input date và bỏ disable
                var emailVerifiedAtValue = $('#emailVerifiedAt').val();
                $('#emailVerifiedAt').replaceWith(`<input type="date" id="emailVerifiedAt" class="form-control" name="email_verified_at" value="${convertDateString(emailVerifiedAtValue)}">`);
                // Thay đổi nút Edit thành Save và cập nhật id của nút
                $(this).text('Save').attr('id', 'saveButton');

            // // Nếu là Admin và người dùng được xem có role là "Client", cho phép chỉnh sửa
            // else if (currentUserRole === 'Admin' && viewedUserRole === 'Client') {
            //     $('#userDetailModal .modal-body').find('input, select').prop('disabled', false);
            //     // Hiển thị input file
            //     $('#avatarInput').show();
            //     // Thay đổi trường "Email Verified At" thành input date và bỏ disable
            //     var emailVerifiedAtValue = $('#emailVerifiedAt').val();
            //     $('#emailVerifiedAt').replaceWith(`<input type="date" id="emailVerifiedAt" class="form-control" name="email_verified_at" value="${convertDateString(emailVerifiedAtValue)}">`);
            //     // Thay đổi nút Edit thành Save và cập nhật id của nút
            //     $(this).text('Save').attr('id', 'saveButton');
            // }
            // // Nếu không thỏa mãn điều kiện trên, disable tất cả các trường
            // else {
            //     // Hiển thị thông báo và không cho phép chỉnh sửa
            //     alert('Bạn chỉ có thể chỉnh sửa thông tin của người dùng có role là "Client".');
            //     $('#userDetailModal .modal-body').find('input, select').prop('disabled', true);
            //     return; // Dừng xử lý sự kiện
            // }

            // Ngoại trừ các trường ngày khác, vẫn giữ chúng ở trạng thái disabled
            $('#createdAt, #updatedAt, #deletedAt').prop('disabled', true);


        });

        // Thêm sự kiện click cho nút Save
        $(document).on('click', '#saveButton', function () {
            // Ẩn input file
            $('#avatarInput').hide();
            // Sau khi lưu, bạn có thể đặt lại nút Save thành Edit và disable các trường lại
            $(this).text('Edit').attr('id', 'editButton');

            $('#userDetailModal .modal-body').find('input, select').prop('disabled', true);

            var formData = new FormData();
            // formData.append('_method', 'PUT');
            formData.append('userId', $('input[name="userId"]').val());
            formData.append('name', $('input[name="name"]').val());
            formData.append('email', $('input[name="email"]').val());
            formData.append('country', $('input[name="country"]').val());
            formData.append('number_phone', $('input[name="number_phone"]').val());
            formData.append('email_verified_at', $('input[name="email_verified_at"]').val());
            formData.append('role_id', $('#role_id').val());
            formData.append('is_active', $('#activeSwitch').is(':checked') ? 1 : 0);
            formData.append('_token', '{{ csrf_token() }}');
            // Thêm file avatar nếu có
            var avatarInput = $('#avatarInput')[0];
            if (avatarInput.files[0]) {
                formData.append('avatar', avatarInput.files[0]);
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var userId = $('input[name="userId"]').val();
            $.ajax({
                url: '/admin/updateUser/' + userId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    alert('User updated successfully.');
                },
                error: function (xhr, status, error) {
                    // Kiểm tra nếu có thông điệp lỗi từ phía server
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Lấy ra một trong các thông điệp lỗi
                        var firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    }
                    alert(firstError);
                }
            });
        });
        $(document).on('change', '#avatarInput', function (e) {

            var file = this.files[0];
            var fileType = file.type;
            var match = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
            if (!((fileType == match[0]) || (fileType == match[1]) || (fileType == match[2]) || (fileType == match[3]) || (fileType == match[4]))) {
                alert('Sorry, only JPG, JPEG, PNG, GIF, & SVG files are allowed to upload.');
                $('#avatarInput').val('');
                return false;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#userAvatar').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });
        var isEditMode = false;
        // Sự kiện khi modal được hiển thị
        $('#userDetailModal').on('shown.bs.modal', function () {
            if (isEditMode) {
                // Tự động chuyển sang chế độ "Edit" nếu isEditMode = true
                $('#editButton').click(); // Giả sử nút "Edit" có id là "editButton"
            }
        });

        // Sự kiện khi modal bắt đầu đóng
        $('#userDetailModal').on('hide.bs.modal', function () {
            isEditMode = false; // Reset trạng thái khi modal đóng
        });
        // Sự kiện khi nhấn nút "Edit" trong modal
        $(document).on('click', '#edit-user', function () {
            var userId = $(this).data('id');
            isEditMode = true; // Cập nhật trạng thái sang chế độ "Edit"
            // Tìm và kích hoạt click cho .view-user tương ứng với userId
            // Giả sử bạn có cách nào đó để kích hoạt click dựa trên userId
            $('.view-user[data-id="' + userId + '"]').click();
        });
    });


</script>

<!-- User Detail Modal -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailModalLabel">Chi Tiết Người Dùng</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card shadow-sm">
                    <div class="card-body">
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @can('edit-users')
                    <button type="button" class="btn btn-primary" id="editButton">Edit</button>
                @endcan

            </div>
        </div>
    </div>
</div>


