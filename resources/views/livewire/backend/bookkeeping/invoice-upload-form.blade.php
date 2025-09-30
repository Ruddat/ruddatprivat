<div>
    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="p-3 mb-4 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="p-3 mb-4 rounded bg-yellow-100 text-yellow-800">{{ session('warning') }}</div>
    @endif
    @if(session('success'))
        <div class="p-3 mb-4 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    {{-- Tenant Auswahl --}}
<div class="mb-4">
    <label class="form-label flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 7v10a1 1 0 001 1h16a1 1 0 001-1V7M9 21V9m6 12V9m-9 0h12"/>
        </svg>
        Firma / Mandant
    </label>
    <select wire:model.change="tenantId" class="form-select mt-1">
        <option value="">-- bitte auswählen --</option>
        @foreach(\App\Models\Tenant::where('customer_id', auth('customer')->id())->get() as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
        @endforeach
    </select>
</div>

    {{-- Upload / Preview --}}
    @if(empty($previews))
        <form wire:submit.prevent="parsePdfs" class="space-y-4">
            <input type="file" wire:model="files" multiple class="form-input">
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded">
                Vorschau anzeigen
            </button>
        </form>
    @else
        <h3 class="font-bold mb-2">Vorschau – bitte prüfen:</h3>

        @foreach($previews as $index => $p)
            <div class="border rounded p-4 mb-4 {{ $p['duplicate'] ? 'bg-red-50 border-red-300' : 'bg-gray-50' }} space-y-2">
                @if($p['duplicate'])
                    <div class="p-2 rounded bg-yellow-100 text-yellow-800 font-semibold">
                        ⚠️ Beleg {{ $p['number'] }} ist schon vorhanden – wird beim Speichern automatisch übersprungen.
                    </div>
                @endif

                <div>
                    <label>Belegnummer</label>
                    <input type="text" wire:model="previews.{{ $index }}.number"
                           class="form-input {{ $p['duplicate'] ? 'border-red-400 bg-red-50' : '' }}">
                </div>
                <div>
                    <label>Datum</label>
                    <input type="date" wire:model="previews.{{ $index }}.date" class="form-input">
                </div>
                <div>
                    <label>Nettobetrag (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.net_amount" class="form-input">
                </div>
                <div>
                    <label>MwSt. (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.vat_amount" class="form-input">
                </div>
                <div>
                    <label>Bruttobetrag (€)</label>
                    <input type="number" step="0.01" wire:model="previews.{{ $index }}.gross_amount" class="form-input">
                </div>

                {{-- Konto-Auswahl --}}
                <div>
                    <label>Erlöskonto</label>
                    <select wire:model="previews.{{ $index }}.revenueAccountId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Umsatzsteuerkonto</label>
                    <select wire:model="previews.{{ $index }}.vatAccountId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Bankkonto</label>
                    <select wire:model="previews.{{ $index }}.bankAccountId" class="form-select">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->number }} – {{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach

        @php
            $duplicates = collect($previews)->where('duplicate', true)->count();
            $newOnes    = collect($previews)->where('duplicate', false)->count();
        @endphp

        <button wire:click="saveAll" class="px-4 py-2 bg-green-600 text-white rounded">
            @if($duplicates > 0)
                {{ $newOnes }} neue Belege speichern ({{ $duplicates }} Dubletten übersprungen)
            @else
                Alle speichern & buchen
            @endif
        </button>
        <button wire:click="$set('previews', [])" class="px-4 py-2 bg-gray-400 text-white rounded">
            Abbrechen
        </button>
    @endif

    {{-- Liste hochgeladener Belege --}}
<div class="mt-6">
    <h3 class="font-bold mb-2">Hochgeladene Belege</h3>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Datum</th>
                    <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">Netto (€)</th>
                    <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">MwSt. (€)</th>
                    <th class="px-4 py-2 text-right text-sm font-semibold text-gray-600">Brutto (€)</th>
                    <th class="px-4 py-2 text-center text-sm font-semibold text-gray-600">Beleg</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($invoices as $inv)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ $inv->date?->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700 text-right">
                            {{ number_format($inv->net_amount, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700 text-right">
                            {{ number_format($inv->vat_amount, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-sm font-medium text-gray-900 text-right">
                            {{ number_format($inv->gross_amount, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-sm text-center">
                            <a href="{{ Storage::url($inv->file_path) }}" 
                               target="_blank" 
                               class="text-pink-600 hover:underline">
                                PDF ansehen
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-gray-500 italic">
                            Keine Belege vorhanden
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if($invoices->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr class="font-bold">
                        <td class="px-4 py-2 text-right">Summe:</td>
                        <td class="px-4 py-2 text-right text-gray-800">
                            {{ number_format($invoices->sum('net_amount'), 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-right text-gray-800">
                            {{ number_format($invoices->sum('vat_amount'), 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2 text-right text-gray-900">
                            {{ number_format($invoices->sum('gross_amount'), 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

</div>
