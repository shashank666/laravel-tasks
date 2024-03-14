
{!! '<'.'?'.'xml version="1.0" encoding="UTF-8" ?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>{{ $site['name'] }}</title>
    <link>{{ $site['url'] }}</link>
    <description><![CDATA[{{ $site['description'] }}]]></description>
    <atom:link href="{{ $site['url'] }}" rel="self" type="application/rss+xml" />
    <language>{{ $site['language'] }}</language>
    <lastBuildDate>{{date("D, d M Y H:i:s T", strtotime($posts[0]['created_at']))}}</lastBuildDate>
    @foreach($posts as $post)
    <item>
      <title><![CDATA[{!! $post->title !!}]]></title>
      <link>{{ secure_url('opinion',['slug'=>$post->slug]) }}</link>
      <guid isPermaLink="true">{{ secure_url('opinion',['slug'=>$post->slug]) }}</guid>
      <description><![CDATA[{!!'<p>'. str::limit($post->plainbody,120).'</p><p>The post <a rel="nofollow" href="'.secure_url('opinion',['slug'=>$post->slug]).'">'.$post->title.'</a> appeared first on <a rel="nofollow" href="https://weopined.com">Opined</a></p>'!!}]]></description>
      <content:encoded><![CDATA[{!! $post->body !!}]]></content:encoded>
      <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/">Opined</dc:creator>
      <category><![CDATA[Opinion]]></category>
      <pubDate>{{date("D, d M Y H:i:s T", strtotime($post->created_at))}}</pubDate>
    </item>
    @endforeach
  </channel>
</rss>