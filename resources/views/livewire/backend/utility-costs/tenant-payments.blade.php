<div class="max-w-7xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Nebenkostenzahlungen verwalten</h2>

    <!-- Fehlerbenachrichtigung -->
    @if (session()->has('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formular -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h5 class="text-lg font-medium text-gray-700 mb-4">
            {{ $editMode ? 'Zahlung bearbeiten' : 'Neue Zahlung hinzufügen' }}
        </h5>

        <form wire:submit.prevent="savePayment" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mieter -->
                <div>
                    <label for="tenant_id" class="block text-sm font-medium text-gray-700">Mieter</label>
                    <select wire:model.change="tenant_id" id="tenant_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                        <option value="">Wählen...</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->first_name }} {{ $tenant->last_name }}</option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mietobjekt -->
                <div>
                    <label for="rental_object_id" class="block text-sm font-medium text-gray-700">Mietobjekt</label>
                    <select wire:model="rental_object_id" id="rental_object_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                        <option value="">Wählen...</option>
                        @foreach ($rentalObjects as $object)
                            <option value="{{ $object->id }}">{{ $object->name }}, {{ $object->street }},
                                {{ $object->city }}</option>
                        @endforeach
                    </select>
                    @error('rental_object_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jahr -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Jahr</label>
                    <select wire:model.change="year" id="year"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                        <option value="">Jahr auswählen...</option>
                        @foreach ($availableYears as $yr)
                            <option value="{{ $yr }}">{{ $yr }}</option>
                        @endforeach
                    </select>
                    @error('year') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Monat -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Monat</label>
                    <select wire:model="month" id="month"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                        <option value="">Monat auswählen...</option>
                        @foreach ($availableMonths as $mo)
                            <option value="{{ $mo }}">
                                {{ DateTime::createFromFormat('!m', $mo)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    @error('month') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Betrag -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Betrag (€)</label>
                    <input type="number" step="0.01" wire:model="amount" id="amount"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                    @error('amount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Zahlungsdatum -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Zahlungsdatum</label>
                    <input type="date" wire:model="payment_date" id="payment_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        required>
                    @error('payment_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    {{ $editMode ? 'Zahlung aktualisieren' : 'Zahlung hinzufügen' }}
                </button>
                <button type="button" wire:click="resetFields"
                    class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md shadow hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    Zurücksetzen
                </button>
            </div>
        </form>
    </div>

    <!-- Tabelle -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-medium text-gray-700">Zahlungsliste</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left">Mieter</th>
                        <th class="px-4 py-2 text-left">Mietobjekt</th>
                        <th class="px-4 py-2 text-left">Jahr</th>
                        <th class="px-4 py-2 text-left">Monat</th>
                        <th class="px-4 py-2 text-left">Betrag (€)</th>
                        <th class="px-4 py-2 text-left">Zahlungsdatum</th>
                        <th class="px-4 py-2 text-left">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($payments as $payment)
                        <tr>
                            <td class="px-4 py-2">{{ $payment->tenant->first_name }} {{ $payment->tenant->last_name }}</td>
                            <td class="px-4 py-2">{{ $payment->rentalObject->name }}, {{ $payment->rentalObject->city }}</td>
                            <td class="px-4 py-2">{{ $payment->year }}</td>
                            <td class="px-4 py-2">{{ DateTime::createFromFormat('!m', $payment->month)->format('F') }}</td>
                            <td class="px-4 py-2">{{ number_format($payment->amount, 2, ',', '.') }} €</td>
                            <td class="px-4 py-2">{{ $payment->payment_date }}</td>
                            <td class="px-4 py-2 space-x-2">
                                <button wire:click="editPayment({{ $payment->id }})"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded-md text-xs hover:bg-yellow-600">
                                    Bearbeiten
                                </button>
                                <button wire:click="deletePayment({{ $payment->id }})"
                                    onclick="return confirm('Sind Sie sicher?')"
                                    class="px-3 py-1 bg-red-600 text-white rounded-md text-xs hover:bg-red-700">
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
