<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-status" content="{{ auth()->check() ? 'authenticated' : 'guest' }}">
    <meta name="auto-auth-enabled" content="{{ config('features.auto_auth.enabled') ? 'true' : 'false' }}">
    <title>Kvadro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        /* Серый фон для всей страницы */
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        /* Меньший отступ между header и контентом на мобильных */
        @media (max-width: 768px) {
            .page {
                padding: 0.5rem 0 !important;
            }
        }
    </style>
</head>
<body>
    @yield('header')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <ul class="mb-0 list-unstyled">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @yield('content')
    
    {{-- Футер --}}
    <x-layout.footer />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{ asset('js/image-preview.js') }}"></script>
    
    {{-- Подключаем автологин только для неавторизованных пользователей и если фича включена --}}
    @if(!auth()->check() && config('features.auto_auth.enabled'))
        <link href="{{ asset('css/auto-auth.css') }}" rel="stylesheet">
        <script src="{{ asset('js/composables.js') }}"></script>
        <x-auto-auth-popup />
    @endif
    
    {{-- Токен автологина передается в JavaScript через data-атрибут --}}
    @if(session('auto_auth_token'))
        <div id="auto-auth-token-data" 
             data-token="{{ session('auto_auth_token') }}" 
             style="display: none;">
        </div>
        @php session()->forget('auto_auth_token') @endphp
    @endif
    
    <script>
    // Автоматическое закрытие уведомлений через 5 секунд
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            // Создаем Bootstrap Alert объект
            const bsAlert = new bootstrap.Alert(alert);
            
            // Автоматически закрываем через 5 секунд
            setTimeout(function() {
                bsAlert.close();
            }, 5000);
        });
    });

    // Очистка localStorage при выходе из системы
    document.addEventListener('click', function(e) {
        if (e.target.matches('a[href*="logout"], button[data-action="logout"], .logout-btn')) {
            console.log('Обнаружен клик по кнопке выхода, очищаем токены...');
            
            // Очищаем токен автологина при выходе через composable
            if (window.useAuth) {
                const auth = window.useAuth();
                auth.clearAutoAuthToken();
            } else {
                // Fallback если composable не загружен
                try {
                    localStorage.removeItem('auto_auth_token');
                    // Также очищаем cookies
                    document.cookie = 'auto_auth_token=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;';
                    console.log('Токен автологина очищен при выходе (fallback)');
                } catch (error) {
                    console.warn('Не удалось очистить токен автологина:', error);
                }
            }
        }
    });
    </script>
    <script src="{{ asset('js/reactions.js') }}"></script>
    @stack('scripts')
</body>
</html>
