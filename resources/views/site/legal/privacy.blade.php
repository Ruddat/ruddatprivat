@extends('site.layouts.app')

@section('title', 'Datenschutzerklärung | RuddatTech')
@section('meta_description', 'Datenschutzerklärung von RuddatTech: Verarbeitung personenbezogener Daten auf der Webseite, im Anfrageformular und bei internen Benachrichtigungen.')
@section('robots', 'noindex, follow')

@php
    $legal = config('ruddattech.legal');
    $alertSettings = app(\App\Services\InquiryAlerts\AlertSettings::class);
    $smsActive = $alertSettings->enabled('sms') && config('inquiry-alerts.channels.sms.driver') === 'seven';
    $telegramActive = $alertSettings->enabled('telegram');
    $whatsappAlertActive = $alertSettings->enabled('whatsapp');
    $whatsappButton = strlen(preg_replace('/\D/', '', (string) config('inquiry-alerts.whatsapp_button_number'))) >= 8;
@endphp

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Rechtliches',
        'title' => 'Datenschutzerklärung',
        'lead' => 'Stand: ' . $legal['privacy_updated'],
        'breadcrumbs' => ['Startseite' => route('home'), 'Datenschutz' => null],
    ])

    <section class="bg-white py-16 sm:py-20">
        <div class="site-container">
            <div class="prose-site">
                <h2 class="!mt-0">1. Verantwortlicher</h2>
                <p>
                    {{ $legal['owner'] }} ({{ $legal['business_name'] }})<br>
                    @if ($legal['street']) {{ $legal['street'] }}, @endif
                    @if ($legal['zip_city']) {{ $legal['zip_city'] }} @else @include('site.legal._missing', ['env' => 'LEGAL_STREET / LEGAL_ZIP_CITY']) @endif<br>
                    E-Mail: <a class="underline decoration-brass-400 underline-offset-4" href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a>
                </p>

                <h2>2. Grundsätze</h2>
                <p>
                    Diese Webseite kommt ohne Analyse- und Trackingdienste, ohne Werbe-Cookies und ohne extern geladene
                    Schriftarten oder Skripte aus. Personenbezogene Daten verarbeiten wir nur, soweit dies für den Betrieb
                    der Webseite und die Bearbeitung Ihrer Anfrage erforderlich ist.
                </p>

                <h2>3. Hosting und Server-Logfiles</h2>
                <p>
                    Die Webseite wird auf einem Server
                    @if ($legal['hoster']) bei {{ $legal['hoster'] }} @else @include('site.legal._missing', ['env' => 'LEGAL_HOSTER']) @endif
                    betrieben. Beim Aufruf werden technisch bedingt Daten verarbeitet, die Ihr Browser übermittelt:
                    IP-Adresse, Datum und Uhrzeit, aufgerufene Seite, Referrer-URL sowie Browser und Betriebssystem.
                    Die Verarbeitung dient dem sicheren und stabilen Betrieb (Art. 6 Abs. 1 lit. f DSGVO).
                    Server-Logfiles werden nach kurzer Zeit automatisch gelöscht, sofern sie nicht zur Aufklärung eines
                    Sicherheitsvorfalls benötigt werden.
                </p>

                <h2>4. Technisch notwendige Cookies</h2>
                <p>
                    Auf der Kontaktseite setzt die Webseite ein Sitzungs-Cookie und ein Sicherheits-Cookie (Schutz vor
                    gefälschten Formularübermittlungen). Diese Cookies sind für die Funktion des Formulars unbedingt
                    erforderlich (§ 25 Abs. 2 Nr. 2 TDDDG, Art. 6 Abs. 1 lit. f DSGVO) und werden mit dem Ende der Sitzung
                    bzw. nach kurzer Zeit ungültig.
                </p>

                <h2>5. Anfrageformular</h2>
                <p>
                    Wenn Sie uns über das Formular kontaktieren, verarbeiten wir Name, Unternehmen, E-Mail-Adresse,
                    optional Telefonnummer, die gewählte Anfrageart sowie Ihre Angaben zur Anwendung und zu technischen
                    Anforderungen. Zweck ist die Bearbeitung Ihrer Anfrage und gegebenenfalls die Anbahnung eines Vertrags
                    (Art. 6 Abs. 1 lit. b DSGVO); im Übrigen besteht ein berechtigtes Interesse an der Beantwortung
                    geschäftlicher Anfragen (Art. 6 Abs. 1 lit. f DSGVO).
                </p>
                <p>
                    Zum Schutz vor Missbrauch und Spam speichern wir zusätzlich einen nicht umkehrbaren Hashwert Ihrer
                    IP-Adresse – nicht die IP-Adresse selbst – und begrenzen die Anzahl der Anfragen pro Zeitraum.
                </p>
                <p>
                    Die Anfragedaten werden gelöscht, sobald sie für die Bearbeitung nicht mehr erforderlich sind. Kommt
                    ein Vertrag zustande, gelten die gesetzlichen Aufbewahrungsfristen für Geschäftskorrespondenz.
                    Bitte übermitteln Sie über das Formular keine Zugangsdaten oder vertraulichen Daten Dritter.
                </p>

                <h2>6. Interne Benachrichtigung über neue Anfragen</h2>
                <p>
                    Damit keine Anfrage unbemerkt bleibt, werden wir über neue Anfragen intern benachrichtigt. Die
                    Benachrichtigung per E-Mail an unser eigenes Postfach enthält Vorgangsnummer, Unternehmen,
                    Ansprechpartner und Anfrageart. Rechtsgrundlage ist unser berechtigtes Interesse an einer zeitnahen
                    Bearbeitung (Art. 6 Abs. 1 lit. f DSGVO).
                </p>
                @if ($smsActive || $telegramActive || $whatsappAlertActive)
                    <p>
                        Zusätzlich nutzen wir die folgenden Dienste. Dabei werden ausschließlich der Name des anfragenden
                        Unternehmens und die Anfrageart übermittelt – keine Namen von Personen, keine E-Mail-Adressen oder
                        Telefonnummern und keine Beschreibung Ihrer Anwendung:
                    </p>
                    <ul class="check-list mt-4 space-y-3">
                        @if ($smsActive)
                            <li><strong>SMS-Versand</strong> über seven communications GmbH &amp; Co. KG (seven.io), Kiel, Deutschland.</li>
                        @endif
                        @if ($telegramActive)
                            <li><strong>Telegram</strong> (Telegram Messenger Inc.). Eine Verarbeitung außerhalb der EU ist dabei nicht auszuschließen.</li>
                        @endif
                        @if ($whatsappAlertActive)
                            <li><strong>WhatsApp Business Platform</strong> der Meta Platforms Ireland Ltd., Dublin, Irland. Eine Übermittlung an Meta Platforms Inc. in den USA ist möglich.</li>
                        @endif
                    </ul>
                @endif

                @if ($whatsappButton)
                    <h2>7. Kontakt über WhatsApp</h2>
                    <p>
                        Auf der Webseite befindet sich ein Link, der einen Chat in WhatsApp öffnet. Erst beim Anklicken
                        wird eine Verbindung zu WhatsApp (Meta Platforms Ireland Ltd.) hergestellt; vorher werden keine
                        Daten an WhatsApp übertragen. Wenn Sie uns über WhatsApp schreiben, gelten die
                        Datenschutzbestimmungen von WhatsApp. Solche Nachrichten werden nicht in unserem Anfragesystem
                        gespeichert. Für vertrauliche Angaben nutzen Sie bitte das Anfrageformular oder E-Mail.
                    </p>
                @endif

                <h2>{{ $whatsappButton ? '8' : '7' }}. Kontakt per E-Mail</h2>
                <p>
                    Wenn Sie uns per E-Mail schreiben, verarbeiten wir Ihre Angaben zur Bearbeitung Ihres Anliegens
                    (Art. 6 Abs. 1 lit. b bzw. f DSGVO).
                </p>

                <h2>{{ $whatsappButton ? '9' : '8' }}. Ihre Rechte</h2>
                <p>
                    Sie haben das Recht auf Auskunft (Art. 15 DSGVO), Berichtigung (Art. 16), Löschung (Art. 17),
                    Einschränkung der Verarbeitung (Art. 18), Datenübertragbarkeit (Art. 20) sowie Widerspruch gegen
                    Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21 DSGVO). Wenden Sie sich dazu an die
                    oben genannte E-Mail-Adresse.
                </p>
                <p>
                    Sie können sich außerdem bei einer Datenschutz-Aufsichtsbehörde beschweren, zum Beispiel bei:
                    {{ $legal['authority'] }}.
                </p>
            </div>
        </div>
    </section>
@endsection
