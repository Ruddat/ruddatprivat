@php
    $field = 'mt-2 block w-full rounded-md border border-slate-300 bg-white px-3.5 py-3 text-base text-navy-900 shadow-sm placeholder:text-slate-400 transition focus:border-brass-500 focus:outline-none focus:ring-2 focus:ring-brass-200 sm:text-sm';
    $label = 'block text-sm font-medium text-navy-900';
    $error = 'mt-2 text-sm text-red-700';
@endphp

<div>
    @if ($submitted)
        <div class="scroll-mt-28 py-6 text-center outline-none" role="status" aria-live="polite" tabindex="-1"
            x-data x-init="$el.scrollIntoView({ block: 'center', behavior: 'smooth' }); $el.focus({ preventScroll: true })">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brass-50 text-brass-600 ring-1 ring-brass-200">
                @include('site.partials.icon', ['name' => 'check', 'class' => 'h-7 w-7'])
            </span>
            <h2 class="mt-6 text-2xl font-semibold text-navy-900">Vielen Dank für Ihre Anfrage.</h2>
            <p class="mx-auto mt-3 max-w-md leading-7 text-anthracite-700">
                Ihre Anfrage ist eingegangen und wird persönlich bearbeitet. Sie erhalten eine Rückmeldung per E-Mail.
            </p>
            @if ($submittedReference)
                <p class="mt-5 text-sm text-slate-500">Ihre Vorgangsnummer: <span class="font-mono font-semibold text-navy-900">{{ $submittedReference }}</span></p>
            @endif
        </div>
    @else
        <form wire:submit="submit" novalidate class="space-y-6">
            <div>
                <h2 class="text-xl font-semibold text-navy-900">Ihre Anfrage</h2>
                <p class="mt-1 text-sm text-slate-500">Felder mit * sind Pflichtfelder.</p>
            </div>

            @error('form')
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $message }}</div>
            @enderror

            {{-- Honeypot: für Menschen unsichtbar --}}
            <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                <label for="inq-website">Webseite (bitte leer lassen)</label>
                <input id="inq-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="inq-name" class="{{ $label }}">Name *</label>
                    <input id="inq-name" type="text" wire:model="name" autocomplete="name" required
                        class="{{ $field }}" @error('name') aria-invalid="true" aria-describedby="inq-name-error" @enderror>
                    @error('name') <p id="inq-name-error" class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="inq-company" class="{{ $label }}">Unternehmen *</label>
                    <input id="inq-company" type="text" wire:model="company" autocomplete="organization" required
                        class="{{ $field }}" @error('company') aria-invalid="true" aria-describedby="inq-company-error" @enderror>
                    @error('company') <p id="inq-company-error" class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="inq-email" class="{{ $label }}">E-Mail-Adresse *</label>
                    <input id="inq-email" type="email" wire:model="email" autocomplete="email" inputmode="email" required
                        class="{{ $field }}" @error('email') aria-invalid="true" aria-describedby="inq-email-error" @enderror>
                    @error('email') <p id="inq-email-error" class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="inq-phone" class="{{ $label }}">Telefonnummer <span class="font-normal text-slate-500">(optional)</span></label>
                    <input id="inq-phone" type="tel" wire:model="phone" autocomplete="tel" inputmode="tel"
                        class="{{ $field }}" @error('phone') aria-invalid="true" aria-describedby="inq-phone-error" @enderror>
                    @error('phone') <p id="inq-phone-error" class="{{ $error }}">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="inq-type" class="{{ $label }}">Art der Anfrage *</label>
                <select id="inq-type" wire:model="type" required class="{{ $field }}"
                    @error('type') aria-invalid="true" aria-describedby="inq-type-error" @enderror>
                    <option value="">Bitte wählen …</option>
                    @foreach ($types as $value => $typeLabel)
                        <option value="{{ $value }}">{{ $typeLabel }}</option>
                    @endforeach
                </select>
                @error('type') <p id="inq-type-error" class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="inq-summary" class="{{ $label }}">Kurze Beschreibung der Anwendung *</label>
                <p id="inq-summary-hint" class="mt-1 text-sm text-slate-500">Wofür wird sie genutzt, auf welcher Technik basiert sie, wo wird sie betrieben?</p>
                <textarea id="inq-summary" wire:model="application_summary" rows="4" required maxlength="2000"
                    class="{{ $field }}" aria-describedby="inq-summary-hint @error('application_summary') inq-summary-error @enderror"
                    @error('application_summary') aria-invalid="true" @enderror></textarea>
                @error('application_summary') <p id="inq-summary-error" class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="inq-req" class="{{ $label }}">Technische Anforderungen <span class="font-normal text-slate-500">(optional)</span></label>
                <textarea id="inq-req" wire:model="requirements" rows="5" maxlength="5000" class="{{ $field }}"
                    @error('requirements') aria-invalid="true" aria-describedby="inq-req-error" @enderror></textarea>
                @error('requirements') <p id="inq-req-error" class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="flex items-start gap-3 text-sm leading-6 text-anthracite-700">
                    <input type="checkbox" wire:model="privacy" required
                        class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-navy-900 focus:ring-brass-300"
                        @error('privacy') aria-invalid="true" aria-describedby="inq-privacy-error" @enderror>
                    <span>
                        Ich habe die <a href="{{ route('datenschutz') }}" target="_blank" class="font-medium text-navy-900 underline decoration-brass-400 underline-offset-4">Datenschutzhinweise</a>
                        gelesen und bin einverstanden, dass meine Angaben zur Bearbeitung meiner Anfrage gespeichert werden. *
                    </span>
                </label>
                @error('privacy') <p id="inq-privacy-error" class="{{ $error }}">{{ $message }}</p> @enderror
            </div>

            <p class="text-sm text-slate-500">Bitte senden Sie keine Zugangsdaten oder Passwörter über dieses Formular.</p>

            <button type="submit" class="btn-primary w-full sm:w-auto" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Anfrage senden</span>
                <span wire:loading wire:target="submit">Wird gesendet …</span>
            </button>
        </form>
    @endif
</div>
