<?php

namespace Database\Seeders;

use App\Models\BwaGroup;
use Illuminate\Database\Seeder;

class BwaGroupSkr03Seeder extends Seeder
{
    public function run(): void
    {
        $skr03Groups = [
            // ERTRÄGE
            ['skr03', 4000, 4999, 1, 'revenue', 'Umsatzerlöse'],
            ['skr03', 8000, 8499, 2, 'other_revenue', 'Sonstige Erträge'],
            
            // PERSONALKOSTEN
            ['skr03', 4500, 4599, 3, 'personnel', 'Personalaufwand'],
            
            // RAUMKOSTEN
            ['skr03', 4600, 4699, 4, 'space', 'Raumkosten'],
            
            // FAHRZEUGKOSTEN
            ['skr03', 4700, 4799, 5, 'vehicle', 'Fahrzeugkosten'],
            
            // TELEKOMMUNIKATION & IT
            ['skr03', 4800, 4899, 6, 'it', 'Telekommunikation & IT'],
            
            // BÜROKOSTEN
            ['skr03', 4900, 4999, 7, 'office', 'Bürokosten'],
            
            // WERBUNG & MARKETING
            ['skr03', 5000, 5099, 8, 'marketing', 'Werbung & Marketing'],
            
            // BERATUNG & DIENSTLEISTUNGEN
            ['skr03', 5100, 5199, 9, 'consulting', 'Beratung & Dienstleistungen'],
            
            // VERSICHERUNGEN
            ['skr03', 5200, 5299, 10, 'insurance', 'Versicherungen'],
            
            // REISEKOSTEN
            ['skr03', 5300, 5399, 11, 'travel', 'Reisekosten'],
            
            // ABSCHREIBUNGEN
            ['skr03', 5400, 5499, 12, 'depreciation', 'Abschreibungen'],
            
            // BANK- UND FINANZKOSTEN
            ['skr03', 5500, 5599, 13, 'bank', 'Bank- und Finanzkosten'],
            ['skr03', 4970, 4979, 13, 'bank', 'Bank- und Finanzkosten'],
            
            // SONSTIGE AUFWENDUNGEN
            ['skr03', 5600, 6999, 14, 'other', 'Sonstige Aufwendungen'],
        ];

        foreach ($skr03Groups as [$skr, $from, $to, $order, $key, $label]) {
            BwaGroup::firstOrCreate(
                [
                    'skr' => $skr,
                    'account_number_from' => $from,
                    'account_number_to' => $to,
                    'group_key' => $key
                ],
                [
                    'group_label' => $label,
                    'order' => $order
                ]
            );
        }

        $this->command->info('BWA Groups für SKR03 erfolgreich erstellt!');
    }
}