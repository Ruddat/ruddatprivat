@extends("frontend.layouts.app")

@section("title", "Willkommen bei Ingo Ruddat")

@section("content")

    <section id="interactive-ui" class="sb-section sb-section-opt-light section-space-to-header"
        style="padding: 40px 0;">

        <div class="container sb-container-fixed sb-container">

            <div class="row align-items-center">
                <!-- Bild oben auf kleinen Bildschirmen -->
                <div class="col-lg-6 order-1 order-lg-2" data-aos="fade-left" data-aos-offset="120"
                    data-aos-duration="400">
                    <figure>
                        <img src="assets/images/interaktiv-ux.webp" alt="Interaktive Benutzeroberflächen"
                            class="img-fluid rounded shadow">
                    </figure>
                </div>

                <!-- Text unten auf kleinen Bildschirmen -->
                <div class="col-lg-6 order-2 order-lg-1" data-aos="fade-right" data-aos-offset="120"
                    data-aos-duration="400">
                    <h2 class="mb-3">Interaktive Benutzeroberflächen</h2>
                    <p class="mb-4">
                        Verbessern Sie die Benutzererfahrung mit interaktiven Benutzeroberflächen, die
                        Ihr Publikum fesseln und begeistern.
                        Ich spezialisiere mich auf die Gestaltung intuitiver Oberflächen mit CSS und
                        Electron, wobei der Fokus auf Ästhetik und Funktionalität liegt.
                        Jedes Projekt wird mit einer benutzerorientierten Denkweise angegangen, um eine
                        nahtlose Navigation und flüssige Interaktionen zu gewährleisten.
                    </p>
                    <p class="mb-4">
                        Durch die Integration moderner Designprinzipien und responsiver Layouts erstelle
                        ich Oberflächen, die nicht nur atemberaubend aussehen,
                        sondern auch auf allen Geräten hervorragend funktionieren. Ob für eine Website
                        oder eine Desktop-Anwendung, mein Ziel ist es,
                        ein immersives Erlebnis zu schaffen, das die Nutzer immer wieder zurückkehren
                        lässt.
                    </p>
                    <a href="{{ url("/intake-form") }}" class="btn btn-success">Termin vereinbaren</a>
                </div>
            </div>

        </div>

    </section>

    @include("frontend.appointment.sections.get-in-touch")

    <style>
        .section-space-to-header {
            margin-top: 150px;
            /* Abstand zum Header */
        }
    </style>

@endsection
