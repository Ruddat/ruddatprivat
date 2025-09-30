<?php

namespace App\Livewire\Backend\Customer;

use Livewire\Component;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class FeedbackForm extends Component
{
    public $title;
    public $message;
    public $category = 'idea';

    protected $rules = [
        'title' => 'required|string|max:255',
        'message' => 'required|string|min:5',
        'category' => 'required|in:bug,feature,idea',
    ];

    public function submit()
    {
        $this->validate();

$feedback = Feedback::create([
    'customer_id' => Auth::guard('customer')->id(),
    'title'       => $this->title,
    'message'     => $this->message,
    'category'    => $this->category,
]);

// Admins benachrichtigen
$admins = \App\Models\Admin::all();
foreach ($admins as $admin) {
    $admin->notify(new \App\Notifications\NewFeedbackNotification($feedback));
}


        $this->reset(['title', 'message', 'category']);
        session()->flash('success', 'Danke für dein Feedback!');
        $this->dispatch('feedback-submitted'); // optional für JS/Modal close
    }

    public function render()
    {
        return view('livewire.backend.customer.feedback-form')
                    ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}