
<div class="row g-4 countdown-list" id="countdownList">
        @forelse($countdowns as $countdown)
            @include('client.countdown.partials._countdown_card', ['countdown' => $countdown])
        @empty
            <div class="col-12 text-center py-5 countdown-empty-state" id="countdownEmptyState">
                <i class="fa-solid fa-stopwatch text-muted opacity-25 mb-3 countdown-empty-icon"></i>
                <h5 class="text-muted fw-bold">Chưa có sự kiện nào</h5>
            </div>
        @endforelse
    </div>
