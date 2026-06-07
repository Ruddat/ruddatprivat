<?php

namespace App\Livewire\Admin\ProjectHub;

use App\Models\ProjectBoard;
use App\Models\ProjectCard;
use App\Models\ProjectList;
use App\Models\ProjectShare;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class BoardShow extends Component
{
    use WithFileUploads;

    public ProjectBoard $board;

    public string $newListTitle = '';
    public array $newCardTitle = [];
    public array $uploads = [];

    public ?int $editingCardId = null;
    public string $editingTitle = '';
    public ?string $editingDescription = null;
    public string $editingPriority = 'normal';
    public ?string $editingDueDate = null;

    public ?string $sharePermission = 'comment';
    public ?string $shareExpiresAt = null;

    private function adminId(): int
    {
        return (int) auth('admin')->id();
    }

    public function mount(ProjectBoard $board): void
    {
        abort_unless((int) $board->owner_admin_id === $this->adminId(), 403);

        $this->board = $board;
    }

    public function createList(): void
    {
        $this->validate([
            'newListTitle' => ['required', 'string', 'max:255'],
        ]);

        $this->board->lists()->create([
            'title' => $this->newListTitle,
            'position' => ((int) $this->board->lists()->max('position')) + 1,
        ]);

        $this->reset('newListTitle');
    }

    public function createCard(int $listId): void
    {
        $title = trim($this->newCardTitle[$listId] ?? '');

        if ($title === '') {
            return;
        }

        $list = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($listId);

        ProjectCard::create([
            'project_board_id' => $this->board->id,
            'project_list_id' => $list->id,
            'title' => $title,
            'position' => ((int) $list->cards()->max('position')) + 1,
        ]);

        $this->newCardTitle[$listId] = '';
    }

    public function openCard(int $cardId): void
    {
        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($cardId);

        $this->editingCardId = $card->id;
        $this->editingTitle = $card->title;
        $this->editingDescription = $card->description;
        $this->editingPriority = $card->priority;
        $this->editingDueDate = $card->due_date?->format('Y-m-d');
    }

    public function saveCard(): void
    {
        $this->validate([
            'editingTitle' => ['required', 'string', 'max:255'],
            'editingDescription' => ['nullable', 'string'],
            'editingPriority' => ['required', 'in:low,normal,high,urgent'],
            'editingDueDate' => ['nullable', 'date'],
        ]);

        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($this->editingCardId);

        $card->update([
            'title' => $this->editingTitle,
            'description' => $this->editingDescription,
            'priority' => $this->editingPriority,
            'due_date' => $this->editingDueDate,
        ]);

        $this->closeCard();
    }

    public function closeCard(): void
    {
        $this->reset([
            'editingCardId',
            'editingTitle',
            'editingDescription',
            'editingPriority',
            'editingDueDate',
        ]);

        $this->editingPriority = 'normal';
    }

    public function moveCard(int $cardId, int $targetListId): void
    {
        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($cardId);

        $targetList = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($targetListId);

        $card->update([
            'project_list_id' => $targetList->id,
            'position' => ((int) $targetList->cards()->max('position')) + 1,
            'status' => $targetList->is_done_list ? 'done' : 'open',
        ]);
    }

    public function reorderCards(int $targetListId, array $orderedCardIds): void
    {
        $targetList = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($targetListId);

        foreach (array_values($orderedCardIds) as $index => $cardId) {
            ProjectCard::where('project_board_id', $this->board->id)
                ->whereKey((int) $cardId)
                ->update([
                    'project_list_id' => $targetList->id,
                    'position' => $index + 1,
                    'status' => $targetList->is_done_list ? 'done' : 'open',
                ]);
        }
    }

    public function uploadFiles(int $cardId): void
    {
        $this->validate([
            "uploads.$cardId.*" => ['required', 'file', 'max:10240'],
        ]);

        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($cardId);

        foreach ($this->uploads[$cardId] ?? [] as $file) {
            $path = $file->store('projecthub/attachments', 'public');

            $card->attachments()->create([
                'uploaded_by_admin_id' => $this->adminId(),
                'author_name' => 'Admin',
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        $this->uploads[$cardId] = [];
        session()->flash('success', 'Dateien wurden hochgeladen.');
    }

    public function deleteCard(int $cardId): void
    {
        ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($cardId)
            ->delete();
    }

    public function createShareLink(): void
    {
        $this->validate([
            'sharePermission' => ['required', 'in:view,comment,upload,approve'],
            'shareExpiresAt' => ['nullable', 'date'],
        ]);

        $this->board->shares()
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->board->shares()->create([
            'token' => Str::random(48),
            'permission' => $this->sharePermission,
            'expires_at' => $this->shareExpiresAt,
            'is_active' => true,
            'created_by_admin_id' => $this->adminId(),
        ]);

        session()->flash('success', 'Kundenlink wurde erstellt.');
    }

    public function disableShareLink(): void
    {
        $this->board->shares()
            ->where('is_active', true)
            ->update(['is_active' => false]);

        session()->flash('success', 'Kundenlink wurde deaktiviert.');
    }

    public function getActiveShareProperty(): ?ProjectShare
    {
        return $this->board->shares()
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    public function render()
    {
        return view('livewire.admin.project-hub.board-show', [
            'boardData' => ProjectBoard::query()
                ->with([
                    'activeShare',
                    'lists.cards' => fn ($query) => $query->orderBy('position'),
                    'lists.cards.attachments',
                    'lists.cards.comments',
                ])
                ->where('owner_admin_id', $this->adminId())
                ->findOrFail($this->board->id),
        ]);
    }
}
