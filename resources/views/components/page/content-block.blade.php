@props(['content'])

<div class="article-content-modern">
    <div class="content-card">
        <div class="content-text">
            {!! nl2br(e($content)) !!}
        </div>
    </div>
</div>
