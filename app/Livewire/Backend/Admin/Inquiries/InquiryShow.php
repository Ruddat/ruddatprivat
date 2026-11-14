<?php

namespace App\Livewire\Backend\Admin\Inquiries;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Validation\Rule;
use Livewire\Component;

class InquiryShow extends Component
{
    public Inquiry $inquiry;

    public string $status = '';

    public string $internal_notes = '';

    public function mount(Inquiry $inquiry): void
    {
        $this->inquiry = $inquiry;
        $inquiry->markViewed();

        $this->status = $inquiry->status->value;
        $this->internal_notes = (string) $inquiry->internal_notes;
    }

    public function save(): void
    {
        $this->validate([
            'status' => ['required', Rule::enum(InquiryStatus::class)],
            'internal_notes' => ['nullable', 'string', 'max:20000'],
        ]);

        $this->inquiry->internal_notes = $this->internal_notes !== '' ? $this->internal_notes : null;
        $this->inquiry->changeStatus(InquiryStatus::from($this->status));
        $this->inquiry->save();

        session()->flash('success', 'Anfrage wurde gespeichert.');
    }

    public function archive(): void
    {
        $this->inquiry->changeStatus(InquiryStatus::Archived);
        $this->status = InquiryStatus::Archived->value;

        session()->flash('success', 'Anfrage wurde archiviert.');
    }

    public function replyMailto(): string
    {
        $subject = 'Ihre Anfrage ' . $this->inquiry->reference . ' – ' . $this->inquiry->type->label();
        $body = 'Guten Tag ' . $this->inquiry->name . ",\n\nvielen Dank für Ihre Anfrage.\n\n";

        return 'mailto:' . rawurlencode($this->inquiry->email)
            . '?subject=' . rawurlencode($subject)
            . '&body=' . rawurlencode($body);
    }

    public function render()
    {
        return view('livewire.backend.admin.inquiries.inquiry-show', [
            'statuses' => InquiryStatus::cases(),
        ])->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
