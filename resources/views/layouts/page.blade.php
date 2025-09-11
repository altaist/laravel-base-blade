@extends('layouts.app')

@section('content')
<div class="page">
    <div class="container">
        @yield('page-content')
    </div>
</div>
@endsection

@push('styles')
    @stack('page-styles')
@endpush

@push('scripts')
    @stack('page-scripts')
@endpush
