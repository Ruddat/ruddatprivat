<footer id="footer" class="footer dark-background">
    <div class="container footer-top">
        <div class="row gy-4">
            <div class="col-lg-5 col-md-12 footer-about">
                <a href="{{ url("/") }}" class="logo d-flex align-items-center">
                    <span class="sitename">Ingo Ruddat</span>
                </a>
                <p>
                    Passionierter Entwickler mit Fokus auf moderne Weblösungen, benutzerfreundliche
                    Interfaces und effiziente Technologien.
                    Gemeinsam gestalten wir digitale Erlebnisse, die begeistern und Ihre Vision
                    Wirklichkeit werden lassen.
                </p>
<div class="social-links d-flex mt-4">
    @php
        $socials = [
            'twitter'  => 'bi bi-twitter',
            'facebook' => 'bi bi-facebook',
            'instagram'=> 'bi bi-instagram',
            'linkedin' => 'bi bi-linkedin',
        ];
    @endphp

    @foreach($socials as $key => $icon)
        @php $url = \App\Helpers\SettingsHelper::get("social.$key"); @endphp
        @if(!empty($url))
            <a href="{{ $url }}" target="_blank" rel="noopener">
                <i class="{{ $icon }}"></i>
            </a>
        @endif
    @endforeach
</div>
            </div>

            <div class="col-lg-2 col-6 footer-links">
                <h4>Nützliche Links</h4>
                <ul>
                    <li><a href="{{ url("/") }}">Startseite</a></li>
                    <li><a href="#">Über mich</a></li>
                    <li><a href="{{ route("agb") }}">AGB</a></li>
                    <li><a href="{{ route("datenschutz") }}">Datenschutz</a></li>
                    <li><a href="{{ route("impressum") }}">Impressum</a></li>
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
    <p>
        {{ \App\Helpers\SettingsHelper::get('company.address') }}<br>
        {{ \App\Helpers\SettingsHelper::get('company.city') }}<br>
        {{ \App\Helpers\SettingsHelper::get('company.state') }}<br>
        {{ \App\Helpers\SettingsHelper::get('company.country') }}
    </p>
    <p>
        <strong>Telefon:</strong>
        <span>{{ \App\Helpers\SettingsHelper::get('company.phone') }}</span>
    </p>
    <p>
        <strong>E-Mail:</strong>
        <a href="mailto:{{ \App\Helpers\SettingsHelper::get('company.email') }}">
            {{ \App\Helpers\SettingsHelper::get('company.email') }}
        </a>
    </p>
    <p>
        <strong>Website:</strong>
        <a href="{{ \App\Helpers\SettingsHelper::get('company.website') }}" target="_blank">
            {{ \App\Helpers\SettingsHelper::get('company.website') }}
        </a>
    </p>
   {{--
    <p>
        <strong>USt-ID:</strong>
        <span>{{ \App\Helpers\SettingsHelper::get('company.tax_id') }}</span>
    </p>
    --}}

</div>
        </div>
    </div>
</footer>
