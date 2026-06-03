@extends('layouts.client')

@section('title', 'Life Tracker - Nhật ký')
@section('header_title', 'Nhật ký')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pages/journals.css') }}">
@endpush

@section('content')
    <script type="application/json" id="journalPageData">
        {!! json_encode([
            'storeUrl' => route('journals.store'),
            'todayDate' => \Carbon\Carbon::now()->format('Y-m-d'),
            'todayDisplay' => \Carbon\Carbon::now()->format('d/m/Y'),
        ]) !!}
    </script>

    <div class="row g-4 journal-page-row">
        @include('client.journals.partials._list')

        @include('client.journals.partials._editor')
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="{{ asset('js/pages/journals.js') }}"></script>
@endpush
