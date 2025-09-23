<div>
    <div class="flex items-center justify-between mb-4 space-x-2">
        <input type="text" wire:model.debounce.500ms="search"
            class="flex-1 border rounded px-3 py-2" placeholder="Suche nach Kunden...">

        <button wire:click="$set('showCreateModal', true)"
            class="bg-pink-600 text-white px-4 py-2 rounded whitespace-nowrap">
            + Neuer Customer
        </button>
    </div>

    <table class="min-w-full bg-white border rounded shadow">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">E-Mail</th>
                <th class="px-4 py-2">Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $customer->id }}</td>
                    <td class="px-4 py-2">{{ $customer->name }}</td>
                    <td class="px-4 py-2">{{ $customer->email }}</td>
                    <td class="px-4 py-2 space-x-2">
<td class="px-4 py-2 space-x-2">
    <a href="{{ route('admin.impersonate.start', $customer->id) }}"
       class="bg-indigo-500 text-white px-3 py-1 rounded">
        Login als
    </a>
</td>
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

    <div class="mt-4">{{ $customers->links() }}</div>

    {{-- Modal: Neuer Customer --}}
    <x-dialog-modal wire:model="showCreateModal">
        <x-slot name="title">Neuer Customer</x-slot>

        <x-slot name="content">
            <div class="space-y-3">
                <input type="text" wire:model="name" placeholder="Name"
                    class="w-full border rounded px-3 py-2">
                <input type="email" wire:model="email" placeholder="E-Mail"
                    class="w-full border rounded px-3 py-2">
                <input type="password" wire:model="password" placeholder="Passwort"
                    class="w-full border rounded px-3 py-2">
                @error("email")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="createCustomer" class="bg-pink-600 text-white px-4 py-2 rounded">
                Speichern
            </button>
        </x-slot>
    </x-dialog-modal>
</div>
