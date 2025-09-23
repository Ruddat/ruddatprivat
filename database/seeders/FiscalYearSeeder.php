<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FiscalYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(['slug' => 'ruddat-ug'], ['name' => 'Ruddat UG']);

        FiscalYear::firstOrCreate(
            ['tenant_id' => $tenant->id, 'year' => 2025],
            [
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
                'closed' => false,
            ],
        );
    }
}
