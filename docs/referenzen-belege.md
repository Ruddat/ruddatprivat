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
| **Veröffentlichungsrechte** ZwergiJump, Fristenfrei, Zwergi Chat/Help, RäumExpress | Als Eigenprojekte angenommen – bitte bestätigen. |
| Screenshots **RäumExpress** und **Zwergi Chat/Help** fehlen | RäumExpress-Startseite wirbt mit „24/7 Service“ und Platzhalter-Telefonnummer; Zwergi lokal ohne gebaute Assets. |
| Screenshot **ZwergiJump-Katalog** verworfen | zeigt einen Testeintrag. |

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

## ZwergiJump (`zwergijump`)

| Aussage | Beleg |
|---|---|
| 222 Migrationen, 95 Models, 17 Jobs | Verzeichnisse `database/migrations`, `app/Models`, `app/Jobs` |
| Fachliche Domänen (Booking, Payment, Payout, Billing, Contract, Franchise, Inventory, Location, ContentSafety, VendorQuality, Seo) | `app/Domain/*` |
| Mandanten-Auflösung, Rollen | `app/Http/Middleware/ResolveTenant.php`, `RoleMiddleware.php`, Guard `staff` |
| Buchungsfristen, Auto-Bestätigung, Bewertungen, Abrechnung, Ferien, Landingpages | `routes/console.php` (`bookings:*`, `reviews:*`, `settlements:*`, `holidays:import`, `landing-pages:*`) |
| PayPal über austauschbare Schnittstelle | `app/Domain/Payment/Contracts/PaymentProviderInterface.php`, `Providers/PaypalProvider.php` |
| Kalenderimport | `app/Jobs/CalendarSyncJob.php` (`CalendarImportService`) |
| Bild-/Inhaltsprüfung, Qualitätsbewertung, IndexNow | `ImageSafetyService`, `ContentSafetyService`, `RecalculateTenantQualityScore`, `SendIndexNowJob` |
| Katalog-API für LieferWiki mit API-Schlüssel und Test | `routes/api.php`, `RequireLieferWikiApiKey`, `CatalogResourceApiTest` |

## Fristenfrei (`fristenfrei`)

| Aussage | Beleg |
|---|---|
| KI-Analyse Text und Bild über OpenRouter | `OpenRouterDocumentAnalyzer`, `OpenRouterVisionDocumentAnalyzer`, `AnalyzeDocumentJob` |
| PDF-Textextraktion | `smalot/pdfparser` |
| Fristen, Erinnerungen, Aufgaben, Antwortvorlagen mit Tonalität | Models `DocumentDeadline*`, `DocumentTask`, `DocumentReplyTemplate*` |
| Terminierter Versand per E-Mail mit Kopie | `SendScheduledDocumentReplyDeliveryJob`, `document-replies:send-due` |
| Tarife, Nutzungszähler, Guthaben, Abos, PayPal | `BillingPlan*`, `UsageCounter`, `UserCreditGrant`, `UserSubscription`, `app/Domain/Payments/PayPal` |
| Protokollierte Impersonation | `ImpersonationLog` |
| Status „geplanter Funktionsumfang umgesetzt“ | letzter Commit 28.05.2026 |
| **Nicht** aufgenommen: Kalenderexport | nicht eindeutig belegbar |

## Zwergi Chat & Zwergi Help (`zwergichat`)

| Aussage | Beleg |
|---|---|
| Einbettbares Widget, Domain-Freigabe, Nutzungsanalyse | `public/widget/widget.js`, `routes/api.php` (`/widget/*`), `WidgetDomain`, `WidgetAnalytic` |
| Bot mit Trainingsantworten, Lernsignalen, Bewertungen | `BotTrainingAnswer`, `BotLearningSignal`, `ChatRating` |
| KI-Rückfallebene | `app/Services/Bot/AiFallbackService.php` (OpenRouter) |
| Hilfe-Center mit Kategorien, Tags, Suche, Feedback | `Help*`-Models, `Frontend/ZwergiHelp/HelpController` |
| Mandanten, Tarife, Rechnungen | `Tenant`, `PricingPlan`, `Subscription`, `Invoice` |
| Gedrosselte Chat-API | `throttle:chat` in `routes/api.php` |
| Frühe Entwicklungsphase | 8 Commits, letzter 21.12.2025 |
| **Nicht** aufgenommen: Live-Chat mit menschlicher Übernahme | nicht eindeutig belegbar |

## RäumExpress (`raumexpress`)

| Aussage | Beleg |
|---|---|
| Anfragen mit Objektart und Räumen, Preisregeln | `CustomerRequest`, `CustOrderRoom`, `PropertyType`, `PricingRule` |
| Franchise-Bewerbung, Onboarding mit Unterschrift, Rechtsdokumente | `FranchiseSignup`, `OnboardingWizard`, `SignaturePad`, `VendorLegalAcceptance` |
| Teams, Mitarbeitende, Kalender, Einsatzplanung | `TeamsIndex`, `MitarbeiterIndex`, `CalendarPage`, `Schedule` |
| Angebote/Aufträge mit PDF | `OfferTable`, `OrderCreate`, `OrderPdfController` |
| Schulungshandbuch mit Quiz und Fortschritt | `Handbook*`-Models und Komponenten |
| Rollen, Berechtigungen, Audit-Log | `Role`, `Permission`, `VendorAuditLog` |
| Prototyp | kein Git-Repository, letzte Änderung September 2025, nur Beispieltests |

## Screenshots

Aufgenommen am 14.11.2026 von den lokalen Instanzen (nur öffentliche Seiten, ohne Login, optionale Cookies
abgelehnt), abgelegt unter `storage/app/reference-screenshots/` (nicht im Repository). Der Seeder übernimmt sie
lokal als WebP. Auf der Produktion bitte nach Prüfung über die Referenzverwaltung hochladen.
