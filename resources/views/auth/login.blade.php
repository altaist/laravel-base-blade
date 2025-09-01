@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Вход</h3>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required autofocus>
                            <label for="email">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Пароль" required>
                            <label for="password">Пароль</label>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Запомнить меня</label>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit">Войти</button>
                        </div>
                    </form>
                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}">Нет аккаунта? Зарегистрироваться</a>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('password.request') }}">Забыли пароль?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
