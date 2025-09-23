<div class="bg-white shadow rounded-xl overflow-hidden">

    <div x-data="{ open: false }" class="relative inline-block text-left mt-4">
        <button type="button" @click="open = !open"
            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2
                   bg-green-600 text-sm font-medium text-white hover:bg-green-700 focus:outline-none">
            📤 Export
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown -->
        <div x-show="open" @click.outside="open = false" x-transition
            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white
                ring-1 ring-black ring-opacity-5 z-50">
            <div class="py-1">
                <a href="{{ route("admin.bookkeeping.entries.export.fancy") }}"
                    class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                    📊 Schönes Excel (Report)
                </a>
                <a href="{{ route("admin.bookkeeping.entries.export.raw") }}"
                    class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">
                    💾 Rohdaten (Import/Backup)
                </a>
            </div>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-600">Datum</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-600">Soll-Konto</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-600">Haben-Konto</th>
                <th class="px-4 py-2 text-right font-semibold text-gray-600">Betrag (€)</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-600">Beschreibung</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-700">{{ $entry->booking_date }}</td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ $entry->debitAccount->number ?? "" }}
                        – {{ $entry->debitAccount->name ?? "" }}
                    </td>
                    <td class="px-4 py-2 text-gray-700">
                        {{ $entry->creditAccount->number ?? "" }}
                        – {{ $entry->creditAccount->name ?? "" }}
                    </td>
                    <td class="px-4 py-2 text-right font-medium text-gray-900">
                        {{ number_format($entry->amount, 2, ",", ".") }}
                    </td>
                    <td class="px-4 py-2 text-gray-500">{{ $entry->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-400">
                        Noch keine Buchungen vorhanden
                    </td>
                </tr>
            @endforelse
        </tbody>

        <!-- Summenzeile -->
        @if ($entries->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">
                        Summe Soll</td>
                    <td class="px-4 py-2 text-right font-bold text-pink-600">
                        {{ number_format($totalDebit, 2, ",", ".") }}
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">
                        Summe Haben</td>
                    <td class="px-4 py-2 text-right font-bold text-pink-600">
                        {{ number_format($totalCredit, 2, ",", ".") }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

</div>
