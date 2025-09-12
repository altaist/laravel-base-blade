@extends('layouts.page')

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
                    <x-page.reactions :item="$article" />
                </x-slot:actions>
            </x-layout.page-header>

            <x-page.image-block 
                :image="$article->imgFile?->url ?? asset('images/placeholder-large.svg')"
                :alt="$article->name"
                :zoom="true"
            />

            @if($article->description)
                <x-page.description-card :description="$article->description" />
            @endif

            <x-page.content-block :content="$article->content" />

            @if($article->hasImages())
                <x-page.gallery 
                    :images="$article->getImageUrls()"
                    :zoom="true"
                />
            @endif
        </div>

        <div class="col-lg-4">
            <x-page.sidebar.wrapper>
                @php
                    $similarArticles = \App\Models\Article::where('status', \App\Enums\ArticleStatus::PUBLISHED)
                        ->where('id', '!=', $article->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(4)
                        ->get();
                @endphp
                
                <x-page.sidebar.similar-items 
                    :items="$similarArticles"
                    title="Похожие статьи"
                />
            </x-page.sidebar.wrapper>
        </div>
    </div>
@endsection

@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
@endpush
