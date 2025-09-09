<?php

namespace App\Services;

use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonService
{
    /**
     * Получить или создать персону для пользователя
     *
     * @param User $user
     * @return Person
     */
    public function getOrCreatePerson(User $user): Person
    {
        return $user->person ?? $user->person()->create([]);
    }

    /**
     * Создать персону для пользователя с данными
     *
     * @param User $user
     * @param array $data
     * @return Person
     */
    public function createPerson(User $user, array $data): Person
    {
        return DB::transaction(function () use ($user, $data) {
            // Обрабатываем JSON поля
            $personData = [];
            
            if (isset($data['first_name'])) {
                $personData['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $personData['last_name'] = $data['last_name'];
            }
            if (isset($data['middle_name'])) {
                $personData['middle_name'] = $data['middle_name'];
            }
            if (isset($data['phone'])) {
                $personData['phone'] = $data['phone'];
            }
            if (isset($data['birth_date'])) {
                $personData['birth_date'] = $data['birth_date'];
            }
            if (isset($data['gender'])) {
                $personData['gender'] = $data['gender'];
            }
            
            // Копируем email из user в person
            $personData['email'] = $user->email;
            
            // Обрабатываем адрес
            if (isset($data['address']) && is_array($data['address'])) {
                $personData['address'] = $data['address'];
            }
            
            // Обрабатываем дополнительную информацию
            if (isset($data['additional_info']) && is_array($data['additional_info'])) {
                $personData['additional_info'] = $data['additional_info'];
            }

            return $user->person()->create($personData);
        });
    }

    /**
     * Обновить данные персоны
     *
     * @param User $user
     * @param array $data
     * @param bool $updateUserName Обновлять ли имя пользователя из first_name
     * @return Person
     */
    public function updatePerson(User $user, array $data, bool $updateUserName = true): Person
    {
        return DB::transaction(function () use ($user, $data, $updateUserName) {
            $person = $this->getOrCreatePerson($user);
            
            // Обрабатываем JSON поля
            if (isset($data['address']) && is_array($data['address'])) {
                $data['address'] = $data['address'];
            }
            
            if (isset($data['additional_info']) && is_array($data['additional_info'])) {
                $data['additional_info'] = $data['additional_info'];
            }

            // Если обновляется first_name, также обновляем name в таблице users (только если разрешено)
            if ($updateUserName && isset($data['first_name']) && !empty($data['first_name'])) {
                $user->update(['name' => $data['first_name']]);
                
                Log::info('Имя пользователя обновлено из профиля', [
                    'user_id' => $user->id,
                    'old_name' => $user->getOriginal('name'),
                    'new_name' => $data['first_name']
                ]);
            }

            // Исключаем поля, которые не должны попадать в таблицу persons
            $personFields = collect($data)->except(['name', 'email', 'role', 'password'])->toArray();
            
            // Копируем email из user в person
            $personFields['email'] = $user->email;
            
            $person->update($personFields);

            Log::info('Данные персоны обновлены', [
                'user_id' => $user->id,
                'person_id' => $person->id,
                'updated_fields' => array_keys($personFields)
            ]);

            return $person;
        });
    }

    /**
     * Обновить адрес персоны
     *
     * @param User $user
     * @param array $addressData
     * @return Person
     */
    public function updateAddress(User $user, array $addressData): Person
    {
        return DB::transaction(function () use ($user, $addressData) {
            $person = $this->getOrCreatePerson($user);
            $person->update(['address' => $addressData]);

            Log::info('Адрес персоны обновлен', [
                'user_id' => $user->id,
                'person_id' => $person->id,
                'address' => $addressData
            ]);

            return $person;
        });
    }

    /**
     * Обновить дополнительную информацию персоны
     *
     * @param User $user
     * @param array $additionalInfo
     * @return Person
     */
    public function updateAdditionalInfo(User $user, array $additionalInfo): Person
    {
        return DB::transaction(function () use ($user, $additionalInfo) {
            $person = $this->getOrCreatePerson($user);
            $person->update(['additional_info' => $additionalInfo]);

            Log::info('Дополнительная информация персоны обновлена', [
                'user_id' => $user->id,
                'person_id' => $person->id,
                'additional_info' => $additionalInfo
            ]);

            return $person;
        });
    }

    /**
     * Получить все данные персоны для формы
     *
     * @param User $user
     * @return array
     */
    public function getPersonDataForForm(User $user): array
    {
        $person = $user->person;
        
        if (!$person) {
            return [
                'first_name' => $user->name ?? '', // Подставляем имя пользователя если персоны нет
                'last_name' => '',
                'middle_name' => '',
                'email' => '',
                'phone' => '',
                'region' => '',
                'gender' => '',
                'birth_date' => '',
                'age' => '',
                'address' => [
                    'street' => '',
                    'house' => '',
                    'apartment' => '',
                    'city' => '',
                    'postal_code' => ''
                ],
                'additional_info' => []
            ];
        }

        return [
            'first_name' => $person->first_name ?: $user->name ?: '', // Подставляем имя пользователя если first_name пустое
            'last_name' => $person->last_name ?? '',
            'middle_name' => $person->middle_name ?? '',
            'email' => $person->email ?? '',
            'phone' => $person->phone ?? '',
            'region' => $person->region ?? '',
            'gender' => $person->gender ?? '',
            'birth_date' => $person->birth_date ? $person->birth_date->format('Y-m-d') : '',
            'age' => $person->age ?? '',
            'address' => $person->address ?? [
                'street' => '',
                'house' => '',
                'apartment' => '',
                'city' => '',
                'postal_code' => ''
            ],
            'additional_info' => $person->additional_info ?? []
        ];
    }
}
