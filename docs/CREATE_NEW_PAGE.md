# Создание новой страницы на базе универсального шаблона page

## 1. Создать blade файл в views/
```php
@extends('layouts.page')

@section('page-content')
    {{-- Основной контент страницы --}}
@endsection

@section('sidebar')
    {{-- Содержимое сайдбара (опционально) --}}
@endsection
```

## 2. Доступные компоненты для использования:

### Основные компоненты страницы:
- `x-page.breadcrumbs` - хлебные крошки
- `x-page.header` - заголовок страницы
- `x-page.meta-info` - метаинформация (дата, автор, статистика)
- `x-page.image-block` - блок с изображением
- `x-page.description-card` - карточка с описанием
- `x-page.content-block` - блок контента
- `x-page.gallery` - галерея изображений
- `x-page.pagination` - пагинация

### Компоненты сайдбара:
- `x-page.sidebar.wrapper` - обертка сайдбара
- `x-page.sidebar.similar-items` - похожие элементы

### Компоненты реакций:
- `x-reactions.like-button` - кнопка лайка
- `x-reactions.favorite-button` - кнопка избранного

## 3. Пример использования компонентов:

```php
@extends('layouts.page')

@section('page-content')
    {{-- Хлебные крошки --}}
    <x-page.breadcrumbs :items="$breadcrumbs" />

    {{-- Заголовок --}}
    <x-page.header 
        :title="$item->title" 
        :subtitle="$item->subtitle ?? ''" 
    />

    {{-- Метаинформация --}}
    <x-page.meta-info 
        :author="$item->author" 
        :date="$item->created_at" 
        :views="$item->views_count" 
    />

    {{-- Изображение --}}
    @if($item->hasImages())
        <x-page.image-block 
            :image="$item->getFirstImageUrl()" 
            :alt="$item->title" 
        />
    @endif

    {{-- Описание --}}
    <x-page.description-card 
        :content="$item->description" 
    />

    {{-- Основной контент --}}
    <x-page.content-block 
        :content="$item->content" 
    />

    {{-- Галерея --}}
    @if($item->hasImages())
        <x-page.gallery 
            :images="$item->getImageUrls()" 
            :zoom="true" 
        />
    @endif

    {{-- Реакции --}}
    <x-page.reactions :item="$item" />

    {{-- Пагинация --}}
    @if(isset($items))
        <x-page.pagination :paginator="$items" />
    @endif
@endsection

@section('sidebar')
    <x-page.sidebar.wrapper>
        {{-- Похожие элементы --}}
        <x-page.sidebar.similar-items 
            :items="$similarItems" 
            title="Похожие статьи" 
        />
    </x-page.sidebar.wrapper>
@endsection
```

## 4. Подключение стилей:
```php
@push('page-styles')
    <link rel="stylesheet" href="{{ asset('css/components/page.css') }}">
@endpush
```

## 5. Подключение скриптов:
```php
@push('page-scripts')
    {{-- Скрипты для реакций подключаются автоматически --}}
    {{-- Дополнительные скрипты если нужны --}}
@endpush
```

## 6. Подготовка данных в контроллере:

### Для списков (с пагинацией):
```php
public function index(Request $request): View
{
    $items = Model::query()
        ->withCount(['likes', 'favorites']) // Для счетчиков реакций
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('your.index', compact('items'));
}
```

### Для детальных страниц:
```php
public function show(string $slug): View
{
    $item = Model::where('slug', $slug)->firstOrFail();
    
    // Подготовка данных для сайдбара
    $similarItems = Model::where('id', '!=', $item->id)
        ->limit(4)
        ->get();

    return view('your.show', compact('item', 'similarItems'));
}
```

## 7. Добавить роут в routes/web.php:
```php
Route::get('/your-page', [YourController::class, 'index'])->name('your.index');
Route::get('/your-page/{slug}', [YourController::class, 'show'])->name('your.show');
```

## 8. Важные замечания:

- **Реакции**: Компоненты `x-reactions.like-button` и `x-reactions.favorite-button` работают автоматически с JavaScript
- **Морфинг**: Убедитесь, что в `AppServiceProvider` настроен морфинг маппинг для вашей модели
- **Стили**: Все стили для компонентов находятся в `public/css/components/page.css`
- **JavaScript**: Реакции обрабатываются глобальным скриптом `public/js/reactions.js`
