# Рефакторинг структуры Blade Views

## Текущие проблемы

1. **Дублирование компонентов:**
   - 3 версии breadcrumbs (layout, page, admin) - разные стили
   - 2 версии favorite-button (components/ и reactions/) - разные API
   - 2 версии like-button (components/ и reactions/) - разные API
   - 2 версии articles-section (articles/ и content/) - дублирование

2. **Нелогичная группировка:**
   - UI компоненты разбросаны по разным папкам
   - Админские формы в разных местах
   - Landing компоненты отдельно от основных

3. **Смешанная логика:**
   - Страницы в pages/ и admin/
   - Компоненты в разных контекстах

## Новая структура

```
resources/views/
├── components/
│   ├── ui/                          # Базовые визуальные компоненты
│   │   ├── button.blade.php
│   │   ├── card.blade.php
│   │   ├── breadcrumbs.blade.php    # Универсальный с вариантами
│   │   └── content-card.blade.php
│   ├── layout/                      # Компоненты макета
│   │   ├── header/
│   │   │   ├── main.blade.php       # Основной сайт
│   │   │   ├── user.blade.php       # Личный кабинет
│   │   │   └── admin.blade.php      # Админка
│   │   ├── footer.blade.php         # Общий для всех
│   │   └── page-header.blade.php
│   ├── reactions/                   # Интерактивные компоненты с бизнес-логикой
│   │   ├── like-button.blade.php
│   │   ├── favorite-button.blade.php
│   │   ├── user-menu.blade.php
│   │   └── telegram-login.blade.php
│   ├── forms/                       # Пользовательские формы
│   │   ├── feedback-form.blade.php
│   │   └── person-edit-form.blade.php
│   ├── admin/                       # Админские компоненты
│   │   ├── user-form.blade.php
│   │   ├── action-buttons.blade.php
│   │   ├── breadcrumbs.blade.php
│   │   ├── admin-form.blade.php     # Переместить из admin/components/
│   │   ├── admin-card.blade.php     # Переместить из admin/components/
│   │   ├── admin-detail.blade.php   # Переместить из admin/components/
│   │   ├── data-table.blade.php     # Переместить из admin/layouts/components/
│   │   ├── search-form.blade.php    # Переместить из admin/layouts/components/
│   │   ├── status-badge.blade.php   # Переместить из admin/layouts/components/
│   │   └── universal/
│   │       ├── field-group.blade.php
│   │       ├── form-card.blade.php
│   │       ├── form-field.blade.php
│   │       ├── form-section.blade.php
│   │       └── form.blade.php
│   ├── articles/                    # Компоненты для статей (списки + отображение)
│   │   ├── article-card.blade.php
│   │   ├── articles-section.blade.php
│   │   ├── article-list-item.blade.php # Переместить из content/
│   │   ├── image-block.blade.php        # Переместить из page/
│   │   ├── description-card.blade.php   # Переместить из page/
│   │   ├── meta-info.blade.php          # Переместить из page/
│   │   └── sidebar/
│   │       ├── similar-items.blade.php  # Переместить из page/sidebar/
│   │       └── wrapper.blade.php        # Переместить из page/sidebar/
│   └── landing/                     # Лендинг компоненты
│       ├── cta.blade.php
│       ├── features.blade.php
│       ├── feedback-form.blade.php
│       ├── gallery.blade.php
│       ├── hero.blade.php
│       ├── pricing.blade.php
│       └── testimonials.blade.php
├── pages/
│   ├── public/                      # Публичные страницы
│   │   ├── home.blade.php
│   │   ├── home1.blade.php
│   │   └── articles/
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   ├── user/                        # Личный кабинет
│   │   ├── dashboard.blade.php
│   │   ├── profile.blade.php
│   │   ├── person-edit.blade.php
│   │   └── files/
│   │       └── index.blade.php
│   ├── admin/                       # Админка
│   │   ├── dashboard.blade.php
│   │   ├── articles/
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   ├── users/
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── index.blade.php
│   │   │   └── show.blade.php
│   │   └── feedbacks/
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   └── auth/                        # Аутентификация
│       ├── login.blade.php
│       ├── register.blade.php
│       └── forgot-password.blade.php
└── layouts/
    ├── public.blade.php             # Основной сайт
    ├── user.blade.php               # Личный кабинет  
    └── admin.blade.php              # Админка
```

