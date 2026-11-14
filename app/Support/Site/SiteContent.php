<?php

namespace App\Support\Site;

/**
 * Redaktionelle Inhalte der öffentlichen RuddatTech-Webseite.
 *
 * Bewusst als einfache PHP-Struktur statt CMS: Die Leistungstexte ändern sich selten,
 * sind versioniert und lassen sich im Code-Review prüfen.
 *
 * Grundsätze: keine Preise, keine Pauschalpakete, keine 24/7- oder SLA-Zusagen,
 * keine erfundenen Kennzahlen oder Kundenstimmen.
 */
class SiteContent
{
    public const CONTRACT_NOTE = 'Individuelle Wartungs- und Betriebsverträge auf Basis einer technischen Bestandsaufnahme.';

    public const CONTACT_EMAIL = 'service@ruddattech.de';

    /**
     * Hauptnavigation (Reihenfolge = Darstellung).
     *
     * @return array<int, array{label: string, route: string, active: string}>
     */
    public static function navigation(): array
    {
        return [
            ['label' => 'Startseite', 'route' => 'home', 'active' => 'home'],
            ['label' => 'Leistungen', 'route' => 'leistungen.index', 'active' => 'leistungen.*'],
            ['label' => 'Referenzen', 'route' => 'referenzen.index', 'active' => 'referenzen.*'],
            ['label' => 'Über RuddatTech', 'route' => 'about', 'active' => 'about'],
            ['label' => 'Kontakt', 'route' => 'kontakt', 'active' => 'kontakt'],
        ];
    }

    /**
     * Ablauf einer Zusammenarbeit.
     *
     * @return array<int, array{title: string, text: string}>
     */
    public static function process(): array
    {
        return [
            [
                'title' => 'Erstgespräch und Anforderungsanalyse',
                'text' => 'Wir klären, welche Rolle die Anwendung in Ihrem Unternehmen spielt, wer sie nutzt, welche Probleme bestehen und welche Erwartungen Sie an Wartung und Betrieb haben.',
            ],
            [
                'title' => 'Technische Bestandsaufnahme',
                'text' => 'Ich sichte Quellcode, Abhängigkeiten, Datenbank, Serverumgebung, Deployment und vorhandene Dokumentation – mit den Zugängen, die Sie dafür freigeben.',
            ],
            [
                'title' => 'Bewertung von Risiken und Wartbarkeit',
                'text' => 'Sie erhalten eine nachvollziehbare Einschätzung: veraltete Komponenten, Sicherheitsrisiken, fehlende Backups, Engpässe und der Aufwand, der für einen stabilen Betrieb nötig ist.',
            ],
            [
                'title' => 'Individuelles Wartungs- bzw. Betriebskonzept',
                'text' => 'Auf dieser Grundlage beschreiben wir gemeinsam Leistungsumfang, Zuständigkeiten, Kommunikationswege und – falls gewünscht – Reaktionszeiten.',
            ],
            [
                'title' => 'Vertraglich geregelte Übernahme und laufende Betreuung',
                'text' => 'Nach Vertragsabschluss folgt eine geordnete Übernahme. Danach betreue ich die Anwendung im vereinbarten Rahmen und dokumentiere die durchgeführten Arbeiten.',
            ],
        ];
    }

