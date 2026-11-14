<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Zurück zum Eingang</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $inquiry->company }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                <span class="font-mono">{{ $inquiry->reference }}</span> ·
                eingegangen am {{ $inquiry->created_at->timezone('Europe/Berlin')->format('d.m.Y \u\m H:i') }} Uhr
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ $this->replyMailto() }}"
                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                Per E-Mail antworten
            </a>
            @if ($inquiry->status !== \App\Enums\InquiryStatus::Archived)
                <button type="button" wire:click="archive" wire:confirm="Anfrage archivieren?"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-gray-600 dark:text-slate-200 dark:hover:bg-gray-700">
                    Archivieren
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
        <div class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Kontakt</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Name</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $inquiry->name }}</dd></div>
                    <div><dt class="text-slate-500">Unternehmen</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $inquiry->company }}</dd></div>
                    <div><dt class="text-slate-500">E-Mail</dt><dd><a class="font-medium text-blue-700 hover:underline dark:text-blue-400" href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></dd></div>
                    <div><dt class="text-slate-500">Telefon</dt><dd class="font-medium text-slate-900 dark:text-white">
                        @if ($inquiry->phone)<a class="hover:underline" href="tel:{{ preg_replace('/[^0-9+]/', '', $inquiry->phone) }}">{{ $inquiry->phone }}</a>@else – @endif
                    </dd></div>
                    <div><dt class="text-slate-500">Art der Anfrage</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $inquiry->type->label() }}</dd></div>
                    <div><dt class="text-slate-500">Datenschutz bestätigt</dt><dd class="font-medium text-slate-900 dark:text-white">{{ $inquiry->privacy_accepted_at->timezone('Europe/Berlin')->format('d.m.Y H:i') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Beschreibung der Anwendung</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-200">{{ $inquiry->application_summary }}</p>

                <h2 class="mt-6 text-sm font-semibold uppercase tracking-wide text-slate-500">Technische Anforderungen</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-200">{{ $inquiry->requirements ?: '–' }}</p>
            </section>
        </div>

        <div class="space-y-6">
            <form wire:submit="save" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Bearbeitung</h2>

                <div>
                    <label for="inq-status" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                    <select id="inq-status" wire:model="status" class="mt-1 w-full rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                        @foreach ($statuses as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="inq-notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Interner Vermerk</label>
                    <textarea id="inq-notes" wire:model="internal_notes" rows="7"
                        class="mt-1 w-full rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900"
                        placeholder="Nur intern sichtbar"></textarea>
                    @error('internal_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Speichern
                </button>
            </form>

            <section class="rounded-xl border border-slate-200 bg-white p-6 text-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Verlauf</h2>
                <dl class="mt-4 space-y-2">
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Eingegangen</dt><dd>{{ $inquiry->created_at->timezone('Europe/Berlin')->format('d.m.Y H:i') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Erstmals geöffnet</dt><dd>{{ $inquiry->first_viewed_at?->timezone('Europe/Berlin')->format('d.m.Y H:i') ?? '–' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Status geändert</dt><dd>{{ $inquiry->status_changed_at?->timezone('Europe/Berlin')->format('d.m.Y H:i') ?? '–' }}</dd></div>
                    @if ($inquiry->archived_at)
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Archiviert</dt><dd>{{ $inquiry->archived_at->timezone('Europe/Berlin')->format('d.m.Y H:i') }}</dd></div>
                    @endif
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 text-sm dark:border-gray-700 dark:bg-gray-800" wire:poll.15s>
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Benachrichtigungen</h2>
                    <a href="{{ route('admin.inquiry-alerts.settings') }}" class="text-xs text-slate-500 hover:underline">Einstellungen</a>
                </div>
                <ul class="mt-4 space-y-3">
                    @forelse ($inquiry->alerts()->get() as $alert)
                        <li class="flex items-start justify-between gap-3">
                            <span>
                                {{ \App\Models\InquiryAlert::channelLabel($alert->channel) }}
                                @if ($alert->is_fallback)<span class="text-xs text-slate-500">(Fallback)</span>@endif
                                <span class="block text-xs text-slate-500">{{ $alert->recipient_masked }} · {{ $alert->attempts }} Versuch(e)</span>
                                @if ($alert->last_error)<span class="block text-xs text-red-700">{{ $alert->last_error }}</span>@endif
                            </span>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $alert->statusClasses() }}">{{ $alert->statusLabel() }}</span>
                        </li>
                    @empty
                        <li class="text-slate-500">Keine Benachrichtigung angestoßen (kein Kanal aktiv).</li>
                    @endforelse
                </ul>
                <p class="mt-4 text-xs text-slate-500">„Angenommen“ bestätigt nur die Übernahme durch den Anbieter, nicht das Lesen.</p>
            </section>
        </div>
    </div>
</div>
