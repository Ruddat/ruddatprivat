<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PortfolioItem;

class PortfolioController extends Controller
{
    public function show(PortfolioItem $portfolioItem)
    {
        $portfolioItem->load('images');

        return view('frontend.portfolio.show', compact('portfolioItem'));
    }
}
