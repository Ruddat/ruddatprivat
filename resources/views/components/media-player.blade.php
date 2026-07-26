@props(['file'])

@php
    $mimeType = $file->mime_type ?? 'application/octet-stream';
    $isVideo = str_starts_with($mimeType, 'video/');
    $isAudio = str_starts_with($mimeType, 'audio/');
@endphp

@if ($isVideo)
    <div class="overflow-hidden rounded-xl bg-black shadow-sm" wire:ignore>
        <video data-media-player controls playsinline preload="metadata" class="w-full">
            <source src="{{ route('drive.stream', $file) }}" type="{{ $mimeType }}">
            Dein Browser kann dieses Videoformat nicht abspielen.
        </video>
    </div>
@elseif ($isAudio)
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-3" wire:ignore>
        <audio data-media-player controls preload="metadata" class="w-full">
            <source src="{{ route('drive.stream', $file) }}" type="{{ $mimeType }}">
            Dein Browser kann dieses Audioformat nicht abspielen.
        </audio>
    </div>
@endif
