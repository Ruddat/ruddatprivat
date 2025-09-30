<div class="bg-white rounded-xl shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            Umsatzsteuer-Voranmeldung
        </h2>

        <div>
            <label class="text-sm text-gray-600">Geschäftsjahr:</label>
            <select wire:model.change="yearId" class="form-select ml-2">
                <option value="">-- wählen --</option>
                @foreach($years as $y)
                    <option value="{{ $y->id }}">
                        {{ $y->year }} ({{ \Carbon\Carbon::parse($y->start_date)->format('d.m.Y') }}
                        – {{ \Carbon\Carbon::parse($y->end_date)->format('d.m.Y') }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    @if(!$yearId)
        <div class="text-gray-500 italic">Bitte ein Geschäftsjahr auswählen.</div>
    @else
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Umsatzsteuer (1776)</span>
                <span class="font-medium text-gray-800">
                    {{ number_format($ust, 2, ",", ".") }} €
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">abzgl. Vorsteuer (1576)</span>
                <span class="font-medium text-gray-800">
                    {{ number_format($vorsteuer, 2, ",", ".") }} €
                </span>
            </div>
            <hr>
            <div class="flex justify-between text-lg font-bold">
                <span>Zahllast</span>
                <span class="{{ $zahllast >= 0 ? 'text-red-700' : 'text-green-700' }}">
                    {{ number_format($zahllast, 2, ",", ".") }} €
                </span>
            </div>
        </div>
    @endif
</div>
