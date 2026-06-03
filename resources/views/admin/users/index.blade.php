@extends('admin.layouts.app')

@section('title', 'Life Tracker - Quản lý người dùng')
@section('page_title', 'Người dùng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/admin-users.css') }}">
@endpush

@section('content')
    <div id="adminUserAlert" class="alert d-none border-0 rounded-3 shadow-sm mb-3"></div>

    <div class="card border-0 shadow-sm rounded-4" id="adminUsersPage">
        <div class="card-body p-4">
            @include('admin.users.partials._filters')
            @include('admin.users.partials._table', ['users' => $users])
        </div>
    </div>

    @include('admin.users.partials._add_admin_modal')
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/admin-users.js') }}"></script>
@endpush
