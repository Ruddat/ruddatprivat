<div>
    @if(session('error'))
        <div class="p-3 mb-4 rounded bg-red-100 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="p-3 mb-4 rounded bg-yellow-100 text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('success'))
        <div class="p-3 mb-4 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
    <label class="form-label">Firma / Mandant</label>
    <select wire:model="tenantId" class="form-select">
        <option value="">-- bitte auswählen --</option>
        @foreach(\App\Models\Tenant::where('customer_id', auth('customer')->id())->get() as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
        @endforeach
    </select>
</div>






    @if(empty($previews))
        <form wire:submit.prevent="parseReceipts" class="space-y-4">
            <input type="file" wire:model="files" multiple class="form-input">
            <button class="px-4 py-2 bg-pink-600 text-white rounded">OCR starten</button>
        </form>
    @else
<h3 class="font-bold mb-4">Vorschau Tankbelege</h3>
<div class="grid gap-6">
    @foreach($previews as $index => $p)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded p-4 bg-gray-50">
            
            <!-- Linke Seite: Beleg-Bild -->
            <div>
                <p class="text-sm font-semibold mb-2">Original-Beleg</p>
                @if(is_object($p['file']) && method_exists($p['file'], 'temporaryUrl'))
                    <img src="{{ $p['file']->temporaryUrl() }}" class="w-full rounded shadow">
                @else
                    <span class="text-gray-500">Kein Bild verfügbar</span>
                @endif
            </div>

            <!-- Rechte Seite: OCR-Werte (editierbar) -->
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Datum</label>
                    <input type="date" wire:model="previews.{{ $index }}.date" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Netto (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.net" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">MwSt. (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.vat" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Brutto (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.gross" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Fahrzeugkonto</label>
                    <select wire:model="previews.{{ $index }}.vehicleId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vorsteuerkonto</label>
                    <select wire:model="previews.{{ $index }}.vatId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Bankkonto</label>
                    <select wire:model="previews.{{ $index }}.bankId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4 flex gap-2">
    <button wire:click="saveAll" class="px-4 py-2 bg-green-600 text-white rounded">Alle buchen</button>
    <button wire:click="$set('previews',[])" class="px-4 py-2 bg-gray-400 text-white rounded">Abbrechen</button>
</div>

    @endif
</div>
