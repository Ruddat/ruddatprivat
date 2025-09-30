

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header mit Mandanteninfo -->
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buchung erfassen</h1>
                @if($currentTenant)
                    <p class="text-gray-600 mt-1">
                        Aktueller Mandant:
                        <span class="font-semibold text-pink-600">{{ $currentTenant->name }}</span>
                        @if($currentTenant->city)
                            <span class="text-gray-500">({{ $currentTenant->zip }} {{ $currentTenant->city }})</span>
                        @endif
                    </p>
                @endif
            </div>

            <div class="flex gap-3">
                <!-- Vorlagen Button -->
                <button type="button" wire:click="$toggle('showTemplates')"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Vorlagen
                </button>

                <!-- Vorlage speichern -->
                @if($debit_account_id && $credit_account_id && $description)
                <button type="button" wire:click="saveTemplate"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Als Vorlage
                </button>
                @endif
            </div>
        </div>

        <!-- Mandanten Auswahl -->
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mandant wechseln</label>
            <select wire:model.change="tenantId" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                @foreach ($tenants as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

<!-- Buchungsvorlagen Sidebar -->
@if($showTemplates)
<div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Buchungsvorlagen</h2>
        <button wire:click="$set('showTemplates', false)" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    @if($templates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($templates as $template)
                <div class="border border-gray-200 rounded-lg p-4 hover:border-pink-300 transition-colors duration-200 group relative">
                    <!-- Lösch-Button - nur für nicht-globale Vorlagen -->
                    @if(!$template->is_global)
                        <button wire:click="deleteTemplate({{ $template->id }})"
                                wire:confirm="Vorlage '{{ $template->name }}' wirklich löschen?"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:bg-red-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif

                    <!-- Vorlagen-Inhalt - klickbar zum Anwenden -->
                    <div class="cursor-pointer" wire:click="applyTemplate({{ $template->id }})">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-medium text-gray-900">{{ $template->name }}</h3>
                            @if($template->is_global)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Global
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Eigen
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-2">{{ $template->description }}</p>
                        <div class="text-xs text-gray-500">
                            Soll: {{ $template->debitAccount->number }} - {{ $template->debitAccount->name }}<br>
                            Haben: {{ $template->creditAccount->number }} - {{ $template->creditAccount->name }}
                            @if($template->with_vat)
                                <br>inkl. {{ $template->vat_rate }}% MwSt
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info Text -->
        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600">
                <span class="font-medium">Hinweis:</span>
                Klicken Sie auf eine Vorlage um sie zu verwenden.
                Eigene Vorlagen können mit dem <span class="text-red-500">×</span> Symbol gelöscht werden.
            </p>
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="mt-2">Keine Vorlagen vorhanden</p>
            <p class="text-sm">Speichern Sie häufig verwendete Buchungen als Vorlagen</p>
        </div>
    @endif
</div>
@endif

<!-- Hauptformular -->
<div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
    @if(session()->has('template_message'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md text-blue-700 text-sm">
            {{ session('template_message') }}
        </div>
    @endif

    <!-- Geschäftsjahr Info -->
    @if($currentFiscalYear && $fiscalYearStart && $fiscalYearEnd)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-800">Aktuelles Geschäftsjahr</h3>
                    <p class="text-blue-600">
                        {{ $currentFiscalYear->year }}
                        ({{ $fiscalYearStart->format('d.m.Y') }} - {{ $fiscalYearEnd->format('d.m.Y') }})
                    </p>
                    @if($currentFiscalYear->closed)
                        <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Geschlossen
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Offen für Buchungen
                        </span>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-600">Buchungsdatum muss innerhalb dieses Zeitraums liegen</p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-800">Kein Geschäftsjahr konfiguriert</h3>
                    <p class="text-yellow-600">
                        Für diesen Mandanten ist kein aktuelles Geschäftsjahr festgelegt.
                        Bitte legen Sie in der Geschäftsjahr-Verwaltung ein Geschäftsjahr an und markieren es als "aktuell".
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Grunddaten -->
        <div class="md:col-span-2">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Buchungsdaten</h3>
        </div>

        <!-- ✅ KORREKTER Date Input - verwende $fiscalYearStart und $fiscalYearEnd -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buchungsdatum *</label>
            <input type="date"
                   wire:model="booking_date"
                   @if($fiscalYearStart && $fiscalYearEnd)
                       min="{{ $fiscalYearStart->format('Y-m-d') }}"
                       max="{{ $fiscalYearEnd->format('Y-m-d') }}"
                   @endif
                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
            @error("booking_date")
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror

            @if($currentFiscalYear && $fiscalYearStart && $fiscalYearEnd)
                <p class="text-xs text-gray-500 mt-1">
                    Erlaubt: {{ $fiscalYearStart->format('d.m.Y') }} - {{ $fiscalYearEnd->format('d.m.Y') }}
                </p>
            @else
                <p class="text-xs text-yellow-500 mt-1">
                    Keine Datumsbeschränkung - bitte Geschäftsjahr konfigurieren
                </p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Eingabeart</label>
            <select wire:model="input_mode"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                <option value="netto">Nettobetrag eingeben</option>
                <option value="brutto">Bruttobetrag eingeben</option>
            </select>
        </div>

            <!-- Betrag und MwSt -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Betrag ({{ $input_mode === "brutto" ? "Brutto" : "Netto" }}) *
                </label>
                <input type="number" step="0.01" wire:model="net_amount"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                @error("net_amount")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <input type="checkbox" id="with_vat" wire:model="with_vat"
                           class="h-4 w-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                    <label for="with_vat" class="ml-2 text-sm text-gray-700">Mit MwSt</label>
                </div>

                @if($with_vat)
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">MwSt %</label>
                    <input type="number" step="1" wire:model="vat_rate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    @error("vat_rate")
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif
            </div>

            <!-- Konten -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Soll-Konto *</label>
                <select wire:model="debit_account_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">-- Bitte wählen --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->number }} - {{ $account->name }}</option>
                    @endforeach
                </select>
                @error("debit_account_id")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Haben-Konto *</label>
                <select wire:model="credit_account_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">-- Bitte wählen --</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->number }} - {{ $account->name }}</option>
                    @endforeach
                </select>
                @error("credit_account_id")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Beschreibung -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                <input type="text" wire:model="description"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                       placeholder="Kurze Beschreibung der Buchung">
                @error("description")
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Beleg -->
            <div class="md:col-span-2 border-t pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Beleg</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Belegtyp</label>
                        <select wire:model="receipt_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            <option value="">-- Ohne Beleg --</option>
                            <option value="fuel">Tankbeleg</option>
                            <option value="invoice">Rechnung</option>
                            <option value="receipt">Kassenbon</option>
                            <option value="other">Sonstiges</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beleg hochladen</label>
                        <input type="file" wire:model="receipt_file"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        @error("receipt_file")
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror

                        <div wire:loading wire:target="receipt_file" class="text-sm text-gray-500 mt-1">
                            Upload läuft...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="md:col-span-2 pt-6 border-t border-gray-200">
                <button type="submit"
                        class="w-full md:w-auto px-6 py-3 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition-colors duration-200 font-medium">
                    Buchung speichern
                </button>
            </div>
        </form>

        <!-- ✅ KORREKT: Popup NACH dem Formular aber VOR der Vorschau -->

        <!-- Nach der Buchung - Template Popup -->
