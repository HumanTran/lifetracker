@extends('admin.layouts.app')

@section('title', 'Life Tracker - Tổng quan vận hành')
@section('page_title', 'Dashboard')

@section('topbar_actions')
    <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}" class="m-0 flex-shrink-0">
        <select id="adminTimeFilter" name="time" class="form-select form-select-sm shadow-none">
            <option value="week" {{ ($timeFilter ?? 'month') === 'week' ? 'selected' : '' }}>Tuần</option>
            <option value="month" {{ ($timeFilter ?? 'month') === 'month' ? 'selected' : '' }}>Tháng</option>
            <option value="year" {{ ($timeFilter ?? 'month') === 'year' ? 'selected' : '' }}>Năm</option>
        </select>
    </form>

    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal"
        data-bs-target="#exportModal">
        <i class="fa-solid fa-download"></i> Xuất báo cáo
    </button>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard.css') }}">
@endpush

@section('content')
    <div id="adminDashboardPage">
        <div class="container-fluid px-0">
            @include('admin.dashboard.partials._stats')
            @include('admin.dashboard.partials._charts')
        </div>

        <script type="application/json" id="adminDashboardChartData">
            {!! json_encode([
                'growthDates' => $growthDates,
                'growthTotals' => $growthTotals,
                'dauTotals' => $dauTotals,
                'featureLabels' => array_keys($featureUsage),
                'featureValues' => array_values($featureUsage),
            ]) !!}
        </script>
    </div>
@endsection

@section('modals')
    @include('admin.dashboard.partials._export_modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/pages/admin-dashboard.js') }}"></script>
@endpush
