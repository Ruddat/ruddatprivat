{{-- resources\views\components\form\input.blade.php --}}

@props([
    'label' => null,
    'id' => null,      // für <label for="...">
    'name' => null,    // Validierungsname, z.B. "street" oder "form.street"
    'type' => 'text',
])

@php
    $name = $name ?? $id; // fallback
    $errorText = $name ? $errors->first($name) : null;

    $base = 'w-full px-4 py-2 bg-white border rounded-lg shadow-sm sm:text-sm';
    $ok   = 'border-gray-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500';
    $bad  = 'border-red-500 focus:ring-2 focus:ring-red-500 focus:border-red-500';
@endphp

<div class="mb-4">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => $base.' '.($errorText ? $bad : $ok)]) }}
    />

    @if ($errorText)
        <p class="mt-1 text-sm text-red-600">{{ $errorText }}</p>
    @endif
</div>
