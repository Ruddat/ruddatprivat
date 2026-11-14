<?php

namespace App\Console\Commands;

use App\Models\PortfolioItem;
use App\Support\Site\SiteContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Meldet die öffentlichen URLs per IndexNow (Bing, Yandex u. a.; Bing speist u. a.
 * Copilot und die ChatGPT-Suche). Läuft nur in der Produktion und nur manuell.
 *
 * Einrichtung: INDEXNOW_KEY in der .env setzen (8–128 Zeichen a-z, A-Z, 0-9, -).
 * Die Schlüsseldatei wird automatisch unter /{key}.txt ausgeliefert.
 */
class SubmitIndexNow extends Command
{
    protected $signature = 'seo:indexnow {--dry-run : Nur anzeigen, nichts senden}';

    protected $description = 'Öffentliche Seiten per IndexNow an Suchmaschinen melden';

    public function handle(): int
    {
        $key = (string) config('services.indexnow.key');

        if (! preg_match('/^[a-zA-Z0-9-]{8,128}$/', $key)) {
            $this->error('INDEXNOW_KEY fehlt oder ist ungültig.');

            return self::FAILURE;
        }

        $urls = $this->urls();
        $this->info(count($urls) . ' URLs:');
        foreach ($urls as $url) {
            $this->line('  ' . $url);
        }

        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }

        if (! app()->isProduction()) {
            $this->warn('Nicht in der Produktion – es wird nichts gesendet.');

            return self::SUCCESS;
        }

        $response = Http::timeout(20)->asJson()->post('https://api.indexnow.org/indexnow', [
            'host' => parse_url(config('app.url'), PHP_URL_HOST),
            'key' => $key,
            'keyLocation' => url($key . '.txt'),
            'urlList' => $urls,
        ]);

        $this->info('Antwort: HTTP ' . $response->status());

        return $response->successful() ? self::SUCCESS : self::FAILURE;
    }

    private function urls(): array
    {
        $urls = [route('home'), route('leistungen.index'), route('referenzen.index'), route('about'), route('kontakt')];

        foreach (array_keys(SiteContent::services()) as $slug) {
            $urls[] = route('leistungen.show', $slug);
        }

        foreach (PortfolioItem::published()->pluck('slug') as $slug) {
            $urls[] = route('referenzen.show', $slug);
        }

        return $urls;
    }
}
