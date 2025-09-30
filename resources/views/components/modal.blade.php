{{-- resources/views/components/modal.blade.php --}}
<div x-data="{ open: @entangle($attributes->wire('model')) }" x-show="open"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div @click.away="open = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
        {{ $slot }}
    </div>
</div>