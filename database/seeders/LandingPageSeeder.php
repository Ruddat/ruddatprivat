<?php

namespace Database\Seeders;

use App\Models\LandingPage;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'webentwicklung-peine',
                'title' => 'Webentwicklung in Peine – RuddatTech für moderne Websites',
                'meta_description' => 'Professionelle Webentwicklung in Peine ✅ RuddatTech entwickelt individuelle Webseiten, Online-Shops und digitale Lösungen.',
                'h1' => 'Webentwicklung in Peine – moderne Websites für Unternehmen',
                'content' => '<p>RuddatTech bietet in Peine maßgeschneiderte Webentwicklungslösungen. Wir entwickeln moderne, sichere und performante Webseiten, die Ihre Kunden begeistern. Egal ob Unternehmensseite, Online-Shop oder Landingpage – wir setzen Ihr Projekt professionell um.</p><p>Unsere Expertise umfasst Laravel, Livewire, TailwindCSS und moderne CMS-Integration. Zudem kümmern wir uns um Suchmaschinenoptimierung (SEO) und Conversion-Optimierung.</p>',
                'features' => ['Modernste Technik', 'SEO-optimiert', 'Responsive Design', 'Schnelle Ladezeiten', 'Individuelle Beratung'],
                'faq' => [
                    ['q' => 'Bieten Sie auch Shops an?', 'a' => 'Ja, wir entwickeln E-Commerce Lösungen mit modernen Frameworks.'],
                    ['q' => 'Sind Ihre Webseiten DSGVO-konform?', 'a' => 'Alle unsere Lösungen werden nach europäischen Datenschutzrichtlinien umgesetzt.'],
                    ['q' => 'Wie lange dauert die Erstellung einer Webseite?', 'a' => 'In der Regel 4–6 Wochen, abhängig vom Umfang.'],
                ],
                'published' => true,
            ],
            [
                'slug' => 'seo-optimierung-hannover',
                'title' => 'SEO Optimierung in Hannover – Sichtbarkeit mit RuddatTech steigern',
                'meta_description' => 'RuddatTech ist Ihr SEO-Partner in Hannover ✅ Wir bringen Ihre Website mit modernen Methoden auf die vorderen Plätze bei Google.',
                'h1' => 'SEO Optimierung in Hannover – mehr Reichweite für Ihr Business',
                'content' => '<p>Mit einer professionellen Suchmaschinenoptimierung (SEO) machen wir Ihr Unternehmen in Hannover sichtbar. Wir analysieren Ihre Webseite, verbessern die Ladezeiten und optimieren Ihre Inhalte für Top-Rankings bei Google.</p><p>SEO ist ein kontinuierlicher Prozess: Wir begleiten Sie langfristig und sorgen für nachhaltige Ergebnisse.</p>',
                'features' => ['Keyword-Analyse', 'OnPage & OffPage SEO', 'Technische Optimierung', 'Lokale SEO-Strategien', 'Monitoring & Reporting'],
                'faq' => [
                    ['q' => 'Wie lange dauert es, bis SEO wirkt?', 'a' => 'Erste Ergebnisse sind meist nach 2–3 Monaten sichtbar.'],
                    ['q' => 'Bieten Sie lokale SEO an?', 'a' => 'Ja, wir optimieren Ihre Sichtbarkeit gezielt für Hannover und Umgebung.'],
                ],
                'published' => true,
            ],
            [
                'slug' => 'webdesign-braunschweig',
                'title' => 'Webdesign in Braunschweig – Kreative Designs von RuddatTech',
                'meta_description' => 'Professionelles Webdesign in Braunschweig ✅ Benutzerfreundliche und moderne Designs für Unternehmen und Startups.',
                'h1' => 'Webdesign in Braunschweig – modern, kreativ & nutzerfreundlich',
                'content' => '<p>Ein gutes Webdesign sorgt nicht nur für einen ansprechenden Look, sondern auch für eine bessere Nutzererfahrung. In Braunschweig gestalten wir Webseiten, die sowohl optisch überzeugen als auch funktional durchdacht sind.</p><p>Wir legen Wert auf CI-gerechtes Design, Barrierefreiheit und intuitive Benutzerführung.</p>',
                'features' => ['Kreative Gestaltung', 'Benutzerfreundlich', 'Barrierefrei', 'Mobile First', 'Branding-orientiert'],
                'faq' => [
                    ['q' => 'Können Sie bestehende Webseiten redesignen?', 'a' => 'Ja, wir modernisieren auch bestehende Webseiten.'],
                    ['q' => 'Arbeiten Sie auch mit Startups?', 'a' => 'Natürlich – wir haben Erfahrung mit Startups und kleinen Unternehmen.'],
                ],
                'published' => true,
            ],
            [
                'slug' => 'it-service-wolfsburg',
                'title' => 'IT-Service in Wolfsburg – RuddatTech für Unternehmen',
                'meta_description' => 'IT-Service in Wolfsburg ✅ RuddatTech unterstützt Unternehmen bei IT-Infrastruktur, Support und Sicherheit.',
                'h1' => 'IT-Service in Wolfsburg – Ihre IT in sicheren Händen',
                'content' => '<p>Wir bieten umfassenden IT-Service in Wolfsburg: Von der Einrichtung Ihrer IT-Infrastruktur über die Wartung bis hin zum Support. Unsere Experten sorgen für eine reibungslose und sichere IT-Umgebung.</p><p>Dank moderner Cloud-Lösungen und Cybersecurity-Konzepten bleiben Sie jederzeit geschützt und flexibel.</p>',
                'features' => ['24/7 Support', 'IT-Sicherheit', 'Cloud-Lösungen', 'Netzwerktechnik', 'Datensicherung'],
                'faq' => [
                    ['q' => 'Bieten Sie Vor-Ort-Service an?', 'a' => 'Ja, wir betreuen Unternehmen direkt in Wolfsburg.'],
                    ['q' => 'Unterstützen Sie auch bei Cloud-Migration?', 'a' => 'Ja, wir begleiten Sie von der Planung bis zur Umsetzung.'],
                ],
                'published' => true,
            ],
            [
                'slug' => 'digitalisierung-gifhorn',
                'title' => 'Digitalisierung in Gifhorn – Prozesse optimieren mit RuddatTech',
                'meta_description' => 'RuddatTech unterstützt Unternehmen in Gifhorn ✅ bei der digitalen Transformation und Automatisierung.',
                'h1' => 'Digitalisierung in Gifhorn – Effiziente Prozesse für Ihr Unternehmen',
                'content' => '<p>Die digitale Transformation verändert die Wirtschaft. Wir helfen Unternehmen in Gifhorn, Prozesse zu automatisieren und digitale Tools effizient einzusetzen.</p><p>Von der Analyse Ihrer Workflows über die Auswahl der passenden Software bis zur Implementierung begleiten wir Sie auf dem Weg zur Digitalisierung.</p>',
                'features' => ['Prozessanalyse', 'Automatisierung', 'Cloud-Integration', 'Schulung & Beratung', 'Individuelle Software'],
                'faq' => [
                    ['q' => 'Welche Branchen betreuen Sie?', 'a' => 'Wir arbeiten branchenübergreifend – von Handwerk über Handel bis Industrie.'],
                    ['q' => 'Geben Sie auch Mitarbeiterschulungen?', 'a' => 'Ja, wir schulen Ihr Team für die Nutzung neuer Systeme.'],
                ],
                'published' => true,
            ],
        ];

        foreach ($pages as $page) {
            LandingPage::create($page);
        }
    }
}
