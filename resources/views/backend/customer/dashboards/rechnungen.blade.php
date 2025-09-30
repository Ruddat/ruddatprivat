@extends("backend.customer.layouts.app")

@section("title", "Rechnungen Dashboard")
@section("page_title", "Rechnungen")

@section("content")

    {{-- Hero --}}
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-2xl font-bold">Hallo {{ $customer->name }},</h2>
        <p class="mt-2">Hier hast du deine Rechnungen im Blick.</p>
        <p class="mt-1 text-sm text-purple-100">
            Offene, bezahlte und neue Rechnungen – alles an einem Ort.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-sm font-medium text-gray-500">Offene Beträge</h3>
            <p class="mt-2 text-2xl font-bold text-red-600">
                {{ number_format($openAmount, 2, ',', '.') }} €
            </p>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-sm font-medium text-gray-500">Bezahlt</h3>
            <p class="mt-2 text-2xl font-bold text-green-600">
                {{ number_format($paidAmount, 2, ',', '.') }} €
            </p>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-sm font-medium text-gray-500">Entwürfe</h3>
            <p class="mt-2 text-2xl font-bold text-gray-600">{{ $invoiceStats['draft'] }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow hover:shadow-md transition">
            <h3 class="text-sm font-medium text-gray-500">Gesendet</h3>
            <p class="mt-2 text-2xl font-bold text-blue-600">{{ $invoiceStats['sent'] }}</p>
        </div>
    </div>

    {{-- Rechnungen Dashboard --}}
    <div class="bg-white p-6 rounded-lg shadow space-y-4">
        <h3 class="text-lg font-semibold text-purple-600">Rechnungen</h3>
        <p class="text-gray-600">Erstelle und verwalte deine Rechnungen.</p>

        @if ($invoiceCreatorsCount === 0)
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800">
                    Du hast noch keinen <strong>Rechnungskopf</strong> angelegt.
                </p>
                <a href="{{ route('customer.e_invoice.invoice_headers') }}"
                   class="mt-2 inline-block px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Rechnungskopf anlegen
                </a>
            </div>
        @elseif ($recipientsCount === 0)
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
                <p class="text-sm text-yellow-800">
                    Du hast noch keinen <strong>Empfänger</strong> angelegt.
                </p>
                <a href="{{ route('customer.e_invoice.customer_manager') }}"
                   class="mt-2 inline-block px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Empfänger anlegen
                </a>
            </div>
        @else
            <div class="flex gap-3">
                <a href="{{ route('customer.new_invoice.invoice_manager') }}"
                   class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 transition">
                    ➕ Neue Rechnung erstellen
                </a>
                <a href="{{ route('customer.new_invoice.pdf_manager') }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                    📄 Rechnungen ansehen
                </a>
            </div>
        @endif
    </div>

    {{-- Letzte Rechnungen --}}
    <div class="bg-white shadow rounded overflow-x-auto mt-8">
        <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Kunde</th>
                    <th class="px-4 py-2 text-left">Datum</th>
                    <th class="px-4 py-2 text-left">Fällig</th>
                    <th class="px-4 py-2 text-left">Betrag</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($latestInvoices as $invoice)
                    <tr>
                        <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2">{{ $invoice->recipient->name ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $invoice->invoice_date }}</td>
                        <td class="px-4 py-2">{{ $invoice->due_date }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->total_amount, 2, ',', '.') }} €</td>
                        <td class="px-4 py-2">
                            @switch($invoice->status)
                                @case('paid')
                                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">Bezahlt</span>
                                    @break
                                @case('sent')
                                    <span class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded">Gesendet</span>
                                    @break
                                @case('draft')
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">Entwurf</span>
                                    @break
                                @case('cancelled')
                                    <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded">Storniert</span>
                                    @break
                                @default
                                    <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-50 rounded">Unbekannt</span>
                            @endswitch
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Keine Rechnungen vorhanden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
