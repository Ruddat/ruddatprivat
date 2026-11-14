@php
    $input = 'w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900 dark:border-gray-600 dark:bg-gray-900';
    $label = 'mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300';
    $hint = 'mt-1 text-xs text-slate-500';
    $err = 'mt-1 text-sm text-red-600';
@endphp

<div class="space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Referenzen</h1>
            <p class="mt-1 text-sm text-slate-500">Technische Fallstudien für die Webseite pflegen. Nur veröffentlichte Einträge sind öffentlich sichtbar.</p>
        </div>
        @unless ($showForm)
            <button type="button" wire:click="create" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Neue Referenz
            </button>
        @endunless
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @if ($showForm)
        <form wire:submit="save" class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $editingId ? 'Referenz bearbeiten' : 'Neue Referenz anlegen' }}</h2>
                <div class="flex gap-2">
                    @if ($editingId && $currentCover?->is_published)
                        <a href="{{ route('referenzen.show', $currentCover->slug) }}" target="_blank" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-200">Ansehen</a>
                    @endif
                    <button type="button" wire:click="cancel" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:text-slate-200">Schließen</button>
                    <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Speichern</span>
                        <span wire:loading wire:target="save">Speichert …</span>
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Bitte die markierten Felder prüfen.</div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,1fr)]">
                <div class="space-y-6">
                    {{-- Grunddaten --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">1. Projekt</h3>
                        <div>
                            <label class="{{ $label }}" for="pf-title">Projektname *</label>
                            <input id="pf-title" type="text" wire:model="title" class="{{ $input }}">
                            @error('title') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="{{ $label }}" for="pf-ptype">Projekttyp</label>
                                <input id="pf-ptype" type="text" wire:model="project_type" class="{{ $input }}" placeholder="z. B. Mehrmandanten-Portalplattform">
                                @error('project_type') <p class="{{ $err }}">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="{{ $label }}" for="pf-cat">Kategorie (Kartenbeschriftung)</label>
                                <input id="pf-cat" type="text" wire:model="category" class="{{ $input }}" placeholder="z. B. Plattform">
                            </div>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-summary">Kurzbeschreibung</label>
                            <textarea id="pf-summary" wire:model="summary" rows="2" maxlength="255" class="{{ $input }}"></textarea>
                            <p class="{{ $hint }}">Für Übersichtskarten, max. 255 Zeichen. Pflicht bei Veröffentlichung.</p>
                            @error('summary') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-desc">Projektbeschreibung</label>
                            <textarea id="pf-desc" wire:model="description" rows="5" class="{{ $input }}"></textarea>
                        </div>
                    </section>

                    {{-- Fallstudie --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">2. Fallstudie</h3>
                        <p class="text-sm text-amber-700">Nur belegbare Angaben eintragen – keine Kundendaten, Zugangsdaten, internen Hosts oder vertraulichen Details.</p>
                        <div>
                            <label class="{{ $label }}" for="pf-challenge">Ausgangssituation / fachliche Problemstellung</label>
                            <textarea id="pf-challenge" wire:model="challenge" rows="4" class="{{ $input }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-tech">Technische Herausforderungen</label>
                            <textarea id="pf-tech" wire:model="technical_challenges" rows="4" class="{{ $input }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-arch">Architektur</label>
                            <textarea id="pf-arch" wire:model="architecture" rows="4" class="{{ $input }}"></textarea>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="{{ $label }}" for="pf-features">Wesentliche Funktionen</label>
                                <textarea id="pf-features" wire:model="features_input" rows="6" class="{{ $input }}"></textarea>
                                <p class="{{ $hint }}">Eine Funktion pro Zeile.</p>
                            </div>
                            <div>
                                <label class="{{ $label }}" for="pf-integrations">Integrationen &amp; Schnittstellen</label>
                                <textarea id="pf-integrations" wire:model="integrations_input" rows="6" class="{{ $input }}"></textarea>
                                <p class="{{ $hint }}">Eine Schnittstelle pro Zeile.</p>
                            </div>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-ops">Datenbank, Sicherheit &amp; Betrieb</label>
                            <textarea id="pf-ops" wire:model="operations" rows="4" class="{{ $input }}"></textarea>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-badges">Technologien</label>
                            <input id="pf-badges" type="text" wire:model="badges_input" class="{{ $input }}" placeholder="Laravel, Livewire, MySQL">
                            <p class="{{ $hint }}">Kommagetrennt; nur bestätigte Technologien.</p>
                        </div>
                        <fieldset>
                            <legend class="{{ $label }}">Nachgewiesene Komplexität</legend>
                            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                @foreach ($complexityOptions as $key => $optionLabel)
                                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" value="{{ $key }}" wire:model="complexity_tags" class="rounded border-slate-300">
                                        {{ $optionLabel }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="{{ $label }}" for="pf-status">Projektstatus</label>
                                <input id="pf-status" type="text" wire:model="project_status" class="{{ $input }}" placeholder="z. B. Produktiv im Einsatz">
                            </div>
                            <div>
                                <label class="{{ $label }}" for="pf-cta">Externer Link (optional)</label>
                                <input id="pf-cta" type="url" wire:model="cta_link" class="{{ $input }}" placeholder="https://…">
                                @error('cta_link') <p class="{{ $err }}">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-role">Rolle von RuddatTech</label>
                            <textarea id="pf-role" wire:model="role" rows="3" class="{{ $input }}" placeholder="z. B. Konzeption, Entwicklung und Betrieb"></textarea>
                        </div>
                    </section>

                    {{-- Galerie --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">3. Screenshot-Galerie</h3>
                        <p class="text-sm text-slate-500">Bilder werden automatisch als WebP in mehreren Größen gespeichert; Metadaten werden entfernt. Vorher Kundendaten, Tokens und interne Adressen unkenntlich machen.</p>

                        @if ($currentImages->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach ($currentImages as $image)
                                    <li wire:key="img-{{ $image->id }}" class="flex flex-col gap-3 rounded-lg border border-slate-200 p-3 sm:flex-row dark:border-gray-700">
                                        <img src="{{ $image->url }}" alt="" class="aspect-[16/10] w-full shrink-0 rounded object-cover object-top sm:w-40">
                                        <div class="flex-1 space-y-2">
                                            <input type="text" wire:model="imageMeta.{{ $image->id }}.alt" class="{{ $input }}" placeholder="Alt-Text (Pflicht bei Veröffentlichung)" aria-label="Alt-Text">
                                            @error("imageMeta.{$image->id}.alt") <p class="{{ $err }}">{{ $message }}</p> @enderror
                                            <input type="text" wire:model="imageMeta.{{ $image->id }}.caption" class="{{ $input }}" placeholder="Bildunterschrift (optional)" aria-label="Bildunterschrift">
                                        </div>
                                        <div class="flex shrink-0 gap-1 sm:flex-col">
                                            <button type="button" wire:click="moveImage({{ $image->id }}, -1)" class="rounded border px-2 py-1 text-xs" title="Nach oben">↑</button>
                                            <button type="button" wire:click="moveImage({{ $image->id }}, 1)" class="rounded border px-2 py-1 text-xs" title="Nach unten">↓</button>
                                            <button type="button" wire:click="deleteImage({{ $image->id }})" wire:confirm="Bild löschen?" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700">Löschen</button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 dark:border-gray-600 dark:bg-gray-900">
                            <label class="{{ $label }}" for="pf-gallery">Screenshots hinzufügen</label>
                            <input id="pf-gallery" type="file" wire:model="gallery_uploads" multiple accept="image/jpeg,image/png,image/webp" class="block w-full text-sm">
                            <div wire:loading wire:target="gallery_uploads" class="mt-2 text-sm text-slate-500">Wird hochgeladen …</div>
                            @error('gallery_uploads') <p class="{{ $err }}">{{ $message }}</p> @enderror
                            @error('gallery_uploads.*') <p class="{{ $err }}">{{ $message }}</p> @enderror
                            @if ($gallery_uploads)
                                <div class="mt-3 grid grid-cols-3 gap-2">
                                    @foreach ($gallery_uploads as $i => $upload)
                                        <div class="relative" wire:key="up-{{ $i }}">
                                            <img src="{{ $upload->temporaryUrl() }}" alt="" class="aspect-[16/10] w-full rounded object-cover object-top">
                                            <button type="button" wire:click="removeGalleryUpload({{ $i }})" class="absolute right-1 top-1 rounded bg-white/90 px-1.5 text-xs">✕</button>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="{{ $hint }}">Werden beim Speichern übernommen.</p>
                            @endif
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    {{-- Veröffentlichung --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">Veröffentlichung</h3>
                        <label class="flex items-start gap-3 text-sm">
                            <input type="checkbox" wire:model="is_published" class="mt-0.5 rounded border-slate-300">
                            <span><span class="font-medium text-slate-900 dark:text-white">Veröffentlicht</span><br><span class="text-slate-500">Nur bei geprüften Veröffentlichungsrechten.</span></span>
                        </label>
                        <label class="flex items-start gap-3 text-sm">
                            <input type="checkbox" wire:model="is_featured" class="mt-0.5 rounded border-slate-300">
                            <span><span class="font-medium text-slate-900 dark:text-white">Auf der Startseite hervorheben</span><br><span class="text-slate-500">Maximal drei werden angezeigt.</span></span>
                        </label>
                        <div>
                            <label class="{{ $label }}" for="pf-sort">Reihenfolge</label>
                            <input id="pf-sort" type="number" min="0" wire:model="sort_order" class="{{ $input }}">
                            <p class="{{ $hint }}">Kleinere Zahl = weiter vorne.</p>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-slug">URL-Slug</label>
                            <input id="pf-slug" type="text" wire:model="slug" class="{{ $input }}" placeholder="wird aus dem Titel erzeugt">
                            <p class="{{ $hint }}">/referenzen/{{ $slug ?: '…' }}</p>
                            @error('slug') <p class="{{ $err }}">Nur Kleinbuchstaben, Ziffern und Bindestriche.</p> @enderror
                        </div>
                    </section>

                    {{-- Hauptbild --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">Hauptbild</h3>
                        @if ($cover_upload)
                            <img src="{{ $cover_upload->temporaryUrl() }}" alt="" class="aspect-[16/10] w-full rounded object-cover object-top">
                        @elseif ($currentCover?->cover_url)
                            <img src="{{ $currentCover->cover_url }}" alt="" class="aspect-[16/10] w-full rounded object-cover object-top">
                        @endif
                        <input type="file" wire:model="cover_upload" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm" aria-label="Hauptbild hochladen">
                        <div wire:loading wire:target="cover_upload" class="text-sm text-slate-500">Wird hochgeladen …</div>
                        @error('cover_upload') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        <div>
                            <label class="{{ $label }}" for="pf-coveralt">Alt-Text des Hauptbilds</label>
                            <input id="pf-coveralt" type="text" wire:model="cover_alt" class="{{ $input }}" placeholder="Beschreibt, was zu sehen ist">
                            @error('cover_alt') <p class="{{ $err }}">{{ $message }}</p> @enderror
                        </div>
                    </section>

                    {{-- SEO --}}
                    <section class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="font-semibold text-slate-900 dark:text-white">SEO</h3>
                        <div>
                            <label class="{{ $label }}" for="pf-seotitle">SEO-Titel</label>
                            <input id="pf-seotitle" type="text" wire:model.live.debounce.500ms="seo_title" maxlength="70" class="{{ $input }}">
                            <p class="{{ $hint }}">{{ mb_strlen($seo_title) }}/70 – leer = „Projektname – Referenz | RuddatTech“</p>
                        </div>
                        <div>
                            <label class="{{ $label }}" for="pf-meta">Meta-Description</label>
                            <textarea id="pf-meta" wire:model.live.debounce.500ms="meta_description" rows="3" maxlength="170" class="{{ $input }}"></textarea>
                            <p class="{{ $hint }}">{{ mb_strlen($meta_description) }}/170 – leer = Kurzbeschreibung</p>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    @endif

    {{-- Liste --}}
    <section class="space-y-4">
        <div class="flex flex-col gap-3 sm:flex-row">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Referenzen durchsuchen …" aria-label="Referenzen durchsuchen" class="{{ $input }} sm:max-w-sm">
            <select wire:model.live="filterStatus" aria-label="Status filtern" class="{{ $input }} sm:max-w-[200px]">
                <option value="">Alle</option>
                <option value="published">Veröffentlicht</option>
                <option value="draft">Entwurf</option>
                <option value="featured">Hervorgehoben</option>
            </select>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Referenz</th>
                        <th class="px-4 py-3">Bilder</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                    @forelse ($items as $item)
                        <tr wire:key="pf-{{ $item->id }}" class="{{ $editingId === $item->id ? 'bg-slate-50 dark:bg-gray-700/40' : '' }}">
                            <td class="px-4 py-3 text-slate-500">{{ $item->sort_order }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $item->title }}</div>
                                <div class="text-xs text-slate-500">/referenzen/{{ $item->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->images_count }}</td>
                            <td class="px-4 py-3">
                                <button type="button" wire:click="togglePublished({{ $item->id }})"
                                    class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $item->is_published ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-slate-50 text-slate-600 ring-slate-200' }}">
                                    {{ $item->is_published ? 'Veröffentlicht' : 'Entwurf' }}
                                </button>
                                @if ($item->is_featured)
                                    <span class="ml-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">Startseite</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <button type="button" wire:click="edit({{ $item->id }})" class="rounded px-2 py-1 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:text-slate-200">Bearbeiten</button>
                                <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="Referenz „{{ $item->title }}“ inklusive Bilder endgültig löschen?" class="rounded px-2 py-1 text-sm font-medium text-red-700 hover:bg-red-50">Löschen</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">Keine Referenzen gefunden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    </section>
</div>
