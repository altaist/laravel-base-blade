# Универсальные компоненты форм для админки

Эта папка содержит универсальные компоненты для создания форм в админке.

## Компоненты

### 1. `form.blade.php` - Основной компонент формы
Создает полную форму с секциями и кнопками действий.

**Параметры:**
- `action` - URL действия формы
- `method` - HTTP метод (POST, PUT, PATCH)
- `formId` - ID формы
- `sections` - массив секций формы
- `cancelUrl` - URL для кнопки отмены
- `saveText` - текст кнопки сохранения
- `showReset` - показывать кнопку сброса
- `showCancel` - показывать кнопку отмены
- `class` - CSS классы
- `enctype` - тип кодировки (для загрузки файлов)

**Пример использования:**
```blade
<x-admin.universal.form 
    action="{{ route('admin.users.store') }}"
    method="POST"
    formId="userForm"
    :sections="$formSections"
    cancelUrl="{{ route('admin.users.index') }}"
    saveText="Создать пользователя" />
```

### 2. `form-card.blade.php` - Карточка секции формы
Создает карточку с заголовком и полями.

**Параметры:**
- `title` - заголовок секции
- `fields` - массив полей
- `class` - CSS классы

**Пример использования:**
```blade
<x-admin.universal.form-card 
    title="Основная информация"
    :fields="$basicFields" />
```

### 3. `form-field.blade.php` - Универсальное поле формы
Создает любое поле формы с валидацией.

**Параметры поля:**
- `name` - имя поля
- `label` - подпись поля
- `type` - тип поля (text, email, password, select, textarea, checkbox, radio, file, hidden, static)
- `value` - значение поля
- `required` - обязательное поле
- `placeholder` - placeholder
- `options` - опции для select/radio
- `rows` - количество строк для textarea
- `class` - CSS классы
- `attributes` - дополнительные атрибуты
- `help` - текст подсказки
- `readonly` - только для чтения
- `disabled` - отключено
- `empty_option` - пустая опция для select

**Примеры полей:**

```php
// Текстовое поле
$field = [
    'name' => 'name',
    'label' => 'Имя',
    'type' => 'text',
    'required' => true,
    'placeholder' => 'Введите имя'
];

// Поле выбора
$field = [
    'name' => 'role',
    'label' => 'Роль',
    'type' => 'select',
    'required' => true,
    'empty_option' => 'Выберите роль',
    'options' => [
        'admin' => 'Администратор',
        'manager' => 'Менеджер',
        'user' => 'Пользователь'
    ]
];

// Текстовое поле
$field = [
    'name' => 'description',
    'label' => 'Описание',
    'type' => 'textarea',
    'rows' => 5,
    'help' => 'Введите подробное описание'
];

// Чекбокс
$field = [
    'name' => 'active',
    'label' => 'Активен',
    'type' => 'checkbox',
    'value' => 1
];
```

### 4. `form-section.blade.php` - Секция формы
Создает секцию с возможностью сворачивания.

**Параметры:**
- `title` - заголовок секции
- `fields` - массив полей
- `class` - CSS классы
- `collapsible` - можно сворачивать
- `collapsed` - свернута по умолчанию

### 5. `field-group.blade.php` - Группа полей
Группирует поля в колонки.

**Параметры:**
- `fields` - массив полей
- `columns` - количество колонок (1-6)
- `class` - CSS классы
- `gap` - отступы между полями

## Пример полной формы

```php
// В контроллере
$formSections = [
    [
        'title' => 'Основная информация',
        'fields' => [
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Введите название'
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'url-slug'
            ],
            [
                'name' => 'status',
                'label' => 'Статус',
                'type' => 'select',
                'required' => true,
                'options' => [
                    'draft' => 'Черновик',
                    'published' => 'Опубликовано'
                ]
            ]
        ]
    ],
    [
        'title' => 'SEO информация',
        'fields' => [
            [
                'name' => 'seo_title',
                'label' => 'SEO заголовок',
                'type' => 'text'
            ],
            [
                'name' => 'seo_description',
                'label' => 'SEO описание',
                'type' => 'textarea',
                'rows' => 3
            ]
        ]
    ]
];
```

```blade
<!-- В шаблоне -->
<x-admin.universal.form 
    action="{{ route('admin.articles.store') }}"
    method="POST"
    formId="articleForm"
    :sections="$formSections"
    cancelUrl="{{ route('admin.articles.index') }}"
    saveText="Создать статью" />
```

## Преимущества

1. **DRY принцип** - нет дублирования кода
2. **Консистентность** - единообразный вид всех форм
3. **Гибкость** - легко настраиваемые компоненты
4. **Валидация** - автоматическая обработка ошибок Laravel
5. **Адаптивность** - поддержка мобильных устройств
6. **Переиспользование** - компоненты можно использовать везде
