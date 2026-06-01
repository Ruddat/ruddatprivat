<div class="p-6 space-y-6">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Private Dateibox</h1>
            <p class="text-sm text-gray-500">Private Dokumente, FPV-Videos, MP3s und Freigabeordner.</p>
        </div>

        <div class="text-sm text-gray-500">
            Aktueller Ordner:
            <span class="font-semibold text-gray-800">{{ $currentFolder?->name ?? 'Hauptverzeichnis' }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex gap-2">
                        @if ($currentFolder)
                            <button wire:click="openFolder(null)" class="rounded-lg border border-gray-300 px-3 py-2 text-sm hover:bg-gray-50">
                                Hauptverzeichnis
                            </button>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row">
                        <input wire:model="folderName" type="text" placeholder="Neuer Ordner" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button wire:click="createFolder" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            Ordner erstellen
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    @forelse ($folders as $folder)
                        <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4 hover:bg-gray-50">
                            <button wire:click="openFolder({{ $folder->id }})" class="text-left">
                                <div class="font-semibold text-gray-900">📁 {{ $folder->name }}</div>
                                <div class="text-xs text-gray-500">{{ $folder->created_at?->format('d.m.Y H:i') }}</div>
                            </button>
                            <button wire:click="deleteFolder({{ $folder->id }})" onclick="return confirm('Ordner wirklich löschen?')" class="text-xs text-red-600 hover:underline">
                                Löschen
                            </button>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-5 text-sm text-gray-500 md:col-span-2">
                            Keine Unterordner vorhanden.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Dateien</h2>

                <div class="mb-5 flex flex-col gap-3 rounded-xl border border-dashed border-gray-300 p-4">
                    <input wire:model="upload" type="file" class="block w-full text-sm text-gray-700">
                    @error('upload')
                        <div class="text-sm text-red-600">{{ $message }}</div>
                    @enderror
                    <div wire:loading wire:target="upload" class="text-sm text-gray-500">Upload wird vorbereitet...</div>
                    <button wire:click="saveUpload" wire:loading.attr="disabled" class="w-fit rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50">
                        Datei hochladen
                    </button>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Typ</th>
                                <th class="px-4 py-3">Größe</th>
                                <th class="px-4 py-3">Aktion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($files as $file)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $file->original_name }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $file->mime_type ?? 'unbekannt' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $file->human_size }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-3">
                                            <a href="{{ route('drive.download', $file) }}" class="text-indigo-600 hover:underline">Download</a>
                                            <button wire:click="deleteFile({{ $file->id }})" onclick="return confirm('Datei wirklich löschen?')" class="text-red-600 hover:underline">
                                                Löschen
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">Keine Dateien in diesem Ordner.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-2 text-lg font-semibold text-gray-900">Freigabe erstellen</h2>
                <p class="mb-4 text-sm text-gray-500">Freigaben gelten immer für den aktuell geöffneten Ordner.</p>

                <div class="space-y-3">
                    <input wire:model="shareName" type="text" placeholder="Name der Freigabe" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input wire:model="shareCanDownload" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Download erlauben
                    </label>

                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input wire:model="shareCanUpload" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Upload erlauben
                    </label>

                    <button wire:click="createShare" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                        Freigabelink erstellen
                    </button>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Aktive Freigaben</h2>

                <div class="space-y-3">
                    @forelse ($shares as $share)
                        <div class="rounded-xl border border-gray-200 p-3">
                            <div class="font-semibold text-gray-900">{{ $share->name }}</div>
                            <div class="text-xs text-gray-500">
                                Upload: {{ $share->can_upload ? 'ja' : 'nein' }} · Download: {{ $share->can_download ? 'ja' : 'nein' }}
                            </div>
                            <a href="{{ route('drive.share.show', $share->token) }}" target="_blank" class="mt-2 block break-all text-xs text-indigo-600 hover:underline">
                                {{ route('drive.share.show', $share->token) }}
                            </a>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Noch keine Freigaben vorhanden.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