## План рефакторинга

### Этап 1: Подготовка
1. Создать резервную копию текущей структуры
2. Создать новые папки согласно структуре
3. Проанализировать все импорты компонентов в файлах

### Этап 2: Перемещение компонентов
1. **UI компоненты** - переместить в `components/ui/`
2. **Layout компоненты** - переместить в `components/layout/`
3. **Admin компоненты** - собрать все в `components/admin/`
4. **Articles компоненты** - собрать в `components/articles/` (включая из page/)
5. **Landing компоненты** - переместить в `components/landing/`
6. **Forms компоненты** - переместить в `components/forms/`
7. **Reactions компоненты** - переместить в `components/reactions/`

### Этап 3: Устранение дублирования
1. **Удалить дублирующиеся файлы из page/:**
   - `page/reactions.blade.php` (использовать reactions/ компоненты)
   - `page/pagination.blade.php` (использовать {{ $items->links() }})
   - `page/gallery.blade.php` (использовать landing/gallery.blade.php)
   - `page/content-block.blade.php` (использовать ui/card.blade.php)
   - `page/breadcrumbs.blade.php` (не используется)
   - `page/header.blade.php` (не используется)
2. Объединить breadcrumbs в универсальный компонент
3. Переместить уникальные компоненты из page/ в articles/

### Этап 4: Перемещение страниц
1. **Public страницы** - переместить в `pages/public/`
2. **User страницы** - переместить в `pages/user/`
3. **Admin страницы** - переместить в `pages/admin/`
4. **Auth страницы** - переместить в `pages/auth/`

### Этап 5: Обновление импортов
1. Найти все файлы с импортами компонентов
2. Обновить пути к компонентам
3. Обновить пути к страницам

### Этап 6: Тестирование
1. Проверить работу всех страниц
2. Проверить работу всех компонентов
3. Исправить ошибки

## Инструкции для AI-помощника

### Промт для рефакторинга:

```
Мне нужно выполнить рефакторинг структуры Blade views в Laravel проекте. 

ТЕКУЩАЯ СТРУКТУРА:
resources/views/
├── admin/ (страницы админки + компоненты)
├── articles/ (страницы статей)
├── auth/ (страницы аутентификации)
├── components/ (все компоненты)
├── layouts/ (лейауты)
└── pages/ (некоторые страницы)

НОВАЯ СТРУКТУРА:
resources/views/
├── components/
│   ├── ui/ (базовые UI компоненты)
│   ├── layout/ (компоненты макета)
│   ├── interactions/ (интерактивные компоненты)
│   ├── forms/ (пользовательские формы)
│   ├── admin/ (админские компоненты)
│   ├── articles/ (компоненты статей)
│   ├── page/ (компоненты страниц статей)
│   └── landing/ (лендинг компоненты)
├── pages/
│   ├── public/ (публичные страницы)
│   ├── user/ (личный кабинет)
│   ├── admin/ (админка)
│   └── auth/ (аутентификация)
└── layouts/ (3 лейаута)

ЗАДАЧИ:
1. Создать новую структуру папок
2. Переместить файлы согласно новой структуре
3. Устранить дублирование компонентов
4. Обновить все импорты в файлах
5. Проверить работоспособность

ПРИНЦИПЫ:
- ui/ - базовые визуальные компоненты (button, card, breadcrumbs)
- reactions/ - интерактивные компоненты с бизнес-логикой (like, favorite, user-menu)
- admin/ - все админские компоненты в одном месте
- articles/ - компоненты для статей (списки + отображение содержимого)
- landing/ - компоненты лендинга
- forms/ - пользовательские формы

ДУБЛИРОВАНИЕ ДЛЯ УСТРАНЕНИЯ:
- breadcrumbs (3 версии) → один универсальный
- favorite-button (2 версии) → reactions/ версия
- like-button (2 версии) → reactions/ версия
- articles-section (2 версии) → articles/ версия
- page/reactions → использовать reactions/ компоненты напрямую
- page/pagination → использовать {{ $items->links() }}
- page/gallery → использовать landing/gallery
- page/content-block → использовать ui/card + стандартный вывод

Выполни рефакторинг пошагово, начиная с создания структуры папок и перемещения файлов.
```

