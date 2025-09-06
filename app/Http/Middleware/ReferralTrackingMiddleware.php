<?php

namespace App\Http\Middleware;

use App\Services\Referral\ReferralService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReferralTrackingMiddleware
{
    public function __construct(
        private ReferralService $referralService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, есть ли реферальный код в URL
        if ($request->has('ref')) {
            $referralCode = $request->get('ref');
            
            Log::info('Обнаружен реферальный код в URL', [
                'code' => $referralCode,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            try {
                $result = $this->referralService->handleReferralClick($referralCode);

                if ($result['success']) {
                    Log::info('Реферальный переход успешно обработан', [
                        'code' => $referralCode,
                        'referral_id' => $result['referral']->id,
                        'referrer_id' => $result['referral']->referrer_id,
                    ]);
                } else {
                    Log::warning('Неудачная обработка реферального перехода', [
                        'code' => $referralCode,
                        'message' => $result['message'],
                    ]);
                }

                // Редирект на ту же страницу без параметра ref
                $redirectUrl = $request->url();
                if ($request->has('ref')) {
                    $queryParams = $request->query();
                    unset($queryParams['ref']);
                    if (!empty($queryParams)) {
                        $redirectUrl .= '?' . http_build_query($queryParams);
                    }
                }

                return redirect($redirectUrl);

            } catch (\Exception $e) {
                Log::error('Ошибка в ReferralTrackingMiddleware', [
                    'code' => $referralCode,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                // В случае ошибки просто продолжаем без редиректа
            }
        }

        return $next($request);
    }
}