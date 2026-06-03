<form id="taskFilterForm" method="GET" action="{{ route('tasks.index') }}" {{-- get là dữ liệu lọc nằm trên url --}}
    class="row g-3 align-items-center mb-4 pb-3 border-bottom">

    <input type="hidden" name="view" value="{{ $view }}">
    <input id="taskPerPageInput" type="hidden" name="per_page" value="{{ request('per_page', 8) }}">

    @if($isDaily)
        <input type="hidden" name="date" value="{{ $selectedDate }}">
    @endif

    <div class="col-12 col-lg">
        <div class="input-group">
            <span class="input-group-text bg-light border-0">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>

            <input id="taskSearch" name="search" type="text" class="form-control bg-light border-0 shadow-none"
                placeholder="Tìm kiếm..." value="{{ $search }}">
        </div>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <select id="tagFilter" name="tag"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted w-100">
            <option value="">Tất cả danh mục</option>

            @foreach($tags as $t)
                <option value="{{ $t->name }}" {{ $tagFilter === $t->name ? 'selected' : '' }}>
                    {{ $t->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-12 col-md-6 col-lg-3">
        <select id="priorityFilter" name="priority"
            class="form-select border-0 bg-light shadow-none fw-medium text-muted w-100">
            <option value="">Tất cả mức độ</option>
            <option value="high" {{ $priorityFilter === 'high' ? 'selected' : '' }}>Cao</option>
            <option value="med" {{ $priorityFilter === 'med' ? 'selected' : '' }}>Trung bình</option>
            <option value="low" {{ $priorityFilter === 'low' ? 'selected' : '' }}>Thấp</option>
        </select>
    </div>
</form>
