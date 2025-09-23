{{-- resources\views\components\form\date.blade.php --}}


@props([
    "label" => null,
    "id" => null,
    "error" => null,
])

<div class="mb-4 relative">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <!-- Input -->
    <input type="date" id="{{ $id }}"
        {{ $attributes->merge([
            "class" => 'w-full pl-3 pr-10 py-2 bg-white border border-gray-300 rounded-lg shadow-sm
                                                                        focus:ring-2 focus:ring-pink-500 focus:border-pink-500 sm:text-sm',
        ]) }} />

    <!-- Klickbares Icon -->
    <button type="button"
        onclick="document.getElementById('{{ $id }}').showPicker?.() || document.getElementById('{{ $id }}').focus();"
        class="absolute right-3 top-[34px] md:top-[30px] text-gray-500 hover:text-pink-600"
        tabindex="-1">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2
                     2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </button>

    @if ($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
