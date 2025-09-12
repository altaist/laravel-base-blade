@props(['articles', 'title' => 'Последние статьи', 'subtitle' => 'Читайте наши новейшие публикации'])

@if($articles->count() > 0)
<section class="articles-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-5">
                    <h2 class="section-title">{{ $title }}</h2>
                    <p class="section-subtitle">{{ $subtitle }}</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            @foreach($articles as $article)
                <div class="col-lg-4 col-md-6 mb-4">
                    <x-ui.content-card :item="$article" type="article" />
                </div>
            @endforeach
        </div>
        
        <div class="row">
            <div class="col-12 text-center mt-4">
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary">
                    Все статьи
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif
