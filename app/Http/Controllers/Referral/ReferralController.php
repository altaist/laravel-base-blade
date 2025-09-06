<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\CreateReferralLinkRequest;
use App\Http\Requests\Referral\UpdateReferralLinkRequest;
use App\Models\Referral\ReferralLink;
use App\Services\Referral\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReferralController extends Controller
{
    public function __construct(
        private ReferralService $referralService
    ) {}

    /**
     * Обработка перехода по реферальной ссылке (публичный метод)
     */
    public function handle(string $code)
    {
        Log::info('Попытка перехода по реферальной ссылке', [
            'code' => $code,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        try {
            $result = $this->referralService->handleReferralClick($code);

            if (!$result['success']) {
                Log::warning('Неудачный переход по реферальной ссылке', [
                    'code' => $code,
                    'message' => $result['message'],
                    'ip' => request()->ip()
                ]);
                
                return redirect()->route('home')->with('error', $result['message']);
            }

            Log::info('Успешный переход по реферальной ссылке', [
                'code' => $code,
                'referral_id' => $result['referral']->id,
                'referrer_id' => $result['referral']->referrer_id,
                'ip' => request()->ip()
            ]);

            // Редирект на главную страницу с информацией о реферале
            return redirect()->route('home')->with('success', 'Добро пожаловать! Вы перешли по реферальной ссылке.');

        } catch (\Exception $e) {
            Log::error('Критическая ошибка при обработке реферальной ссылки', [
                'code' => $code,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ip' => request()->ip(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('home')->with('error', 'Произошла ошибка при обработке ссылки');
        }
    }

    /**
     * Создать новую реферальную ссылку
     */
    public function create(CreateReferralLinkRequest $request)
    {
        try {
            $user = Auth::user();
            
            $options = [
                'name' => $request->validated('name'),
                'type' => $request->validated('type'),
                'max_uses' => $request->validated('max_uses'),
                'expires_at' => $request->validated('expires_at'),
            ];

            $referralLink = $this->referralService->createLinkForUser($user, $options);

            Log::info('Создана новая реферальная ссылка', [
                'user_id' => $user->id,
                'link_id' => $referralLink->id,
                'code' => $referralLink->code,
                'type' => $referralLink->type,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Реферальная ссылка создана успешно',
                'data' => [
                    'id' => $referralLink->id,
                    'code' => $referralLink->code,
                    'name' => $referralLink->name,
                    'type' => $referralLink->type,
                    'url' => $referralLink->full_url,
                    'is_active' => $referralLink->is_active,
                    'max_uses' => $referralLink->max_uses,
                    'current_uses' => $referralLink->current_uses,
                    'expires_at' => $referralLink->expires_at,
                    'created_at' => $referralLink->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании реферальной ссылки', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании ссылки'
            ], 500);
        }
    }

    /**
     * Получить список реферальных ссылок пользователя
     */
    public function list()
    {
        try {
            $user = Auth::user();
            $links = $this->referralService->getUserLinks($user);

            $data = $links->map(function ($link) {
                return [
                    'id' => $link->id,
                    'code' => $link->code,
                    'name' => $link->name,
                    'type' => $link->type,
                    'url' => $link->full_url,
                    'is_active' => $link->is_active,
                    'max_uses' => $link->max_uses,
                    'current_uses' => $link->current_uses,
                    'expires_at' => $link->expires_at,
                    'created_at' => $link->created_at,
                    'stats' => $link->stats,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка реферальных ссылок', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении списка ссылок'
            ], 500);
        }
    }

    /**
     * Получить статистику рефералов пользователя
     */
    public function stats()
    {
        try {
            $user = Auth::user();
            $stats = $this->referralService->getUserReferralStats($user);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении статистики рефералов', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении статистики'
            ], 500);
        }
    }

    /**
     * Получить список приглашенных пользователей
     */
    public function referredUsers()
    {
        try {
            $user = Auth::user();
            $referredUsers = $this->referralService->getReferredUsers($user);

            $data = $referredUsers->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referred_user' => [
                        'id' => $referral->referred->id,
                        'name' => $referral->referred->name,
                        'email' => $referral->referred->email,
                        'created_at' => $referral->referred->created_at,
                    ],
                    'status' => $referral->status,
                    'created_at' => $referral->created_at,
                    'completed_at' => $referral->status === 'completed' ? $referral->updated_at : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка приглашенных пользователей', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при получении списка приглашенных'
            ], 500);
        }
    }

    /**
     * Обновить настройки реферальной ссылки
     */
    public function update(UpdateReferralLinkRequest $request, ReferralLink $link)
    {
        try {
            $settings = $request->validated();
            $success = $this->referralService->updateLinkSettings($link, $settings);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при обновлении ссылки'
                ], 500);
            }

            $link->refresh();

            Log::info('Обновлены настройки реферальной ссылки', [
                'user_id' => Auth::id(),
                'link_id' => $link->id,
                'settings' => $settings
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Настройки ссылки обновлены успешно',
                'data' => [
                    'id' => $link->id,
                    'code' => $link->code,
                    'name' => $link->name,
                    'type' => $link->type,
                    'is_active' => $link->is_active,
                    'max_uses' => $link->max_uses,
                    'expires_at' => $link->expires_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении реферальной ссылки', [
                'user_id' => Auth::id(),
                'link_id' => $link->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при обновлении ссылки'
            ], 500);
        }
    }

    /**
     * Деактивировать реферальную ссылку
     */
    public function deactivate(ReferralLink $link)
    {
        try {
            $success = $this->referralService->deactivateLink($link);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при деактивации ссылки'
                ], 500);
            }

            Log::info('Деактивирована реферальная ссылка', [
                'user_id' => Auth::id(),
                'link_id' => $link->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ссылка деактивирована успешно'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при деактивации реферальной ссылки', [
                'user_id' => Auth::id(),
                'link_id' => $link->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при деактивации ссылки'
            ], 500);
        }
    }

    /**
     * Активировать реферальную ссылку
     */
    public function activate(ReferralLink $link)
    {
        try {
            $success = $this->referralService->activateLink($link);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при активации ссылки'
                ], 500);
            }

            Log::info('Активирована реферальная ссылка', [
                'user_id' => Auth::id(),
                'link_id' => $link->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ссылка активирована успешно'
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при активации реферальной ссылки', [
                'user_id' => Auth::id(),
                'link_id' => $link->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при активации ссылки'
            ], 500);
        }
    }
}
