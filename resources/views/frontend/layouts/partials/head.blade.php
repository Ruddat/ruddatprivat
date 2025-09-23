{{-- resources/views/frontend/layouts/partials/head.blade.php --}}

<!-- Favicons -->
<link href="{{ asset("assets/img/favicon.png") }}" rel="icon">
<link href="{{ asset("assets/img/apple-touch-icon.png") }}" rel="apple-touch-icon">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&family=Poppins:wght@400;600;700&family=Raleway:wght@400;600;700&display=swap"
    rel="stylesheet">

<!-- Vendor CSS (optional, nur wenn gebraucht) -->
{{-- Wenn du komplett Tailwind/DaisyUI nutzt → Bootstrap & main.css besser weglassen --}}
<link href="{{ asset("assets/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
<link href="{{ asset("assets/vendor/bootstrap-icons/bootstrap-icons.css") }}" rel="stylesheet">
<link href="{{ asset("assets/vendor/aos/aos.css") }}" rel="stylesheet">
<link href="{{ asset("assets/vendor/glightbox/css/glightbox.min.css") }}" rel="stylesheet">
<link href="{{ asset("assets/vendor/swiper/swiper-bundle.min.css") }}" rel="stylesheet">

<!-- Vite (Tailwind + DaisyUI Build) -->
@vite(["resources/css/app.css", "resources/js/app.js"])

<!-- Custom CSS (falls du eigenes ergänzen willst) -->
<!-- Main CSS File -->
<link href="{{ asset("assets/css/main.css") }}" rel="stylesheet">
<link href="{{ asset("assets/css/ruddat.css") }}" rel="stylesheet">
