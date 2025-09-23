<section id="portfolio" class="py-20 bg-base-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Title -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-base-content">Portfolio</h2>
            <p class="mt-2 text-base-content/70">
                Eine Auswahl meiner Projekte – von individuellen Apps über Branding bis hin zu
                Produkten.
            </p>
        </div>

        <!-- Filter Buttons -->
        <div class="flex justify-center flex-wrap gap-3 mb-12">
            <button class="btn btn-primary btn-sm filter-btn" data-filter="all">Alle</button>
            <button class="btn btn-outline btn-sm filter-btn" data-filter="app">Apps</button>
            <button class="btn btn-outline btn-sm filter-btn"
                data-filter="product">Produkte</button>
            <button class="btn btn-outline btn-sm filter-btn"
                data-filter="branding">Branding</button>
            <button class="btn btn-outline btn-sm filter-btn" data-filter="books">Books</button>
        </div>

        <!-- Grid -->
        <div id="portfolio-grid" class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            <!-- Item -->
            <div
                class="portfolio-item app card bg-base-100 shadow-xl group relative overflow-hidden">
                <figure>
                    <img src="{{ asset("assets/img/portfolio/app-1.jpg") }}" alt="App 1"
                        class="h-56 w-full object-cover" />
                </figure>
                <div class="card-body">
                    <h4 class="card-title">App 1</h4>
                    <p>Laravel-basiertes Buchungssystem für einen lokalen Anbieter.</p>
                    <div class="flex gap-2 mt-2">
                        <span class="badge badge-primary">Laravel</span>
                        <span class="badge badge-secondary">Livewire</span>
                        <span class="badge badge-accent">Tailwind</span>
                    </div>
                </div>
                <!-- Hover Overlay -->
                <div
                    class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <a href="{{ asset("assets/img/portfolio/app-1.jpg") }}"
                        class="btn btn-primary glightbox">Ansehen</a>
                </div>
            </div>

            <!-- Item -->
            <div
                class="portfolio-item product card bg-base-100 shadow-xl group relative overflow-hidden">
                <figure>
                    <img src="{{ asset("assets/img/portfolio/product-1.jpg") }}" alt="Product 1"
                        class="h-56 w-full object-cover" />
                </figure>
                <div class="card-body">
                    <h4 class="card-title">Product 1</h4>
                    <p>E-Commerce Lösung mit automatisierter Zahlungsanbindung.</p>
                    <div class="flex gap-2 mt-2">
                        <span class="badge badge-primary">Shop</span>
                        <span class="badge badge-secondary">API</span>
                        <span class="badge badge-accent">MySQL</span>
                    </div>
                </div>
                <div
                    class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                    <a href="{{ asset("assets/img/portfolio/product-1.jpg") }}"
                        class="btn btn-primary glightbox">Ansehen</a>
                </div>
            </div>

            <!-- Weitere Items analog ... -->

        </div>
    </div>
</section>
