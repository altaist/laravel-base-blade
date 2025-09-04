<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminUserService;
use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct(
        private AdminUserService $adminUserService
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
        $person = $user->person ?? new \App\Models\Person();
        
        // Подготавливаем данные для формы
        $personData = [
            'first_name' => $person->first_name ?? '',
            'last_name' => $person->last_name ?? '',
            'middle_name' => $person->middle_name ?? '',
            'email' => $person->email ?? '',
            'phone' => $person->phone ?? '',
            'address' => $person->address ?? [],
            'region' => $person->region ?? '',
            'gender' => $person->gender ?? '',
            'birth_date' => $person->birth_date ?? '',
            'age' => $person->age ?? '',
            'additional_info' => $person->additional_info ?? [],
        ];

        return view('admin.users.edit', compact('user', 'personData'));
    }

    /**
     * Обновление пользователя
     */
    public function userUpdate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            // Данные пользователя
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,user',
            
            // Данные персоны
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|array',
            'region' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:150',
            'additional_info' => 'nullable|array',
        ]);

        try {
            // Разделяем данные пользователя и персоны
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            $personData = array_filter([
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'middle_name' => $validated['middle_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'region' => $validated['region'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'age' => $validated['age'] ?? null,
                'additional_info' => $validated['additional_info'] ?? null,
            ], function ($value) {
                return $value !== null && $value !== '';
            });

            $this->adminUserService->updateUser($user->id, $userData, $personData);

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
