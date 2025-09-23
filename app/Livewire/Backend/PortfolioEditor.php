<?php

namespace App\Livewire\Backend;

use App\Models\PortfolioImage;
use App\Models\PortfolioItem;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortfolioEditor extends Component
{
    use WithFileUploads;

    public $items;

    public $itemId;

    public $title;

    public $category = 'app';

    public $summary;

    public $description;

    public $badges = [];

    public $coverImage;

    public $newCover;

    public $gallery = [];

    public $newGallery = [];

public $type = 'showcase';
public $cta_link;


protected $rules = [
    'title' => 'required|string|max:255',
    'category' => 'required|in:all,app,product,branding,books,web,kundenprojekt,marketplace,travel',
    'summary' => 'nullable|string|max:500',
    'description' => 'nullable|string',
    'badges' => 'nullable|array',
    'newCover' => 'nullable|image|max:4048',
    'newGallery.*' => 'nullable|image|max:12048',
    'type' => 'required|in:showcase,module',
    'cta_link' => 'nullable|string|max:500',
];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->items = PortfolioItem::with('images')->get();
    }

    public function save()
    {
        $this->validate();

        // Cover-Path korrekt setzen
        $coverPath = $this->coverImage ?? null;
        if ($this->newCover) {
            $coverPath = $this->newCover->store('portfolio/covers', 'public');
        }

$item = PortfolioItem::updateOrCreate(
    ['id' => $this->itemId],
    [
        'title' => $this->title,
        'category' => $this->category,
        'summary' => $this->summary,
        'description' => $this->description,
        'badges' => is_array($this->badges) ? $this->badges : [],
        'cover_image' => $coverPath,
        'type' => $this->type,
        'cta_link' => $this->cta_link,
    ],
);

// Galerie speichern – nur neue Bilder
if (!empty($this->newGallery)) {
    foreach ($this->newGallery as $image) {
        $path = $image->store('portfolio/gallery', 'public');
        PortfolioImage::create([
            'portfolio_item_id' => $item->id,
            'path' => $path,
        ]);
    }
    $this->newGallery = []; // nach dem Speichern zurücksetzen
}

        // Nur resetten, wenn ein neues Item angelegt wurde
        if (! $this->itemId) {
            $this->resetForm();
        } else {
            // Wenn Bearbeiten → Item gleich wieder laden
            $this->edit($item->id);
        }

        $this->loadItems();
    }

    public function edit($id)
    {
        $item = PortfolioItem::with('images')->findOrFail($id);
        $this->itemId = $item->id;
        $this->title = $item->title;
        $this->category = $item->category;
        $this->summary = $item->summary;
        $this->description = $item->description;
        $this->badges = $item->badges ?? [];
        $this->coverImage = $item->cover_image;
        $this->gallery = $item->images->toArray();
        $this->type = $item->type ?? 'showcase';
        $this->cta_link = $item->cta_link;
    }

    public function deleteImage($id)
    {
        $image = PortfolioImage::findOrFail($id);

        // Datei löschen
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        // Galerie aktualisieren
        $this->gallery = PortfolioImage::where('portfolio_item_id', $this->itemId)->get()->toArray();
    }

    public function deleteCover()
    {
        $item = PortfolioItem::findOrFail($this->itemId);

        if ($item->cover_image && Storage::disk('public')->exists($item->cover_image)) {
            Storage::disk('public')->delete($item->cover_image);
        }

        $item->update(['cover_image' => null]);
        $this->coverImage = null;
    }


public function deleteItem($id)
{
    $item = PortfolioItem::with('images')->findOrFail($id);

    // Cover löschen
    if ($item->cover_image && Storage::disk('public')->exists($item->cover_image)) {
        Storage::disk('public')->delete($item->cover_image);
    }

    // Galerie löschen
    foreach ($item->images as $image) {
        if ($image->path && Storage::disk('public')->exists($image->path)) {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();
    }

    // Item löschen
    $item->delete();

    // Items neu laden
    $this->loadItems();
}


    private function resetForm()
    {
        $this->reset([
            'itemId',
            'title',
            'category',
            'summary',
            'description',
            'badges',
            'coverImage',
            'newCover',
            'newGallery',
            'gallery',
        ]);

        $this->category = 'app'; // Default setzen
        $this->badges = [];
        $this->type = 'showcase';
        $this->cta_link = null;
    }

    public function render()
    {
        return view('livewire.backend.portfolio-editor')
            ->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
