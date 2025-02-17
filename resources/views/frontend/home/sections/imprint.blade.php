@extends('frontend.layouts.app')

@section('title', 'Impressum - Ingo Ruddat')

@section('content')

@include('frontend.home.sections.hero')

<section id="impressum" class="services section bg-light py-5">
      <!-- Section Title -->
      <div class="container section-title aos-init aos-animate" data-aos="fade-up">
        <h2>Impressum</h2>
        <p>Angaben gemäß § 5 TMG</p>
      </div><!-- End Section Title -->


    <div class="container mt-4" data-aos="fade-up" data-aos-delay="100">
        <!-- Impressum Details -->
        <div class="card shadow-sm p-4">
            <h3 class="mb-3">Angaben gemäß § 5 TMG</h3>
            <p><strong>Ingo Ruddat</strong><br>
                Braunschweig, Niedersachsen, Deutschland<br>
                <strong>E-Mail:</strong> <a href="mailto:service@ruddattech.de">service@ruddattech.de</a><br>
            </p>

            <h3 class="mt-4">Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h3>
            <p><strong>Ingo Ruddat</strong><br>
                Lehrte, Niedersachsen, Deutschland
            </p>

            <h3 class="mt-4">Haftung für Inhalte</h3>
            <p>
                Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.
                Nach §§ 8 bis 10 TMG sind wir jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach
                Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
            </p>
            <p>
                Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt.
                Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich.
                Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
            </p>

            <h3 class="mt-4">Haftung für Links</h3>
            <p>
                Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese
                fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber
                der Seiten verantwortlich.
            </p>
            <p>
                Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum
                Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete
                Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend
                entfernen.
            </p>

            <h3 class="mt-4">Urheberrecht</h3>
            <p>
                Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die
                Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der
                schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten,
                nicht kommerziellen Gebrauch gestattet.
            </p>
            <p>
                Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere
                werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir
                um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.
            </p>

            <h3 class="mt-4">Online-Streitbeilegung</h3>
            <p>
                Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: <a href="https://ec.europa.eu/consumers/odr"
                    target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr</a>.
            </p>
            <p>
                Unsere E-Mail-Adresse finden Sie oben im Impressum.
            </p>
            <p>
                Wir sind weder verpflichtet noch bereit, an einem Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.
            </p>

            <h3 class="mt-4">Kontakt</h3>
            <p>Für weitere Fragen stehen wir Ihnen gerne zur Verfügung!</p>
        </div>
    </div>
</section>

@endsection
