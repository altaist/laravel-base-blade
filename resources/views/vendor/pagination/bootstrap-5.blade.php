@if ($paginator->hasPages())
    <div class="pagination-container">
        <nav>
            <ul class="pagination pagination-lg">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>

        <div class="text-center mt-3">
            <small class="text-muted">
                {{ __('pagination.total') }}: {{ $paginator->total() }}
            </small>
        </div>
    </div>

    <style>
    .pagination-container {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .pagination {
        background: white;
        padding: 0.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        gap: 0.3rem;
    }

    .page-link {
        border: 2px solid #e9ecef;
        background: white;
        color: #495057;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px !important;
        margin: 0 2px;
        padding: 0;
    }

    .page-link:hover {
        border-color: #007bff;
        color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        background: white;
        z-index: 3;
    }

    .page-link:focus {
        box-shadow: none;
        border-color: #007bff;
        color: #007bff;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border-color: #0056b3;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        transform: translateY(-2px);
    }

    .page-item.disabled .page-link {
        background: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
        border-color: #e9ecef;
    }

    .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
        background: #f8f9fa;
    }

    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        width: 45px;
        padding: 0;
    }

    .page-item:first-child .page-link {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }

    /* Мобильная адаптация */
    @media (max-width: 768px) {
        .pagination {
            padding: 0.4rem;
            gap: 0.2rem;
        }

        .page-link {
            min-width: 40px;
            height: 40px;
            font-size: 0.95rem;
        }

        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            width: 40px;
        }
    }

    @media (max-width: 576px) {
        .pagination {
            padding: 0.3rem;
            gap: 0.15rem;
        }

        .page-link {
            min-width: 35px;
            height: 35px;
            font-size: 0.9rem;
            border-width: 1px;
        }

        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            width: 35px;
        }
    }
    </style>
@endif