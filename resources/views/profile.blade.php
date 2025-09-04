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
                    

                    @if(!auth()->user()->telegram_id && $telegramLink)
                        <div class="mt-3">
                            <a href="{{ $telegramLink }}" target="_blank" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                Привязать через Telegram
                            </a>
                        </div>
                    @endif
                    
                    <!-- Пригласить друга -->
                    <div class="mt-4 pt-3 border-top">
                        <h5 class="text-muted mb-3">Пригласить друга</h5>
                        <div class="row">
                            <div class="col-md-6" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="card-title text-info">Ссылка для авторизации</h6>
                                        <p class="card-text small text-muted">Создать ссылку для входа в систему</p>
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="generateAuthLink()">
                                            Создать ссылку
                                        </button>
                                        <div id="authLinkResult" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="card-title text-success">Ссылка для регистрации</h6>
                                        <p class="card-text small text-muted">Создать ссылку для регистрации нового пользователя</p>
                                        <button type="button" class="btn btn-outline-success btn-sm" onclick="generateRegistrationLink()">
                                            Создать ссылку
                                        </button>
                                        <div id="registrationLinkResult" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Здесь можно добавить больше функционала, например, редактирование профиля -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateAuthLink() {
    fetch('{{ route("auth-link.generate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            expires_in_minutes: 60
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const link = data.data.login_url;
            document.getElementById('authLinkResult').innerHTML = `
                <div class="alert alert-info p-2">
                    <small><strong>Ссылка:</strong> <a href="${link}" target="_blank">${link}</a></small>
                </div>
            `;
        } else {
            document.getElementById('authLinkResult').innerHTML = `
                <div class="alert alert-danger p-2">
                    <small>Ошибка: ${data.message}</small>
                </div>
            `;
        }
    })
    .catch(error => {
        document.getElementById('authLinkResult').innerHTML = `
            <div class="alert alert-danger p-2">
                <small>Ошибка: ${error.message}</small>
            </div>
        `;
    });
}

function generateRegistrationLink() {
    console.log('Начинаем создание ссылки для регистрации...');
    
    const csrfToken = '{{ csrf_token() }}';
    const url = '{{ route("auth-link.generate-registration") }}';
    
    console.log('CSRF Token:', csrfToken);
    console.log('URL:', url);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            expires_in_minutes: 60,
            name: 'Тестовый пользователь',
            email: 'test@example.com'
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            const link = data.data.registration_url;
            document.getElementById('registrationLinkResult').innerHTML = `
                <div class="alert alert-success p-2">
                    <small><strong>Ссылка:</strong> <a href="${link}" target="_blank">${link}</a></small>
                </div>
            `;
        } else {
            document.getElementById('registrationLinkResult').innerHTML = `
                <div class="alert alert-danger p-2">
                    <small>Ошибка: ${data.message}</small>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('registrationLinkResult').innerHTML = `
            <div class="alert alert-danger p-2">
                <small>Ошибка: ${error.message}</small>
            </div>
        `;
    });
}
</script>
@endsection
