<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-end">

      <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
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

      <a class="btn-getstarted" href="{{ url('/schedule-appointment') }}">Jetzt Startem</a>

    </div>
    
  </header>
