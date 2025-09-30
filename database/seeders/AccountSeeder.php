<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'ruddat-ug'],
            [
                'name' => 'Ruddat UG',
                'email' => 'info@ruddat.de',
                'phone' => '+49 123 456789',
                'street' => 'Heidkrugsweg',
                'house_number' => '31',
                'zip' => '31234',
                'city' => 'Edemissen',
                'country' => 'Deutschland',

                // steuerliche Angaben
                'tax_number' => '30/041/37838',
                'vat_id' => 'DE123456789',
                'commercial_register' => 'HRB 12345',
                'court_register' => 'Amtsgericht Hildesheim',

                // Bank
                'bank_name' => 'Sparkasse Hildesheim',
                'iban' => 'DE12 3456 7890 1234 5678 00',
                'bic' => 'NOLADE21HIK',

                // Buchhaltungs-Einstellungen
                'fiscal_year_start' => '2025-01-01',
                'currency' => 'EUR',
                'active' => true,
            ],
        );

        $accounts = [
            // ===== AKTIVA (Vermögen) =====
            ['1000', 'Kasse', 'asset'],
            ['1200', 'Bank', 'asset'],
            ['1210', 'Bankkredit / Passivsaldo', 'liability'],

            // Forderungen
            ['1400', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
            ['1410', 'Forderungen gegen verbundene Unternehmen', 'asset'],
            ['1545', 'Umsatzsteuerforderungen', 'asset'],

            // Vorsteuer
            ['1576', 'Vorsteuer 19%', 'asset'],
            ['1577', 'Vorsteuer 7%', 'asset'],

            // Anlagevermögen
            ['0200', 'Geschäftsausstattung', 'asset'],
            ['0480', 'Geringwertige Wirtschaftsgüter (GWG)', 'asset'],
            ['4850', 'Betriebs- und Geschäftsausstattung (BGA)', 'asset'],

            // ===== PASSIVA (Verbindlichkeiten) =====
            ['1776', 'Umsatzsteuer 19%', 'liability'],
            ['1777', 'Umsatzsteuer 7%', 'liability'],
            ['1797', 'USt.-Verbindlichkeiten (Sammelkonto)', 'liability'],
            ['2000', 'Verbindlichkeiten aus Lieferungen und Leistungen', 'liability'],
            ['2400', 'Darlehen', 'liability'],

            // ===== EIGENKAPITAL =====
            ['0800', 'Gezeichnetes Kapital', 'equity'],
            ['0840', 'Privateinlagen', 'equity'],
            ['0849', 'Privatentnahmen', 'equity'],
            ['0860', 'Gewinnvortrag', 'equity'],
            ['0868', 'Verlustvortrag', 'equity'],
            ['2970', 'Ergebnisvortrag', 'equity'],
            ['2980', 'Jahresüberschuss/Jahresfehlbetrag', 'equity'],

            // ===== ERTRÄGE (Revenue) =====
            ['4000', 'Umsatzerlöse 19%', 'revenue'],
            ['4007', 'Umsatzerlöse 7%', 'revenue'],
            ['4100', 'Mieteinnahmen', 'revenue'],
            ['4200', 'Provisionserlöse', 'revenue'],
            ['4300', 'Beratungserlöse', 'revenue'],
            ['4400', 'Lizenzerträge', 'revenue'],
            ['4800', 'Sonstige betriebliche Erträge', 'revenue'],
            ['8400', 'Erlöse 19%', 'revenue'],
            ['8300', 'Erlöse 7%', 'revenue'],

            // ===== AUFWENDUNGEN (Expenses) =====
            // Personalkosten
            ['4500', 'Gehälter und Löhne', 'expense'],
            ['4510', 'Sozialversicherungsbeiträge', 'expense'],
            ['4520', 'Pensionskosten', 'expense'],

            // Raumkosten
            ['4600', 'Mieten', 'expense'],
            ['4610', 'Nebenkosten', 'expense'],
            ['4620', 'Strom und Heizung', 'expense'],

            // Fahrzeugkosten
            ['4700', 'Kraftstoffkosten', 'expense'],
            ['4710', 'KFZ-Versicherung', 'expense'],
            ['4720', 'KFZ-Steuer', 'expense'],
            ['4730', 'KFZ-Reparaturen und Wartung', 'expense'],

            // Telekommunikation & IT
            ['4800', 'Telefon und Internet', 'expense'],
            ['4810', 'Hosting und Domains', 'expense'],
            ['4820', 'Softwarelizenzen', 'expense'],
            ['4830', 'IT-Wartung und Support', 'expense'],

            // Bürokosten
            ['4900', 'Büromaterial', 'expense'],
            ['4910', 'Porto und Versand', 'expense'],
            ['4920', 'Drucker und Kopierer', 'expense'],
            ['4930', 'Bürobedarf', 'expense'],

            // Werbung und Marketing
            ['5000', 'Werbung und Marketing', 'expense'],
            ['5010', 'Website und SEO', 'expense'],
            ['5020', 'Printmedien', 'expense'],

            // Beratung und Dienstleistungen
            ['5100', 'Rechts- und Steuerberatung', 'expense'],
            ['5110', 'Wirtschaftsprüfung', 'expense'],
            ['5120', 'Externe Berater', 'expense'],

            // Versicherungen
            ['5200', 'Betriebsversicherungen', 'expense'],
            ['5210', 'Haftpflichtversicherung', 'expense'],
            ['5220', 'Rechtsschutzversicherung', 'expense'],

            // Reisekosten
            ['5300', 'Reisekosten Inland', 'expense'],
            ['5310', 'Reisekosten Ausland', 'expense'],
            ['5320', 'Übernachtungskosten', 'expense'],
            ['5330', 'Verpflegungsmehraufwand', 'expense'],

            // Abschreibungen
            ['5400', 'Abschreibungen auf Sachanlagen', 'expense'],
            ['5410', 'Abschreibungen auf GWG', 'expense'],

            // Bank- und Finanzkosten
            ['5500', 'Bankgebühren', 'expense'],
            ['5510', 'Kontoführungsgebühren', 'expense'],
            ['5520', 'Zinsaufwendungen', 'expense'],
            ['5530', 'Kreditkartengebühren', 'expense'],
            ['4970', 'Kontoführungsgebühren', 'expense'],
            ['4975', 'PayPal-Gebühren', 'expense'],

            // Sonstige Aufwendungen
            ['5600', 'Bücher und Fachliteratur', 'expense'],
            ['5610', 'Fortbildungskosten', 'expense'],
            ['5620', 'Beiträge und Mitgliedschaften', 'expense'],
            ['5630', 'Spenden', 'expense'],
            ['6900', 'Sonstige betriebliche Aufwendungen', 'expense'],
            ['6950', 'Abgeschriebene Forderungen', 'expense'],
            ['6980', 'Sonstige betriebliche Aufwendungen', 'expense'],

            // ===== EBK =====
            ['9000', 'Eröffnungsbilanzkonto', 'equity'],
        ];

        foreach ($accounts as [$nr, $name, $type]) {
            Account::firstOrCreate(
                ['tenant_id' => $tenant->id, 'number' => $nr],
                ['name' => $name, 'type' => $type],
            );
        }

        $this->command->info('Kontenrahmen für Ruddat UG wurde erfolgreich erstellt!');
        $this->command->info('Anzahl Konten: ' . count($accounts));
    }
}
