<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use App\Services\PersonService;
use App\Models\User;
use App\Models\Feedback;
use App\Http\Requests\PersonEditRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct(
        private AdminUserService $adminUserService,
        private PersonService $personService
    ) {}

    /**
     * Главная страница админки
     */
    public function dashboard(): View
    {
        $userStats = $this->adminUserService->getUserStats();
        $feedbackStats = [
            'total' => Feedback::count(),
            'recent' => Feedback::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.dashboard', compact('userStats', 'feedbackStats'));
    }

    /**
     * Список пользователей
     */
    public function users(Request $request): View
    {
        $search = $request->get('search');
        
        if ($search) {
            $users = $this->adminUserService->searchUsers($search);
        } else {
            $users = $this->adminUserService->getAllUsers();
        }

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Страница редактирования пользователя
     */
    public function userEdit(User $user): View
    {
        $personData = $this->personService->getPersonDataForForm($user);

        return view('admin.users.edit', compact('user', 'personData'));
    }

    /**
     * Обновление пользователя
     */
    public function userUpdate(PersonEditRequest $request, User $user): RedirectResponse
    {
        // Валидируем роль отдельно
        $roleValidated = $request->validate([
            'role' => 'required|in:admin,manager,user',
        ]);

        try {
            // Обновляем роль пользователя
            $user->update(['role' => $roleValidated['role']]);

            // Обновляем данные персоны через PersonService (без обновления имени пользователя)
            // PersonEditRequest уже обработал данные в prepareForValidation()
            $this->personService->updatePerson($user, $request->validated(), false);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Пользователь успешно обновлен');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при обновлении пользователя: ' . $e->getMessage()]);
        }
    }

    /**
     * Удаление пользователя
     */
    public function userDestroy(User $user): RedirectResponse
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
