
<div class="modal fade" id="addCountdownModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered countdown-modal-dialog">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-stopwatch text-primary me-2"></i>Thêm sự kiện
                    </h5>

                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <form id="addCountdownForm" action="{{ route('countdown.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">
                        <div id="addCountdownError" class="alert alert-danger border-0 rounded-3 d-none mb-3">
                            Không thể tạo sự kiện.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small mb-1">
                                Tên sự kiện <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="title" class="form-control shadow-none py-2" required>
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-semibold text-secondary small mb-1">
                                    Ngày diễn ra <span class="text-danger">*</span>
                                </label>

                                <input type="date" name="event_date" class="form-control shadow-none py-2" required
                                    min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-semibold text-secondary small mb-1">Màu sắc</label>

                                <input type="color" name="color_code"
                                    class="form-control form-control-color shadow-none w-100 native-color-picker" value="#3b82f6">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-grid d-sm-flex justify-content-sm-end gap-2">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Hủy</button>

                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                            <i class="fa-solid fa-check me-2"></i>Tạo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
