<div class="space-y-6" wire:poll.15s>
    <div>
        <a href="{{ route('admin.inquiries.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Anfragen</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Benachrichtigungen</h1>
        <p class="mt-1 max-w-3xl text-sm text-slate-500">
            Sofortbenachrichtigung bei neuen Anfragen. Zugangsdaten werden ausschließlich in der <code>.env</code> gepflegt;
            hier lassen sich Kanäle schalten, Empfänger festlegen und Testnachrichten auslösen.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Queue-Zustand --}}
    <div class="rounded-xl border p-4 text-sm {{ $stalledJobs > 0 ? 'border-red-200 bg-red-50 text-red-800' : 'border-slate-200 bg-white text-slate-600 dark:border-gray-700 dark:bg-gray-800 dark:text-slate-300' }}">
        @if ($stalledJobs > 0)
            <strong>Achtung:</strong> {{ $stalledJobs }} Job(s) warten seit über 2 Minuten. Läuft der Queue-Worker (<code>php artisan queue:work</code>)?
        @else
            Queue: <code>{{ $queueConnection }}</code> · keine hängenden Jobs ·
            letzte vom Anbieter angenommene Benachrichtigung:
            {{ $lastAccepted ? \Illuminate\Support\Carbon::parse($lastAccepted)->timezone('Europe/Berlin')->format('d.m.Y H:i') : '–' }}
        @endif
    </div>

    <form wire:submit="save" class="space-y-4">
        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($channels as $key => $channel)
                <section wire:key="ch-{{ $key }}" class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="font-semibold text-slate-900 dark:text-white">{{ \App\Models\InquiryAlert::channelLabel($key) }}</h2>
                            <p class="text-xs text-slate-500">Anbieter: {{ $channel->provider() }}</p>
                        </div>
                        @if ($channel->isConfigured())
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">Konfiguriert</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">Nicht konfiguriert</span>
                        @endif
                    </div>

                    <label class="flex items-center gap-3 text-sm">
                        <input type="checkbox" wire:model="enabled.{{ $key }}" class="rounded border-slate-300" @disabled(! $channel->isConfigured() && ! ($enabled[$key] ?? false))>
                        <span class="font-medium text-slate-800 dark:text-slate-200">Bei neuen Anfragen benachrichtigen</span>
                    </label>
                    @error("enabled.$key") <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                    @if (in_array($key, $editable, true))
                        <div>
                            <label for="rcpt-{{ $key }}" class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                {{ $key === 'mail' ? 'Empfänger-Adresse' : 'Empfängernummer' }}
                            </label>
                            <input id="rcpt-{{ $key }}" type="{{ $key === 'mail' ? 'email' : 'tel' }}" wire:model="recipients.{{ $key }}"
                                placeholder="{{ $key === 'mail' ? 'service@…' : '+4917612345678' }}"
                                class="w-full rounded-lg border-slate-300 text-sm dark:border-gray-600 dark:bg-gray-900">
                            @error("recipients.$key") <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <p class="text-sm text-slate-500">Chat-ID aus <code>TELEGRAM_CHAT_ID</code>: {{ \App\Models\InquiryAlert::mask($channel->recipient()) ?? '–' }}</p>
                    @endif

                    @if ($key === 'sms')
                        <p class="text-xs text-slate-500">
                            Treiber: <code>{{ $smsDriver }}</code>
                            @if ($smsDriver !== 'seven') – es werden <strong>keine echten SMS</strong> versendet (nur Log). Für den Livebetrieb <code>INQUIRY_SMS_DRIVER=seven</code> setzen. @endif
                        </p>
                    @endif
                    @if ($key === 'whatsapp')
                        <p class="text-xs text-slate-500">Benötigt WhatsApp Business Platform: <code>WHATSAPP_ACCESS_TOKEN</code>, <code>WHATSAPP_PHONE_NUMBER_ID</code>, ein freigegebenes Template (<code>WHATSAPP_ALERT_TEMPLATE</code>) und die Zustimmung des Empfängers.</p>
                    @endif

                    <button type="button" wire:click="sendTest('{{ $key }}')" @disabled(! $channel->isConfigured())
                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:text-slate-200">
                        Testbenachrichtigung senden
                    </button>
                </section>
            @endforeach
        </div>

        <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800">Einstellungen speichern</button>
    </form>

    {{-- Protokoll --}}
    <section class="rounded-xl border border-slate-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-slate-200 p-5 dark:border-gray-700">
            <h2 class="font-semibold text-slate-900 dark:text-white">Letzte Benachrichtigungen</h2>
            <p class="mt-1 text-xs text-slate-500">„Vom Anbieter angenommen“ heißt: Der Dienst hat die Nachricht übernommen. Ob sie auf dem Gerät angekommen oder gelesen wurde, ist damit nicht bestätigt.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-gray-900">
                    <tr><th class="px-4 py-3">Zeit</th><th class="px-4 py-3">Kanal</th><th class="px-4 py-3">Anlass</th><th class="px-4 py-3">Empfänger</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Versuche</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-gray-700">
                    @forelse ($alerts as $alert)
                        <tr wire:key="al-{{ $alert->id }}">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $alert->created_at->timezone('Europe/Berlin')->format('d.m. H:i:s') }}</td>
                            <td class="px-4 py-3">{{ \App\Models\InquiryAlert::channelLabel($alert->channel) }}@if ($alert->is_fallback) <span class="text-xs text-slate-500">(Fallback)</span>@endif</td>
                            <td class="px-4 py-3">
                                @if ($alert->is_test) Test
                                @elseif ($alert->inquiry) <a class="hover:underline" href="{{ route('admin.inquiries.show', $alert->inquiry_id) }}">{{ $alert->inquiry->reference }}</a>
                                @else – @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $alert->recipient_masked ?? '–' }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $alert->statusClasses() }}">{{ $alert->statusLabel() }}</span>
                                @if ($alert->last_error)<div class="mt-1 max-w-xs text-xs text-red-700">{{ $alert->last_error }}</div>@endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $alert->attempts }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Noch keine Benachrichtigungen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
