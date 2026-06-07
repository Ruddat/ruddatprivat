<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">ProjectHub</h1>
            <p class="text-sm text-gray-500">Boards f&uuml;r interne Projekte und sp&auml;tere Kundenfreigaben.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h2 class="font-semibold text-gray-900 mb-4">Neues Board erstellen</h2>

        <form wire:submit.prevent="createBoard" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                <input type="text" wire:model.defer="title"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kunde</label>
                <input type="text" wire:model.defer="client_name"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('client_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kunden-E-Mail</label>
                <input type="email" wire:model.defer="client_email"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('client_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                <input type="text" wire:model.defer="description"
                       class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                    Board erstellen
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @forelse ($boards as $board)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $board->title }}
                        </h3>

                        @if ($board->client_name)
                            <p class="text-sm text-gray-500 mt-1">
                                Kunde: {{ $board->client_name }}
                            </p>
                        @endif
                    </div>

                    <button wire:click="toggleBoard({{ $board->id }})"
                            class="text-xs px-3 py-1 rounded-full {{ $board->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $board->is_active ? 'aktiv' : 'inaktiv' }}
                    </button>
                </div>

                @if ($board->description)
                    <p class="text-sm text-gray-600 mt-3">
                        {{ $board->description }}
                    </p>
                @endif

                <div class="flex items-center gap-4 text-sm text-gray-500 mt-4">
                    <span>{{ $board->lists_count }} Listen</span>
                    <span>{{ $board->cards_count }} Karten</span>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <a href="{{ route('admin.projecthub.show', $board) }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-500">
                        Board &ouml;ffnen
                    </a>

                    <button wire:click="deleteBoard({{ $board->id }})"
                            wire:confirm="Board wirklich l&ouml;schen? Alle Listen und Karten werden ebenfalls gel&ouml;scht."
                            class="px-4 py-2 rounded-xl border border-red-200 text-red-600 font-semibold hover:bg-red-50 text-sm">
                        L&ouml;schen
                    </button>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                Noch keine Boards vorhanden.
            </div>
        @endforelse
    </div>
</div>
