@extends('site.layouts.app')

@section('title', 'Impressum | RuddatTech')
@section('meta_description', 'Impressum von RuddatTech – Anbieterkennzeichnung nach § 5 DDG.')
@section('robots', 'noindex, follow')

@php
    $legal = config('ruddattech.legal');
@endphp

@section('content')
    @include('site.partials.page-hero', [
        'eyebrow' => 'Rechtliches',
        'title' => 'Impressum',
        'breadcrumbs' => ['Startseite' => route('home'), 'Impressum' => null],
    ])

    <section class="bg-white py-16 sm:py-20">
        <div class="site-container">
            <div class="prose-site">
                <h2 class="!mt-0">Angaben gemäß § 5 DDG</h2>
                <p>
                    {{ $legal['owner'] }}<br>
                    {{ $legal['business_name'] }}<br>
                    @if ($legal['street']) {{ $legal['street'] }} @else @include('site.legal._missing', ['env' => 'LEGAL_STREET']) @endif<br>
                    @if ($legal['zip_city']) {{ $legal['zip_city'] }} @else @include('site.legal._missing', ['env' => 'LEGAL_ZIP_CITY']) @endif<br>
                    {{ $legal['country'] }}
                </p>

                <h2>Kontakt</h2>
                <p>
                    E-Mail: <a class="underline decoration-brass-400 underline-offset-4" href="mailto:{{ $legal['email'] }}">{{ $legal['email'] }}</a>
                    @if ($legal['phone'])<br>Telefon: {{ $legal['phone'] }}@endif
                </p>

                @if ($legal['vat_id'])
                    <h2>Umsatzsteuer-Identifikationsnummer</h2>
                    <p>gemäß § 27a Umsatzsteuergesetz: {{ $legal['vat_id'] }}</p>
                @endif

                <h2>Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV</h2>
                <p>{{ $legal['owner'] }}, Anschrift wie oben</p>

                <h2>Verbraucherstreitbeilegung</h2>
                <p>
                    Wir sind nicht bereit und nicht verpflichtet, an Streitbeilegungsverfahren vor einer
                    Verbraucherschlichtungsstelle teilzunehmen.
                </p>

                <h2>Haftung für Inhalte</h2>
                <p>
                    Die Inhalte dieser Seiten wurden mit Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und
                    Aktualität der Inhalte kann jedoch keine Gewähr übernommen werden. Als Diensteanbieter sind wir für
                    eigene Inhalte nach den allgemeinen Gesetzen verantwortlich. Eine Pflicht zur Überwachung
                    übermittelter oder gespeicherter fremder Informationen besteht nicht. Bei Bekanntwerden von
                    Rechtsverletzungen werden wir entsprechende Inhalte umgehend entfernen.
                </p>

                <h2>Haftung für Links</h2>
                <p>
                    Diese Webseite enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben.
                    Für diese fremden Inhalte ist stets der jeweilige Anbieter oder Betreiber verantwortlich. Bei
                    Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.
                </p>

                <h2>Urheberrecht</h2>
                <p>
                    Die Inhalte und Werke auf diesen Seiten, einschließlich der Projektansichten, unterliegen dem deutschen
                    Urheberrecht. Vervielfältigung, Bearbeitung und Verbreitung außerhalb der Grenzen des Urheberrechts
                    bedürfen der schriftlichen Zustimmung.
                </p>
            </div>
        </div>
    </section>
@endsection
