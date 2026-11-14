<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;

class HomeController extends Controller
{
    public function index()
    {
        $featuredReferences = PortfolioItem::published()
            ->where('is_featured', true)
            ->ordered()
            ->limit(3)
            ->get();

        return view('site.home', compact('featuredReferences'));
    }
}
