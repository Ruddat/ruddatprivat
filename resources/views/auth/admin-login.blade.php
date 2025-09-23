{{-- resources\views\auth\admin-login.blade.php --}}

@extends("frontend.layouts.app")

@section("title", "Admin Login")

@section("content")
    <div class="max-w-md mx-auto mt-20 bg-white p-6 rounded shadow">
        <form method="POST" action="{{ route("admin.login.submit") }}">
            @csrf
            <h2 class="text-xl font-bold mb-4">Admin Login</h2>

            <div class="mb-3">
                <label class="block text-sm font-medium">E-Mail</label>
                <input type="email" name="email" class="form-input w-full" required autofocus>
                @error("email")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium">Passwort</label>
                <input type="password" name="password" class="form-input w-full" required>
                @error("password")
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember">
                    <span class="ml-2 text-sm">Eingeloggt bleiben</span>
                </label>
            </div>

            <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded w-full">
                Einloggen
            </button>
        </form>
    </div>
@endsection
