<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Traits\HasAdminCrud;
use App\Services\UserService;
use App\Services\PersonService;
use App\Models\User;
use App\Http\Requests\PersonEditRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    use HasAdminCrud;

    public function __construct(
        private UserService $userService,
        private PersonService $personService
    ) {}

    /**
     * Переопределяем метод edit для пользователей
     */
    public function edit(User $user): View
    {
        $personData = $this->personService->getPersonDataForForm($user);
        
        return view($this->getViewPath('edit'), [
            'user' => $user,
            'personData' => $personData,
            'config' => $this->getEntityConfig()
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
     * Переопределяем метод destroy для пользователей
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user->id);
            
            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Пользователь успешно удален');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
