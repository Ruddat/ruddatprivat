<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\PortfolioItem;
use App\Support\Site\SiteContent;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $add = function (string $loc, string $changefreq, string $priority, $lastmod = null) use (&$urls) {
            $urls[] = [
                'loc' => $loc,
                'lastmod' => ($lastmod ?? now())->toAtomString(),
                'changefreq' => $changefreq,
                'priority' => $priority,
            ];
        };

        $add(route('home'), 'weekly', '1.0');
        $add(route('leistungen.index'), 'monthly', '0.9');

        foreach (array_keys(SiteContent::services()) as $slug) {
            $add(route('leistungen.show', $slug), 'monthly', '0.9');
        }

        $add(route('referenzen.index'), 'monthly', '0.8');

        // Nur veröffentlichte Referenzen
        PortfolioItem::published()->ordered()->get(['slug', 'updated_at'])
            ->each(fn ($item) => $add(route('referenzen.show', $item->slug), 'monthly', '0.7', $item->updated_at));

        $add(route('about'), 'yearly', '0.6');
        $add(route('kontakt'), 'yearly', '0.7');

        LandingPage::where('published', true)->get(['slug', 'updated_at'])
            ->each(fn ($page) => $add(url('/lp/' . $page->slug), 'monthly', '0.5', $page->updated_at));

        $add(route('impressum'), 'yearly', '0.2');
        $add(route('datenschutz'), 'yearly', '0.2');

        return response()
            ->view('sitemap.index', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
