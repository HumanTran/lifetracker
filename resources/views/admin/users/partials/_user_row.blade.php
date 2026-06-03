@php
    $normalizedStatus = trim((string) $user->status);

    if (! in_array($normalizedStatus, ['active', 'banned'], true)) {
        $normalizedStatus = 'active';
    }

    $statusSelectClass = match ($normalizedStatus) {
        'banned' => 'admin-status-banned',
        default => 'admin-status-active',
    };

    $lastLogin = $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at) : null;
@endphp

<tr id="admin-user-row-{{ $user->id }}"
    class="border-bottom admin-user-row admin-user-record"
    data-user-id="{{ $user->id }}"
    data-user-role="{{ $user->role }}"
    data-user-status="{{ $normalizedStatus }}"
    data-user-name="{{ \Illuminate\Support\Str::lower($user->name) }}"
    data-user-email="{{ \Illuminate\Support\Str::lower($user->email) }}">
    <td class="text-muted fw-semibold">
        <span class="admin-user-id-value">#{{ $user->id }}</span>
    </td>

    <td>
        <div class="fw-semibold text-dark admin-user-name">
            {{ $user->name }}
        </div>

        <div class="text-muted small admin-user-email">
            {{ $user->email }}
        </div>
    </td>

    <td>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="m-0 ajax-admin-user-update-form">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="{{ $normalizedStatus }}">

            <select name="role"
                class="form-select form-select-sm border-0 bg-light shadow-none fw-semibold admin-user-select admin-user-role-select">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
            </select>
        </form>
    </td>

    <td>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="m-0 ajax-admin-user-update-form">
            @csrf
            @method('PUT')

            <input type="hidden" name="role" value="{{ $user->role }}">

            <select name="status"
                class="form-select form-select-sm shadow-none fw-semibold admin-user-select admin-status-select {{ $statusSelectClass }}">
                <option value="active" {{ $normalizedStatus === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="banned" {{ $normalizedStatus === 'banned' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </form>
    </td>

    <td class="small">
        @if($lastLogin)
            <div class="admin-user-login-value">
                <div class="fw-medium text-dark admin-login-date">
                    {{ $lastLogin->format('d/m/Y') }}
                </div>

                <div class="text-muted admin-login-time">
                    {{ $lastLogin->format('H:i') }}
                </div>
            </div>
        @else
            <span class="text-muted admin-user-login-value">Chưa có</span>
        @endif
    </td>

    <td class="text-center">
        <div class="admin-user-actions d-flex justify-content-center align-items-center">
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                class="m-0 ajax-admin-user-delete-form">
                @csrf
                @method('DELETE')

                <button type="submit"
                    class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center justify-content-center p-0 admin-delete-btn"
                    aria-label="Xóa người dùng {{ $user->name }}">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
