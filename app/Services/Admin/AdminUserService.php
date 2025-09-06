<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Person;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminUserService
{
    /**
     * Получить всех пользователей с пагинацией
     */
    public function getAllUsers(int $perPage = 20): LengthAwarePaginator
    {
        return User::with('person')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Получить пользователя по ID с персоной
     */
    public function getUserById(int $userId): ?User
    {
        return User::with('person')->find($userId);
    }

    /**
     * Обновить данные пользователя и персоны
     */
    public function updateUser(int $userId, array $userData, array $personData = []): User
    {
        return DB::transaction(function () use ($userId, $userData, $personData) {
            $user = User::findOrFail($userId);
            
            // Обновляем данные пользователя
            if (!empty($userData)) {
                $user->update($userData);
            }
            
            // Обновляем или создаем персону
            if (!empty($personData)) {
                $person = $user->person;
                if ($person) {
                    $person->update($personData);
                } else {
                    $user->person()->create($personData);
                }
            }
            
            return $user->fresh(['person']);
        });
    }

    /**
     * Удалить пользователя (мягкое удаление)
     */
    public function deleteUser(int $userId): bool
    {
        try {
            $user = User::findOrFail($userId);
            
            // Проверяем, что это не последний админ
            if ($user->isAdmin() && $this->getAdminCount() <= 1) {
                throw new \Exception('Нельзя удалить последнего администратора');
            }
            
            return $user->delete();
        } catch (\Exception $e) {
            Log::error('Ошибка при удалении пользователя: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Получить количество администраторов
     */
    public function getAdminCount(): int
    {
        return User::where('role', 'admin')->count();
    }

    /**
     * Получить статистику пользователей
     */
    public function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'managers' => User::where('role', 'manager')->count(),
            'users' => User::where('role', 'user')->count(),
            'with_telegram' => User::whereNotNull('telegram_id')->count(),
            'recent' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Поиск пользователей
     */
    public function searchUsers(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return User::with('person')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhereHas('person', function ($personQuery) use ($query) {
                      $personQuery->where('first_name', 'like', "%{$query}%")
                                  ->orWhere('last_name', 'like', "%{$query}%")
                                  ->orWhere('phone', 'like', "%{$query}%");
                  });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
