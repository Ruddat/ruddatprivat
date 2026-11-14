<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use Database\Seeders\ReferenceCaseStudySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReferenceCaseStudySeederTest extends TestCase
{
    use RefreshDatabase;

    private const SLUGS = ['justdeliver-4', 'justdeliver-legacy-modernisierung', 'portal-core', 'belegkalle'];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_seeds_four_unpublished_case_studies(): void
    {
        $this->seed(ReferenceCaseStudySeeder::class);

        $items = PortfolioItem::whereIn('slug', self::SLUGS)->get();
        $this->assertCount(4, $items);
        $this->assertTrue($items->every(fn ($i) => $i->is_published === false));
        $this->assertSame(3, $items->where('is_featured', true)->count());

        // Unveröffentlicht: öffentlich nicht sichtbar
        $this->get(route('referenzen.show', 'portal-core'))->assertNotFound();
    }

    public function test_texts_fit_database_and_seo_limits(): void
    {
        $this->seed(ReferenceCaseStudySeeder::class);

        foreach (PortfolioItem::whereIn('slug', self::SLUGS)->get() as $item) {
            $this->assertLessThanOrEqual(70, mb_strlen($item->seo_title), "$item->slug seo_title");
            $this->assertLessThanOrEqual(170, mb_strlen($item->meta_description), "$item->slug meta_description");
            $this->assertLessThanOrEqual(255, mb_strlen($item->summary), "$item->slug summary");
            $this->assertLessThanOrEqual(160, mb_strlen($item->project_type), "$item->slug project_type");
            $this->assertLessThanOrEqual(160, mb_strlen($item->project_status), "$item->slug project_status");
            $this->assertNotEmpty($item->features);
            $this->assertNotEmpty($item->complexityLabels(), "$item->slug ohne Komplexitätsmerkmale");
            $this->assertSame(count($item->complexity_tags), count($item->complexityLabels()), "$item->slug unbekanntes Merkmal");
        }
    }

    public function test_no_forbidden_claims_or_sensitive_details(): void
    {
        $this->seed(ReferenceCaseStudySeeder::class);

        $text = PortfolioItem::whereIn('slug', self::SLUGS)->get()
            ->map(fn ($i) => json_encode($i->only([
                'title', 'summary', 'description', 'challenge', 'technical_challenges', 'architecture',
                'features', 'integrations', 'operations', 'role', 'seo_title', 'meta_description',
            ]), JSON_UNESCAPED_UNICODE))
            ->implode(' ');

        foreach (['24/7', 'garantiert', 'revisionssicher', 'Kunde', 'Akquise', 'Vertrieb', 'password', 'Passwort:', 'api_key', '.test', 'localhost', '192.168.'] as $forbidden) {
            $this->assertStringNotContainsStringIgnoringCase($forbidden, $text, "Unzulässig: $forbidden");
        }
    }

    public function test_rerun_keeps_admin_publication_decisions(): void
    {
        $this->seed(ReferenceCaseStudySeeder::class);
        PortfolioItem::where('slug', 'belegkalle')->update(['is_published' => true, 'is_featured' => true]);
        PortfolioItem::where('slug', 'portal-core')->update(['is_featured' => false]);

        $this->seed(ReferenceCaseStudySeeder::class);

        $this->assertTrue(PortfolioItem::firstWhere('slug', 'belegkalle')->is_published);
        $this->assertTrue(PortfolioItem::firstWhere('slug', 'belegkalle')->is_featured);
        $this->assertFalse(PortfolioItem::firstWhere('slug', 'portal-core')->is_featured);
        $this->assertSame(4, PortfolioItem::whereIn('slug', self::SLUGS)->count());
    }

    public function test_published_case_study_renders_all_sections(): void
    {
        $this->seed(ReferenceCaseStudySeeder::class);
        PortfolioItem::where('slug', 'justdeliver-4')->update(['is_published' => true]);

        $this->get(route('referenzen.show', 'justdeliver-4'))
            ->assertOk()
            ->assertSee('Ausgangssituation')
            ->assertSee('Technische Herausforderungen')
            ->assertSee('Architektur')
            ->assertSee('Datenbank, Sicherheit und Betrieb')
            ->assertSee('Wesentliche Funktionen')
            ->assertSee('Integrationen und Schnittstellen')
            ->assertSee('Rolle von RuddatTech')
            ->assertSee('Bewältigte Komplexität');
    }
}
