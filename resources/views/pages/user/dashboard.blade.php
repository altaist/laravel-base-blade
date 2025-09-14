@extends('layouts.user')

@section('page-content')
        <div class="row">
            <div class="col-12">
            <!-- Главный блок профиля -->
            <div class="page-header mb-2 mb-md-4" style="display: block !important;">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-header__title mb-1 mb-md-2">{{ auth()->user()->name }}</h1>
                        <div class="page-header__meta">
                            <span><i class="fas fa-envelope"></i> {{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-cog"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <style>
            @media (max-width: 768px) {
                .page-header {
                    padding: 1rem !important;
                    margin-bottom: 1rem !important;
                }
                .page-header__title {
                    font-size: 1.5rem !important;
                    margin-bottom: 0.5rem !important;
                }
                .page-header__meta {
                    font-size: 0.9rem;
                }
                .page-header__meta span {
                    font-size: 0.85rem;
                }
                .btn-lg {
                    padding: 0.5rem 0.75rem !important;
                    font-size: 1rem !important;
                }
            }
            </style>
            </div>
        </div>
    </div>

@endsection
