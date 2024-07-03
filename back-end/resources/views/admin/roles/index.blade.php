@extends('admin.index')
@section('title')
    Roles
@endsection
{{-- CSS--}}
@section('css')
    <style>
        /* Tùy chỉnh màu viền cho dropdown và input search */
        #role-dropdown,
        #active-dropdown,
        #search-input {
            border: 1px solid #dcdcdc !important; /* Sử dụng !important để đảm bảo ghi đè các style khác */
        }

        #user-table-container {
            width: 1050px;
            max-width: 1050px;
        }

        #user-table-container th:first-child, #user-table-container td:first-child {
            max-width: 50px;
            width: 50px;
        }

        #user-table-container th:nth-child(2),
        #user-table-container td:nth-child(2),
        #user-table-container th:nth-child(3),
        #user-table-container td:nth-child(3) {
            width: 200px;
            max-width: 200px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #user-table-container th:nth-child(4), #user-table-container td:nth-child(4) {
            width: 150px;
            max-width: 150px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #user-table-container th:nth-child(5), #user-table-container td:nth-child(5),
        #user-table-container th:nth-child(6), #user-table-container td:nth-child(6) {
            width: 130px;
            max-width: 130px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #user-table-container th:nth-child(7), #user-table-container td:nth-child(7) {
            width: 90px;
            max-width: 90px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        #user-table-container th:nth-child(8), #user-table-container td:nth-child(8) {
            width: 100px;
            max-width: 100px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>
@endsection
@section('css')

@endsection
{{--JS--}}
@section('js')

    <script>
        $(document).ready(function () {
            var currentSort = getUrlParameter('sort') || null;
            var currentDirection = getUrlParameter('direction') || 'asc';
            var currentPage = getUrlParameter('page') || 1;
            var currentSearch = getUrlParameter('search') || '';
            var currentRole = getUrlParameter('role') || '';
            var currentPerPage = getUrlParameter('perPage') || '10';


            // Cập nhật input tìm kiếm với giá trị từ URL
            $('#search-input').val(currentSearch);
            $('#role-dropdown').val(currentRole);
            $('#records-per-page').val(currentPerPage);

            function fetchData(page = currentPage) {
                var search = $('#search-input').val() || '';
                var role = $('#role-dropdown').val() || '';
                var active = $('#active-dropdown').val() || '';
                var perPage = $('#records-per-page').val() || '';

                // Cập nhật URL
                updateUrl(page, currentSort, currentDirection, role, active, search, perPage);

                $.ajax({
                    url: "{{ route('admin.roles') }}",
                    type: 'GET',
                    data: {
                        sort: currentSort,
                        direction: currentDirection,
                        search: search,
                        role: role,
                        active: active,
                        perPage: perPage,
                        page: page
                    },
                    success: function (data) {
                        $('#user-table-container tbody').html(data.tableHtml);
                        $('.pagination').html(data.paginationHtml);
                        updateSortIcons();
                    }
                });
            }

            function updateUrl(page, sort, direction, role, active, search, perPage) {
                // Lấy giá trị hiện tại từ các dropdown và input
                role = $('#role-dropdown').val() || role;
                active = $('#active-dropdown').val() || active;
                search = $('#search-input').val() || search;
                perPage = $('#records-per-page').val() || perPage;

                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                var params = [];

                // Thêm các tham số vào URL chỉ khi chúng có giá trị
                if (page != 1) params.push(`page=${page}`);
                if (sort) params.push(`sort=${sort}`);
                if (sort && direction) params.push(`direction=${direction}`);
                if (role) params.push(`role=${role}`);
                if (active) params.push(`active=${active}`);
                if (perPage != 10) params.push(`perPage=${perPage}`);
                if (search) params.push(`search=${encodeURIComponent(search)}`);

                // Tạo chuỗi query từ mảng params
                var queryString = params.length > 0 ? `?${params.join('&')}` : '';

                window.history.pushState({path: newUrl}, '', newUrl + queryString);
            }

            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            // Gọi fetchData khi trang được tải để hiển thị dữ liệu dựa trên trạng thái URL
            fetchData();

            $('#userDetailModal').on('hidden.bs.modal', function () {
                // Gọi hàm fetchData() để tải lại dữ liệu cho bảng
                fetchData();
            });

            // Gắn sự kiện click cho phân trang
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page); // Sử dụng các tham số bộ lọc hiện tại
            });
            // Sự kiện cho việc tìm kiếm
            $('#button-addon2').off('click').on('click', function () {
                fetchData();
            });
            // Gắn sự kiện change cho dropdowns
            $('#role-dropdown, #active-dropdown,#records-per-page').on('change', function () {
                fetchData();
            });

            $('#search-input').off('keypress').on('keypress', function (e) {
                if (e.which == 13) {
                    fetchData();
                    e.preventDefault();
                }
            });

            function updateSortIcons() {
                $('.sortable').each(function () {
                    var sort = $(this).data('column');
                    if (sort === currentSort) {
                        $(this).attr('data-order', currentDirection);
                        var iconClass = currentDirection === 'asc' ? 'bi-caret-up-fill' : 'bi-caret-down-fill';
                        $(this).find('i').remove(); // Xóa icon cũ
                        $(this).append(`<i class="bi ${iconClass}"></i>`); // Thêm icon mới
                    } else {
                        $(this).find('i').remove(); // Xóa icon nếu không phải cột hiện tại
                    }
                });
            }


            // Gắn sự kiện click cho các cột có thể sắp xếp
            $(document).on('click', '.sortable', function () {
                var sort = $(this).data('column');
                var direction = $(this).data('order') === 'asc' ? 'desc' : 'asc';

                // Cập nhật giá trị hiện tại và `data-order` cho cột
                currentSort = sort;
                currentDirection = direction;
                $('.sortable').data('order', 'asc'); // Đặt lại tất cả các cột về 'asc'
                $(this).data('order', direction); // Cập nhật cột hiện tại
                fetchData();
            });
            $('#roleDetailModal').on('hidden.bs.modal', function () {
                // Gọi hàm fetchData() để tải lại dữ liệu cho bảng
                fetchData();
            });
            // Sự kiện khi nhấn nút "Delete"
            $(document).on('click', '.delete-role-icon', function () {
                var roleId = $(this).data('role-id'); // Lấy ID của role được click

                if(confirm('Are you sure you want to delete this role?')) {
                    $.ajax({
                        url: '/roles/' + roleId,
                        type: 'DELETE', // Use DELETE method
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            alert('Role deleted successfully!');
                            $('#roleDetailModal').modal('hide');
                            // Refresh the page or update the UI as needed
                            fetchData();

                        },
                        error: function(error) {
                            console.log(error);
                            alert('An error occurred. Please try again.');
                        }
                    });
                }
            });

        });



    </script>
