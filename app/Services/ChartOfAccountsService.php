<?php

namespace App\Services;

use App\Models\Account;

class ChartOfAccountsService
{
    public static function getFrameworks(): array
    {
        return [
            'basic' => 'Basis-Kontenrahmen (Minimal, erweitert für EBK)',
            'skr03' => 'SKR03 Light (mit EB-Konten)',
            'skr04' => 'SKR04 Light (mit EB-Konten)',
        ];
    }

    public static function createForTenant(int $tenantId, string $framework = 'basic'): void
    {
        $accounts = match ($framework) {
            'skr04' => self::skr04(),
            'skr03' => self::skr03(),
            default => self::basic(),
        };

        foreach ($accounts as [$nr,$name,$type]) {
            Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => $nr],
                ['name' => $name, 'type' => $type],
            );
        }
    }

    /**
     * Basis-Kontenrahmen – universell für kleine Firmen, minimal aber EBK-fähig.
     */
    private static function basic(): array
    {
        return [
            // Aktiva
            ['1000', 'Kasse', 'asset'],
            ['1200', 'Bank', 'asset'],
            ['1210', 'Bankkredit / Passivsaldo', 'liability'], // 🆕 neues Konto
            ['0480', 'Geringwertige Wirtschaftsgüter (GWG)', 'asset'],
            ['1410', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
            ['1545', 'Umsatzsteuerforderungen', 'asset'],
            ['1576', 'Vorsteuer 19%', 'asset'],

            // Passiva / Verbindlichkeiten
            ['1776', 'Umsatzsteuer 19%', 'liability'],
            ['1797', 'USt.-Verbindlichkeiten (Sammelkonto)', 'liability'],

            // Eigenkapital
            ['0800', 'Gezeichnetes Kapital', 'equity'],
            ['0860', 'Gewinnvortrag vor Verwendung', 'equity'],
            ['0868', 'Verlustvortrag vor Verwendung', 'equity'],
            ['2970', 'Ergebnisvortrag', 'equity'],
            ['2979', 'Jahresüberschuss/Jahresfehlbetrag', 'equity'],

            // EBK
            ['9000', 'Eröffnungsbilanzkonto', 'equity'],

            // Beispiel GuV-Konten
            ['8400', 'Erlöse 19%', 'revenue'],
            ['4930', 'Bürobedarf', 'expense'],
            ['4975', 'PayPal-Gebühren', 'expense'],
        ];
    }

    /**
     * SKR03 Light.
     */
    private static function skr03(): array
    {
        return [
            // Aktiva
            ['1000', 'Kasse', 'asset'],
            ['1200', 'Bank', 'asset'],
            ['1210', 'Bankkredit / Passivsaldo', 'liability'], // 🆕 neues Konto
            ['0480', 'Geringwertige Wirtschaftsgüter (GWG)', 'asset'],
            ['1410', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
            ['1545', 'Umsatzsteuerforderungen', 'asset'],
            ['1576', 'Vorsteuer 19%', 'asset'],

            // Passiva
            ['1776', 'Umsatzsteuer 19%', 'liability'],
            ['1797', 'USt.-Verbindlichkeiten (Sammelkonto)', 'liability'],

            // Eigenkapital
            ['0800', 'Gezeichnetes Kapital', 'equity'],
            ['0860', 'Gewinnvortrag vor Verwendung', 'equity'],
            ['0868', 'Verlustvortrag vor Verwendung', 'equity'],
            ['2970', 'Ergebnisvortrag', 'equity'],
            ['2979', 'Jahresüberschuss/Jahresfehlbetrag', 'equity'],

            // EBK
            ['9000', 'Eröffnungsbilanzkonto', 'equity'],

            // GuV
            ['8000', 'Erlöse 19%', 'revenue'],
            ['6000', 'Mietaufwand', 'expense'],
            ['6600', 'Bürobedarf', 'expense'],
            ['6855', 'Bankgebühren', 'expense'],
        ];
    }

    /**
     * SKR04 Light.
     */
    private static function skr04(): array
    {
        return [
            // Aktiva
            ['1000', 'Kasse', 'asset'],
            ['1200', 'Bank', 'asset'],
            ['1210', 'Bankkredit / Passivsaldo', 'liability'], // 🆕 neues Konto
            ['0480', 'Geringwertige Wirtschaftsgüter (GWG)', 'asset'],
            ['1406', 'Vorsteuer 19%', 'asset'],

            // Forderungen
            ['1410', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
            ['1545', 'Umsatzsteuerforderungen', 'asset'],

            // Passiva
            ['3806', 'Umsatzsteuer 19%', 'liability'],
            ['3797', 'USt.-Verbindlichkeiten (Sammelkonto)', 'liability'],

            // Eigenkapital
            ['0800', 'Gezeichnetes Kapital', 'equity'],
            ['0860', 'Gewinnvortrag vor Verwendung', 'equity'],
            ['0868', 'Verlustvortrag vor Verwendung', 'equity'],
            ['2970', 'Ergebnisvortrag', 'equity'],
            ['2979', 'Jahresüberschuss/Jahresfehlbetrag', 'equity'],

            // EBK
            ['9000', 'Eröffnungsbilanzkonto', 'equity'],

            // GuV
            ['4000', 'Erlöse 19%', 'revenue'],
            ['6000', 'Mietaufwand', 'expense'],
            ['6800', 'Bürobedarf', 'expense'],
            ['6850', 'Bankgebühren', 'expense'],
        ];
    }
}
