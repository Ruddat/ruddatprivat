<div class="w-full overflow-x-auto">
    {{-- Header mit Suche + Button --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-4 gap-2">
        <input type="text" wire:model.debounce.500ms="search"
            class="w-full sm:w-auto flex-1 rounded-lg border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm"
            placeholder="Suche nach Kunden...">

        <button wire:click="$set('showCreateModal', true)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 focus:ring-4 focus:ring-pink-200">
            + Neuer Customer
        </button>
    </div>

{{-- Tabelle Container --}}
<div class="w-full overflow-x-auto shadow-md rounded-lg">
    {{-- Desktop Ansicht (ab sm) --}}
    <table class="hidden sm:table w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3">ID</th>
                <th scope="col" class="px-4 py-3">Name</th>
                <th scope="col" class="px-4 py-3">E-Mail</th>
                <th scope="col" class="px-4 py-3">Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $customer->id }}</td>
                    <td class="px-4 py-2">{{ $customer->name }}</td>
                    <td class="px-4 py-2">{{ $customer->email }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.impersonate.start', $customer->id) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200">
                            Login als
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">
                        Keine Kunden gefunden.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Mobile Ansicht (bis sm) --}}
    <div class="sm:hidden space-y-4 p-4">
        @forelse($customers as $customer)
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900">{{ $customer->name }}</span>
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">ID: {{ $customer->id }}</span>
                    </div>
                    
                    <div class="text-sm text-gray-600">
                        <div class="flex items-center gap-1">
                            <span>📧</span>
                            <span class="truncate">{{ $customer->email }}</span>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <a href="{{ route('admin.impersonate.start', $customer->id) }}"
                           class="inline-flex items-center justify-center w-full px-3 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200">
                            👤 Login als {{ $customer->name }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8">
                Keine Kunden gefunden.
            </div>
        @endforelse
    </div>
</div>

    <div class="mt-4">{{ $customers->links() }}</div>

    {{-- Modal: Neuer Customer --}}
    <x-dialog-modal wire:model="showCreateModal">
        <x-slot name="title">Neuer Customer</x-slot>

        <x-slot name="content">
            <div class="space-y-3">
                <input type="text" wire:model="name" placeholder="Name"
                    class="block w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                <input type="email" wire:model="email" placeholder="E-Mail"
                    class="block w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                <input type="password" wire:model="password" placeholder="Passwort"
                    class="block w-full rounded-lg border-gray-300 focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error("email")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="createCustomer"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 focus:ring-4 focus:ring-pink-200">
                Speichern
            </button>
        </x-slot>
    </x-dialog-modal>
</div>