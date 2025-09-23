<div class="space-y-6">
    {{-- Headline --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Systemeinstellungen
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Verwalten Sie hier die globalen Einstellungen für Firma, Limits und weitere Module.
        </p>
    </div>

    {{-- Layout --}}
    <div class="flex flex-col lg:flex-row gap-6">
        {{-- left: groups --}}
        <aside class="bg-white dark:bg-gray-800 rounded shadow w-full lg:w-56">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($groups as $g)
                    <li>
                        <button
                            wire:click="switchGroup('{{ $g }}')"
                            class="w-full text-left px-4 py-2 text-sm transition
                                   {{ $activeGroup === $g
                                        ? 'bg-pink-600 text-white font-semibold'
                                        : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            {{ ucfirst($g) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- right: settings for active group --}}
        <section class="flex-1">
            @if (session()->has('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($activeGroup)
                <form wire:submit.prevent="save" class="space-y-5 bg-white dark:bg-gray-800 p-6 rounded shadow">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        {{ ucfirst($activeGroup) }} Einstellungen
                    </h3>

                    @forelse($items as $item)
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                {{ $item->description ?: ucfirst($item->key) }}
                                <span class="text-gray-400 text-xs">({{ $item->key }})</span>
                            </label>

                            @switch($item->type)
                                @case('number')
                                    <input type="number"
                                           wire:model.defer="values.{{ $item->id }}"
                                           class="form-input w-full rounded border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200">
                                    @break

                                @case('boolean')
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox"
                                               wire:model.defer="values.{{ $item->id }}"
                                               value="1"
                                               @checked((string)($values[$item->id] ?? '') === '1')>
                                        <span>Aktiv</span>
                                    </label>
                                    @break

                                @case('text')
                                    <textarea rows="4"
                                              wire:model.defer="values.{{ $item->id }}"
                                              class="form-input w-full rounded border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200"></textarea>
                                    @break

                                @case('json')
                                    <textarea rows="6"
                                              wire:model.defer="values.{{ $item->id }}"
                                              class="form-input w-full font-mono rounded border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200"></textarea>
                                    @error('values.'.$item->id)
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                    @break

                                @default
                                    <input type="text"
                                           wire:model.defer="values.{{ $item->id }}"
                                           class="form-input w-full rounded border-gray-300 focus:border-pink-500 focus:ring focus:ring-pink-200">
                            @endswitch
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Keine Einstellungen in dieser Gruppe.</p>
                    @endforelse

                    <div>
                        <button type="submit"
                                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded shadow">
                            Speichern
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</div>
