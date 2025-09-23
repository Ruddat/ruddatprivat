<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;

class LandingPageController extends Controller
{
    public function show($slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        return view('landing.' . $page->template, compact('page'));
    }
}
