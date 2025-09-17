# Telegram Architecture Refactoring Plan

## Current Problems
- Hardcoded two bots (bot, admin_bot) in TelegramService
- Code duplication for bot operations
- Difficult to add new bots
- User can only be linked to one bot via telegram_id
- Commands are bot-specific but architecture doesn't reflect this

## Proposed Architecture

### 1. New File Structure
```
app/Services/Telegram/
├── TelegramBotManager.php          # Central bot manager
├── TelegramBot.php                 # Individual bot class
├── TelegramService.php             # Refactored main service
├── Base/
│   └── BaseTelegramCommand.php     # Updated base command
├── Commands/
│   ├── Main/                       # Commands for main bot
│   │   ├── StartCommand.php
│   │   ├── ProfileCommand.php
│   │   └── AboutCommand.php
│   ├── Admin/                      # Commands for admin bot
│   │   ├── StartCommand.php
│   │   ├── UsersCommand.php
│   │   └── AboutCommand.php
│   └── Manager/                    # Commands for manager bot
│       ├── StartCommand.php
│       └── StatsCommand.php
└── TelegramBotService.php          # Updated bot service

app/Models/
└── UserTelegramBot.php             # New model for user-bot relations

database/migrations/
└── create_user_telegram_bots_table.php
```

### 2. Database Changes
```php
// Migration: create_user_telegram_bots_table
Schema::create('user_telegram_bots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('bot_name'); // 'main', 'admin', 'manager'
    $table->string('telegram_id');
    $table->json('bot_data')->nullable();
    $table->timestamps();
    
    $table->unique(['bot_name', 'telegram_id']);
    $table->index(['user_id', 'bot_name']);
});
```

### 3. New Classes

#### TelegramBotManager
```php
class TelegramBotManager
{
    private array $bots = [];
    
    public function registerBot(string $name, string $token, ?string $chatId = null): void
    public function getBot(string $name): TelegramBot
    public function getAllBots(): array
    public function getBotByToken(string $token): ?TelegramBot
}
```

#### TelegramBot
```php
class TelegramBot
{
    public function __construct(
        private string $name,
        private string $token,
        private ?string $chatId = null
    ) {}
    
    public function sendMessage(...): bool
    public function sendMessageWithKeyboard(...): bool
    public function answerCallbackQuery(...): bool
    // All API methods
}
```

#### UserTelegramBot Model
```php
class UserTelegramBot extends Model
{
    protected $fillable = ['user_id', 'bot_name', 'telegram_id', 'bot_data'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 4. Updated Configuration
```php
// config/telegram.php
return [
    'bots' => [
        'main' => [
            'name' => env('TELEGRAM_BOT_NAME'),
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\Main\StartCommand::class,
                'profile' => \App\Services\Telegram\Commands\Main\ProfileCommand::class,
                'about' => \App\Services\Telegram\Commands\Main\AboutCommand::class,
                'auth' => \App\Services\Telegram\Commands\Main\AuthLinkCommand::class,
                'default' => \App\Services\Telegram\Commands\Main\DefaultCommand::class,
            ]
        ],
        'admin' => [
            'name' => env('TELEGRAM_ADMIN_BOT_NAME'),
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\Admin\StartCommand::class,
                'users' => \App\Services\Telegram\Commands\Admin\UsersCommand::class,
                'about' => \App\Services\Telegram\Commands\Admin\AboutCommand::class,
            ]
        ],
        'manager' => [
            'name' => env('TELEGRAM_MANAGER_BOT_NAME'),
            'token' => env('TELEGRAM_MANAGER_BOT_TOKEN'),
            'commands' => [
                'start' => \App\Services\Telegram\Commands\Manager\StartCommand::class,
                'stats' => \App\Services\Telegram\Commands\Manager\StatsCommand::class,
            ]
        ]
    ]
];
```

### 5. Updated User Model
```php
// Add to User model
public function telegramBots()
{
    return $this->hasMany(UserTelegramBot::class);
}

public function getTelegramIdForBot(string $botName): ?string
{
    return $this->telegramBots()
        ->where('bot_name', $botName)
        ->value('telegram_id');
}

public function hasTelegramBot(string $botName): bool
{
    return $this->telegramBots()
        ->where('bot_name', $botName)
        ->exists();
}
```

### 6. Updated Commands
```php
// All commands updated to work with any bot
class StartCommand extends BaseTelegramCommand
{
    public function process(TelegramMessageDto $message): void
    {
        $botName = $message->botId; // 'main', 'admin', 'manager'
        $user = $this->findUserByTelegramId($message->userId, $botName);
        
        if ($user) {
            $this->handleExistingUser($message, $user, $botName);
        } else {
            $this->handleNewUser($message, $botName);
        }
    }
    
    private function findUserByTelegramId(string $telegramId, string $botName): ?User
    {
        return User::whereHas('telegramBots', function($query) use ($telegramId, $botName) {
            $query->where('telegram_id', $telegramId)
                  ->where('bot_name', $botName);
        })->first();
    }
}
```

### 7. Updated AuthLinkService
```php
// Update bindTelegramAccount method
public function bindTelegramAccount(string $token, string $telegramId, string $botName): array
{
    $authLink = AuthLink::where('token', $token)->first();
    
    if (!$authLink || $authLink->isExpired()) {
        return ['success' => false, 'message' => 'Ссылка недействительна'];
    }
    
    $user = User::find($authLink->user_id);
    
    // Check for conflicts
    $existing = UserTelegramBot::where('telegram_id', $telegramId)
        ->where('user_id', '!=', $user->id)
        ->first();
        
    if ($existing) {
        return ['success' => false, 'message' => 'Telegram ID уже привязан к другому пользователю'];
    }
    
    // Bind user to specific bot
    UserTelegramBot::updateOrCreate(
        ['user_id' => $user->id, 'bot_name' => $botName],
        ['telegram_id' => $telegramId]
    );
    
    return ['success' => true, 'message' => 'Аккаунт привязан'];
}
```

## Implementation Steps
1. Create migration for user_telegram_bots table
2. Create UserTelegramBot model
3. Create TelegramBot class
4. Create TelegramBotManager class
5. Refactor TelegramService to use TelegramBotManager
6. Update User model with new relationships
7. Move existing commands to Main/ folder
8. Create Admin/ and Manager/ command folders
9. Update AuthLinkService for multi-bot support
10. Update configuration
11. Test all functionality

## Benefits
- One user can be linked to multiple bots
- Easy to add new bots without code changes
- Commands are properly organized by bot type
- No code duplication
- Scalable architecture
- Backward compatible (can be implemented gradually)
