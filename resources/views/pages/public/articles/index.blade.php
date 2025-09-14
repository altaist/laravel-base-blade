@extends('layouts.page')

@section('page-content')
    @php
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => route('home')],
            ['name' => 'Статьи']
        ];
    @endphp

    <x-layout.breadcrumbs :items="$breadcrumbs" />

    <div class="row">
        <div class="col-lg-8">
            <x-layout.page-header title="Статьи">
                <x-slot:meta>
                    <div class="page-header__meta">
                        <span><i class="fas fa-newspaper"></i> Всего статей: {{ $articles->total() }}</span>
                    </div>
                </x-slot:meta>
            </x-layout.page-header>

            <div class="articles-list">
                @foreach($articles as $article)
                    <x-articles.article-list-item :article="$article" />
                @endforeach
            </div>

            <div class="pagination-container">
                {{ $articles->links() }}
            </div>
        </div>

        <div class="col-lg-4">
            <x-articles.sidebar.wrapper>
                @if($popularArticles->isNotEmpty())
                    <x-articles.sidebar.similar-items 
                        :items="$popularArticles"
                        title="Популярные статьи"
                    />
                @endif
            </x-articles.sidebar.wrapper>
        </div>
    </div>
@endsection

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
@endpush

