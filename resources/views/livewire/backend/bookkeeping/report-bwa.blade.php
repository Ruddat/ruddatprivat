<div class="max-w-6xl mx-auto space-y-6">
    <!-- Mandantenauswahl -->
    @if($availableTenants->count() > 1)
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-3">Mandant auswählen</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($availableTenants as $availableTenant)
                <button 
                    wire:click="$set('tenantId', {{ $availableTenant->id }})"
                    class="px-4 py-2 rounded-lg border transition-all {{ $tenantId == $availableTenant->id ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                >
                    {{ $availableTenant->name }}
                    @if($availableTenant->is_current)
                        <span class="text-xs ml-1">(aktuell)</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- BWA Report -->
    <div class="bg-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">
                BWA - Betriebswirtschaftliche Auswertung
                @if($tenant)
                    <span class="text-gray-600 text-sm ml-2">({{ $tenant->name }})</span>
                @endif
            </h2>
            @if($fiscalYear ?? false)
                <span class="text-sm text-gray-500">
                    Geschäftsjahr: {{ $fiscalYear->year }} 
                    ({{ $fiscalYear->start_date }} - {{ $fiscalYear->end_date }})
                </span>
            @endif
        </div>

        @if(!$hasData)
            <div class="text-center py-8 text-gray-500">
                @if(!$fiscalYear)
                    <p>Kein aktives Geschäftsjahr für diesen Mandanten gefunden.</p>
                    <a href="{{ route('backend.bookkeeping.fiscal-years') }}" class="text-pink-600 hover:underline mt-2 inline-block">
                        Geschäftsjahr anlegen
                    </a>
                @elseif(($entries && $entries->count() === 0))
                    <p>Keine Buchungen für das aktuelle Geschäftsjahr vorhanden.</p>
                @else
                    <p>Keine Daten verfügbar.</p>
                @endif
            </div>
        @else
            <!-- BWA-Tabelle -->
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300">
                        <th class="text-left py-2 font-semibold">Position</th>
                        <th class="text-right py-2 font-semibold">Betrag</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $key => $row)
                        @if($row['amount'] != 0)
                        <tr class="border-b {{ $key === 'revenue' ? 'font-semibold' : '' }}">
                            <td class="py-2">{{ $row['label'] }}</td>
                            <td class="text-right py-2">
                                {{ number_format($row['amount'], 2, ',', '.') }} €
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    
                    <!-- Ergebniszeile -->
                    <tr class="border-t-2 border-gray-300 font-bold">
                        <td class="py-2">Jahresüberschuss/-fehlbetrag</td>
                        <td class="text-right py-2 {{ $result >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($result, 2, ',', '.') }} €
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>