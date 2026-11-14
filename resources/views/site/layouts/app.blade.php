{{-- Layout der öffentlichen RuddatTech-Webseite --}}
@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'RuddatTech – Softwarewartung & Managed Application Services';
    $pageDescription = trim($__env->yieldContent('meta_description')) ?: 'RuddatTech übernimmt Wartung, Stabilisierung und langfristige technische Betreuung individueller Geschäftsanwendungen.';
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $ogImage = trim($__env->yieldContent('og_image')) ?: asset('brand/og-image.png');
    $ogImageAlt = trim($__env->yieldContent('og_image_alt')) ?: 'RuddatTech – Komplexe Software. Zuverlässig betreut.';
    $robots = trim($__env->yieldContent('robots')) ?: 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
@endphp
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="alternate" hreflang="de" href="{{ $canonical }}">
    <meta name="author" content="RuddatTech – Ingo Ruddat">
    <meta name="theme-color" content="#0B1426">

    {{-- Open Graph / Social Preview --}}
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="RuddatTech">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    @if ($ogImage === asset('brand/og-image.png'))
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:type" content="image/png">
    @endif
    <meta property="og:image:alt" content="{{ $ogImageAlt }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $ogImageAlt }}">

    {{-- Icons --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    {{-- Maschinenlesbare Kurzbeschreibung für KI-Assistenten --}}
    <link rel="alternate" type="text/markdown" title="RuddatTech für KI-Assistenten" href="{{ route('llms') }}">

    @vite(['resources/css/site.css', 'resources/js/site.js'])

    @stack('head')

    {{-- Organisation und Webseite – auf allen Seiten, von Einzelseiten per @id referenziert --}}
    <script type="application/ld+json">
        {!! json_encode(\App\Support\Site\StructuredData::siteGraph(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
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
