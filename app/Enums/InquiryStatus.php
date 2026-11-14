<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Question = 'question';
    case OfferSent = 'offer_sent';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Neu',
            self::InProgress => 'In Bearbeitung',
            self::Question => 'Rückfrage',
            self::OfferSent => 'Angebot erstellt',
            self::Completed => 'Abgeschlossen',
            self::Archived => 'Archiviert',
        };
    }

    /** Tailwind-Klassen für Status-Badges im Adminbereich. */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-amber-100 text-amber-800 ring-amber-200',
            self::InProgress => 'bg-blue-100 text-blue-800 ring-blue-200',
            self::Question => 'bg-purple-100 text-purple-800 ring-purple-200',
            self::OfferSent => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            self::Completed => 'bg-slate-100 text-slate-700 ring-slate-200',
            self::Archived => 'bg-slate-50 text-slate-500 ring-slate-200',
        };
    }

    /** Status, die als „offen“ gelten. */
    public static function open(): array
    {
        return [self::New, self::InProgress, self::Question, self::OfferSent];
    }
}
