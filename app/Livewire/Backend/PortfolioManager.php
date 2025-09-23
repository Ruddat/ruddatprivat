<?php

namespace App\Livewire\Backend;

use App\Models\PortfolioItem;
use Livewire\Component;
use Livewire\WithFileUploads;

class PortfolioManager extends Component
{
    use WithFileUploads;

    public $items;

    public $itemId = null;

    public $title;

    public $description;

    public $category = 'app';

    public $badges = [];

    public $image;

    public $newImage;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'required|string',
        'badges' => 'nullable|array',
        'newImage' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $this->items = PortfolioItem::all();
    }

    public function save()
    {
        $this->validate();

        $path = $this->image;
        if ($this->newImage) {
            $path = $this->newImage->store('portfolio', 'public');
        }

        PortfolioItem::updateOrCreate(
            ['id' => $this->itemId],
            [
                'title' => $this->title,
                'description' => $this->description,
                'category' => $this->category,
                'badges' => $this->badges,
                'image' => $path,
            ],
        );

        $this->resetForm();
        $this->loadItems();
    }

    public function edit($id)
    {
        $item = PortfolioItem::findOrFail($id);
        $this->itemId = $item->id;
        $this->title = $item->title;
        $this->description = $item->description;
        $this->category = $item->category;
        $this->badges = $item->badges ?? [];
        $this->image = $item->image;
    }

    public function delete($id)
    {
        PortfolioItem::findOrFail($id)->delete();
        $this->loadItems();
    }

    private function resetForm()
    {
        $this->reset(['itemId', 'title', 'description', 'category', 'badges', 'newImage', 'image']);
    }

    public function render()
    {
        return view('livewire.backend.portfolio-manager')
                    ->extends('backend.customer.layouts.app')
            ->section('content');
    }
}
