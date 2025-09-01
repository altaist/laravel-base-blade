@extends('layouts.app', ['header' => 'profile'])

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Личный кабинет</h3>
                    <p>Привет, {{ auth()->user()->name }}!</p>
                    <p>Email: {{ auth()->user()->email }}</p>
                    <!-- Здесь можно добавить больше функционала, например, редактирование профиля -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection