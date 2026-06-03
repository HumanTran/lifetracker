<form id="adminUserFilterForm" method="GET" action="{{ route('admin.users.index') }}"
    class="row g-3 align-items-center mb-4 pb-3 border-bottom">
    @php
        $adminUserPerPage = in_array((int) request('per_page', 8), [5, 8], true)
            ? (int) request('per_page', 8)
            : 8;
    @endphp

    <input id="adminUserPerPageInput" type="hidden" name="per_page" value="{{ $adminUserPerPage }}">

    <div class="col-12 col-lg admin-user-filter-search">
        <div class="input-group">
            <span class="input-group-text bg-light border-0">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>

            <input id="adminUserSearch" type="text" name="search"
                class="form-control bg-light border-0 shadow-none"
                placeholder="Tìm tên hoặc email..."
                value="{{ request('search') }}">
        </div>
    </div>

    <div class="col-12 col-md-4 col-lg-auto">
        <select id="adminUserRoleFilter" name="role"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted w-100 admin-user-filter-role">
            <option value="">Tất cả vai trò</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        </select>
    </div>

    <div class="col-12 col-md-4 col-lg-auto">
        <select id="adminUserStatusFilter" name="status"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted w-100 admin-user-filter-status">
            <option value="">Tất cả trạng thái</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Bị khóa</option>
        </select>
    </div>

    <div class="col-12 col-md-4 col-lg-auto d-grid d-lg-block">
        <button type="button" class="btn btn-primary fw-semibold px-4 text-nowrap w-100"
            data-bs-toggle="modal" data-bs-target="#addAdminModal">
            <i class="fa-solid fa-plus me-1"></i>
            Thêm admin
        </button>
    </div>
</form>