## Детальные инструкции по перемещению

### 1. Создание структуры папок
```bash
mkdir -p resources/views/components/{ui,layout/{header},interactions,forms,admin/universal,articles,page/sidebar,landing}
mkdir -p resources/views/pages/{public/articles,user/files,admin/{articles,users,feedbacks},auth}
```

### 2. Перемещение UI компонентов
```bash
# Базовые UI
mv resources/views/components/ui/button.blade.php resources/views/components/ui/
mv resources/views/components/ui/card.blade.php resources/views/components/ui/
mv resources/views/components/ui/content-card.blade.php resources/views/components/ui/

# Breadcrumbs (объединить в один)
# Оставить components/layout/breadcrumbs.blade.php как основу
# Удалить components/page/breadcrumbs.blade.php
# Удалить components/admin/breadcrumbs.blade.php
```

### 3. Перемещение Layout компонентов
```bash
# Headers
mv resources/views/components/headers/header.blade.php resources/views/components/layout/header/main.blade.php
mv resources/views/components/headers/header1.blade.php resources/views/components/layout/header/user.blade.php
mv resources/views/components/headers/admin.blade.php resources/views/components/layout/header/admin.blade.php
mv resources/views/components/headers/profile.blade.php resources/views/components/layout/header/
mv resources/views/components/headers/edit.blade.php resources/views/components/layout/header/
mv resources/views/components/headers/detail.blade.php resources/views/components/layout/header/

# Footer
mv resources/views/components/footer.blade.php resources/views/components/layout/

# Page header
mv resources/views/components/layout/page-header.blade.php resources/views/components/layout/
```

### 4. Перемещение Reactions компонентов
```bash
# Reactions (использовать reactions/ версии)
mv resources/views/components/reactions/like-button.blade.php resources/views/components/reactions/
mv resources/views/components/reactions/favorite-button.blade.php resources/views/components/reactions/
# Удалить старые версии
rm resources/views/components/like-button.blade.php
rm resources/views/components/favorite-button.blade.php

# User menu
mv resources/views/components/user-menu.blade.php resources/views/components/reactions/

# Telegram login
mv resources/views/components/telegram-login.blade.php resources/views/components/reactions/
```

### 5. Перемещение Forms компонентов
```bash
mv resources/views/components/feedback-form.blade.php resources/views/components/forms/
mv resources/views/components/person-edit-form.blade.php resources/views/components/forms/
```

### 6. Перемещение Admin компонентов
```bash
# Из components/admin/
mv resources/views/components/admin/user-form.blade.php resources/views/components/admin/
mv resources/views/components/admin/action-buttons.blade.php resources/views/components/admin/
mv resources/views/components/admin/breadcrumbs.blade.php resources/views/components/admin/
mv resources/views/components/admin/universal/ resources/views/components/admin/

# Из admin/components/
mv resources/views/admin/components/admin-form.blade.php resources/views/components/admin/
mv resources/views/admin/components/admin-detail.blade.php resources/views/components/admin/
mv resources/views/admin/components/admin-card.blade.php resources/views/components/admin/

# Из admin/layouts/components/
mv resources/views/admin/layouts/components/action-buttons.blade.php resources/views/components/admin/
mv resources/views/admin/layouts/components/data-table.blade.php resources/views/components/admin/
mv resources/views/admin/layouts/components/search-form.blade.php resources/views/components/admin/
mv resources/views/admin/layouts/components/status-badge.blade.php resources/views/components/admin/
```

