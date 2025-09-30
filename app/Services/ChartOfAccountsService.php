<?php

namespace App\Services;

use App\Models\Account;

class ChartOfAccountsService
{
    public static function getFrameworks(): array
    {
        return [
            'basic' => 'Basis-Kontenrahmen (Vollständig, EBK-fähig)',
            'skr03' => 'SKR03 Light (mit EB-Konten)',
            'skr04' => 'SKR04 Light (mit EB-Konten)',
            'minimal' => 'Minimaler Kontenrahmen (Schnellstart)',
        ];
    }

    public static function createForTenant(int $tenantId, string $framework = 'basic'): void
    {
        $accounts = match ($framework) {
            'skr04' => self::skr04(),
            'skr03' => self::skr03(),
            'minimal' => self::minimal(),
            default => self::basic(),
        };

        foreach ($accounts as [$nr, $name, $type]) {
            Account::firstOrCreate(
                ['tenant_id' => $tenantId, 'number' => $nr],
                ['name' => $name, 'type' => $type],
            );
        }
    }

    /**
     * Vollständiger Basis-Kontenrahmen – professionell für kleine und mittlere Unternehmen
     */
private static function basic(): array
{
    return [
        // ===== AKTIVA =====
        ['1000', 'Kasse', 'asset'],
        ['1200', 'Bank', 'asset'],
        ['1400', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
        ['1576', 'Vorsteuer 19%', 'asset'],
        ['1577', 'Vorsteuer 7%', 'asset'],
        ['0220', 'Betriebs- und Geschäftsausstattung', 'asset'],
        ['0280', 'Fuhrpark', 'asset'],

        // ===== PASSIVA =====
        ['1776', 'Umsatzsteuer 19%', 'liability'],
        ['1777', 'Umsatzsteuer 7%', 'liability'],
        ['2000', 'Verbindlichkeiten aus Lieferungen und Leistungen', 'liability'],
        ['2700', 'Darlehen', 'liability'],

        // ===== EIGENKAPITAL =====
        ['0800', 'Gezeichnetes Kapital', 'equity'],
        ['0840', 'Privateinlagen', 'equity'],
        ['0849', 'Privatentnahmen', 'equity'],
        ['0860', 'Gewinnvortrag', 'equity'],
        ['0868', 'Verlustvortrag', 'equity'],
        ['0880', 'Jahresüberschuss', 'equity'],
        ['0888', 'Jahresfehlbetrag', 'equity'],

        // ===== ERTRÄGE (8000-8999) =====
        ['8400', 'Umsatzerlöse 19%', 'revenue'],
        ['8407', 'Umsatzerlöse 7%', 'revenue'],
        ['8300', 'Erlöse 19%', 'revenue'],
        ['8307', 'Erlöse 7%', 'revenue'],
        ['8500', 'Mieteinnahmen', 'revenue'],
        ['8600', 'Provisionserlöse', 'revenue'],
        ['8700', 'Beratungserlöse', 'revenue'],
        ['8800', 'Lizenzerträge', 'revenue'],
        ['8900', 'Sonstige betriebliche Erträge', 'revenue'],

        // ===== AUFWENDUNGEN (5000-6999) =====
        // Personalkosten
        ['6000', 'Gehälter und Löhne', 'expense'],
        ['6010', 'Sozialversicherungsbeiträge', 'expense'],
        
        // Raumkosten
        ['6200', 'Mieten', 'expense'],
        ['6240', 'Strom', 'expense'],
        ['6250', 'Heizung', 'expense'],
        
        // Fahrzeugkosten
        ['6400', 'Kraftstoffkosten', 'expense'],
        ['6420', 'KFZ-Versicherung', 'expense'],
        ['6430', 'KFZ-Steuer', 'expense'],
        ['6440', 'KFZ-Reparaturen und Wartung', 'expense'],
        
        // Telekommunikation & IT
        ['6270', 'Telefon und Internet', 'expense'],
        ['6280', 'Hosting und Domains', 'expense'],
        ['5620', 'Softwarelizenzen', 'expense'],
        
        // Bürokosten
        ['6290', 'Büromaterial', 'expense'],
        ['6280', 'Porto und Versand', 'expense'],
        
        // Werbung und Marketing
        ['6340', 'Werbung und Marketing', 'expense'],
        ['6345', 'Website und SEO', 'expense'],
        
        // Beratung und Dienstleistungen
        ['6360', 'Rechts- und Steuerberatung', 'expense'],
        ['6365', 'Externe Berater', 'expense'],
        
        // Versicherungen
        ['6350', 'Betriebsversicherungen', 'expense'],
        ['6355', 'Haftpflichtversicherung', 'expense'],
        
        // Reisekosten
        ['6320', 'Reisekosten', 'expense'],
        ['6325', 'Übernachtungskosten', 'expense'],
        
        // Abschreibungen
        ['6100', 'Abschreibungen auf Sachanlagen', 'expense'],
        
        // Bank- und Finanzkosten
        ['6390', 'Bankgebühren', 'expense'],
        ['6500', 'Zinsaufwendungen', 'expense'],
        
        // Sonstige Aufwendungen
        ['6300', 'Bücher und Fachliteratur', 'expense'],
        ['6310', 'Fortbildungskosten', 'expense'],
        ['6900', 'Sonstige betriebliche Aufwendungen', 'expense'],

        // ===== EBK =====
        ['9000', 'Eröffnungsbilanzkonto', 'equity'],
    ];
}

    /**
     * Minimaler Kontenrahmen für Schnellstart
     */
private static function minimal(): array
{
    return [
        // Aktiva
        ['1000', 'Kasse', 'asset'],
        ['1200', 'Bank', 'asset'],
        ['1576', 'Vorsteuer 19%', 'asset'],

        // Passiva
        ['1776', 'Umsatzsteuer 19%', 'liability'],

        // Eigenkapital
        ['0800', 'Gezeichnetes Kapital', 'equity'],
        ['0880', 'Jahresüberschuss', 'equity'],
        ['0888', 'Jahresfehlbetrag', 'equity'],

        // Erträge
        ['8400', 'Umsatzerlöse 19%', 'revenue'],

        // Aufwendungen
        ['6290', 'Büromaterial', 'expense'],
        ['6400', 'Kraftstoffkosten', 'expense'],
        ['6270', 'Telefon und Internet', 'expense'],
        ['6224', 'Reparaturkosten', 'expense'],
        ['6900', 'Sonstige Aufwendungen', 'expense'],

        // EBK
        ['9000', 'Eröffnungsbilanzkonto', 'equity'],
    ];
}

    /**
     * SKR03 Light - angepasst an Standard SKR03 Nummern
     */
private static function skr03(): array
{
    return [
        // ===== AKTIVA =====
        ['0100', 'Grundstücke und Bauten', 'asset'],
        ['0120', 'Geschäfts- und Firmenwert', 'asset'],
        ['0200', 'Technische Anlagen und Maschinen', 'asset'],
        ['0220', 'Betriebs- und Geschäftsausstattung', 'asset'],
        ['0240', 'Büroausstattung', 'asset'],
        ['0260', 'EDV-Anlagen', 'asset'],
        ['0280', 'Fuhrpark', 'asset'],
        ['0300', 'Geleistete Anzahlungen', 'asset'],
        ['0320', 'Anlagen im Bau', 'asset'],
        
        // Umlaufvermögen
        ['1000', 'Kasse', 'asset'],
        ['1020', 'Bundesbankguthaben', 'asset'],
        ['1040', 'Schecks', 'asset'],
        ['1200', 'Bank', 'asset'],
        ['1220', 'Termingelder', 'asset'],
        ['1400', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
        ['1420', 'Forderungen gegen verbundene Unternehmen', 'asset'],
        ['1440', 'Forderungen gegen Gesellschafter', 'asset'],
        ['1460', 'Sonstige Vermögensgegenstände', 'asset'],
        ['1480', 'Wechsel', 'asset'],
        
        // Vorräte
        ['1600', 'Rohstoffe', 'asset'],
        ['1620', 'Hilfsstoffe', 'asset'],
        ['1640', 'Betriebsstoffe', 'asset'],
        ['1660', 'Unfertige Erzeugnisse', 'asset'],
        ['1680', 'Fertige Erzeugnisse', 'asset'],
        ['1700', 'Waren', 'asset'],
        
        // Aktive Rechnungsabgrenzung
        ['1800', 'Aktive Rechnungsabgrenzung', 'asset'],
        
        // Vorsteuer
        ['1576', 'Vorsteuer 19%', 'asset'],
        ['1577', 'Vorsteuer 7%', 'asset'],

        // ===== PASSIVA =====
        ['2000', 'Verbindlichkeiten aus Lieferungen und Leistungen', 'liability'],
        ['2020', 'Verbindlichkeiten gegen verbundene Unternehmen', 'liability'],
        ['2040', 'Verbindlichkeiten gegen Gesellschafter', 'liability'],
        ['2060', 'Wechselverbindlichkeiten', 'liability'],
        ['2080', 'Sonstige Verbindlichkeiten', 'liability'],
        
        // Rückstellungen
        ['2500', 'Rückstellungen für Pensionen', 'liability'],
        ['2520', 'Steuerrückstellungen', 'liability'],
        ['2540', 'Sonstige Rückstellungen', 'liability'],
        
        // Umsatzsteuer
        ['1776', 'Umsatzsteuer 19%', 'liability'],
        ['1777', 'Umsatzsteuer 7%', 'liability'],
        ['1779', 'Umsatzsteuer Voranmeldung', 'liability'],
        
        // Darlehen/Kredite
        ['2700', 'Darlehen', 'liability'],
        ['2720', 'Hypotheken', 'liability'],
        
        // Passive Rechnungsabgrenzung
        ['2800', 'Passive Rechnungsabgrenzung', 'liability'],

        // ===== EIGENKAPITAL =====
        ['0800', 'Gezeichnetes Kapital', 'equity'],
        ['0820', 'Kapitalrücklage', 'equity'],
        ['0840', 'Gewinnrücklage', 'equity'],
        ['0860', 'Gewinnvortrag', 'equity'],
        ['0868', 'Verlustvortrag', 'equity'],
        ['0880', 'Jahresüberschuss', 'equity'],
        ['0888', 'Jahresfehlbetrag', 'equity'],
        ['0890', 'Privatkonto', 'equity'],

        // ===== ERTRÄGE (8000-8999) =====
        ['8000', 'Umsatzerlöse 19%', 'revenue'],
        ['8001', 'Umsatzerlöse 7%', 'revenue'],
        ['8100', 'Bestandsveränderungen', 'revenue'],
        ['8200', 'andere aktivierte Eigenleistungen', 'revenue'],
        ['8300', 'Erlöse 19%', 'revenue'],
        ['8301', 'Erlöse 7%', 'revenue'],
        ['8400', 'Sonstige betriebliche Erträge', 'revenue'],
        ['8500', 'Mieterträge', 'revenue'],
        ['8600', 'Zinserträge', 'revenue'],
        ['8700', 'Erträge aus Beteiligungen', 'revenue'],
        ['8800', 'Erträge aus Gewinnabführungsverträgen', 'revenue'],
        ['8900', 'außerordentliche Erträge', 'revenue'],

        // ===== AUFWENDUNGEN (5000-6999) =====
        // Materialaufwand
        ['5000', 'Aufwendungen für Rohstoffe', 'expense'],
        ['5100', 'Aufwendungen für Hilfsstoffe', 'expense'],
        ['5200', 'Aufwendungen für Betriebsstoffe', 'expense'],
        ['5300', 'Bezugskosten', 'expense'],
        ['5400', 'Wareneingang', 'expense'],
        
        // Personalaufwand
        ['6000', 'Löhne und Gehälter', 'expense'],
        ['6010', 'Sozialversicherungsbeiträge', 'expense'],
        ['6020', 'Beiträge zur Berufsgenossenschaft', 'expense'],
        ['6030', 'Aufwendungen für Altersversorgung', 'expense'],
        ['6040', 'Aufwendungen für Unterstützung', 'expense'],
        
        // Abschreibungen
        ['6100', 'Abschreibungen auf Sachanlagen', 'expense'],
        ['6120', 'Abschreibungen auf immaterielle Vermögensgegenstände', 'expense'],
        
        // Sonstige betriebliche Aufwendungen
        ['6200', 'Mieten', 'expense'],
        ['6210', 'Pachten', 'expense'],
        ['6220', 'Reparaturen und Instandhaltung', 'expense'],
        ['6230', 'Beleuchtung', 'expense'],
        ['6240', 'Heizung', 'expense'],
        ['6250', 'Strom', 'expense'],
        ['6260', 'Wasser', 'expense'],
        ['6270', 'Telefon', 'expense'],
        ['6280', 'Porto', 'expense'],
        ['6290', 'Büromaterial', 'expense'],
        ['6300', 'Fachliteratur', 'expense'],
        ['6310', 'Fortbildungskosten', 'expense'],
        ['6320', 'Reisekosten', 'expense'],
        ['6330', 'Bewirtungskosten', 'expense'],
        ['6340', 'Werbekosten', 'expense'],
        ['6350', 'Versicherungsbeiträge', 'expense'],
        ['6360', 'Rechts- und Beratungskosten', 'expense'],
        ['6370', 'Steuerberatungskosten', 'expense'],
        ['6380', 'Buchführungs- und Bilanzierungshilfen', 'expense'],
        ['6390', 'Bankgebühren', 'expense'],
        ['6400', 'Kfz-Kosten', 'expense'],
        ['6410', 'Kraftstoff', 'expense'],
        ['6420', 'Kfz-Versicherung', 'expense'],
        ['6430', 'Kfz-Steuer', 'expense'],
        ['6440', 'Kfz-Reparaturen', 'expense'],
        ['6450', 'Abschreibungen auf Kfz', 'expense'],
        
        // Finanzaufwendungen
        ['6500', 'Zinsaufwendungen', 'expense'],
        ['6600', 'Abschreibungen auf Finanzanlagen', 'expense'],
        
        // Außerordentliche Aufwendungen
        ['6700', 'außerordentliche Aufwendungen', 'expense'],
        
        // Steuern
        ['6800', 'Ertragsteuern', 'expense'],
        ['6900', 'Sonstige Steuern', 'expense'],

        // ===== EBK =====
        ['9000', 'Eröffnungsbilanzkonto', 'equity'],
        ['9001', 'Schlussbilanzkonto', 'equity'],
    ];
}
    /**
     * SKR04 Light - angepasst an Standard SKR04 Nummern
     */
private static function skr04(): array
{
    return [
        // ===== ANLAGEVERMÖGEN =====
        // Immaterielle Vermögensgegenstände
        ['0000', 'Grundstücke', 'asset'],
        ['0001', 'Grundstücke im Bau', 'asset'],
        ['0010', 'Bauten auf eigenen Grundstücken', 'asset'],
        ['0011', 'Bauten auf fremden Grundstücken', 'asset'],
        ['0020', 'Technische Anlagen und Maschinen', 'asset'],
        ['0030', 'Betriebs- und Geschäftsausstattung', 'asset'],
        ['0040', 'Büroausstattung', 'asset'],
        ['0050', 'EDV-Anlagen', 'asset'],
        ['0060', 'Fuhrpark', 'asset'],
        ['0070', 'Geleistete Anzahlungen', 'asset'],
        ['0080', 'Anlagen im Bau', 'asset'],
        ['0090', 'Geschäfts- oder Firmenwert', 'asset'],

        // ===== UMLAUFVERMÖGEN =====
        // Flüssige Mittel
        ['1000', 'Kasse', 'asset'],
        ['1020', 'Bundesbankguthaben', 'asset'],
        ['1040', 'Schecks', 'asset'],
        ['1200', 'Bank', 'asset'],
        ['1220', 'Termingelder', 'asset'],
        
        // Forderungen
        ['1400', 'Forderungen aus Lieferungen und Leistungen', 'asset'],
        ['1401', 'Zweifelhafte Forderungen', 'asset'],
        ['1402', 'Forderungen gegen verbundene Unternehmen', 'asset'],
        ['1403', 'Forderungen gegen Gesellschafter', 'asset'],
        ['1404', 'Sonstige Vermögensgegenstände', 'asset'],
        ['1405', 'Wechsel', 'asset'],
        
        // Vorsteuer
        ['1406', 'Vorsteuer 19%', 'asset'],
        ['1407', 'Vorsteuer 7%', 'asset'],
        
        // Vorräte
        ['1600', 'Rohstoffe', 'asset'],
        ['1601', 'Hilfsstoffe', 'asset'],
        ['1602', 'Betriebsstoffe', 'asset'],
        ['1603', 'Unfertige Erzeugnisse', 'asset'],
        ['1604', 'Fertige Erzeugnisse', 'asset'],
        ['1605', 'Waren', 'asset'],
        
        // Aktive Rechnungsabgrenzung
        ['1800', 'Aktive Rechnungsabgrenzung', 'asset'],

        // ===== PASSIVA =====
        // Verbindlichkeiten
        ['3300', 'Verbindlichkeiten aus Lieferungen und Leistungen', 'liability'],
        ['3301', 'Verbindlichkeiten gegen verbundene Unternehmen', 'liability'],
        ['3302', 'Verbindlichkeiten gegen Gesellschafter', 'liability'],
        ['3303', 'Wechselverbindlichkeiten', 'liability'],
        ['3304', 'Sonstige Verbindlichkeiten', 'liability'],
        
        // Rückstellungen
        ['3000', 'Rückstellungen für Pensionen', 'liability'],
        ['3001', 'Steuerrückstellungen', 'liability'],
        ['3002', 'Sonstige Rückstellungen', 'liability'],
        
        // Umsatzsteuer
        ['3800', 'Umsatzsteuer 19%', 'liability'],
        ['3801', 'Umsatzsteuer 7%', 'liability'],
        ['3806', 'Umsatzsteuer Voranmeldung', 'liability'],
        
        // Darlehen/Kredite
        ['3700', 'Darlehen', 'liability'],
        ['3701', 'Hypotheken', 'liability'],
        
        // Passive Rechnungsabgrenzung
        ['3900', 'Passive Rechnungsabgrenzung', 'liability'],

        // ===== EIGENKAPITAL =====
        ['0800', 'Gezeichnetes Kapital', 'equity'],
        ['0801', 'Kapitalrücklage', 'equity'],
        ['0802', 'Gewinnrücklage', 'equity'],
        ['0803', 'Gewinnvortrag', 'equity'],
        ['0804', 'Verlustvortrag', 'equity'],
        ['0805', 'Jahresüberschuss', 'equity'],
        ['0806', 'Jahresfehlbetrag', 'equity'],
        ['0807', 'Privatkonto', 'equity'],

        // ===== BETRIEBLICHE ERTRÄGE (4000-4999) =====
        ['4000', 'Umsatzerlöse 19%', 'revenue'],
        ['4001', 'Umsatzerlöse 7%', 'revenue'],
        ['4100', 'Bestandsveränderungen', 'revenue'],
        ['4200', 'andere aktivierte Eigenleistungen', 'revenue'],
        ['4300', 'Sonstige betriebliche Erträge', 'revenue'],
        ['4400', 'Mieterträge', 'revenue'],
        ['4500', 'Zinserträge', 'revenue'],
        ['4600', 'Erträge aus Beteiligungen', 'revenue'],
        ['4700', 'Erträge aus Gewinnabführungsverträgen', 'revenue'],
        ['4800', 'außerordentliche Erträge', 'revenue'],
        ['4900', 'Erträge aus dem Abgang von Gegenständen des Anlagevermögens', 'revenue'],

        // ===== BETRIEBLICHE AUFWENDUNGEN (5000-6999) =====
        // Materialaufwand
        ['5000', 'Aufwendungen für Rohstoffe', 'expense'],
        ['5001', 'Aufwendungen für Hilfsstoffe', 'expense'],
        ['5002', 'Aufwendungen für Betriebsstoffe', 'expense'],
        ['5003', 'Bezugskosten', 'expense'],
        ['5004', 'Wareneingang', 'expense'],
        
        // Personalaufwand
        ['6000', 'Löhne und Gehälter', 'expense'],
        ['6001', 'Sozialversicherungsbeiträge', 'expense'],
        ['6002', 'Beiträge zur Berufsgenossenschaft', 'expense'],
        ['6003', 'Aufwendungen für Altersversorgung', 'expense'],
        ['6004', 'Aufwendungen für Unterstützung', 'expense'],
        
        // Abschreibungen
        ['6100', 'Abschreibungen auf Sachanlagen', 'expense'],
        ['6101', 'Abschreibungen auf immaterielle Vermögensgegenstände', 'expense'],
        
        // Sonstige betriebliche Aufwendungen
        ['6200', 'Mieten', 'expense'],
        ['6201', 'Pachten', 'expense'],
        ['6202', 'Reparaturen und Instandhaltung', 'expense'],
        ['6203', 'Beleuchtung', 'expense'],
        ['6204', 'Heizung', 'expense'],
        ['6205', 'Strom', 'expense'],
        ['6206', 'Wasser', 'expense'],
        ['6207', 'Telefon', 'expense'],
        ['6208', 'Porto', 'expense'],
        ['6209', 'Büromaterial', 'expense'],
        ['6210', 'Fachliteratur', 'expense'],
        ['6211', 'Fortbildungskosten', 'expense'],
        ['6212', 'Reisekosten', 'expense'],
        ['6213', 'Bewirtungskosten', 'expense'],
        ['6214', 'Werbekosten', 'expense'],
        ['6215', 'Versicherungsbeiträge', 'expense'],
        ['6216', 'Rechts- und Beratungskosten', 'expense'],
        ['6217', 'Steuerberatungskosten', 'expense'],
        ['6218', 'Buchführungs- und Bilanzierungshilfen', 'expense'],
        ['6219', 'Bankgebühren', 'expense'],
        ['6220', 'Kfz-Kosten', 'expense'],
        ['6221', 'Kraftstoff', 'expense'],
        ['6222', 'Kfz-Versicherung', 'expense'],
        ['6223', 'Kfz-Steuer', 'expense'],
        ['6224', 'Kfz-Reparaturen', 'expense'],
        ['6225', 'Abschreibungen auf Kfz', 'expense'],
        
        // Finanzaufwendungen
        ['6300', 'Zinsaufwendungen', 'expense'],
        ['6301', 'Abschreibungen auf Finanzanlagen', 'expense'],
        
        // Außerordentliche Aufwendungen
        ['6400', 'außerordentliche Aufwendungen', 'expense'],
        
        // Steuern
        ['6500', 'Ertragsteuern', 'expense'],
        ['6501', 'Sonstige Steuern', 'expense'],
        
        // Landwirtschaftliche Aufwendungen (SKR04 spezifisch)
        ['6600', 'Aufwendungen für Saatgut', 'expense'],
        ['6601', 'Aufwendungen für Düngemittel', 'expense'],
        ['6602', 'Aufwendungen für Pflanzenschutz', 'expense'],
        ['6603', 'Aufwendungen für Futtermittel', 'expense'],
        ['6604', 'Tierarztkosten', 'expense'],
        ['6605', 'Maschinenkosten', 'expense'],
        ['6606', 'Aufwendungen für Energie', 'expense'],

        // ===== EBK =====
        ['9000', 'Eröffnungsbilanzkonto', 'equity'],
        ['9001', 'Schlussbilanzkonto', 'equity'],
    ];
}

}
