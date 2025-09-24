<div class="space-y-4">
    @if (session()->has('message'))
        <div class="p-3 rounded bg-green-100 text-green-800">
            {{ session('message') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Kunde</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Datum</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Fällig</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Gesamtbetrag</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">PDF</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2">{{ $invoice->recipient->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $invoice->invoice_date }}</td>
                        <td class="px-4 py-2">{{ $invoice->due_date }}</td>
                        <td class="px-4 py-2 font-medium text-gray-900">
                            {{ number_format($invoice->total_amount, 2, ',', '.') }} €
                        </td>
                        <td class="px-4 py-2">
                            @if ($invoice->pdf_path)
                                <a href="{{ asset('storage/invoices/' . basename($invoice->pdf_path)) }}"
                                   target="_blank"
                                   class="text-pink-600 hover:underline">
                                    PDF ansehen
                                </a>
                            @else
                                <span class="text-gray-500">Nicht verfügbar</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="generatePdf({{ $invoice->id }}, false)"
                                    class="px-3 py-1 bg-pink-600 text-white rounded hover:bg-pink-700 text-xs">
                                PDF erstellen
                            </button>
                            <button wire:click="generatePdf({{ $invoice->id }}, true)"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                ZUGFeRD-PDF
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
