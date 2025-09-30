<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header mit Button für neuen Mandanten -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Mandantenverwaltung</h1>

        @if(!$showForm)
            <button wire:click="create"
                    class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Neuen Mandanten anlegen
            </button>
        @endif
    </div>

    <!-- Formular (nur sichtbar wenn showForm = true) -->
    @if($showForm)
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">
                {{ $tenantId ? "Mandant bearbeiten" : "Neuen Mandanten anlegen" }}
            </h2>
            <button wire:click="cancel"
                    class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Allgemeine Informationen -->
            <div class="md:col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Allgemeine Informationen</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" wire:model="name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                @error("name")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                <input type="text" wire:model="slug" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                @error("slug")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kontaktinformationen -->
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Kontaktinformationen</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                <input type="email" wire:model="email" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                <input type="text" wire:model="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <!-- Adresse -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                <div class="space-y-2">
                    <div class="flex gap-3">
                        <input type="text" wire:model="street" placeholder="Straße" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <input type="text" wire:model="house_number" placeholder="Hausnummer" class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div class="flex gap-3">
                        <input type="text" wire:model="zip" placeholder="PLZ" class="w-32 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <input type="text" wire:model="city" placeholder="Ort" class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Land</label>
                <input type="text" wire:model="country" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <!-- Rechtliche Informationen -->
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Rechtliche Informationen</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Steuernummer</label>
                <input type="text" wire:model="tax_number" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">USt-ID</label>
                <input type="text" wire:model="vat_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Handelsregister</label>
                <input type="text" wire:model="commercial_register" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Registergericht</label>
                <input type="text" wire:model="court_register" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <!-- Bankinformationen -->
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Bankinformationen</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bankname</label>
                <input type="text" wire:model="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IBAN</label>
                <input type="text" wire:model="iban" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BIC</label>
                <input type="text" wire:model="bic" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <!-- Einstellungen -->
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Einstellungen</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Geschäftsjahresbeginn *</label>
                <input type="date" wire:model="fiscal_year_start" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Währung *</label>
                <input type="text" wire:model="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" wire:model="active"
                       class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                <label class="ml-2 text-sm text-gray-700">Aktiv</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontorahmen *</label>
                <select wire:model="chart_of_accounts" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    @foreach (\App\Services\ChartOfAccountsService::getFrameworks() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 pt-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" wire:click="cancel"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                    Abbrechen
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition-colors duration-200">
                    {{ $tenantId ? 'Aktualisieren' : 'Anlegen' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Tabelle (immer sichtbar) -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Vorhandene Mandanten</h2>

        @if($tenants->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ort</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USt-ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($tenants as $t)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $t->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->slug }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->zip }} {{ $t->city }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->vat_id ?: '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $t->bank_name ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($t->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        aktiv
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        inaktiv
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <button wire:click="edit({{ $t->id }})"
                                            class="text-pink-600 hover:text-pink-700 transition-colors duration-200 text-sm font-medium">
                                        Bearbeiten
                                    </button>

                                    @if ($t->is_current)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            aktuell
                                        </span>
                                    @else
                                        <button wire:click="setCurrent({{ $t->id }})"
                                                class="text-gray-600 hover:text-gray-700 transition-colors duration-200 text-sm font-medium">
                                            aktivieren
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Keine Mandanten vorhanden</h3>
            <p class="mt-1 text-sm text-gray-500">Legen Sie Ihren ersten Mandanten an.</p>
        </div>
        @endif
    </div>
</div>
