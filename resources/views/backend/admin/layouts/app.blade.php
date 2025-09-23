{{-- resources/views/backend/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="de" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', v => localStorage.setItem('darkMode', v))"
    x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield("title", "Admin Dashboard")</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])

    @livewireStyles
</head>

<body class="bg-gray-100 text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">

    <div class="flex min-h-screen">

        {{-- Overlay für Mobile Sidebar --}}
        <div class="fixed inset-0 bg-black/40 z-40 md:hidden" x-show="sidebarOpen"
            x-transition.opacity @click="sidebarOpen = false"></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-200 z-50
               md:translate-x-0 md:static md:inset-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <div
                class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center md:block">
                <h1 class="text-xl font-bold text-orange-600 dark:text-orange-400">Admin</h1>
                <button class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-300"
                    @click="sidebarOpen = false">✕</button>
            </div>

            <nav class="p-4 space-y-2">
                <a href="{{ route("admin.dashboard") }}"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Dashboard
                </a>
                <a href="{{ route("admin.portfolio.editor") }}"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Portfolio Editor
                </a>
                <a href="{{ route("admin.bookkeeping.dashboard") }}"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Buchhaltung
                </a>
                <a href="#"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Nebenkosten
                </a>

                <a href="#"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Systemberichte
                </a>



                <a href="{{ route("admin.settings") }}"
                    class="block px-4 py-2 rounded hover:bg-orange-50 hover:text-orange-600 dark:hover:bg-gray-700">
                    Einstellungen
                </a>






            </nav>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col">

            {{-- Header --}}
{{-- Header --}}
<header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
    <h2 class="text-lg font-semibold">@yield("page_title", "Dashboard")</h2>

    <div class="flex items-center space-x-4">

        {{-- Suche --}}
        <a href=""
           class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
           title="Suche">
            🔍
        </a>

        {{-- Hilfe / FAQ --}}
        <a href=""
           class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
           title="Hilfe & FAQ">
            ❓
        </a>

        {{-- Einstellungen --}}
        <a href="{{ route('admin.settings') }}"
           class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
           title="Einstellungen">
            ⚙️
        </a>

        {{-- Notifications --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="relative p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
                title="Benachrichtigungen">
                🔔
                <span class="absolute -top-1 -right-1 bg-pink-600 text-white text-xs rounded-full px-1.5 py-0.5">
                    3
                </span>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-50">
                <div class="p-3 text-sm font-semibold border-b dark:border-gray-600">
                    Benachrichtigungen
                </div>
                <ul class="max-h-60 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-600">
                    <li class="p-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer">
                        Neue Rechnung erstellt ✅
                    </li>
                    <li class="p-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer">
                        Limit fast erreicht ⚠️
                    </li>
                    <li class="p-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer">
                        Neuer Login von IP 83.12.xxx
                    </li>
                </ul>
                <div class="p-2 text-center border-t dark:border-gray-600">
                    <a href="#" class="text-sm text-pink-600 hover:underline">Alle ansehen</a>
                </div>
            </div>
        </div>

        {{-- Darkmode Toggle --}}
        <button @click="darkMode = !darkMode"
            class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600"
            title="Darkmode">
            <span x-show="!darkMode">🌙</span>
            <span x-show="darkMode">☀️</span>
        </button>

        {{-- User Menu --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                class="flex items-center space-x-2 focus:outline-none">
                <span class="hidden sm:inline text-sm font-medium">
                    {{ auth("admin")->user()->name ?? "Admin" }}
                </span>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="open" @click.away="open = false" x-transition
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-50">
                <div
                    class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border-b dark:border-gray-600">
                    {{ auth("admin")->user()->email ?? "" }}
                </div>
                <a href="#"
                    class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">Profil</a>
                <form method="POST" action="{{ route("admin.logout") }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar Toggle Mobile --}}
        <button class="md:hidden text-gray-600 hover:text-gray-800 dark:text-gray-300"
            @click="sidebarOpen = true">☰</button>
    </div>
</header>


            {{-- Content --}}
            <main class="p-6 flex-1">
                @yield("content")
            </main>
        </div>
    </div>
    @livewireScripts
</body>

</html>
