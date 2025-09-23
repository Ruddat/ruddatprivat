{{-- resources\views\frontend\layouts\app.blade.php --}}
<!DOCTYPE html>
<html lang="de" data-theme="ruddattech">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield("title", "RuddatTech – Digitale Lösungen")</title>
    <meta name="description" content="@yield("meta_description", "RuddatTech entwickelt moderne Weblösungen – Webentwicklung, IT-Service, SEO und mehr.")">
    <meta name="keywords" content="@yield("meta_keywords", "Webentwicklung, IT-Service, SEO, Digitalisierung, Peine, Hannover")">

    <link rel="canonical" href="{{ url()->current() }}">

    {{-- OpenGraph --}}
    <meta property="og:title" content="@yield("title", "RuddatTech")">
    <meta property="og:description" content="@yield("meta_description", "Digitale Lösungen für KMU")">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Poppins:wght@400;600;700&family=Raleway:wght@400;600;700&display=swap"
        rel="stylesheet">

    {{-- Bootstrap + Icons (für Vendor Themes) --}}
    <link href="{{ asset("assets/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/bootstrap-icons/bootstrap-icons.css") }}" rel="stylesheet">

    {{-- Tailwind/DaisyUI Build --}}
    @vite(["resources/css/app.css", "resources/js/app.js"])

    {{-- Optional Vendor CSS (nur wenn genutzt) --}}
    <link href="{{ asset("assets/vendor/aos/aos.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/glightbox/css/glightbox.min.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/vendor/swiper/swiper-bundle.min.css") }}" rel="stylesheet">

    {{-- Eigene Styles --}}
    <link href="{{ asset("assets/css/main.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/css/ruddat.css") }}" rel="stylesheet">

    @cookieconsentscripts
</head>

<body class="bg-white text-gray-900 antialiased">

    {{-- Header --}}
    @include("frontend.layouts.partials.header")

    {{-- Main --}}
    <main class="min-h-screen">
        @yield("content")
    </main>

    {{-- Scroll Top --}}
    <a href="#" id="scroll-top"
        class="fixed bottom-6 right-6 flex items-center justify-center w-12 h-12 rounded-full bg-pink-600 text-white shadow-lg hover:bg-pink-700 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
        </svg>
    </a>

    {{-- Preloader --}}
    <div id="preloader" class="fixed inset-0 flex items-center justify-center bg-white z-50 hidden">
        <div
            class="w-12 h-12 border-4 border-pink-600 border-t-transparent rounded-full animate-spin">
        </div>
    </div>

    {{-- Footer --}}
    @include("frontend.layouts.partials.footer")

    {{-- Scripts --}}
    <script src="{{ asset("assets/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
    <script src="{{ asset("assets/vendor/aos/aos.js") }}"></script>
    <script src="{{ asset("assets/vendor/glightbox/js/glightbox.min.js") }}"></script>
    <script src="{{ asset("assets/vendor/swiper/swiper-bundle.min.js") }}"></script>
    <script src="{{ asset("assets/js/main.js") }}"></script>

    @include("frontend.layouts.partials.footer-script")
    @cookieconsentview
    @livewireScripts

    {{-- GLightbox Init --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof GLightbox !== 'undefined') {
                GLightbox({
                    selector: '.glightbox'
                });
            }
        });
    </script>
</body>

</html>
