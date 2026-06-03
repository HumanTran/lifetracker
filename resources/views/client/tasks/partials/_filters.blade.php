
<form id="taskFilterForm" method="GET" action="{{ route('tasks.index') }}"
                class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-3">

                <input type="hidden" name="view" value="{{ $view }}">
                <input id="taskPerPageInput" type="hidden" name="per_page" value="{{ request('per_page', 8) }}">

                @if($isDaily)
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                @endif

                <div class="input-group task-filter-search">
                    <span class="input-group-text bg-light border-0">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>

                    <input id="taskSearch" name="search" type="text"
                        class="form-control bg-light border-0 shadow-none"
                        placeholder="Tìm kiếm..."
                        value="{{ $search }}">
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <select id="tagFilter" name="tag"
                        class="form-select border-0 bg-light shadow-none fw-medium text-muted flex-shrink-0 task-filter-tag">
                        <option value="">Tất cả danh mục</option>

                        @foreach($tags as $t)
                            <option value="{{ $t->name }}" {{ $tagFilter === $t->name ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>

                    <select id="priorityFilter" name="priority"
                        class="form-select border-0 bg-light shadow-none fw-medium text-muted flex-shrink-0 task-filter-priority">
                        <option value="">Tất cả mức độ</option>
                        <option value="high" {{ $priorityFilter === 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="med" {{ $priorityFilter === 'med' ? 'selected' : '' }}>Trung bình</option>
                        <option value="low" {{ $priorityFilter === 'low' ? 'selected' : '' }}>Thấp</option>
                    </select>
                </div>
            </form>
