<?php

namespace App\Livewire\Admin\ProjectHub;

use App\Models\ProjectBoard;
use App\Models\ProjectCard;
use App\Models\ProjectList;
use App\Models\ProjectShare;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('backend.layouts.backend')]
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
    public string $editingComment = '';

    public ?string $sharePermission = 'comment';
    public ?string $shareExpiresAt = null;

    // Board editing
    public bool $showEditBoardModal = false;
    public string $editBoardTitle = '';
    public ?string $editBoardDescription = null;
    public ?string $editBoardClientName = null;
    public ?string $editBoardClientEmail = null;

    // List editing
    public bool $showEditListModal = false;
    public ?int $editingListId = null;
    public string $editingListTitle = '';

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
        $this->editingComment = '';
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
            'editingComment',
        ]);

        $this->editingPriority = 'normal';
    }

    public function addComment(): void
    {
        $this->validate([
            'editingComment' => ['required', 'string', 'max:2000'],
        ]);

        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($this->editingCardId);

        $card->comments()->create([
            'author_name' => 'Admin',
            'comment' => $this->editingComment,
        ]);

        $this->editingComment = '';
        session()->flash('success', 'Kommentar wurde gespeichert.');
    }

    public function approveCard(): void
    {
        $card = ProjectCard::where('project_board_id', $this->board->id)
            ->findOrFail($this->editingCardId);

        $card->update([
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        session()->flash('success', 'Karte wurde freigegeben.');
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

    // --- Board editing ---

    public function openEditBoard(): void
    {
        $this->editBoardTitle = $this->board->title;
        $this->editBoardDescription = $this->board->description;
        $this->editBoardClientName = $this->board->client_name;
        $this->editBoardClientEmail = $this->board->client_email;
        $this->showEditBoardModal = true;
    }

    public function saveBoard(): void
    {
        $this->validate([
            'editBoardTitle' => ['required', 'string', 'max:255'],
            'editBoardDescription' => ['nullable', 'string'],
            'editBoardClientName' => ['nullable', 'string', 'max:255'],
            'editBoardClientEmail' => ['nullable', 'email', 'max:255'],
        ]);

        $this->board->update([
            'title' => $this->editBoardTitle,
            'description' => $this->editBoardDescription,
            'client_name' => $this->editBoardClientName,
            'client_email' => $this->editBoardClientEmail,
        ]);

        $this->showEditBoardModal = false;
        session()->flash('success', 'Board wurde aktualisiert.');
    }

    public function deleteBoard(): void
    {
        $this->board->delete();
        $this->redirectRoute('admin.projecthub.index', navigate: true);
    }

    // --- List editing / deletion ---

    public function openEditList(int $listId): void
    {
        $list = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($listId);

        $this->editingListId = $list->id;
        $this->editingListTitle = $list->title;
        $this->showEditListModal = true;
    }

    public function saveList(): void
    {
        $this->validate([
            'editingListTitle' => ['required', 'string', 'max:255'],
        ]);

        $list = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($this->editingListId);

        $list->update([
            'title' => $this->editingListTitle,
        ]);

        $this->showEditListModal = false;
        $this->reset(['editingListId', 'editingListTitle']);
        session()->flash('success', 'Liste wurde aktualisiert.');
    }

    public function deleteList(int $listId): void
    {
        $list = ProjectList::where('project_board_id', $this->board->id)
            ->findOrFail($listId);

        $list->cards()->delete();
        $list->delete();

        session()->flash('success', 'Liste wurde gelöscht.');
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
