<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    @if(isset($showBackButton) && $showBackButton)
      <a class="navbar-brand d-flex align-items-center" href="{{ $backUrl ?? route('profile') }}">
        <i class="fas fa-arrow-left me-2"></i>
        {{ $backText ?? 'Назад' }}
      </a>
    @else
      <a class="navbar-brand" href="/">Kvadro</a>
    @endif
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/">На главную</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('user.files.index') }}">Файлы</a>
        </li>
        @can('admin')
          <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
              <i class="fas fa-cog me-1"></i>Админка
            </a>
          </li>
        @endcan
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">{{ auth()->user()->name }}</span>
        </li>
        <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link">Выход</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