    /**
     * Gründe für RuddatTech – nur belegbare Aussagen.
     *
     * @return array<int, array{icon: string, title: string, text: string}>
     */
    public static function reasons(): array
    {
        return [
            [
                'icon' => 'layers',
                'title' => 'Erfahrung mit komplexen Webanwendungen',
                'text' => 'Eigene Plattformen mit umfangreichen Datenmodellen, Zahlungs- und Abrechnungsprozessen, Hintergrundverarbeitung und externen Schnittstellen – entwickelt, weiterentwickelt und produktiv betrieben.',
            ],
            [
                'icon' => 'briefcase',
                'title' => 'Verständnis für Geschäftsprozesse',
                'text' => 'Software ist kein Selbstzweck. Ich frage zuerst, welcher Prozess an einer Funktion hängt und was ein Ausfall für Ihr Tagesgeschäft bedeutet.',
            ],
            [
                'icon' => 'server',
                'title' => 'Blick auf die gesamte Infrastruktur',
                'text' => 'Anwendung, Datenbank, Webserver, Queues, Cronjobs, E-Mail-Versand, Backups und Deployment hängen zusammen. Ich betrachte sie als ein System.',
            ],
            [
                'icon' => 'clipboard',
                'title' => 'Strukturierte, nachvollziehbare Arbeitsweise',
                'text' => 'Änderungen erfolgen versioniert und dokumentiert, nach Möglichkeit über Entwicklungszweige und Tests. Eingriffe in produktive Systeme werden vorher abgestimmt.',
            ],
            [
                'icon' => 'calendar',
                'title' => 'Langfristige technische Betreuung',
                'text' => 'Mein Ziel ist keine schnelle Einzelmaßnahme, sondern eine Anwendung, die über Jahre wartbar, aktuell und verlässlich bleibt.',
            ],
            [
                'icon' => 'user',
                'title' => 'Direkter persönlicher Ansprechpartner',
                'text' => 'Sie sprechen direkt mit der Person, die an Ihrer Anwendung arbeitet – ohne Ticket-Weiterleitung über mehrere Ebenen.',
            ],
        ];
    }

    /**
     * Abgrenzung der Leistungsebenen.
     *
     * @return array<int, array{title: string, text: string}>
     */
    public static function responsibilityLevels(): array
    {
        return [
            [
                'title' => 'Wartung',
                'text' => 'Pflege der Software selbst: Updates von Framework und Abhängigkeiten, Sicherheitskorrekturen, Fehlerbehebung und technische Verbesserungen.',
            ],
            [
                'title' => 'Betriebsverantwortung',
                'text' => 'Zusätzliche Verantwortung für die Umgebung – etwa Server, Datenbank, Backups, Deployment und Monitoring –, soweit sie ausdrücklich vereinbart ist.',
            ],
            [
                'title' => 'Verfügbarkeitsleistungen',
                'text' => 'Reaktionszeiten, Erreichbarkeit außerhalb der Geschäftszeiten oder Bereitschaften gelten nur, wenn sie vertraglich festgelegt sind. Pauschale 24/7-Zusagen gebe ich nicht.',
            ],
        ];
    }

