<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-status" content="{{ auth()->check() ? 'authenticated' : 'guest' }}">
    <title>Kvadro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(request()->routeIs('admin.*'))
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @endif
    @if(request()->routeIs('home') || request()->routeIs('article.show') || request()->routeIs('articles.index') || request()->routeIs('dashboard') || request()->routeIs('profile') || request()->routeIs('person.*') || request()->routeIs('user.*'))
        <link href="{{ asset('css/components.css') }}" rel="stylesheet">
    @endif
    <link href="{{ asset('css/components/header.css') }}" rel="stylesheet">
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
    {{-- Header для всех страниц --}}
    @if(isset($header) && $header === 'profile')
        <x-layout.header.user />
    @elseif(isset($header) && $header === 'admin')
        <x-layout.header.admin />
    @else
        <x-layout.header.public />
    @endif

    @if(request()->routeIs('admin.*'))
        @include('components.admin.breadcrumbs')
    @endif

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
    @if(request()->routeIs('home') || request()->routeIs('article.show'))
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
    </script>
    <script src="{{ asset('js/reactions.js') }}"></script>
    @stack('scripts')
</body>
</html>