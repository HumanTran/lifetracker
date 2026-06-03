
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-pen-to-square text-primary me-2"></i>
                        Sửa công việc
                    </h5>

                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <form id="editTaskForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editTaskId">

                    <div class="modal-body p-4">
                        <div id="editTaskError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                            Không thể cập nhật công việc.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">
                                Tên công việc <span class="text-danger">*</span>
                            </label>

                            <input type="text" id="editTaskTitle" class="form-control shadow-none py-2" required maxlength="255">
                        </div>

                        <div class="row">
                            @if(!$isDaily)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold text-secondary small mb-1">
                                        Hạn chót
                                    </label>

                                    <input type="date" id="editTaskDueDate" class="form-control shadow-none py-2">
                                </div>
                            @endif

                            <div class="{{ !$isDaily ? 'col-md-6' : 'col-12' }} mb-3">
                                <label class="form-label fw-semibold text-secondary small mb-1">
                                    Mức độ ưu tiên
                                </label>

                                <select id="editTaskPriority" class="form-select shadow-none py-2" required>
                                    <option value="med">Trung bình</option>
                                    <option value="high">Cao</option>
                                    <option value="low">Thấp</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-grid d-sm-flex justify-content-sm-end gap-2">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">
                            Hủy
                        </button>

                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                            <i class="fa-solid fa-check me-2"></i>
                            Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