    /**
     * Die vier Leistungsbereiche, jeweils mit eigener Seite.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function services(): array
    {
        return [
            'softwarewartung' => [
                'label' => 'Application Maintenance',
                'title' => 'Professionelle Softwarewartung',
                'icon' => 'wrench',
                'card_text' => 'Regelmäßige technische Wartung, Prüfung von Abhängigkeiten, Updates und nachhaltige Stabilisierung.',
                'seo_title' => 'Softwarewartung für Unternehmen – Laravel- und PHP-Anwendungen | RuddatTech',
                'meta_description' => 'Professionelle Wartung bestehender Webanwendungen: Updates, Sicherheitskorrekturen, Abhängigkeiten und Stabilisierung für Laravel- und PHP-Software. Individuell vereinbart.',
                'headline' => 'Wartung, die Ihre Anwendung dauerhaft aktuell und sicher hält.',
                'intro' => 'Geschäftsanwendungen altern, auch wenn niemand an ihnen arbeitet: Frameworks erreichen ihr Supportende, Bibliotheken erhalten Sicherheitswarnungen, Server werden aktualisiert. Regelmäßige Wartung verhindert, dass aus kleinen Rückständen ein teures Risiko wird.',
                'situation' => [
                    'Die Anwendung läuft, wurde aber seit längerer Zeit nicht aktualisiert.',
                    'Framework- oder PHP-Version erhalten bald oder bereits keine Sicherheitsupdates mehr.',
                    'Es gibt niemanden, der Updates prüft, testet und kontrolliert einspielt.',
                    'Kleine Fehler sammeln sich an, weil keine feste Zuständigkeit besteht.',
                ],
                'scope' => [
                    ['title' => 'Prüfung von Abhängigkeiten', 'text' => 'Regelmäßige Kontrolle von Composer- und npm-Paketen auf Sicherheitsmeldungen und veraltete Versionen.'],
                    ['title' => 'Updates und Versionswechsel', 'text' => 'Planbare Aktualisierung von Framework, PHP-Version und Bibliotheken – getestet, bevor sie produktiv gehen.'],
                    ['title' => 'Sicherheitskorrekturen', 'text' => 'Zeitnahe Bewertung und Behebung bekannt gewordener Schwachstellen in Code und Abhängigkeiten.'],
                    ['title' => 'Fehlerbehebung', 'text' => 'Analyse und Korrektur gemeldeter Fehler im vereinbarten Kontingent oder nach Aufwand.'],
                    ['title' => 'Technische Pflege', 'text' => 'Aufräumen von Altlasten, Verbesserung von Logging und Fehlerbehandlung, Ergänzung von Tests an kritischen Stellen.'],
                    ['title' => 'Dokumentation', 'text' => 'Nachvollziehbare Aufzeichnung der durchgeführten Arbeiten und des technischen Zustands.'],
                ],
                'benefits' => [
                    'Feste Zuständigkeit statt Wartung „bei Gelegenheit“',
                    'Geringeres Risiko durch veraltete oder unsichere Komponenten',
                    'Planbare Updates statt hektischer Notfall-Migrationen',
                    'Transparenz über den technischen Zustand Ihrer Anwendung',
                ],
                'faq' => [
                    ['q' => 'Welche Anwendungen warten Sie?', 'a' => 'Der Schwerpunkt liegt auf individuellen Webanwendungen auf Basis von PHP und Laravel, die in Unternehmen produktiv genutzt werden. Ob eine Anwendung passt, klärt die technische Bestandsaufnahme.'],
                    ['q' => 'Gibt es feste Wartungspakete?', 'a' => 'Nein. Umfang und Rhythmus hängen vom Zustand und der Bedeutung der Anwendung ab und werden nach der Bestandsaufnahme individuell vereinbart.'],
                ],
            ],
            'managed-application-services' => [
                'label' => 'Managed Application Services',
                'title' => 'Managed Application Services',
                'icon' => 'server',
                'card_text' => 'Ganzheitliche technische Betreuung inklusive Server, Datenbanken, Backups, Deployment und Monitoring gemäß vereinbartem Leistungsumfang.',
                'seo_title' => 'Managed Application Services – Webanwendungen betreuen lassen | RuddatTech',
                'meta_description' => 'Technische Betreuung von Webanwendungen inklusive Server, Datenbank, Backups, Deployment und Monitoring – mit klar vereinbarten Zuständigkeiten statt Pauschalversprechen.',
                'headline' => 'Ein Ansprechpartner für Anwendung und Betrieb.',
                'intro' => 'Wenn Anwendung und Infrastruktur von unterschiedlichen Stellen betreut werden – oder von niemandem –, entstehen Lücken: Backups, die nie getestet wurden, Deployments, die nur eine Person beherrscht, Warnungen, die niemand liest. Managed Application Services bündeln diese Verantwortung im vereinbarten Umfang.',
                'situation' => [
                    'Server, Datenbank und Anwendung werden von verschiedenen Personen oder gar nicht betreut.',
                    'Es ist unklar, ob Backups vollständig sind und sich wiederherstellen lassen.',
                    'Deployments erfolgen manuell und sind schlecht dokumentiert.',
                    'Störungen fallen erst auf, wenn Kunden oder Mitarbeitende sie melden.',
                ],
                'scope' => [
                    ['title' => 'Server- und Umgebungsbetreuung', 'text' => 'Pflege der Anwendungsumgebung, z. B. PHP-Versionen, Webserver-Konfiguration, Queue-Worker und Cronjobs.'],
                    ['title' => 'Datenbankbetreuung', 'text' => 'Kontrolle von Migrationen, Datenwachstum und Abfrageperformance sowie Unterstützung bei Datenkorrekturen.'],
                    ['title' => 'Backup und Wiederherstellung', 'text' => 'Einrichtung oder Prüfung von Backups und – ebenso wichtig – Tests, ob eine Wiederherstellung tatsächlich funktioniert.'],
                    ['title' => 'Deployment', 'text' => 'Nachvollziehbare, möglichst automatisierte Auslieferung neuer Versionen mit Rückfallmöglichkeit.'],
                    ['title' => 'Monitoring', 'text' => 'Überwachung von Erreichbarkeit, Fehlerprotokollen und Hintergrundprozessen im vereinbarten Rahmen.'],
                    ['title' => 'Regelmäßige Berichte', 'text' => 'Kurze Zusammenfassungen über Zustand, durchgeführte Arbeiten und empfohlene Maßnahmen.'],
                ],
                'benefits' => [
                    'Klare Verantwortlichkeiten für Anwendung und Umgebung',
                    'Geprüfte Backups statt bloßer Annahme',
                    'Reproduzierbare Deployments',
                    'Frühere Erkennung von Problemen im vereinbarten Umfang',
                ],
                'faq' => [
                    ['q' => 'Übernehmen Sie auch das Hosting?', 'a' => 'Ich bin kein Hosting-Anbieter. Ich betreue Anwendungen auf Ihrer bestehenden Infrastruktur oder bei einem Hoster Ihrer Wahl und kann bei der Auswahl einer geeigneten Umgebung beraten.'],
                    ['q' => 'Ist eine Erreichbarkeit rund um die Uhr enthalten?', 'a' => 'Nein, nicht pauschal. Reaktionszeiten und Erreichbarkeit werden – falls benötigt – individuell und realistisch vertraglich vereinbart.'],
                ],
            ],
            'uebernahme-bestehender-software' => [
                'label' => 'Application Takeover',
                'title' => 'Technische Übernahme bestehender Anwendungen',
                'icon' => 'handover',
                'card_text' => 'Strukturierte Übernahme bestehender Software, auch wenn der ursprüngliche Entwickler oder Dienstleister nicht mehr verfügbar ist.',
                'seo_title' => 'Software technisch übernehmen lassen – bestehende Webanwendungen | RuddatTech',
                'meta_description' => 'Ihr Entwickler oder Dienstleister ist nicht mehr verfügbar? Strukturierte technische Übernahme bestehender PHP- und Laravel-Anwendungen mit Bestandsaufnahme und Risikoanalyse.',
                'headline' => 'Ihre Software ist verwaist? Ich übernehme strukturiert.',
                'intro' => 'Der ursprüngliche Entwickler ist nicht mehr erreichbar, die Agentur hat sich zurückgezogen oder das Wissen liegt bei einer einzigen Person. Eine geordnete Übernahme sorgt dafür, dass Ihre Anwendung wieder eine klare technische Zuständigkeit bekommt – ohne riskante Schnellschüsse.',
                'situation' => [
                    'Der bisherige Entwickler oder Dienstleister steht nicht mehr zur Verfügung.',
                    'Es gibt kaum Dokumentation, und niemand kennt alle Zugänge.',
                    'Die Anwendung basiert auf veralteten PHP- oder Framework-Versionen.',
                    'Änderungen werden vermieden, weil niemand die Folgen abschätzen kann.',
                ],
                'scope' => [
                    ['title' => 'Sicherung von Zugängen und Quellcode', 'text' => 'Zusammentragen von Repositorys, Server-, Datenbank- und Dienstzugängen und Überführung in eine geordnete Verwaltung.'],
                    ['title' => 'Technische Bestandsaufnahme', 'text' => 'Analyse von Architektur, Datenmodell, Abhängigkeiten, Schnittstellen und Betriebsumgebung.'],
                    ['title' => 'Sicherheits- und Kompatibilitätsaudit', 'text' => 'Prüfung auf Schwachstellen, offen liegende Zugangsdaten und Kompatibilität mit aktuellen PHP-Versionen.'],
                    ['title' => 'Aufbau einer Entwicklungsumgebung', 'text' => 'Lokale bzw. getrennte Umgebung, in der Änderungen gefahrlos entwickelt und getestet werden können.'],
                    ['title' => 'Schrittweise Modernisierung', 'text' => 'Planbare Migration auf unterstützte Versionen – in kleinen, prüfbaren Schritten, während das Produktivsystem weiterläuft.'],
                    ['title' => 'Dokumentation', 'text' => 'Festhalten von Architektur, Betriebsabläufen und Besonderheiten, damit Wissen nicht erneut an einer Person hängt.'],
                ],
                'benefits' => [
                    'Wiederhergestellte technische Handlungsfähigkeit',
                    'Realistische Einschätzung von Zustand und Risiken',
                    'Modernisierung ohne Unterbrechung des laufenden Betriebs',
                    'Dokumentiertes Wissen statt Abhängigkeit von Einzelpersonen',
                ],
                'faq' => [
                    ['q' => 'Übernehmen Sie auch ältere PHP-Anwendungen ohne Framework?', 'a' => 'Ja, sofern eine Übernahme technisch und wirtschaftlich sinnvoll ist. Das Ergebnis der Bestandsaufnahme zeigt, ob eine Modernisierung, eine schrittweise Ablösung oder ein anderer Weg empfehlenswert ist.'],
                    ['q' => 'Was passiert, wenn kein Quellcode mehr vorliegt?', 'a' => 'Dann prüfen wir zunächst gemeinsam, welche Bestandteile auf dem Server oder in Backups noch vorhanden sind. Eine verlässliche Aussage ist erst nach dieser Sichtung möglich.'],
                ],
            ],
            'fehleranalyse-stabilisierung' => [
                'label' => 'Incident & Recovery',
                'title' => 'Fehleranalyse und Stabilisierung',
                'icon' => 'lifebuoy',
                'card_text' => 'Technische Fehleranalyse, Störungsbehebung und Wiederherstellung geschäftlicher Anwendungen.',
                'seo_title' => 'Fehleranalyse und Stabilisierung von Webanwendungen | RuddatTech',
                'meta_description' => 'Ihre Webanwendung fällt aus, wird langsam oder verarbeitet Daten fehlerhaft? Strukturierte Fehleranalyse, Störungsbehebung und nachhaltige Stabilisierung für PHP- und Laravel-Software.',
                'headline' => 'Wenn Ihre Anwendung nicht mehr zuverlässig funktioniert.',
                'intro' => 'Ausfälle, unerklärliche Fehler, hängende Hintergrundprozesse oder plötzlich langsame Seiten – im Störungsfall zählt eine ruhige, systematische Analyse. Ziel ist nicht nur die schnelle Behebung, sondern dass derselbe Fehler nicht wiederkehrt.',
                'situation' => [
                    'Die Anwendung ist nicht erreichbar oder zeigt Fehlerseiten.',
                    'E-Mails, Zahlungen oder Hintergrundjobs werden nicht mehr verarbeitet.',
                    'Die Anwendung ist nach einem Update oder Serverwechsel instabil.',
                    'Fehler treten sporadisch auf und lassen sich nicht nachvollziehen.',
                ],
                'scope' => [
                    ['title' => 'Strukturierte Fehleranalyse', 'text' => 'Auswertung von Logs, Fehlermeldungen, Datenbankzustand und Serverumgebung, um die Ursache einzugrenzen.'],
                    ['title' => 'Störungsbehebung', 'text' => 'Abgestimmte Korrektur – bei Bedarf zunächst als Übergangslösung, um den Betrieb wiederherzustellen.'],
                    ['title' => 'Wiederherstellung', 'text' => 'Unterstützung bei der Wiederherstellung aus Backups und bei der Prüfung betroffener Daten.'],
                    ['title' => 'Ursachenbeseitigung', 'text' => 'Nachhaltige Lösung des eigentlichen Problems statt wiederholter Symptombehandlung.'],
                    ['title' => 'Stabilisierung', 'text' => 'Verbesserung von Fehlerbehandlung, Logging und Überwachung, damit Probleme künftig früher sichtbar werden.'],
                    ['title' => 'Nachbericht', 'text' => 'Kurze Dokumentation: was passiert ist, was getan wurde und welche Maßnahmen empfohlen werden.'],
                ],
                'benefits' => [
                    'Systematisches Vorgehen statt Ausprobieren',
                    'Wiederherstellung des Betriebs mit Blick auf die Datenintegrität',
                    'Behebung der Ursache, nicht nur des Symptoms',
                    'Dokumentierte Erkenntnisse für den künftigen Betrieb',
                ],
                'faq' => [
                    ['q' => 'Helfen Sie auch kurzfristig ohne bestehenden Vertrag?', 'a' => 'Kurzfristige Unterstützung ist nach Verfügbarkeit möglich. Eine garantierte Reaktionszeit besteht nur im Rahmen eines vereinbarten Betreuungsvertrags.'],
                    ['q' => 'Was benötigen Sie im Störungsfall?', 'a' => 'Eine kurze Beschreibung der Auswirkungen, seit wann das Problem besteht, ob kürzlich Änderungen erfolgt sind, sowie – nach Absprache – Zugang zu Logs, Server und Quellcode.'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function service(string $slug): ?array
    {
        $service = static::services()[$slug] ?? null;

        return $service ? ['slug' => $slug] + $service : null;
    }
}
