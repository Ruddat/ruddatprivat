{{-- resources/views/auth/customer-login.blade.php --}}

@extends("frontend.layouts.app")

@section("title", "Login")

@section("content")
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="w-100" style="max-width: 420px;">
        <div class="bg-white rounded shadow p-4">
            <h2 class="h5 mb-4 text-center fw-semibold text-gray-800">Login</h2>

            <form method="POST" action="{{ route('customer.login.submit') }}">
                @csrf

                {{-- E-Mail --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-Mail</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required autofocus>
                    @error("email")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Passwort --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Passwort</label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>
                    @error("password")
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Eingeloggt bleiben --}}
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label for="remember" class="form-check-label text-sm">Eingeloggt bleiben</label>
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <button type="submit"
                            class="btn text-white fw-semibold py-2 rounded"
                            style="font-size: 0.95rem; background: linear-gradient(90deg, #ec4899, #db2777);">
                        Einloggen
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center text-sm text-gray-600">
                Noch kein Konto?
                <a href="{{ route('customer.register') }}" class="text-pink-600 fw-semibold hover:underline">
                    Jetzt registrieren
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
