
<div class="modal fade" id="addHabitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-fire text-warning me-2"></i>Thêm thói quen mới
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <form id="addHabitForm" action="{{ route('habits.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="addHabitError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                            Không thể thêm thói quen.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">Tên thói quen <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control shadow-none py-2" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                            <i class="fa-solid fa-check me-2"></i>Thêm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
