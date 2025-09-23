{{-- resources\views\components\form\textarea.blade.php --}}

@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'rows' => 3,
])

@php
    $name = $name ?? $id;
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

    <textarea
        id="{{ $id ?? $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => $base.' '.($errorText ? $bad : $ok)]) }}
    ></textarea>

    @if ($errorText)
        <p class="mt-1 text-sm text-red-600">{{ $errorText }}</p>
    @endif
</div>
