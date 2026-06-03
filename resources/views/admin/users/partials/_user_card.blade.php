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
    class="admin-user-card admin-user-record d-grid gap-3 p-3 border bg-white shadow-sm rounded-4"
    data-user-id="{{ $user->id }}"
    data-user-role="{{ $user->role }}"
    data-user-status="{{ $normalizedStatus }}"
    data-user-name="{{ \Illuminate\Support\Str::lower($user->name) }}"
    data-user-email="{{ \Illuminate\Support\Str::lower($user->email) }}">
    <div class="row g-2 align-items-start">
        <div class="col overflow-hidden">
            <h3 class="h6 mb-0 fw-bold text-dark text-break">{{ $user->name }}</h3>
            <div class="small text-secondary text-break mt-1">{{ $user->email }}</div>
        </div>

        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
            class="col-auto m-0 ajax-admin-user-delete-form">
            @csrf
            @method('DELETE')

            <button type="submit"
                class="btn btn-sm btn-light border text-danger d-inline-flex align-items-center justify-content-center p-0 admin-delete-btn"
                aria-label="Xóa người dùng {{ $user->name }}">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>

    <div class="row g-2 admin-user-card-controls">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="col-12 col-sm-6 m-0 ajax-admin-user-update-form admin-user-card-field">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="{{ $normalizedStatus }}">

            <label class="d-block mb-1 text-secondary small fw-bold" for="admin-user-card-role-{{ $user->id }}">
                Vai trò
            </label>

            <select id="admin-user-card-role-{{ $user->id }}" name="role"
                class="form-select form-select-sm border-0 bg-light shadow-none fw-semibold admin-user-select admin-user-role-select">
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
            </select>
        </form>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
            class="col-12 col-sm-6 m-0 ajax-admin-user-update-form admin-user-card-field">
            @csrf
            @method('PUT')

            <input type="hidden" name="role" value="{{ $user->role }}">

            <label class="d-block mb-1 text-secondary small fw-bold" for="admin-user-card-status-{{ $user->id }}">
                Trạng thái
            </label>

            <select id="admin-user-card-status-{{ $user->id }}" name="status"
                class="form-select form-select-sm shadow-none fw-semibold admin-user-select admin-status-select {{ $statusSelectClass }}">
                <option value="active" {{ $normalizedStatus === 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="banned" {{ $normalizedStatus === 'banned' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </form>
    </div>

    <div class="row g-1 g-sm-2 align-items-sm-center admin-user-card-login">
        <span class="col-12 col-sm-4 mb-0 text-secondary small fw-bold">Đăng nhập cuối</span>

        @if($lastLogin)
            <span class="col-12 col-sm text-dark small fw-semibold text-break">
                {{ $lastLogin->format('d/m/Y') }} {{ $lastLogin->format('H:i') }}
            </span>
        @else
            <span class="col-12 col-sm text-muted small fw-semibold text-break">Chưa có</span>
        @endif
    </div>
</article>
