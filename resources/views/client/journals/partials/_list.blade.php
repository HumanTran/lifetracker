
<div class="col-12 col-xl-4 order-2 order-xl-1">
            <div class="card border-0 shadow-sm rounded-4 h-100 journal-list-card">
                <div class="card-body p-4 d-flex flex-column">

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-shrink-0">
                        <h6 class="fw-bold text-dark mb-0">Lịch sử nhật ký</h6>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm journal-create-btn" data-action="journal-create">
                            <i class="fa-solid fa-plus me-1"></i> Tạo
                        </button>
                    </div>

                    <form method="GET" action="{{ route('journals.index') }}" class="mb-3 flex-shrink-0 journal-inline-filter-form">
                        <div class="input-group mb-2 shadow-sm rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-white border-0"><i
                                    class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 shadow-none"
                                value="{{ request('search') }}" placeholder="Tìm nhật ký...">
                        </div>
                        <div class="row g-2 journal-filter-row">
                            <div class="col-12 col-sm">
                                <input type="date" name="date"
                                    class="form-control form-control-sm border shadow-sm text-muted rounded-3"
                                    value="{{ request('date') }}">
                            </div>

                            <div class="col-12 col-sm-auto d-grid">
                                <button type="submit"
                                    class="btn btn-dark btn-sm px-3 fw-medium shadow-sm rounded-3 journal-filter-btn">
                                    Lọc
                                </button>
                            </div>

                            @if(request()->hasAny(['search', 'date']) && (request('search') != '' || request('date') != ''))
                                <div class="col-12 col-sm-auto d-grid">
                                    <a href="{{ route('journals.index') }}"
                                        class="btn btn-light btn-sm px-3 border text-danger rounded-3 d-inline-flex align-items-center justify-content-center" title="Xóa bộ lọc">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>

                    <div id="journalList" class="d-flex flex-column gap-2 mb-auto journal-list-wrap">
                        @forelse($journals as $journal)
                            <div class="journal-item p-3 rounded-3"
                                data-action="journal-load"
                                data-journal-id="{{ $journal->id }}"
                                data-journal-title="{{ $journal->title }}"
                                data-journal-date="{{ $journal->created_at->format('Y-m-d') }}"
                                id="journal-{{ $journal->id }}">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1 journal-item-head">
                                    <h6 class="fw-bold text-dark mb-0 journal-title">
                                        {{ $journal->title }}
                                    </h6>
                                    <small class="text-muted journal-date-sm">{{ $journal->created_at->format('d/m') }}</small>
                                </div>
                                <p class="text-muted small mb-0 journal-excerpt">{{ strip_tags($journal->content) }}</p>
                            </div>
                            <script type="application/json" id="journal-content-{{ $journal->id }}">@json($journal->content)</script>
                        @empty
                            <div class="text-center text-muted py-4 fst-italic small">
                                @if(request('search') || request('date'))
                                    Không tìm thấy kết quả nào phù hợp.
                                @else
                                    Chưa có trang nhật ký nào.
                                @endif
                            </div>
                        @endforelse
                    </div>

                    @if($journals->hasPages())
                        <div class="mt-3 flex-shrink-0 journal-pagination-wrap">
                            {{ $journals->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
