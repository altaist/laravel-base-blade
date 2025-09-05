# Система Attachments

Система attachments позволяет привязывать файлы и внешние URL к любым моделям Laravel через морф-связи.

## Основные компоненты

### 1. AttachmentType Enum
```php
use App\Enums\AttachmentType;

// Типы attachments
AttachmentType::IMAGE     // Изображение (по умолчанию)
AttachmentType::DOCUMENT  // Документ
```

### 2. Модель Attachment
```php
use App\Models\Attachment;

// Основные поля
$attachment->related_type  // Тип связанной модели
$attachment->related_id    // ID связанной модели
$attachment->file_id       // ID файла (nullable)
$attachment->url          // Внешний URL (nullable)
$attachment->name         // Название attachment
$attachment->description  // Описание
$attachment->type         // Тип attachment (Image/Document)
```

### 3. AttachmentService
```php
use App\Services\Files\AttachmentService;

$attachmentService = app(AttachmentService::class);

// Создать attachment с файлом
$attachment = $attachmentService->createAttachment(
    relatedModel: $user,
    file: $uploadedFile,
    name: 'Аватар пользователя',
    description: 'Основное фото профиля',
    type: AttachmentType::IMAGE
);

// Создать attachment с внешней ссылкой
$attachment = $attachmentService->createAttachmentFromUrl(
    relatedModel: $user,
    url: 'https://example.com/image.jpg',
    name: 'Внешнее изображение',
    type: AttachmentType::IMAGE
);

// Удалить attachment
$attachmentService->deleteAttachment($attachment, $deleteFile = false);

// Получить attachments для модели
$attachments = $attachmentService->getAttachmentsForModel($user);
$images = $attachmentService->getImagesForModel($user);
$documents = $attachmentService->getDocumentsForModel($user);
```

## API Endpoints

### Загрузка attachment
```http
POST /api/attachments/upload
Content-Type: multipart/form-data
Authorization: Bearer {token}

{
    "file": {file},
    "related_type": "App\\Models\\User",
    "related_id": 1,
    "name": "Название attachment",
    "description": "Описание",
    "type": "image" // или "document"
}
```

### Удаление attachment
```http
DELETE /api/attachments/{attachment_id}
Authorization: Bearer {token}

{
    "delete_file": true // удалить ли физический файл
}
```

### Получение attachments
```http
GET /api/attachments/{related_type}/{related_id}?type=image
Authorization: Bearer {token}
```

## Использование в моделях

### Добавление связей в модель
```php
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    // Связь с attachments (морф)
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related');
    }

    // Связь с файлами
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}

class File extends Model
{
    // Связь с пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь с attachments
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    // Проверить, используется ли файл в attachments
    public function isUsedInAttachments(): bool
    {
        return $this->attachments()->exists();
    }
}
```

### Использование в коде
```php
$user = User::find(1);

// Получить все attachments пользователя
$attachments = $user->attachments;

// Получить все файлы пользователя
$files = $user->files;

// Получить только изображения
$images = $user->attachments()->where('type', 'image')->get();

// Создать attachment через сервис
$attachmentService = app(AttachmentService::class);
$attachment = $attachmentService->createAttachment($user, $file);

// Работа с файлами
$file = File::find(1);

// Проверить, используется ли файл в attachments
if ($file->isUsedInAttachments()) {
    echo "Файл используется в {$file->attachments_count} attachment(s)";
}

// Получить все attachments для файла
$fileAttachments = $file->attachments;

// Проверить тип файла
if ($file->isImage()) {
    echo "Это изображение";
} elseif ($file->isDocument()) {
    echo "Это документ";
}

// Получить изображения и документы пользователя
$userImages = $user->images;
$userDocuments = $user->documents;
```

## Примеры использования

### 1. Аватар пользователя
```php
$attachmentService = app(AttachmentService::class);
$attachment = $attachmentService->createAttachment(
    relatedModel: $user,
    file: $avatarFile,
    name: 'Аватар',
    type: AttachmentType::IMAGE
);
```

### 2. Документы товара
```php
$attachmentService = app(AttachmentService::class);
$attachment = $attachmentService->createAttachment(
    relatedModel: $product,
    file: $documentFile,
    name: 'Инструкция по эксплуатации',
    description: 'PDF с подробным описанием',
    type: AttachmentType::DOCUMENT
);
```

### 3. Внешние изображения
```php
$attachmentService = app(AttachmentService::class);
$attachment = $attachmentService->createAttachmentFromUrl(
    relatedModel: $article,
    url: 'https://example.com/featured-image.jpg',
    name: 'Главное изображение статьи',
    type: AttachmentType::IMAGE
);
```

## Безопасность

- Все операции требуют авторизации
- Пользователь может удалять только свои attachments
- Файлы проверяются на размер (максимум 10MB)
- Поддерживается валидация типов файлов

## Особенности

- Attachment может существовать без файла (только URL)
- При удалении attachment можно выбрать, удалять ли физический файл
- Система автоматически определяет тип attachment по MIME типу файла
- Поддержка морф-связей позволяет привязывать attachments к любым моделям
