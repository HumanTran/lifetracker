<div class="modal fade" id="tagManageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="modal-title fw-bold text-dark" id="tagManageModalTitle">
                    <i class="fa-solid fa-tag text-primary me-2"></i>
                    Quản lý tag
                </h5>

                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <form id="tagManageForm">
                <input type="hidden" id="tagManageMode" value="create">
                <input type="hidden" id="tagManageTaskId" value="">
                <input type="hidden" id="tagManageTagId" value="">
                <input type="hidden" id="tagManageCurrentName" value="">

                <div class="modal-body p-4">
                    <div id="tagManageError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                        Không thể cập nhật tag.
                    </div>

                    <p class="text-secondary mb-3" id="tagManageDescription">
                        Đặt tên tag để gán cho công việc.
                    </p>

                    <div class="mb-0" id="tagManageNameGroup">
                        <label class="form-label fw-semibold text-secondary small mb-1">
                            Tên tag <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            id="tagManageName"
                            class="form-control shadow-none py-2"
                            maxlength="50"
                            autocomplete="off">
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-grid d-sm-flex justify-content-sm-end gap-2">
                    <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">
                        Hủy
                    </button>

                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" id="tagManageSubmit">
                        <i class="fa-solid fa-check me-2"></i>
                        Lưu tag
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
