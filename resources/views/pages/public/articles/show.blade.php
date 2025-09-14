@extends('layouts.public')

@section('page-content')
    @php
        $breadcrumbs = [
            ['name' => 'Главная', 'url' => route('home')],
            ['name' => 'Статьи', 'url' => route('articles.index')],
            ['name' => $article->name]
        ];

        $metaInfo = [
            ['icon' => 'user', 'text' => $article->user->name ?? 'Автор'],
            ['icon' => 'calendar', 'text' => $article->created_at->format('d.m.Y')],
            ['icon' => 'clock', 'text' => $article->created_at->format('H:i')]
        ];
    @endphp

    <x-layout.breadcrumbs :items="$breadcrumbs" />

    <div class="row">
        <div class="col-lg-8">
            <x-layout.page-header :title="$article->name">
                <x-slot:meta>
                    <div class="page-header__meta">
                        @foreach($metaInfo as $meta)
                            <span><i class="fas fa-{{ $meta['icon'] }}"></i> {{ $meta['text'] }}</span>
                        @endforeach
                    </div>
                </x-slot:meta>
                <x-slot:actions>
                    <x-like-button :item="$article" :type="'article'" />
                    <x-favorite-button :item="$article" :type="'article'" />
                </x-slot:actions>
            </x-layout.page-header>

            <x-articles.image-block 
                :image="$article->imgFile?->url ?? asset('images/placeholder-large.svg')"
                :alt="$article->name"
                :zoom="true"
            />

            @if($article->description)
                <x-articles.description-card :description="$article->description" />
            @endif

            <div class="article-content-modern">
                <div class="content-card">
                    <div class="content-text">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </div>

            @if($article->hasImages())
                <x-landing.gallery 
                    :images="$article->getImageUrls()"
                    :zoom="true"
                />
            @endif
        </div>

        <div class="col-lg-4">
            <x-articles.sidebar.wrapper>
                @php
                    $similarArticles = \App\Models\Article::where('status', \App\Enums\ArticleStatus::PUBLISHED)
                        ->where('id', '!=', $article->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(4)
                        ->get();
                @endphp
                
                <x-articles.sidebar.similar-items 
                    :items="$similarArticles"
                    title="Похожие статьи"
                />
            </x-articles.sidebar.wrapper>
        </div>
    </div>
@endsection

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
@endpush
