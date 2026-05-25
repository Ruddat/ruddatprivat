{{-- resources/views/livewire/backend/portfolio-manager.blade.php --}}
<div class="min-h-screen bg-slate-100 p-4 sm:p-6 lg:p-8">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-2xl">
            <div class="grid gap-8 p-8 lg:grid-cols-[1.1fr_0.9fr] lg:p-10">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-pink-300">Portfolio Studio</p>
                    <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-5xl">Projekte sauber präsentieren.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-300">
                        Verwalte Case Studies, Module, Produkte und Referenzen für die Startseite. Fokus: klare Titel, kurze Nutzenbeschreibung, starke Badges und gutes Vorschaubild.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 self-end">
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 text-center">
                        <div class="text-3xl font-black">{{ $items->count() }}</div>
                        <div class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">Einträge</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 text-center">
                        <div class="text-3xl font-black">{{ $items->where('type', 'module')->count() }}</div>
                        <div class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">Module</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 text-center">
                        <div class="text-3xl font-black">{{ $items->pluck('category')->unique()->count() }}</div>
                        <div class="mt-1 text-xs font-bold uppercase tracking-wider text-slate-400">Kategorien</div>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-8 xl:grid-cols-[0.92fr_1.08fr]">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-600">Editor</p>
                        <h2 class="mt-2 text-2xl font-black text-slate-950">
                            {{ $itemId ? 'Eintrag bearbeiten' : 'Neuen Eintrag erstellen' }}
                        </h2>
                    </div>

                    @if($itemId)
                        <button type="button" wire:click="resetForm" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50">
                            Neu
                        </button>
                    @endif
                </div>

                <form wire:submit.prevent="save" class="space-y-5">
                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-800">Titel</label>
                        <input type="text" wire:model.live="title" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100" placeholder="z.B. Fristenfrei – Dokumente verstehen">
                        @error('title') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-800">Kurzbeschreibung</label>
                        <textarea wire:model.live="summary" rows="3" maxlength="180" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100" placeholder="Kurz, konkret, nutzenorientiert. Wird auf der Startseite angezeigt."></textarea>
                        <div class="mt-1 text-xs font-semibold text-slate-400">Optimal: 110–160 Zeichen.</div>
                        @error('summary') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-800">Kategorie</label>
                            <select wire:model="category" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100">
                                <option value="app">App</option>
                                <option value="product">Produkt</option>
                                <option value="branding">Branding</option>
                                <option value="books">Books</option>
                                <option value="landingpage">Landingpage</option>
                                <option value="automation">Automatisierung</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-black text-slate-800">Typ</label>
                            <select wire:model="type" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100">
                                <option value="project">Projekt / Case</option>
                                <option value="module">Modul / Tool</option>
                                <option value="product">Produkt</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-800">Badges</label>
                        <input type="text" wire:model.live="badgeInput" wire:blur="syncBadgesFromInput" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100" placeholder="Laravel, Livewire, SEO, PayPal">
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($badges as $badge)
                                <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-black text-pink-700 ring-1 ring-pink-100">{{ $badge }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-800">CTA-Link</label>
                        <input type="url" wire:model="cta_link" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100" placeholder="https://... oder leer lassen">
                        @error('cta_link') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-black text-slate-800">Detailbeschreibung</label>
                        <textarea wire:model="description" rows="7" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-950 outline-none transition focus:border-pink-400 focus:ring-4 focus:ring-pink-100" placeholder="Was wurde gebaut? Welches Problem löst es? Welche Technik steckt drin?"></textarea>
                    </div>

                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-5">
                        <label class="mb-3 block text-sm font-black text-slate-800">Coverbild</label>

                        <div class="grid gap-4 sm:grid-cols-[160px_1fr] sm:items-center">
                            <div class="overflow-hidden rounded-2xl bg-slate-200">
                                @if ($newImage)
                                    <img src="{{ $newImage->temporaryUrl() }}" class="h-32 w-full object-cover" alt="Neue Vorschau">
                                @elseif ($cover_image)
                                    <img src="{{ Storage::url($cover_image) }}" class="h-32 w-full object-cover" alt="Aktuelles Coverbild">
                                @else
                                    <div class="flex h-32 items-center justify-center text-sm font-bold text-slate-500">Kein Bild</div>
                                @endif
                            </div>

                            <div>
                                <input type="file" wire:model="newImage" class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                                <p class="mt-2 text-xs font-semibold text-slate-500">Empfohlen: 1200×800px, JPG/WebP/PNG, max. 4 MB.</p>
                                @error('newImage') <span class="mt-2 block text-sm font-semibold text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-pink-600 px-6 py-4 text-sm font-black text-white shadow-lg shadow-pink-600/20 transition hover:bg-pink-500">
                            {{ $itemId ? 'Änderungen speichern' : 'Eintrag speichern' }}
                        </button>
                        <button type="button" wire:click="resetForm" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-6 py-4 text-sm font-black text-slate-700 transition hover:bg-slate-50">
                            Zurücksetzen
                        </button>
                    </div>
                </form>
            </section>

            <section class="space-y-5">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                    <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-600">Live Preview</p>
                    <div class="mt-5 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="h-52 bg-slate-200">
                            @if ($newImage)
                                <img src="{{ $newImage->temporaryUrl() }}" class="h-full w-full object-cover" alt="Preview">
                            @elseif ($cover_image)
                                <img src="{{ Storage::url($cover_image) }}" class="h-full w-full object-cover" alt="Preview">
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-slate-900 to-slate-700 text-white">
                                    <span class="text-lg font-black">{{ $title ?: 'Projektvorschau' }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <div class="mb-3 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $category }}</span>
                                <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-black text-pink-700">{{ $type }}</span>
                            </div>
                            <h3 class="text-2xl font-black text-slate-950">{{ $title ?: 'Titel deines Projekts' }}</h3>
                            <p class="mt-3 leading-7 text-slate-600">{{ $summary ?: 'Hier erscheint die kurze Beschreibung, die später auf der Startseite verkaufen soll.' }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($badges as $badge)
                                    <span class="rounded-full border border-slate-200 px-3 py-1 text-xs font-bold text-slate-600">{{ $badge }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                    <div class="mb-5 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-pink-600">Einträge</p>
                            <h2 class="mt-1 text-2xl font-black text-slate-950">Portfolio-Liste</h2>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse ($items as $item)
                            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50 transition hover:border-pink-200 hover:bg-white hover:shadow-lg">
                                <div class="grid gap-4 p-4 sm:grid-cols-[130px_1fr]">
                                    <div class="overflow-hidden rounded-2xl bg-slate-200">
                                        @if($item->cover_image)
                                            <img src="{{ Storage::url($item->cover_image) }}" alt="{{ $item->title }}" class="h-28 w-full object-cover">
                                        @else
                                            <div class="flex h-28 items-center justify-center text-xs font-bold text-slate-500">Kein Bild</div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <h3 class="text-lg font-black text-slate-950">{{ $item->title }}</h3>
                                                <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-600">{{ $item->summary ?: $item->description }}</p>
                                            </div>
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-600 ring-1 ring-slate-200">{{ $item->category }}</span>
                                        </div>

                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($item->badges ?? [] as $badge)
                                                <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-bold text-slate-600">{{ $badge }}</span>
                                            @endforeach
                                        </div>

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            <button wire:click="edit({{ $item->id }})" class="rounded-full bg-slate-950 px-4 py-2 text-xs font-black text-white transition hover:bg-slate-800">Bearbeiten</button>
                                            <button wire:click="duplicate({{ $item->id }})" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-100">Duplizieren</button>
                                            <button wire:click="delete({{ $item->id }})" wire:confirm="Diesen Portfolio-Eintrag wirklich löschen?" class="rounded-full border border-red-200 bg-red-50 px-4 py-2 text-xs font-black text-red-700 transition hover:bg-red-100">Löschen</button>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                                <h3 class="text-xl font-black text-slate-950">Noch keine Portfolio-Einträge</h3>
                                <p class="mt-2 text-slate-600">Lege links deinen ersten Eintrag an.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
