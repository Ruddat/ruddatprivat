<footer id="footer" class="footer dark-background">
    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-5 col-md-12 footer-about">
                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                    <span class="sitename">Ingo Ruddat</span>
                </a>
                <p>
                    Passionierter Entwickler mit Fokus auf moderne Weblösungen, benutzerfreundliche Interfaces und effiziente Technologien.
                    Gemeinsam gestalten wir digitale Erlebnisse, die begeistern und Ihre Vision Wirklichkeit werden lassen.
                </p>
                <div class="social-links d-flex mt-4">
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-6 footer-links">
                <h4>Nützliche Links</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Startseite</a></li>
                    <li><a href="#">Über mich</a></li>
                    <li><a href="{{ route('agb') }}">AGB</a></li>
                    <li><a href="{{ route('datenschutz') }}">Datenschutz</a></li>
                    <li><a href="{{ route('impressum') }}">Impressum</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-6 footer-links">
                <h4>Meine Leistungen</h4>
                <ul>
                    <li><a href="#">Webdesign</a></li>
                    <li><a href="#">Webentwicklung</a></li>
                    <li><a href="#">Produktmanagement</a></li>
                    <li><a href="#">Marketing</a></li>
                    <li><a href="#">Grafikdesign</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                <h4>Kontakt</h4>
                <p>Braunschweig, NI DE</p>
                <p>Germany</p>
                <p><strong>E-Mail:</strong> <span>fbp89262@kisoq.com</span></p>
            </div>
        </div>
    </div>
</footer>
