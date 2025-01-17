<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Home')</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    @include('frontend.layouts.partials.head')
    @cookieconsentscripts

</head>
<body class="index-page">


    @include('frontend.layouts.partials.header')

    <!-- Main Content -->
    <main class="main">
        @yield('content')
    </div>




    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Footer -->
    @include('frontend.layouts.partials.footer')
    <!-- Vendor JS Files -->
    @include('frontend.layouts.partials.footer-script')
    @cookieconsentview
</body>
</html>
