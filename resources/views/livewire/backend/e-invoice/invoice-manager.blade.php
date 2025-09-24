<div class="space-y-4">
    {{-- Flash-Messages --}}
    @if (session()->has('message'))
        <div class="p-3 rounded bg-green-100 text-green-800">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-3 rounded bg-red-100 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div>
        @error('items') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
    </div>

    {{-- Button-Leiste --}}
    <div class="flex gap-2">
        <button wire:click="$set('showForm', true)"
            class="px-4 py-2 border border-pink-500 text-pink-600 rounded hover:bg-pink-50 transition">
            Neue Rechnung erstellen
        </button>
        <a href="{{ route('customer.e_invoice.customer_manager') }}"
            class="px-4 py-2 border border-gray-400 text-gray-700 rounded hover:bg-gray-50 transition">
            Kunden verwalten
        </a>
    </div>

    {{-- Formular --}}
    @if ($showForm)
        <form wire:submit.prevent="saveInvoice" class="mt-6 space-y-4 bg-white p-6 shadow rounded">
            <div>
                <label class="block text-sm font-medium text-gray-700">Rechnungskopf</label>
                <select wire:model="invoice.invoice_creator_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="">-- Rechnungskopf wählen --</option>
                    @foreach ($invoice_creators as $creator)
                        <option value="{{ $creator->id }}">{{ $creator->company_name ?? $creator->getFullNameAttribute() }}</option>
                    @endforeach
                </select>
                @error('invoice.invoice_creator_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Kunde</label>
                <select wire:model="invoice.recipient_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="">-- Kunde wählen --</option>
                    @foreach ($recipients as $recipient)
                        <option value="{{ $recipient->id }}">{{ $recipient->name }}</option>
                    @endforeach
                </select>
                @error('invoice.recipient_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rechnungsdatum</label>
                    <input type="date" wire:model="invoice.invoice_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('invoice.invoice_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fälligkeitsdatum</label>
                    <input type="date" wire:model="invoice.due_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('invoice.due_date') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model="invoice.status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    <option value="">-- Status wählen --</option>
                    <option value="draft">Entwurf</option>
                    <option value="sent">Gesendet</option>
                    <option value="paid">Bezahlt</option>
                    <option value="cancelled">Storniert</option>
                </select>
                @error('invoice.status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notizen</label>
                <textarea wire:model="invoice.notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"></textarea>
                @error('invoice.notes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Rechnungspositionen --}}
            <div class="border rounded-lg shadow-sm p-4 bg-gray-50">
                <h5 class="font-semibold mb-3">Rechnungspositionen</h5>
                <div class="space-y-4">
                    @foreach ($items as $index => $item)
                        <div class="border rounded p-4 bg-white shadow-sm space-y-2">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Artikelnummer</label>
                                    <input type="text" wire:model="items.{{ $index }}.item_number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                        placeholder="Artikelnummer">
                                    @error("items.{$index}.item_number") <small class="text-red-600">{{ $message }}</small> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700">Beschreibung</label>
                                    <textarea wire:model="items.{{ $index }}.description" rows="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                        placeholder="Beschreibung"></textarea>
                                    @error("items.{$index}.description") <small class="text-red-600">{{ $message }}</small> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Menge</label>
                                    <input type="number" wire:model="items.{{ $index }}.quantity" min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                        placeholder="Menge">
                                    @error("items.{$index}.quantity") <small class="text-red-600">{{ $message }}</small> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Einzelpreis (€)</label>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                        placeholder="Einzelpreis">
                                    @error("items.{$index}.unit_price") <small class="text-red-600">{{ $message }}</small> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Steuersatz (%)</label>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.tax_rate"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                                        placeholder="Steuersatz">
                                    @error("items.{$index}.tax_rate") <small class="text-red-600">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="button" wire:click="removeItem({{ $index }})"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                                    Löschen
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-right mt-3">
                    <button type="button" wire:click="addItem"
                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs">
                        + Position hinzufügen
                    </button>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Rechnung speichern
                </button>
                <button type="button" wire:click="$set('showForm', false)"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Abbrechen
                </button>
            </div>
        </form>
    @endif

    {{-- Rechnungs-Tabelle --}}
<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-sm divide-y divide-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">#</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Kunde</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Datum</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Fällig</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Betrag</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Status</th>
                <th class="px-4 py-2 text-left font-semibold text-gray-700">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($invoices as $invoice)
                <tr>
                    <td class="px-4 py-2">{{ $invoice->invoice_number }}</td>
                    <td class="px-4 py-2">{{ $invoice->recipient->name }}</td>
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
                    <td class="px-4 py-2 space-x-2">
                        <button wire:click="editInvoice({{ $invoice->id }})"
                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                            Bearbeiten
                        </button>
                        <button type="button" wire:click="deleteInvoice({{ $invoice->id }})"
                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                            Löschen
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


</div>
