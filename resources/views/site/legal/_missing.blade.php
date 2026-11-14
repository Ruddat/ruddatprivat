{{-- Hinweis auf fehlende Pflichtangaben – nur außerhalb der Produktion sichtbar --}}
@unless (app()->isProduction())
    <span class="rounded bg-red-50 px-1.5 py-0.5 font-mono text-sm text-red-700 ring-1 ring-red-200">fehlt: {{ $env }} in .env setzen</span>
@endunless
