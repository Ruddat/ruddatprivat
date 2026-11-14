<?php

namespace App\Livewire\Backend;

use App\Models\PortfolioImage;
use App\Models\PortfolioItem;
use App\Services\Images\ImageOptimizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Services\Images\ImageOptimizationException;

/**
 * Verwaltung der Referenzen / Fallstudien.
 */
class PortfolioEditor extends Component
{
    use WithFileUploads;
    use WithPagination;

    public ?int $editingId = null;

    public bool $showForm = false;

    // Grunddaten
    public string $title = '';
    public string $slug = '';
    public string $category = '';
    public string $project_type = '';
    public string $project_status = '';
    public string $role = '';
    public string $summary = '';
    public string $type = 'showcase';
    public string $cta_link = '';

    // Fallstudie
    public string $description = '';
    public string $challenge = '';
    public string $technical_challenges = '';
    public string $architecture = '';
    public string $features_input = '';
    public string $integrations_input = '';
    public string $operations = '';
    public string $badges_input = '';
    public array $complexity_tags = [];

    // Veröffentlichung & SEO
    public bool $is_published = false;
    public bool $is_featured = false;
    public int $sort_order = 0;
    public string $seo_title = '';
    public string $meta_description = '';

    // Bilder
    public string $cover_alt = '';
    public $cover_upload = null;
    public array $gallery_uploads = [];
    /** @var array<int, array{alt: string, caption: string}> */
    public array $imageMeta = [];

