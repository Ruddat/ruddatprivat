<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $share->name }} · Dateifreigabe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <main class="mx-auto max-w-5xl p-6">
        <div class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h1 class="text-2xl font-bold">{{ $share->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">Private Dateifreigabe. Dateien sind nicht öffentlich gelistet.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($share->can_upload)
            <form method="post" action="{{ route('drive.share.upload', $share->token) }}" enctype="multipart/form-data" class="mb-6 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                @csrf
                <h2 class="mb-3 text-lg font-semibold">Datei hochladen</h2>
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <input type="file" name="file" required class="block w-full text-sm text-gray-700">
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Hochladen
                    </button>
                </div>
                @error('file')
                    <div class="mt-3 text-sm text-red-600">{{ $message }}</div>
                @enderror
                <p class="mt-3 text-xs text-gray-500">Aktuelles Limit in Laravel-Validation: 500 MB. Server/PHP-Limits können zusätzlich begrenzen.</p>
            </form>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 text-lg font-semibold">Dateien</h2>

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
                                <td class="px-4 py-3 font-medium">{{ $file->original_name }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $file->mime_type ?? 'unbekannt' }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $file->human_size }}</td>
                                <td class="px-4 py-3">
                                    @if ($share->can_download)
                                        <a href="{{ route('drive.share.download', [$share->token, $file]) }}" class="text-indigo-600 hover:underline">Download</a>
                                    @else
                                        <span class="text-gray-400">Nicht erlaubt</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Noch keine Dateien vorhanden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
