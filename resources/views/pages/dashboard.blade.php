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
                    
                    <!-- Управление профилем -->
                    <div class="mt-3 d-flex flex-column flex-md-row gap-2">
                        <a href="{{ route('profile') }}" class="btn btn-outline-primary">
                            Профиль
                        </a>
                        
                        @if(!auth()->user()->telegram_id && $telegramLink)
                            <a href="{{ $telegramLink }}" target="_blank" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                Привязать через Telegram
                            </a>
                        @endif
                    </div>
                    
                   
                    
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

function copyReferralLink() {
    const referralInput = document.getElementById('referralLink');
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Выделяем и копируем текст
    referralInput.select();
    referralInput.setSelectionRange(0, 99999); // Для мобильных устройств
    
    try {
        document.execCommand('copy');
        
        // Показываем успешное копирование
        button.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            Скопировано!
        `;
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        // Возвращаем исходный вид через 2 секунды
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
        
    } catch (err) {
        // Fallback для современных браузеров
        if (navigator.clipboard) {
            navigator.clipboard.writeText(referralInput.value).then(() => {
                button.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    Скопировано!
                `;
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(() => {
                alert('Не удалось скопировать ссылку. Выделите и скопируйте вручную.');
            });
        } else {
            alert('Не удалось скопировать ссылку. Выделите и скопируйте вручную.');
        }
    }
}


</script>
@endsection
