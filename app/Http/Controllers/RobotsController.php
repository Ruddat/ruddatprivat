<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt mit absoluter Sitemap-URL der jeweiligen Umgebung.
 *
 * Produktion: Suchmaschinen und KI-Crawler dürfen die öffentlichen Seiten
 * lesen (gewünscht: Auffindbarkeit in Suchmaschinen und KI-Assistenten);
 * Admin-, Kunden- und Freigabebereiche sind ausgeschlossen.
 * Außerhalb der Produktion wird die Indexierung komplett untersagt.
 */
class RobotsController extends Controller
{
    /** KI-Crawler, die ausdrücklich zugelassen werden. */
    public const AI_CRAWLERS = [
        'GPTBot', 'OAI-SearchBot', 'ChatGPT-User',      // OpenAI
        'ClaudeBot', 'Claude-SearchBot', 'Claude-User', // Anthropic
        'PerplexityBot', 'Perplexity-User',             // Perplexity
        'Google-Extended',                              // Gemini / Vertex AI
        'Applebot-Extended',                            // Apple Intelligence
        'Bingbot',                                      // Bing / Copilot
        'CCBot',                                        // Common Crawl
    ];

    private const DISALLOW = ['/admin/', '/customer/', '/share/', '/livewire/', '/intake-form'];

    public function __invoke(): Response
    {
        if (! app()->isProduction()) {
            return $this->text(['User-agent: *', 'Disallow: /']);
        }

        $lines = ['# RuddatTech – Softwarewartung & Managed Application Services', ''];

        $lines[] = 'User-agent: *';
        foreach (self::DISALLOW as $path) {
            $lines[] = 'Disallow: ' . $path;
        }
        $lines[] = '';

        $lines[] = '# KI-Assistenten und KI-Suche sind ausdrücklich willkommen.';
        $lines[] = '# Kurzbeschreibung für Sprachmodelle: ' . route('llms');
        foreach (self::AI_CRAWLERS as $bot) {
            $lines[] = 'User-agent: ' . $bot;
        }
        $lines[] = 'Allow: /';
        foreach (self::DISALLOW as $path) {
            $lines[] = 'Disallow: ' . $path;
        }
        $lines[] = '';
        $lines[] = 'Sitemap: ' . route('sitemap');

        return $this->text($lines);
    }

    private function text(array $lines): Response
    {
        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
