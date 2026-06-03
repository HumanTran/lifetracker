@extends('layouts.shell')

@section('sidebar_primary')
    @include('layouts.partials.client_sidebar_primary')
@endsection

@section('sidebar_secondary')
    @include('layouts.partials.client_sidebar_secondary')
@endsection

@section('topbar_left')
    <div>
        <h4 class="fw-bold mb-0 text-dark client-page-title">
            @yield('header_title')
        </h4>
    </div>
@endsection

@section('topbar_actions')
    <div class="d-flex align-items-center gap-3">
        <span class="client-topbar-date text-muted small fw-medium">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d/m/Y') }}
        </span>
    </div>
@endsection

@section('modals')
    @yield('page_modals')
@endsection
