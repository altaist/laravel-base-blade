@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<div class="admin-breadcrumbs">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        @if($index === count($breadcrumbs) - 1)
                            <!-- Последний элемент - не ссылка -->
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $breadcrumb['name'] }}
                            </li>
                        @else
                            <!-- Обычные элементы - ссылки -->
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}" class="text-decoration-none">
                                    {{ $breadcrumb['name'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
            
        </div>
    </div>
</div>
@endif
