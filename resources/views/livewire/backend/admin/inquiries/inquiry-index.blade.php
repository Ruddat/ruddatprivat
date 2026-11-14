<div class="space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Anfragen</h1>
            <p class="mt-1 text-sm text-slate-500">Eingang des Kontaktformulars der Webseite.</p>
        </div>
    </div>

    {{-- Kennzahlen --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <button type="button" wire:click="$set('status', 'new')"
            class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-left transition hover:shadow">
            <p class="text-sm font-medium text-amber-800">Neue Anfragen</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">{{ $stats['new'] }}</p>
        </button>
        <button type="button" wire:click="$set('status', 'open')"
            class="rounded-xl border border-slate-200 bg-white p-5 text-left transition hover:shadow dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Offene Anfragen</p>
            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['open'] }}</p>
        </button>
        <button type="button" wire:click="$set('period', '30'); $set('status', 'all')"
            class="rounded-xl border border-slate-200 bg-white p-5 text-left transition hover:shadow dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Letzte 30 Tage</p>
            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['last30'] }}</p>
        </button>
    </div>

    {{-- Filter --}}
    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-[2fr_1fr_1fr_1fr_auto] dark:border-gray-700 dark:bg-gray-800">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Suche: Name, Firma, E-Mail, Nr. …"
            aria-label="Anfragen durchsuchen"
            class="rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
        <select wire:model.live="status" aria-label="Status" class="rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
            <option value="open">Offen</option>
            <option value="all">Alle</option>
            @foreach ($statuses as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
            @endforeach
        </select>
        <select wire:model.live="type" aria-label="Art der Anfrage" class="rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
            <option value="">Alle Arten</option>
            @foreach ($types as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="period" aria-label="Zeitraum" class="rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
            <option value="">Gesamter Zeitraum</option>
            <option value="7">Letzte 7 Tage</option>
            <option value="30">Letzte 30 Tage</option>
            <option value="90">Letzte 90 Tage</option>
            <option value="365">Letzte 12 Monate</option>
        </select>
        <button type="button" wire:click="resetFilters" class="rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-gray-700">
            Zurücksetzen
        </button>
    </div>

    {{-- Liste --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3">Eingang</th>
                        <th class="px-4 py-3">Nr.</th>
                        <th class="px-4 py-3">Unternehmen / Name</th>
                        <th class="px-4 py-3">Art</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                    @forelse ($inquiries as $inquiry)
                        <tr wire:key="inq-{{ $inquiry->id }}"
                            class="cursor-pointer transition hover:bg-slate-50 dark:hover:bg-gray-700/50 {{ $inquiry->isNew() ? 'bg-amber-50/60 dark:bg-amber-900/10' : '' }}"
                            onclick="window.location='{{ route('admin.inquiries.show', $inquiry) }}'">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">
                                {{ $inquiry->created_at->timezone('Europe/Berlin')->format('d.m.Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-500">{{ $inquiry->reference }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="font-semibold text-slate-900 hover:underline dark:text-white">
                                    {{ $inquiry->company }}
                                </a>
                                <div class="text-slate-500">{{ $inquiry->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $inquiry->type->label() }}</td>
                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $inquiry->status->badgeClasses() }}">
                                    @if ($inquiry->isNew())<span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>@endif
                                    {{ $inquiry->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">Keine Anfragen für diese Auswahl.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $inquiries->links() }}
</div>
