{{-- resources/views/livewire/customer/feedback-form.blade.php --}}
<div class="bg-white p-4 rounded shadow">
    @if (session('success'))
        <div class="p-2 mb-2 text-green-700 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-4">
        <div>
            <label class="form-label">Titel</label>
            <input type="text" wire:model="title" class="form-input">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="form-label">Kategorie</label>
            <select wire:model="category" class="form-select">
                <option value="idea">💡 Idee</option>
                <option value="feature">⚙️ Feature</option>
                <option value="bug">🐞 Bug</option>
            </select>
        </div>

        <div>
            <label class="form-label">Nachricht</label>
            <textarea wire:model="message" class="form-input" rows="4"></textarea>
            @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <button type="submit"
            class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">
            Feedback absenden
        </button>
    </form>
</div>
