{{-- Layout der öffentlichen RuddatTech-Webseite --}}
@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'RuddatTech – Softwarewartung & Managed Application Services';
    $pageDescription = trim($__env->yieldContent('meta_description')) ?: 'RuddatTech übernimmt Wartung, Stabilisierung und langfristige technische Betreuung individueller Geschäftsanwendungen.';
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
@endphp
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="#0B1426">

    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="RuddatTech">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta name="twitter:card" content="summary">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    @vite(['resources/css/site.css', 'resources/js/site.js'])

    @stack('head')
    @stack('structured_data')
</head>

<body class="flex min-h-screen flex-col">
    <a href="#inhalt"
        class="sr-only z-[60] rounded-md bg-brass-400 px-4 py-2 text-sm font-semibold text-navy-950 focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
        Zum Inhalt springen
    </a>

    @include('site.partials.header')

    <main id="inhalt" class="flex-1">
        @yield('content')
    </main>

    @include('site.partials.footer')

    @include('site.partials.whatsapp-button')

    {{-- Kontakt-CTA auf Mobilgeräten immer erreichbar --}}
    @unless (request()->routeIs('kontakt'))
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-white/10 bg-navy-950/95 p-3 backdrop-blur lg:hidden"
            style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
            <a href="{{ route('kontakt') }}" class="btn-primary w-full">
                Technische Betreuung anfragen
            </a>
        </div>
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
    @endunless
</body>

</html>
