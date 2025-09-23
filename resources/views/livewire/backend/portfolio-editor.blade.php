<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Portfolio verwalten</h2>

    <!-- Formular -->
<!-- Formular -->
<form wire:submit.prevent="save" class="space-y-4 bg-white p-6 rounded-lg shadow">

    <!-- Titel -->
    <label class="block text-sm font-medium text-gray-700">Titel</label>
    <input type="text" wire:model="title" placeholder="Titel"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500">

    <!-- Kurzbeschreibung -->
    <label class="block text-sm font-medium text-gray-700">Kurzbeschreibung</label>
    <textarea wire:model="summary" placeholder="Kurzbeschreibung"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500"></textarea>

    <!-- Lange Beschreibung -->
    <label class="block text-sm font-medium text-gray-700">Lange Beschreibung</label>
    <textarea wire:model="description" placeholder="Lange Beschreibung (Markdown möglich)"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500"></textarea>

    <!-- Badges -->
    <label class="block text-sm font-medium text-gray-700">Badges</label>
    <input type="text" wire:model.lazy="badges" x-data
        x-on:input="$wire.badges = $event.target.value.split(',').map(i => i.trim())"
        placeholder="Badges (z.B. Laravel, Tailwind)"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500">

    <!-- Cover -->
    @if ($coverImage)
        <div class="relative inline-block mb-2">
            <img src="{{ Storage::url($coverImage) }}" class="h-32 rounded">
            <button wire:click="deleteCover" type="button"
                class="btn btn-xs btn-error absolute top-1 right-1">x</button>
        </div>
    @endif
    <input type="file" wire:model="newCover"
        class="block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4
               file:rounded-md file:border-0
               file:text-sm file:font-semibold
               file:bg-pink-50 file:text-pink-600
               hover:file:bg-pink-100">

    <!-- Gallery -->
    <label class="block text-sm font-medium text-gray-700">Bilderstrecke</label>
    <div class="grid grid-cols-3 gap-2 mb-2">
        @foreach ($gallery as $g)
            <div class="relative" wire:key="gallery-{{ $g['id'] }}">
                <img src="{{ Storage::url($g['path']) }}"
                    class="h-24 w-full object-cover rounded">
                <button wire:click="deleteImage({{ $g['id'] }})" type="button"
                    class="btn btn-xs btn-error absolute top-1 right-1">x</button>
            </div>
        @endforeach
    </div>
    <input type="file" wire:model="newGallery" multiple
        class="block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4
               file:rounded-md file:border-0
               file:text-sm file:font-semibold
               file:bg-pink-50 file:text-pink-600
               hover:file:bg-pink-100">

    <!-- Typ -->
    <label class="block text-sm font-medium text-gray-700">Typ</label>
    <select wire:model="type"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500">
        <option value="showcase">Showcase (Referenz)</option>
        <option value="module">Modul (Paperkram)</option>
    </select>

    <!-- CTA -->
    <label class="block text-sm font-medium text-gray-700">Link zum Modul</label>
    <input type="text" wire:model="cta_link"
        placeholder="Link zum Modul oder Registrierung"
        class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
               focus:border-pink-500 focus:ring-pink-500">

<!-- Kategorie -->
<label class="block text-sm font-medium text-gray-700">Kategorie</label>
<select wire:model="category"
    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
           focus:border-pink-500 focus:ring-pink-500">
    <option value="all">Alle</option>
    <option value="app">Apps</option>
    <option value="product">Produkte</option>
    <option value="branding">Branding</option>
    <option value="books">Books</option>
    <option value="web">Web / Plattformen</option>
    <option value="kundenprojekt">Kundenprojekte</option>
    <option value="marketplace">Marketplaces</option>
    <option value="travel">Travel / Data</option>
</select>


    <!-- Button -->
    <button class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow
                   hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
        Speichern
    </button>
</form>

    <!-- Liste -->
    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($items as $item)
            <div class="card bg-base-100 shadow-lg">
                <figure>
                    <img src="{{ Storage::url($item->cover_image) }}"
                        class="h-40 w-full object-cover">
                </figure>
                <div class="card-body">
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <p class="text-sm">{{ $item->summary }}</p>
                    <div class="flex gap-2 flex-wrap mt-2">
                        @foreach ($item->badges ?? [] as $badge)
                            <span class="badge badge-outline">{{ $badge }}</span>
                        @endforeach
                    </div>
                    <button wire:click="edit({{ $item->id }})"
                        class="btn btn-sm mt-4">Bearbeiten</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
