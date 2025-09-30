<div class="bg-white rounded-xl shadow p-6">
    <!-- Header mit Erklärungs-Button -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            📊 Bilanz zum {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}
        </h2>
        <button wire:click="toggleExplanation"
                class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm hover:bg-blue-200">
            ℹ️ Erklärung
        </button>
    </div>

    <!-- Erklärungs-Box -->
    @if($showExplanation)
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-800 mb-2">Was ist die Bilanz?</h3>
        <p class="text-sm text-blue-700 mb-2">
            Die <strong>Bilanz</strong> zeigt die Vermögenswerte (Aktiva) und die Schulden (Passiva)
            Ihres Unternehmens zu einem bestimmten Stichtag.
        </p>
        <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
            <li><strong>Aktiva</strong> = Was dem Unternehmen gehört (Vermögen)</li>
            <li><strong>Passiva</strong> = Woher das Vermögen kommt (Schulden + Eigenkapital)</li>
            <li><strong>Bilanzgleichung</strong>: Aktiva = Passiva (muss immer ausgeglichen sein!)</li>
        </ul>
    </div>
    @endif

    <!-- Status-Leiste -->
    @if($fiscalYear)
        <div class="mb-6 p-3 rounded-lg flex items-center justify-between
                    {{ $fiscalYear->closed ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
            <div class="flex items-center">
                <span class="font-semibold">
                    📅 Geschäftsjahr {{ $fiscalYear->year }}
                </span>
                <span class="ml-2 text-sm text-gray-500">
                    ({{ \Carbon\Carbon::parse($fiscalYear->start_date)->format('d.m.Y') }}
                    – {{ \Carbon\Carbon::parse($fiscalYear->end_date)->format('d.m.Y') }})
                </span>
            </div>
            <div class="text-sm font-medium">
                Status:
                @if($fiscalYear->closed)
                    <span class="ml-1 text-red-700">🔒 geschlossen</span>
                @else
                    <span class="ml-1 text-green-700">🔓 offen</span>
                @endif
            </div>
        </div>
    @else
        <div class="mb-6 p-3 rounded-lg bg-yellow-50 text-yellow-700">
            ⚠️ Kein aktuelles Geschäftsjahr gefunden.
        </div>
    @endif

    <!-- Erfolgsmeldungen -->
    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 border border-green-200">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700 border border-red-200">
            ❌ {{ session('error') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-700 border border-yellow-200">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 rounded bg-blue-100 text-blue-700 border border-blue-200">
            ℹ️ {{ session('info') }}
        </div>
    @endif

    <!-- Bilanz-Tabelle -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Aktiva -->
        <div class="border border-gray-200 rounded-lg">
            <div class="bg-green-50 border-b border-green-200 p-3">
                <h3 class="text-md font-bold text-green-800">🟢 Aktiva (Vermögen)</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @foreach($groups['asset'] ?? [] as $groupKey => $group)
                        @if(count($group['accounts']) > 0)
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="2" class="px-3 py-2">{{ $group['label'] }}</td>
                            </tr>
                            @foreach($group['accounts'] as $acc)
                                <tr class="hover:bg-gray-50">
                                    <td class="pl-6 py-1">
                                        <span class="text-xs text-gray-500">{{ $acc['account']->number }}</span><br>
                                        {{ $acc['account']->name }}
                                    </td>
                                    <td class="text-right pr-3 py-1 font-mono">
                                        {{ number_format($acc['balance'], 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="font-bold border-t bg-gray-100">
                                <td class="pl-3 py-1">Summe {{ $group['label'] }}</td>
                                <td class="text-right pr-3 py-1 font-mono">
                                    {{ number_format($group['balance'], 2, ',', '.') }} €
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold border-t-2 border-green-300 bg-green-50">
                        <td class="px-3 py-2 text-green-800">Gesamtsumme Aktiva</td>
                        <td class="text-right px-3 py-2 font-mono text-green-800">
                            {{ number_format($totals['aktiva'], 2, ',', '.') }} €
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Passiva -->
        <div class="border border-gray-200 rounded-lg">
            <div class="bg-blue-50 border-b border-blue-200 p-3">
                <h3 class="text-md font-bold text-blue-800">🔵 Passiva (Schulden + Eigenkapital)</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    @foreach(($groups['equity'] ?? []) as $groupKey => $group)
                        @if(count($group['accounts']) > 0)
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="2" class="px-3 py-2">{{ $group['label'] }}</td>
                            </tr>
                            @foreach($group['accounts'] as $acc)
                                <tr class="hover:bg-gray-50">
                                    <td class="pl-6 py-1">
                                        <span class="text-xs text-gray-500">{{ $acc['account']->number }}</span><br>
                                        {{ $acc['account']->name }}
                                    </td>
                                    <td class="text-right pr-3 py-1 font-mono">
                                        {{ number_format($acc['balance'], 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="font-bold border-t bg-gray-100">
                                <td class="pl-3 py-1">Summe {{ $group['label'] }}</td>
                                <td class="text-right pr-3 py-1 font-mono">
                                    {{ number_format($group['balance'], 2, ',', '.') }} €
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    @foreach(($groups['liability'] ?? []) as $groupKey => $group)
                        @if(count($group['accounts']) > 0)
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="2" class="px-3 py-2">{{ $group['label'] }}</td>
                            </tr>
                            @foreach($group['accounts'] as $acc)
                                <tr class="hover:bg-gray-50">
                                    <td class="pl-6 py-1">
                                        <span class="text-xs text-gray-500">{{ $acc['account']->number }}</span><br>
                                        {{ $acc['account']->name }}
                                    </td>
                                    <td class="text-right pr-3 py-1 font-mono">
                                        {{ number_format($acc['balance'], 2, ',', '.') }} €
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="font-bold border-t bg-gray-100">
                                <td class="pl-3 py-1">Summe {{ $group['label'] }}</td>
                                <td class="text-right pr-3 py-1 font-mono">
                                    {{ number_format($group['balance'], 2, ',', '.') }} €
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold border-t-2 border-blue-300 bg-blue-50">
                        <td class="px-3 py-2 text-blue-800">Gesamtsumme Passiva</td>
                        <td class="text-right px-3 py-2 font-mono text-blue-800">
                            {{ number_format($totals['passiva'], 2, ',', '.') }} €
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Bilanzkontrolle und Aktionen -->
<!-- Erweiterte Bilanzkontrolle -->
<div class="mt-8 p-4 border rounded-lg {{ $totals['isBalanced'] ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
    <div class="flex justify-between items-center mb-4">
        <div>
            <span class="font-semibold {{ $totals['isBalanced'] ? 'text-green-700' : 'text-yellow-700' }}">
                {{ $totals['isBalanced'] ? '✅ Bilanz ausgeglichen' : '⚠️ Bilanz nicht ausgeglichen' }}
            </span>
            <div class="text-sm {{ $totals['isBalanced'] ? 'text-green-600' : 'text-yellow-600' }} mt-1">
                Aktiva ({{ number_format($totals['aktiva'], 2, ',', '.') }} €)
                = Passiva ({{ number_format($totals['passiva'], 2, ',', '.') }} €)
                @if(!$totals['isBalanced'])
                    + Gewinn/Verlust ({{ number_format(abs($totals['difference']), 2, ',', '.') }} €)
                @endif
            </div>
        </div>

        @if(!$totals['isBalanced'])
            <div class="text-right">
                <div class="font-bold {{ $totals['difference'] > 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ $totals['difference'] > 0 ? '🔺 Gewinn' : '🔻 Verlust' }}:
                    {{ number_format(abs($totals['difference']), 2, ',', '.') }} €
                </div>
                <div class="text-xs text-gray-500 mt-1">
                    Noch auf Erfolgskonten (GuV)
                </div>
            </div>
        @endif
    </div>

    <!-- Detaillierte GuV-Anzeige -->
    @if(!$totals['isBalanced'])
    <div class="border-t pt-3">
        <h4 class="font-semibold text-gray-700 mb-2">📈 Gewinn- und Verlustrechnung (GuV)</h4>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="bg-red-50 p-2 rounded">
                <div class="font-semibold text-red-700">Aufwendungen</div>
                <div class="text-right font-mono">{{ number_format($totals['expenses'] ?? 0, 2, ',', '.') }} €</div>
            </div>
            <div class="bg-green-50 p-2 rounded">
                <div class="font-semibold text-green-700">Erträge</div>
                <div class="text-right font-mono">{{ number_format($totals['revenue'] ?? 0, 2, ',', '.') }} €</div>
            </div>
        </div>

        <div class="mt-2 text-sm text-gray-600">
            <strong>Erklärung:</strong> Die Bilanz geht nicht auf, weil dein Gewinn von
            <span class="font-bold">{{ number_format(abs($totals['difference']), 2, ',', '.') }} €</span>
            noch nicht ins Eigenkapital umgebucht wurde.
        </div>
    </div>
    @endif
</div>

        @if(!$totals['isBalanced'] && $fiscalYear && !$fiscalYear->closed)
            <div class="border-t pt-3">
                <p class="text-sm text-gray-600 mb-3">
                    <strong>Jahresabschluss durchführen:</strong>
                    Das Jahresergebnis wird automatisch ins Eigenkapital übertragen.
                </p>

                <div class="flex flex-wrap gap-2">
                    <button wire:click="closeYear"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:bg-gray-400 flex items-center">
                        @if($closingInProgress)
                            ⏳ Verarbeite...
                        @else
                            📘 Aktuelles Jahr abschließen
                        @endif
                    </button>

                    <button wire:click="closePreviousYear"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-400">
                        📗 Vorjahr abschließen
                    </button>
                </div>
            </div>

            @endif

        @if($fiscalYear && $fiscalYear->closed)
            <div class="border-t pt-3">
                <p class="text-sm text-gray-600 mb-2">
                    Dieses Geschäftsjahr ist bereits abgeschlossen.
                </p>
                <button wire:click="rollbackClosing({{ $fiscalYear->id }})"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                    🔄 Abschluss rückgängig machen
                </button>
            </div>


       <!-- DRINGENDE KORREKTUR -->
@if(!$totals['isBalanced'])
       <div class="mt-6 p-4 bg-red-100 border border-red-300 rounded-lg">
    <div class="flex items-center mb-2">
        <span class="text-red-600 font-bold text-lg">🚨 DRINGEND: Bilanzfehler!</span>
    </div>
    <p class="text-red-700 mb-3">
        Die Abschlussbuchung ist technisch falsch (fehlendes Gegenkonto).
        Dies führt zu einer nicht ausgeglichenen Bilanz.
    </p>
    <button wire:click="immediateFix"
            onclick="return confirm('ACHTUNG: Dies löscht die aktuelle Abschlussbuchung und erstellt eine korrigierte Version. Fortfahren?')"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-semibold">
        🔧 SOFORT KORRIGIEREN
    </button>
</div>
@endif

            @endif



    </div>
</div>

@script
<script>
    // Bestätigungsdialog für kritische Aktionen
    function confirmAction(message) {
        return confirm(message);
    }
</script>
@endscript
