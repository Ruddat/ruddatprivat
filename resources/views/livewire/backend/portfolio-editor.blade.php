<div class="space-y-8">

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Portfolio Editor</h1>
            <p class="mt-1 text-sm text-slate-500">
                Projekte, Module und Referenzen für RuddatTech pflegen.
            </p>
        </div>

        <button type="button" wire:click="create"
            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            Neuer Eintrag
        </button>
    </div>

    @if (session()->has('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if ($showForm)
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(360px,0.9fr)]">

            {{-- FORMULAR --}}
            <form wire:submit.prevent="save" class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $editingId ? 'Eintrag bearbeiten' : 'Neuen Eintrag anlegen' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Nutzt deine bestehende Tabelle: title, category, summary, description, cover_image, badges,
                        type, cta_link.
                    </p>
                </div>

                <div class="space-y-5 p-6">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Titel</label>
                        <input type="text" wire:model.live="title"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                            placeholder="z. B. Nebenkosten-Modul">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Kategorie</label>
                            <input type="text" wire:model.live="category"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                                placeholder="z. B. Laravel, Plattform, KI">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Typ</label>
                            <input type="text" wire:model.live="type"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                                placeholder="z. B. project, module, website">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Kurzbeschreibung</label>
                        <textarea wire:model.live="summary" rows="3"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                            placeholder="Kurzer Text für die Kartenansicht"></textarea>
                        @error('summary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Beschreibung</label>
                        <textarea wire:model.defer="description" rows="7"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                            placeholder="Ausführlicher Beschreibungstext"></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-slate-700">Cover-Bild</label>

                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                            <input type="file" wire:model="cover_upload" accept="image/*"
                                class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800">

                            <div wire:loading wire:target="cover_upload" class="mt-2 text-sm text-slate-500">
                                Bild wird hochgeladen...
                            </div>

                            @error('cover_upload')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-medium text-slate-500">
                                Oder vorhandene Bild-URL / Pfad eintragen
                            </label>

                            <input type="text" wire:model.live="cover_image"
                                class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                                placeholder="/storage/portfolio/bild.jpg oder https://...">

                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($cover_upload)
                            <div class="overflow-hidden rounded-xl border border-slate-200">
                                <img src="{{ $cover_upload->temporaryUrl() }}" alt="Neues Cover"
                                    class="h-48 w-full object-cover">
                            </div>
                        @elseif($cover_image)
                            <div class="overflow-hidden rounded-xl border border-slate-200">
                                <img src="{{ $cover_image }}" alt="Aktuelles Cover" class="h-48 w-full object-cover">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Badges</label>
                        <input type="text" wire:model.live="badges_input"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                            placeholder="Laravel, Livewire, Tailwind, PDF">
                        <p class="mt-1 text-xs text-slate-500">Kommagetrennt eingeben.</p>
                        @error('badges_input')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">CTA-Link</label>
                        <input type="text" wire:model.live="cta_link"
                            class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                            placeholder="https://ruddattech.de/...">
                        @error('cta_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        @if ($editingId)
                            <button type="button" wire:click="resetForm"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Abbrechen
                            </button>
                        @endif

                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            {{ $editingId ? 'Speichern' : 'Anlegen' }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- VORSCHAU --}}
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Vorschau</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Direkte Karten-Vorschau beim Bearbeiten.
                </p>

                <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="aspect-video bg-slate-100">
                        @if ($cover_image)
                            <img src="{{ $cover_image }}" alt="" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-slate-400">
                                Kein Cover-Bild
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4 p-5">
                        <div class="flex flex-wrap gap-2">
                            @if ($type)
                                <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">
                                    {{ $type }}
                                </span>
                            @endif

                            @if ($category)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    {{ $category }}
                                </span>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-slate-900">
                                {{ $title ?: 'Noch kein Titel' }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                {{ $summary ?: 'Hier erscheint die Kurzbeschreibung.' }}
                            </p>
                        </div>

                        @php
                            $previewBadges = collect(explode(',', $badges_input))
                                ->map(fn($badge) => trim($badge))
                                ->filter()
                                ->take(10);
                        @endphp

                        @if ($previewBadges->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($previewBadges as $badge)
                                    <span
                                        class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $badge }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @if ($cta_link)
                            <a href="{{ $cta_link }}" target="_blank"
                                class="inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                Öffnen
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-3">
            <input type="text" wire:model.live.debounce.300ms="search"
                class="rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900"
                placeholder="Suchen nach Titel, Text oder Kategorie...">

            <select wire:model.live="filterCategory"
                class="rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">Alle Kategorien</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterType"
                class="rounded-xl border-slate-300 shadow-sm focus:border-slate-900 focus:ring-slate-900">
                <option value="">Alle Typen</option>
                @foreach ($types as $existingType)
                    <option value="{{ $existingType }}">{{ $existingType }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- LISTE --}}
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($items as $item)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="aspect-video bg-slate-100">
                    @if ($item->cover_image)
                        <img src="{{ $item->cover_image }}" alt="{{ $item->title }}"
                            class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-slate-400">
                            Kein Cover
                        </div>
                    @endif
                </div>

                <div class="space-y-4 p-5">
                    <div class="flex flex-wrap gap-2">
                        @if ($item->type)
                            <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold text-white">
                                {{ $item->type }}
                            </span>
                        @endif

                        @if ($item->category)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                {{ $item->category }}
                            </span>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ $item->title }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ \Illuminate\Support\Str::limit($item->summary, 160) }}
                        </p>
                    </div>

                    @if (!empty($item->badges))
                        <div class="flex flex-wrap gap-2">
                            @foreach (array_slice($item->badges, 0, 8) as $badge)
                                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                    {{ $badge }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                        <button type="button" wire:click="edit({{ $item->id }})"
                            class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Bearbeiten
                        </button>

                        @if ($item->cta_link)
                            <a href="{{ $item->cta_link }}" target="_blank"
                                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Öffnen
                            </a>
                        @endif

                        <button type="button" wire:click="delete({{ $item->id }})"
                            wire:confirm="Diesen Portfolio-Eintrag wirklich löschen?"
                            class="rounded-xl border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">
                            Löschen
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <h3 class="text-base font-semibold text-slate-900">Keine Portfolio-Einträge gefunden</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Erstelle den ersten Eintrag oder ändere die Filter.
                </p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $items->links() }}
    </div>
</div>
