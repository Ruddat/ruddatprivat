<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow-md">

    <div class="mb-4">
        <label class="form-label">Mandant</label>
        <select wire:model.change="tenantId" class="form-select">
            @foreach (\App\Models\Tenant::orderBy("name")->get() as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
        </select>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Datum -->
        <div>
            <label for="booking_date" class="form-label">Datum</label>
            <input type="date" id="booking_date" wire:model.change="booking_date"
                class="form-input">
            @error("booking_date")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            @error("fiscal_year_id")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            @if (session()->has("error"))
                <div class="mt-2 text-red-600 text-sm">
                    {{ session("error") }}
                </div>
            @endif

        </div>

        <!-- Eingabeart -->
        <div>
            <label for="input_mode" class="form-label">Eingabe</label>
            <select id="input_mode" wire:model="input_mode" class="form-select">
                <option value="netto">Nettobetrag eingeben</option>
                <option value="brutto">Bruttobetrag eingeben</option>
            </select>
        </div>

        <!-- Betrag -->
        <div>
            <label class="form-label">
                Betrag ({{ $input_mode === "brutto" ? "Brutto" : "Netto" }})
            </label>
            <input type="number" step="0.01" wire:model="net_amount" class="form-input">
            @error("net_amount")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- MwSt Checkbox -->
        <div class="flex items-center md:col-span-2">
            <input type="checkbox" id="with_vat" wire:model="with_vat"
                class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
            <label for="with_vat" class="ml-2 text-sm text-gray-700">Mit MwSt</label>
        </div>

        <!-- MwSt Eingabe -->
        @if ($with_vat)
            <div>
                <label for="vat_rate" class="form-label">MwSt %</label>
                <input type="number" step="1" id="vat_rate" wire:model="vat_rate"
                    class="form-input">
                @error("vat_rate")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endif

        <!-- Soll-Konto -->
        <div>
            <label for="debit_account_id" class="form-label">Soll-Konto</label>
            <select id="debit_account_id" wire:model="debit_account_id" class="form-select">
                <option value="">-- wählen --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->number }} -
                        {{ $account->name }}</option>
                @endforeach
            </select>
            @error("debit_account_id")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Haben-Konto -->
        <div>
            <label for="credit_account_id" class="form-label">Haben-Konto</label>
            <select id="credit_account_id" wire:model="credit_account_id" class="form-select">
                <option value="">-- wählen --</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->number }} -
                        {{ $account->name }}</option>
                @endforeach
            </select>
            @error("credit_account_id")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Beschreibung -->
        <div class="md:col-span-2">
            <label for="description" class="form-label">Beschreibung</label>
            <input type="text" id="description" wire:model="description" class="form-input">
            @error("description")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit -->
        <div class="md:col-span-2 pt-4">
            <button type="submit"
                class="w-full inline-flex justify-center rounded-lg border border-transparent
                           bg-pink-600 px-4 py-2 text-sm font-medium text-white shadow-sm
                           hover:bg-pink-700 focus:outline-none focus:ring-2
                           focus:ring-pink-500 focus:ring-offset-2 transition">
                Speichern
            </button>
        </div>
    </form>

    <!-- Vorschau -->
    @if ($this->getPreview())
        <div class="mt-6 bg-gray-50 border rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Vorschau der Buchungssätze</h3>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-gray-600">
                        <th class="px-2 py-1 text-left">Datum</th>
                        <th class="px-2 py-1 text-left">Soll</th>
                        <th class="px-2 py-1 text-left">Haben</th>
                        <th class="px-2 py-1 text-right">Betrag</th>
                        <th class="px-2 py-1 text-left">Beschreibung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($this->getPreview() as $row)
                        <tr>
                            <td class="px-2 py-1">{{ $row["date"] }}</td>
                            <td class="px-2 py-1">{{ $row["debit"]->number }} –
                                {{ $row["debit"]->name }}</td>
                            <td class="px-2 py-1">{{ $row["credit"]->number }} –
                                {{ $row["credit"]->name }}</td>
                            <td class="px-2 py-1 text-right">
                                {{ number_format($row["amount"], 2, ",", ".") }}</td>
                            <td class="px-2 py-1 text-gray-500">{{ $row["desc"] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