@endsection

{{--content--}}
@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                var toastEl = document.getElementById('addUserSuccess');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        </script>
    @endif
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="card-title mb-0">List Roles </h1>
                </div>
                <div class="table-responsive">
                    <div id="selection-info" class="d-flex justify-content-between align-items-center mb-3 mt-2 ms-2"
                         style=" height: fit-content; ">
                        <div>
                            <a href="{{route('admin.roles')}}"
                               class="btn btn-primary btn-rounded btn-fw  me-2">Roles</a>
                            <a href="{{route('admin.permissions')}}"
                               class="btn btn-info btn-rounded btn-fw">Permissions</a>
                        </div>
                        <div>
                            @can('add-roles')
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal"
                                    data-bs-target="#userDetailModal">Add Role
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="search-container d-flex justify-content-end">
                        <div class="input-group" style="width: 60%">
                            <!-- Dropdown for Role -->
                            <select id="role-dropdown" class="custom-select  me-5" style="height: 38px;">
                                <option value="">Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <input style="    height: 38px;" type="text" id="search-input" class="form-control"
                                   placeholder="Search users..." aria-label="Search users"
                                   aria-describedby="button-addon2">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <table class="table table-striped" id="user-table-container">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>STT</th>
                            <th class="sortable" data-column="name" data-order="asc">
                                Name
                                <i class="bi bi-caret-up-fill"></i>
                            </th>
                            <th>
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                </div>
                <div class="d-flex  justify-content-between">
                    <div class="records-per-page">
                        <label for="records-per-page">Records per page:</label>
                        <select id="records-per-page" class="custom-select">
                            <option value="2">2</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                    <div class="pagination">
                        {!! $roles->links() !!}
                    </div>
                </div>

            </div>
            @if(session('success'))
                <div class="position-fixed top-10 end-0 p-3" style="z-index: 11; width: auto;">
                    <div id="addUserSuccess" class="toast custom-toast hide bg-success text-white" role="alert"
                         aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Successful notification</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                                    aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @include('admin.roles.popup.popup-add')
        @include('admin.roles.popup.popup-view')
    </div>

@endsection
