<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Gewinn -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-medium text-gray-500">Gewinn</h3>
        <p class="mt-2 text-3xl font-bold text-gray-800">
            {{ number_format($profit, 2, ",", ".") }} €
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Erlöse: {{ number_format($revenues, 2, ",", ".") }} € –
            Aufwände: {{ number_format($expenses, 2, ",", ".") }} €
        </p>
    </div>

    <!-- Umsatzsteuer -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-medium text-gray-500">Umsatzsteuer-Zahllast</h3>
        <p
            class="mt-2 text-3xl font-bold {{ $tax_liability >= 0 ? "text-red-600" : "text-green-600" }}">
            {{ number_format($tax_liability, 2, ",", ".") }} €
        </p>
        <p class="text-xs text-gray-400 mt-1">
            USt: {{ number_format($ust, 2, ",", ".") }} € –
            VSt: {{ number_format($vorsteuer, 2, ",", ".") }} €
        </p>
    </div>

    <!-- Fixkosten -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-sm font-medium text-gray-500">Fixkosten</h3>
        <p class="mt-2 text-3xl font-bold text-gray-800">
            {{ number_format($fixcosts, 2, ",", ".") }} €
        </p>
        <p class="text-xs text-gray-400 mt-1">
            inkl. PayPal, Hosting, Lizenz, Kontoführung
        </p>
    </div>
</div>
