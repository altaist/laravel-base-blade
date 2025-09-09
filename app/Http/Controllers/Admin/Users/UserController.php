<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use App\Services\PersonService;
use App\Models\User;
use App\Http\Requests\PersonEditRequest;
use App\Http\Requests\AdminUserCreateRequest;
use App\Http\Requests\AdminUserEditRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{

    public function __construct(
        private AdminUserService $adminUserService,
        private PersonService $personService
    ) {}

    /**
     * Список пользователей
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        if ($search) {
            $users = $this->adminUserService->searchUsers($search);
        } else {
            $users = $this->adminUserService->getAllUsers();
        }
        
        $userStats = $this->adminUserService->getUserStats();
        
        return view('admin.users.index', compact('users', 'search', 'userStats'));
    }

    /**
     * Просмотр пользователя
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Форма редактирования пользователя
     */
    public function edit(User $user): View
    {
        $personData = $this->personService->getPersonDataForForm($user);
        
        return view('admin.users.edit', [
            'user' => $user,
            'personData' => $personData
        ]);
    }

    /**
     * Переопределяем метод update для пользователей
     */
    public function update(AdminUserEditRequest $request, User $user): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            // Обновляем основные данные пользователя
            $user->update([
                'name' => $validated['name'],
                'role' => $validated['role'],
            ]);

            // Обновляем данные персоны через PersonService (без обновления имени пользователя)
            // Исключаем поля users из данных персоны
            $personData = collect($validated)->except(['name', 'email', 'role'])->toArray();
            $this->personService->updatePerson($user, $personData, false);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Пользователь успешно обновлен');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при обновлении пользователя: ' . $e->getMessage()]);
        }
    }

    /**
     * Форма создания пользователя
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Сохранение нового пользователя
     */
    public function store(AdminUserCreateRequest $request): RedirectResponse
    {
        try {
            // Создаем пользователя
            $validated = $request->validated();
            $firstName = $validated['first_name'] ?? '';
            $lastName = $validated['last_name'] ?? '';
            $userName = trim($firstName . ' ' . $lastName) ?: $validated['email'];
            
            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
                'name' => $userName,
            ]);

            // Создаем персону через PersonService
            $this->personService->createPerson($user, $validated);

            return redirect()
                ->route('admin.users.show', $user)
                ->with('success', 'Пользователь успешно создан');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при создании пользователя: ' . $e->getMessage()]);
        }
    }

    /**
     * Удаление пользователя
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->adminUserService->deleteUser($user->id);
            
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Пользователь успешно удален');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
