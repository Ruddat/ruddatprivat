<?php

namespace Database\Seeders;

use App\Models\BwaGroup;
use Illuminate\Database\Seeder;

class BwaGroupSeeder extends Seeder
{
    public function run()
    {
        // Lösche vorhandene Gruppen
        BwaGroup::truncate();

        // SKR03 Gruppen
        $skr03Groups = [
            // Erträge
            [
                'skr' => 'skr03',
                'group_key' => 'revenue',
                'group_label' => 'Umsatzerlöse',
                'account_number_from' => '3000',
                'account_number_to' => '3999',
                'order' => 1,
            ],
            // Materialaufwand
            [
                'skr' => 'skr03',
                'group_key' => 'material',
                'group_label' => 'Materialaufwand',
                'account_number_from' => '5000',
                'account_number_to' => '5099',
                'order' => 2,
            ],
            // Personalaufwand
            [
                'skr' => 'skr03',
                'group_key' => 'personnel',
                'group_label' => 'Personalaufwand',
                'account_number_from' => '6000',
                'account_number_to' => '6199',
                'order' => 3,
            ],
            // Raumkosten
            [
                'skr' => 'skr03',
                'group_key' => 'space',
                'group_label' => 'Raumkosten',
                'account_number_from' => '6200',
                'account_number_to' => '6299',
                'order' => 4,
            ],
            // Betriebskosten
            [
                'skr' => 'skr03',
                'group_key' => 'operations',
                'group_label' => 'Betriebskosten',
                'account_number_from' => '6300',
                'account_number_to' => '6599',
                'order' => 5,
            ],
            // Fahrzeugkosten
            [
                'skr' => 'skr03',
                'group_key' => 'vehicle',
                'group_label' => 'Fahrzeugkosten',
                'account_number_from' => '6600',
                'account_number_to' => '6699',
                'order' => 6,
            ],
            // Werbung/Reisen
            [
                'skr' => 'skr03',
                'group_key' => 'marketing',
                'group_label' => 'Werbung und Reisen',
                'account_number_from' => '6700',
                'account_number_to' => '6899',
                'order' => 7,
            ],
            // Sonstige Aufwendungen
            [
                'skr' => 'skr03',
                'group_key' => 'other',
                'group_label' => 'Sonstige Aufwendungen',
                'account_number_from' => '6900',
                'account_number_to' => '6999',
                'order' => 8,
            ],
        ];

        // SKR04 Gruppen
        $skr04Groups = [
            // Erträge
            [
                'skr' => 'skr04',
                'group_key' => 'revenue',
                'group_label' => 'Umsatzerlöse',
                'account_number_from' => '4000',
                'account_number_to' => '4999',
                'order' => 1,
            ],
            // Materialaufwand
            [
                'skr' => 'skr04',
                'group_key' => 'material',
                'group_label' => 'Materialaufwand',
                'account_number_from' => '5000',
                'account_number_to' => '5099',
                'order' => 2,
            ],
            // Personalaufwand
            [
                'skr' => 'skr04',
                'group_key' => 'personnel',
                'group_label' => 'Personalaufwand',
                'account_number_from' => '6000',
                'account_number_to' => '6099',
                'order' => 3,
            ],
            // Raumkosten
            [
                'skr' => 'skr04',
                'group_key' => 'space',
                'group_label' => 'Raumkosten',
                'account_number_from' => '6100',
                'account_number_to' => '6199',
                'order' => 4,
            ],
            // Betriebskosten
            [
                'skr' => 'skr04',
                'group_key' => 'operations',
                'group_label' => 'Betriebskosten',
                'account_number_from' => '6200',
                'account_number_to' => '6399',
                'order' => 5,
            ],
            // Fahrzeugkosten
            [
                'skr' => 'skr04',
                'group_key' => 'vehicle',
                'group_label' => 'Fahrzeugkosten',
                'account_number_from' => '6400',
                'account_number_to' => '6499',
                'order' => 6,
            ],
            // Werbung/Reisen
            [
                'skr' => 'skr04',
                'group_key' => 'marketing',
                'group_label' => 'Werbung und Reisen',
                'account_number_from' => '6500',
                'account_number_to' => '6699',
                'order' => 7,
            ],
            // Sonstige Aufwendungen
            [
                'skr' => 'skr04',
                'group_key' => 'other',
                'group_label' => 'Sonstige Aufwendungen',
                'account_number_from' => '6700',
                'account_number_to' => '6999',
                'order' => 8,
            ],
        ];

        // ✅ KORRIGIERT: Basis-Kontenrahmen Gruppen ohne Überlappungen
        $basicGroups = [
            // Erträge (nur 4000-4499, damit 4500-4899 für andere Gruppen frei bleiben)
            [
                'skr' => 'basic',
                'group_key' => 'revenue',
                'group_label' => 'Umsatzerlöse',
                'account_number_from' => '4000',
                'account_number_to' => '4499',
                'order' => 1,
            ],
            // Personalaufwand
            [
                'skr' => 'basic',
                'group_key' => 'personnel',
                'group_label' => 'Personalaufwand',
                'account_number_from' => '4500',
                'account_number_to' => '4599',
                'order' => 2,
            ],
            // Raumkosten
            [
                'skr' => 'basic',
                'group_key' => 'space',
                'group_label' => 'Raumkosten',
                'account_number_from' => '4600',
                'account_number_to' => '4699',
                'order' => 3,
            ],
            // Fahrzeugkosten
            [
                'skr' => 'basic',
                'group_key' => 'vehicle',
                'group_label' => 'Fahrzeugkosten',
                'account_number_from' => '4700',
                'account_number_to' => '4799',
                'order' => 4,
            ],
            // Telekommunikation & IT
            [
                'skr' => 'basic',
                'group_key' => 'it',
                'group_label' => 'Telekommunikation & IT',
                'account_number_from' => '4800',
                'account_number_to' => '4899',
                'order' => 5,
            ],
            // Bürokosten
            [
                'skr' => 'basic',
                'group_key' => 'office',
                'group_label' => 'Bürokosten',
                'account_number_from' => '4900',
                'account_number_to' => '4999',
                'order' => 6,
            ],
            // Werbung & Marketing
            [
                'skr' => 'basic',
                'group_key' => 'marketing',
                'group_label' => 'Werbung & Marketing',
                'account_number_from' => '5000',
                'account_number_to' => '5099',
                'order' => 7,
            ],
            // Beratung & Dienstleistungen
            [
                'skr' => 'basic',
                'group_key' => 'consulting',
                'group_label' => 'Beratung & Dienstleistungen',
                'account_number_from' => '5100',
                'account_number_to' => '5199',
                'order' => 8,
            ],
            // Versicherungen
            [
                'skr' => 'basic',
                'group_key' => 'insurance',
                'group_label' => 'Versicherungen',
                'account_number_from' => '5200',
                'account_number_to' => '5299',
                'order' => 9,
            ],
            // Reisekosten
            [
                'skr' => 'basic',
                'group_key' => 'travel',
                'group_label' => 'Reisekosten',
                'account_number_from' => '5300',
                'account_number_to' => '5399',
                'order' => 10,
            ],
            // Abschreibungen
            [
                'skr' => 'basic',
                'group_key' => 'depreciation',
                'group_label' => 'Abschreibungen',
                'account_number_from' => '5400',
                'account_number_to' => '5499',
                'order' => 11,
            ],
            // Bankkosten
            [
                'skr' => 'basic',
                'group_key' => 'bank',
                'group_label' => 'Bank- und Finanzkosten',
                'account_number_from' => '5500',
                'account_number_to' => '5599',
                'order' => 12,
            ],
            // Sonstige Aufwendungen
            [
                'skr' => 'basic',
                'group_key' => 'other',
                'group_label' => 'Sonstige Aufwendungen',
                'account_number_from' => '5600',
                'account_number_to' => '6999',
                'order' => 13,
            ],
        ];

        // Alle Gruppen speichern
        foreach (array_merge($skr03Groups, $skr04Groups, $basicGroups) as $group) {
            BwaGroup::create($group);
        }

        $this->command->info('BWA-Gruppen wurden erfolgreich erstellt!');
        $this->command->info('SKR03: ' . count($skr03Groups) . ' Gruppen');
        $this->command->info('SKR04: ' . count($skr04Groups) . ' Gruppen');
        $this->command->info('Basic: ' . count($basicGroups) . ' Gruppen');
    }
}
