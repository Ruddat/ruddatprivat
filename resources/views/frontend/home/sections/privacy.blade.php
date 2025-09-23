@extends("frontend.layouts.app")

@section("title", "Datenschutzerklärung - Ingo Ruddat")

@section("content")

    @include("frontend.home.sections.hero")

    <section id="datenschutz" class="services section bg-light py-5">
        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Datenschutzerklärung</h2>
            <p>Schutz Ihrer persönlichen Daten gemäß DSGVO</p>
        </div><!-- End Section Title -->

        <div class="container mt-4" data-aos="fade-up" data-aos-delay="100">
            <!-- Datenschutzerklärung Details -->
            <div class="card shadow-sm p-4">
                <h3 class="mb-3">1. Einleitung</h3>
                <p>
                    Der Schutz Ihrer persönlichen Daten ist uns ein wichtiges Anliegen. Wir behandeln
                    Ihre personenbezogenen Daten vertraulich und
                    entsprechend den gesetzlichen Datenschutzvorschriften sowie dieser
                    Datenschutzerklärung.
                </p>

                <h3 class="mt-4">2. Verantwortlicher</h3>
                <p>
                    Verantwortlich für die Verarbeitung Ihrer personenbezogenen Daten ist:<br>
                    <strong>Ingo Ruddat</strong><br>
                    <strong>E-Mail:</strong> <a
                        href="mailto:service@ruddattech.de">service@ruddattech.de</a><br>
                </p>

                <h3 class="mt-4">3. Erhebung und Speicherung personenbezogener Daten</h3>
                <h4 class="mt-2">3.1 Beim Besuch der Website</h4>
                <p>
                    Beim Besuch unserer Website werden automatisch folgende Informationen erhoben und
                    temporär gespeichert:
                </p>
                <ul>
                    <li>IP-Adresse des anfragenden Rechners</li>
                    <li>Datum und Uhrzeit des Zugriffs</li>
                    <li>Name und URL der abgerufenen Datei</li>
                    <li>Website, von der aus der Zugriff erfolgt (Referrer-URL)</li>
                    <li>Verwendeter Browser und ggf. Betriebssystem Ihres Rechners</li>
                    <li>Name Ihres Access-Providers</li>
                </ul>
                <p>
                    Diese Daten werden zur Sicherstellung der Funktionsfähigkeit und Sicherheit der
                    Website sowie zur Verbesserung des Angebots genutzt.
                </p>

                <h4 class="mt-3">3.2 Bei Kontaktaufnahme</h4>
                <p>
                    Wenn Sie uns über das Kontaktformular oder per E-Mail kontaktieren, verarbeiten wir:
                </p>
                <ul>
                    <li>Ihren Namen</li>
                    <li>Ihre E-Mail-Adresse</li>
                    <li>Ihre Telefonnummer (falls angegeben)</li>
                    <li>Inhalte Ihrer Nachricht</li>
                </ul>
                <p>Diese Daten werden ausschließlich zur Bearbeitung Ihrer Anfrage verwendet.</p>

                <h3 class="mt-4">4. Rechtsgrundlage der Verarbeitung</h3>
                <p>Wir verarbeiten Ihre Daten basierend auf folgenden Grundlagen:</p>
                <ul>
                    <li>Einwilligung (Art. 6 Abs. 1 lit. a DSGVO)</li>
                    <li>Vertragserfüllung oder vorvertragliche Maßnahmen (Art. 6 Abs. 1 lit. b DSGVO)
                    </li>
                    <li>Rechtliche Verpflichtungen (Art. 6 Abs. 1 lit. c DSGVO)</li>
                    <li>Berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO)</li>
                </ul>

                <h3 class="mt-4">5. Weitergabe von Daten</h3>
                <p>
                    Eine Weitergabe Ihrer Daten an Dritte erfolgt nur, wenn:
                </p>
                <ul>
                    <li>Sie ausdrücklich zugestimmt haben</li>
                    <li>Dies für die Vertragsabwicklung erforderlich ist</li>
                    <li>Eine gesetzliche Verpflichtung besteht</li>
                    <li>Es zur Durchsetzung unserer Rechte notwendig ist</li>
                </ul>

                <h3 class="mt-4">6. Speicherdauer</h3>
                <p>
                    Ihre Daten werden nur so lange gespeichert, wie es der jeweilige Zweck erfordert
                    oder wie es gesetzliche Vorgaben vorschreiben.
                </p>

                <h3 class="mt-4">7. Ihre Rechte</h3>
                <p>Sie haben das Recht:</p>
                <ul>
                    <li>Auskunft über Ihre gespeicherten Daten zu verlangen</li>
                    <li>Berichtigung unrichtiger oder unvollständiger Daten zu verlangen</li>
                    <li>Löschung Ihrer Daten zu verlangen (soweit keine gesetzlichen Pflichten
                        entgegenstehen)</li>
                    <li>Die Verarbeitung Ihrer Daten einzuschränken</li>
                    <li>Datenübertragbarkeit zu verlangen</li>
                    <li>Ihre Einwilligung jederzeit zu widerrufen</li>
                    <li>Sich bei einer Aufsichtsbehörde zu beschweren</li>
                </ul>

                <h3 class="mt-4">8. Sicherheit</h3>
                <p>
                    Wir setzen technische und organisatorische Maßnahmen ein, um Ihre Daten vor
                    unbefugtem Zugriff, Manipulation und Verlust zu schützen.
                </p>

                <h3 class="mt-4">9. Änderungen dieser Datenschutzerklärung</h3>
                <p>
                    Wir behalten uns vor, diese Datenschutzerklärung anzupassen, um sie an geänderte
                    rechtliche Anforderungen oder an Änderungen
                    unserer Leistungen anzupassen.
                </p>

                <h3 class="mt-4">Kontakt</h3>
                <p>Für weitere Fragen stehen wir Ihnen gerne zur Verfügung:</p>
                <p>
                    <strong>E-Mail:</strong> <a
                        href="mailto:service@ruddattech.de">service@ruddattech.de</a><br>
                </p>
            </div>
        </div>
    </section>

@endsection
