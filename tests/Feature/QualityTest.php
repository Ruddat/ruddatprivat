<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression und Qualitätsprüfungen über die gesamte Webseite.
 */
class QualityTest extends TestCase
{
    use RefreshDatabase;

    /** Bestehende, nicht vom Relaunch betroffene Bereiche funktionieren weiter. */
    public function test_existing_public_routes_still_work(): void
    {
        $this->get('/schedule-appointment')->assertOk();
        $this->get('/intake-form')->assertOk();
        $this->get('/schedule-meeting')->assertOk();
        $this->get('/admin/login')->assertOk();
        $this->get('/customer/login')->assertOk();
        $this->get('/up')->assertOk();
        $this->get('/share/drive/gibt-es-nicht')->assertNotFound();
    }

    public function test_published_landing_pages_still_render(): void
    {
        LandingPage::create([
            'slug' => 'test-seite', 'title' => 'Testseite', 'h1' => 'Überschrift', 'template' => 'default', 'published' => true,
        ]);
        LandingPage::create([
            'slug' => 'entwurf', 'title' => 'Entwurf', 'h1' => 'Entwurf', 'template' => 'default', 'published' => false,
        ]);

        $this->get('/lp/test-seite')->assertOk()->assertSee('Überschrift');
        $this->get('/lp/entwurf')->assertNotFound();
    }

    public function test_legal_pages_use_new_layout_and_are_noindex(): void
    {
        foreach (['impressum', 'datenschutz', 'agb'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('<meta name="robots" content="noindex, follow">', false)
                ->assertSee('Zum Inhalt springen')
                ->assertDontSee('bootstrap.min.css', false);
        }
    }

    public function test_imprint_shows_configured_address_and_current_law(): void
    {
        config(['ruddattech.legal.street' => 'Musterweg 1', 'ruddattech.legal.zip_city' => '38100 Braunschweig']);

        $this->get(route('impressum'))
            ->assertSee('§ 5 DDG')
            ->assertSee('Musterweg 1')
            ->assertSee('38100 Braunschweig')
            ->assertDontSee('TMG')
            ->assertDontSee('ec.europa.eu/consumers/odr', false);
    }

    public function test_imprint_flags_missing_address_outside_production(): void
    {
        config(['ruddattech.legal.street' => null]);

        $this->get(route('impressum'))->assertSee('fehlt: LEGAL_STREET');
    }

    public function test_privacy_lists_only_active_external_services(): void
    {
        $this->get(route('datenschutz'))
            ->assertSee('Anfrageformular')
            ->assertSee('Hashwert')
            ->assertDontSee('seven communications')
            ->assertDontSee('Telegram Messenger')
            ->assertDontSee('Kontakt über WhatsApp');

        config([
            'inquiry-alerts.channels.sms.enabled' => true,
            'inquiry-alerts.channels.sms.driver' => 'seven',
            'inquiry-alerts.channels.telegram.enabled' => true,
            'inquiry-alerts.whatsapp_button_number' => '+491511234567',
        ]);

        $this->get(route('datenschutz'))
            ->assertSee('seven communications')
            ->assertSee('Telegram Messenger')
            ->assertSee('Kontakt über WhatsApp');
    }

    public function test_security_headers_are_set(): void
    {
        $this->get('/')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee("Disallow: /\n", false)->assertDontSee('Sitemap');
    }

    public function test_robots_in_production_points_to_sitemap(): void
    {
        $this->app['env'] = 'production';

        $this->get('/robots.txt')
            ->assertSee('Sitemap: ' . route('sitemap'), false)
            ->assertSee('Disallow: /admin/', false);
    }

    public function test_custom_404_page(): void
    {
        $this->get('/diese-seite-gibt-es-nicht')
            ->assertNotFound()
            ->assertSee('Diese Seite gibt es nicht (mehr).')
            ->assertSee('noindex', false)
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_public_pages_load_no_external_resources(): void
    {
        foreach (['/', '/leistungen', '/kontakt', '/datenschutz'] as $url) {
            $html = $this->get($url)->getContent();

            $this->assertStringNotContainsString('fonts.googleapis.com', $html, $url);
            $this->assertStringNotContainsString('cdn.jsdelivr.net', $html, $url);
            $this->assertStringNotContainsString('assets/vendor/bootstrap', $html, $url);
        }
    }

    public function test_every_page_has_exactly_one_h1(): void
    {
        foreach (['/', '/leistungen', '/leistungen/softwarewartung', '/referenzen', '/ueber-ruddattech', '/kontakt', '/impressum', '/datenschutz', '/agb'] as $url) {
            $html = $this->get($url)->getContent();
            $this->assertSame(1, preg_match_all('/<h1[\s>]/', $html), "Genau eine H1 auf $url");
        }
    }

    public function test_images_have_alt_attributes(): void
    {
        $reference = \App\Models\PortfolioItem::create([
            'title' => 'Bildtest', 'summary' => 'Kurz', 'cover_image' => 'portfolio/c.webp', 'cover_alt' => 'Übersicht',
            'is_published' => true, 'is_featured' => true,
        ]);
        $reference->images()->create(['path' => 'portfolio/g.webp', 'alt' => 'Galerie']);

        $checked = 0;
        foreach (['/', '/referenzen', route('referenzen.show', $reference)] as $url) {
            $html = $this->get($url)->getContent();
            preg_match_all('/<img\b[^>]*>/i', $html, $imgs);
            $checked += count($imgs[0]);
            foreach ($imgs[0] as $img) {
                $this->assertMatchesRegularExpression('/\balt=/', $img, "Bild ohne alt auf $url: $img");
            }
        }

        $this->assertGreaterThanOrEqual(4, $checked, 'Es sollten Bilder geprüft worden sein.');
    }

    public function test_bot_tracking_does_not_log_every_request(): void
    {
        \Illuminate\Support\Facades\Log::spy();

        $this->get('/', ['User-Agent' => 'Mozilla/5.0 Testbrowser'])->assertOk();

        \Illuminate\Support\Facades\Log::shouldNotHaveReceived('info');
        $this->addToAssertionCount(1);
    }
}
