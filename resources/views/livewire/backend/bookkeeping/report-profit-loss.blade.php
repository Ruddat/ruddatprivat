<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Gewinn- und Verlustrechnung
            </h2>
            <p class="text-sm text-gray-500">
                @if ($tenant)
                    {{ $tenant->name }}
                @else
                    <span class="italic text-gray-400">kein Mandant aktiv</span>
                @endif

                @if ($fiscalYear)
                    – Geschäftsjahr {{ $fiscalYear->year }}
                    <span class="ml-1 text-gray-400">
                        ({{ $fiscalYear->start_date }} – {{ $fiscalYear->end_date }})
                    </span>
                @else
                    <span class="italic text-gray-400">kein Jahr gewählt</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Inhalt -->
    @if (!$fiscalYear)
        <div class="text-gray-600 italic">
            Kein aktuelles Jahr gefunden. Bitte ein Geschäftsjahr auswählen oder anlegen.
        </div>
    @else
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Erlöse</span>
                <span class="font-medium text-green-600">
                    {{ number_format($revenue, 2, ",", ".") }} €
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Aufwendungen</span>
                <span class="font-medium text-red-600">
                    {{ number_format($expenses, 2, ",", ".") }} €
                </span>
            </div>
            <hr class="my-2">
            <div class="flex justify-between text-lg font-bold">
                <span>Ergebnis</span>
                <span class="{{ $profit >= 0 ? "text-green-700" : "text-red-700" }}">
                    {{ number_format($profit, 2, ",", ".") }} €
                </span>
            </div>
        </div>
    @endif
</div>
