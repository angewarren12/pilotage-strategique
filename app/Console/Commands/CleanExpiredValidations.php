<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ValidationService;

class CleanExpiredValidations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validations:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoyer les validations expirées et notifier les demandeurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Nettoyage des validations expirées...');
        
        $validationService = app(ValidationService::class);
        $cleanedCount = $validationService->cleanExpiredValidations();
        
        if ($cleanedCount > 0) {
            $this->info("{$cleanedCount} validation(s) expirée(s) nettoyée(s) avec succès.");
        } else {
            $this->info('Aucune validation expirée trouvée.');
        }
        
        return 0;
    }
}
