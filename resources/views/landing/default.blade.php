@extends("frontend.layouts.app")

@section("title", $page->title)
@section("meta_description", $page->meta_description)

@section("content")
    {{-- Hero --}}
    <section
        class="relative overflow-hidden bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white py-24">
        <!-- Hintergrund -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <div
                class="absolute top-10 left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-soft-light filter blur-xl animate-pulse">
            </div>
            <div
                class="absolute bottom-10 right-10 w-96 h-96 bg-pink-300 rounded-full mix-blend-soft-light filter blur-xl animate-pulse animation-delay-2000">
            </div>
        </div>

        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="max-w-3xl mx-auto">
                <h1
                    class="text-5xl md:text-6xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-pink-400 to-purple-300">
                    {{ $page->h1 }}
                </h1>

                @if ($page->meta_description)
                    <p class="mt-6 max-w-2xl mx-auto text-xl md:text-2xl opacity-90 font-light">
                        {{ $page->meta_description }}
                    </p>
                @endif

                <div class="mt-10">
                    <a href="/kontakt"
                        class="inline-block px-8 py-4 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-lg font-semibold rounded-xl shadow-lg transition">
                        Jetzt kostenlos beraten lassen
                    </a>
                </div>

                @if ($page->hero_image)
                    <div class="mt-12">
                        <img src="{{ asset("storage/" . $page->hero_image) }}"
                            alt="{{ $page->h1 }} – Beispielbild" loading="lazy"
                            class="mx-auto rounded-3xl shadow-2xl max-h-96 object-cover transform hover:scale-105 transition-transform duration-700">
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="container mx-auto px-4 py-16">
        <div
            class="prose prose-lg max-w-3xl mx-auto text-gray-800 prose-headings:font-bold prose-headings:bg-gradient-to-r prose-headings:from-purple-600 prose-headings:to-pink-600 prose-headings:bg-clip-text prose-headings:text-transparent prose-a:text-purple-600 hover:prose-a:text-pink-600 prose-strong:text-gray-900 prose-blockquote:border-l-purple-500 prose-blockquote:bg-purple-50">
            {!! $page->content !!}
        </div>
    </section>

    {{-- Features --}}
    @if ($page->features)
        <section class="py-20 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2
                        class="text-4xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Unsere Vorteile</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">Darum entscheiden sich Kunden für
                        RuddatTech</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($page->features as $feature)
                        <div
                            class="group p-8 bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
                            <div class="relative mb-6">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg transform rotate-3 group-hover:rotate-6 transition-transform duration-500">
                                </div>
                                <div
                                    class="relative bg-gradient-to-br from-purple-600 to-pink-600 w-16 h-16 rounded-lg flex items-center justify-center text-white text-2xl">
                                    <i class="fa-solid fa-check-circle"></i>
                                </div>
                            </div>
                            <p
                                class="font-semibold text-lg text-gray-800 group-hover:text-purple-700 transition-colors duration-300">
                                {{ $feature }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Kontaktblock --}}
    <section class="bg-gray-50 py-16">
        <div class="container mx-auto text-center">
            <h2 class="text-2xl font-bold mb-4">Direkt Kontakt aufnehmen</h2>
            <p class="mb-6 text-gray-600">
                Telefon: <a href="tel:+495171123456" class="text-purple-600 hover:underline">+49 5171
                    123456</a><br>
                E-Mail: <a href="mailto:info@ruddattech.de"
                    class="text-purple-600 hover:underline">info@ruddattech.de</a>
            </p>
            <a href="/kontakt"
                class="inline-block px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-lg shadow hover:shadow-lg transition">
                Kostenlose Beratung anfragen
            </a>
        </div>
    </section>

    {{-- FAQ --}}
    @if ($page->faq)
        <section class="container mx-auto px-4 py-20">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h2
                        class="text-4xl font-bold mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Häufige Fragen</h2>
                </div>
                <div class="space-y-6">
                    @foreach ($page->faq as $faq)
                        <div
                            class="group rounded-2xl bg-white p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <h3
                                class="text-xl font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-300 flex items-center">
                                <span class="mr-3 text-purple-500">
                                    <i class="fa-solid fa-question-circle"></i>
                                </span>
                                {{ $faq["q"] }}
                            </h3>
                            <div
                                class="mt-4 pl-9 text-gray-600 border-l-2 border-purple-200 group-hover:border-purple-400 transition-colors duration-300">
                                {{ $faq["a"] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Call-to-Action --}}
    <section
        class="relative py-24 bg-gradient-to-r from-indigo-700 to-purple-700 text-white overflow-hidden">
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold mb-6">Bereit, Ihr Projekt zu starten?</h2>
            <p class="mt-4 text-xl opacity-90 max-w-2xl mx-auto">Kontaktieren Sie RuddatTech für ein
                unverbindliches Gespräch.</p>
            <div class="mt-10">
                <a href="/kontakt"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-lg font-semibold rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 group">
                    <span>Jetzt Kontakt aufnehmen</span>
                    <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Sticky CTA Button --}}
    <a href="/kontakt"
        class="fixed bottom-6 left-6 flex items-center space-x-2 px-5 py-3 z-50 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-full shadow-lg hover:shadow-xl transition transform hover:scale-105">
        <span class="text-sm">🚀 Angebot anfordern</span>
    </a>
@endsection

@push("head")
    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "RuddatTech",
  "url": "{{ url()->current() }}",
  "image": "{{ $page->hero_image ? asset('storage/' . $page->hero_image) : asset('assets/img/favicon.png') }}",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Peine",
    "addressRegion": "Niedersachsen",
    "postalCode": "31224",
    "addressCountry": "DE"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+49-5171-123456",
    "contactType": "customer service",
    "availableLanguage": "German"
  }
}
</script>
@endpush
