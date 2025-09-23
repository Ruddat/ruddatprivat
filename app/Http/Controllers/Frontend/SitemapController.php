<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;

class SitemapController extends Controller
{
    public function index()
    {
        $pages = LandingPage::where('published', true)->get();

        $content = view('sitemap.index', compact('pages'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
