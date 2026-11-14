<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Support\Site\SiteContent;
use Illuminate\Http\Response;

/**
 * llms.txt – kompakte, maschinenlesbare Beschreibung für KI-Assistenten
 * (Vorschlag llmstxt.org). /llms-full.txt enthält zusätzlich die
 * vollständigen Leistungstexte und Fallstudien.
 *
 * Inhalte stammen aus denselben Quellen wie die Webseite, damit KI-Systeme
 * keine abweichenden Aussagen erhalten. Nur veröffentlichte Referenzen.
 */
class LlmsController extends Controller
{
    public function index(): Response
    {
        return $this->markdown($this->build(full: false));
    }

    public function full(): Response
    {
        return $this->markdown($this->build(full: true));
    }

    private function build(bool $full): string
    {
        $legal = config('ruddattech.legal');
        $lines = [];

        $lines[] = '# RuddatTech';
        $lines[] = '';
        $lines[] = '> RuddatTech ist der technische Dienstleister von ' . $legal['owner'] . ' (Deutschland) für die Wartung, Stabilisierung, technische Übernahme und langfristige Betreuung individueller Geschäftsanwendungen – mit Schwerpunkt auf PHP- und Laravel-Webanwendungen. Zielgruppe sind Unternehmen und Betreiber produktiver Software.';
        $lines[] = '';
        $lines[] = 'Wichtige Einordnung:';
        $lines[] = '';
        $lines[] = '- Leistungen werden individuell auf Basis einer technischen Bestandsaufnahme vereinbart. Es gibt keine öffentlichen Pauschalpakete oder Preislisten.';
        $lines[] = '- Wartung, Betriebsverantwortung und Verfügbarkeitsleistungen sind getrennte Leistungsebenen. Reaktionszeiten gelten nur, wenn sie vertraglich vereinbart sind; es gibt keine pauschale 24/7-Zusage.';
        $lines[] = '- RuddatTech ist kein Hosting-Anbieter und kein Wartungsdienst für beliebige private Webseiten.';
        $lines[] = '- Ansprechpartner ist direkt die Person, die an der Anwendung arbeitet.';
        $lines[] = '';

        $lines[] = '## Leistungen';
        $lines[] = '';
        foreach (SiteContent::services() as $slug => $service) {
            $lines[] = '- [' . $service['title'] . '](' . route('leistungen.show', $slug) . '): ' . $service['card_text'];
        }
        $lines[] = '';

        if ($full) {
            foreach (SiteContent::services() as $slug => $service) {
                $lines[] = '### ' . $service['title'];
                $lines[] = '';
                $lines[] = $service['intro'];
                $lines[] = '';
                $lines[] = 'Typische Ausgangslagen:';
                foreach ($service['situation'] as $item) {
                    $lines[] = '- ' . $item;
                }
                $lines[] = '';
                $lines[] = 'Leistungsumfang:';
                foreach ($service['scope'] as $item) {
                    $lines[] = '- **' . $item['title'] . ':** ' . $item['text'];
                }
                $lines[] = '';
                foreach ($service['faq'] as $faq) {
                    $lines[] = '**' . $faq['q'] . '** ' . $faq['a'];
                    $lines[] = '';
                }
            }

            $lines[] = '## Ablauf einer Zusammenarbeit';
            $lines[] = '';
            foreach (SiteContent::process() as $i => $step) {
                $lines[] = ($i + 1) . '. **' . $step['title'] . ':** ' . $step['text'];
            }
            $lines[] = '';
        }

        $references = PortfolioItem::published()->ordered()->get();
        if ($references->isNotEmpty()) {
            $lines[] = '## Referenzen (technische Fallstudien)';
            $lines[] = '';
            foreach ($references as $reference) {
                $lines[] = '- [' . $reference->title . '](' . route('referenzen.show', $reference) . ')' . ($reference->summary ? ': ' . $reference->summary : '');
                if ($full) {
                    $labels = $reference->complexityLabels();
                    if ($labels) {
                        $lines[] = '  - Komplexität: ' . implode(', ', $labels);
                    }
                    if ($reference->project_status) {
                        $lines[] = '  - Status: ' . $reference->project_status;
                    }
                    if ($reference->role) {
                        $lines[] = '  - Rolle von RuddatTech: ' . $reference->role;
                    }
                }
            }
            $lines[] = '';
        }

        $lines[] = '## Seiten';
        $lines[] = '';
        $lines[] = '- [Startseite](' . route('home') . ')';
        $lines[] = '- [Leistungen](' . route('leistungen.index') . ')';
        $lines[] = '- [Referenzen](' . route('referenzen.index') . ')';
        $lines[] = '- [Über RuddatTech](' . route('about') . ')';
        $lines[] = '- [Kontakt und Anfrage](' . route('kontakt') . ')';
        $lines[] = '';

        $lines[] = '## Kontakt';
        $lines[] = '';
        $lines[] = '- Anfrageformular: ' . route('kontakt');
        $lines[] = '- E-Mail: ' . $legal['email'];
        $lines[] = '';

        $lines[] = '## Optional';
        $lines[] = '';
        if (! $full) {
            $lines[] = '- [Vollständige Fassung mit allen Leistungstexten](' . route('llms.full') . ')';
        }
        $lines[] = '- [Impressum](' . route('impressum') . ')';
        $lines[] = '- [Datenschutzerklärung](' . route('datenschutz') . ')';
        $lines[] = '- [Sitemap](' . route('sitemap') . ')';

        return implode("\n", $lines) . "\n";
    }

    private function markdown(string $content): Response
    {
        return response($content, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
