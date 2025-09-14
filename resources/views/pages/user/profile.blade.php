@extends('layouts.app')

@section('content')
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-12">
            <!-- Заголовок страницы -->
            <x-layout.page-header title="Профиль">
                <x-slot:meta>
                    <div class="page-header__meta">
                        <span><i class="fas fa-user-circle"></i> Управление вашим профилем</span>
                    </div>
                </x-slot:meta>
            </x-layout.page-header>
            
            <!-- Информация о пользователе -->
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">{{ auth()->user()->name }}!</h3>
                    <p class="text-center text-muted mb-4">Email: {{ auth()->user()->email }}</p>
                    
                    <!-- Управление профилем -->
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                        <a href="{{ route('person.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Редактировать профиль
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Назад в кабинет
                        </a>
                        
                        @if(!(auth()->user()->telegram_id && isset($telegramLink)))
                            <a href="{{ $telegramLink ?? '#' }}" target="_blank" class="btn btn-success">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                Привязать через Telegram
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Пригласить друга -->
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-body p-5">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-user-plus me-2"></i>
                        Пригласить друга
                    </h5>
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
            </div>
            
            <!-- Реферальная ссылка -->
            @if($referralLink)
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-body p-5">
                    <h5 class="text-muted mb-3">
                        <i class="fas fa-gift me-2"></i>
                        Реферальная ссылка
                    </h5>
                    <p class="text-muted mb-4">
                        Поделитесь этой ссылкой с друзьями. Когда они зарегистрируются по вашей ссылке, вы получите бонус!
                    </p>
                    
                    <div class="input-group mb-4">
                        <input type="text" class="form-control" id="referralLink" 
                               value="{{ $referralLink->full_url }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyReferralLink()">
                            <i class="fas fa-copy me-1"></i>
                            Копировать
                        </button>
                    </div>
                    
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h5 mb-0 text-primary" id="totalClicks">{{ $referralLink->stats['total_clicks'] }}</div>
                                <small class="text-muted">Переходов</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <div class="h5 mb-0 text-success" id="completedRegistrations">{{ $referralLink->stats['completed_registrations'] }}</div>
                                <small class="text-muted">Регистраций</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0 text-info" id="conversionRate">{{ $referralLink->stats['conversion_rate'] }}%</div>
                            <small class="text-muted">Конверсия</small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshReferralStats()">
                            <i class="fas fa-sync-alt me-1"></i>
                            Обновить статистику
                        </button>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ссылка действует бессрочно
                        </small>
                    </div>
                </div>
            </div>
            @endif
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

function refreshReferralStats() {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Показываем загрузку
    button.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="me-1">
            <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
        </svg>
        Обновление...
    `;
    button.disabled = true;
    
    fetch('{{ route("referral.stats") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем статистику
            document.getElementById('totalClicks').textContent = data.data.total_clicks;
            document.getElementById('completedRegistrations').textContent = data.data.total_registrations;
            document.getElementById('conversionRate').textContent = data.data.overall_conversion_rate + '%';
            
            // Показываем успех
            button.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                Обновлено!
            `;
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-success');
        } else {
            throw new Error(data.message || 'Ошибка получения статистики');
        }
    })
    .catch(error => {
        console.error('Ошибка обновления статистики:', error);
        button.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="me-1">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/>
            </svg>
            Ошибка
        `;
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-danger');
    })
    .finally(() => {
        // Возвращаем исходный вид через 2 секунды
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success', 'btn-danger');
            button.classList.add('btn-outline-primary');
            button.disabled = false;
        }, 2000);
    });
}
</script>
@endsection
