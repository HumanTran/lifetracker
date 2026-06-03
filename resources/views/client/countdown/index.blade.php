@extends('layouts.client')

@section('title', 'Life Tracker - Đếm ngược')
@section('header_title', 'Đếm ngược')

@section('topbar_actions')
    <button class="btn btn-primary fw-semibold shadow-sm rounded-3 px-3 py-2 countdown-add-trigger" data-bs-toggle="modal"
        data-bs-target="#addCountdownModal">
        <i class="fa-solid fa-plus me-1"></i> Thêm sự kiện
    </button>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/countdown.css') }}">
@endpush

@section('content')
    @include('client.countdown.partials._list')
@endsection

@section('page_modals')
    @include('client.countdown.partials._add_modal')
    @include('client.countdown.partials._edit_modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/countdown.js') }}"></script>
@endpush
