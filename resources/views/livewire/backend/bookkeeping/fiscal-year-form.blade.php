<div class="max-w-4xl mx-auto space-y-6">
    <!-- Neues Jahr -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">Neues Buchungsjahr</h2>
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Jahr</label>
                <input type="number" wire:model="year" class="form-input">
                @error("year")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="form-label">Startdatum</label>
                <input type="date" wire:model="start_date" class="form-input">
                @error("start_date")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="form-label">Enddatum</label>
                <input type="date" wire:model="end_date" class="form-input">
                @error("end_date")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex items-center">
                <input type="checkbox" wire:model="closed" id="closed"
                    class="h-4 w-4 text-pink-600">
                <label for="closed" class="ml-2 text-sm text-gray-700">geschlossen anlegen</label>
            </div>
            <div class="col-span-2 pt-2">
                <button type="submit"
                    class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Anlegen
                </button>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">Buchungsjahre</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-gray-600">
                    <th class="px-2 py-1">Jahr</th>
                    <th class="px-2 py-1">Zeitraum</th>
                    <th class="px-2 py-1">Status</th>
                    <th class="px-2 py-1"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($years as $y)
                    <tr class="border-b">
                        <td class="px-2 py-1">{{ $y->year }}</td>
                        <td class="px-2 py-1">{{ $y->start_date }} – {{ $y->end_date }}</td>
                        <td class="px-2 py-1">
                            @if ($y->is_current)
                                <span
                                    class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">aktuell</span>
                            @endif
                            @if ($y->closed)
                                <span
                                    class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">geschlossen</span>
                            @endif
                        </td>
                        <td class="px-2 py-1 space-x-2">
                            <button wire:click="setCurrent({{ $y->id }})"
                                class="text-blue-600 hover:underline">aktivieren</button>
                            <button wire:click="toggleClosed({{ $y->id }})"
                                class="text-pink-600 hover:underline">
                                {{ $y->closed ? "öffnen" : "schließen" }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
