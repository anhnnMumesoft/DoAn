@if($loginHistories->isEmpty())
    <tr>
        <td colspan="8" class="text-center">No data matching</td>
    </tr>
@else
@foreach ($loginHistories as $history)
    <tr>
{{--        <td><input type="checkbox" class="user-checkbox" value="{{ $history->id }}"></td>--}}
        <td>{{ $loop->iteration }}</td>
        <td>{{ $history->user->name ?? 'N/A' }}</td> {{-- Hiển thị tên người dùng --}}
        <td>{{ $history->user->email ?? 'N/A' }}</td> {{-- Hiển thị email người dùng --}}
        <td>{{ $history->user->role->name ?? 'N/A' }}</td> {{-- Giả sử bạn muốn hiển thị role; đảm bảo quan hệ với Role được thiết lập --}}
        <td>{{ $history->created_at }}</td> {{-- Hiển thị thời gian đăng nhập --}}
        <td>{{ $history->ip_address }}</td> {{-- Hiển thị địa chỉ IP --}}
    </tr>
@endforeach
@endif
