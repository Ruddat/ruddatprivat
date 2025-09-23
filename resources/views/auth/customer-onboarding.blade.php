@extends("frontend.layouts.app")

@section("title", "Willkommen")

@section("content")
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="w-100" style="max-width: 480px;">
        <div class="bg-white rounded shadow p-4">
            <h2 class="h5 mb-4 text-center fw-semibold text-gray-800">
                Willkommen, {{ auth('customer')->user()->name }} 🎉
            </h2>
            <p class="text-center text-gray-600 mb-4">
                Bitte vervollständige dein Profil, um loszulegen.
            </p>

            <form method="POST" action="{{ route('customer.onboarding.store') }}">
                @csrf

                {{-- Telefonnummer --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Telefonnummer</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone', auth('customer')->user()->phone) }}" required>
                    @error("phone") <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Adresse --}}
                <div class="mb-4">
                    <label for="address" class="form-label fw-semibold">Adresse</label>
                    <input type="text" class="form-control"
                           id="address" name="address" value="{{ old('address', auth('customer')->user()->address) }}">
                </div>

                <div class="d-grid">
                    <button type="submit"
                            class="btn text-white fw-semibold py-2 rounded"
                            style="font-size: 0.95rem; background: linear-gradient(90deg, #ec4899, #db2777);">
                        Fertig & Dashboard öffnen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
