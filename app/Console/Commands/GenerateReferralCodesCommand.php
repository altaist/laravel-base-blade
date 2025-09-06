<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Referral\ReferralService;
use Illuminate\Console\Command;

class GenerateReferralCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:generate-codes {--force : Принудительно создать ссылки для всех пользователей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать реферальные ссылки для существующих пользователей';

    /**
     * Execute the console command.
     */
    public function handle(ReferralService $referralService): int
    {
        $this->info('Начинаем создание реферальных ссылок для пользователей...');

        try {
            $query = User::query();
            
            if (!$this->option('force')) {
                // Создаем ссылки только для пользователей, у которых их еще нет
                $query->whereDoesntHave('referralLinks');
            }

            $users = $query->get();
            $createdCount = 0;

            $this->info("Найдено пользователей: {$users->count()}");

            if ($users->isEmpty()) {
                $this->info('Пользователи для создания ссылок не найдены.');
                return Command::SUCCESS;
            }

            $progressBar = $this->output->createProgressBar($users->count());
            $progressBar->start();

            foreach ($users as $user) {
                try {
                    $referralService->createLinkForUser($user, [
                        'name' => 'Основная ссылка',
                        'type' => \App\Enums\Referral\ReferralLinkType::CUSTOM,
                    ]);
                    $createdCount++;
                } catch (\Exception $e) {
                    $this->error("Ошибка при создании ссылки для пользователя {$user->id}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();

            $this->info("Создано реферальных ссылок: {$createdCount}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Ошибка при создании реферальных ссылок: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}