### 7. Перемещение Articles компонентов
```bash
# Articles
mv resources/views/components/articles/ resources/views/components/articles/
# Переместить article-list-item из content/
mv resources/views/components/content/article-list-item.blade.php resources/views/components/articles/
# Удалить дублирующуюся articles-section
rm resources/views/components/content/articles-section.blade.php

# Переместить уникальные компоненты из page/ в articles/
mv resources/views/components/page/image-block.blade.php resources/views/components/articles/
mv resources/views/components/page/description-card.blade.php resources/views/components/articles/
mv resources/views/components/page/meta-info.blade.php resources/views/components/articles/
mv resources/views/components/page/sidebar/ resources/views/components/articles/sidebar/

# Удалить дублирующиеся компоненты из page/
rm resources/views/components/page/reactions.blade.php
rm resources/views/components/page/pagination.blade.php
rm resources/views/components/page/gallery.blade.php
rm resources/views/components/page/content-block.blade.php
rm resources/views/components/page/breadcrumbs.blade.php
rm resources/views/components/page/header.blade.php

# Удалить пустую папку page/
rmdir resources/views/components/page/
```

### 8. Перемещение Landing компонентов
```bash
mv resources/views/components/landing/ resources/views/components/landing/
```

### 9. Перемещение страниц
```bash
# Public страницы
mv resources/views/pages/home.blade.php resources/views/pages/public/
mv resources/views/pages/home1.blade.php resources/views/pages/public/
mv resources/views/articles/ resources/views/pages/public/articles/

# User страницы
mv resources/views/pages/dashboard.blade.php resources/views/pages/user/
mv resources/views/pages/profile.blade.php resources/views/pages/user/
mv resources/views/pages/person-edit.blade.php resources/views/pages/user/
mv resources/views/pages/user/ resources/views/pages/user/

# Admin страницы
mv resources/views/admin/dashboard.blade.php resources/views/pages/admin/
mv resources/views/admin/articles/ resources/views/pages/admin/
mv resources/views/admin/users/ resources/views/pages/admin/
mv resources/views/admin/feedbacks/ resources/views/pages/admin/

# Auth страницы
mv resources/views/auth/ resources/views/pages/auth/
```

### 10. Обновление импортов

Найти и заменить все импорты компонентов:

```bash
# Поиск всех импортов
grep -r "components\." resources/views/ --include="*.blade.php"
grep -r "admin\." resources/views/ --include="*.blade.php"
grep -r "articles\." resources/views/ --include="*.blade.php"
```

Основные замены:
- `@include('components.favorite-button')` → `@include('components.reactions.favorite-button')`
- `@include('components.like-button')` → `@include('components.reactions.like-button')`
- `@include('components.user-menu')` → `@include('components.reactions.user-menu')`
- `@include('admin.components.admin-form')` → `@include('components.admin.admin-form')`
- `@include('components.content.article-list-item')` → `@include('components.articles.article-list-item')`
- `x-page.reactions` → `x-reactions.like-button` + `x-reactions.favorite-button`
- `x-page.pagination` → `{{ $items->links() }}`
- `x-page.gallery` → `x-landing.gallery`
- `x-page.content-block` → `x-ui.card` + стандартный вывод
- `x-page.image-block` → `x-articles.image-block`
- `x-page.description-card` → `x-articles.description-card`
- `x-page.meta-info` → `x-articles.meta-info`
- `x-page.sidebar.wrapper` → `x-articles.sidebar.wrapper`
- `x-page.sidebar.similar-items` → `x-articles.sidebar.similar-items`

### 11. Очистка

Удалить пустые папки:
```bash
rmdir resources/views/admin/components
rmdir resources/views/admin/layouts/components
rmdir resources/views/admin/layouts
rmdir resources/views/admin
rmdir resources/views/components/headers
rmdir resources/views/components/content
rmdir resources/views/components/reactions
```

## Проверка после рефакторинга

1. **Проверить все страницы:**
   - Главная страница
   - Страницы статей
   - Личный кабинет
   - Админка
   - Аутентификация

2. **Проверить все компоненты:**
   - UI компоненты
   - Layout компоненты
   - Interactions компоненты
   - Admin компоненты
   - Articles компоненты

3. **Проверить импорты:**
   - Все компоненты загружаются
   - Нет ошибок 404
   - Нет ошибок в логах

## Преимущества новой структуры

1. **Четкое разделение по контекстам** - легко найти нужный компонент
2. **Устранение дублирования** - один компонент для одной задачи
3. **Логичная группировка** - связанные компоненты рядом
4. **Легкая расширяемость** - новые компоненты в правильные папки
5. **Переиспользование** - общие компоненты в ui/ и reactions/
