@props(["id" => null, "maxWidth" => "2xl"])

<div x-data="{ show: @entangle($attributes->wire("model")) }" x-show="show" class="fixed inset-0 flex items-center justify-center z-50"
    style="display: none;">
    <div class="fixed inset-0 bg-black bg-opacity-50"></div>

    <div class="bg-white rounded-lg shadow-lg w-full max-w-{{ $maxWidth }} p-6 z-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">{{ $title }}</h2>
            <button @click="show = false">&times;</button>
        </div>

        <div class="mb-4">
            {{ $content }}
        </div>

        <div class="flex justify-end space-x-2">
            {{ $footer }}
        </div>
    </div>
</div>
