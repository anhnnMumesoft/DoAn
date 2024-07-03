<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link mt-4" href="{{route('admin.dashboard')}}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        @can('view-users')
            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false"
                   aria-controls="form-elements">
                    <i class="menu-icon mdi mdi-account-circle-outline"></i>
                    <span class="menu-title">Manage Users</span>
                </a>
                <div class="collapse" id="form-elements" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{route('admin.users')}}">List</a></li>

                        @can('add-users')
                            <li class="nav-item"><a class="nav-link" href="{{route('admin.addUser')}}">Add</a></li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('view-roles')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#form-elements1" aria-expanded="false"
                   aria-controls="form-elements1">
                    <i class="menu-icon mdi mdi-alarm-check"></i>
                    <span class="menu-title">Role</span>
                </a>
                <div class="collapse" id="form-elements1" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{route('admin.roles')}}">List Roles</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('admin.permissions')}}"> List
                                Permissions</a></li>
                    </ul>
                </div>
            </li>
        @endcan

        @can('view-history')
            <li class="nav-item">
{{--                <a class="nav-link" href="{{route('admin.histories')}}">--}}
                    <a class="nav-link" data-bs-toggle="collapse" href="#form-elements2" aria-expanded="false">
                    <i class="mdi mdi-account-convert menu-icon"></i>
                    <span class="menu-title">Login Histories</span>
                </a>

                <div class="collapse" id="form-elements2" style="">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item"><a class="nav-link" href="{{route('admin.histories')}}">All</a></li>
                        <li class="nav-item"><a class="nav-link" href=""> Individual</a></li>
                    </ul>
                </div>
            </li>
        @endcan
    </ul>
</nav>
