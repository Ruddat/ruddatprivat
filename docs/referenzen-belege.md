# Referenzen – Belege für die Fallstudien

Stand: 14.11.2026. Jede Aussage in `database/seeders/ReferenceCaseStudySeeder.php` wurde im jeweiligen
Repository unter `C:\laragon\www\…` geprüft (Quellcode, `composer.json`, Projektdokumentation).
Interne Dokumente zu Vertrieb/Akquise wurden **nicht** als Quelle verwendet.

## Bitte vor der Veröffentlichung bestätigen

| Punkt | Warum |
|---|---|
| **Projektstatus** aller vier Einträge | Aus der Doku abgeleitet; ob ein System bereits produktiv genutzt wird, ist im Code nicht eindeutig. |
| **Rolle von RuddatTech** | Formuliert als Eigenprojekt (Konzeption, Architektur, Entwicklung). |
| Kein Merkmal **„Betrieb produktiver Anwendungen“** gesetzt | Erst zuordnen, wenn der Livebetrieb bestätigt ist. |
| Screenshot **Legacy-Startseite** | Zeigt einen echten Restaurantnamen mit Gericht und Preis aus dem Datenbestand. Ggf. ersetzen. |
| Screenshot **Stadtschrei** | Nachrichtenticker abgeschnitten; Veranstaltungs- und Wetter-Screenshots verworfen (fremde Inhalte bzw. leere Daten). |

## Portal-Core (`LieferWiki`)

| Aussage | Beleg |
|---|---|
| Marke wird per Domain aufgelöst, mehrere Domains/www-Alias | `app/Services/Portal/BrandResolver.php`, `app/Http/Middleware/ResolvePortalContext.php`, README |
| Synchronisierung mit JustDeliver 4 | `app/Services/Portal/Integrations/JustDeliver/` (Client, SyncManager); `routes/console.php`: `portal:sync-jd4` für Produkte, Menüs, Öffnungszeiten, Rabatte |
| ~30 Queue-Jobs, Scheduler mit Überschneidungsschutz | 28 Klassen in `app/Jobs`, `withoutOverlapping()` in `routes/console.php` |
| > 280 Tests | 283 `*Test.php` |
| Gruppenbestellungen, Checkout-Übergabe | `app/Services/Portal/GroupOrders/*` |
| PayPal, GTFS Realtime, Firebase | Treffer in `app/` (10 / 10 / 5 Dateien), README |
| Capacitor-App (Android) | `capacitor.config.ts`, `android/` |
| Manager-/Anbieterbereiche mit Berechtigungen | README, Gates/Policies in `app/` |
| Laravel 13, PHP 8.3 | `composer.json` |

## JustDeliver 4 (`justdeliver4`)

| Aussage | Beleg |
|---|---|
| > 150 Datenmodelle, mehrere Hundert Migrationen | 154 Models, 388 Migrationen |
| > 120 Tests | 125 `*Test.php` |
| 7 getrennte Authentifizierungsbereiche | `config/auth.php`: developer, web, customer, vendor, sales_partner, admin, pos |
| Smoke-/Preflight-Prüfung, Go-Live-Runbook | `justdeliver:smoke`, `justdeliver:preflight`, `docs/go-live-runbook.md` |
| Zahlungs-/Mail-Überwachung, erneuter Versand | `orders:payment-health-watch`, `orders:mail-health-watch`, `orders:retry-confirmation-mails`, `orders:expire-pending-paypal` |
| Backups inkl. Prüfung und Wiederherstellung | `jd4-backup:run|verify|restore|cleanup` |
| Abrechnungen wöchentlich/monatlich, Export, GoBD | `settlements:*`, `GenerateGobdExportJob` |
| Kassenübergabe, KI-Bilder, Treueprogramm, Kampagnen, Bewertungsprüfung | Jobs `DispatchPosOrder`, `GenerateProductAiImageJob`, `LoyaltyRewardJob`, `SendMarketingCampaignRecipient`, `CheckReviewTextJob` |
| Laravel 12, Livewire 3 | `composer.json` |

## Übernahme & Modernisierung (`justdeliver-legacy-repo`)

| Aussage | Beleg |
|---|---|
| > 1.000 PHP-Dateien, 129 Templates, ~60 Module | `docs/PHP8-MIGRATION.md` (1.064 / 129 / 20+38) |
| Eigenes Framework (2010), Smarty 2.6 | `docs/PHP8-MIGRATION.md` |
| Sicherheitsaudit vor Import | `docs/PHP8-MIGRATION.md` (Befunde bewusst **nicht** veröffentlicht) |
| Smarty 4 über Composer, Adapter | `docs/SMARTY4-MIGRATION.md`, `composer.json` |
| PDO-Kompatibilität, strikter SQL-Modus | `docs/MIGRATION-ZWISCHENSTAND-2026-11-04.md` |
| Leaflet/OpenStreetMap statt Google Maps | ebenda |
| Statische Analysen ohne Auto-Fix | `docs/PHP83-AUDIT.md` |
| Noch nicht freigegeben, Produktivsystem unverändert | README, Zwischenstand |

## BelegKalle (`steuerordner`)

| Aussage | Beleg |
|---|---|
| Festschreibung, fortlaufende Nummern, Storno per Gegenbuchung | `docs/JOURNAL_FESTSCHREIBUNG_UND_STORNO.md` |
| SHA-256-Fingerabdruck (ausdrücklich kein manipulationssicheres Archiv) | ebenda |
| Zahlungsregister, Teilzahlungen, Cent-Beträge, Anfragetokens | `docs/PROJEKTSTAND.md` |
| Versionierte Kontenpläne mit Prüfsumme, Manager-Gate | `docs/PROJEKTSTAND.md` |
| Getrennte Privat-/Unternehmensbereiche | `docs/PROJEKTSTAND.md` |
| ~60 Tests | 60 `*Test.php` |
| PDF-Erzeugung (DomPDF) | `app/Services/OwnReceipts.php` |
| Offene Punkte (E-Mail-Bestätigung, Passwort-Reset, Bankimport) | `docs/PROJEKTSTAND.md` |
| **Nicht** aufgenommen: ZUGFeRD | nur in Demo-Klassen verwendet |

## Screenshots

Aufgenommen am 14.11.2026 von den lokalen Instanzen (nur öffentliche Seiten, ohne Login, optionale Cookies
abgelehnt), abgelegt unter `storage/app/reference-screenshots/` (nicht im Repository). Der Seeder übernimmt sie
lokal als WebP. Auf der Produktion bitte nach Prüfung über die Referenzverwaltung hochladen.
