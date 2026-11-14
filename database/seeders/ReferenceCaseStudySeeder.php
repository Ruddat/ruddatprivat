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
                'title' => 'JustDeliver 4 / Foodzwerge',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 20,
                'is_featured' => true,
                'project_type' => 'Commerce-Engine für Bestellungen, Kasse und Abrechnung mit API für angebundene Portale',
                'project_status' => 'Im Produktivbetrieb seit Juni 2026 (Foodzwerge)',
                'role' => 'Konzeption, Architektur, Entwicklung, Aufbau der Betriebs- und Prüfprozesse sowie laufender Betrieb (Eigenprojekt).',
                'summary' => 'Produktive Laravel-Commerce-Engine hinter Foodzwerge: Bestellungen, Zahlungen, Kassenanbindung, Abrechnungen und Schnittstellen zu Shopify und Portal-Core – abgesichert durch eigene Betriebsprüfungen.',
                'description' => "JustDeliver 4 ist die Neuentwicklung einer Bestellplattform für regionale Restaurants, Lieferdienste und Shops und läuft seit Juni 2026 im Produktivbetrieb unter der Marke Foodzwerge. Die Plattform bündelt Online-Bestellungen, Zahlungsabwicklung, die Anbindung von Kassensystemen und die Abrechnung gegenüber den angeschlossenen Betrieben.\n\nDarüber hinaus ist JustDeliver 4 als Commerce-Engine ausgebaut: Über eigene APIs versorgt es Portal-Core mit Produkten, Speisekarten, Öffnungszeiten und Rabatten und übernimmt dort ausgelöste Bestellungen. Shopify-Kataloge angebundener Shops werden automatisch importiert.",
                'challenge' => "Viele Betriebe teilen sich eine Plattform, haben aber eigene Speisekarten, Öffnungszeiten, Liefergebiete und Abrechnungen. Gleichzeitig hängen an jeder Bestellung Zahlungen, Bestätigungen und spätere Auszahlungen – Fehler wirken sich unmittelbar auf Umsatz und Vertrauen aus.\n\nZiel war deshalb nicht nur der Funktionsumfang, sondern ein Betrieb, der sich vor und nach jedem Deployment nachvollziehbar prüfen lässt.",
                'technical_challenges' => "Eine sehr umfangreiche Datenstruktur mit mehreren Hundert Migrationen und über 150 Datenmodellen musste konsistent bleiben. Zahlungsflüsse mit PayPal, ablaufende offene Zahlungen und erneut zu versendende Bestellbestätigungen erfordern robuste, wiederholbare Prozesse.\n\nHinzu kommen getrennte Zugänge für sehr unterschiedliche Nutzergruppen – Administration, Betriebe, Kundschaft, Kassenarbeitsplätze und Partner – mit jeweils eigenen Berechtigungen.",
                'architecture' => "Laravel-Anwendung mit Livewire-Oberflächen und MySQL-Datenbank. Rechenintensive oder externe Arbeitsschritte – etwa Kassenübergabe, PDF-Erzeugung, Exporte, KI-gestützte Bildgenerierung und Kampagnenversand – laufen als Queue-Jobs. Wiederkehrende Aufgaben wie Abrechnungsläufe, Zahlungs- und Mail-Überwachung sowie der Shopify-Katalogabgleich werden über den Laravel-Scheduler gesteuert.\n\nAnbindungen an externe Shopsysteme laufen über eine eigene Commerce-Schicht mit austauschbaren Konnektoren; Portale greifen über versionierte APIs zu. Sieben getrennte Authentifizierungsbereiche (Guards) trennen die Nutzergruppen sauber voneinander.",
                'features' => [
                    'Online-Bestellung mit Lieferung und Abholung',
                    'Verwaltung von Betrieben, Speisekarten, Öffnungszeiten und Liefergebieten',
                    'PayPal-Zahlungen inklusive Behandlung abgelaufener Zahlungen',
                    'Übergabe von Bestellungen an Kassensysteme (POS)',
                    'Wöchentliche und monatliche Abrechnungen mit Buchhaltungs- und GoBD-Export',
                    'Treueprogramm, Marketing-Kampagnen und Bewertungsprüfung',
                    'Getrennte Bereiche für Administration, Betriebe, Kundschaft, Kasse und Partner',
                    'Commerce-API für angebundene Portale und automatischer Shopify-Katalogimport',
                ],
                'integrations' => [
                    'Portal-API für Portal-Core (Kalle-Welt, LieferWiki)',
                    'Shopify (Katalogimport)',
                    'PayPal',
                    'Kassensysteme (POS)',
                    'OpenAI (Bildgenerierung)',
                    'PDF- und Excel-Exporte',
                ],
                'operations' => "Für den Betrieb wurden eigene Prüfbefehle entwickelt: ein schneller Smoke-Test nach jedem Deployment und eine zentrale Preflight-Prüfung, deren Warnungen vor einem Go-Live bewusst bewertet werden müssen. Ein Go-Live-Runbook beschreibt Deployment und Freigabekriterien verbindlich.\n\nZahlungs- und Mail-Zustellung werden überwacht, fehlgeschlagene Bestellbestätigungen können erneut versendet werden. Backups lassen sich nicht nur erstellen, sondern auch verifizieren und wiederherstellen. Für Abrechnungsdaten gibt es Aufbewahrungs- und Bereinigungsregeln. Über 120 automatisierte Tests sichern die Kernabläufe ab.\n\nSeit dem Livegang im Juni 2026 läuft die Plattform störungsfrei im Produktivbetrieb.",
                'badges' => ['Laravel 12', 'PHP 8.3', 'Livewire 3', 'MySQL', 'Queues & Scheduler', 'PayPal', 'Shopify-API', 'DomPDF'],
                'complexity_tags' => ['multi_tenancy', 'large_schema', 'payments', 'external_apis', 'queues', 'roles', 'production', 'critical_processes'],
                'seo_title' => 'JustDeliver 4 / Foodzwerge – Fallstudie Commerce-Engine',
                'meta_description' => 'Fallstudie: produktive Laravel-Commerce-Engine mit Bestellungen, PayPal, Kasse, Abrechnungen, Shopify- und Portal-API sowie Smoke-Tests, Preflight und Backups.',
                '_images' => [
                    ['file' => 'justdeliver4-start.png', 'alt' => 'Startseite des Bestellportals mit Standortsuche, Lieferung oder Abholung und Kategorien wie Pizza, Burger und Sushi'],
                ],
            ],
            [
                'slug' => 'justdeliver-legacy-modernisierung',
                'title' => 'JustDeliver: 16 Jahre Betrieb, Übernahme und PHP-8-Modernisierung',
                'category' => 'Übernahme & Modernisierung',
                'type' => 'showcase',
                'sort_order' => 10,
                'is_featured' => true,
                'project_type' => 'Technische Übernahme und PHP-8-Migration einer seit 16 Jahren produktiven Bestellplattform',
                'project_status' => 'Seit rund 16 Jahren im Produktivbetrieb; modernisierte PHP-8-Version live seit Ende Oktober 2026',
                'role' => 'Betrieb der Plattform, Bestandsaufnahme, Sicherheits- und Kompatibilitätsaudit, Migrationsstrategie, Umsetzung und Livegang.',
                'summary' => 'Eine seit 16 Jahren produktive Bestellplattform mit eigenem Framework und Smarty 2 wurde kontrolliert auf PHP 8 gebracht und ohne Unterbrechung des Geschäftsbetriebs wieder live geschaltet.',
                'description' => 'Die JustDeliver-Plattform vermittelt seit rund 16 Jahren Bestellungen an regionale Restaurants und Lieferdienste und verarbeitet täglich Bestellungen. Sie basiert auf einem eigenen PHP-Framework aus dem Jahr 2010 und der Template-Engine Smarty 2.6 und lief zuletzt auf einer PHP-Version ohne Sicherheitsupdates.\n\nStatt eines riskanten Komplettumbaus wurde die Anwendung strukturiert übernommen, auditiert und schrittweise modernisiert. Seit Ende Oktober 2026 läuft die modernisierte Version im Produktivbetrieb.',
                'challenge' => "Eine geschäftskritische Anwendung mit täglichem Bestellaufkommen durfte während der Modernisierung nicht ausfallen. Ausgangspunkt war ein Archiv des Produktivsystems: über 1.000 PHP-Dateien, 129 Templates und rund 60 Modulverzeichnisse – ohne Versionsverwaltung und ohne automatisierte Tests.\n\nDie Anwendung nutzte Sprachfunktionen, die unter PHP 8 nicht mehr existieren, und eine Template-Engine aus dem Jahr 2008.",
                'technical_challenges' => "Vor dem ersten Import in ein Repository war ein Sicherheitsaudit nötig: Konfigurationsdateien und Quellcode wurden auf hinterlegte Zugangsdaten geprüft, Authentifizierung und Datenbankzugriff bewertet und nicht benötigte Altbestandteile ausgeschlossen.\n\nTechnisch mussten die alte MySQL-Schnittstelle, entfernte PHP-Funktionen und Smarty 2 ersetzt werden, ohne die Programmierschnittstellen des eigenen Frameworks zu brechen. Strikter SQL-Modus und über Jahre gewachsene Altdaten erforderten zusätzliche Anpassungen.",
                'architecture' => "Die Migration erfolgte auf einem eigenen Entwicklungszweig. Eine Kompatibilitätsschicht bewahrt die bestehenden Datenbankfunktionen des Frameworks und arbeitet intern mit PDO. Smarty 2 wurde durch eine fest versionierte Smarty-4-Installation über Composer ersetzt; ein Adapter hält die bestehenden Templates lauffähig.\n\nDie Kompatibilität wurde mit statischen Analysen (Syntaxprüfung, PHPCompatibility) und Smoke-Tests geprüft – lesend und ohne automatische Code-Änderungen. Parallel wurden die öffentlichen Seiten responsiv neu gestaltet.",
                'features' => [
                    'Statische Bestandsaufnahme und dokumentierter Migrationsbefund',
                    'Sicherheitsaudit vor dem Quellcode-Import',
                    'PHP-8-Kompatibilitätsschicht mit PDO',
                    'Migration von Smarty 2.6 auf Smarty 4',
                    'Umstellung der Restaurantkarte auf Leaflet und OpenStreetMap',
                    'Responsives Redesign der öffentlichen Seiten',
                    'Livegang der modernisierten Version im laufenden Betrieb',
                ],
                'integrations' => [
                    'MySQL über PDO',
                    'Leaflet / OpenStreetMap',
                    'Composer-Abhängigkeitsverwaltung',
                ],
                'operations' => "Grundregel der Übernahme: Das Produktivsystem blieb unberührt, bis die Migration als Ganzes geprüft war. Backups und ein anonymisierter Test-Datenbestand wurden getrennt gehalten; ausgehende E-Mails, Faxe und Zahlungen waren in der Entwicklungsumgebung deaktiviert. Jeder Zwischenstand wurde mit offenen Punkten und Risiken dokumentiert.\n\nSeit Ende Oktober 2026 läuft die modernisierte Version störungsfrei im Produktivbetrieb und verarbeitet weiterhin täglich Bestellungen.",
                'badges' => ['PHP 8', 'Smarty 4', 'PDO / MySQL', 'Composer', 'PHPCompatibility', 'Leaflet'],
                'complexity_tags' => ['legacy_modernization', 'production', 'critical_processes'],
                'seo_title' => 'JustDeliver: Übernahme und PHP-8-Migration im Livebetrieb',
                'meta_description' => 'Fallstudie: Eine seit 16 Jahren produktive Bestellplattform wird auditiert, auf PHP 8 und Smarty 4 migriert und ohne Betriebsunterbrechung wieder live geschaltet.',
                '_images' => [
                    ['file' => 'justdeliver-legacy-start.png', 'alt' => 'Neu gestaltete Startseite der modernisierten Bestellplattform mit Suche nach Ort und Küche'],
                ],
            ],
            [
                'slug' => 'portal-core',
                'title' => 'Portal-Core und die Kalle-Welt',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 30,
                'is_featured' => true,
                'project_type' => 'Mehrmarken-Portalkern mit angebundenen Fach-Backends für Bestellung und Vermietung',
                'project_status' => 'Im Produktivbetrieb mit mehreren Marken (u. a. FragKalle, LieferKalle, RegioKalle, StadtKalle)',
                'role' => 'Konzeption, Architektur, Entwicklung und Betrieb (Eigenprojekt).',
                'summary' => 'Ein Laravel-Kern trägt die Kalle-Welt – FragKalle, LieferKalle, RegioKalle, StadtKalle, RetterKalle und weitere Marken – und bindet Fach-Backends wie JustDeliver 4 und ZwergiJump über Schnittstellen an.',
                'description' => "Portal-Core ist die gemeinsame technische Basis mehrerer regionaler Marken. Unter dem Dach von FragKalle laufen unter anderem LieferKalle für Lieferdienste und Ortsseiten, RegioKalle für regionale Erzeuger mit eigenem Hof-Cockpit, StadtKalle als Stadtportal mit Nachrichten, Veranstaltungen und Kleinanzeigen, RetterKalle für Rettungsangebote sowie TischKalle und KioskKalle. Auch LieferWiki und Stadtschrei basieren auf demselben Kern.\n\nJede Marke hat eigene Domain, Gestaltung und Zuständigkeiten. Portal-Core selbst enthält bewusst kein eigenes Shopsystem: Bestellungen und Vermietungen werden von spezialisierten Backends abgewickelt – JustDeliver 4 für Essen und Kiosk, ZwergiJump für Vermietung.",
                'challenge' => "Mehrere Marken mit sehr unterschiedlichen Anwendungsfällen – Essen bestellen, regionale Produkte, Gruppenbestellungen, Stadtinformationen, Nachrichten, Veranstaltungen, Kleinanzeigen, Branchenbuch – sollten nicht als getrennte Kopien entstehen, sondern auf einem gemeinsamen Kern laufen.\n\nGleichzeitig durfte die operative Fachlogik der angebundenen Systeme, etwa Bestellabwicklung, Vermietung und Checkout, nicht mit dem Portal vermischt werden.",
                'technical_challenges' => "Die aktive Marke wird anhand der aufgerufenen Domain aufgelöst, inklusive mehrerer Domains und www-Aliasse je Marke. Daten aus externen Quellen werden über stabile externe IDs synchronisiert, ohne manuelle Pflege zu überschreiben.\n\nRegelmäßige Hintergrundprozesse – Datenabgleich, Nachrichten- und Veranstaltungsimporte, Wetter, ÖPNV-Echtzeitdaten und automatische Anbieter-Erkennung – müssen zuverlässig und ohne Überschneidungen laufen.",
                'architecture' => "Laravel-Anwendung mit Blade, Alpine.js und Tailwind CSS. Ein zentrales Modell trennt Marke (Brand), Datenquelle (Source), Angebotsart (Offer Type) und Interaktionsablauf (Flow) – so lassen sich neue Anwendungsfälle ergänzen, ohne markenspezifische Sonderlogik einzubauen.\n\nDie Synchronisierung mit JustDeliver 4 läuft über einen eigenen API-Client und geplante Abgleichläufe für Produkte, Speisekarten, Öffnungszeiten und Rabatte. Rund 30 Queue-Jobs und zahlreiche geplante Aufgaben laufen mit Überschneidungsschutz. Eine Capacitor-App für Android nutzt Standortdienste und Push-Benachrichtigungen.",
                'features' => [
                    'Kalle-Welt: FragKalle, LieferKalle, RegioKalle, StadtKalle, RetterKalle, TischKalle, KioskKalle',
                    'Mehrere Marken mit eigener Domain, eigenem Theme und eigenen Funktionen',
                    'Fach-Backends statt eigenem Shop: JustDeliver 4 (Bestellung) und ZwergiJump (Vermietung)',
                    'Partner-Cockpits je Marke, z. B. Hof-Cockpit für regionale Erzeuger',
                    'Anbieter- und Angebotssuche mit Standort- und Adresslogik',
                    'Warenkorb, Gruppenbestellungen und Übergabe an den Checkout',
                    'Nachrichten-, Veranstaltungs- und Wetterbereiche für Stadtportale',
                    'Automatisierte Erkennung und Übernahme regionaler Anbieter',
                    'Manager- und Anbieterbereiche mit eigenen Berechtigungen',
                    'Mobile App mit Push-Benachrichtigungen',
                ],
                'integrations' => [
                    'JustDeliver 4 (Synchronisierung und Checkout-Übergabe)',
                    'ZwergiJump (Katalog- und Verfügbarkeits-API für Vermietung)',
                    'PayPal',
                    'OpenStreetMap / Nominatim und Photon (Geocoding)',
                    'GTFS / GTFS Realtime (ÖPNV-Daten)',
                    'Firebase Cloud Messaging (Push)',
                    'Capacitor (Android-App)',
                ],
                'operations' => "Hintergrundprozesse laufen über den Laravel-Scheduler mit Überschneidungsschutz und eigenen Protokolldateien je Prozess. Markenspezifische Sitemaps und robots.txt sowie pflegbare Rechtstexte je Marke gehören zum Kern.\n\nÜber 280 automatisierte Tests decken insbesondere Marken- und Domainauflösung, Standortlogik, Warenkorb, Checkout und Gruppenbestellungen ab. Die Marken der Kalle-Welt sind unter eigenen .de-Domains im Produktivbetrieb.",
                'badges' => ['Laravel 13', 'PHP 8.3', 'Blade', 'Alpine.js', 'Tailwind CSS 4', 'MySQL', 'Capacitor'],
                'complexity_tags' => ['multi_tenancy', 'large_schema', 'payments', 'external_apis', 'queues', 'roles', 'production'],
                'seo_title' => 'Portal-Core & Kalle-Welt – Fallstudie Mehrmarken-Plattform',
                'meta_description' => 'Fallstudie: ein Laravel-Kern für FragKalle, LieferKalle, RegioKalle, StadtKalle und weitere Marken, mit JustDeliver 4 und ZwergiJump als Fach-Backends.',
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
            [
                'slug' => 'zwergijump',
                'title' => 'ZwergiJump',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 50,
                'is_featured' => false,
                'project_type' => 'Buchungs- und Vermietungsplattform für Hüpfburgen und Eventausstattung mit Partner- und Franchisemodell',
                'project_status' => 'Eigenentwicklung, in aktiver Weiterentwicklung',
                'role' => 'Konzeption, Architektur und Entwicklung (Eigenprojekt).',
                'summary' => 'Regionale Vermietung von Hüpfburgen und Eventmodulen: Verfügbarkeit, Buchung in Schritten, Partnerverwaltung, Franchiseverträge und wöchentliche Abrechnungen auf einer Plattform.',
                'description' => 'ZwergiJump verbindet Familien und Veranstalter mit regionalen Vermietern von Hüpfburgen, Partyzubehör und Eventmodulen. Die Plattform übernimmt Katalog, Verfügbarkeit, Buchung und Bezahlung und organisiert im Hintergrund Partner, Verträge und Abrechnungen.',
                'challenge' => "Vermietungen hängen an Datum, Ort, Lieferbarkeit und Zusatzleistungen. Mehrere unabhängige Partner bieten ihre Ausstattung an, teils im Rahmen eines Franchisemodells mit eigenen Verträgen und Abrechnungen.\n\nBuchungen dürfen weder doppelt vergeben werden noch unbearbeitet liegen bleiben, und Inhalte der Partner müssen vor der Veröffentlichung geprüft werden.",
                'technical_challenges' => "Eine Buchung durchläuft einen eigenen Lebenszyklus: Entwürfe werden bereinigt, offene Buchungen nach Fristen storniert oder automatisch bestätigt, Partner bei ausbleibender Reaktion eskaliert. Externe Kalender der Partner werden importiert, um Verfügbarkeiten abzugleichen.\n\nZahlungen laufen über eine austauschbare Anbieterschnittstelle; Auszahlungen an Partner werden erst nach Prüfung freigegeben.",
                'architecture' => "Laravel-Anwendung mit Livewire, fachlich in Domänen gegliedert – unter anderem Buchung, Zahlung, Auszahlung, Abrechnung, Verträge, Franchise, Inventar, Standorte, Inhaltsprüfung und Anbieterqualität. Mandanten werden über eine eigene Middleware aufgelöst; Rollen steuern den Zugriff auf Verwaltung, Partner- und Franchisebereiche.\n\nBildverarbeitung, Inhaltsprüfung, Kalenderabgleich, Erinnerungen, Qualitätsbewertung und Suchmaschinen-Benachrichtigungen laufen als Queue-Jobs.",
                'features' => [
                    'Katalog mit Verfügbarkeit und Buchung in Schritten (Datum, Produkt, Extras)',
                    'Buchungslebenszyklus mit automatischer Bestätigung und Fristen',
                    'Partnerbereich mit Onboarding, Verträgen, Regionen und Liefergebieten',
                    'Franchiseverträge, -pläne und -abrechnungen',
                    'Wöchentliche Abrechnungen mit Freigabe der Auszahlungen',
                    'Bewertungsanfragen, Gutscheine und QR-Kampagnen',
                    'Automatische Prüfung von Bildern und Texten der Partner',
                    'Qualitätsbewertung der Anbieter und lokale SEO-Landingpages',
                ],
                'integrations' => [
                    'PayPal (über austauschbare Zahlungsschnittstelle)',
                    'Katalog-API für Portal-Core / LieferWiki',
                    'Import externer Kalender',
                    'IndexNow',
                    'Import von Schulferien',
                ],
                'operations' => "Geplante Aufgaben steuern Buchungsfristen, Bewertungserinnerungen, Abrechnungsläufe, Ferienimporte und die Erzeugung von Landingpages. Rechtliche Dokumente werden versioniert, ihre Annahme wird protokolliert.\n\nDie Katalog-Schnittstelle für Portal-Core ist per API-Schlüssel geschützt und durch automatisierte Tests abgesichert.",
                'badges' => ['Laravel 12', 'PHP 8.2+', 'Livewire 3', 'MySQL', 'Redis', 'Queues & Scheduler', 'PayPal'],
                'complexity_tags' => ['multi_tenancy', 'large_schema', 'payments', 'external_apis', 'queues', 'roles', 'critical_processes'],
                'seo_title' => 'ZwergiJump – Fallstudie Buchungs- und Vermietungsplattform',
                'meta_description' => 'Fallstudie: Laravel-Plattform für die Vermietung von Hüpfburgen mit Buchungslebenszyklus, Partner- und Franchisemodell, Abrechnungen und Kalenderabgleich.',
                '_images' => [
                    ['file' => 'zwergijump-start.png', 'alt' => 'Startseite von ZwergiJump mit Ortssuche und Buchung in vier Schritten: Datum, Produkt, Extras und Bestätigung'],
                ],
            ],
            [
                'slug' => 'fristenfrei',
                'title' => 'Fristenfrei',
                'category' => 'SaaS',
                'type' => 'showcase',
                'sort_order' => 60,
                'is_featured' => false,
                'project_type' => 'SaaS zur Analyse von Behördenpost mit Fristenerkennung und Antwortentwürfen',
                'project_status' => 'Geplanter Funktionsumfang umgesetzt (Stand Mai 2026)',
                'role' => 'Konzeption, Entwicklung und KI-Integration (Eigenprojekt).',
                'summary' => 'Behördenschreiben hochladen, Inhalt und Fristen verstehen, Antwort vorbereiten: KI-gestützte Dokumentanalyse mit Erinnerungen, Antwortvorlagen und abgestuften Tarifen.',
                'description' => 'Fristenfrei hilft dabei, Bescheide und andere Behördenschreiben zu verstehen, bevor Fristen ablaufen. Dokumente werden analysiert, Fristen und nächste Schritte herausgearbeitet und sachliche Antwortentwürfe vorbereitet. Die Entscheidung bleibt beim Nutzer; die Anwendung ersetzt keine Rechtsberatung.',
                'challenge' => "Behördenpost ist vertraulich, oft schwer verständlich und mit Fristen verbunden. Dokumente liegen als PDF oder als Scan vor, manchmal schlecht lesbar.\n\nDie Anwendung musste sensible Inhalte zuverlässig verarbeiten, ohne selbstständig zu handeln, und gleichzeitig ein nachvollziehbares, abgestuftes Bezahlmodell abbilden.",
                'technical_challenges' => "Für Text-PDFs wird der Inhalt extrahiert, gescannte Dokumente werden über ein bildfähiges Modell analysiert. Die Analyse läuft als Hintergrundjob, damit Uploads nicht blockieren.\n\nFristen erzeugen Erinnerungen; Antworten können zu einem gewählten Zeitpunkt per E-Mail versendet werden, auf Wunsch mit Kopie an den Nutzer. Nutzungszähler, Guthaben und Abonnements begrenzen den Verbrauch je Tarif.",
                'architecture' => "Laravel-Anwendung mit Livewire. Die KI-Analyse ist über austauschbare Analyzer-Klassen angebunden (Text und Bild). Ein Zugriffsdienst prüft für jedes Dokument die Berechtigung; Tarife, Preise und Funktionen sind datenbankgestützt konfigurierbar.\n\nMinütliche geplante Aufgaben versenden fällige Erinnerungen und terminierte Antworten.",
                'features' => [
                    'Upload von PDFs und Scans mit automatischer Analyse',
                    'Erkennung von Dokumenttyp, Fristen und nächsten Schritten',
                    'Fristenerinnerungen und Aufgaben je Dokument',
                    'Antwortentwürfe mit Vorlagen und wählbarer Tonalität',
                    'Terminierter Versand von Antworten per E-Mail',
                    'Tarife mit Nutzungszählern, Guthaben und Abonnements',
                    'Protokollierte Support-Anmeldung als Nutzer (Impersonation)',
                ],
                'integrations' => [
                    'OpenRouter (Text- und Bildanalyse)',
                    'PDF-Textextraktion',
                    'PayPal',
                    'E-Mail-Versand',
                ],
                'operations' => "Hintergrundverarbeitung entkoppelt die Analyse vom Upload. Erinnerungen und terminierte Antworten werden minütlich verarbeitet. Administrative Anmeldungen als Nutzer werden protokolliert.\n\nDie Anwendung weist ausdrücklich darauf hin, dass sie keine Rechtsberatung ersetzt und nichts ohne Zutun des Nutzers versendet.",
                'badges' => ['Laravel 12', 'PHP 8.2+', 'Livewire 4', 'OpenRouter', 'PayPal', 'mPDF'],
                'complexity_tags' => ['payments', 'external_apis', 'queues', 'critical_processes'],
                'seo_title' => 'Fristenfrei – Fallstudie KI-gestützte Dokumentanalyse',
                'meta_description' => 'Fallstudie: Laravel-SaaS zur Analyse von Behördenpost mit Fristenerkennung, Erinnerungen, Antwortentwürfen, Tarifen und PayPal.',
                '_images' => [
                    ['file' => 'fristenfrei-start.png', 'alt' => 'Startseite von Fristenfrei mit Beispielanalyse: Dokumenttyp, erkannte Frist und nächster Schritt'],
                ],
            ],
            [
                'slug' => 'zwergi-chat-help',
                'title' => 'Zwergi Chat & Zwergi Help',
                'category' => 'SaaS',
                'type' => 'showcase',
                'sort_order' => 70,
                'is_featured' => false,
                'project_type' => 'Einbettbares Chat-Widget mit Bot und Hilfe-Center',
                'project_status' => 'Frühe Entwicklungsphase',
                'role' => 'Konzeption und Entwicklung (Eigenprojekt).',
                'summary' => 'Support-Widget für Webseiten: Bot-Antworten mit Lernfunktion, KI-Rückfallebene und ein durchsuchbares Hilfe-Center – mandantenfähig mit Tarifen.',
                'description' => 'Zwergi Chat ist ein Chat-Widget, das Unternehmen mit einem Skript in ihre Webseite einbinden. Häufige Fragen beantwortet ein Bot, der aus Bewertungen und Trainingsantworten lernt. Zwergi Help ergänzt ein Hilfe-Center mit Artikeln, Kategorien und Suche.',
                'challenge' => "Ein Widget, das auf fremden Webseiten läuft, muss sicher konfigurierbar sein: Nur freigegebene Domains dürfen es nutzen, und jeder Mandant sieht ausschließlich seine eigenen Gespräche, Inhalte und Auswertungen.\n\nDer Bot sollte nicht nur feste Antworten liefern, sondern aus Bewertungen und Trainingsantworten besser werden.",
                'technical_challenges' => "Das Widget kommuniziert über eine eigene, gedrosselte API mit dem Server; Konfiguration, Nutzungsereignisse und Chatverläufe werden je Mandant und Domain getrennt.\n\nFür nicht beantwortbare Fragen gibt es eine KI-Rückfallebene. Suchanfragen im Hilfe-Center werden protokolliert, um fehlende Artikel zu erkennen.",
                'architecture' => "Laravel-Anwendung mit Livewire und Sanctum. Ein eigenständiges JavaScript-Widget lädt seine Konfiguration vom Server. Chat-Sitzungen, Kontext, Nachrichten und Bewertungen sind getrennte Modelle; Lernsignale und Trainingsantworten fließen in die Bot-Antworten ein.\n\nDas Hilfe-Center nutzt Markdown für Artikel, Tags und Kategorien sowie eine eigene Sitemap.",
                'features' => [
                    'Einbettbares Chat-Widget mit Domain-Freigabe',
                    'Bot-Antworten mit Trainingsantworten und Lernsignalen',
                    'KI-Rückfallebene für offene Fragen',
                    'Chat-Bewertungen und Nutzungsanalyse des Widgets',
                    'Hilfe-Center mit Kategorien, Tags, Suche und Artikel-Feedback',
                    'Mandanten mit Tarifen, Abonnements und Rechnungen',
                ],
                'integrations' => [
                    'OpenRouter (KI-Rückfallebene)',
                    'JavaScript-Widget zum Einbinden in Webseiten',
                    'Markdown (CommonMark)',
                ],
                'operations' => "Die Chat-API ist gegen übermäßige Anfragen gedrosselt. Widget-Aufrufe werden je Domain erfasst, sodass Nutzung und Fehlkonfigurationen sichtbar werden.\n\nDas Projekt befindet sich in einer frühen Entwicklungsphase.",
                'badges' => ['Laravel 12', 'PHP 8.2+', 'Livewire 3', 'Sanctum', 'JavaScript', 'OpenRouter'],
                'complexity_tags' => ['multi_tenancy', 'external_apis'],
                'seo_title' => 'Zwergi Chat & Help – Fallstudie Chat-Widget und Hilfe-Center',
                'meta_description' => 'Fallstudie: mandantenfähiges Chat-Widget mit lernendem Bot, KI-Rückfallebene und Hilfe-Center auf Basis von Laravel.',
                '_images' => [],
            ],
            [
                'slug' => 'raeumexpress',
                'title' => 'RäumExpress',
                'category' => 'Plattform',
                'type' => 'showcase',
                'sort_order' => 80,
                'is_featured' => false,
                'project_type' => 'Auftrags- und Partnerplattform für ein Franchise-Netz im Bereich Entrümpelung',
                'project_status' => 'Prototyp (Stand September 2025)',
                'role' => 'Konzeption und Entwicklung (Eigenprojekt).',
                'summary' => 'Anfragen mit Objektart und Räumen, Preisregeln, Partner-Onboarding mit digitaler Unterschrift, Einsatzplanung und ein Schulungshandbuch für Franchisepartner.',
                'description' => 'RäumExpress bildet die Abläufe eines Franchise-Netzes für Entrümpelungen ab: von der Anfrage über Angebot und Auftrag bis zur Einsatzplanung. Partner werden über einen Onboarding-Prozess aufgenommen und mit einem Schulungshandbuch eingearbeitet.',
                'challenge' => "Ein Franchise-Netz braucht einheitliche Abläufe, obwohl jeder Partner eigenständig arbeitet: gleiche Preislogik, nachvollziehbare Verträge, dokumentierte Einarbeitung und eine klare Zuständigkeit für jede Anfrage.",
                'technical_challenges' => "Preise ergeben sich aus Regeln je Objektart und Umfang. Partner durchlaufen einen mehrstufigen Onboarding-Assistenten mit Dokumenten, Ausstattung, digitaler Unterschrift und protokollierter Annahme rechtlicher Dokumente.\n\nTeams, Mitarbeitende und Berechtigungen müssen je Partner getrennt verwaltet werden.",
                'architecture' => "Laravel-Anwendung mit Livewire und getrennten Bereichen für Verwaltung und Partner. Rollen und Berechtigungen steuern den Zugriff; Änderungen an Partnerdaten werden in einem Audit-Log erfasst. Angebote und Aufträge werden als PDF erzeugt.",
                'features' => [
                    'Anfrageformular mit Objektart und Räumen',
                    'Preisregeln je Objektart',
                    'Franchise-Bewerbung und Partner-Onboarding mit digitaler Unterschrift',
                    'Teams, Mitarbeitende, Kalender und Einsatzplanung',
                    'Angebote und Aufträge mit PDF-Ausgabe',
                    'Schulungshandbuch mit Modulen, Lektionen, Quiz und Fortschritt',
                ],
                'integrations' => [
                    'PDF-Erzeugung (DomPDF)',
                ],
                'operations' => "Rechtliche Dokumente werden versioniert und ihre Annahme durch Partner protokolliert. Ein Audit-Log hält Änderungen an Partnerdaten fest.\n\nDas Projekt liegt als Prototyp vor.",
                'badges' => ['Laravel 12', 'PHP 8.2+', 'Livewire 3', 'MySQL', 'DomPDF'],
                'complexity_tags' => ['roles'],
                'seo_title' => 'RäumExpress – Fallstudie Franchise- und Auftragsplattform',
                'meta_description' => 'Fallstudie: Laravel-Plattform für ein Franchise-Netz mit Anfragen, Preisregeln, Partner-Onboarding, Einsatzplanung und Schulungshandbuch.',
                '_images' => [],
            ],
        ];
    }
}
