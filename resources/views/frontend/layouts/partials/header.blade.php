<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-end">

        <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
            <h1 class="sitename">Ingo Ruddat</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/#hero') }}" class="active">Home</a></li>
                <li><a href="{{ url('/#about') }}">Über mich</a></li>
                <li><a href="{{ url('/#why-us') }}">Services</a></li>
                <li><a href="{{ url('/#contact') }}">Kontakt</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        {{-- Auth Bereich --}}
        @guest('customer')
            <a class="btn-getstarted me-2" href="{{ route('customer.login') }}">Login</a>
        @endguest

        @auth('customer')
            <a class="btn-getstarted me-2" href="{{ route('customer.dashboard') }}">Mein Bereich</a>
            <form action="{{ route('customer.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-getstarted btn btn-danger ms-2">Logout</button>
            </form>
        @endauth

        <a class="btn-getstarted" href="{{ url('/schedule-appointment') }}">Jetzt Starten</a>
    </div>
</header>
