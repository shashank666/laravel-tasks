<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

        @foreach ($threads as $thread)
            <url>
                <loc>{{ secure_url('thread',['name'=>$thread->name]) }}</loc>
                <lastmod>{{ $thread->updated_at->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>daily</changefreq>
                <priority>1</priority>
            </url>
        @endforeach
</urlset>
