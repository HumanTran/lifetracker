
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-plus-circle text-primary me-2"></i>
                        Thêm công việc mới
                    </h5>

                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="view" value="{{ $view }}">

                    @if($isDaily)
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                    @endif

                    <div class="modal-body p-4">
                        <div id="addTaskError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                            Không thể thêm công việc.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">
                                Tên công việc <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="title" class="form-control shadow-none py-2" required>
                        </div>

                        <div class="row">
                            @if(!$isDaily)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold text-secondary small mb-1">
                                        Hạn chót
                                    </label>

                                    <input type="date" name="due_date" class="form-control shadow-none py-2">
                                </div>
                            @endif

                            <div class="{{ !$isDaily ? 'col-md-6' : 'col-12' }} mb-3">
                                <label class="form-label fw-semibold text-secondary small mb-1">
                                    Mức độ ưu tiên
                                </label>

                                <select name="priority" class="form-select shadow-none py-2">
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
                            Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
