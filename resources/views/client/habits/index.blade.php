@extends('layouts.client')

@section('title', 'Life Tracker - Thói quen')
@section('header_title', 'Thói quen')

@section('topbar_actions')
    <button class="btn btn-primary fw-semibold shadow-sm rounded-3 px-3 py-2 habit-add-btn" data-bs-toggle="modal"
        data-bs-target="#addHabitModal">
        <i class="fa-solid fa-plus me-1"></i> Thêm thói quen
    </button>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/habits.css') }}">
@endpush

@section('content')
    <script type="application/json" id="habitPageData">
        {!! json_encode([
            'firstHabitId' => optional($habits->first())->id,
        ]) !!}
    </script>

    @include('client.habits.partials._tracker_history')
@endsection

@section('page_modals')
    @include('client.habits.partials._add_habit_modal')
    @include('client.habits.partials._edit_habit_modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/habits.js') }}"></script>
@endpush
