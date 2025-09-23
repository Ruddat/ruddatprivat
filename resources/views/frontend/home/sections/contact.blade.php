<!-- Kontakt Abschnitt -->
<section id="contact" class="contact section">

    <!-- Abschnittsüberschrift -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Kontakt</h2>
        <p>Lassen Sie uns über Ihr Projekt sprechen!</p>
    </div><!-- Ende Abschnittsüberschrift -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

<div class="col-lg-5">
    <div class="info-wrap">
        {{-- Email --}}
        @if($email = \App\Helpers\SettingsHelper::get('contact.email'))
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                    <h3>Kontakt</h3>
                    <p>{{ $email }}</p>
                </div>
            </div>
        @endif

        {{-- Address --}}
        @if($address = \App\Helpers\SettingsHelper::get('contact.address'))
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                    <h3>Standort</h3>
                    <p>{{ $address }}</p>
                </div>
            </div>
        @endif

        {{-- Opening hours --}}
        @if($hours = \App\Helpers\SettingsHelper::get('contact.opening_hours'))
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-clock flex-shrink-0"></i>
                <div>
                    <h3>Öffnungszeiten</h3>
                    <p>{!! nl2br(e($hours)) !!}</p>
                </div>
            </div>
        @endif
    </div>
</div>


            <livewire:frontend.contact-form.contact-form-component />

        </div>

    </div>

</section><!-- /Kontakt Abschnitt -->
