<?php

namespace App\Livewire\Public;

use App\Models\ProjectCard;
use App\Models\ProjectShare;
use Livewire\Component;

class ProjectShareShow extends Component
{
    public ProjectShare $share;

    public string $visitorName = '';
    public array $commentText = [];

    public function mount(string $token): void
    {
        $share = ProjectShare::query()
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if ($share->isExpired()) {
            abort(403, 'Dieser Freigabelink ist abgelaufen.');
        }

        $share->update([
            'last_opened_at' => now(),
        ]);

        $this->share = $share;
    }

    public function addComment(int $cardId): void
    {
        if (! in_array($this->share->permission, ['comment', 'upload', 'approve'], true)) {
            abort(403);
        }

        $this->validate([
            'visitorName' => ['required', 'string', 'max:255'],
            "commentText.$cardId" => ['required', 'string', 'max:2000'],
        ], [
            'visitorName.required' => 'Bitte Namen eintragen.',
            "commentText.$cardId.required" => 'Bitte Kommentar eintragen.',
        ]);

        $board = $this->share->shareable;

        $card = ProjectCard::query()
            ->where('project_board_id', $board->id)
            ->findOrFail($cardId);

        $card->comments()->create([
            'author_name' => $this->visitorName,
            'comment' => $this->commentText[$cardId],
        ]);

        $this->commentText[$cardId] = '';

        session()->flash('success', 'Kommentar wurde gespeichert.');
    }

    public function approveCard(int $cardId): void
    {
        if ($this->share->permission !== 'approve') {
            abort(403);
        }

        $board = $this->share->shareable;

        $card = ProjectCard::query()
            ->where('project_board_id', $board->id)
            ->findOrFail($cardId);

        $card->update([
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        session()->flash('success', 'Karte wurde freigegeben.');
    }

    public function render()
    {
        $board = $this->share->shareable()
            ->with([
                'lists.cards.comments' => fn ($query) => $query->latest(),
            ])
            ->firstOrFail();

        return view('livewire.public.project-share-show', [
            'board' => $board,
            'canComment' => in_array($this->share->permission, ['comment', 'upload', 'approve'], true),
            'canApprove' => $this->share->permission === 'approve',
        ]) ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
