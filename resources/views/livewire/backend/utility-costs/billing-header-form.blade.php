<div class="p-6">
    <!-- Toggle Button -->
    <button wire:click="toggleForm"
        class="mb-4 inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
        {{ $showForm ? "Formular ausblenden" : "Abrechnungskopf hinzufügen" }}
    </button>

    <!-- Formular -->
    @if ($showForm)
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Abrechnungskopf hinzufügen</h2>

            <form wire:submit.prevent="saveHeader" class="space-y-6">
                <!-- Name -->
                <x-form.input label="Name" id="creator_name" name="creator_name"
                    wire:model="creator_name" />

                <!-- Vorname -->
                <x-form.input label="Vorname" id="first_name" name="first_name"
                    wire:model="first_name" />

                <!-- Straße + Hausnummer -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <x-form.input label="Straße" id="street" name="street"
                            wire:model="street" />
                    </div>
                    <div>
                        <x-form.input label="Hausnummer" id="house_number" name="house_number"
                            wire:model="house_number" />
                    </div>
                </div>

                <!-- PLZ + Ort -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-form.input label="PLZ" id="zip_code" name="zip_code"
                            wire:model="zip_code" />
                    </div>
                    <div class="md:col-span-2">
                        <x-form.input label="Ort" id="city" name="city"
                            wire:model="city" />
                    </div>
                </div>

                <!-- Telefon -->
                <x-form.input label="Telefon (optional)" id="phone" name="phone"
                    wire:model="phone" />

                <!-- E-Mail -->
                <x-form.input label="E-Mail (optional)" id="email" name="email" type="email"
                    wire:model="email" />

                <!-- Bankdaten -->
                <x-form.input label="Bankname (optional)" id="bank_name" name="bank_name"
                    wire:model="bank_name" />

                <x-form.input label="IBAN (optional)" id="iban" name="iban"
                    wire:model="iban" />

                <x-form.input label="BIC (optional)" id="bic" name="bic"
                    wire:model="bic" />

                <!-- Fußtext -->
                <div>
<x-form.textarea
    label="Fußtext (optional)"
    id="footer_text"
    name="footer_text"
    wire:model="footer_text" />
                </div>

                <!-- Notizen -->
                <div>
<!-- Notizen -->
<x-form.textarea
    label="Notizen"
    id="notes"
    name="notes"
    wire:model="notes" />
                </div>

                <!-- Logo Upload -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Logo
                        (optional)</label>
                    <input type="file" wire:model="logo" id="logo"
                        class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    @error("logo")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($logoPreview)
                        <img src="{{ $logoPreview }}" alt="Logo Vorschau"
                            class="mt-3 h-16 rounded shadow">
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md shadow hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    Abrechnungskopf speichern
                </button>
            </form>
        </div>
    @endif

    <!-- Tabelle -->
    <div class="bg-white shadow rounded-lg p-6 mt-8 overflow-x-auto">
        <h5 class="text-lg font-semibold text-gray-800 mb-4">Gespeicherte Abrechnungsköpfe</h5>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Vorname</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Adresse</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Telefon</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">E-Mail</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Logo</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($billingHeaders as $header)
                    <tr>
                        <td class="px-4 py-2">{{ $header->creator_name }}</td>
                        <td class="px-4 py-2">{{ $header->first_name }}</td>
                        <td class="px-4 py-2">
                            {{ $header->street }} {{ $header->house_number }},
                            {{ $header->zip_code }} {{ $header->city }}
                        </td>
                        <td class="px-4 py-2">{{ $header->phone ?? "-" }}</td>
                        <td class="px-4 py-2">{{ $header->email ?? "-" }}</td>
                        <td class="px-4 py-2">
                            @if ($header->logo_path)
                                <img src="{{ asset("storage/" . $header->logo_path) }}"
                                    alt="Logo" class="h-10 rounded shadow">
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <button wire:click="deleteHeader({{ $header->id }})"
                                onclick="return confirm('Sicher löschen?')"
                                class="px-2 py-1 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
