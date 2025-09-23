{{-- resources/views/frontend/home/sections/hero.blade.php --}}
<section id="hero" class="relative text-white">
    <!-- Hintergrundbild -->
    <img src="{{ asset("assets/images/hero_image.jpeg") }}" alt="Hero Background"
        class="absolute inset-0 w-full h-full object-cover opacity-70">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Inhalt -->
    <div class="relative z-10 max-w-4xl mx-auto px-6 py-32 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-4">
            Experte für Webentwicklung
        </h2>
        <p class="text-lg md:text-xl mb-8 text-base-200">
            Entfalten Sie Ihr digitales Potenzial
        </p>
        <a href="{{ url("/#about") }}" class="btn btn-primary btn-lg">
            Mehr erfahren
        </a>
    </div>
</section>
