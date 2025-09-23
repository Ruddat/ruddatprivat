@extends("frontend.layouts.app")

@section("title", "Registrieren")

@section("content")
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="w-100" style="max-width: 420px;">
        <div class="bg-white rounded shadow p-4">
            <h2 class="h5 mb-4 text-center fw-semibold text-gray-800">Jetzt Registrieren</h2>

            <form method="POST" action="{{ route('customer.register.submit') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- E-Mail --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">E-Mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Passwort --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Passwort</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Passwort bestätigen --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Passwort bestätigen</label>
                    <input type="password" class="form-control"
                           id="password_confirmation" name="password_confirmation" required>
                </div>

                {{-- Submit --}}
                <div class="d-grid">
                    <button type="submit"
                            class="btn text-white fw-semibold py-2 rounded"
                            style="font-size: 0.95rem; background: linear-gradient(90deg, #ec4899, #db2777);">
                        Registrieren
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center text-sm text-gray-600">
                Bereits registriert? 
                <a href="{{ route('customer.login') }}" class="text-pink-600 fw-semibold hover:underline">
                    Jetzt einloggen
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
