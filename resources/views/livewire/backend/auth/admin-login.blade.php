{{-- resources/views/livewire/backend/auth/admin-login.blade.php --}}

@section("content")
    <div class="max-w-md mx-auto mt-20 bg-white p-6 rounded shadow">
        <form wire:submit.prevent="login">
            <h2 class="text-xl font-bold mb-4">Admin Login</h2>

            <div class="mb-3">
                <label class="block text-sm font-medium">E-Mail</label>
                <input type="email" wire:model="email" class="form-input w-full">
                @error("email")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Passwort</label>
                <input type="password" wire:model="password" class="form-input w-full">
                @error("password")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model="remember">
                    <span class="ml-2 text-sm">Eingeloggt bleiben</span>
                </label>
            </div>

            <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded w-full">
                Einloggen
            </button>
        </form>
    </div>
@endsection
