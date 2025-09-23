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
            // Zahlungsmittel
            ['1000', 'Kasse', 'asset'],
            ['1200', 'Bank', 'asset'],

            // Umsatzerlöse
            ['4800', 'Provisionserlöse 19%', 'revenue'],
            ['8400', 'Erlöse 19%', 'revenue'],
            ['8300', 'Erlöse 7%', 'revenue'],

            // Aufwendungen
            ['4930', 'Bürobedarf', 'expense'],
            ['4950', 'EDV / Hostingkosten', 'expense'],     // Server, Domains, SaaS
            ['4600', 'Fahrzeugkosten', 'expense'],
            ['4900', 'Provisionen / Fremdleistungen', 'expense'],
            ['4905', 'Lizenzgebühren / Software-Nutzung', 'expense'], // 🆕 Zahlung an dich privat
            ['4970', 'Kontoführungsgebühren', 'expense'],
            ['4975', 'PayPal-Gebühren', 'expense'],         // 🆕 PayPal Kosten
            ['6950', 'Abgeschriebene Forderungen', 'expense'],
            ['6980', 'Sonstige betriebliche Aufwendungen', 'expense'],

            // Anlagen
            ['0480', 'Geringwertige Wirtschaftsgüter (GWG)', 'asset'], // bis 800 € netto
            ['4850', 'Betriebs- und Geschäftsausstattung (BGA)', 'asset'], // über 800 €

            // Steuerkonten
            ['1576', 'Vorsteuer 19%', 'asset'],
            ['1776', 'Umsatzsteuer 19%', 'liability'],

            // Eigenkapital
            ['2970', 'Ergebnisvortrag', 'equity'],
        ];

        foreach ($accounts as [$nr,$name,$type]) {
            Account::firstOrCreate(
                ['tenant_id' => $tenant->id, 'number' => $nr],
                ['name' => $name, 'type' => $type],
            );
        }
    }
}
