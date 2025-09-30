<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\BkBookingTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BkBookingTemplateSeeder extends Seeder
{
    public function run()
    {
        // Globale Buchungsvorlagen - tenant_id = null für globale Vorlagen
        $globalTemplates = [
            [
                'name' => 'Büromaterial Einkauf',
                'description' => 'Einkauf von Büromaterial',
                'debit_account_number' => '5000', // Büromaterial
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Tankkosten',
                'description' => 'Tankbeleg für Kraftstoff',
                'debit_account_number' => '4000', // Kraftstoffkosten
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'fuel',
                'is_global' => true,
            ],
            [
                'name' => 'Reisekosten',
                'description' => 'Reisekosten erstattet',
                'debit_account_number' => '6000', // Reisekosten
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'receipt',
                'is_global' => true,
            ],
            [
                'name' => 'Umsatzerlöse',
                'description' => 'Verkauf an Kunden',
                'debit_account_number' => '1200', // Bank
                'credit_account_number' => '8000', // Umsatzerlöse
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Gehälter',
                'description' => 'Gehaltszahlung Mitarbeiter',
                'debit_account_number' => '4500', // Gehälter
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 0.00,
                'with_vat' => false,
                'receipt_type' => 'other',
                'is_global' => true,
            ],
            [
                'name' => 'Mieteinnahmen',
                'description' => 'Mieteinnahmen von Mieter',
                'debit_account_number' => '1200', // Bank
                'credit_account_number' => '8100', // Mieteinnahmen
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Werbekosten',
                'description' => 'Werbung und Marketing',
                'debit_account_number' => '6300', // Werbekosten
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Reparaturkosten',
                'description' => 'Reparatur und Wartung',
                'debit_account_number' => '4900', // Reparaturkosten
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Beratungshonorar',
                'description' => 'Honorar für externe Beratung',
                'debit_account_number' => '4700', // Beratungskosten
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
            [
                'name' => 'Internet/Telefon',
                'description' => 'Telekommunikationskosten',
                'debit_account_number' => '4800', // Telekommunikation
                'credit_account_number' => '1200', // Bank
                'vat_rate' => 19.00,
                'with_vat' => true,
                'receipt_type' => 'invoice',
                'is_global' => true,
            ],
        ];

        foreach ($globalTemplates as $templateData) {
            // Finde die Account IDs basierend auf den Kontonummern
            $debitAccount = Account::where('number', $templateData['debit_account_number'])->first();
            $creditAccount = Account::where('number', $templateData['credit_account_number'])->first();

            if ($debitAccount && $creditAccount) {
                BkBookingTemplate::create([
                    'tenant_id' => null, // NULL für globale Vorlagen
                    'name' => $templateData['name'],
                    'debit_account_id' => $debitAccount->id,
                    'credit_account_id' => $creditAccount->id,
                    'vat_rate' => $templateData['vat_rate'],
                    'with_vat' => $templateData['with_vat'],
                    'description' => $templateData['description'],
                    'receipt_type' => $templateData['receipt_type'],
                    'is_global' => true,
                ]);
            } else {
                $this->command->warn("Konten nicht gefunden für Vorlage: {$templateData['name']}");
            }
        }

        $this->command->info('Globale Buchungsvorlagen wurden erstellt!');
    }
}
