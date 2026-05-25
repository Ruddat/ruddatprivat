<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-end">

        <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
            <h1 class="sitename">Ingo Ruddat</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/#hero') }}" class="active">Start</a></li>
                <li><a href="{{ url('/#about') }}">Profil</a></li>
                <li><a href="{{ url('/#services') }}">Leistungen</a></li>
                <li><a href="{{ url('/#work') }}">Projekte</a></li>
                <li><a href="{{ url('/#stack') }}">Stack</a></li>
                <li><a href="{{ url('/#contact') }}">Kontakt</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        {{-- Auth Bereich --}}
        @guest('customer')
            @if(\Illuminate\Support\Facades\Route::has('customer.login'))
                <a class="btn-getstarted me-2" href="{{ route('customer.login') }}">Login</a>
            @endif
        @endguest

        @auth('customer')
            @if(\Illuminate\Support\Facades\Route::has('customer.dashboard'))
                <a class="btn-getstarted me-2" href="{{ route('customer.dashboard') }}">Mein Bereich</a>
            @endif

            @if(\Illuminate\Support\Facades\Route::has('customer.logout'))
                <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-getstarted btn btn-danger ms-2">Logout</button>
                </form>
            @endif
        @endauth

        <a class="btn-getstarted" href="{{ url('/#contact') }}">Anfragen</a>
    </div>
</header>
