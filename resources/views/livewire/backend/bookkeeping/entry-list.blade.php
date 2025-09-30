<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header mit Mandantenauswahl -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buchungsübersicht</h1>
                @if($currentTenant)
                    <p class="text-gray-600 mt-1">
                        Aktueller Mandant:
                        <span class="font-semibold text-pink-600">{{ $currentTenant->name }}</span>
                        @if($currentTenant->city)
                            <span class="text-gray-500">({{ $currentTenant->zip }} {{ $currentTenant->city }})</span>
                        @endif
                    </p>
                @endif
            </div>
        </div>

        <!-- Mandanten Auswahl -->
        @if($availableTenants->count() > 1)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mandant wechseln</label>
            <select wire:model.change="tenantId"
                    class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                @foreach ($availableTenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Filter -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Geschäftsjahr Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Geschäftsjahr</label>
                <select wire:model.change="yearId"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Alle Jahre</option>
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Suchfeld -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Suche</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Beschreibung, Konto, Datum..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>
        </div>
    </div>

    <!-- Fehler-Anzeige -->
    @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-red-800">{{ $error }}</span>
            </div>
        </div>
    @endif

    <!-- Inhalt -->
    @if(!isset($error))
        <!-- Summen Anzeige -->
        <div class="bg-white p-4 rounded-xl shadow border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-sm text-gray-600">Anzahl Buchungen</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $paginatedEntries->total() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Gesamt Soll</p>
                    <p class="text-lg font-semibold text-red-600">{{ number_format($totalDebit, 2, ',', '.') }} €</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Gesamt Haben</p>
                    <p class="text-lg font-semibold text-green-600">{{ number_format($totalCredit, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>

        <!-- Buchungsliste -->
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
            @if($transactions->count() > 0)
                <!-- Tabelle -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Soll</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Haben</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Betrag</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Beschreibung</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($transactions as $transactionId => $entries)
                                @foreach($entries as $index => $entry)
                                    <tr class="{{ $index === 0 ? 'bg-blue-50' : 'bg-white' }} hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            @if($index === 0)
                                                {{ $entry->booking_date->format('d.m.Y') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            @if($entry->debit_account_id && $entry->debitAccount)
                                                {{ $entry->debitAccount->number }} - {{ $entry->debitAccount->name }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            @if($entry->credit_account_id && $entry->creditAccount)
                                                {{ $entry->creditAccount->number }} - {{ $entry->creditAccount->name }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-mono {{ $entry->debit_account_id ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($entry->amount, 2, ',', '.') }} €
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $entry->description }}
                                            @if($entry->receipt)
                                                <span class="inline-flex items-center px-2 py-1 ml-2 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Beleg
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-medium">
                                            @if($index === 0)
                                                <div class="flex justify-end space-x-2">
                                                    @if($entry->receipt)
                                                        <button wire:click="downloadReceipt({{ $entry->receipt->id }})"
                                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                                title="Beleg herunterladen">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    <button wire:click="deleteEntry({{ $entry->id }})"
                                                            wire:confirm="Buchung vom {{ $entry->booking_date->format('d.m.Y') }} wirklich löschen?"
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                                        Löschen
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- Trennlinie zwischen Transaktionen -->
                                <tr>
                                    <td colspan="6" class="px-4 py-2 bg-gray-100"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                    {{ $paginatedEntries->links() }}
                </div>
            @else
                <!-- Leere State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Buchungen gefunden</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($search || $yearId)
                            Versuchen Sie Ihre Suchkriterien anzupassen.
                        @else
                            Beginnen Sie mit der Erfassung Ihrer ersten Buchung.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
