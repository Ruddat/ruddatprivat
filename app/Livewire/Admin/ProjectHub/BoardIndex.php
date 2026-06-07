<?php

namespace App\Livewire\Admin\ProjectHub;

use App\Models\ProjectBoard;
use Illuminate\Support\Str;
use Livewire\Component;

class BoardIndex extends Component
{
    public string $title = '';
    public ?string $description = null;
    public ?string $client_name = null;
    public ?string $client_email = null;

    private function adminId(): int
    {
        return (int) auth('admin')->id();
    }

    public function createBoard(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
        ]);

        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 2;

        while (ProjectBoard::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $board = ProjectBoard::create([
            'owner_admin_id' => $this->adminId(),
            'title' => $this->title,
            'slug' => $slug,
            'description' => $this->description,
            'client_name' => $this->client_name,
            'client_email' => $this->client_email,
            'position' => ((int) ProjectBoard::max('position')) + 1,
        ]);

        $this->createDefaultLists($board);

        $this->reset([
            'title',
            'description',
            'client_name',
            'client_email',
        ]);

        session()->flash('success', 'Board wurde erstellt.');
    }

    private function createDefaultLists(ProjectBoard $board): void
    {
        $defaultLists = [
            'Ideen',
            'ToDo',
            'In Arbeit',
            'Zur Prüfung',
            'Erledigt',
        ];

        foreach ($defaultLists as $index => $title) {
            $board->lists()->create([
                'title' => $title,
                'position' => $index + 1,
                'is_done_list' => $title === 'Erledigt',
            ]);
        }
    }

    public function toggleBoard(int $boardId): void
    {
        $board = ProjectBoard::query()
            ->where('owner_admin_id', $this->adminId())
            ->findOrFail($boardId);

        $board->update([
            'is_active' => ! $board->is_active,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.project-hub.board-index', [
            'boards' => ProjectBoard::query()
                ->where('owner_admin_id', $this->adminId())
                ->withCount(['lists', 'cards'])
                ->orderBy('position')
                ->latest()
                ->get(),
        ]);
    }
}
