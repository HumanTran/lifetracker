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

<article id="admin-user-card-{{ $user->id }}"
    class="admin-user-card admin-user-record"
    data-user-id="{{ $user->id }}"
    data-user-role="{{ $user->role }}"
    data-user-status="{{ $normalizedStatus }}"
    data-user-name="{{ \Illuminate\Support\Str::lower($user->name) }}"
    data-user-email="{{ \Illuminate\Support\Str::lower($user->email) }}">
    <div class="admin-user-card-header">
        <div class="admin-user-card-identity">
            <h3 class="admin-user-card-name">{{ $user->name }}</h3>
            <div class="admin-user-card-email">{{ $user->email }}</div>
        </div>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
            class="m-0 ajax-admin-user-delete-form admin-user-card-delete-form">
            @csrf
            @method('DELETE')

            <button type="submit"
                class="btn btn-sm btn-light border text-danger admin-delete-btn"
                aria-label="Xóa người dùng {{ $user->name }}">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

    <div class="admin-user-card-controls">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="m-0 ajax-admin-user-update-form admin-user-card-field">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="{{ $normalizedStatus }}">

            <label class="admin-user-card-label" for="admin-user-card-role-{{ $user->id }}">
                Vai trò
            </label>

            <select id="admin-user-card-role-{{ $user->id }}" name="role"
                class="form-select form-select-sm border-0 bg-light shadow-none fw-semibold admin-user-select admin-user-role-select">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
            </select>
        </form>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="m-0 ajax-admin-user-update-form admin-user-card-field">
            @csrf
            @method('PUT')

            <input type="hidden" name="role" value="{{ $user->role }}">

            <label class="admin-user-card-label" for="admin-user-card-status-{{ $user->id }}">
                Trạng thái
            </label>

            <select id="admin-user-card-status-{{ $user->id }}" name="status"
                class="form-select form-select-sm shadow-none fw-semibold admin-user-select admin-status-select {{ $statusSelectClass }}">
                <option value="active" {{ $normalizedStatus === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="banned" {{ $normalizedStatus === 'banned' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </form>
    </div>

    <div class="admin-user-card-login">
        <span class="admin-user-card-label">Đăng nhập cuối</span>

        @if($lastLogin)
            <span class="admin-user-card-login-value">
                {{ $lastLogin->format('d/m/Y') }} {{ $lastLogin->format('H:i') }}
            </span>
        @else
            <span class="admin-user-card-login-value text-muted">Chưa có</span>
        @endif
    </div>
</article>
