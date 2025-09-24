<div class="space-y-6">
    <!-- Flash-Messages -->
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

    <!-- Filter und Suche -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <input type="text" wire:model.debounce.500ms="search"
            class="w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
            placeholder="Kunden suchen...">

        <div class="flex flex-wrap gap-2">
            <button wire:click="toggleFilter"
                class="px-3 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                {{ $filterActive ? 'Inaktive Kunden anzeigen' : 'Aktive Kunden anzeigen' }}
            </button>
            <button wire:click="exportCustomers"
                class="px-3 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 transition">
                Kunden exportieren
            </button>
        </div>
    </div>

    <!-- Button für neues Kundenformular -->
    <button wire:click="openCreateForm"
        class="px-4 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
        Neuen Kunden hinzufügen
    </button>

    <!-- Kundenformular -->
    @if ($showForm)
        <form wire:submit.prevent="{{ $isEditMode ? 'updateCustomer' : 'createCustomer' }}"
            class="space-y-4 p-6 bg-white shadow rounded-md">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="newCustomer.name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('newCustomer.name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" wire:model="newCustomer.email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('newCustomer.email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Telefon -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                <input type="text" wire:model="newCustomer.phone"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('newCustomer.phone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Adresse -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                <input type="text" wire:model="newCustomer.address"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('newCustomer.address') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Stadt + PLZ + Land -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Stadt</label>
                    <input type="text" wire:model="newCustomer.city"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.city') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Postleitzahl</label>
                    <input type="text" wire:model="newCustomer.postal_code"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.postal_code') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Land</label>
                    <input type="text" wire:model="newCustomer.country"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.country') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Kundentyp + Firma -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kundentyp</label>
                    <select wire:model="newCustomer.customer_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        <option value="private">Privat</option>
                        <option value="business">Geschäftlich</option>
                    </select>
                    @error('newCustomer.customer_type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Firmenname</label>
                    <input type="text" wire:model="newCustomer.company_name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.company_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- VAT + Zahlungsbedingungen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Umsatzsteuer-ID</label>
                    <input type="text" wire:model="newCustomer.vat_number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.vat_number') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Zahlungsbedingungen</label>
                    <input type="text" wire:model="newCustomer.payment_terms"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    @error('newCustomer.payment_terms') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Notizen -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Notizen</label>
                <textarea wire:model="newCustomer.notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"></textarea>
                @error('newCustomer.notes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Aktiv -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" wire:model="newCustomer.is_active"
                    class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                <label class="text-sm text-gray-700">Aktiv</label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                    {{ $isEditMode ? 'Kunde aktualisieren' : 'Kunde speichern' }}
                </button>
                <button type="button" wire:click="cancelEdit"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    Abbrechen
                </button>
            </div>
        </form>
    @endif

    <!-- Kundenliste -->
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Typ</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Land</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Aktiv</th>
                    <th class="px-4 py-2 text-left font-semibold text-gray-700">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-4 py-2">{{ $customer->name }}</td>
                        <td class="px-4 py-2">{{ $customer->email }}</td>
                        <td class="px-4 py-2">{{ ucfirst($customer->customer_type) }}</td>
                        <td class="px-4 py-2">{{ $customer->country }}</td>
                        <td class="px-4 py-2">
                            <button wire:click="toggleActive({{ $customer->id }})"
                                class="px-2 py-1 text-xs rounded-md {{ $customer->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">
                                {{ $customer->is_active ? 'Aktiv' : 'Inaktiv' }}
                            </button>
                        </td>
                        <td class="px-4 py-2 space-x-2">
<button wire:click="startEditCustomer({{ $customer->id }})"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
                                Bearbeiten
                            </button>
                            <button wire:click="deleteCustomer({{ $customer->id }})"
                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Keine Kunden gefunden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $customers->links() }}
    </div>
</div>
