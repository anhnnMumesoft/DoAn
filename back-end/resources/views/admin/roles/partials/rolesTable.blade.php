@if($roles->isEmpty())
    <tr>
        <td colspan="8" class="text-center">No data matching</td>
    </tr>
@else
    @foreach ($roles as $role)
        {{-- Điều chỉnh logic hiển thị dựa trên primary_role của người dùng hiện tại --}}
        @php
            $currentUserPrimaryRole = auth()->user()->role->primary_role;
        @endphp

        {{-- Luôn hiển thị role nếu người dùng hiện tại có primary_role == 0 --}}
        @if($currentUserPrimaryRole == 0 || ($currentUserPrimaryRole == 1 && $role->primary_role != 0))
            <tr>
                <td><input type="checkbox" class="user-checkbox" value="{{ $role->id }}"></td>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>
                    {{-- Người dùng có primary_role == 0 có thể xem tất cả --}}
                    {{-- Người dùng có primary_role == 1 chỉ có thể xem role có primary_role != 0 và != 1, trừ khi role đó có primary_role == 1 --}}
                    <a class="text-primary view-role live-eye" data-role-id="{{ $role->id }}" title="Detail"><i class="fas fa-eye fa-lg"></i></a>

                    @if($currentUserPrimaryRole == 0 || ($currentUserPrimaryRole == 1 && $role->primary_role != 1))
                        @can('edit-roles')
                            <a class="text-warning edit-role" id="edit-role" data-role-id="{{ $role->id }}" title="Edit"><i class="fas fa-edit fa-lg"></i></a>
                        @endcan
                        @can('delete-roles')
                            <a class="text-danger delete-role-icon" data-role-id="{{ $role->id }}" title="Delete"><i class="fas fa-trash-alt fa-lg"></i></a>
                        @endcan
                    @endif
                </td>
            </tr>
        @endif
    @endforeach
@endif
