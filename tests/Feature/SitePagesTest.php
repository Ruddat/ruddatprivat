<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Support\Site\SiteContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_pages_render_with_unique_titles(): void
    {
        $pages = [
            route('home'),
            route('leistungen.index'),
            route('referenzen.index'),
            route('about'),
            route('kontakt'),
        ];

        foreach (array_keys(SiteContent::services()) as $slug) {
            $pages[] = route('leistungen.show', $slug);
        }

        $titles = [];

        foreach ($pages as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('~<title>[^<]+</title>~', $html, $url);
            $this->assertStringContainsString('<link rel="canonical"', $html, $url);
            $this->assertStringContainsString('name="description"', $html, $url);

            preg_match('~<title>([^<]+)</title>~', $html, $m);
            $titles[] = $m[1];
        }

        $this->assertSame(count($titles), count(array_unique($titles)), 'Seitentitel müssen eindeutig sein.');
    }

    public function test_home_contains_hero_and_ctas(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Ihre Software ist geschäftskritisch.', false)
            ->assertSee('Technische Betreuung anfragen')
            ->assertSee('Referenzprojekte ansehen')
            ->assertSee('Betreuung anfragen');
    }

    public function test_pages_make_no_forbidden_promises(): void
    {
        $urls = [route('home'), route('leistungen.index')];
        foreach (array_keys(SiteContent::services()) as $slug) {
            $urls[] = route('leistungen.show', $slug);
        }

        foreach ($urls as $url) {
            $text = strip_tags($this->get($url)->getContent());

            $this->assertDoesNotMatchRegularExpression('~\d+\s?(€|Euro)~u', $text, "Keine Preise auf $url");
            $this->assertDoesNotMatchRegularExpression('~garantiert(e|en)?\s+(24/7|Verfügbarkeit)~iu', $text, $url);
        }
    }

    public function test_unknown_service_returns_404(): void
    {
        $this->get('/leistungen/gibt-es-nicht')->assertNotFound();
    }

    public function test_only_published_references_are_visible(): void
    {
        $published = PortfolioItem::create([
            'title' => 'Freigegebenes Projekt', 'slug' => 'freigegeben', 'is_published' => true, 'is_featured' => true,
        ]);
        $hidden = PortfolioItem::create([
            'title' => 'Internes Testprojekt', 'slug' => 'intern', 'is_published' => false,
        ]);

        $this->get(route('referenzen.index'))
            ->assertOk()
            ->assertSee('Freigegebenes Projekt')
            ->assertDontSee('Internes Testprojekt');

        $this->get('/')->assertSee('Freigegebenes Projekt')->assertDontSee('Internes Testprojekt');

        $this->get(route('referenzen.show', $published))->assertOk();
        $this->get(route('referenzen.show', $hidden))->assertNotFound();
    }

    public function test_reference_section_is_hidden_without_featured_references(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Ausgewählte Referenzprojekte');
        $this->get(route('referenzen.index'))->assertSee('Die Fallstudien werden derzeit aufbereitet.');
    }

    public function test_old_portfolio_urls_redirect_permanently(): void
    {
        $this->get('/portfolio')->assertStatus(301)->assertRedirect('/referenzen');
        $this->get('/portfolio/alter-eintrag-68d2f588d19e3')->assertStatus(301)->assertRedirect('/referenzen');
    }

    public function test_removed_telegram_test_route_is_gone(): void
    {
        $this->get('/send-telegram')->assertNotFound();
    }

    public function test_sitemap_lists_new_pages_but_not_unpublished_references(): void
    {
        PortfolioItem::create(['title' => 'Intern', 'slug' => 'intern-geheim', 'is_published' => false]);

        $xml = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->getContent();

        $this->assertStringContainsString(route('leistungen.show', 'softwarewartung'), $xml);
        $this->assertStringContainsString(route('kontakt'), $xml);
        $this->assertStringNotContainsString('intern-geheim', $xml);
    }

    public function test_structured_data_is_valid_json(): void
    {
        $html = $this->get(route('leistungen.show', 'managed-application-services'))->getContent();

        preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $html, $matches);

        $this->assertNotEmpty($matches[1]);
        foreach ($matches[1] as $json) {
            $this->assertIsArray(json_decode(trim($json), true, flags: JSON_THROW_ON_ERROR));
        }
    }
}
