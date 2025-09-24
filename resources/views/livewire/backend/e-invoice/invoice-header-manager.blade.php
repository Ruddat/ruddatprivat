<div class="p-6 bg-white shadow rounded-lg">
    <h4 class="text-lg font-semibold mb-4">Rechnungsköpfe</h4>

    <button wire:click="$set('showForm', true)"
        class="mb-4 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
        Neuen Rechnungskopf erstellen
    </button>

    @if ($showForm)
        <form wire:submit.prevent="saveInvoiceHeader" class="space-y-4">
            <!-- Vorhandene Felder -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Vorname</label>
                <input type="text" wire:model="invoiceHeader.first_name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('invoiceHeader.first_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nachname</label>
                <input type="text" wire:model="invoiceHeader.last_name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('invoiceHeader.last_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Firmenname</label>
                <input type="text" wire:model="invoiceHeader.company_name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('invoiceHeader.company_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">E-Mail</label>
                <input type="email" wire:model="invoiceHeader.email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('invoiceHeader.email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Weitere Felder analog ... -->

            <div>
                <label class="block text-sm font-medium text-gray-700">Logo</label>
                <input type="file" wire:model="logo"
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer focus:outline-none">
                @error('logo') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror

                @if ($logo)
                    <div class="mt-2">
                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo Vorschau" class="h-16 rounded shadow">
                    </div>
                @elseif (isset($invoiceHeader['logo_path']))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $invoiceHeader['logo_path']) }}" alt="Aktuelles Logo" class="h-16 rounded shadow">
                    </div>
                @endif
            </div>

            <div class="flex space-x-3 pt-4">
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Speichern
                </button>
                <button type="button" wire:click="$set('showForm', false)"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Abbrechen
                </button>
            </div>
        </form>
    @endif

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full border border-gray-200 divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Vorname</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Nachname</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Firma</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">E-Mail</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($invoiceHeaders as $header)
                    <tr>
                        <td class="px-4 py-2">{{ $header->first_name }}</td>
                        <td class="px-4 py-2">{{ $header->last_name }}</td>
                        <td class="px-4 py-2">{{ $header->company_name }}</td>
                        <td class="px-4 py-2">{{ $header->email }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <button wire:click="edit({{ $header->id }})"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                Bearbeiten
                            </button>
                            <button wire:click="delete({{ $header->id }})"
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
