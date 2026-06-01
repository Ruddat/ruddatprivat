<?php

namespace App\Livewire\Backend;

use App\Models\PortfolioItem;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PortfolioEditor extends Component
{
    use WithPagination;
    use WithFileUploads;

    public ?int $editingId = null;

    public bool $showForm = false;

    public string $title = '';
    public string $category = '';
    public string $summary = '';
    public string $description = '';
    public string $cover_image = '';
    public string $badges_input = '';
    public string $type = 'project';
    public string $cta_link = '';

    public $cover_upload = null;

    public string $search = '';
    public string $filterCategory = '';
    public string $filterType = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:190'],
            'category' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'badges_input' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:80'],
            'cta_link' => ['nullable', 'string', 'max:500'],

            'cover_upload' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $badges = collect(explode(',', $this->badges_input))
            ->map(fn ($badge) => trim($badge))
            ->filter()
            ->values()
            ->toArray();

        $coverPath = $this->cover_image;

        if ($this->cover_upload) {
            $coverPath = $this->cover_upload->store('portfolio/covers', 'public');
            $coverPath = '/storage/' . $coverPath;
        }

        PortfolioItem::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title' => $this->title,
                'category' => $this->category,
                'summary' => $this->summary,
                'description' => $this->description,
                'cover_image' => $coverPath,
                'badges' => $badges,
                'type' => $this->type ?: 'project',
                'cta_link' => $this->cta_link,
            ]
        );

        $this->resetForm();
        $this->showForm = false;

        session()->flash('success', 'Portfolio-Eintrag wurde gespeichert.');
    }

    public function edit(int $id): void
    {
        $item = PortfolioItem::findOrFail($id);

        $this->editingId = $item->id;
        $this->title = $item->title ?? '';
        $this->category = $item->category ?? '';
        $this->summary = $item->summary ?? '';
        $this->description = $item->description ?? '';
        $this->cover_image = $item->cover_image ?? '';
        $this->badges_input = implode(', ', $item->badges ?? []);
        $this->type = $item->type ?? 'project';
        $this->cta_link = $item->cta_link ?? '';
        $this->cover_upload = null;

        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        PortfolioItem::findOrFail($id)->delete();

        if ($this->editingId === $id) {
            $this->resetForm();
            $this->showForm = false;
        }

        session()->flash('success', 'Portfolio-Eintrag wurde gelöscht.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm(): void
    {
        $this->editingId = null;

        $this->reset([
            'title',
            'category',
            'summary',
            'description',
            'cover_image',
            'badges_input',
            'cta_link',
            'cover_upload',
        ]);

        $this->type = 'project';

        $this->resetValidation();
    }

    public function updatedCoverUpload(): void
    {
        $this->validateOnly('cover_upload');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function getItemsProperty()
    {
        return PortfolioItem::query()
            ->with('images')
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('summary', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, fn ($query) => $query->where('category', $this->filterCategory))
            ->when($this->filterType, fn ($query) => $query->where('type', $this->filterType))
            ->latest()
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.backend.portfolio-editor', [
            'items' => $this->items,
            'categories' => PortfolioItem::query()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'types' => PortfolioItem::query()
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->distinct()
                ->orderBy('type')
                ->pluck('type'),
        ])->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
