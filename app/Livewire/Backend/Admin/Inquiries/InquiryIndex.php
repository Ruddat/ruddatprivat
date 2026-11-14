<?php

namespace App\Livewire\Backend\Admin\Inquiries;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Models\Inquiry;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class InquiryIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    /** 'open' (Standard), 'all' oder ein konkreter Status */
    #[Url]
    public string $status = 'open';

    #[Url]
    public string $type = '';

    /** Zeitraum in Tagen, leer = alle */
    #[Url]
    public string $period = '';

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'type', 'period'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'period']);
        $this->status = 'open';
        $this->resetPage();
    }

    public function render()
    {
        $inquiries = Inquiry::query()
            ->search($this->search)
            ->when($this->status === 'open', fn ($q) => $q->open())
            ->when(! in_array($this->status, ['open', 'all'], true) && InquiryStatus::tryFrom($this->status),
                fn ($q) => $q->where('status', $this->status))
            ->when(InquiryType::tryFrom($this->type), fn ($q) => $q->where('type', $this->type))
            ->when(in_array($this->period, ['7', '30', '90', '365'], true),
                fn ($q) => $q->where('created_at', '>=', now()->subDays((int) $this->period)))
            // Neue Anfragen zuerst, danach nach Eingang
            ->orderByRaw("case when status = 'new' then 0 else 1 end")
            ->latest()
            ->paginate(20);

        return view('livewire.backend.admin.inquiries.inquiry-index', [
            'inquiries' => $inquiries,
            'stats' => [
                'new' => Inquiry::where('status', InquiryStatus::New->value)->count(),
                'open' => Inquiry::open()->count(),
                'last30' => Inquiry::where('created_at', '>=', now()->subDays(30))->count(),
            ],
            'statuses' => InquiryStatus::cases(),
            'types' => InquiryType::options(),
        ])->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
