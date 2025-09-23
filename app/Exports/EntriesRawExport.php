<?php

namespace App\Exports;

use App\Models\Entry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntriesRawExport implements FromCollection, WithHeadings
{
    protected int $tenantId;

    protected int $fiscalYearId;

    public function __construct(int $tenantId, int $fiscalYearId)
    {
        $this->tenantId = $tenantId;
        $this->fiscalYearId = $fiscalYearId;
    }

    public function collection()
    {
        return Entry::select([
            'id',
            'tenant_id',
            'fiscal_year_id',
            'booking_date',
            'debit_account_id',
            'credit_account_id',
            'amount',
            'description',
        ])
            ->where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $this->fiscalYearId)
            ->orderBy('booking_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tenant ID',
            'Fiscal Year ID',
            'Datum',
            'Soll-Konto-ID',
            'Haben-Konto-ID',
            'Betrag',
            'Beschreibung',
        ];
    }
}
