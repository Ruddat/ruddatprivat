<div class="bg-white shadow rounded-xl p-6">
    <h2 class="text-lg font-semibold mb-4">Eröffnungsbilanz erfassen</h2>

    @if (session()->has("error"))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            {{ session("error") }}
        </div>
    @endif
    @if (session()->has("success"))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            {{ session("success") }}
        </div>
    @endif

    @if (session()->has("totals"))
        @php $t = session('totals'); @endphp
        <div class="mt-4 p-3 bg-gray-100 rounded text-sm">
            <strong>Summen:</strong>
            Soll {{ number_format($t["soll"], 2, ",", ".") }} € |
            Haben {{ number_format($t["haben"], 2, ",", ".") }} € |
            Differenz {{ number_format($t["differenz"], 2, ",", ".") }} €
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-2">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 text-left">Konto</th>
                        <th class="px-2 py-1 text-left">Name</th>
                        <th class="px-2 py-1 text-right">Soll</th>
                        <th class="px-2 py-1 text-right">Haben</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($accounts as $acc)
                        <tr class="border-t">
                            <td class="px-2 py-1">{{ $acc->number }}</td>
                            <td class="px-2 py-1">{{ $acc->name }}</td>
                            <td class="px-2 py-1">
                                <input type="number" step="0.01"
                                    wire:model.change="balances.{{ $acc->id }}.debit"
                                    class="form-input w-28 text-right" placeholder="0,00">
                            </td>
                            <td class="px-2 py-1">
                                <input type="number" step="0.01"
                                    wire:model.change="balances.{{ $acc->id }}.credit"
                                    class="form-input w-28 text-right" placeholder="0,00">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pt-4">
            <button type="submit"
                class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                Speichern
            </button>
        </div>
    </form>

    <!-- Upload separat -->
    <div class="mt-8">
        <label class="form-label">Saldenliste importieren</label>
        <input type="file" wire:model="file" class="form-input">
        @error("file")
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <button type="button" wire:click="import"
            class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Importieren
        </button>
    </div>
</div>
