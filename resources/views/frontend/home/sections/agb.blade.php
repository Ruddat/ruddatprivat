@extends('frontend.layouts.app')

@section('title', 'AGB - Allgemeine Geschäftsbedingungen')

@section('content')

@include('frontend.home.sections.hero')

<section id="agb" class="services section bg-light py-5">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Allgemeine Geschäftsbedingungen (AGB)</h2>
        <p>Rechtliche Grundlagen für unsere Zusammenarbeit</p>
    </div><!-- End Section Title -->

    <div class="container mt-4" data-aos="fade-up" data-aos-delay="100">
        <!-- AGB Details -->
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">1. Geltungsbereich</h3>
            <p>
                Diese Allgemeinen Geschäftsbedingungen (AGB) gelten für alle Verträge, die zwischen <strong>Ingo Ruddat</strong> und seinen Kunden
                abgeschlossen werden. Abweichende Bedingungen des Kunden werden nicht anerkannt, es sei denn, <strong>Ingo Ruddat</strong> stimmt ihrer
                Geltung ausdrücklich zu.
            </p>

            <h3 class="mt-4">2. Vertragsabschluss</h3>
            <p>
                Ein Vertrag kommt erst mit der schriftlichen Bestätigung der Bestellung durch <strong>Ingo Ruddat</strong> oder mit Beginn der
                Vertragsausführung zustande. Angebote sind freibleibend und unverbindlich.
            </p>

            <h3 class="mt-4">3. Leistungen</h3>
            <h4 class="mt-3">3.1 Leistungsbeschreibung</h4>
            <p>
                Die Leistungen umfassen Webdesign, Webentwicklung, Beratungsleistungen sowie andere individuell vereinbarte Tätigkeiten.
            </p>
            <h4 class="mt-3">3.2 Änderungen</h4>
            <p>
                <strong>Ingo Ruddat</strong> behält sich das Recht vor, Änderungen oder Anpassungen an den Leistungen vorzunehmen, soweit diese dem Kunden
                zumutbar sind.
            </p>

            <h3 class="mt-4">4. Preise und Zahlungsbedingungen</h3>
            <h4 class="mt-3">4.1 Preise</h4>
            <p>
                Alle Preise verstehen sich zuzüglich der gesetzlichen Mehrwertsteuer, sofern nicht anders angegeben.
            </p>
            <h4 class="mt-3">4.2 Zahlungsbedingungen</h4>
            <p>
                Die Zahlung ist spätestens 14 Tage nach Rechnungsstellung fällig. Im Falle des Zahlungsverzugs behält sich <strong>Ingo Ruddat</strong> das
                Recht vor, Verzugszinsen in gesetzlicher Höhe zu berechnen.
            </p>

            <h3 class="mt-4">5. Mitwirkungspflichten des Kunden</h3>
            <p>
                Der Kunde verpflichtet sich, alle notwendigen Informationen und Unterlagen rechtzeitig bereitzustellen, um eine ordnungsgemäße
                Leistungserbringung zu gewährleisten.
            </p>

            <h3 class="mt-4">6. Haftung</h3>
            <h4 class="mt-3">6.1 Haftungsausschluss</h4>
            <p>
                <strong>Ingo Ruddat</strong> haftet nicht für Schäden, die durch leichte Fahrlässigkeit verursacht wurden, es sei denn, es handelt sich um
                die Verletzung wesentlicher Vertragspflichten.
            </p>
            <h4 class="mt-3">6.2 Haftungshöchstbetrag</h4>
            <p>
                Die Haftung ist auf den Auftragswert beschränkt, sofern nicht gesetzlich zwingend etwas anderes vorgeschrieben ist.
            </p>

            <h3 class="mt-4">7. Widerrufsrecht</h3>
            <p>
                Kunden, die Verbraucher im Sinne des § 13 BGB sind, haben das Recht, den Vertrag innerhalb von 14 Tagen ohne Angabe von Gründen zu
                widerrufen. Die Widerrufsfrist beginnt mit dem Tag des Vertragsabschlusses.
            </p>

            <h3 class="mt-4">8. Datenschutz</h3>
            <p>
                <strong>Ingo Ruddat</strong> verarbeitet personenbezogene Daten des Kunden nur im Rahmen der geltenden Datenschutzgesetze. Weitere
                Informationen finden Sie in der <a href="{{ route('datenschutz') }}">Datenschutzerklärung</a>.
            </p>

            <h3 class="mt-4">9. Kündigung</h3>
            <p>
                Eine Kündigung des Vertrages ist schriftlich mit einer Frist von 4 Wochen möglich, sofern nichts anderes vereinbart wurde.
            </p>

            <h3 class="mt-4">10. Schlussbestimmungen</h3>
            <h4 class="mt-3">10.1 Salvatorische Klausel</h4>
            <p>
                Sollte eine Bestimmung dieser AGB unwirksam sein, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt.
            </p>
            <h4 class="mt-3">10.2 Gerichtsstand</h4>
            <p>
                Gerichtsstand ist Braunschweig, Niedersachsen, sofern der Kunde Kaufmann, juristische Person des öffentlichen Rechts oder
                öffentlich-rechtliches Sondervermögen ist.
            </p>

            <h3 class="mt-4">Kontakt</h3>
            <p>Für Fragen oder weitere Informationen stehen wir Ihnen gerne zur Verfügung:</p>
            <p>
                <strong>E-Mail:</strong> <a href="mailto:service@ruddattech.de">service@ruddattech.de</a><br>
            </p>
        </div>
    </div>
</section>

@endsection
