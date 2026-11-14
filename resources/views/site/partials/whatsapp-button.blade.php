{{--
    Öffentlicher WhatsApp-Kontaktbutton: öffnet lediglich einen normalen WhatsApp-Chat (wa.me).
    Nachrichten auf diesem Weg werden NICHT im Anfragesystem erfasst und lösen keine
    Benachrichtigung aus. Es wird kein WhatsApp-Skript geladen.
--}}
@php
    $waNumber = preg_replace('/\D/', '', (string) config('inquiry-alerts.whatsapp_button_number'));
@endphp

@if (strlen($waNumber) >= 8)
    <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Guten Tag, ich habe eine Frage zur technischen Betreuung meiner Anwendung.') }}"
        target="_blank" rel="noopener nofollow"
        class="fixed bottom-24 right-4 z-40 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#1F7A4D] text-white shadow-lg ring-1 ring-black/10 transition hover:-translate-y-0.5 hover:bg-[#19663F] lg:bottom-6 lg:right-6"
        title="Direkt per WhatsApp schreiben (öffnet WhatsApp, keine Erfassung im Anfragesystem)">
        <span class="sr-only">Direkt per WhatsApp schreiben – öffnet WhatsApp in einem neuen Fenster</span>
        <svg viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor" aria-hidden="true">
            <path d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.9 2 7L3 29l6.4-2a13.2 13.2 0 0 0 6.6 1.7c7.2 0 13-5.7 13-12.8C29 8.7 23.2 3 16 3Zm0 23.4c-2.1 0-4.1-.6-5.9-1.6l-.4-.3-3.8 1.2 1.2-3.7-.3-.4a10.4 10.4 0 0 1-1.7-5.8C5.1 10 10 5.3 16 5.3S26.9 10 26.9 15.8 22 26.4 16 26.4Zm6-7.8c-.3-.2-1.9-.9-2.2-1-.3-.1-.5-.2-.7.2l-1 1.2c-.2.2-.4.2-.7.1-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.2-.2-.3 0-.5.1-.7l.5-.6.3-.5c.1-.2 0-.4 0-.6l-1-2.4c-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.7s1.2 3.2 1.4 3.4c.2.2 2.4 3.6 5.8 5 .8.3 1.4.5 1.9.7.8.2 1.5.2 2.1.1.6-.1 1.9-.8 2.2-1.5.3-.7.3-1.3.2-1.5-.1-.1-.3-.2-.6-.3Z" />
        </svg>
    </a>
@endif
