<?php

namespace App\Livewire\Backend;

use App\Models\PortfolioItem;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortfolioManager extends Component
{
    use WithFileUploads;

    public $items;

    public $itemId = null;

    public string $title = '';

    public string $summary = '';

    public string $description = '';

    public string $category = 'app';

    public string $type = 'project';

    public string $cta_link = '';

    public array $badges = [];

    public string $badgeInput = '';

    public $cover_image;

    public $newImage;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:180',
            'description' => 'nullable|string',
            'category' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'cta_link' => 'nullable|url|max:500',
            'badges' => 'nullable|array',
            'newImage' => 'nullable|image|max:4096',
        ];
    }

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadItems(): void
    {
        $this->items = PortfolioItem::query()
            ->latest()
            ->get();
    }

    public function save(): void
    {
        $this->syncBadgesFromInput();
        $this->validate();

        $path = $this->cover_image;

        if ($this->newImage) {
            $path = $this->newImage->store('portfolio', 'public');
        }

        PortfolioItem::updateOrCreate(
            ['id' => $this->itemId],
            [
                'title' => $this->title,
                'slug' => $this->itemId ? null : Str::slug($this->title) . '-' . uniqid(),
                'summary' => $this->summary,
                'description' => $this->description,
                'category' => $this->category,
                'type' => $this->type,
                'cta_link' => $this->cta_link ?: null,
                'badges' => array_values(array_filter($this->badges)),
                'cover_image' => $path,
            ],
        );

        session()->flash('success', 'Portfolio-Eintrag wurde gespeichert.');

        $this->resetForm();
        $this->loadItems();
    }

    public function edit($id): void
    {
        $item = PortfolioItem::findOrFail($id);

        $this->itemId = $item->id;
        $this->title = (string) $item->title;
        $this->summary = (string) $item->summary;
        $this->description = (string) $item->description;
        $this->category = (string) $item->category;
        $this->type = (string) ($item->type ?: 'project');
        $this->cta_link = (string) $item->cta_link;
        $this->badges = $item->badges ?? [];
        $this->badgeInput = implode(', ', $this->badges);
        $this->cover_image = $item->cover_image;
        $this->newImage = null;
    }

    public function duplicate($id): void
    {
        $item = PortfolioItem::findOrFail($id);

        $copy = $item->replicate();
        $copy->title = $item->title . ' Kopie';
        $copy->slug = Str::slug($copy->title) . '-' . uniqid();
        $copy->save();

        session()->flash('success', 'Portfolio-Eintrag wurde dupliziert.');
        $this->loadItems();
    }

    public function delete($id): void
    {
        PortfolioItem::findOrFail($id)->delete();

        session()->flash('success', 'Portfolio-Eintrag wurde gelöscht.');
        $this->loadItems();
    }

    public function resetForm(): void
    {
        $this->reset([
            'itemId',
            'title',
            'summary',
            'description',
            'category',
            'type',
            'cta_link',
            'badges',
            'badgeInput',
            'newImage',
            'cover_image',
        ]);

        $this->category = 'app';
        $this->type = 'project';
        $this->badges = [];
    }

    public function syncBadgesFromInput(): void
    {
        $this->badges = collect(explode(',', $this->badgeInput))
            ->map(fn ($badge) => trim($badge))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.backend.portfolio-manager')
            ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
