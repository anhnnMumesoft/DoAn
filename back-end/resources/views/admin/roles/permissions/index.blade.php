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
            max-width: 100px;
            width:100px;
        }

        #user-table-container th:nth-child(2),
        #user-table-container td:nth-child(2){
            width: 200px;
            max-width: 200px;
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
                    <div id="selection-info" class="d-flex justify-content-between align-items-center mb-3 mt-2 ms-2" style=" height: fit-content; ">
                        <div>
                            <a href="{{route('admin.roles')}}" class="btn btn-primary btn-rounded btn-fw  me-2">Roles</a>
                            <a href="{{route('admin.permissions')}}" class="btn btn-info btn-rounded btn-fw">Permissions</a>
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
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($permissions as $permission)
                            <tr>
{{--                                <td><input type="checkbox" class="user-checkbox" value="{{ $permission->id }}"></td>--}}
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $permission->name }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

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
        @include('admin.roles.permissions.popup-view')
    </div>

@endsection
