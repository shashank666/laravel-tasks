<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
            <loc>https://www.weopined.com/sitemap/posts</loc>
            <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://www.weopined.com/sitemap/categories</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://www.weopined.com/sitemap/threads</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://www.weopined.com/sitemap/opinions</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://www.weopined.com/sitemap/polls</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
    <sitemap>
        <loc>https://www.weopined.com/sitemap/news</loc>
        <lastmod>{{ \Carbon\Carbon::now()->toAtomString()  }}</lastmod>
    </sitemap>
</sitemapindex>
