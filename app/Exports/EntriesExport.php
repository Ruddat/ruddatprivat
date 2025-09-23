<?php

namespace App\Exports;

use App\Models\Entry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntriesExport implements FromCollection, WithHeadings, WithMapping
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
        return Entry::with(['debitAccount', 'creditAccount'])
            ->where('tenant_id', $this->tenantId)
            ->where('fiscal_year_id', $this->fiscalYearId)
            ->orderBy('booking_date')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Datum',
            'Soll-Konto',
            'Haben-Konto',
            'Betrag',
            'Beschreibung',
        ];
    }

    public function map($entry): array
    {
        return [
            $entry->booking_date->format('Y-m-d'),
            $entry->debitAccount?->number . ' – ' . $entry->debitAccount?->name,
            $entry->creditAccount?->number . ' – ' . $entry->creditAccount?->name,
            number_format($entry->amount, 2, ',', '.'),
            $entry->description,
        ];
    }
}
