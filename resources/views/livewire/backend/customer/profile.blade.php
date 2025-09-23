<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Linke Spalte: Profil bearbeiten --}}
    <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Profil bearbeiten</h2>

        @if (session()->has('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="updateProfile" class="space-y-4">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium">Name</label>
                <input type="text" id="name" wire:model="name"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium">E-Mail</label>
                <input type="email" id="email" wire:model="email"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <button type="submit"
                        class="px-4 py-2 bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
                    Speichern
                </button>
            </div>
        </form>
    </div>

    {{-- Rechte Spalte: Aktionen --}}
<div class="space-y-6">
    {{-- Passwort ändern --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-md font-semibold mb-4">Passwort ändern</h3>
        <form wire:submit.prevent="updatePassword" class="space-y-3">
            <div>
                <label for="password" class="block text-sm font-medium">Neues Passwort</label>
                <input type="password" id="password" wire:model="password"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium">Passwort bestätigen</label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
            </div>

            <button type="submit"
                    class="px-3 py-2 w-full bg-pink-600 text-white rounded-md hover:bg-pink-700 transition">
                Passwort ändern
            </button>
        </form>
    </div>

    {{-- Account löschen --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-md font-semibold mb-4 text-red-600">Account löschen</h3>

        @if (Auth::guard('customer')->user()->scheduled_for_deletion)
            <div class="p-3 mb-3 text-red-800 bg-red-100 rounded">
                Dein Account ist zur Löschung am 
                <strong>{{ Auth::guard('customer')->user()->deletion_date->format('d.m.Y H:i') }}</strong> vorgemerkt.
            </div>
            <button wire:click="cancelDelete"
                    class="px-3 py-2 w-full bg-gray-500 text-white rounded-md hover:bg-gray-600">
                Löschung abbrechen
            </button>
        @else
            <button wire:click="confirmDelete"
                    class="px-3 py-2 w-full bg-red-600 text-white rounded-md hover:bg-red-700">
                Account zur Löschung vormerken
            </button>
        @endif
    </div>

    {{-- Logout --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h3 class="text-md font-semibold mb-4">Abmelden</h3>
        <form method="POST" action="{{ route('customer.logout') }}">
            @csrf
            <button type="submit"
                    class="px-3 py-2 w-full bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                Abmelden
            </button>
        </form>
    </div>
</div>
</div>

{{-- Timer Script --}}
<script>
    window.addEventListener('start-delete-timer', () => {
        let countdown = @this.get('deleteCountdown');

        const interval = setInterval(() => {
            if (countdown <= 1) {
                clearInterval(interval);
                @this.call('deleteAccount');
            } else {
                countdown--;
                @this.set('deleteCountdown', countdown);
            }
        }, 1000);
    });
</script>
