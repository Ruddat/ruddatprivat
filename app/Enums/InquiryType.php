<?php

namespace App\Enums;

enum InquiryType: string
{
    case Maintenance = 'maintenance';
    case ManagedServices = 'managed_services';
    case Takeover = 'takeover';
    case Incident = 'incident';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Maintenance => 'Softwarewartung',
            self::ManagedServices => 'Managed Application Services',
            self::Takeover => 'Übernahme bestehender Software',
            self::Incident => 'Technische Störung',
            self::Other => 'Sonstige Anfrage',
        };
    }

    /** @return array<string, string> value => label */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $t) => [$t->value => $t->label()])->all();
    }
}
