<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $share->name }} · Private Dateifreigabe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-white">
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <section
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-600 via-fuchsia-600 to-slate-950 p-8 shadow-2xl">
            <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-cyan-400/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p
                        class="mb-3 inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-white/80 ring-1 ring-white/20">
                        Private Cloud-Freigabe
                    </p>
                    <h1 class="text-4xl font-black tracking-tight md:text-5xl">{{ $share->name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm text-white/75">
                        Geschützter Austauschbereich für Bilder, Musik, PDFs und Videos. Dateien sind nicht öffentlich
                        gelistet.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15 backdrop-blur">
                        <div class="text-white/60">Dateien</div>
                        <div class="text-2xl font-bold">{{ $files->count() }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15 backdrop-blur">
                        <div class="text-white/60">Upload</div>
                        <div class="text-2xl font-bold">{{ $share->can_upload ? 'An' : 'Aus' }}</div>
                    </div>
                </div>

<div class="mb-6 text-sm text-slate-400">

    <a href="{{ route('drive.share.show',$share->token) }}">
        Hauptordner
    </a>

    @if($folder)
        / {{ $folder->name }}
    @endif

</div>

            </div>
        </section>

        @if (session('success'))
            <div
                class="mt-6 rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if ($share->can_upload)
            <section class="mt-6 rounded-3xl border border-white/10 bg-white/[0.06] p-6 shadow-xl backdrop-blur">
                <form method="post" action="{{ route('drive.share.upload', $share->token) }}"
                    enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                    @csrf
<input type="hidden" name="folder_id" value="{{ $folder?->id }}">

                    <div>
                        <h2 class="text-xl font-bold">Datei hochladen</h2>
                        <p class="mt-1 text-sm text-slate-400">Bilder, MP3s, PDFs und Videos direkt in diese Freigabe
                            laden.</p>
                        <input type="file" name="file" required
                            class="mt-4 block w-full cursor-pointer rounded-2xl border border-white/10 bg-slate-900/80 text-sm text-slate-200 file:mr-4 file:border-0 file:bg-indigo-500 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400">
                        @error('file')
                            <div class="mt-3 text-sm text-red-300">{{ $message }}</div>
                        @enderror
                    </div>
                    <button
                        class="rounded-2xl bg-white px-6 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-indigo-950/40 transition hover:-translate-y-0.5 hover:bg-indigo-100">
                        Hochladen
                    </button>
                </form>
            </section>
        @endif



        <section class="mt-8">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black">Dateien</h2>
                    <p class="text-sm text-slate-400">Direkt ansehen, abspielen oder herunterladen.</p>
                </div>
            </div>

            @if ($files->isEmpty())
                <div
                    class="rounded-3xl border border-dashed border-white/15 bg-white/[0.04] p-10 text-center text-slate-400">
                    Noch keine Dateien vorhanden.
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($files as $file)
                        @php
                            $mime = (string) ($file->mime_type ?? 'application/octet-stream');
                            $streamUrl = route('drive.share.stream', [$share->token, $file]);
                            $downloadUrl = route('drive.share.download', [$share->token, $file]);
                            $isImage = str_starts_with($mime, 'image/');
                            $isAudio = str_starts_with($mime, 'audio/');
                            $isVideo = str_starts_with($mime, 'video/');
                            $isPdf = $mime === 'application/pdf';
                        @endphp

                        <article
                            class="group overflow-hidden rounded-3xl border border-white/10 bg-white/[0.06] shadow-xl shadow-black/20 backdrop-blur transition hover:-translate-y-1 hover:border-indigo-300/40 hover:bg-white/[0.09]">
                            <div class="relative aspect-video bg-slate-900">
                                @if ($isImage)
                                    <a href="{{ $streamUrl }}" target="_blank" class="block h-full w-full">
                                        <img src="{{ $streamUrl }}" alt="{{ $file->original_name }}"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    </a>
                                @elseif ($isVideo)
                                    <video controls preload="metadata" class="h-full w-full bg-black object-contain">
                                        <source src="{{ $streamUrl }}" type="{{ $mime }}">
                                    </video>
                                @elseif ($isAudio)
                                    <div
                                        class="flex h-full flex-col items-center justify-center bg-gradient-to-br from-indigo-500/30 via-fuchsia-500/20 to-slate-900 p-6 text-center">
                                        <div
                                            class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/10 text-4xl ring-1 ring-white/20">
                                            ♪</div>
                                        <audio controls preload="metadata" class="w-full">
                                            <source src="{{ $streamUrl }}" type="{{ $mime }}">
                                        </audio>
                                    </div>
                                @elseif ($isPdf)
                                    <iframe src="{{ $streamUrl }}" class="h-full w-full bg-white"
                                        loading="lazy"></iframe>
                                @else
                                    <div
                                        class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950">
                                        <div class="text-center">
                                            <div
                                                class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-3xl ring-1 ring-white/15">
                                                ☁</div>
                                            <div class="text-sm text-slate-400">Keine Vorschau</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="min-h-[3rem]">
                                    <h3 class="line-clamp-2 text-base font-bold text-white">{{ $file->original_name }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-400">{{ $mime }} ·
                                        {{ $file->human_size }}</p>
                                </div>

                                <div class="mt-5 flex flex-wrap gap-2">
                                    @if ($isImage || $isPdf)
                                        <a href="{{ $streamUrl }}" target="_blank"
                                            class="rounded-xl bg-indigo-500 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-400">
                                            Vorschau
                                        </a>
                                    @elseif ($isVideo || $isAudio)
                                        <a href="{{ $streamUrl }}" target="_blank"
                                            class="rounded-xl bg-indigo-500 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-400">
                                            Direkt öffnen
                                        </a>
                                    @endif

                                    @if ($share->can_download)
                                        <a href="{{ $downloadUrl }}"
                                            class="rounded-xl bg-white/10 px-3 py-2 text-xs font-bold text-white ring-1 ring-white/10 hover:bg-white/20">
                                            Download
                                        </a>
                                    @endif

        @if (
            $share->can_delete &&
                filled($file->public_upload_key) &&
                isset($publicUploadKey) &&
                hash_equals((string) $file->public_upload_key, (string) $publicUploadKey))
            <form method="post" action="{{ route('drive.share.delete', [$share->token, $file]) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl bg-red-500 px-3 py-2 text-xs font-bold text-white">
                    Entfernen
                </button>
            </form>
        @endif


                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</body>

</html>
