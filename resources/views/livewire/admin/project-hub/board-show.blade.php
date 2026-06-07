<div class="p-6 space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.projecthub.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                ← Zurück zu ProjectHub
            </a>

            <h1 class="text-2xl font-bold text-gray-900 mt-2">
                {{ $boardData->title }}
            </h1>

            @if ($boardData->description)
                <p class="text-sm text-gray-500 mt-1">
                    {{ $boardData->description }}
                </p>
            @endif
        </div>

        <div class="text-right">
            @if ($boardData->client_name)
                <p class="text-sm font-semibold text-gray-700">{{ $boardData->client_name }}</p>
            @endif

            @if ($boardData->client_email)
                <p class="text-sm text-gray-500">{{ $boardData->client_email }}</p>
            @endif
        </div>
    </div>

@if (session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="font-bold text-gray-900">Kundenfreigabe</h2>
            <p class="text-sm text-gray-500 mt-1">
                Erzeuge einen geschützten Link, den du dem Kunden schicken kannst.
            </p>
        </div>

        @if ($boardData->activeShare)
            <span class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700">
                aktiv
            </span>
        @else
            <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500">
                nicht aktiv
            </span>
        @endif
    </div>

    @if ($boardData->activeShare)
        <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kundenlink
            </label>

            <div class="flex gap-3">
                <input type="text"
                       readonly
                       value="{{ route('project-share.show', $boardData->activeShare->token) }}"
                       class="flex-1 rounded-xl border-gray-300 bg-white text-sm">

                <button type="button"
                        onclick="navigator.clipboard.writeText('{{ route('project-share.show', $boardData->activeShare->token) }}')"
                        class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500">
                    Kopieren
                </button>

                <button type="button"
                        wire:click="disableShareLink"
                        wire:confirm="Kundenlink wirklich deaktivieren?"
                        class="px-4 py-2 rounded-xl border border-red-200 text-red-600 font-semibold hover:bg-red-50">
                    Deaktivieren
                </button>
            </div>

            <div class="flex flex-wrap gap-3 mt-3 text-xs text-gray-500">
                <span>Berechtigung: {{ $boardData->activeShare->permission }}</span>

                @if ($boardData->activeShare->expires_at)
                    <span>Gültig bis: {{ $boardData->activeShare->expires_at->format('d.m.Y H:i') }}</span>
                @else
                    <span>Kein Ablaufdatum</span>
                @endif
            </div>
        </div>
    @else
        <form wire:submit.prevent="createShareLink" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Berechtigung
                </label>

                <select wire:model.defer="sharePermission"
                        class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="view">Nur ansehen</option>
                    <option value="comment">Ansehen + kommentieren</option>
                    <option value="upload">Ansehen + kommentieren + Upload</option>
                    <option value="approve">Ansehen + kommentieren + freigeben</option>
                </select>

                @error('sharePermission')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ablaufdatum optional
                </label>

                <input type="datetime-local"
                       wire:model.defer="shareExpiresAt"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                @error('shareExpiresAt')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                    Kundenlink erstellen
                </button>
            </div>
        </form>
    @endif
</div>


    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form wire:submit.prevent="createList" class="flex gap-3">
            <input type="text"
                   wire:model.defer="newListTitle"
                   placeholder="Neue Liste, z.B. Wartet auf Kunde"
                   class="flex-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

            <button type="submit"
                    class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                Liste hinzufügen
            </button>
        </form>

        @error('newListTitle')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <div class="overflow-x-auto pb-4">
        <div class="flex gap-5 min-w-max">
            @foreach ($boardData->lists as $list)
                <div class="w-80 bg-gray-50 rounded-2xl border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-bold text-gray-900">
                            {{ $list->title }}
                        </h2>

                        <span class="text-xs px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-500">
                            {{ $list->cards->count() }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($list->cards as $card)
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                                <button wire:click="openCard({{ $card->id }})"
                                        class="text-left w-full">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="font-semibold text-gray-900">
                                            {{ $card->title }}
                                        </h3>

                                        <span class="text-xs px-2 py-1 rounded-full
                                            @if($card->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($card->priority === 'high') bg-orange-100 text-orange-700
                                            @elseif($card->priority === 'low') bg-gray-100 text-gray-500
                                            @else bg-blue-100 text-blue-700
                                            @endif">
                                            {{ $card->priority }}
                                        </span>
                                    </div>

                                    @if ($card->description)
                                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                            {{ $card->description }}
                                        </p>
                                    @endif

                                    @if ($card->due_date)
                                        <p class="text-xs text-gray-400 mt-3">
                                            Fällig: {{ $card->due_date->format('d.m.Y') }}
                                        </p>
                                    @endif
                                </button>

                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <select wire:change="moveCard({{ $card->id }}, $event.target.value)"
                                            class="text-xs rounded-lg border-gray-300">
                                        @foreach ($boardData->lists as $targetList)
                                            <option value="{{ $targetList->id }}" @selected($targetList->id === $list->id)>
                                                {{ $targetList->title }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button wire:click="deleteCard({{ $card->id }})"
                                            wire:confirm="Karte wirklich löschen?"
                                            class="text-xs text-red-600 hover:text-red-500">
                                        Löschen
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form wire:submit.prevent="createCard({{ $list->id }})" class="mt-4 space-y-2">
                        <input type="text"
                               wire:model.defer="newCardTitle.{{ $list->id }}"
                               placeholder="Neue Karte"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                        <button type="submit"
                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Karte hinzufügen
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    @if ($editingCardId)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Karte bearbeiten</h2>

                    <button wire:click="closeCard" class="text-gray-400 hover:text-gray-600">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveCard" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                        <input type="text"
                               wire:model.defer="editingTitle"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('editingTitle') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                        <textarea wire:model.defer="editingDescription"
                                  rows="5"
                                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('editingDescription') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priorität</label>
                            <select wire:model.defer="editingPriority"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Niedrig</option>
                                <option value="normal">Normal</option>
                                <option value="high">Hoch</option>
                                <option value="urgent">Dringend</option>
                            </select>
                            @error('editingPriority') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fällig am</label>
                            <input type="date"
                                   wire:model.defer="editingDueDate"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('editingDueDate') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4">
                        <button type="button"
                                wire:click="closeCard"
                                class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold">
                            Abbrechen
                        </button>

                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500">
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
