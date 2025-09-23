{{-- resources\views\components\form\select.blade.php --}}
@props([
    "label" => null,
    "id" => null,
    "error" => null,
])

<div class="mb-4">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <select id="{{ $id }}"
        {{ $attributes->merge([
            "class" => 'w-full px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm
                                                                    focus:ring-2 focus:ring-pink-500 focus:border-pink-500 sm:text-sm',
        ]) }}>
        {{ $slot }}
    </select>

    @if ($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
