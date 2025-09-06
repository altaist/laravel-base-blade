<?php

namespace App\Console\Commands;

use App\Services\Referral\ReferralService;
use Illuminate\Console\Command;

class ReferralCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить истекшие реферальные записи';

    /**
     * Execute the console command.
     */
    public function handle(ReferralService $referralService): int
    {
        $this->info('Начинаем очистку истекших рефералов...');

        try {
            $cleanedCount = $referralService->cleanupExpiredReferrals();

            $this->info("Очищено истекших рефералов: {$cleanedCount}");

            if ($cleanedCount > 0) {
                $this->info('Очистка завершена успешно.');
            } else {
                $this->info('Истекших рефералов не найдено.');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Ошибка при очистке рефералов: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}