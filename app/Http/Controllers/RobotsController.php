<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt mit absoluter Sitemap-URL der jeweiligen Umgebung.
 * Außerhalb der Produktion wird die Indexierung komplett untersagt.
 */
class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = app()->isProduction()
            ? [
                'User-agent: *',
                'Disallow: /admin/',
                'Disallow: /customer/',
                'Disallow: /share/',
                'Disallow: /livewire/',
                'Disallow: /intake-form',
                '',
                'Sitemap: ' . route('sitemap'),
            ]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
