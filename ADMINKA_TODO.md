# ADMINKA_TODO

## Цель
Создать универсальную админку для управления сущностями (users, feedbacks, articles, news, products) с единообразным UI и переиспользуемым кодом.

## Архитектура

### 1. Структура контроллеров
```
app/Http/Controllers/Admin/
├── DashboardController.php
├── Users/UserController.php
├── Feedbacks/FeedbackController.php
├── Articles/ArticleController.php
├── Products/ProductController.php
└── News/NewsController.php
```

### 2. Базовый трейт HasAdminCrud
```php
trait HasAdminCrud
{
    protected function getEntityName(): string
    protected function getService()
    protected function getViewPath(string $view): string
    
    // Стандартные методы: index, show, create, store, edit, update, destroy
    // Используют getService() и getViewPath()
}
```

### 3. Конфигурация сущностей
```php
// config/admin.php
'entities' => [
    'users' => [
        'model' => User::class,
        'service' => UserService::class,
        'permissions' => ['view', 'create', 'update', 'delete'],
        'searchable' => ['name', 'email'],
        'columns' => [
            'id' => 'ID',
            'name' => 'Имя',
            'email' => 'Email',
            'role' => 'Роль',
            'created_at' => 'Дата регистрации'
        ],
        'form_fields' => [
            'name' => ['type' => 'text', 'required' => true],
            'email' => ['type' => 'email', 'required' => true, 'readonly' => true],
            'role' => ['type' => 'select', 'options' => ['admin', 'manager', 'user']]
        ]
    ]
]
```

### 4. Структура blade шаблонов
```
resources/views/admin/
├── layouts/
│   ├── admin.blade.php             # Основной layout
│   └── components/
│       ├── data-table.blade.php    # Универсальная таблица
│       ├── search-form.blade.php   # Универсальная форма поиска
│       ├── action-buttons.blade.php # Кнопки действий
│       └── status-badge.blade.php  # Статусные бейджи
├── components/
│   ├── admin-card.blade.php        # Универсальная карточка
│   ├── admin-form.blade.php        # Универсальная форма
│   └── admin-detail.blade.php      # Универсальный просмотр
└── [entity]/
    ├── index.blade.php             # Список (использует data-table)
    ├── show.blade.php              # Просмотр (использует admin-detail)
    ├── create.blade.php            # Создание (использует admin-form)
    └── edit.blade.php              # Редактирование (использует admin-form)
```

## Реализация

### Этап 1: Базовые компоненты
1. Создать трейт HasAdminCrud
2. Создать config/admin.php
3. Создать универсальные blade компоненты
4. Создать admin layout

### Этап 2: Рефакторинг существующих
1. Разбить AdminController на отдельные контроллеры
2. Перенести users/feedbacks на новую структуру
3. Использовать существующие сервисы (UserService, PersonService)

### Этап 3: Новые сущности
1. Создать контроллеры для articles, products, news
2. Добавить конфигурацию в config/admin.php
3. Создать blade шаблоны

## Ключевые принципы
- Использовать существующие сервисы, не создавать новые
- Контроллеры тонкие, вся логика в сервисах
- Единообразный UI через компоненты
- Простота важнее "красоты" архитектуры
- Быстрая реализация и легкая поддержка

## Маршруты
```php
Route::prefix('admin')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Стандартные CRUD маршруты для каждой сущности
    Route::resource('users', Users\UserController::class);
    Route::resource('feedbacks', Feedbacks\FeedbackController::class);
    Route::resource('articles', Articles\ArticleController::class);
    Route::resource('products', Products\ProductController::class);
    Route::resource('news', News\NewsController::class);
});
```

## Результат
- Единообразная админка для всех сущностей
- Легко добавлять новые сущности
- Переиспользуемый код
- Простая поддержка
- Готовность к масштабированию
