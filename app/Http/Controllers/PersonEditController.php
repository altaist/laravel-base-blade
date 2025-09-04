<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonEditRequest;
use App\Services\PersonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PersonEditController extends Controller
{
    public function __construct(
        private PersonService $personService
    ) {}

    /**
     * Показать форму редактирования персоны
     */
    public function edit(): View
    {
        $user = Auth::user();
        $personData = $this->personService->getPersonDataForForm($user);

        return view('person-edit', [
            'personData' => $personData,
            'user' => $user
        ]);
    }

    /**
     * Обновить данные персоны
     */
    public function update(PersonEditRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        try {
            $this->personService->updatePerson($user, $validated);

            return redirect()
                ->route('person.edit')
                ->with('success', 'Профиль успешно обновлен');
        } catch (\Exception $e) {
            return redirect()
                ->route('person.edit')
                ->withInput()
                ->with('error', 'Произошла ошибка при обновлении профиля: ' . $e->getMessage());
        }
    }

    /**
     * Обновить только адрес
     */
    public function updateAddress(PersonEditRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        if (!isset($validated['address'])) {
            return redirect()
                ->route('person.edit')
                ->with('error', 'Данные адреса не найдены');
        }

        try {
            $this->personService->updateAddress($user, $validated['address']);

            return redirect()
                ->route('person.edit')
                ->with('success', 'Адрес успешно обновлен');
        } catch (\Exception $e) {
            return redirect()
                ->route('person.edit')
                ->withInput()
                ->with('error', 'Произошла ошибка при обновлении адреса: ' . $e->getMessage());
        }
    }

    /**
     * Обновить только дополнительную информацию
     */
    public function updateAdditionalInfo(PersonEditRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        if (!isset($validated['additional_info'])) {
            return redirect()
                ->route('person.edit')
                ->with('error', 'Дополнительная информация не найдена');
        }

        try {
            $this->personService->updateAdditionalInfo($user, $validated['additional_info']);

            return redirect()
                ->route('person.edit')
                ->with('success', 'Дополнительная информация успешно обновлена');
        } catch (\Exception $e) {
            return redirect()
                ->route('person.edit')
                ->withInput()
                ->with('error', 'Произошла ошибка при обновлении дополнительной информации: ' . $e->getMessage());
        }
    }
}
