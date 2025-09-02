<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $person = Auth::user()->person ?? new Person();
        
        return view('profile', [
            'person' => $person
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|array',
            'region' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female',
            'birth_date' => 'nullable|date',
            'age' => 'nullable|integer|min:0|max:150',
            'additional_info' => 'nullable|array',
        ]);

        $user = Auth::user();
        $person = $user->person ?? new Person();
        
        // Если это новая запись, устанавливаем связь с пользователем
        if (!$person->exists) {
            $person->user()->associate($user);
        }

        $person->fill($validated);
        $person->save();

        return redirect()->route('profile')->with('status', 'Профиль успешно обновлен');
    }

    public function updateAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => 'required|array',
            'address.street' => 'required|string|max:255',
            'address.house' => 'required|string|max:50',
            'address.apartment' => 'nullable|string|max:50',
            'address.city' => 'required|string|max:255',
            'address.postal_code' => 'nullable|string|max:20',
        ]);

        $person = Auth::user()->person ?? new Person();
        
        if (!$person->exists) {
            $person->user()->associate(Auth::user());
        }

        $person->address = $validated['address'];
        $person->save();

        return redirect()->route('profile')->with('status', 'Адрес успешно обновлен');
    }

    public function updateAdditionalInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'additional_info' => 'required|array',
        ]);

        $person = Auth::user()->person ?? new Person();
        
        if (!$person->exists) {
            $person->user()->associate(Auth::user());
        }

        $person->additional_info = $validated['additional_info'];
        $person->save();

        return redirect()->route('profile')->with('status', 'Дополнительная информация успешно обновлена');
    }
}