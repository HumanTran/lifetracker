
<div class="col-12 col-md-6 col-xl-4 d-flex countdown-item" id="countdown-item-{{ $countdown->id }}">
    <div class="card countdown-card border-0 shadow-sm rounded-4 h-100 w-100"
        data-countdown-color="{{ $countdown->color_code ?? '#3b82f6' }}">
        <div class="card-body p-4 d-flex flex-column gap-3 countdown-card-body">
            <div class="countdown-card-header d-flex justify-content-between align-items-start gap-2 gap-md-3 mb-0">
                <div class="countdown-card-info">
                    <h5 class="fw-bold mb-1 countdown-title" title="{{ $countdown->title }}">
                        {{ $countdown->title }}
                    </h5>

                    <div class="small opacity-75 d-flex align-items-center gap-1 countdown-date">
                        <i class="fa-regular fa-calendar flex-shrink-0"></i>
                        {{ \Carbon\Carbon::parse($countdown->event_date)->format('d/m/Y') }}
                    </div>
                </div>

                <div class="countdown-card-actions d-flex align-items-center gap-2 gap-md-3">
                    <button type="button" class="btn btn-sm btn-link text-white opacity-75 shadow-none p-0 d-inline-flex align-items-center justify-content-center"
                        title="Sửa sự kiện"
                        data-countdown-id="{{ $countdown->id }}"
                        data-countdown-title="{{ $countdown->title }}"
                        data-countdown-date="{{ \Carbon\Carbon::parse($countdown->event_date)->format('Y-m-d') }}"
                        data-countdown-color="{{ $countdown->color_code ?? '#3b82f6' }}"
                        data-action="open-edit-countdown">
                        <i class="fa-solid fa-pen-to-square fs-6"></i>
                    </button>

                    <form action="{{ route('countdown.destroy', $countdown->id) }}" method="POST"
                        class="m-0 ajax-delete-countdown-form">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="btn btn-sm btn-link text-white opacity-75 shadow-none p-0 d-inline-flex align-items-center justify-content-center"
                            title="Xóa sự kiện">
                            <i class="fa-solid fa-xmark fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-1 g-md-2 mt-auto countdown-timer"
                data-date="{{ \Carbon\Carbon::parse($countdown->event_date)->format('Y-m-d') }}">
                <div class="col-3">
                    <div class="time-box h-100 text-center rounded-3">
                        <div class="num" id="d-{{ $countdown->id }}">--</div>
                        <div class="label">Ngày</div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="time-box h-100 text-center rounded-3">
                        <div class="num" id="h-{{ $countdown->id }}">--</div>
                        <div class="label">Giờ</div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="time-box h-100 text-center rounded-3">
                        <div class="num" id="m-{{ $countdown->id }}">--</div>
                        <div class="label">Phút</div>
                    </div>
                </div>

                <div class="col-3">
                    <div class="time-box h-100 text-center rounded-3">
                        <div class="num" id="s-{{ $countdown->id }}">--</div>
                        <div class="label">Giây</div>
                    </div>
                </div>
            </div>

            <div class="text-center small fw-medium d-flex align-items-center justify-content-center align-self-end w-100 message-box countdown-message-slot" id="msg-{{ $countdown->id }}">
                <i class="fa-solid fa-flag-checkered me-1"></i>
                Sự kiện đã diễn ra!
            </div>
        </div>
    </div>
</div>
