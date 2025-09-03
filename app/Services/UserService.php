<?php

namespace App\Services;

use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Создать пользователя и связанную персону.
     * Если данные о персоне не переданы, создается запись с пустыми значениями.
     *
     * @param array $userData Данные пользователя (name, email, password и т.д.).
     * @param array|null $personData Данные персоны (опционально).
     * @return User
     */
    public function createUserWithPerson(array $userData, ?array $personData = null): User
    {
        return DB::transaction(function () use ($userData, $personData) {
            // Создаем пользователя
            $user = User::create($userData);

            // Создаем персону (если данные не переданы, используем пустой массив)
            $personData = $personData ?? [];
            $user->person()->create($personData);

            return $user;
        });
    }
}
