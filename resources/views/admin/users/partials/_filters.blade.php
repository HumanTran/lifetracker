<form id="adminUserFilterForm" method="GET" action="{{ route('admin.users.index') }}"
    class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-3">
    @php
        $adminUserPerPage = in_array((int) request('per_page', 8), [5, 8], true)
            ? (int) request('per_page', 8)
            : 8;
    @endphp

    <input id="adminUserPerPageInput" type="hidden" name="per_page" value="{{ $adminUserPerPage }}">

    <div class="input-group admin-user-filter-search">
        <span class="input-group-text bg-light border-0">
            <i class="fa-solid fa-magnifying-glass text-muted"></i>
        </span>

        <input id="adminUserSearch" type="text" name="search"
            class="form-control bg-light border-0 shadow-none"
            placeholder="Tìm tên hoặc email..."
            value="{{ request('search') }}">
    </div>

    <div class="d-flex gap-3 flex-wrap admin-user-filter-actions">
        <select id="adminUserRoleFilter" name="role"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted admin-user-filter-role">
            <option value="">Tất cả vai trò</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        </select>

        <select id="adminUserStatusFilter" name="status"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted admin-user-filter-status">
            <option value="">Tất cả trạng thái</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Bị khóa</option>
        </select>

        <button type="button" class="btn btn-primary fw-semibold px-4"
            data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fa-solid fa-plus me-1"></i>
            Thêm admin
        </button>
    </div>
</form>
