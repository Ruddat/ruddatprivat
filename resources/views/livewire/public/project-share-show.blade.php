<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-indigo-600">Projektfreigabe</p>

                    <h1 class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $board->title }}
                    </h1>

                    @if ($board->description)
                        <p class="text-gray-600 mt-2">
                            {{ $board->description }}
                        </p>
                    @endif
                </div>

                @if ($board->client_name)
                    <div class="rounded-2xl bg-gray-50 border border-gray-200 px-4 py-3">
                        <p class="text-xs text-gray-500">Kunde</p>
                        <p class="font-semibold text-gray-900">{{ $board->client_name }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($canComment || $canUpload)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Dein Name für Kommentare, Uploads und neue Karten
                </label>

                <input type="text"
                       wire:model.defer="visitorName"
                       placeholder="z.B. Herr Müller"
                       class="w-full md:w-96 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

                @error('visitorName')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="overflow-x-auto pb-4">
            <div class="flex gap-5 min-w-max">
                @foreach ($board->lists as $list)
                    <div class="w-80 bg-gray-50 rounded-2xl border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-gray-900">
                                {{ $list->title }}
                            </h2>

                            <span class="text-xs px-2 py-1 rounded-full bg-white border border-gray-200 text-gray-500">
                                {{ $list->cards->count() }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            @forelse ($list->cards as $card)
                                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <h3 class="font-bold text-gray-900">
                                            {{ $card->title }}
                                        </h3>

                                        @if ($card->approved_at)
                                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                                freigegeben
                                            </span>
                                        @else
                                            <span class="text-xs px-2 py-1 rounded-full
                                                @if($card->priority === 'urgent') bg-red-100 text-red-700
                                                @elseif($card->priority === 'high') bg-orange-100 text-orange-700
                                                @elseif($card->priority === 'low') bg-gray-100 text-gray-500
                                                @else bg-blue-100 text-blue-700
                                                @endif">
                                                {{ $card->priority }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($card->description)
                                        <div class="prose prose-sm max-w-none text-gray-600 mt-3">
                                            {!! nl2br(e($card->description)) !!}
                                        </div>
                                    @endif

                                    @if ($card->due_date)
                                        <p class="text-xs text-gray-400 mt-3">
                                            Fällig: {{ $card->due_date->format('d.m.Y') }}
                                        </p>
                                    @endif

                                    @if ($card->attachments->count())
                                        <div class="mt-4 border-t border-gray-100 pt-3 space-y-3">
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Dateien</p>

                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach ($card->attachments as $attachment)
                                                    @if ($attachment->is_image)
                                                        <a href="{{ $attachment->url }}" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                                            <img src="{{ $attachment->url }}" alt="{{ $attachment->original_name }}" class="w-full h-28 object-cover">
                                                            <div class="p-2">
                                                                <p class="text-xs font-medium text-gray-700 truncate">{{ $attachment->original_name }}</p>
                                                                <p class="text-xs text-gray-400">{{ $attachment->readable_size }}</p>
                                                            </div>
                                                        </a>
                                                    @else
                                                        <a href="{{ $attachment->url }}" target="_blank" class="block rounded-xl border border-gray-200 bg-gray-50 p-3 hover:bg-gray-100">
                                                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $attachment->original_name }}</p>
                                                            <p class="text-xs text-gray-400 mt-1">{{ $attachment->readable_size }}</p>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($card->comments->count())
                                        <div class="mt-4 border-t border-gray-100 pt-3 space-y-3">
                                            <p class="text-xs font-semibold text-gray-500 uppercase">Kommentare</p>

                                            @foreach ($card->comments as $comment)
                                                <div class="rounded-xl bg-gray-50 border border-gray-100 p-3">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <p class="text-sm font-semibold text-gray-800">
                                                            {{ $comment->author_name ?? 'Gast' }}
                                                        </p>

                                                        <p class="text-xs text-gray-400">
                                                            {{ $comment->created_at->format('d.m.Y H:i') }}
                                                        </p>
                                                    </div>

                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ $comment->comment }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($canUpload)
                                        <form wire:submit.prevent="uploadFiles({{ $card->id }})" class="mt-4 space-y-2">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase">
                                                Dateien hochladen
                                            </label>

                                            <input type="file"
                                                   wire:model="uploads.{{ $card->id }}"
                                                   multiple
                                                   class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-2 file:text-white file:font-semibold">

                                            @error('uploads.' . $card->id . '.*')
                                                <p class="text-sm text-red-600">{{ $message }}</p>
                                            @enderror

                                            <button type="submit" class="px-3 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500">
                                                Dateien speichern
                                            </button>
                                        </form>
                                    @endif

                                    @if ($canComment)
                                        <form wire:submit.prevent="addComment({{ $card->id }})" class="mt-4 space-y-2">
                                            <textarea wire:model.defer="commentText.{{ $card->id }}" rows="3" placeholder="Kommentar zu dieser Karte schreiben..." class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>

                                            @error('commentText.' . $card->id)
                                                <p class="text-sm text-red-600">{{ $message }}</p>
                                            @enderror

                                            <div class="flex items-center justify-between gap-2">
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                                                    Kommentar speichern
                                                </button>

                                                @if ($canApprove && ! $card->approved_at)
                                                    <button type="button" wire:click="approveCard({{ $card->id }})" wire:confirm="Diese Aufgabe wirklich freigeben?" class="px-3 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-500">
                                                        Freigeben
                                                    </button>
                                                @endif
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-400 text-center">
                                    Keine Karten vorhanden.
                                </div>
                            @endforelse
                        </div>

                        @if ($canCreateCards)
                            <form wire:submit.prevent="createCard({{ $list->id }})" class="mt-4 space-y-2 rounded-xl bg-white border border-gray-200 p-3">
                                <label class="block text-xs font-semibold text-gray-500 uppercase">
                                    Neue Karte vorschlagen
                                </label>

                                <input type="text"
                                       wire:model.defer="newCardTitle.{{ $list->id }}"
                                       placeholder="z.B. Bitte neues Bild einbauen"
                                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                                @error('newCardTitle.' . $list->id)
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                                    Karte anlegen
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-center text-xs text-gray-400 py-4">
            Projektfreigabe über Ruddat ProjectHub
        </div>
    </div>
</div>
