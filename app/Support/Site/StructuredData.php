<?php

namespace App\Support\Site;

/**
 * Schema.org-Daten (JSON-LD) der öffentlichen Webseite.
 *
 * Organisation, Webseite und Person stehen einmal im globalen Graphen und
 * werden von Einzelseiten per @id referenziert – so entstehen keine
 * widersprüchlichen Duplikate für Suchmaschinen und KI-Systeme.
 */
class StructuredData
{
    public static function organizationId(): string
    {
        return route('home') . '#organization';
    }

    public static function websiteId(): string
    {
        return route('home') . '#website';
    }

    public static function founderId(): string
    {
        return route('about') . '#ingo-ruddat';
    }

    public static function siteGraph(): array
    {
        $legal = config('ruddattech.legal');

        $organization = array_filter([
            '@type' => 'ProfessionalService',
            '@id' => self::organizationId(),
            'name' => 'RuddatTech',
            'alternateName' => 'RuddatTech – Softwarewartung & Managed Application Services',
            'slogan' => 'Komplexe Software. Zuverlässig betreut.',
            'description' => 'Wartung, Stabilisierung, technische Übernahme und langfristige Betreuung individueller Geschäftsanwendungen auf Basis von PHP und Laravel.',
            'url' => route('home'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('brand/icon-512.png'),
                'width' => 512,
                'height' => 512,
            ],
            'image' => asset('brand/og-image.png'),
            'email' => $legal['email'],
            'telephone' => $legal['phone'] ?: null,
            'address' => ($legal['street'] && $legal['zip_city']) ? self::address($legal) : null,
            'founder' => ['@id' => self::founderId()],
            'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland'],
            'knowsAbout' => [
                'Softwarewartung', 'Application Maintenance', 'Managed Application Services',
                'Technische Übernahme bestehender Software', 'Fehleranalyse', 'Laravel', 'PHP',
                'MySQL', 'Queue-Verarbeitung', 'PHP-8-Migration', 'Linux-Server',
            ],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Leistungen',
                'itemListElement' => collect(SiteContent::services())->map(fn ($s, $slug) => [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $s['title'],
                        'url' => route('leistungen.show', $slug),
                    ],
                ])->values()->all(),
            ],
        ]);

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $organization,
                [
                    '@type' => 'WebSite',
                    '@id' => self::websiteId(),
                    'url' => route('home'),
                    'name' => 'RuddatTech',
                    'inLanguage' => 'de-DE',
                    'publisher' => ['@id' => self::organizationId()],
                ],
                [
                    '@type' => 'Person',
                    '@id' => self::founderId(),
                    'name' => $legal['owner'],
                    'jobTitle' => 'Inhaber, Softwareentwickler',
                    'worksFor' => ['@id' => self::organizationId()],
                    'url' => route('about'),
                    'knowsAbout' => ['Laravel', 'PHP', 'Softwarewartung', 'Softwarearchitektur'],
                ],
            ],
        ];
    }

    /** Breadcrumb-Liste aus [Name => URL]. */
    public static function breadcrumbs(array $items): array
    {
        $position = 0;

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->map(fn ($url, $name) => [
                '@type' => 'ListItem',
                'position' => ++$position,
                'name' => $name,
                'item' => $url,
            ])->values()->all(),
        ];
    }

    private static function address(array $legal): array
    {
        $zipCity = trim((string) $legal['zip_city']);
        [$zip, $city] = preg_match('/^(\d{5})\s+(.+)$/', $zipCity, $m) ? [$m[1], $m[2]] : [null, $zipCity];

        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $legal['street'],
            'postalCode' => $zip,
            'addressLocality' => $city,
            'addressCountry' => 'DE',
        ]);
    }
}
