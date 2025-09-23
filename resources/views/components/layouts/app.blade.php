{{-- resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? "Dashboard" }}</title>

    @vite(["resources/css/app.css", "resources/js/app.js"])
    @livewireStyles
</head>

<body class="min-h-screen bg-base-200 text-base-content antialiased">

    <!-- Header -->
    <header class="bg-base-100 shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-primary">RuddatTech</h1>
            <nav class="space-x-4">
                <a href="/" class="link">Home</a>
                <a href="/admin" class="link">Admin</a>
            </nav>
        </div>
    </header>

    <!-- Main -->
    <main class="max-w-7xl mx-auto p-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-base-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-base-content/70">
            © {{ date("Y") }} RuddatTech – Alle Rechte vorbehalten.
        </div>
    </footer>

    @livewireScripts
</body>

</html>
