<div class="space-y-4">
    {{-- Header --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <p class="text-xs font-semibold text-indigo-600">Projektfreigabe</p>
                <h1 class="text-2xl font-bold text-gray-900 mt-0.5">{{ $board->title }}</h1>
                @if ($board->description)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $board->description }}</p>
                @endif
            </div>

            @if ($board->client_name)
                <div class="rounded-lg bg-gray-50 border border-gray-200 px-3 py-2 text-sm">
                    <p class="text-xs text-gray-500">Kunde</p>
                    <p class="font-semibold text-gray-900">{{ $board->client_name }}</p>
                </div>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($canComment || $canUpload)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3">
            <label class="block text-xs font-medium text-gray-700 mb-1">
                Dein Name
            </label>
            <input type="text"
                   wire:model.defer="visitorName"
                   placeholder="z.B. Herr M&uuml;ller"
                   class="w-full md:w-72 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('visitorName')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    @endif

    @if ($canDrag)
        <p class="text-xs text-gray-400 text-center">
            Karten kannst du per Drag &amp; Drop zwischen die Listen verschieben.
        </p>
    @endif

    {{-- Board Columns --}}
    <div class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max items-start">
            @foreach ($board->lists as $list)
                <div class="w-72 bg-gray-50 rounded-xl border border-gray-200 flex flex-col max-h-[calc(100vh-200px)]">
                    {{-- List Header --}}
                    <div class="flex items-center justify-between p-3 border-b border-gray-200 flex-shrink-0">
                        <h2 class="font-bold text-gray-900 text-sm">{{ $list->title }}</h2>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-white border border-gray-200 text-gray-500">
                            {{ $list->cards->count() }}
                        </span>
                    </div>

                    {{-- Cards (scrollable, draggable) --}}
                    <div class="space-y-2 p-2 overflow-y-auto flex-1 {{ $canDrag ? 'projecthub-sortable' : '' }}"
                         data-projecthub-list-id="{{ $list->id }}">
                        @forelse ($list->cards as $card)
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-3 {{ $canDrag ? 'cursor-grab' : '' }}"
                                 data-projecthub-card-id="{{ $card->id }}">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-semibold text-gray-900 text-sm">{{ $card->title }}</h3>

                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        @if ($card->approved_at)
                                            <span class="text-xs px-1.5 py-0.5 rounded-full bg-green-100 text-green-700">OK</span>
                                        @else
                                            <span class="text-xs px-1.5 py-0.5 rounded-full
                                                @if($card->priority === 'urgent') bg-red-100 text-red-700
                                                @elseif($card->priority === 'high') bg-orange-100 text-orange-700
                                                @elseif($card->priority === 'low') bg-gray-100 text-gray-500
                                                @else bg-blue-100 text-blue-700
                                                @endif">
                                                {{ $card->priority }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($card->description)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                        {{ $card->description }}
                                    </p>
                                @endif

                                @if ($card->due_date)
                                    <p class="text-xs text-gray-400 mt-1">
                                        F&auml;llig: {{ $card->due_date->format('d.m.Y') }}
                                    </p>
                                @endif

                                @if ($card->attachments->count())
                                    <div class="mt-2 border-t border-gray-100 pt-2">
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Dateien</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            @foreach ($card->attachments as $attachment)
                                                @if ($attachment->is_image)
                                                    <a href="{{ $attachment->url }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                                                        <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" class="w-full h-20 object-cover">
                                                        <div class="p-1">
                                                            <p class="text-xs text-gray-700 truncate">{{ $attachment->original_name }}</p>
                                                            <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                                        </div>
                                                    </a>
                                                @else
                                                    <a href="{{ $attachment->url }}" target="_blank" class="block rounded-lg border border-gray-200 bg-gray-50 p-2 hover:bg-gray-100">
                                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $attachment->original_name }}</p>
                                                        <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($card->comments->count())
                                    <div class="mt-2 border-t border-gray-100 pt-2 space-y-1.5">
                                        <p class="text-xs font-semibold text-gray-500 uppercase">Kommentare</p>
                                        @foreach ($card->comments as $comment)
                                            <div class="rounded-lg bg-gray-50 border border-gray-100 p-2">
                                                <div class="flex items-center justify-between gap-1">
                                                    <p class="text-xs font-semibold text-gray-800">{{ $comment->author_name ?? 'Gast' }}</p>
                                                    <p class="text-xs text-gray-400">{{ $comment->created_at->format('d.m.Y H:i') }}</p>
                                                </div>
                                                <p class="text-xs text-gray-600 mt-0.5">{{ $comment->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Actions per card --}}
                                <div class="mt-2 pt-2 border-t border-gray-100 space-y-2">
                                    @if ($canUpload)
                                        <form wire:submit.prevent="uploadFiles({{ $card->id }})" class="space-y-1">
                                            <input type="file"
                                                   wire:model="uploads.{{ $card->id }}"
                                                   multiple
                                                   class="block w-full text-xs text-gray-600 file:mr-2 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-2 file:py-1 file:text-white file:font-semibold file:text-xs">
                                            @error('uploads.' . $card->id . '.*')
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-500">
                                                Dateien speichern
                                            </button>
                                        </form>
                                    @endif

                                    @if ($canComment)
                                        <form wire:submit.prevent="addComment({{ $card->id }})" class="space-y-1">
                                            <textarea wire:model.defer="commentText.{{ $card->id }}" rows="2" placeholder="Kommentar..." class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            @error('commentText.' . $card->id)
                                                <p class="text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                            <div class="flex items-center gap-2">
                                                <button type="submit" class="px-2 py-1 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800">
                                                    Kommentar
                                                </button>
                                                @if ($canApprove && ! $card->approved_at)
                                                    <button type="button" wire:click="approveCard({{ $card->id }})" wire:confirm="Freigeben?" class="px-2 py-1 rounded-lg bg-green-600 text-white text-xs font-semibold hover:bg-green-500">
                                                        Freigeben
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 p-3 text-xs text-gray-400 text-center">
                                Keine Karten
                            </div>
                        @endforelse
                    </div>

                    {{-- Add Card (compact, at bottom) --}}
                    @if ($canCreateCards)
                        <form wire:submit.prevent="createCard({{ $list->id }})" class="p-2 border-t border-gray-200 flex-shrink-0">
                            <div class="flex gap-2">
                                <input type="text"
                                       wire:model.defer="newCardTitle.{{ $list->id }}"
                                       placeholder="Neue Karte..."
                                       class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                    +
                                </button>
                            </div>
                            @error('newCardTitle.' . $list->id)
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="text-center text-xs text-gray-400 py-2">
        Projektfreigabe &uuml;ber Ruddat ProjectHub
    </div>
</div>
