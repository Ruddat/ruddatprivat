<?php

namespace App\Livewire\Backend\Admin\Customer;

use Livewire\Component;
use App\Models\Feedback;

class FeedbackManager extends Component
{
    public $statusFilter = 'all';

    public function setStatus($feedbackId, $status)
    {
        Feedback::find($feedbackId)?->update(['status' => $status]);
    }

    public function vote($feedbackId, $up = true)
    {
        $fb = Feedback::find($feedbackId);
        if ($fb) {
            $fb->increment('votes', $up ? 1 : -1);
        }
    }

    public function render()
    {
        $query = Feedback::query();
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
        return view('livewire.backend.admin.customer.feedback-manager', [
            'feedbacks' => $query->latest()->paginate(10),
        ])            ->extends('backend.admin.layouts.app')
            ->section('content');
    }
}

