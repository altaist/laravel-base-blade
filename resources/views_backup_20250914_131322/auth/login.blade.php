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
                    <div class="text-center mt-4">
                        <div class="text-muted mb-2">Или войдите через бота</div>
                        <a href="https://t.me/{{ config('telegram.bot.name') }}" class="btn btn-primary btn-lg" target="_blank">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                            Войти через бота
                        </a>
                    </div>
                    
                    <!-- Telegram Login Widget -->
                    <div class="text-center mt-4">
                        <x-telegram-login />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
