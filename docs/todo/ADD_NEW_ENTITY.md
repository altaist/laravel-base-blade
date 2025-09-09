# ДОБАВЛЕНИЕ НОВОЙ СУЩНОСТИ В АДМИНКУ

## Шаги:

### 1. Создать контроллер
```bash
php artisan make:controller Admin/EntityName/EntityNameController
```

### 2. Настроить контроллер
- Наследовать от `Controller`
- Добавить методы: `index`, `show`, `edit`, `update`, `destroy`
- Использовать существующий сервис или создать новый
- Возвращать view с правильными именами

### 3. Добавить маршруты в routes/web.php
```php
Route::resource('entityname', \App\Http\Controllers\Admin\EntityName\EntityNameController::class)->names([
    'index' => 'admin.entityname.index',
    'show' => 'admin.entityname.show',
    'create' => 'admin.entityname.create',
    'store' => 'admin.entityname.store',
    'edit' => 'admin.entityname.edit',
    'update' => 'admin.entityname.update',
    'destroy' => 'admin.entityname.destroy'
]);
```

### 4. Создать blade шаблоны
```
resources/views/admin/entityname/
├── index.blade.php    # Список (копировать из users/index.blade.php)
├── show.blade.php     # Просмотр (копировать из users/show.blade.php)
└── edit.blade.php     # Редактирование (копировать из users/edit.blade.php)
```

### 5. Адаптировать шаблоны
- Заменить `$users` на `$entityname`
- Заменить `$user` на `$entity`
- Обновить маршруты: `admin.users.*` → `admin.entityname.*`
- Адаптировать поля под модель
- Обновить breadcrumbs

### 6. Добавить в конфигурацию (опционально)
```php
// config/admin.php
'entities' => [
    'entityname' => [
        'model' => EntityName::class,
        'service' => EntityNameService::class,
        'permissions' => ['view', 'create', 'update', 'delete'],
        'searchable' => ['field1', 'field2'],
        'columns' => [...],
        'form_fields' => [...],
        'routes' => [...]
    ]
]
```

## Шаблоны для копирования:
- **index**: `admin/users/index.blade.php`
- **show**: `admin/users/show.blade.php` 
- **edit**: `admin/users/edit.blade.php`

## Проверка:
- Все маршруты работают
- CRUD операции функционируют
- Дизайн соответствует существующему
- Поиск и пагинация работают
