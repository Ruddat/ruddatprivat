<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Services\Images\ImageOptimizer;
use Illuminate\Database\Seeder;

/**
 * Technische Fallstudien der RuddatTech-Referenzen.
 *
 * Alle Angaben wurden am 14.11.2026 im Quellcode und in der Projektdokumentation
 * der jeweiligen Repositorys geprüft (Belegliste: docs/referenzen-belege.md).
 *
 * - Einträge werden UNVERÖFFENTLICHT angelegt. Freigabe erfolgt im Adminbereich.
 * - Erneutes Ausführen aktualisiert die Texte, lässt Veröffentlichung,
 *   Hervorhebung und vorhandene Bilder aber unverändert.
 * - Liegen lokal geprüfte Screenshots unter storage/app/reference-screenshots,
 *   werden sie als WebP übernommen (nur wenn noch kein Hauptbild existiert).
 *
 * Aufruf: php artisan db:seed --class=ReferenceCaseStudySeeder
 */
class ReferenceCaseStudySeeder extends Seeder
{
    public function run(ImageOptimizer $optimizer): void
    {
        foreach ($this->caseStudies() as $data) {
            $images = $data['_images'] ?? [];
            $featured = (bool) ($data['is_featured'] ?? false);
            unset($data['_images'], $data['is_featured']);

            $item = PortfolioItem::firstOrNew(['slug' => $data['slug']]);

            // Veröffentlichung und Hervorhebung nur beim Anlegen setzen,
            // danach gilt ausschließlich die Auswahl im Adminbereich.
            if (! $item->exists) {
                $item->is_published = false;
                $item->is_featured = $featured;
            }

            $item->fill($data)->save();

            $this->attachImages($item, $images, $optimizer);
        }
    }

