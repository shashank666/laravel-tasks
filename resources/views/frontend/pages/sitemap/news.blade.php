<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"       
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
  @foreach ($news as $post)
  <url>
    <loc>{{$post->slug}}</loc>
    <news:news>
    <news:publication>
      <news:name>{{$post->title}}</news:name>
      <news:language>en</news:language>
    </news:publication>
    <news:publication_date>{{ $post->created_at}}</news:publication_date>
      <news:title>{{$post->title}}</news:title>
    </news:news>
  </url>
  @endforeach
</urlset>