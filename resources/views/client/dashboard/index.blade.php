@extends('layouts.client')

@section('title', 'Life Tracker - Tổng quan')
@section('header_title', 'Tổng quan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/client-dashboard.css') }}">
@endpush

@section('content')
    @include('client.dashboard.partials._summary_cards')

    @include('client.dashboard.partials._work_and_chart')

    <div class="row mb-4 g-3 g-xl-4 dashboard-lower-row">
        @include('client.dashboard.partials._productivity_chart_card')
    </div>

    <script type="application/json" id="clientDashboardChartData">
        {!! json_encode([
            'labels' => $chartLabels,
            'data' => $chartData,
        ]) !!}
    </script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/pages/client-dashboard.js') }}"></script>
@endpush
