<div class="space-y-6">
{{-- Header --}}
<div class="flex justify-between items-center">
    <h2 class="text-xl font-bold text-gray-800">
        {{ $receiptTypes[$receiptType] ?? 'Quittungen' }}
    </h2>
    <button wire:click="toggleForm"
        class="px-4 py-2 rounded text-white font-medium transition
               {{ $showForm ? 'bg-gray-600 hover:bg-gray-700' : 'bg-pink-600 hover:bg-pink-700' }}">
        {{ $showForm ? 'Formular schließen' : '➕ Neue ' . ($receiptTypes[$receiptType] ?? 'Quittung') }}
    </button>
</div>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formular --}}
    @if ($showForm)
        <form wire:submit.prevent="saveReceipt" class="bg-white rounded shadow p-6 space-y-6">
{{-- Schnelle Vorlagen-Auswahl --}}
@if(count($availableTemplates) > 0)
<div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
    <label for="selectedTemplateForUse" class="block text-sm font-medium text-blue-800 mb-2">
        🚀 Schnellvorlage verwenden
    </label>
    <div class="flex gap-2">
        <select id="selectedTemplateForUse" wire:model="selectedTemplateForUse"
                class="flex-1 rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">-- Vorlage auswählen --</option>
            @foreach($availableTemplates as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <button type="button" wire:click="loadTemplateData"
                wire:loading.attr="disabled"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm disabled:opacity-50">
            <span wire:loading.remove>Laden</span>
            <span wire:loading>Lädt...</span>
        </button>
    </div>
    <p class="text-xs text-blue-600 mt-1">Wählen Sie eine gespeicherte Vorlage aus und klicken Sie auf "Laden".</p>
</div>
@endif

{{-- Quittungstyp Auswahl --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div>
        <label for="receiptType" class="block text-sm font-medium text-gray-700">Quittungstyp *</label>
        <select id="receiptType" wire:model="receiptType" required
                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
            @foreach($receiptTypes as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @error('receiptType') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titel</label>
        <input type="text" id="title" wire:model="title"
               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
               placeholder="Automatisch basierend auf Typ">
        @error('title') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
    </div>
</div>

            {{-- Absender Adresse --}}
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Absender (Vermieter)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="sender" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" id="sender" wire:model="sender" required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_street" class="block text-sm font-medium text-gray-700">Straße</label>
                        <input type="text" id="sender_street" wire:model="sender_street"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_street') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_house_number" class="block text-sm font-medium text-gray-700">Hausnummer</label>
                        <input type="text" id="sender_house_number" wire:model="sender_house_number"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_house_number') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_zip" class="block text-sm font-medium text-gray-700">PLZ</label>
                        <input type="text" id="sender_zip" wire:model="sender_zip"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_zip') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_city" class="block text-sm font-medium text-gray-700">Stadt</label>
                        <input type="text" id="sender_city" wire:model="sender_city"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_city') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input type="text" id="sender_phone" wire:model="sender_phone"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="sender_email" class="block text-sm font-medium text-gray-700">E-Mail</label>
                        <input type="email" id="sender_email" wire:model="sender_email"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="sender_tax_number" class="block text-sm font-medium text-gray-700">Steuernummer</label>
                        <input type="text" id="sender_tax_number" wire:model="sender_tax_number"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('sender_tax_number') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Empfänger Adresse --}}
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Empfänger (Mieter)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="receiver" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input type="text" id="receiver" wire:model="receiver" required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="receiver_street" class="block text-sm font-medium text-gray-700">Straße</label>
                        <input type="text" id="receiver_street" wire:model="receiver_street"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_street') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="receiver_house_number" class="block text-sm font-medium text-gray-700">Hausnummer</label>
                        <input type="text" id="receiver_house_number" wire:model="receiver_house_number"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_house_number') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="receiver_zip" class="block text-sm font-medium text-gray-700">PLZ</label>
                        <input type="text" id="receiver_zip" wire:model="receiver_zip"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_zip') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="receiver_city" class="block text-sm font-medium text-gray-700">Stadt</label>
                        <input type="text" id="receiver_city" wire:model="receiver_city"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_city') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="receiver_phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                        <input type="text" id="receiver_phone" wire:model="receiver_phone"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_phone') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="receiver_email" class="block text-sm font-medium text-gray-700">E-Mail</label>
                        <input type="email" id="receiver_email" wire:model="receiver_email"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('receiver_email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Betrag & Steuer --}}
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Betrag & Steuern</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Betrag *</label>
                        <input type="number" step="0.01" id="amount" wire:model="amount" required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        @error('amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="taxType" class="block text-sm font-medium text-gray-700">Betragstyp</label>
                        <select id="taxType" wire:model="taxType"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                            <option value="netto">Netto (zzgl. MwSt.)</option>
                            <option value="brutto">Brutto (inkl. MwSt.)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="includeTax" wire:model="includeTax"
                               class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                        <label for="includeTax" class="text-sm text-gray-700">MwSt. hinzufügen</label>
                    </div>
                    <div>
                        <label for="customTaxPercent" class="block text-sm font-medium text-gray-700">MwSt.-Satz (%)</label>
                        <input type="number" step="0.01" id="customTaxPercent" wire:model="customTaxPercent"
                               placeholder="{{ $taxPercent }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                    </div>
                </div>
            </div>

            {{-- Datum --}}
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">Datum *</label>
                <input type="date" id="date" wire:model="date" required
                       class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('date') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Beschreibung --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Beschreibung</label>
                <textarea id="description" wire:model="description" rows="3"
                          class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                          placeholder="z.B. Miete für Januar 2024, Nebenkostenabrechnung..."></textarea>

                @if (!empty($suggestedDescriptions))
                    <ul class="mt-2 space-y-2">
                        @foreach ($suggestedDescriptions as $suggestion)
                            <li class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded text-sm">
                                <span class="cursor-pointer hover:text-pink-600"
                                      wire:click="$set('description', '{{ $suggestion }}')">
                                    {{ $suggestion }}
                                </span>
                                <button type="button" class="text-red-600 hover:underline text-xs"
                                        wire:click="deleteDescription('{{ $suggestion }}')">
                                    Löschen
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @error('description') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

{{-- Als Vorlage speichern Section erweitern --}}
<div class="border-t pt-6">
    <div class="flex items-center space-x-2 mb-4">
        <input type="checkbox" id="saveAsTemplate" wire:model.change="saveAsTemplate"
               class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
        <label for="saveAsTemplate" class="text-sm font-medium text-gray-700">
            Aktuelle Einstellungen als Vorlage speichern
            @if($saveAsTemplate)
                <span class="text-green-600 text-xs">✓ Aktiv</span>
            @endif
        </label>
    </div>

    @if($saveAsTemplate)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-yellow-50 p-4 rounded border border-yellow-200">
        <div>
            <label for="templateName" class="block text-sm font-medium text-gray-700">Vorlagenname *</label>
            <input type="text" id="templateName" wire:model="templateName" required
                   class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
                   placeholder="z.B. Standard Rechnung">
            @error('templateName') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="templateType" class="block text-sm font-medium text-gray-700">Vorlagentyp</label>
            <select id="templateType" wire:model="templateType"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @foreach($receiptTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
</div>

            {{-- Buttons --}}
            <div class="flex justify-end space-x-2 pt-6 border-t">
                <button type="button" wire:click="toggleForm"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Abbrechen
                </button>
<button type="submit"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    {{ $editMode ? 'Aktualisieren' : $receiptTypes[$receiptType] ?? 'Quittung' . ' erstellen' }}
</button>
            </div>
        </form>
    @endif

    {{-- Tabelle --}}
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nummer</th>
                    <th class="px-4 py-2 text-left">Empfänger</th>
                    <th class="px-4 py-2 text-left">Netto</th>
                    <th class="px-4 py-2 text-left">MwSt. €</th>
                    <th class="px-4 py-2 text-left">Brutto</th>
                    <th class="px-4 py-2 text-left">Datum</th>
                    <th class="px-4 py-2 text-left">Beschreibung</th>
                    <th class="px-4 py-2 text-left">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($receipts as $receipt)
                    <tr>
                        <td class="px-4 py-2 font-mono text-xs">{{ $receipt->number }}</td>
                        <td class="px-4 py-2">
                            <div class="font-medium">{{ $receipt->receiver }}</div>
                            @if($receipt->receiver_city)
                                <div class="text-xs text-gray-500">{{ $receipt->receiver_city }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ number_format($receipt->amount, 2, ',', '.') }} €</td>
                        <td class="px-4 py-2">{{ number_format($receipt->tax_amount, 2, ',', '.') }} €</td>
                        <td class="px-4 py-2 font-medium">{{ number_format($receipt->amount + $receipt->tax_amount, 2, ',', '.') }} €</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($receipt->date)->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">
                            <div class="max-w-xs truncate" title="{{ $receipt->description }}">
                                {{ $receipt->description }}
                            </div>
                        </td>
                        <td class="px-4 py-2 space-x-1">
                            <button wire:click="editReceipt({{ $receipt->id }})"
                                    class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs"
                                    title="Bearbeiten">
                                ✏️
                            </button>
                            <button wire:click="deleteReceipt({{ $receipt->id }})"
                                    class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs"
                                    title="Löschen">
                                🗑️
                            </button>
@if($receipt->pdf_path && Storage::disk('public')->exists($receipt->pdf_path))
<a href="{{ Storage::disk('public')->url($receipt->pdf_path) }}" target="_blank"
   class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs"
   title="PDF anzeigen">
    📄
</a>
                            <button wire:click="sendWhatsApp({{ $receipt->id }})"
                                    class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs"
                                    title="Per WhatsApp senden">
                                💬
                            </button>
                            @else
                            <span class="px-2 py-1 bg-gray-400 text-white rounded text-xs cursor-not-allowed"
                                  title="PDF nicht verfügbar">
                                📄
                            </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $receipts->links() }}
    </div>

    {{-- Info Box für Vorlagen --}}
    @if($templates->count() > 0)
    <div class="bg-blue-50 border border-blue-200 rounded p-4 mt-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Vorlagen verfügbar</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Sie haben {{ $templates->count() }} gespeicherte Vorlagen. Wählen Sie eine Vorlage aus, um die Absenderdaten automatisch zu füllen.</p>
                </div>
            </div>
        </div>
    </div>
    @endif


{{-- Template Management --}}
@if($templates->count() > 0)
<div class="bg-white rounded shadow mt-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Gespeicherte Vorlagen</h3>
        <p class="text-sm text-gray-500 mt-1">Verwalten Sie Ihre gespeicherten Vorlagen</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Absender</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empfänger</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($templates as $template)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $template->name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $receiptTypes[$template->type] ?? $template->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $template->sender_name }}</div>
                            @if($template->sender_city)
                                <div class="text-xs text-gray-500">{{ $template->sender_city }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ $template->receiver_name }}</div>
                            @if($template->receiver_city)
                                <div class="text-xs text-gray-500">{{ $template->receiver_city }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($template->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Standard
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            {{-- Vorlage laden Button --}}
                            <button wire:click="loadTemplateById({{ $template->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    title="Diese Vorlage laden">
                                Laden
                            </button>

                            {{-- Als Standard setzen --}}
                            @if(!$template->is_default)
                            <button wire:click="setDefaultTemplate({{ $template->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    title="Als Standardvorlage setzen">
                                Standard
                            </button>
                            @endif

                            {{-- Löschen Button --}}
                            <button wire:click="deleteTemplate({{ $template->id }})"
                                    wire:confirm="Sind Sie sicher, dass Sie die Vorlage '{{ $template->name }}' löschen möchten?"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    title="Vorlage löschen">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif



</div>
