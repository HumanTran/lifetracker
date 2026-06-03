<div id="adminUsersTableArea">
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-borderless align-middle mb-0 admin-users-table">
            <colgroup>
                <col class="admin-col-id">
                <col class="admin-col-user">
                <col class="admin-col-role">
                <col class="admin-col-status">
                <col class="admin-col-login">
                <col class="admin-col-actions">
            </colgroup>

            <thead class="text-muted small border-bottom">
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Đăng nhập cuối</th>
                    <th class="text-center"></th>
                </tr>
            </thead>

            <tbody id="adminUsersTableBody">
                @forelse($users as $user)
                    @include('admin.users.partials._user_row', ['user' => $user])
                @empty
                    <tr id="adminUsersEmptyRow">
                        <td colspan="6" class="text-center text-muted py-5 fst-italic">
                            Không có người dùng phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="adminUsersCardList" class="row g-3 admin-users-card-list d-lg-none">
        @forelse($users as $user)
            <div class="col-12 col-md-6">
                @include('admin.users.partials._user_card', ['user' => $user])
            </div>
        @empty
            <div class="col-12">
                <div id="adminUsersMobileEmpty" class="admin-users-empty-card border bg-white shadow-sm rounded-4 p-4 text-muted fst-italic text-center">
                    Không có người dùng phù hợp.
                </div>
            </div>
        @endforelse
    </div>
</div>

<div id="adminUsersPaginationArea">
    @if($users->hasPages())
        <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-start align-items-md-center mt-4 gap-3 lt-pagination-wrap">
            <div class="text-muted small fw-medium">
                Hiển thị {{ $users->firstItem() }} - {{ $users->lastItem() }}
                / Tổng {{ $users->total() }} người dùng
            </div>

            <div>
                {{ $users->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>
