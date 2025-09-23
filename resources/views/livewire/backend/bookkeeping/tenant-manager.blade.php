<div class="max-w-6xl mx-auto space-y-6">
    <!-- Formular -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">
            {{ $tenantId ? "Tenant bearbeiten" : "Neuen Tenant anlegen" }}
        </h2>

        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Name</label>
                <input type="text" wire:model="name" class="form-input">
                @error("name")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="form-label">Slug</label>
                <input type="text" wire:model="slug" class="form-input">
                @error("slug")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="form-label">E-Mail</label>
                <input type="email" wire:model="email" class="form-input">
            </div>
            <div>
                <label class="form-label">Telefon</label>
                <input type="text" wire:model="phone" class="form-input">
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Adresse</label>
                <input type="text" wire:model="street" class="form-input" placeholder="Straße">
                <div class="flex gap-2 mt-2">
                    <input type="text" wire:model="house_number" class="form-input w-24"
                        placeholder="Nr.">
                    <input type="text" wire:model="zip" class="form-input w-32"
                        placeholder="PLZ">
                    <input type="text" wire:model="city" class="form-input flex-1"
                        placeholder="Ort">
                </div>
            </div>
            <div>
                <label class="form-label">Land</label>
                <input type="text" wire:model="country" class="form-input">
            </div>

            <div>
                <label class="form-label">Steuernummer</label>
                <input type="text" wire:model="tax_number" class="form-input">
            </div>
            <div>
                <label class="form-label">USt-ID</label>
                <input type="text" wire:model="vat_id" class="form-input">
            </div>
            <div>
                <label class="form-label">Handelsregister</label>
                <input type="text" wire:model="commercial_register" class="form-input">
            </div>
            <div>
                <label class="form-label">Registergericht</label>
                <input type="text" wire:model="court_register" class="form-input">
            </div>

            <div>
                <label class="form-label">Bankname</label>
                <input type="text" wire:model="bank_name" class="form-input">
            </div>
            <div>
                <label class="form-label">IBAN</label>
                <input type="text" wire:model="iban" class="form-input">
            </div>
            <div>
                <label class="form-label">BIC</label>
                <input type="text" wire:model="bic" class="form-input">
            </div>

            <div>
                <label class="form-label">Geschäftsjahresbeginn</label>
                <input type="date" wire:model="fiscal_year_start" class="form-input">
            </div>
            <div>
                <label class="form-label">Währung</label>
                <input type="text" wire:model="currency" class="form-input">
            </div>

            <div class="flex items-center col-span-2">
                <input type="checkbox" wire:model="active"
                    class="h-4 w-4 text-pink-600 border-gray-300 rounded">
                <label class="ml-2 text-sm text-gray-700">Aktiv</label>
            </div>

            <div>
                <label class="form-label">Kontorahmen</label>
                <select wire:model="chart_of_accounts" class="form-select">
                    @foreach (\App\Services\ChartOfAccountsService::getFrameworks() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 pt-4">
                <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Speichern
                </button>
                @if ($tenantId)
                    <button type="button" wire:click="resetForm"
                        class="ml-2 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Abbrechen
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabelle -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">Vorhandene Tenants</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Ort</th>
                    <th>USt-ID</th>
                    <th>Bank</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tenants as $t)
                    <tr class="border-b">
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->slug }}</td>
                        <td>{{ $t->zip }} {{ $t->city }}</td>
                        <td>{{ $t->vat_id }}</td>
                        <td>{{ $t->bank_name }}</td>
                        <td>
                            @if ($t->active)
                                <span class="text-green-600">aktiv</span>
                            @else
                                <span class="text-red-600">inaktiv</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="edit({{ $t->id }})"
                                class="text-pink-600 hover:underline">Bearbeiten</button>
                        </td>

                        <td>
                            @if ($t->is_current)
                                <span
                                    class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">aktuell</span>
                            @else
                                <button wire:click="setCurrent({{ $t->id }})"
                                    class="px-2 py-1 bg-gray-200 rounded text-xs hover:bg-gray-300">
                                    aktivieren
                                </button>
                            @endif
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
