@props(['config', 'search' => '', 'filters' => []])

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route($config['routes']['index']) }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="{{ config('admin.search.placeholder', 'Поиск...') }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-column flex-md-row gap-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-md">
                        <i class="fas fa-search me-1 me-md-2"></i>Найти
                    </button>
                    @if($search)
                        <a href="{{ route($config['routes']['index']) }}" class="btn btn-outline-secondary btn-sm btn-md">
                            <i class="fas fa-times me-1 me-md-2"></i>Очистить
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
