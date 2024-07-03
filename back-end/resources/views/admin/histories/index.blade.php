@extends('admin.index')
@section('title')
    History
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
        #user-table-container th:first-child, #user-table-container td:first-child{
            max-width: 50px;
            width: 50px;
        }

        #user-table-container th:nth-child(2),
        #user-table-container td:nth-child(2) ,
        #user-table-container th:nth-child(3),
        #user-table-container td:nth-child(3),
        #user-table-container th:nth-child(4),
        #user-table-container td:nth-child(4) {
            max-width: 200px;
            width: 200px;
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
        $(document).ready(function() {
            var currentSort = getUrlParameter('sort') || null;
            var currentDirection = getUrlParameter('direction') || 'desc';
            var currentPage = getUrlParameter('page') || 1;
            var currentSearch = getUrlParameter('search') || '';
            var currentStartDate = getUrlParameter('startDate') || '';
            var currentEndDate = getUrlParameter('endDate') || '';
            var currentRole = getUrlParameter('role') || '';
            var currentPerPage = getUrlParameter('perPage') || '10';



            // Cập nhật input tìm kiếm với giá trị từ URL
            $('#search-input').val(currentSearch);
            $('#role-dropdown').val(currentRole);
            // $('#active-dropdown').val(currentActive);
            $('#records-per-page').val(currentPerPage);
            $('#start-date').val(currentStartDate);
            $('#end-date').val(currentEndDate);





            function fetchData(page = currentPage) {
                var search = $('#search-input').val() || '';
                var role = $('#role-dropdown').val() || '';
                var perPage = $('#records-per-page').val() || '';
                var startDate = $('#start-date').val() || '';
                var endDate = $('#end-date').val() || '';
                updateUrl(page, currentSort, currentDirection, role, search, perPage, startDate, endDate);

                $.ajax({
                    url: "{{ route('admin.histories') }}",
                    type: 'GET',
                    data: {
                        sort: currentSort,
                        direction: currentDirection,
                        search: search,
                        role: role,
                        perPage: perPage,
                        page: page,
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function (response) {
                        // Giả sử bạn trả về HTML cho bảng người dùng và phân trang
                        $('#user-table-container tbody').html(response.tableHtml);
                        $('.pagination').html(response.paginationHtml);

                        updateSortIcons();
                        updateSelectionInfo();
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }

            $('#role-dropdown, #active-dropdown, #records-per-page,#start-date,#end-date' ).on('change', function(event) {
                event.preventDefault();
                fetchData();
            });
            $('#search-input').off('keypress').on('keypress', function(e) {
                if(e.which == 13) {
                    fetchData();
                    e.preventDefault();
                }
            });
            // Sự kiện cho việc tìm kiếm
            $('#button-addon2').off('click').on('click', function() {
                fetchData();
            });

            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetchData(page);
            });

            function updateUrl(page, sort, direction, role, search, perPage,startDate, endDate) {
                // Lấy giá trị hiện tại từ các dropdown và input
                role = $('#role-dropdown').val() || role;
                search = $('#search-input').val() || search;
                perPage = $('#records-per-page').val() || perPage;
                startDate = $('#start-date').val() || '';
                endDate = $('#end-date').val() || '';
                var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                var params = [];

                // Thêm các tham số vào URL chỉ khi chúng có giá trị
                if (page != 1) params.push(`page=${page}`);
                if (sort) params.push(`sort=${sort}`);
                if (sort && direction) params.push(`direction=${direction}`);
                if (role) params.push(`role=${role}`);
                if (perPage != 10) params.push(`perPage=${perPage}`);
                if (search) params.push(`search=${encodeURIComponent(search)}`);
                if (startDate) params.push(`startDate=${startDate}`);
                if (endDate) params.push(`endDate=${endDate}`);

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
            function updateSortIcons() {
                $('.sortable').each(function() {
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
            $(document).on('click', '.sortable', function() {
                var sort = $(this).data('column');
                var direction = $(this).data('order') === 'asc' ? 'desc' : 'asc';

                // Cập nhật giá trị hiện tại và `data-order` cho cột
                currentSort = sort;
                currentDirection = direction;
                console.log("sdvdsdvds", currentSort, direction, $(this).data('order'));
                $('.sortable').data('order', 'asc'); // Đặt lại tất cả các cột về 'asc'
                $(this).data('order', direction); // Cập nhật cột hiện tại
                fetchData();
            });
            // Cập nhật thông tin đã chọn
            function updateSelectionInfo() {
                var selectedCount = $('.user-checkbox:checked').length;
                $('#selected-count').text(selectedCount);

                if (selectedCount > 0) {
                    $('#selection-info').removeClass('d-none');
                } else {
                    $('#selection-info').addClass('d-none');
                }
            }



            // Khởi tạo
            fetchData();
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
                    <h1 class="card-title mb-0">List Histories</h1>
                </div>
                {{-- Các input tìm kiếm khác --}}

                <div class="table-responsive">
                    <div class="search-container d-flex justify-content-end">
                        <div class="input-group me-1" style="width: 30%; " >
                            <span class="input-group-text">From</span>
                            <input style="    height: 38px;" type="date" id="start-date" class="form-control">
                        </div>
                        <div class="input-group me-1" style=" width: 30%; ">
                            <span class="input-group-text">To</span>
                            <input style="    height: 38px;" type="date" id="end-date" class="form-control">
                        </div>
                        <div class="input-group" style="width: 60%">
                            <input style="    height: 38px;" type="text" id="search-input" class="form-control" placeholder="Search users..." aria-label="Search users" aria-describedby="button-addon2">
                            <button class="btn btn-outline-secondary" type="button" id="button-addon2">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                    </div>
                    <table class="table table-striped" id="user-table-container">
                        <thead>
                        <tr>
{{--                            <th><input type="checkbox" id="select-all"></th>--}}
                            <th>STT</th>
                            <th class="sortable" data-column="name" data-order="asc">
                                Name
                                <i class="bi bi-caret-up-fill"></i>
                            </th>
                            <th class="sortable" data-column="email" data-order="asc">
                                Email
                                <i class="bi bi-caret-up-fill"></i>
                            </th>
                            <th class="sortable" data-column="role" data-order="asc">
                                Role
                                <i class="bi bi-caret-up-fill"></i>
                            </th>
                            <th class="sortable" data-column="created_at" data-order="asc">
                                Login At
                                <i class="bi bi-caret-up-fill"></i>
                            </th>
                            <th >
                                IP address
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($loginHistories as $history)
                            <tr>
{{--                                <td><input type="checkbox" class="user-checkbox" value="{{ $history->id }}"></td>--}}
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $history->user->name ?? 'N/A' }}</td> {{-- Hiển thị tên người dùng --}}
                                <td>{{ $history->user->email ?? 'N/A' }}</td> {{-- Hiển thị email người dùng --}}
                                <td>{{ $history->user->role->name ?? 'N/A' }}</td> {{-- Giả sử bạn muốn hiển thị role; đảm bảo quan hệ với Role được thiết lập --}}
                                <td>{{ $history->created_at }}</td> {{-- Hiển thị thời gian đăng nhập --}}
                                <td>{{ $history->ip_address }}</td> {{-- Hiển thị địa chỉ IP --}}
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
                <div class="d-flex  justify-content-between">
                    <div class="records-per-page">
                        <label for="records-per-page">Records per page:</label>
                        <select id="records-per-page" class="custom-select">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                    <div>
                        {!! $loginHistories->links() !!}
                    </div>
                </div>
            </div>
            @if(session('success'))
                <div class="position-fixed top-10 end-0 p-3" style="z-index: 11; width: auto;">
                    <div id="addUserSuccess" class="toast custom-toast hide bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header">
                            <strong class="me-auto">Successful notification</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

@endsection
