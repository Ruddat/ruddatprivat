<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>'; ?>'; ?>'; ?>'; ?>'; ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {{-- Startseite --}}
    <url>
        <loc>{{ url("/") }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Landing Pages --}}
    @foreach ($pages as $page)
        <url>
            <loc>{{ url($page->slug) }}</loc>
            <lastmod>{{ optional($page->updated_at)->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
