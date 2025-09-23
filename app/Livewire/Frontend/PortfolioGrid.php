<?php

namespace App\Livewire\Frontend;

use App\Models\PortfolioItem;
use Livewire\Component;

class PortfolioGrid extends Component
{
    public string $category = 'all';

    protected $queryString = ['category'];

    public function filterBy(string $category)
    {
        $this->category = $category;
    }

public function render()
{
    $query = PortfolioItem::with('images');

    if ($this->category !== 'all') {
        $query->where('category', $this->category);
    }

    $items = $query->get();

    // vorhandene Kategorien aus DB
    $categories = PortfolioItem::select('category')
        ->distinct()
        ->pluck('category')
        ->toArray();

    // Mapping für Labels
    $labelMap = [
        'app'      => 'Apps',
        'product'  => 'Produkte',
        'branding' => 'Branding',
        'books'    => 'Books',
    ];

    return view('livewire.frontend.portfolio-grid', [
        'items' => $items,
        'categories' => $categories,
        'labelMap' => $labelMap,
    ]);
}

}
