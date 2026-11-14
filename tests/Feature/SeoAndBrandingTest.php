<?php

namespace Tests\Feature;

use App\Http\Controllers\RobotsController;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_brand_assets_exist_and_are_valid(): void
    {
        foreach ([
            'favicon.ico' => null, 'favicon.svg' => null, 'apple-touch-icon.png' => [180, 180],
            'brand/icon-192.png' => [192, 192], 'brand/icon-512.png' => [512, 512],
            'brand/icon-512-maskable.png' => [512, 512], 'brand/og-image.png' => [1200, 630],
            'brand/logo.svg' => null, 'brand/logo-mark.svg' => null,
        ] as $file => $size) {
            $path = public_path($file);
            $this->assertFileExists($path);
            $this->assertGreaterThan(100, filesize($path), "$file ist leer");
            if ($size) {
                $this->assertSame($size, array_slice(getimagesize($path), 0, 2), "$file falsche Größe");
            }
        }

        // ICO-Header: reserviert 0, Typ 1, mindestens 2 Größen
        $header = unpack('vreserved/vtype/vcount', file_get_contents(public_path('favicon.ico'), false, null, 0, 6));
        $this->assertSame([0, 1], [$header['reserved'], $header['type']]);
        $this->assertGreaterThanOrEqual(2, $header['count']);
    }

    public function test_web_manifest_is_valid(): void
    {
        $manifest = json_decode(file_get_contents(public_path('site.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('RuddatTech', $manifest['short_name']);
        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_head_contains_social_and_icon_tags(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            '<meta property="og:image" content="' . asset('brand/og-image.png') . '">',
            '<meta property="og:image:width" content="1200">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<link rel="apple-touch-icon" href="' . asset('apple-touch-icon.png') . '">',
            '<link rel="manifest" href="' . asset('site.webmanifest') . '">',
            'type="image/svg+xml"',
            'max-image-preview:large',
            'hreflang="de"',
            'href="' . route('llms') . '"',
        ] as $needle) {
            $this->assertStringContainsString($needle, $html);
        }
    }

    public function test_noindex_pages_keep_noindex(): void
    {
        $this->get(route('impressum'))->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_structured_data_has_single_organization_and_valid_references(): void
    {
        foreach (['/', '/leistungen/softwarewartung', '/ueber-ruddattech', '/referenzen'] as $url) {
            $html = $this->get($url)->getContent();
            preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);

            $nodes = collect($m[1])->flatMap(function ($json) {
                $data = json_decode(trim($json), true, flags: JSON_THROW_ON_ERROR);

                return $data['@graph'] ?? [$data];
            });

            $orgs = $nodes->where('@type', 'ProfessionalService');
            $this->assertCount(1, $orgs, "Genau eine Organisation auf $url");
            $this->assertSame(route('home') . '#organization', $orgs->first()['@id']);
            $this->assertSame(1, $nodes->where('@type', 'WebSite')->count(), $url);
        }
    }

    public function test_case_study_uses_cover_as_og_image(): void
    {
        $item = PortfolioItem::create([
            'title' => 'OG-Test', 'summary' => 'Kurz', 'cover_image' => '/storage/portfolio/x.webp',
            'cover_alt' => 'Ansicht', 'is_published' => true,
        ]);

        $this->get(route('referenzen.show', $item))
            ->assertSee('<meta property="og:image" content="' . url('/storage/portfolio/x.webp') . '">', false)
            ->assertSee('<meta property="og:image:alt" content="Ansicht">', false)
            ->assertSee('<meta property="og:type" content="article">', false);
    }

    public function test_llms_txt_describes_services_without_unpublished_references(): void
    {
        PortfolioItem::create(['title' => 'Öffentliche Referenz', 'summary' => 'Sichtbar', 'is_published' => true]);
        PortfolioItem::create(['title' => 'Geheimer Entwurf', 'summary' => 'Intern', 'is_published' => false]);

        $text = $this->get('/llms.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->getContent();

        $this->assertStringStartsWith('# RuddatTech', $text);
        $this->assertStringContainsString('> RuddatTech ist', $text);
        $this->assertStringContainsString(route('leistungen.show', 'managed-application-services'), $text);
        $this->assertStringContainsString('keine pauschale 24/7-Zusage', $text);
        $this->assertStringContainsString('Öffentliche Referenz', $text);
        $this->assertStringNotContainsString('Geheimer Entwurf', $text);
        $this->assertStringContainsString(route('llms.full'), $text);
    }

    public function test_llms_full_contains_detailed_service_texts(): void
    {
        $this->get('/llms-full.txt')
            ->assertOk()
            ->assertSee('### Professionelle Softwarewartung')
            ->assertSee('Leistungsumfang:')
            ->assertSee('## Ablauf einer Zusammenarbeit');
    }

    public function test_robots_in_production_welcomes_ai_crawlers(): void
    {
        $this->app['env'] = 'production';

        $text = $this->get('/robots.txt')->getContent();

        foreach (RobotsController::AI_CRAWLERS as $bot) {
            $this->assertStringContainsString('User-agent: ' . $bot, $text);
        }
        $this->assertStringContainsString(route('llms'), $text);
        $this->assertStringContainsString('Disallow: /admin/', $text);
        $this->assertStringContainsString('Sitemap: ' . route('sitemap'), $text);
    }

    public function test_indexnow_key_file_only_with_configured_key(): void
    {
        $this->get('/abcdef123456.txt')->assertNotFound();

        config(['services.indexnow.key' => 'abcdef123456']);
        $this->get('/abcdef123456.txt')->assertOk()->assertSee('abcdef123456');
        $this->get('/falscherschluessel.txt')->assertNotFound();

        // robots.txt und llms.txt werden nicht von der Schlüssel-Route abgefangen
        $this->get('/robots.txt')->assertOk()->assertSee('User-agent');
        $this->get('/llms.txt')->assertOk()->assertSee('# RuddatTech');
    }

    public function test_indexnow_command_dry_run_sends_nothing(): void
    {
        \Illuminate\Support\Facades\Http::fake();
        config(['services.indexnow.key' => 'abcdef123456']);

        $this->artisan('seo:indexnow --dry-run')->assertSuccessful();
        \Illuminate\Support\Facades\Http::assertNothingSent();

        config(['services.indexnow.key' => null]);
        $this->artisan('seo:indexnow')->assertFailed();
    }
}
