@extends('layouts.app')

@section('header')
    @include('components.header')
@endsection

@section('content')
    @include('components.hero')
    @if (auth()->check())
      <div class="container">
        <p class="text-center mt-4">Привет, {{ auth()->user()->name }}! <a href="{{ route('profile') }}">Перейти в личный кабинет</a></p>
      </div>
    @endif
@endsection
