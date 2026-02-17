<?php

namespace App\Console\Commands;

use App\Services\ExpirationAlertService;
use Illuminate\Console\Command;

class CheckProductExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les produits expirés et expirant bientôt, générer les alertes correspondantes';

    /**
     * Execute the console command.
     */
    public function handle(ExpirationAlertService $expirationService): int
    {
        $this->info('Vérification des expirations de produits...');

        try {
            $expirationService->checkAndGenerateExpirationAlerts();
            $this->info('✅ Vérification des expirations terminée avec succès.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la vérification : ' . $e->getMessage());
            return 1;
        }
    }
}
