<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? "App" }}</title>
    @vite("resources/css/app.css")
    @livewireStyles
</head>

<body class="bg-gray-100 text-gray-900">
    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
        <div class="text-lg font-bold">
            <a href="{{ route("home") }}">🏠 Meine App</a>
        </div>
        <div class="flex items-center space-x-4">
            @auth("admin")
                <span class="text-sm">👑 {{ auth("admin")->user()->name }}</span>
                <form method="POST" action="{{ route("admin.logout") }}">
                    @csrf
                    <button type="submit" class="text-red-600">Logout</button>
                </form>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto mt-6">
        @yield("content")
    </main>

    @livewireScripts
</body>

</html>
