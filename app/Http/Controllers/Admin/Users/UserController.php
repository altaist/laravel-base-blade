<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use App\Services\PersonService;
use App\Models\User;
use App\Http\Requests\PersonEditRequest;
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
    public function update(PersonEditRequest $request, User $user): RedirectResponse
    {
        // Валидируем роль отдельно
        $roleValidated = $request->validate([
            'role' => 'required|in:admin,manager,user',
        ]);

        try {
            // Обновляем роль пользователя
            $user->update(['role' => $roleValidated['role']]);

            // Обновляем данные персоны через PersonService (без обновления имени пользователя)
            $this->personService->updatePerson($user, $request->validated(), false);

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
        abort(404); // Пока не реализовано
    }

    /**
     * Сохранение нового пользователя
     */
    public function store(Request $request): RedirectResponse
    {
        abort(404); // Пока не реализовано
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
