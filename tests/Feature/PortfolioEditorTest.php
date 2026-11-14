<?php

namespace Tests\Feature;

use App\Livewire\Backend\PortfolioEditor;
use App\Models\Admin;
use App\Models\PortfolioImage;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PortfolioEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(Admin::create(['name' => 'Admin', 'email' => 'a@example.com', 'password' => bcrypt('x-test')]), 'admin');
    }

    public function test_editor_requires_admin_login(): void
    {
        auth('admin')->logout();
        $this->get(route('admin.portfolio.editor'))->assertRedirect();
    }

    public function test_editor_page_renders(): void
    {
        $this->get(route('admin.portfolio.editor'))->assertOk()->assertSee('Referenzen');
    }

    public function test_create_case_study_with_clean_unique_slug(): void
    {
        PortfolioItem::create(['title' => 'Portal Core', 'slug' => 'portal-core']);

        Livewire::test(PortfolioEditor::class)
            ->call('create')
            ->set('title', 'Portal Core')
            ->set('project_type', 'Mehrmandanten-Plattform')
            ->set('features_input', "- Mandantenverwaltung\n• Suche\n\n")
            ->set('integrations_input', 'JustDeliver-Synchronisation')
            ->set('badges_input', 'Laravel, MySQL , ')
            ->set('complexity_tags', ['multi_tenancy', 'queues'])
            ->call('save')
            ->assertHasNoErrors();

        $item = PortfolioItem::where('title', 'Portal Core')->latest('id')->first();
        $this->assertSame('portal-core-2', $item->slug);
        $this->assertSame(['Mandantenverwaltung', 'Suche'], $item->features);
        $this->assertSame(['Laravel', 'MySQL'], $item->badges);
        $this->assertSame(['Mehrmandantenfähigkeit', 'Hintergrundverarbeitung und Queues'], array_values($item->complexityLabels()));
        $this->assertFalse($item->is_published);
    }

    public function test_unknown_complexity_tag_is_rejected(): void
    {
        Livewire::test(PortfolioEditor::class)
            ->call('create')
            ->set('title', 'X')
            ->set('complexity_tags', ['erfunden'])
            ->call('save')
            ->assertHasErrors('complexity_tags.0');
    }

    public function test_uploads_are_converted_to_webp_variants(): void
    {
        Livewire::test(PortfolioEditor::class)
            ->call('create')
            ->set('title', 'JustDeliver 4')
            ->set('cover_upload', UploadedFile::fake()->image('cover.png', 2400, 1500))
            ->set('gallery_uploads', [UploadedFile::fake()->image('a.jpg', 800, 500)])
            ->call('save')
            ->assertHasNoErrors();

        $item = PortfolioItem::firstWhere('title', 'JustDeliver 4');
        $this->assertSame([640, 1280, 1920], array_keys($item->cover_variants));
        foreach ($item->cover_variants as $path) {
            Storage::disk('public')->assertExists($path);
            $this->assertStringEndsWith('.webp', $path);
        }

        $image = $item->images()->sole();
        // Kein Hochskalieren: 800px Original → Varianten 640 und 800
        $this->assertSame([640, 800], array_keys($image->variants));
        $this->assertSame(800, $image->width);
        $this->assertSame(500, $image->height);
    }

    public function test_publication_requires_summary_and_alt_texts(): void
    {
        $component = Livewire::test(PortfolioEditor::class)
            ->call('create')
            ->set('title', 'BelegKalle')
            ->set('cover_upload', UploadedFile::fake()->image('c.png', 1200, 800))
            ->set('is_published', true)
            ->call('save')
            ->assertHasErrors(['summary', 'cover_alt']);

        $this->assertSame(0, PortfolioItem::count());

        $component->set('summary', 'Digitaler Belegordner')
            ->set('cover_alt', 'Übersicht der Belegliste')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(PortfolioItem::sole()->is_published);
    }

    public function test_gallery_alt_texts_are_saved_and_required_for_publication(): void
    {
        $item = PortfolioItem::create(['title' => 'Legacy', 'summary' => 'Migration']);
        $image = $item->images()->create(['path' => 'portfolio/x.webp']);

        Livewire::test(PortfolioEditor::class)
            ->call('edit', $item->id)
            ->set('is_published', true)
            ->call('save')
            ->assertHasErrors("imageMeta.{$image->id}.alt")
            ->set("imageMeta.{$image->id}.alt", 'Audit-Bericht')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Audit-Bericht', $image->fresh()->alt);
        $this->assertTrue($item->fresh()->is_published);
    }

    public function test_toggle_publish_from_list_is_blocked_without_alt_texts(): void
    {
        $item = PortfolioItem::create(['title' => 'Ohne Text']);

        Livewire::test(PortfolioEditor::class)->call('togglePublished', $item->id);
        $this->assertFalse($item->fresh()->is_published);

        $item->update(['summary' => 'Kurz']);
        Livewire::test(PortfolioEditor::class)->call('togglePublished', $item->id);
        $this->assertTrue($item->fresh()->is_published);
    }

    public function test_reorder_and_delete_images(): void
    {
        $item = PortfolioItem::create(['title' => 'Galerie']);
        $a = $item->images()->create(['path' => 'portfolio/a.webp', 'sort_order' => 1]);
        $b = $item->images()->create(['path' => 'portfolio/b.webp', 'sort_order' => 2]);
        Storage::disk('public')->put('portfolio/a.webp', 'x');

        $component = Livewire::test(PortfolioEditor::class)->call('edit', $item->id)->call('moveImage', $b->id, -1);
        $this->assertSame([$b->id, $a->id], $item->images()->pluck('id')->all());

        $component->call('deleteImage', $a->id);
        $this->assertNull(PortfolioImage::find($a->id));
        Storage::disk('public')->assertMissing('portfolio/a.webp');
    }

    public function test_case_study_page_shows_structured_sections_and_gallery(): void
    {
        $item = PortfolioItem::create([
            'title' => 'JustDeliver 4',
            'summary' => 'Plattform für lokale Lieferdienste',
            'project_type' => 'Bestell- und Abrechnungsplattform',
            'challenge' => 'Ausgangslage-Text',
            'architecture' => 'Architektur-Text',
            'features' => ['Bestellabwicklung'],
            'integrations' => ['Zahlungsanbieter'],
            'complexity_tags' => ['payments'],
            'role' => 'Entwicklung und Betrieb',
            'is_published' => true,
            'seo_title' => 'JustDeliver 4 – Fallstudie',
        ]);
        $item->images()->create(['path' => 'portfolio/s.webp', 'alt' => 'Kassenansicht', 'variants' => [640 => 'portfolio/s-640.webp']]);

        $this->get(route('referenzen.show', $item))
            ->assertOk()
            ->assertSee('<title>JustDeliver 4 – Fallstudie</title>', false)
            ->assertSee('Ausgangssituation')
            ->assertSee('Architektur-Text')
            ->assertSee('Bestellabwicklung')
            ->assertSee('Zahlungsanbieter')
            ->assertSee('Zahlungs- und Abrechnungssysteme')
            ->assertSee('Entwicklung und Betrieb')
            ->assertSee('alt="Kassenansicht"', false)
            ->assertSee('640w', false)
            ->assertSee('id="lightbox"', false);
    }
}
