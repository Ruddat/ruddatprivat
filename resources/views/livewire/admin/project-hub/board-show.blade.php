<div class="space-y-4">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.projecthub.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                &larr; Zur&uuml;ck
            </a>

            <h1 class="text-2xl font-bold text-gray-900 mt-1">
                {{ $boardData->title }}
            </h1>

            @if ($boardData->description)
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $boardData->description }}
                </p>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($boardData->client_name || $boardData->client_email)
                <div class="text-right text-sm mr-2">
                    @if ($boardData->client_name)
                        <p class="font-semibold text-gray-700">{{ $boardData->client_name }}</p>
                    @endif
                    @if ($boardData->client_email)
                        <p class="text-gray-500">{{ $boardData->client_email }}</p>
                    @endif
                </div>
            @endif

            <button wire:click="openEditBoard"
                    class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                Bearbeiten
            </button>

            <button wire:click="deleteBoard"
                    wire:confirm="Board wirklich l&ouml;schen? Alle Listen und Karten werden ebenfalls gel&ouml;scht."
                    class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 text-sm hover:bg-red-50">
                L&ouml;schen
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Collapsible: Kundenfreigabe --}}
    <div x-data="{ open: false }" class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <button @click="open = !open" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold text-gray-900">Kundenfreigabe</h2>
                @if ($boardData->activeShare)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">aktiv</span>
                @else
                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">inaktiv</span>
                @endif
            </div>
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transform transition-transform text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>

        <div x-show="open" x-collapse class="px-3 pb-3">
            @if ($boardData->activeShare)
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                    <div class="flex gap-2">
                        <input type="text"
                               readonly
                               value="{{ route('project-share.show', $boardData->activeShare->token) }}"
                               class="flex-1 rounded-lg border-gray-300 bg-white text-sm text-xs">

                        <button type="button"
                                onclick="navigator.clipboard.writeText('{{ route('project-share.show', $boardData->activeShare->token) }}')"
                                class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">
                            Kopieren
                        </button>

                        <button type="button"
                                wire:click="disableShareLink"
                                wire:confirm="Kundenlink wirklich deaktivieren?"
                                class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50">
                            Deaktivieren
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                        <span>Berechtigung: {{ $boardData->activeShare->permission }}</span>
                        @if ($boardData->activeShare->expires_at)
                            <span>G&uuml;ltig bis: {{ $boardData->activeShare->expires_at->format('d.m.Y H:i') }}</span>
                        @else
                            <span>Kein Ablaufdatum</span>
                        @endif
                    </div>
                </div>
            @else
                <form wire:submit.prevent="createShareLink" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <select wire:model.defer="sharePermission"
                                class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="view">Nur ansehen</option>
                            <option value="comment">Ansehen + kommentieren</option>
                            <option value="upload">Ansehen + kommentieren + Upload</option>
                            <option value="approve">Ansehen + kommentieren + freigeben</option>
                        </select>
                    </div>

                    <div>
                        <input type="datetime-local"
                               wire:model.defer="shareExpiresAt"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                            Kundenlink erstellen
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Add List (compact) --}}
    <form wire:submit.prevent="createList" class="flex gap-2">
        <input type="text"
               wire:model.defer="newListTitle"
               placeholder="Neue Liste hinzuf&uuml;gen..."
               class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">

        <button type="submit"
                class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
            +
        </button>
    </form>

    @error('newListTitle')
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror

    {{-- Board Columns --}}
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max items-start">
            @foreach ($boardData->lists as $list)
                <div class="w-72 bg-gray-50 rounded-xl border border-gray-200 flex flex-col max-h-[calc(100vh-220px)]">
                    {{-- List Header --}}
                    <div class="flex items-center justify-between p-3 border-b border-gray-200 flex-shrink-0">
                        <h2 class="font-bold text-gray-900 text-sm">
                            {{ $list->title }}
                        </h2>

                        <div class="flex items-center gap-1.5">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-500">
                                {{ $list->cards->count() }}
                            </span>

                            <button wire:click="openEditList({{ $list->id }})" class="text-gray-400 hover:text-gray-600" title="Bearbeiten">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <button wire:click="deleteList({{ $list->id }})" wire:confirm="Liste wirklich l&ouml;schen?" class="text-gray-400 hover:text-red-500" title="L&ouml;schen">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Cards (scrollable, draggable) --}}
                    <div class="space-y-2 p-2 overflow-y-auto flex-1"
                         data-projecthub-list-id="{{ $list->id }}">
                        @foreach ($list->cards as $card)
                            @php
                                $cardBorderClass = match($card->priority) {
                                    'urgent' => 'border-l-4 border-l-red-500 bg-red-50/40',
                                    'high' => 'border-l-4 border-l-orange-400 bg-orange-50/40',
                                    'low' => 'border-l-4 border-l-gray-300 bg-gray-50/60',
                                    default => 'border-l-4 border-l-blue-400 bg-blue-50/30',
                                };
                            @endphp
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 cursor-grab {{ $cardBorderClass }}"
                                 data-projecthub-card-id="{{ $card->id }}">
                                <button wire:click="openCard({{ $card->id }})"
                                        class="text-left w-full">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-gray-900 text-sm">
                                            {{ $card->title }}
                                        </h3>

                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            @if ($card->approved_at)
                                                <span class="text-xs px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">OK</span>
                                            @endif

                                            <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                                @if($card->priority === 'urgent') bg-red-100 text-red-700
                                                @elseif($card->priority === 'high') bg-orange-100 text-orange-700
                                                @elseif($card->priority === 'low') bg-gray-100 text-gray-500
                                                @else bg-blue-100 text-blue-700
                                                @endif">
                                                @if($card->priority === 'urgent') Dringend
                                                @elseif($card->priority === 'high') Hoch
                                                @elseif($card->priority === 'low') Niedrig
                                                @else Normal
                                                @endif
                                            </span>
                                        </div>
                                    </div>

                                    @if ($card->description)
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                            {{ $card->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                                        @if ($card->due_date)
                                            <span>{{ $card->due_date->format('d.m.Y') }}</span>
                                        @endif

                                        @if ($card->comments->count())
                                            <span>{{ $card->comments->count() }} K</span>
                                        @endif

                                        @if ($card->attachments->count())
                                            <span>{{ $card->attachments->count() }} D</span>
                                        @endif
                                    </div>
                                </button>

                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <select wire:change="moveCard({{ $card->id }}, $event.target.value)"
                                            class="text-xs rounded-md border-gray-300 py-0.5">
                                        @foreach ($boardData->lists as $targetList)
                                            <option value="{{ $targetList->id }}" @selected($targetList->id === $list->id)>
                                                {{ $targetList->title }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button wire:click="deleteCard({{ $card->id }})"
                                            wire:confirm="Karte wirklich l&ouml;schen?"
                                            class="text-xs text-red-500 hover:text-red-400">
                                        L&ouml;schen
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Add Card (compact, at bottom) --}}
                    <form wire:submit.prevent="createCard({{ $list->id }})" class="p-2 border-t border-gray-200 flex-shrink-0">
                        <div class="flex gap-2">
                            <input type="text"
                                   wire:model.defer="newCardTitle.{{ $list->id }}"
                                   placeholder="Neue Karte..."
                                   class="flex-1 rounded-lg border-gray-300 text-sm">
                            <button type="submit"
                                    class="px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-sm text-gray-700 hover:bg-gray-100">
                                +
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Card Edit Modal --}}
    @if ($editingCardId)
        @php
            $editingCard = $boardData->lists->flatMap->cards->first(fn ($c) => $c->id === $editingCardId);
        @endphp

        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" wire:click.self="closeCard">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Karte bearbeiten</h2>
                    <button wire:click="closeCard" class="text-gray-400 hover:text-gray-600">
                        &#10005;
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
                                  rows="3"
                                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        @error('editingDescription') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priorit&auml;t</label>
                            <select wire:model.defer="editingPriority"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="low">Niedrig</option>
                                <option value="normal">Normal</option>
                                <option value="high">Hoch</option>
                                <option value="urgent">Dringend</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">F&auml;llig am</label>
                            <input type="date"
                                   wire:model.defer="editingDueDate"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    @if ($editingCard && $editingCard->approved_at)
                        <div class="rounded-lg bg-green-50 border border-green-200 px-3 py-2 text-green-700 text-sm">
                            Freigegeben am {{ $editingCard->approved_at->format('d.m.Y H:i') }}
                        </div>
                    @else
                        <button type="button" wire:click="approveCard"
                                class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-500">
                            Karte freigeben
                        </button>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-3">
                        <button type="button" wire:click="closeCard"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 font-semibold">
                            Abbrechen
                        </button>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-500">
                            Speichern
                        </button>
                    </div>
                </form>

                @if ($editingCard)
                    {{-- Attachments --}}
                    <div class="mt-5 border-t pt-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Dateien ({{ $editingCard->attachments->count() }})</h3>

                        @if ($editingCard->attachments->count())
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                @foreach ($editingCard->attachments as $attachment)
                                    @if ($attachment->is_image)
                                        <a href="{{ $attachment->url }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" class="w-full h-24 object-cover">
                                            <div class="p-1.5">
                                                <p class="text-xs font-medium text-gray-700 truncate">{{ $attachment->original_name }}</p>
                                                <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ $attachment->url }}" target="_blank" class="block rounded-lg border border-gray-200 bg-gray-50 p-2 hover:bg-gray-100">
                                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $attachment->original_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <form wire:submit.prevent="uploadFiles({{ $editingCard->id }})" class="space-y-2">
                            <input type="file"
                                   wire:model="uploads.{{ $editingCard->id }}"
                                   multiple
                                   class="block w-full text-sm text-gray-600 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1.5 file:text-white file:font-semibold">
                            @error('uploads.' . $editingCard->id . '.*')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">
                                Dateien speichern
                            </button>
                        </form>
                    </div>

                    {{-- Comments --}}
                    <div class="mt-5 border-t pt-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-2">Kommentare ({{ $editingCard->comments->count() }})</h3>

                        @if ($editingCard->comments->count())
                            <div class="space-y-2 mb-3 max-h-48 overflow-y-auto">
                                @foreach ($editingCard->comments as $comment)
                                    <div class="rounded-lg bg-gray-50 border border-gray-100 p-2.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-semibold text-gray-800">{{ $comment->author_name ?? 'Gast' }}</p>
                                            <p class="text-xs text-gray-400">{{ $comment->created_at->format('d.m.Y H:i') }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $comment->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form wire:submit.prevent="addComment" class="space-y-2">
                            <textarea wire:model.defer="editingComment" rows="2" placeholder="Kommentar schreiben..." class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                            @error('editingComment')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                                Kommentar speichern
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Edit Board Modal --}}
    @if ($showEditBoardModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" wire:click.self="$toggle('showEditBoardModal')">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Board bearbeiten</h2>
                    <button wire:click="$toggle('showEditBoardModal')" class="text-gray-400 hover:text-gray-600">&#10005;</button>
                </div>

                <form wire:submit.prevent="saveBoard" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                        <input type="text" wire:model.defer="editBoardTitle"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('editBoardTitle') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                        <textarea wire:model.defer="editBoardDescription" rows="2"
                                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kunde</label>
                            <input type="text" wire:model.defer="editBoardClientName"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kunden-E-Mail</label>
                            <input type="email" wire:model.defer="editBoardClientEmail"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-3">
                        <button type="button" wire:click="$toggle('showEditBoardModal')"
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

    {{-- Edit List Modal --}}
    @if ($showEditListModal)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" wire:click.self="$toggle('showEditListModal')">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Liste bearbeiten</h2>
                    <button wire:click="$toggle('showEditListModal')" class="text-gray-400 hover:text-gray-600">&#10005;</button>
                </div>

                <form wire:submit.prevent="saveList" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Listenname</label>
                        <input type="text" wire:model.defer="editingListTitle"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('editingListTitle') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-3">
                        <button type="button" wire:click="$toggle('showEditListModal')"
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
