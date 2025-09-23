<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Umsatzsteuer-Voranmeldung</h2>

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
            <span class="{{ $zahllast >= 0 ? "text-red-700" : "text-green-700" }}">
                {{ number_format($zahllast, 2, ",", ".") }} €
            </span>
        </div>
    </div>
</div>
