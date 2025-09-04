@extends('layouts.app')

@section('content')
    @include('components.hero')
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <!-- Современный заголовок секции -->
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold text-dark mb-3">Обратная связь</h2>
                    <p class="lead text-muted">Мы всегда рады услышать ваше мнение и ответить на вопросы</p>
                    <div class="mx-auto" style="width: 60px; height: 3px; background: linear-gradient(90deg, #007bff, #6f42c1); border-radius: 2px;"></div>
                </div>
                
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-4">
                        <x-feedback-form />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection