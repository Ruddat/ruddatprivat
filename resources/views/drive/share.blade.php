<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $share->name }} · Private Dateifreigabe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])



</head>

<body class="min-h-screen overflow-x-hidden bg-[#050816] text-white antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-indigo-600/20 blur-[120px]"></div>
        <div class="absolute -right-40 top-40 h-[30rem] w-[30rem] rounded-full bg-fuchsia-600/15 blur-[120px]"></div>
        <div class="absolute bottom-0 left-1/3 h-[24rem] w-[24rem] rounded-full bg-cyan-500/10 blur-[110px]"></div>
        <div
            class="absolute inset-0 opacity-[0.035]"
            style="background-image:
                linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px);
                background-size: 48px 48px;">
        </div>
    </div>

    <main class="relative mx-auto max-w-[1500px] px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
<section
    class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.055] shadow-[0_35px_120px_rgba(0,0,0,0.45)] backdrop-blur-2xl">

    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 via-fuchsia-500/10 to-transparent"></div>

    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-indigo-400/20 blur-3xl"></div>
    <div class="absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-fuchsia-500/15 blur-3xl"></div>

    <div class="relative p-6 sm:p-8 lg:p-10">
        <div class="flex flex-col gap-8 xl:flex-row xl:items-end xl:justify-between">

            <div class="max-w-3xl">
                <div class="mb-5 flex flex-wrap items-center gap-3">
                    <span
                        class="inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-300 shadow-[0_0_15px_rgba(110,231,183,.9)]"></span>
                        Private Freigabe
                    </span>

                    <span
                        class="inline-flex items-center rounded-full border border-white/10 bg-white/[0.06] px-3 py-1.5 text-xs font-medium text-slate-300">
                        Geschützt und nicht öffentlich gelistet
                    </span>
                </div>

                <h1
                    class="max-w-3xl text-4xl font-black leading-tight tracking-[-0.04em] text-white sm:text-5xl lg:text-6xl">
                    {{ $share->name }}
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">
                    Bilder, Videos, Musik, PDFs und große Dateien sicher teilen, ansehen und herunterladen.
                </p>

                <nav class="mt-6 flex flex-wrap items-center gap-2 text-sm">
                    <a
                        href="{{ route('drive.share.show', $share->token) }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.06] px-3 py-2 font-semibold text-slate-200 transition hover:border-indigo-300/30 hover:bg-white/[0.1] hover:text-white">
                        <span>⌂</span>
                        Hauptordner
                    </a>

                    @if ($folder)
                        <span class="text-slate-600">/</span>

                        <span
                            class="rounded-xl border border-indigo-300/20 bg-indigo-400/10 px-3 py-2 font-semibold text-indigo-200">
                            {{ $folder->name }}
                        </span>
                    @endif
                </nav>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:min-w-[360px]">
                <div
                    class="rounded-2xl border border-white/10 bg-black/20 p-5 shadow-inner shadow-black/20 backdrop-blur">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Dateien
                        </span>

                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-400/10 text-lg text-indigo-200 ring-1 ring-indigo-300/15">
                            ◫
                        </span>
                    </div>

                    <div class="mt-4 text-3xl font-black text-white">
                        {{ $files->count() }}
                    </div>
                </div>

                <div
                    class="rounded-2xl border border-white/10 bg-black/20 p-5 shadow-inner shadow-black/20 backdrop-blur">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Upload
                        </span>

                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl {{ $share->can_upload ? 'bg-emerald-400/10 text-emerald-200 ring-emerald-300/15' : 'bg-slate-400/10 text-slate-400 ring-white/10' }} ring-1">
                            ↑
                        </span>
                    </div>

                    <div class="mt-4 text-3xl font-black {{ $share->can_upload ? 'text-emerald-300' : 'text-slate-400' }}">
                        {{ $share->can_upload ? 'Aktiv' : 'Aus' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if (session('success'))
    <div
        class="mt-6 flex items-start gap-3 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100 shadow-lg shadow-emerald-950/20 backdrop-blur">
        <span
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-300/15 font-black text-emerald-200">
            ✓
        </span>

        <div class="pt-1.5">
            {{ session('success') }}
        </div>
    </div>
@endif
        {{--
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
--}}

        {{--

@if ($share->can_upload)
    <section class="mt-6 rounded-3xl border border-white/10 bg-white/[0.06] p-6 shadow-xl backdrop-blur">
        <h2 class="text-xl font-bold">Große Datei hochladen</h2>
        <p class="mt-1 text-sm text-slate-400">
            Große FPV-Videos werden in 50-MB-Blöcken hochgeladen.
        </p>

        <input id="chunk-file" type="file"
               class="mt-4 block w-full cursor-pointer rounded-2xl border border-white/10 bg-slate-900/80 text-sm text-slate-200 file:mr-4 file:border-0 file:bg-indigo-500 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-400">

        <button id="chunk-upload-button"
                type="button"
                class="mt-4 rounded-2xl bg-white px-6 py-3 text-sm font-bold text-slate-950 shadow-lg hover:bg-indigo-100">
            Chunk Upload starten
        </button>

        <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/10">
            <div id="chunk-progress-bar" class="h-full w-0 bg-indigo-400 transition-all"></div>
        </div>

        <div id="chunk-status" class="mt-3 text-sm text-slate-400"></div>
    </section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('chunk-file');
        const button = document.getElementById('chunk-upload-button');
        const bar = document.getElementById('chunk-progress-bar');
        const status = document.getElementById('chunk-status');

        const chunkSize = 20 * 1024 * 1024; // 20 MB
        const maxRetries = 3;

        async function uploadChunk(formData, index) {
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    const response = await fetch('{{ route('drive.share.chunk-upload', $share->token) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        },
                    });

                    if (response.ok) {
                        return await response.json();
                    }

                    let errorText = await response.text();

                    if (attempt === maxRetries) {
                        throw new Error('Chunk ' + (index + 1) + ' fehlgeschlagen: ' + errorText.substring(0, 300));
                    }
                } catch (error) {
                    if (attempt === maxRetries) {
                        throw error;
                    }

                    status.innerText = 'Chunk ' + (index + 1) + ' fehlgeschlagen, neuer Versuch ' + (attempt + 1) + ' von ' + maxRetries + '...';
                    await new Promise(resolve => setTimeout(resolve, 1000 * attempt));
                }
            }
        }

        button.addEventListener('click', async () => {
            const file = input.files[0];

            if (!file) {
                status.innerText = 'Bitte Datei auswählen.';
                return;
            }

            button.disabled = true;
            input.disabled = true;
            button.innerText = 'Upload läuft...';
            bar.style.width = '0%';
            status.innerText = 'Upload wird vorbereitet...';

            const totalChunks = Math.ceil(file.size / chunkSize);
            const uploadId = crypto.randomUUID ? crypto.randomUUID() : String(Date.now()) + '-' + Math.random().toString(36).substring(2);

            try {
                for (let index = 0; index < totalChunks; index++) {
                    const start = index * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const chunk = file.slice(start, end);

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('upload_id', uploadId);
                    formData.append('chunk', chunk, file.name + '.part' + index);
                    formData.append('chunk_index', index);
                    formData.append('total_chunks', totalChunks);
                    formData.append('file_name', file.name);
                    formData.append('file_size', file.size);
                    formData.append('mime_type', file.type || 'application/octet-stream');
                    formData.append('folder_id', '{{ $folder?->id }}');

                    await uploadChunk(formData, index);

                    const percent = Math.round(((index + 1) / totalChunks) * 100);
                    bar.style.width = percent + '%';
                    status.innerText = `Upload ${percent}% · Chunk ${index + 1} von ${totalChunks}`;
                }

                status.innerText = 'Upload fertig. Seite wird aktualisiert...';
                window.location.reload();
            } catch (error) {
                console.error(error);
                status.innerText = error.message || 'Upload fehlgeschlagen.';
                button.disabled = false;
                input.disabled = false;
                button.innerText = 'Chunk Upload erneut starten';
            }
        });
    });
</script>
@endif

--}}


        @if ($share->can_upload)
<section
    class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.045] shadow-[0_25px_80px_rgba(0,0,0,0.3)] backdrop-blur-xl">

    <div class="border-b border-white/10 px-6 py-5 sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 text-xl font-black shadow-lg shadow-indigo-950/40">
                    ↑
                </span>

                <div>
                    <h2 class="text-xl font-black tracking-tight text-white">
                        Dateien hochladen
                    </h2>

                    <p class="mt-1 text-sm text-slate-400">
                        Große Videos und Dateien werden stabil in 20-MB-Blöcken übertragen.
                    </p>
                </div>
            </div>

            <div
                class="inline-flex items-center gap-2 self-start rounded-full border border-indigo-300/15 bg-indigo-400/10 px-3 py-1.5 text-xs font-semibold text-indigo-200">
                Bis zu 10 Dateien
            </div>
        </div>
    </div>

    <div class="p-5 sm:p-7">
        <div id="uppy-dashboard"></div>

        <div class="mt-5 h-3 overflow-hidden rounded-full bg-white/10">
            <div
                id="chunk-progress-bar"
                class="h-full w-0 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 transition-all duration-300">
            </div>
        </div>

        <div
            id="chunk-status"
            class="mt-3 min-h-5 text-sm text-slate-400">
        </div>
    </div>
</section>

            <link href="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.css" rel="stylesheet">
            <script src="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bar = document.getElementById('chunk-progress-bar');
        const status = document.getElementById('chunk-status');

        if (!bar || !status || typeof Uppy === 'undefined') {
            console.error('[Uppy] Upload-Oberfläche konnte nicht initialisiert werden.');
            return;
        }

        const chunkSize = 20 * 1024 * 1024;
        const maxRetries = 3;

        const uppy = new Uppy.Uppy({
            autoProceed: false,
            restrictions: {
                maxNumberOfFiles: 10,
            },
        });

        uppy.use(Uppy.Dashboard, {
            inline: true,
            target: '#uppy-dashboard',
            proudlyDisplayPoweredByUppy: false,
            showProgressDetails: true,
            height: 360,
            note: 'FPV-Videos, Bilder, ZIPs und Musikdateien möglich',
        });

        async function uploadChunk(formData, fileName, index) {
            for (let attempt = 1; attempt <= maxRetries; attempt++) {
                try {
                    const response = await fetch(
                        '{{ route('drive.share.chunk-upload', $share->token) }}',
                        {
                            method: 'POST',
                            body: formData,
                            headers: {
                                Accept: 'application/json',
                            },
                        },
                    );

                    if (response.ok) {
                        return await response.json();
                    }

                    const errorText = await response.text();

                    if (attempt === maxRetries) {
                        throw new Error(
                            fileName
                            + ': Chunk '
                            + (index + 1)
                            + ' fehlgeschlagen: '
                            + errorText.substring(0, 250),
                        );
                    }
                } catch (error) {
                    if (attempt === maxRetries) {
                        throw error;
                    }

                    status.innerText =
                        fileName
                        + ': Chunk '
                        + (index + 1)
                        + ' fehlgeschlagen, neuer Versuch '
                        + (attempt + 1)
                        + ' von '
                        + maxRetries
                        + '...';

                    await new Promise((resolve) => {
                        window.setTimeout(resolve, 1000 * attempt);
                    });
                }
            }

            throw new Error(fileName + ': Upload konnte nicht abgeschlossen werden.');
        }

        async function uploadFileInChunks(fileObject) {
            const file = fileObject.data;

            if (!(file instanceof Blob)) {
                throw new Error(fileObject.name + ': Ungültige Datei.');
            }

            const totalChunks = Math.ceil(file.size / chunkSize);

            const uploadId = crypto.randomUUID
                ? crypto.randomUUID()
                : String(Date.now())
                    + '-'
                    + Math.random().toString(36).substring(2);

            const uploadStarted = Date.now();

            uppy.setFileState(fileObject.id, {
                progress: {
                    uploadStarted,
                    uploadComplete: false,
                    percentage: 0,
                    bytesUploaded: 0,
                    bytesTotal: file.size,
                },
            });

            for (let index = 0; index < totalChunks; index++) {
                const start = index * chunkSize;
                const end = Math.min(start + chunkSize, file.size);
                const chunk = file.slice(start, end);

                const formData = new FormData();

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('upload_id', uploadId);
                formData.append('chunk', chunk, file.name + '.part' + index);
                formData.append('chunk_index', String(index));
                formData.append('total_chunks', String(totalChunks));
                formData.append('file_name', file.name);
                formData.append('file_size', String(file.size));
                formData.append(
                    'mime_type',
                    file.type || 'application/octet-stream',
                );
                formData.append('folder_id', '{{ $folder?->id }}');

                await uploadChunk(
                    formData,
                    file.name,
                    index,
                );

                const bytesUploaded = Math.min(end, file.size);
                const percentage = Math.round(
                    (bytesUploaded / file.size) * 100,
                );

                uppy.setFileState(fileObject.id, {
                    progress: {
                        uploadStarted,
                        uploadComplete: percentage === 100,
                        percentage,
                        bytesUploaded,
                        bytesTotal: file.size,
                    },
                });

                bar.style.width = percentage + '%';

                status.innerText =
                    file.name
                    + ': '
                    + percentage
                    + '% · Chunk '
                    + (index + 1)
                    + ' von '
                    + totalChunks;
            }

            uppy.setFileState(fileObject.id, {
                progress: {
                    uploadStarted,
                    uploadComplete: true,
                    percentage: 100,
                    bytesUploaded: file.size,
                    bytesTotal: file.size,
                },
            });
        }

        uppy.addUploader(async (fileIDs) => {
            if (!fileIDs.length) {
                status.innerText = 'Bitte Datei auswählen.';
                return;
            }

            try {
                for (const fileID of fileIDs) {
                    const fileObject = uppy.getFile(fileID);

                    if (!fileObject) {
                        continue;
                    }

                    await uploadFileInChunks(fileObject);

                    uppy.emit('upload-success', fileObject, {
                        status: 200,
                        body: {
                            success: true,
                        },
                    });
                }

                bar.style.width = '100%';
                status.innerText = 'Upload fertig. Seite wird aktualisiert...';

                window.setTimeout(() => {
                    window.location.reload();
                }, 800);
            } catch (error) {
                console.error('[Uppy Chunk Upload]', error);

                for (const fileID of fileIDs) {
                    const fileObject = uppy.getFile(fileID);

                    if (fileObject) {
                        uppy.emit(
                            'upload-error',
                            fileObject,
                            error,
                        );
                    }
                }

                status.innerText =
                    error.message || 'Upload fehlgeschlagen.';

                throw error;
            }
        });
    });
</script>

            <style>
                #uppy-dashboard {
                    max-width: 100%;
                }

                #uppy-dashboard .uppy-Dashboard {
                    background: rgba(15, 23, 42, 0.72);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 24px;
                    overflow: hidden;
                    color: #fff;
                    box-shadow: 0 24px 80px rgba(0, 0, 0, 0.35);
                }

                #uppy-dashboard .uppy-Dashboard-inner {
                    background: transparent;
                    border: 0;
                    width: 100% !important;
                }

                #uppy-dashboard .uppy-Dashboard-AddFiles {
                    border: 2px dashed rgba(129, 140, 248, 0.45);
                    background: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(217, 70, 239, 0.12));
                    border-radius: 22px;
                    color: #e5e7eb;
                }

                #uppy-dashboard .uppy-Dashboard-AddFiles-title,
                #uppy-dashboard .uppy-Dashboard-note,
                #uppy-dashboard .uppy-DashboardItem-name,
                #uppy-dashboard .uppy-DashboardItem-status {
                    color: #e5e7eb;
                }

                #uppy-dashboard .uppy-DashboardContent-bar,
                #uppy-dashboard .uppy-DashboardContent-panel,
                #uppy-dashboard .uppy-Dashboard-files {
                    background: rgba(2, 6, 23, 0.35);
                    border-color: rgba(255, 255, 255, 0.08);
                }

                #uppy-dashboard .uppy-DashboardItem {
                    background: rgba(255, 255, 255, 0.06);
                    border: 1px solid rgba(255, 255, 255, 0.10);
                    border-radius: 18px;
                }

                #uppy-dashboard .uppy-DashboardItem-preview {
                    border-radius: 16px;
                    background: linear-gradient(135deg, rgba(79, 70, 229, 0.65), rgba(14, 165, 233, 0.35));
                }

                #uppy-dashboard .uppy-DashboardContent-title,
                #uppy-dashboard .uppy-DashboardContent-back,
                #uppy-dashboard .uppy-DashboardContent-addMore,
                #uppy-dashboard .uppy-DashboardContent-save {
                    color: #fff;
                }

                #uppy-dashboard .uppy-DashboardContent-addMore {
                    color: #a5b4fc;
                }

                #uppy-dashboard .uppy-StatusBar {
                    background: rgba(15, 23, 42, 0.9);
                    border-top: 1px solid rgba(255, 255, 255, 0.08);
                }

                #uppy-dashboard .uppy-StatusBar-actionBtn--upload,
                #uppy-dashboard .uppy-c-btn-primary {
                    background: linear-gradient(135deg, #6366f1, #d946ef);
                    border-radius: 14px;
                    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.35);
                    font-weight: 800;
                }

                #uppy-dashboard .uppy-StatusBar-actionBtn--upload:hover,
                #uppy-dashboard .uppy-c-btn-primary:hover {
                    background: linear-gradient(135deg, #818cf8, #e879f9);
                }

                #uppy-dashboard .uppy-DashboardItem-action--remove {
                    background: rgba(239, 68, 68, 0.95);
                    color: white;
                }

                #uppy-dashboard .uppy-DashboardContent-bar {
                    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                }

#uppy-dashboard .uppy-Dashboard {
    min-height: 360px;
}

#uppy-dashboard .uppy-Dashboard-inner {
    min-height: 360px;
}

#uppy-dashboard .uppy-Dashboard-AddFiles {
    min-height: 280px;
    transition:
        border-color 180ms ease,
        background 180ms ease,
        transform 180ms ease;
}

#uppy-dashboard .uppy-Dashboard-AddFiles:hover {
    border-color: rgba(165, 180, 252, 0.75);
    background:
        linear-gradient(
            135deg,
            rgba(99, 102, 241, 0.25),
            rgba(217, 70, 239, 0.18)
        );
}

#uppy-dashboard .uppy-Dashboard-dropFilesHereHint {
    border-radius: 20px;
    background: rgba(79, 70, 229, 0.92);
    color: white;
}

#uppy-dashboard .uppy-Dashboard-browse {
    color: #c4b5fd;
    font-weight: 800;
}

#uppy-dashboard .uppy-Dashboard-browse:hover {
    color: #e9d5ff;
}

#uppy-dashboard .uppy-StatusBar-progress {
    background:
        linear-gradient(
            90deg,
            #6366f1,
            #a855f7,
            #ec4899
        );
}


            </style>
        @endif


        @if (isset($folders) && $folders->count())
            <section class="mt-8">
                <div class="mb-5">
                    <h2 class="text-2xl font-black">Ordner</h2>
                    <p class="text-sm text-slate-400">Unterordner dieser Freigabe.</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($folders as $childFolder)
                        <a href="{{ route('drive.share.folder', [$share->token, $childFolder]) }}"
                            class="group rounded-3xl border border-white/10 bg-white/[0.06] p-5 shadow-xl shadow-black/20 backdrop-blur transition hover:-translate-y-1 hover:border-indigo-300/40 hover:bg-white/[0.09]">

                            <div
                                class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/20 text-4xl ring-1 ring-white/10">
                                📁
                            </div>

                            <div class="text-base font-bold text-white">
                                {{ $childFolder->name }}
                            </div>

                            <div class="mt-1 text-xs text-slate-400">
                                {{ $childFolder->files()->count() }} Dateien
                            </div>
                        </a>
                    @endforeach
                </div>
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
                            $mime = strtolower((string) ($file->mime_type ?? 'application/octet-stream'));
                            $extension = strtolower(pathinfo((string) $file->original_name, PATHINFO_EXTENSION));

                            $streamUrl = route('drive.share.stream', [$share->token, $file]);
                            $downloadUrl = route('drive.share.download', [$share->token, $file]);

                            $isImage = str_starts_with($mime, 'image/');
                            $isAudio = str_starts_with($mime, 'audio/');
                            $isPdf = $mime === 'application/pdf';

                            $isMov =
                                $extension === 'mov' || $mime === 'video/quicktime' || $mime === 'video/x-quicktime';

                            $isVideo =
                                str_starts_with($mime, 'video/') ||
                                in_array($extension, ['mp4', 'mov', 'm4v', 'webm', 'mkv', 'avi'], true);
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
                                    @if ($isMov)
                                        <movi-player src="{{ $streamUrl }}" controls
                                            class="block h-full w-full bg-black"
                                            style="width: 100%; height: 100%; min-height: 220px; background: #000;">
                                        </movi-player>
                                    @else
                                        <video controls preload="metadata"
                                            class="h-full w-full bg-black object-contain">
                                            <source src="{{ $streamUrl }}" type="{{ $mime }}">
                                        </video>
                                    @endif
                                @elseif ($isAudio)
                                    <div
                                        class="flex h-full flex-col items-center justify-center bg-gradient-to-br from-indigo-500/30 via-fuchsia-500/20 to-slate-900 p-6 text-center">

                                        <div
                                            class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white/10 text-4xl ring-1 ring-white/20">
                                            ♪
                                        </div>

                                        <audio controls preload="metadata" class="w-full">
                                            <source src="{{ $streamUrl }}" type="{{ $mime }}">
                                        </audio>
                                    </div>
                                @elseif ($isPdf)
                                    <iframe src="{{ $streamUrl }}" class="h-full w-full bg-white" loading="lazy">
                                    </iframe>
                                @else
                                    <div
                                        class="flex h-full items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950">

                                        <div class="text-center">
                                            <div
                                                class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-3xl ring-1 ring-white/15">
                                                ☁
                                            </div>

                                            <div class="text-sm text-slate-400">
                                                Keine Vorschau
                                            </div>
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
                                        <form method="post"
                                            action="{{ route('drive.share.delete', [$share->token, $file]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-xl bg-red-500 px-3 py-2 text-xs font-bold text-white">
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
