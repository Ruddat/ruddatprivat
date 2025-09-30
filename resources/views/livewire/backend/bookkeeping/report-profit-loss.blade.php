<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Gewinn- und Verlustrechnung
            </h2>
            <p class="text-sm text-gray-500">
                @if ($tenant)
                    <span class="font-medium">Aktiver Mandant:</span> {{ $tenant->name }}
                @else
                    <span class="italic text-gray-400">kein Mandant aktiv</span>
                @endif
            </p>
        </div>

        <!-- Geschäftsjahr Auswahl -->
        @if ($years->count() > 0)
            <div>
                <label for="yearId" class="text-sm text-gray-600 mr-2">Geschäftsjahr:</label>
                <select wire:model.change="yearId" id="yearId" class="form-select text-sm">
                    <option value="">-- auswählen --</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}">
                            {{ $year->year }}
                            ({{ \Carbon\Carbon::parse($year->start_date)->format('d.m.Y') }}
                             – {{ \Carbon\Carbon::parse($year->end_date)->format('d.m.Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- Detailanzeige -->
    @if (!$fiscalYear)
        <div class="text-gray-600 italic">
            Kein Geschäftsjahr ausgewählt oder aktiv.
        </div>
    @else
        <div class="space-y-3 mb-6">
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
                <span class="{{ $profit >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ number_format($profit, 2, ",", ".") }} €
                </span>
            </div>
        </div>
    @endif

    <!-- Summenübersicht pro Jahr -->
    @if ($yearlySummaries->count() > 0)
        <h3 class="font-semibold text-gray-800 mb-2">Übersicht aller Jahre</h3>
        <table class="min-w-full text-sm border border-gray-200 rounded">
            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-3 py-2 text-left">Jahr</th>
                    <th class="px-3 py-2 text-right">Erlöse</th>
                    <th class="px-3 py-2 text-right">Aufwendungen</th>
                    <th class="px-3 py-2 text-right">Ergebnis</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($yearlySummaries as $summary)
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            {{ $summary['year']->year }}
                        </td>
                        <td class="px-3 py-2 text-right text-green-600">
                            {{ number_format($summary['revenue'], 2, ",", ".") }} €
                        </td>
                        <td class="px-3 py-2 text-right text-red-600">
                            {{ number_format($summary['expenses'], 2, ",", ".") }} €
                        </td>
                        <td class="px-3 py-2 text-right font-bold {{ $summary['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($summary['profit'], 2, ",", ".") }} €
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
