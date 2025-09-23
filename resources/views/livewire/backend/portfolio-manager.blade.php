{{-- resources/views/livewire/backend/portfolio-manager.blade.php --}}
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Portfolio verwalten</h2>

    <!-- Formular -->
    <form wire:submit.prevent="save" class="space-y-4 bg-base-200 p-4 rounded-lg">
        <div>
            <label class="block font-semibold">Titel</label>
            <input type="text" wire:model="title" class="input input-bordered w-full">
            @error("title")
                <span class="text-error">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block font-semibold">Beschreibung</label>
            <textarea wire:model="description" class="textarea textarea-bordered w-full"></textarea>
        </div>

        <div>
            <label class="block font-semibold">Kategorie</label>
            <select wire:model="category" class="select select-bordered w-full">
                <option value="app">App</option>
                <option value="product">Produkt</option>
                <option value="branding">Branding</option>
                <option value="books">Books</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold">Badges (Technologien, Komma-getrennt)</label>
            <input type="text" wire:model.lazy="badges" x-data
                x-on:input="$wire.badges = $event.target.value.split(',').map(i => i.trim())"
                class="input input-bordered w-full">
            <div class="mt-2 flex gap-2 flex-wrap">
                @foreach ($badges as $badge)
                    <span class="badge badge-primary">{{ $badge }}</span>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block font-semibold">Bild</label>
            @if ($image)
                <img src="{{ Storage::url($image) }}" class="h-32 mb-2 rounded">
            @endif
            <input type="file" wire:model="newImage"
                class="file-input file-input-bordered w-full">
            @error("newImage")
                <span class="text-error">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>

    <!-- Liste -->
    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($items as $item)
            <div class="card bg-base-100 shadow-lg">
                <figure>
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                        class="h-40 w-full object-cover">
                </figure>
                <div class="card-body">
                    <h3 class="card-title">{{ $item->title }}</h3>
                    <p class="text-sm text-base-content/70">{{ $item->description }}</p>
                    <div class="flex gap-2 flex-wrap mt-2">
                        @foreach ($item->badges ?? [] as $badge)
                            <span class="badge badge-outline">{{ $badge }}</span>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button wire:click="edit({{ $item->id }})"
                            class="btn btn-sm">Bearbeiten</button>
                        <button wire:click="delete({{ $item->id }})"
                            class="btn btn-sm btn-error">Löschen</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
