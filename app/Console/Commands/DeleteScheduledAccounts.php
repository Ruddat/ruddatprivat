<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class DeleteScheduledAccounts extends Command
{
    protected $signature = 'accounts:delete-scheduled';
    protected $description = 'Löscht Kundenkonten, die zur Löschung vorgemerkt und abgelaufen sind.';

    public function handle()
    {
        $toDelete = Customer::where('scheduled_for_deletion', true)
            ->where('deletion_date', '<=', now())
            ->get();

        foreach ($toDelete as $customer) {
            $this->info("Deleting customer ID {$customer->id} ({$customer->email})");
            $customer->delete(); // ggf. mit ->forceDelete() oder Events für Aufräumarbeiten
        }

        return Command::SUCCESS;
    }
}