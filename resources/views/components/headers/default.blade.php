<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Kvadro</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        @guest
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Вход</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
          </li>
        @else
          <li class="nav-item">
            <a class="nav-link" href="{{ route('profile') }}">Личный кабинет</a>
          </li>
          @can('admin')
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-cog me-1"></i>Админка
              </a>
            </li>
          @endcan
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="nav-link btn btn-link">Выход</button>
            </form>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>
