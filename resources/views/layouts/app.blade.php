<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kvadro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if(request()->routeIs('admin.*'))
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @endif
</head>
<body>
    <div class="sticky-top">
        @php
            // Автоматическое определение типа header, если не задан явно
            $headerType = $header ?? 'auto';
            
            if ($headerType === 'auto') {
                if (request()->routeIs('admin.*')) {
                    $headerType = 'admin';
                } elseif (request()->routeIs('profile') || request()->routeIs('person.*') || request()->routeIs('user.*')) {
                    $headerType = 'profile';
                } else {
                    $headerType = 'default';
                }
            }
        @endphp
        
        @include('components.headers.' . $headerType, [
            'showBackButton' => $showBackButton ?? false,
            'backUrl' => $backUrl ?? null,
            'backText' => $backText ?? 'Назад'
        ])
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="{{ asset('js/image-preview.js') }}"></script>
    
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
</body>
</html>