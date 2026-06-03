
<div class="col-12 col-xl-8 order-1 order-xl-2">
            <div class="card border-0 shadow-sm rounded-4 h-100 journal-editor-card">
                <div class="card-body px-4 pt-4 pb-2 flex-grow-0">
                    <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-1" id="editor-header-title">Viết nhật ký mới</h5>

                            <div id="journalDatePicker"
                                class="d-inline-flex align-items-center gap-2 text-muted small mt-1 position-relative journal-editor-trigger"
                                role="button" tabindex="0">
                                <i class="fa-regular fa-calendar"></i>

                                <span id="journalDateText" class="fw-medium">
                                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                                </span>

                                <input type="date" name="created_date" id="journalDate" form="journalForm"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                    max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required
                                    class="visually-hidden-input">
                            </div>
                        </div>

                        <div class="d-flex gap-2 me-2 mt-1 journal-action-buttons-hidden" id="action-buttons">
                            <form id="deleteForm" method="POST"
                                onsubmit="event.stopPropagation(); return true;" class="m-0">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-light text-danger border shadow-sm rounded-3 d-inline-flex align-items-center justify-content-center p-0">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <form id="journalForm" action="{{ route('journals.store') }}" method="POST" class="d-flex flex-column flex-grow-1">
                    @csrf
                    <div id="method-put"></div>

                    <div class="mb-3 px-4">
                        <label for="journalTitle" class="form-label fw-bold text-dark mb-2 journal-label-lg">
                            Tiêu đề
                        </label>

                        <input type="text" name="title" id="journalTitle"
                            class="form-control form-control-lg border-0 bg-light shadow-none fw-bold rounded-3"
                            value="{{ \Carbon\Carbon::now()->format('d/m/Y') }}" required>
                    </div>

                    <div class="mb-4">
                        <div id="editor-container"></div>
                    </div>

                    <input type="hidden" name="content" id="journalContent" required>

                    <div class="d-grid d-md-flex justify-content-md-end px-4 pb-3">
                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm rounded-3"
                            data-action="sync-content">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Lưu nhật ký
                        </button>
                    </div>
                </form>
            </div>
        </div>
