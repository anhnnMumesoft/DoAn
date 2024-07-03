@if($users->isEmpty())
    <tr>
        <td colspan="8" class="text-center">No data matching</td>
    </tr>
@else
    @foreach($users as $user)
        <tr>
            <td><input type="checkbox" class="user-checkbox" value="{{ $user->id }}"></td>
            <td class="py-1">
                <img src="{{ asset($user->avatar ?? 'images/avatars/avatarDefault.png') }}" alt="image" />
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->number_phone }}</td>
            <td>{{ $user->country }}</td>
            <td>{{ $user->role->name ?? 'No role assigned' }}</td>
            <td>{{ $user->is_active ? 'Yes' : 'No' }}</td>
            <td>
                {{-- Mọi người dùng đều có thể xem chi tiết --}}
                <a class="text-primary view-user live-eye" data-id="{{ $user->id }}" title="Detail"><i class="fas fa-eye fa-lg"></i></a>

                {{-- Kiểm tra điều kiện để hiển thị nút edit và delete --}}
                @if(auth()->user()->role->primary_role == 0 || (auth()->user()->role->primary_role == 1 && $user->role->primary_role != 1 && $user->role->primary_role != 0))
                    {{-- Người dùng có primary_role == 0 có tất cả các quyền --}}
                    @can('edit-users')
                        <a class="text-warning edit-user" id="edit-user" data-id="{{ $user->id }}" title="Edit"><i class="fas fa-edit fa-lg"></i></a>
                    @endcan
                    @can('delete-users')
                        <a class="text-danger delete-user-icon" data-user-id="{{ $user->id }}" title="Delete"><i class="fas fa-trash-alt fa-lg"></i></a>
                    @endcan
                @elseif(auth()->user()->role->primary_role == 1 && $user->role->primary_role == 2)
                    {{-- Người dùng có primary_role == 1 chỉ có thể view, edit, delete người dùng có primary_role == 2 --}}
                    @can('edit-users')
                        <a class="text-warning edit-user" id="edit-user" data-id="{{ $user->id }}" title="Edit"><i class="fas fa-edit fa-lg"></i></a>
                    @endcan
                    @can('delete-users')
                        <a class="text-danger delete-user-icon" data-user-id="{{ $user->id }}" title="Delete"><i class="fas fa-trash-alt fa-lg"></i></a>
                    @endcan
                @endif
            </td>
        </tr>
    @endforeach
@endif
