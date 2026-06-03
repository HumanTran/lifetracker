@extends('layouts.client')

@section('title', 'Life Tracker - Quản lý Công việc')
@section('header_title', 'Công việc')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/tasks.css') }}">
@endpush

@section('content')
    <div class="card border-0 shadow-sm rounded-4 task-page-card">
        <div class="card-body p-3 p-md-4">
            @php
                $view = $view ?? 'daily';
                $isDaily = $view === 'daily';
                $selectedDate = $selectedDate ?? \Carbon\Carbon::today()->toDateString();

                $search = $search ?? request('search', '');
                $tagFilter = $tagFilter ?? request('tag', '');
                $priorityFilter = $priorityFilter ?? request('priority', '');
            @endphp

            <script type="application/json" id="taskPageData">
                {!! json_encode([
                    'view' => $view,
                    'selectedDate' => $selectedDate,
                    'isDaily' => $isDaily,
                    'indexUrl' => route('tasks.index'),
                ]) !!}  {{-- {!!...!!} là để json được in ra đúng dạng, ko bị biến dấu ngoặc kép thành html --}}
            </script>

            @include('client.tasks.partials._filters')

            @include('client.tasks.partials._toolbar')
            @include('client.tasks.partials._table')
        </div>
    </div>
@endsection

@section('page_modals')
    @include('client.tasks.partials._add_task_modal')
    @include('client.tasks.partials._edit_task_modal')
    @include('client.tasks.partials._tag_modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/tasks.js') }}"></script>
    <script src="{{ asset('js/pages/tasks/tags.js') }}"></script>
    <script src="{{ asset('js/pages/tasks/actions.js') }}"></script>
    <script src="{{ asset('js/pages/tasks/tag-form.js') }}"></script>
    <script src="{{ asset('js/pages/tasks/filters.js') }}"></script>
@endpush