<!-- Nach der Buchung - Template Popup -->
@if ($showTemplatePrompt && $debit_account_id && $credit_account_id)
    @php
        $similarTemplates = $this->getSimilarTemplates();
    @endphp

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-blue-800 font-medium">Buchung erfolgreich gespeichert!</span>
            </div>
            <div class="flex gap-2">
                <button type="button" wire:click="saveTemplate"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                    Als Vorlage speichern
                </button>
                <button type="button" wire:click="closeTemplatePrompt"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Nicht jetzt
                </button>
            </div>
        </div>
        <p class="text-blue-600 text-sm mt-2">
            Speichern Sie diese Buchung als Vorlage für zukünftige Verwendung.
            @if($description)
                <br><span class="font-medium">Beschreibung:</span> "{{ $description }}"
            @endif
        </p>

        <!-- Ähnliche Vorlagen Hinweis -->
        @if($similarTemplates->count() > 0)
            <div class="mt-3 pt-3 border-t border-blue-200">
                <p class="text-blue-600 text-sm font-medium mb-2">Ähnliche Vorlagen vorhanden:</p>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($similarTemplates as $similar)
                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded border">
                            <div>
                                <span class="font-medium">{{ $similar->name }}</span>
                                <span class="text-gray-500 ml-2">
                                    ({{ $similar->debitAccount->number }} / {{ $similar->creditAccount->number }})
                                </span>
                            </div>
                            <button type="button" wire:click="applyTemplate({{ $similar->id }})"
                                    class="text-blue-600 hover:text-blue-800 text-xs">
                                Verwenden
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

<!-- Alternative: Message wenn Vorlage bereits existiert -->
@if (session()->has('success') && $this->templateExists())
    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                    <p class="text-green-600 text-sm mt-1">
                        Eine Vorlage für diese Buchung existiert bereits.
                        <button type="button" wire:click="$toggle('showTemplates')"
                                class="text-green-700 underline hover:text-green-800">
                            Zu den Vorlagen
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

        <!-- Normale Success Message (falls kein Template Popup) -->
        @if (session()->has('success') && !$showTemplatePrompt)
            <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif


        <!-- Vorschau -->
        @if ($this->getPreview())
            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vorschau der Buchungssätze</h3>
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-gray-600 border-b">
                                <th class="px-3 py-2 text-left">Datum</th>
                                <th class="px-3 py-2 text-left">Soll</th>
                                <th class="px-3 py-2 text-left">Haben</th>
                                <th class="px-3 py-2 text-right">Betrag (€)</th>
                                <th class="px-3 py-2 text-left">Beschreibung</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($this->getPreview() as $row)
                                <tr>
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($row["date"])->format('d.m.Y') }}</td>
                                    <td class="px-3 py-2">{{ $row["debit"]->number }} – {{ $row["debit"]->name }}</td>
                                    <td class="px-3 py-2">{{ $row["credit"]->number }} – {{ $row["credit"]->name }}</td>
                                    <td class="px-3 py-2 text-right font-mono">
                                        {{ number_format($row["amount"], 2, ",", ".") }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ $row["desc"] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