    // Liste
    public string $search = '';
    public string $filterStatus = '';

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'category' => ['nullable', 'string', 'max:120'],
            'project_type' => ['nullable', 'string', 'max:160'],
            'project_status' => ['nullable', 'string', 'max:160'],
            'role' => ['nullable', 'string', 'max:2000'],
            'summary' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['showcase', 'module'])],
            'cta_link' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:20000'],
            'challenge' => ['nullable', 'string', 'max:10000'],
            'technical_challenges' => ['nullable', 'string', 'max:10000'],
            'architecture' => ['nullable', 'string', 'max:10000'],
            'features_input' => ['nullable', 'string', 'max:10000'],
            'integrations_input' => ['nullable', 'string', 'max:5000'],
            'operations' => ['nullable', 'string', 'max:10000'],
            'badges_input' => ['nullable', 'string', 'max:1000'],
            'complexity_tags' => ['array'],
            'complexity_tags.*' => [Rule::in(array_keys(PortfolioItem::COMPLEXITY_TAGS))],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:9999'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            'cover_alt' => ['nullable', 'string', 'max:255'],
            'cover_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'gallery_uploads' => ['array', 'max:20'],
            'gallery_uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'imageMeta.*.alt' => ['nullable', 'string', 'max:255'],
            'imageMeta.*.caption' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'cover_alt' => 'Alt-Text des Hauptbilds',
            'cover_upload' => 'Hauptbild',
            'gallery_uploads.*' => 'Screenshot',
            'imageMeta.*.alt' => 'Alt-Text',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $item = PortfolioItem::with('images')->findOrFail($id);

        $this->resetForm();
        $this->editingId = $item->id;

        foreach ([
            'title', 'slug', 'category', 'project_type', 'project_status', 'role', 'summary', 'cta_link',
            'description', 'challenge', 'technical_challenges', 'architecture', 'operations',
            'seo_title', 'meta_description', 'cover_alt',
        ] as $field) {
            $this->{$field} = (string) $item->{$field};
        }

        $this->type = $item->type ?: 'showcase';
        $this->features_input = implode("\n", $item->features ?? []);
        $this->integrations_input = implode("\n", $item->integrations ?? []);
        $this->badges_input = implode(', ', $item->badges ?? []);
        $this->complexity_tags = $item->complexity_tags ?? [];
        $this->is_published = $item->is_published;
        $this->is_featured = $item->is_featured;
        $this->sort_order = $item->sort_order;
        $this->loadImageMeta($item);

        $this->showForm = true;
    }

    public function save(ImageOptimizer $optimizer): void
    {
        $this->validate();

        $item = $this->editingId ? PortfolioItem::with('images')->findOrFail($this->editingId) : null;

        $this->guardPublication($item);

        $lines = fn (string $text) => collect(preg_split('/\R/', $text))->map(fn ($l) => trim($l, " \t-•"))->filter()->values()->all();

        $data = [
            'title' => $this->title,
            'slug' => PortfolioItem::uniqueSlug($this->slug ?: $this->title, $this->editingId),
            'category' => $this->category, // Spalte ist NOT NULL (Altbestand)
            'project_type' => $this->project_type ?: null,
            'project_status' => $this->project_status ?: null,
            'role' => $this->role ?: null,
            'summary' => $this->summary ?: null,
            'type' => $this->type ?: 'showcase',
            'cta_link' => $this->cta_link ?: null,
            'description' => $this->description ?: null,
            'challenge' => $this->challenge ?: null,
            'technical_challenges' => $this->technical_challenges ?: null,
            'architecture' => $this->architecture ?: null,
            'features' => $lines($this->features_input),
            'integrations' => $lines($this->integrations_input),
            'operations' => $this->operations ?: null,
            'badges' => collect(explode(',', $this->badges_input))->map(fn ($b) => trim($b))->filter()->values()->all(),
            'complexity_tags' => array_values($this->complexity_tags),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'seo_title' => $this->seo_title ?: null,
            'meta_description' => $this->meta_description ?: null,
            'cover_alt' => $this->cover_alt ?: null,
        ];

        try {
            DB::transaction(function () use (&$item, $data, $optimizer) {
                $item ??= new PortfolioItem;
                $item->fill($data)->save();

                if ($this->cover_upload) {
                    $result = $optimizer->storeWebpVariants($this->cover_upload->getRealPath(), "portfolio/{$item->id}");
                    $optimizer->delete($item->cover_variants);
                    $item->forceFill(['cover_image' => $result['path'], 'cover_variants' => $result['variants']])->save();
                }

                foreach ($this->imageMeta as $id => $meta) {
                    $item->images()->whereKey($id)->update([
                        'alt' => $meta['alt'] ?: null,
                        'caption' => $meta['caption'] ?: null,
                    ]);
                }

                $next = (int) $item->images()->max('sort_order');
                foreach ($this->gallery_uploads as $upload) {
                    $result = $optimizer->storeWebpVariants($upload->getRealPath(), "portfolio/{$item->id}");
                    $item->images()->create([
                        'path' => $result['path'],
                        'variants' => $result['variants'],
                        'width' => $result['width'],
                        'height' => $result['height'],
                        'sort_order' => ++$next,
                    ]);
                }
            });
        } catch (ImageOptimizationException $e) {
            $this->addError('cover_upload', $e->getMessage());

            return;
        }

        $this->editingId = $item->id;
        $this->slug = $item->slug;
        $this->reset(['cover_upload', 'gallery_uploads']);
        $this->loadImageMeta($item->fresh('images'));

        session()->flash('success', 'Referenz wurde gespeichert.');
    }

    /**
     * Qualitätsprüfung vor der Veröffentlichung: Kurzbeschreibung, Hauptbild mit Alt-Text
     * und Alt-Texte für alle Screenshots.
     */
    private function guardPublication(?PortfolioItem $item): void
    {
        if (! $this->is_published) {
            return;
        }

        $errors = [];

        if (blank($this->summary)) {
            $errors['summary'] = 'Für die Veröffentlichung ist eine Kurzbeschreibung erforderlich.';
        }

        $hasCover = $this->cover_upload || filled($item?->cover_image);
        if ($hasCover && blank($this->cover_alt)) {
            $errors['cover_alt'] = 'Bitte einen Alt-Text für das Hauptbild angeben.';
        }

        foreach ($this->imageMeta as $id => $meta) {
            if (blank($meta['alt'] ?? null)) {
                $errors["imageMeta.$id.alt"] = 'Alt-Text fehlt.';
            }
        }

        if ($this->gallery_uploads) {
            $errors['gallery_uploads'] = 'Bitte neue Screenshots zuerst speichern und mit Alt-Texten versehen, dann veröffentlichen.';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function moveImage(int $imageId, int $direction): void
    {
        $item = PortfolioItem::with('images')->findOrFail($this->editingId);
        $images = $item->images->values();
        $index = $images->search(fn ($img) => $img->id === $imageId);
        $swap = $index === false ? null : $images->get($index + $direction);

        if (! $swap) {
            return;
        }

        $current = $images[$index];
        DB::transaction(function () use ($images, $current, $swap) {
            // Reihenfolge normalisieren, dann tauschen
            foreach ($images as $i => $img) {
                $img->update(['sort_order' => $i + 1]);
            }
            [$a, $b] = [$current->fresh()->sort_order, $swap->fresh()->sort_order];
            $current->update(['sort_order' => $b]);
            $swap->update(['sort_order' => $a]);
        });

        $this->loadImageMeta($item->fresh('images'));
    }

    public function deleteImage(int $imageId, ImageOptimizer $optimizer): void
    {
        $image = PortfolioImage::where('portfolio_item_id', $this->editingId)->findOrFail($imageId);
        $optimizer->delete($image->variants ?: [$image->path]);
        $image->delete();

        unset($this->imageMeta[$imageId]);
    }

    public function removeGalleryUpload(int $index): void
    {
        array_splice($this->gallery_uploads, $index, 1);
    }

    public function togglePublished(int $id): void
    {
        $item = PortfolioItem::findOrFail($id);

        if (! $item->is_published) {
            // Einfache Freigabe aus der Liste nur, wenn die Pflichtangaben vorhanden sind
            $missingAlt = $item->images()->whereNull('alt')->exists();
            if (blank($item->summary) || ($item->cover_image && blank($item->cover_alt)) || $missingAlt) {
                session()->flash('error', "„{$item->title}“ kann noch nicht veröffentlicht werden: Kurzbeschreibung und Alt-Texte prüfen.");

                return;
            }
        }

        $item->update(['is_published' => ! $item->is_published]);
    }

    public function delete(int $id, ImageOptimizer $optimizer): void
    {
        $item = PortfolioItem::with('images')->findOrFail($id);

        DB::transaction(function () use ($item, $optimizer) {
            foreach ($item->images as $image) {
                $optimizer->delete($image->variants ?: [$image->path]);
            }
            $item->images()->delete();
            $optimizer->delete($item->cover_variants);
            $item->delete();
        });

        if ($this->editingId === $id) {
            $this->resetForm();
            $this->showForm = false;
        }

        session()->flash('success', 'Referenz wurde gelöscht.');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'title', 'slug', 'category', 'project_type', 'project_status', 'role', 'summary', 'cta_link',
            'description', 'challenge', 'technical_challenges', 'architecture', 'features_input', 'integrations_input',
            'operations', 'badges_input', 'complexity_tags', 'is_published', 'is_featured', 'sort_order', 'seo_title',
            'meta_description', 'cover_alt', 'cover_upload', 'gallery_uploads', 'imageMeta',
        ]);
        $this->type = 'showcase';
        $this->resetValidation();
    }

    private function loadImageMeta(PortfolioItem $item): void
    {
        $this->imageMeta = $item->images
            ->mapWithKeys(fn ($img) => [$img->id => ['alt' => (string) $img->alt, 'caption' => (string) $img->caption]])
            ->all();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = PortfolioItem::query()
            ->withCount('images')
            ->when($this->search, function ($query) {
                $like = '%' . $this->search . '%';
                $query->where(fn ($q) => $q->where('title', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('category', 'like', $like));
            })
            ->when($this->filterStatus === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->filterStatus === 'draft', fn ($q) => $q->where('is_published', false))
            ->when($this->filterStatus === 'featured', fn ($q) => $q->where('is_featured', true))
            ->ordered()
            ->paginate(20);

        return view('livewire.backend.portfolio-editor', [
            'items' => $items,
            'currentImages' => $this->editingId
                ? PortfolioImage::where('portfolio_item_id', $this->editingId)->orderBy('sort_order')->orderBy('id')->get()
                : collect(),
            'currentCover' => $this->editingId ? PortfolioItem::find($this->editingId) : null,
            'complexityOptions' => PortfolioItem::COMPLEXITY_TAGS,
        ])->extends('backend.admin.layouts.app')
            ->section('content');
    }
}
