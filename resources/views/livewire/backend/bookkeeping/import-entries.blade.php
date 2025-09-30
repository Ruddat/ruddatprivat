<div class="p-6 bg-white shadow rounded-xl">
    <h2 class="text-lg font-semibold mb-4">Buchungen importieren</h2>

    <form wire:submit.prevent="import" class="space-y-4">

        <!-- Auswahl Buchhaltung -->
        <div>
            <label for="source" class="block text-sm font-medium">Quelle</label>
            <select wire:model="source" id="source" class="form-select mt-1">
                <option value="ms_buchhalter">MS Buchhalter</option>
                <option value="datev">DATEV</option>
                <option value="lexoffice">Lexoffice</option>
            </select>
        </div>


<!-- Geschäftsjahr -->
<div>
    <label class="block text-sm font-medium">Geschäftsjahr</label>
    <select wire:model="fiscalYearId" class="form-select mt-1">
        <option value="auto">Automatisch (nach Buchungsdatum)</option>
        @foreach ($fiscalYearOptions as $fy)
            <option value="{{ $fy['id'] }}">{{ $fy['text'] }}</option>
        @endforeach
    </select>
    <p class="text-xs text-gray-500 mt-1">
        Tipp: Lass „Automatisch“, wenn dein Geschäftsjahr über Start/Ende sauber definiert ist.
    </p>
</div>

        <!-- Datei Upload -->
        <div>
            <label for="file" class="block text-sm font-medium">CSV-Datei</label>
            <input type="file" wire:model="file" id="file" accept=".csv,.txt" class="form-input mt-1">
            @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-500 mt-1">Erwartetes Format: CSV mit Semikolon-Trenner, Encoding: UTF-8 oder ISO-8859-1</p>
        </div>

        <!-- Loading Indicator -->
        <div wire:loading wire:target="file" class="text-blue-500">
            Verarbeite Datei für Vorschau...
        </div>

        <!-- Vorschau -->
        @if (!empty($preview))
            <div class="mt-6">
                <h3 class="text-sm font-medium mb-2">Vorschau (erste 10 Zeilen)</h3>
                <p class="text-xs text-gray-500 mb-2">
                    Überprüfe ob die Spalten korrekt erkannt wurden. 
                    @if(isset($preview[0]['Buchungsdatum']))
                        <span class="text-green-600">✓ Format erkannt</span>
                    @else
                        <span class="text-red-600">⚠ Format könnte falsch sein</span>
                    @endif
                </p>
                
                <div class="overflow-x-auto border rounded">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach (array_keys($preview[0]) as $col)
                                    <th class="px-3 py-2 border-b font-medium text-left">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($preview as $index => $row)
                                <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    @foreach ($row as $cell)
                                        <td class="px-3 py-2 border-b whitespace-nowrap">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Import Button -->
                <div class="mt-4 flex items-center gap-4">
                    <button type="submit"
                        class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 disabled:bg-gray-400"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="import">Import starten</span>
                        <span wire:loading wire:target="import">Importiere...</span>
                    </button>
                    
                    <button type="button" 
                        wire:click="$set('file', null)"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Abbrechen
                    </button>
                </div>
            </div>
        @endif
    </form>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mt-4 p-3 text-green-800 bg-green-100 border border-green-200 rounded">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mt-4 p-3 text-amber-800 bg-amber-100 border border-amber-200 rounded">
            ⚠ {{ session('warning') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 p-3 text-red-800 bg-red-100 border border-red-200 rounded">
            ❌ {{ session('error') }}
        </div>
    @endif
</div>