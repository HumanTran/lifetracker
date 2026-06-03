<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa-solid fa-user-shield text-primary me-2"></i>
                    Thêm admin mới
                </h5>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form id="addAdminForm" action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="modal-body p-4">
                    <div id="addAdminError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                        Không thể thêm admin.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">
                            Tên admin
                        </label>

                        <input type="text" name="name" class="form-control shadow-none py-2"
                            required placeholder="Nhập tên admin...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small mb-1">
                            Mật khẩu
                        </label>

                        <input type="password" name="password" class="form-control shadow-none py-2"
                            required placeholder="Nhập mật khẩu...">
                    </div>

                    <div class="text-muted small">
                        Email sẽ được hệ thống tự tạo theo tên admin.
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-grid d-sm-flex justify-content-sm-end gap-2">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">
                        Hủy
                    </button>

                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="fa-solid fa-check me-2"></i>
                        Lưu admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
