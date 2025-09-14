@extends('layouts.base')

@section('header')
    <x-layout.header.public />
@endsection

@push('styles')
    <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('css/components/header.css') }}" rel="stylesheet">
    @stack('page-styles')
@endpush

@section('content')
    <div class="page">
        <div class="container">
            @yield('page-content')
        </div>
    </div>
@endsection

@push('scripts')
    @stack('page-scripts')
@endpush
