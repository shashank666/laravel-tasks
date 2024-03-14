<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

        @foreach ($posts as $post)
            <url>
                <loc>{{ secure_url('opinion',['slug'=>$post->slug]) }}</loc>
                @if($post->coverimage!=null)
                <image:image>
                <image:loc>{{ $post->coverimage }}</image:loc>
                </image:image>
                @endif
                <lastmod>{{ $post->updated_at->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>1</priority>
            </url>
        @endforeach
</urlset>
