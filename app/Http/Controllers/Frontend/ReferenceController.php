<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;

class ReferenceController extends Controller
{
    public function index()
    {
        $references = PortfolioItem::published()->ordered()->get();

        return view('site.references.index', compact('references'));
    }

    public function show(PortfolioItem $reference)
    {
        // Unveröffentlichte Einträge sind öffentlich nicht erreichbar.
        abort_unless($reference->is_published, 404);

        $reference->load('images');

        return view('site.references.show', compact('reference'));
    }
}
