{{-- resources/views/livewire/frontend/portfolio-grid.blade.php --}}
<div>
    @if($items->count() > 0)
<section id="portfolio" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Title -->
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900">Portfolio</h2>
            <p class="mt-2 text-gray-600">
                Von Projekten bis hin zu Modulen – hier findest du eine Auswahl meiner Arbeit.
            </p>
        </div>

{{-- Filter Buttons --}}
<div class="flex justify-center flex-wrap gap-2 mb-12">
    <button 
        wire:click="filterBy('all')"
        class="px-3 py-1.5 text-xs sm:text-sm rounded-full font-medium transition-all duration-300
               {{ $category === 'all' 
                    ? 'badge-all' 
                    : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 hover:text-gray-900' }}">
        Alle
    </button>

    @foreach($categories as $cat)
        @php $label = $labelMap[$cat] ?? ucfirst($cat); @endphp
        <button 
            wire:click="filterBy('{{ $cat }}')"
            class="px-3 py-1.5 text-xs sm:text-sm rounded-full font-medium transition-all duration-300
                   {{ $category === $cat
                        ? 'badge-'.$cat 
                        : 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 hover:text-gray-900' }}">
            {{ $label }}
        </button>
    @endforeach
</div>

        <!-- Grid -->
<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
  @foreach($items as $item)
    <div class="portfolio-card">
      <img src="{{ $item->cover_image ? Storage::url($item->cover_image) : 'https://via.placeholder.com/600x400' }}" 
           alt="{{ $item->title }}" 
           class="portfolio-image">

      <div class="portfolio-body">
        <h4 class="portfolio-title">{{ $item->title }}</h4>
        <p class="portfolio-summary">{{ $item->summary }}</p>

        <div class="flex gap-2 flex-wrap mt-3">
          @foreach($item->badges ?? [] as $badge)
            <span class="portfolio-badge">{{ $badge }}</span>
          @endforeach
        </div>

        <div class="mt-4">
          @if($item->type === 'module')
            <a href="{{ $item->cta_link ?? route('register', ['module' => $item->slug]) }}"
               class="portfolio-btn-primary w-full">
              Jetzt ausprobieren
            </a>
          @else
            <a href="{{ route('portfolio.show', $item->slug) }}"
               class="portfolio-btn-secondary w-full">
              Mehr erfahren
            </a>
          @endif
        </div>
      </div>
    </div>
  @endforeach
</div>
    </div>
</section>
@endif






</div>

