<div class="max-w-6xl mx-auto space-y-6">
    <!-- Mandantenauswahl -->
    @if($availableTenants->count() > 1)
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-3">Mandant auswählen</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($availableTenants as $availableTenant)
                <button 
                    wire:click="$set('tenantId', {{ $availableTenant->id }})"
                    class="px-4 py-2 rounded-lg border transition-all {{ $tenantId == $availableTenant->id ? 'bg-pink-600 text-white border-pink-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                >
                    {{ $availableTenant->name }}
                    @if($availableTenant->is_current)
                        <span class="text-xs ml-1">(aktuell)</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
    @endif

    @if(!$tenantId)
        <div class="bg-white p-6 rounded-xl shadow text-center text-gray-500">
            <p>Bitte wählen Sie einen Mandanten aus.</p>
        </div>
    @else
        <!-- Neues Jahr -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Neues Buchungsjahr</h2>
                @if($currentTenant)
                    <span class="text-sm text-gray-600">für {{ $currentTenant->name }}</span>
                @endif
            </div>
            
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jahr *</label>
                    <input type="number" wire:model="year" class="form-input" min="2000" max="2100" 
                           placeholder="2025" required>
                    @error("year")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Startdatum *</label>
                    <input type="date" wire:model="start_date" class="form-input" required>
                    @error("start_date")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Enddatum *</label>
                    <input type="date" wire:model="end_date" class="form-input" required>
                    @error("end_date")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" wire:model="closed" id="closed"
                        class="h-4 w-4 text-pink-600 rounded">
                    <label for="closed" class="ml-2 text-sm text-gray-700">geschlossen anlegen</label>
                </div>
                <div class="col-span-2 pt-2">
                    <button type="submit"
                        class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                        Buchungsjahr anlegen
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste -->
        <div class="bg-white p-6 rounded-xl shadow">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Buchungsjahre</h2>
                @if($currentTenant)
                    <span class="text-sm text-gray-600">{{ $currentTenant->name }}</span>
                @endif
            </div>
            
            @if($years->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <p>Noch keine Buchungsjahre angelegt.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-3 font-semibold">Jahr</th>
                                <th class="text-left py-3 font-semibold">Zeitraum</th>
                                <th class="text-left py-3 font-semibold">Status</th>
                                <th class="text-right py-3 font-semibold">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($years as $y)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 font-medium">{{ $y->year }}</td>
                                    <td class="py-3 text-gray-600">
                                        {{ \Carbon\Carbon::parse($y->start_date)->format('d.m.Y') }} – 
                                        {{ \Carbon\Carbon::parse($y->end_date)->format('d.m.Y') }}
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @if ($y->is_current)
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full font-medium">aktuell</span>
                                            @endif
                                            @if ($y->closed)
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full font-medium">geschlossen</span>
                                            @endif
                                            @if (!$y->is_current && !$y->closed)
                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full font-medium">inaktiv</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 text-right">
                                        <div class="flex justify-end space-x-2">
                                            @if (!$y->is_current)
                                                <button wire:click="setCurrent({{ $y->id }})" 
                                                    wire:confirm="Buchungsjahr {{ $y->year }} aktivieren?"
                                                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                                    aktivieren
                                                </button>
                                            @endif
                                            
                                            <button wire:click="toggleClosed({{ $y->id }})"
                                                class="px-3 py-1 text-xs {{ $y->closed ? 'bg-green-600' : 'bg-yellow-600' }} text-white rounded hover:opacity-90 transition-colors">
                                                {{ $y->closed ? "öffnen" : "schließen" }}
                                            </button>
                                            
                                            @if($y->entries_count == 0)
                                                <button wire:click="deleteYear({{ $y->id }})"
                                                    wire:confirm="Buchungsjahr {{ $y->year }} wirklich löschen?"
                                                    class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                                    löschen
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>