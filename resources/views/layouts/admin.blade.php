@extends('layouts.base')

@section('header')
    <x-layout.header.admin />
@endsection

@push('styles')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components/header.css') }}" rel="stylesheet">
    @stack('page-styles')
@endpush

@section('content')
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        @include('components.admin.breadcrumbs')
    @endif
    
    <div class="container-fluid admin-container">
        @yield('page-content')
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-common.js') }}"></script>
    @stack('page-scripts')
@endpush