    private function attachImages(PortfolioItem $item, array $images, ImageOptimizer $optimizer): void
    {
        foreach ($images as $index => $image) {
            $source = storage_path('app/reference-screenshots/' . $image['file']);
            if (! is_file($source)) {
                continue;
            }

            $result = $optimizer->storeWebpVariants($source, "portfolio/{$item->id}");

            if ($index === 0) {
                if (filled($item->cover_image)) {
                    $optimizer->delete($result['variants']);

                    continue;
                }
                $item->forceFill([
                    'cover_image' => $result['path'],
                    'cover_variants' => $result['variants'],
                    'cover_alt' => $image['alt'],
                ])->save();
            } elseif (! $item->images()->where('alt', $image['alt'])->exists()) {
                $item->images()->create([
                    'path' => $result['path'],
                    'variants' => $result['variants'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'alt' => $image['alt'],
                    'caption' => $image['caption'] ?? null,
                    'sort_order' => $index,
                ]);
            } else {
                $optimizer->delete($result['variants']);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function caseStudies(): array
    {
        return [
            [
                'slug' => 'justdeliver-4',
                'title' => 'JustDeliver 4',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 10,
                'is_featured' => true,
                'project_type' => 'Bestell-, Kassen- und Abrechnungsplattform für lokale Lieferdienste',
                'project_status' => 'Eigenentwicklung, in aktiver Weiterentwicklung',
                'role' => 'Konzeption, Architektur, Entwicklung sowie Aufbau der Betriebs- und Prüfprozesse (Eigenprojekt).',
                'summary' => 'Laravel-Plattform für lokale Lieferdienste und Shops: Bestellungen, Zahlungen, Kassenanbindung und wöchentliche Abrechnungen mit klar geregelten Betriebsprüfungen.',
                'description' => 'JustDeliver 4 ist die Neuentwicklung einer Bestellplattform für regionale Restaurants und Lieferdienste. Die Plattform bündelt Online-Bestellungen, Zahlungsabwicklung, die Anbindung von Kassensystemen und die Abrechnung gegenüber den angeschlossenen Betrieben in einer gemeinsamen Anwendung.',
                'challenge' => "Viele Betriebe teilen sich eine Plattform, haben aber eigene Speisekarten, Öffnungszeiten, Liefergebiete und Abrechnungen. Gleichzeitig hängen an jeder Bestellung Zahlungen, Bestätigungen und spätere Auszahlungen – Fehler wirken sich unmittelbar auf Umsatz und Vertrauen aus.\n\nZiel war deshalb nicht nur der Funktionsumfang, sondern ein Betrieb, der sich vor und nach jedem Deployment nachvollziehbar prüfen lässt.",
                'technical_challenges' => "Eine sehr umfangreiche Datenstruktur mit mehreren Hundert Migrationen und über 150 Datenmodellen musste konsistent bleiben. Zahlungsflüsse mit PayPal, ablaufende offene Zahlungen und erneut zu versendende Bestellbestätigungen erfordern robuste, wiederholbare Prozesse.\n\nHinzu kommen getrennte Zugänge für sehr unterschiedliche Nutzergruppen – Administration, Betriebe, Kundschaft, Kassenarbeitsplätze und Partner – mit jeweils eigenen Berechtigungen.",
                'architecture' => "Laravel-Anwendung mit Livewire-Oberflächen und MySQL-Datenbank. Rechenintensive oder externe Arbeitsschritte – etwa Kassenübergabe, PDF-Erzeugung, Exporte, KI-gestützte Bildgenerierung und Kampagnenversand – laufen als Queue-Jobs. Wiederkehrende Aufgaben wie Abrechnungsläufe, Zahlungs- und Mail-Überwachung werden über den Laravel-Scheduler gesteuert.\n\nSieben getrennte Authentifizierungsbereiche (Guards) trennen die Nutzergruppen sauber voneinander.",
                'features' => [
                    'Online-Bestellung mit Lieferung und Abholung',
                    'Verwaltung von Betrieben, Speisekarten, Öffnungszeiten und Liefergebieten',
                    'PayPal-Zahlungen inklusive Behandlung abgelaufener Zahlungen',
                    'Übergabe von Bestellungen an Kassensysteme (POS)',
                    'Wöchentliche und monatliche Abrechnungen mit Buchhaltungs- und GoBD-Export',
                    'Treueprogramm, Marketing-Kampagnen und Bewertungsprüfung',
                    'Getrennte Bereiche für Administration, Betriebe, Kundschaft, Kasse und Partner',
                ],
                'integrations' => [
                    'PayPal',
                    'Kassensysteme (POS)',
                    'OpenAI (Bildgenerierung)',
                    'Portal-API für Portal-Core / LieferWiki',
                    'PDF- und Excel-Exporte',
                ],
                'operations' => "Für den Betrieb wurden eigene Prüfbefehle entwickelt: ein schneller Smoke-Test nach jedem Deployment und eine zentrale Preflight-Prüfung, deren Warnungen vor einem Go-Live bewusst bewertet werden müssen. Ein Go-Live-Runbook beschreibt Deployment und Freigabekriterien verbindlich.\n\nZahlungs- und Mail-Zustellung werden überwacht, fehlgeschlagene Bestellbestätigungen können erneut versendet werden. Backups lassen sich nicht nur erstellen, sondern auch verifizieren und wiederherstellen. Für Abrechnungsdaten gibt es Aufbewahrungs- und Bereinigungsregeln. Über 120 automatisierte Tests sichern die Kernabläufe ab.",
                'badges' => ['Laravel 12', 'PHP 8.3', 'Livewire 3', 'MySQL', 'Queues & Scheduler', 'PayPal', 'DomPDF'],
                'complexity_tags' => ['multi_tenancy', 'large_schema', 'payments', 'external_apis', 'queues', 'roles', 'critical_processes'],
                'seo_title' => 'JustDeliver 4 – Fallstudie Bestell- und Abrechnungsplattform',
                'meta_description' => 'Fallstudie: Laravel-Plattform für lokale Lieferdienste mit Bestellungen, PayPal, Kassenanbindung, Abrechnungen sowie Smoke-Tests, Preflight-Prüfung und Backups.',
                '_images' => [
                    ['file' => 'justdeliver4-start.png', 'alt' => 'Startseite des Bestellportals mit Standortsuche, Lieferung oder Abholung und Kategorien wie Pizza, Burger und Sushi'],
                ],
            ],
            [
                'slug' => 'justdeliver-legacy-modernisierung',
                'title' => 'Übernahme und Modernisierung einer PHP-Altanwendung',
                'category' => 'Übernahme & Modernisierung',
                'type' => 'showcase',
                'sort_order' => 20,
                'is_featured' => true,
                'project_type' => 'Technische Übernahme und schrittweise PHP-8-Migration einer produktiven Bestellplattform',
                'project_status' => 'Migration in Arbeit – das Produktivsystem bleibt bis zur Freigabe unverändert in Betrieb',
                'role' => 'Bestandsaufnahme, Sicherheits- und Kompatibilitätsaudit, Migrationsstrategie und Umsetzung.',
                'summary' => 'Eine über Jahre gewachsene PHP-Anwendung mit eigenem Framework und Smarty 2 wird kontrolliert auf PHP 8 gebracht – ohne den laufenden Betrieb zu gefährden.',
                'description' => 'Die ursprüngliche JustDeliver-Plattform basiert auf einem eigenen PHP-Framework aus dem Jahr 2010 und der Template-Engine Smarty 2.6. Sie lief auf einer PHP-Version, die seit Langem keine Sicherheitsupdates mehr erhält. Statt eines riskanten Komplettumbaus wurde die Anwendung strukturiert übernommen und schrittweise modernisiert.',
                'challenge' => "Ausgangspunkt war ein ZIP-Archiv des Produktivsystems: über 1.000 PHP-Dateien, 129 Templates, rund 60 Modulverzeichnisse – ohne Versionsverwaltung und ohne automatisierte Tests. Die Anwendung nutzte veraltete Sprachfunktionen, die unter PHP 8 nicht mehr existieren.\n\nDas Produktivsystem musste währenddessen unverändert weiterlaufen.",
                'technical_challenges' => "Vor dem ersten Import in ein Repository war ein Sicherheitsaudit nötig: Konfigurationsdateien und Quellcode wurden auf hinterlegte Zugangsdaten geprüft, Authentifizierung und Datenbankzugriff bewertet und nicht benötigte Altbestandteile ausgeschlossen.\n\nTechnisch mussten die alte MySQL-Schnittstelle, entfernte PHP-Funktionen und Smarty 2 ersetzt werden, ohne die Programmierschnittstellen des eigenen Frameworks zu brechen. Strikter SQL-Modus und Altdaten wie ungültige Datumswerte erforderten zusätzliche Anpassungen.",
                'architecture' => "Die Migration erfolgt auf einem eigenen Entwicklungszweig. Eine Kompatibilitätsschicht bewahrt die bestehenden Datenbankfunktionen des Frameworks und arbeitet intern mit PDO. Smarty 2 wurde durch eine fest versionierte Smarty-4-Installation über Composer ersetzt; ein Adapter hält die bestehenden Templates lauffähig.\n\nDie Kompatibilität wird mit statischen Analysen (Syntaxprüfung, PHPCompatibility) und Smoke-Tests geprüft – lesend und ohne automatische Code-Änderungen.",
                'features' => [
                    'Statische Bestandsaufnahme und dokumentierter Migrationsbefund',
                    'Sicherheitsaudit vor dem Quellcode-Import',
                    'PHP-8-Kompatibilitätsschicht mit PDO',
                    'Migration von Smarty 2.6 auf Smarty 4',
                    'Umstellung der Restaurantkarte auf Leaflet und OpenStreetMap',
                    'Responsives Redesign der öffentlichen Seiten',
                ],
                'integrations' => [
                    'MySQL über PDO',
                    'Leaflet / OpenStreetMap',
                    'Composer-Abhängigkeitsverwaltung',
                ],
                'operations' => "Grundregel der Übernahme: Das Produktivsystem bleibt unberührt, bis die Migration als Ganzes geprüft und freigegeben ist. Backups und ein anonymisierter Test-Datenbestand werden getrennt gehalten; ausgehende E-Mails, Faxe und Zahlungen sind in der Entwicklungsumgebung deaktiviert.\n\nJeder Zwischenstand wird mit offenen Punkten und Risiken dokumentiert. Die Migration ist bewusst noch nicht für den Produktivbetrieb freigegeben.",
                'badges' => ['PHP 8', 'Smarty 4', 'PDO / MySQL', 'Composer', 'PHPCompatibility', 'Leaflet'],
                'complexity_tags' => ['legacy_modernization'],
                'seo_title' => 'Fallstudie: Übernahme und PHP-8-Migration einer Altanwendung',
                'meta_description' => 'Wie eine produktive PHP-Anwendung mit eigenem Framework und Smarty 2 strukturiert übernommen, auditiert und schrittweise auf PHP 8 modernisiert wird.',
                '_images' => [
                    ['file' => 'justdeliver-legacy-start.png', 'alt' => 'Neu gestaltete Startseite der modernisierten Bestellplattform mit Suche nach Ort und Küche'],
                ],
            ],
            [
                'slug' => 'portal-core',
                'title' => 'Portal-Core',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 30,
                'is_featured' => true,
                'project_type' => 'Mehrmandantenfähiger Kern für regionale Portale und Bestellplattformen',
                'project_status' => 'Eigenentwicklung, in aktiver Weiterentwicklung',
                'role' => 'Konzeption, Architektur und Entwicklung (Eigenprojekt).',
                'summary' => 'Ein gemeinsamer Laravel-Kern trägt mehrere Portalmarken – vom Lieferportal bis zum Stadtportal – und bindet operative Systeme wie JustDeliver 4 über Schnittstellen an.',
                'description' => 'Portal-Core ist die technische Basis mehrerer regionaler Portale, darunter LieferWiki und das Stadtportal Stadtschrei. Jede Marke hat eigene Domains, Gestaltung und Funktionen; Anbieterdaten, Suche, Standortlogik und Inhalte teilen sich jedoch einen gemeinsamen Kern.',
                'challenge' => "Mehrere Marken mit sehr unterschiedlichen Anwendungsfällen – Essen bestellen, Gruppenbestellungen, Stadtinformationen, Nachrichten, Veranstaltungen, Branchenbuch – sollten nicht als getrennte Kopien entstehen, sondern auf einem gemeinsamen Kern laufen.\n\nGleichzeitig durfte die operative Fachlogik der angebundenen Systeme, etwa Bestellabwicklung und Checkout, nicht mit dem Portal vermischt werden.",
                'technical_challenges' => "Die aktive Marke wird anhand der aufgerufenen Domain aufgelöst, inklusive mehrerer Domains und www-Aliasse je Marke. Daten aus externen Quellen werden über stabile externe IDs synchronisiert, ohne manuelle Pflege zu überschreiben.\n\nRegelmäßige Hintergrundprozesse – Datenabgleich, Nachrichten- und Veranstaltungsimporte, Wetter, ÖPNV-Echtzeitdaten und automatische Anbieter-Erkennung – müssen zuverlässig und ohne Überschneidungen laufen.",
                'architecture' => "Laravel-Anwendung mit Blade, Alpine.js und Tailwind CSS. Ein zentrales Modell trennt Marke (Brand), Datenquelle (Source), Angebotsart (Offer Type) und Interaktionsablauf (Flow) – so lassen sich neue Anwendungsfälle ergänzen, ohne markenspezifische Sonderlogik einzubauen.\n\nDie Synchronisierung mit JustDeliver 4 läuft über einen eigenen API-Client und geplante Abgleichläufe für Produkte, Speisekarten, Öffnungszeiten und Rabatte. Rund 30 Queue-Jobs und zahlreiche geplante Aufgaben laufen mit Überschneidungsschutz. Eine Capacitor-App für Android nutzt Standortdienste und Push-Benachrichtigungen.",
                'features' => [
                    'Mehrere Marken mit eigener Domain, eigenem Theme und eigenen Funktionen',
                    'Anbieter- und Angebotssuche mit Standort- und Adresslogik',
                    'Warenkorb, Gruppenbestellungen und Übergabe an den Checkout',
                    'Nachrichten-, Veranstaltungs- und Wetterbereiche für Stadtportale',
                    'Automatisierte Erkennung und Übernahme regionaler Anbieter',
                    'Manager- und Anbieterbereiche mit eigenen Berechtigungen',
                    'Mobile App mit Push-Benachrichtigungen',
                ],
                'integrations' => [
                    'JustDeliver 4 (Synchronisierung und Checkout-Übergabe)',
                    'PayPal',
                    'OpenStreetMap / Nominatim und Photon (Geocoding)',
                    'GTFS / GTFS Realtime (ÖPNV-Daten)',
                    'Firebase Cloud Messaging (Push)',
                    'Capacitor (Android-App)',
                ],
                'operations' => "Hintergrundprozesse laufen über den Laravel-Scheduler mit Überschneidungsschutz und eigenen Protokolldateien je Prozess. Markenspezifische Sitemaps und robots.txt sowie pflegbare Rechtstexte je Marke gehören zum Kern.\n\nÜber 280 automatisierte Tests decken insbesondere Marken- und Domainauflösung, Standortlogik, Warenkorb, Checkout und Gruppenbestellungen ab.",
                'badges' => ['Laravel 13', 'PHP 8.3', 'Blade', 'Alpine.js', 'Tailwind CSS 4', 'MySQL', 'Capacitor'],
                'complexity_tags' => ['multi_tenancy', 'large_schema', 'payments', 'external_apis', 'queues', 'roles'],
                'seo_title' => 'Portal-Core – Fallstudie Mehrmandanten-Portalplattform',
                'meta_description' => 'Fallstudie: gemeinsamer Laravel-Kern für mehrere Portalmarken mit Domain-Auflösung, Synchronisierung externer Systeme, Hintergrundprozessen und mobiler App.',
                '_images' => [
                    ['file' => 'portal-core-lieferwiki-start.png', 'alt' => 'Startseite des Lieferportals LieferWiki mit Adresssuche und Ablauf in drei Schritten'],
                    ['file' => 'portal-core-stadtschrei-start.png', 'alt' => 'Startseite des Stadtportals Stadtschrei mit Navigation zu Nachrichten, Events, Wetter und Branchenbuch', 'caption' => 'Dieselbe Plattform als Stadtportal: eigene Marke, Domain und Gestaltung.'],
                ],
            ],
            [
                'slug' => 'belegkalle',
                'title' => 'BelegKalle',
                'category' => 'Fachanwendung',
                'type' => 'showcase',
                'sort_order' => 40,
                'is_featured' => false,
                'project_type' => 'Digitale Belegverwaltung und Buchhaltung für Selbstständige und kleine Unternehmen',
                'project_status' => 'In Entwicklung, noch nicht öffentlich freigegeben',
                'role' => 'Konzeption, Fachlogik und Entwicklung (Eigenprojekt).',
                'summary' => 'Belege, Zahlungen und Buchungen nachvollziehbar verwalten – mit festgeschriebenem Journal, Storno statt Löschen und strikter Trennung der Bereiche.',
                'description' => 'BelegKalle ist ein digitaler Aktenordner für Belege, Buchhaltung und Steuerunterlagen. Der Schwerpunkt liegt auf Nachvollziehbarkeit: Einmal festgeschriebene Buchungen werden nicht verändert, sondern bei Bedarf storniert, und jeder Arbeitsschritt bleibt dokumentiert.',
                'challenge' => "Buchhaltungsdaten verlangen eine andere Sorgfalt als gewöhnliche Webanwendungen: Beträge müssen exakt stimmen, Korrekturen nachvollziehbar sein und historische Auswertungen dürfen sich durch spätere Änderungen nicht verschieben.\n\nZugleich sollten private und geschäftliche Bereiche eines Nutzers sauber getrennt bleiben.",
                'technical_challenges' => "Festgeschriebene Buchungen erhalten fortlaufende Nummern je Betrieb und Jahr, die nie neu vergeben werden. Beim Festschreiben werden Belegdaten, Kontonummern und Kontobezeichnungen mitgespeichert, damit spätere Änderungen am Kontenplan alte Auswertungen nicht verändern.\n\nZahlungen können auf mehrere Belege verteilt werden und umgekehrt; Beträge werden in ganzen Cent verarbeitet. Wiederholte identische Bestätigungen sind über Anfragetokens gegen Doppelanlage geschützt.",
                'architecture' => "Laravel-Anwendung mit transaktionalen Schreibvorgängen und Sperren auf Bereichs- und Datensatzebene. Kontenpläne werden zentral als unveränderliche, versionierte Stände mit Prüfsumme bereitgestellt und erst nach ausdrücklicher Bestätigung veröffentlicht.\n\nDer Verwaltungsbereich ist zusätzlich über ein eigenes Berechtigungs-Gate geschützt und auf ausdrücklich freigegebene Konten beschränkt.",
                'features' => [
                    'Belegerfassung in digitalen Monatsordnern',
                    'Buchungsjournal mit Festschreibung und Storno per Gegenbuchung',
                    'Zahlungsregister mit Teilzahlungen und Zuordnung zu Belegen',
                    'Zentral gepflegte, versionierte Kontenpläne',
                    'Getrennte Privat- und Unternehmensbereiche je Nutzer',
                    'Druckansichten für Belegliste und Journal',
                ],
                'integrations' => [
                    'PDF-Erzeugung',
                    'CSV- und PDF-Import von Kontenplänen',
                ],
                'operations' => "Mutationen prüfen Eigentümer und Bereich, laufen in Transaktionen und sperren die betroffenen Datensätze. Ein SHA-256-Fingerabdruck sichert den gespeicherten Buchungsinhalt beim Storno gegen die unbemerkte Verwendung veränderter Daten ab.\n\nEtwa 60 automatisierte Tests decken die Kernabläufe ab. Bewusst noch offen sind unter anderem E-Mail-Bestätigung, Passwort-Wiederherstellung und Bankimport.",
                'badges' => ['Laravel 13', 'PHP 8.3', 'MySQL', 'DomPDF'],
                'complexity_tags' => ['multi_tenancy', 'critical_processes'],
                'seo_title' => 'BelegKalle – Fallstudie nachvollziehbare Belegverwaltung',
                'meta_description' => 'Fallstudie: Laravel-Anwendung für Belege und Buchhaltung mit festgeschriebenem Journal, Storno, Zahlungszuordnung und versionierten Kontenplänen.',
                '_images' => [
                    ['file' => 'belegkalle-start.png', 'alt' => 'Startseite von BelegKalle mit den Bereichen Belege sammeln, Buchhaltung überblicken und Steuern verstehen'],
                ],
            ],
        ];
    }
}
