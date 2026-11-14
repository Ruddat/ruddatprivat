@extends('site.layouts.app')

{{-- Inhalt unverändert aus der bisherigen AGB-Seite übernommen. Eine Anpassung an
     Wartungs- und Betriebsverträge (Laufzeiten, Reaktionszeiten, Mitwirkung,
     Haftung) muss rechtlich geprüft werden und ist bewusst nicht Teil des Relaunchs. --}}

@section('title', 'AGB – Allgemeine Geschäftsbedingungen | RuddatTech')
@section('meta_description', 'Allgemeine Geschäftsbedingungen von RuddatTech (Ingo Ruddat).')
@section('robots', 'noindex, follow')

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Rechtliches',
        'title' => 'Allgemeine Geschäftsbedingungen',
        'breadcrumbs' => ['Startseite' => route('home'), 'AGB' => null],
    ])

    <section class="bg-white py-16 sm:py-20">
        <div class="site-container">
            <div class="prose-site">
                <h2 class="!mt-0">1. Geltungsbereich</h2>
                <p>Diese Allgemeinen Geschäftsbedingungen (AGB) gelten für alle Verträge, die zwischen Ingo Ruddat und seinen Kunden abgeschlossen werden. Abweichende Bedingungen des Kunden werden nicht anerkannt, es sei denn, Ingo Ruddat stimmt ihrer Geltung ausdrücklich zu.</p>

                <h2>2. Vertragsabschluss</h2>
                <p>Ein Vertrag kommt erst mit der schriftlichen Bestätigung der Bestellung durch Ingo Ruddat oder mit Beginn der Vertragsausführung zustande. Angebote sind freibleibend und unverbindlich.</p>

                <h2>3. Leistungen</h2>
                <p><strong>3.1 Leistungsbeschreibung.</strong> Die Leistungen umfassen Webdesign, Webentwicklung, Beratungsleistungen sowie andere individuell vereinbarte Tätigkeiten.</p>
                <p><strong>3.2 Änderungen.</strong> Ingo Ruddat behält sich das Recht vor, Änderungen oder Anpassungen an den Leistungen vorzunehmen, soweit diese dem Kunden zumutbar sind.</p>

                <h2>4. Preise und Zahlungsbedingungen</h2>
                <p><strong>4.1 Preise.</strong> Alle Preise verstehen sich zuzüglich der gesetzlichen Mehrwertsteuer, sofern nicht anders angegeben.</p>
                <p><strong>4.2 Zahlungsbedingungen.</strong> Die Zahlung ist spätestens 14 Tage nach Rechnungsstellung fällig. Im Falle des Zahlungsverzugs behält sich Ingo Ruddat das Recht vor, Verzugszinsen in gesetzlicher Höhe zu berechnen.</p>

                <h2>5. Mitwirkungspflichten des Kunden</h2>
                <p>Der Kunde verpflichtet sich, alle notwendigen Informationen und Unterlagen rechtzeitig bereitzustellen, um eine ordnungsgemäße Leistungserbringung zu gewährleisten.</p>

                <h2>6. Haftung</h2>
                <p><strong>6.1 Haftungsausschluss.</strong> Ingo Ruddat haftet nicht für Schäden, die durch leichte Fahrlässigkeit verursacht wurden, es sei denn, es handelt sich um die Verletzung wesentlicher Vertragspflichten.</p>
                <p><strong>6.2 Haftungshöchstbetrag.</strong> Die Haftung ist auf den Auftragswert beschränkt, sofern nicht gesetzlich zwingend etwas anderes vorgeschrieben ist.</p>

                <h2>7. Widerrufsrecht</h2>
                <p>Kunden, die Verbraucher im Sinne des § 13 BGB sind, haben das Recht, den Vertrag innerhalb von 14 Tagen ohne Angabe von Gründen zu widerrufen. Die Widerrufsfrist beginnt mit dem Tag des Vertragsabschlusses.</p>

                <h2>8. Datenschutz</h2>
                <p>Ingo Ruddat verarbeitet personenbezogene Daten des Kunden nur im Rahmen der geltenden Datenschutzgesetze. Weitere Informationen finden Sie in der <a class="underline decoration-brass-400 underline-offset-4" href="{{ route('datenschutz') }}">Datenschutzerklärung</a>.</p>

                <h2>9. Kündigung</h2>
                <p>Eine Kündigung des Vertrages ist schriftlich mit einer Frist von 4 Wochen möglich, sofern nichts anderes vereinbart wurde.</p>

                <h2>10. Schlussbestimmungen</h2>
                <p><strong>10.1 Salvatorische Klausel.</strong> Sollte eine Bestimmung dieser AGB unwirksam sein, bleibt die Wirksamkeit der übrigen Bestimmungen unberührt.</p>
                <p><strong>10.2 Gerichtsstand.</strong> Gerichtsstand ist Braunschweig, Niedersachsen, sofern der Kunde Kaufmann, juristische Person des öffentlichen Rechts oder öffentlich-rechtliches Sondervermögen ist.</p>

                <h2>Kontakt</h2>
                <p>Für Fragen oder weitere Informationen: <a class="underline decoration-brass-400 underline-offset-4" href="mailto:{{ config('ruddattech.legal.email') }}">{{ config('ruddattech.legal.email') }}</a></p>
            </div>
        </div>
    </section>
@endsection
