<?php

namespace App\Livewire\Backend\Customer;

use Livewire\Component;
use App\Models\Feedback;
use App\Models\FeedbackVote;
use Illuminate\Support\Facades\Auth;

class FeedbackBoard extends Component
{
    public $showForm = false;

public function vote($id, $up = true)
{
    $customerId = Auth::guard('customer')->id();

    $vote = \App\Models\FeedbackVote::where('feedback_id', $id)
        ->where('customer_id', $customerId)
        ->first();

    if ($vote) {
        // wenn gleiche Stimme erneut geklickt → zurücknehmen (löschen)
        if ($vote->upvote === $up) {
            $vote->delete();
        } else {
            // ansonsten Richtung ändern
            $vote->update(['upvote' => $up]);
        }
    } else {
        // erste Stimme
        \App\Models\FeedbackVote::create([
            'feedback_id' => $id,
            'customer_id' => $customerId,
            'upvote'      => $up,
        ]);
    }

    // Votes neu berechnen
    $upCount = \App\Models\FeedbackVote::where('feedback_id', $id)->where('upvote', true)->count();
    $downCount = \App\Models\FeedbackVote::where('feedback_id', $id)->where('upvote', false)->count();

    \App\Models\Feedback::where('id', $id)->update([
        'votes' => $upCount - $downCount,
    ]);
}


public function render()
{
    $customerId = Auth::guard('customer')->id();

    $feedbacks = Feedback::with(['customer'])
        ->orderByDesc('votes')
        ->latest()
        ->get()
        ->map(function ($fb) use ($customerId) {
            $vote = \App\Models\FeedbackVote::where('feedback_id', $fb->id)
                ->where('customer_id', $customerId)
                ->first();
            $fb->user_vote = $vote?->upvote; // true = up, false = down, null = keine Stimme
            return $fb;
        });

    return view('livewire.backend.customer.feedback-board', [
        'feedbacks' => $feedbacks,
    ])
    ->extends('backend.customer.layouts.app')
    ->section('content');
}

}