<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BwaGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bestehende Einträge löschen
        DB::table('bwa_groups')->truncate();

        // -------------------
        // SKR03
        // -------------------
        DB::table('bwa_groups')->insert([
            // Erlöse
            ['skr' => 'skr03', 'account_number_from' => 4800, 'account_number_to' => 4899, 'group_key' => 'revenue', 'group_label' => 'Provisionserlöse'],
            ['skr' => 'skr03', 'account_number_from' => 8300, 'account_number_to' => 8399, 'group_key' => 'revenue', 'group_label' => 'Umsatzerlöse 7%'],
            ['skr' => 'skr03', 'account_number_from' => 8400, 'account_number_to' => 8499, 'group_key' => 'revenue', 'group_label' => 'Umsatzerlöse 19%'],
            ['skr' => 'skr03', 'account_number_from' => 9000, 'account_number_to' => 9099, 'group_key' => 'revenue', 'group_label' => 'Sonstige betriebliche Erträge'],
            ['skr' => 'skr03', 'account_number_from' => 8336, 'account_number_to' => 8336, 'group_key' => 'revenue', 'group_label' => 'Erlöse aus dem innergemeinschaftlichen Erwerb'],
            ['skr' => 'skr03', 'account_number_from' => 8337, 'account_number_to' => 8337, 'group_key' => 'revenue', 'group_label' => 'Erlöse aus dem innergemeinschaftlichen Erwerb 7%'],
            ['skr' => 'skr03', 'account_number_from' => 8347, 'account_number_to' => 8347, 'group_key' => 'revenue', 'group_label' => 'Erlöse aus dem innergemeinschaftlichen Erwerb 19%'],
            // Material / Fremdleistungen
            ['skr' => 'skr03', 'account_number_from' => 3000, 'account_number_to' => 3999, 'group_key' => 'material', 'group_label' => 'Wareneinsatz / Fremdleistungen'],
            ['skr' => 'skr03', 'account_number_from' => 2000, 'account_number_to' => 2999, 'group_key' => 'material', 'group_label' => 'Bestandsveränderungen / aktivierte Eigenleistungen'],
            ['skr' => 'skr03', 'account_number_from' => 4900, 'account_number_to' => 4900, 'group_key' => 'material', 'group_label' => 'Eingangsrechnungen für Anlagen'],
            // Personal
['skr' => 'skr03', 'account_number_from' => 4000, 'account_number_to' => 4999, 'group_key' => 'personnel', 'group_label' => 'Personalaufwand'],
            ['skr' => 'skr03', 'account_number_from' => 5000, 'account_number_to' => 5099, 'group_key' => 'personnel', 'group_label' => 'Löhne'],
            ['skr' => 'skr03', 'account_number_from' => 5100, 'account_number_to' => 5199, 'group_key' => 'personnel', 'group_label' => 'Gehälter'],
            ['skr' => 'skr03', 'account_number_from' => 5200, 'account_number_to' => 5299, 'group_key' => 'personnel', 'group_label' => 'Sozialabgaben AG-Anteil'],
            ['skr' => 'skr03', 'account_number_from' => 5300, 'account_number_to' => 5399, 'group_key' => 'personnel', 'group_label' => 'Sonstiger Personalaufwand'],
            // Raumkosten
            ['skr' => 'skr03', 'account_number_from' => 6000, 'account_number_to' => 6099, 'group_key' => 'rooms', 'group_label' => 'Raumkosten'],

            // Fahrzeugkosten
            ['skr' => 'skr03', 'account_number_from' => 4600, 'account_number_to' => 4699, 'group_key' => 'vehicle', 'group_label' => 'Fahrzeugkosten'],
            ['skr' => 'skr03', 'account_number_from' => 4700, 'account_number_to' => 4799, 'group_key' => 'vehicle', 'group_label' => 'Reisekosten'],
            ['skr' => 'skr03', 'account_number_from' => 4650, 'account_number_to' => 4650, 'group_key' => 'vehicle', 'group_label' => 'Kfz-Steuer'],
            ['skr' => 'skr03', 'account_number_from' => 4660, 'account_number_to' => 4660, 'group_key' => 'vehicle', 'group_label' => 'Kfz-Versicherung'],

            // Werbung
            ['skr' => 'skr03', 'account_number_from' => 6600, 'account_number_to' => 6699, 'group_key' => 'advert', 'group_label' => 'Werbekosten'],

            // Bankgebühren & Zahlungen
            ['skr' => 'skr03', 'account_number_from' => 4970, 'account_number_to' => 4979, 'group_key' => 'other', 'group_label' => 'Bankgebühren'],
            ['skr' => 'skr03', 'account_number_from' => 4975, 'account_number_to' => 4975, 'group_key' => 'other', 'group_label' => 'PayPal-Gebühren'],

            // Büro / EDV / Software
            ['skr' => 'skr03', 'account_number_from' => 4930, 'account_number_to' => 4939, 'group_key' => 'other', 'group_label' => 'Bürobedarf'],
            ['skr' => 'skr03', 'account_number_from' => 4950, 'account_number_to' => 4959, 'group_key' => 'other', 'group_label' => 'EDV / Hostingkosten'],
            ['skr' => 'skr03', 'account_number_from' => 4905, 'account_number_to' => 4905, 'group_key' => 'other', 'group_label' => 'Lizenzgebühren / Software'],

            // Sonstiges
            ['skr' => 'skr03', 'account_number_from' => 6800, 'account_number_to' => 6999, 'group_key' => 'other', 'group_label' => 'Sonstige betriebliche Aufwendungen'],
            ['skr' => 'skr03', 'account_number_from' => 6950, 'account_number_to' => 6950, 'group_key' => 'other', 'group_label' => 'Abgeschriebene Forderungen'],
            ['skr' => 'skr03', 'account_number_from' => 480, 'account_number_to' => 480, 'group_key' => 'gwg', 'group_label' => 'GWG']
        ]);

        // -------------------
        // SKR04 (Basis wie bisher)
        // -------------------
        DB::table('bwa_groups')->insert([
            ['skr' => 'skr04', 'account_number_from' => 4000, 'account_number_to' => 4999, 'group_key' => 'revenue', 'group_label' => 'Umsatzerlöse'],
            ['skr' => 'skr04', 'account_number_from' => 5000, 'account_number_to' => 5999, 'group_key' => 'material', 'group_label' => 'Wareneinsatz'],
            ['skr' => 'skr04', 'account_number_from' => 6000, 'account_number_to' => 6999, 'group_key' => 'personnel', 'group_label' => 'Personalaufwand'],
            ['skr' => 'skr04', 'account_number_from' => 7000, 'account_number_to' => 7999, 'group_key' => 'rooms', 'group_label' => 'Raumkosten'],
            ['skr' => 'skr04', 'account_number_from' => 8000, 'account_number_to' => 8099, 'group_key' => 'vehicle', 'group_label' => 'Kfz-Kosten'],
            ['skr' => 'skr04', 'account_number_from' => 8500, 'account_number_to' => 8599, 'group_key' => 'advert', 'group_label' => 'Werbekosten'],
            ['skr' => 'skr04', 'account_number_from' => 8700, 'account_number_to' => 8999, 'group_key' => 'other', 'group_label' => 'Sonstige Aufwendungen'],
        ]);
    }
}
