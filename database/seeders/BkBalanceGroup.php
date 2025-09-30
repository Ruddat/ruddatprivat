<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BkBalanceGroup extends Seeder
{
    public function run(): void
    {
        DB::table('bk_balance_groups')->truncate();

        DB::table('bk_balance_groups')->insert([
            // === AKTIVA ===
            [
                'skr' => 'skr03',
                'side' => 'asset',
                'account_number_from' => 0,
                'account_number_to'   => 1999,
                'group_key' => 'anlagevermoegen',
                'group_label' => 'Anlagevermögen',
            ],
            [
                'skr' => 'skr03',
                'side' => 'asset',
                'account_number_from' => 2000,
                'account_number_to'   => 2999,
                'group_key' => 'umlaufvermoegen',
                'group_label' => 'Umlaufvermögen',
            ],

            // === PASSIVA ===
            [
                'skr' => 'skr03',
                'side' => 'equity',
                'account_number_from' => 3000,
                'account_number_to'   => 3999,
                'group_key' => 'eigenkapital',
                'group_label' => 'Eigenkapital',
            ],
            [
                'skr' => 'skr03',
                'side' => 'liability',
                'account_number_from' => 4000,
                'account_number_to'   => 4999,
                'group_key' => 'rueckstellungen',
                'group_label' => 'Rückstellungen',
            ],
            [
                'skr' => 'skr03',
                'side' => 'liability',
                'account_number_from' => 5000,
                'account_number_to'   => 5999,
                'group_key' => 'verbindlichkeiten',
                'group_label' => 'Verbindlichkeiten',
            ],

            // === STEUERKONTEN / SONSTIGES ===
            [
                'skr' => 'skr03',
                'side' => 'asset',
                'account_number_from' => 1570,
                'account_number_to'   => 1579,
                'group_key' => 'vorsteuer',
                'group_label' => 'Forderungen Vorsteuer',
            ],
            [
                'skr' => 'skr03',
                'side' => 'liability',
                'account_number_from' => 1770,
                'account_number_to'   => 1779,
                'group_key' => 'umsatzsteuer',
                'group_label' => 'Verbindlichkeiten Umsatzsteuer',
            ],
        ]);
    }
}