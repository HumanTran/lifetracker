<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-download text-success me-2"></i>
                    Xuất dữ liệu
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4">
                <form action="{{ url('/admin/export-report') }}" method="GET">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small">Chọn mốc thời gian:</label>
                        <select name="time" class="form-select shadow-none">
                            <option value="week" {{ ($timeFilter ?? 'month') === 'week' ? 'selected' : '' }}>Dữ liệu 7 ngày qua</option>
                            <option value="month" {{ ($timeFilter ?? 'month') === 'month' ? 'selected' : '' }}>Dữ liệu 30 ngày qua</option>
                            <option value="year" {{ ($timeFilter ?? 'month') === 'year' ? 'selected' : '' }}>Dữ liệu 365 ngày qua</option>
                            <option value="all">Toàn bộ dữ liệu tích lũy</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-file-csv"></i> Xác nhận xuất file
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
