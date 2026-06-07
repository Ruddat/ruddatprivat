<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Projektfreigabe' }} – RuddatTech</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-gray-100 text-gray-800 antialiased">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-14 items-center">
                <span class="text-lg font-bold text-pink-600">RuddatTech</span>

                <span class="text-xs text-gray-400">Projektfreigabe</span>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} RuddatTech – Alle Rechte vorbehalten.
        </div>
    </footer>

    @livewireScripts
</body>

</html>
