<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
      xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

        @php $count=0; @endphp
        @foreach ($opinions as $opinion)
        @php
        $beforeslug = '@'.$opinions[$count]->user->username.'/opinion';
        $username = '@'.$opinions[$count]->user->username;
        $user = $opinions[$count]->user->name;
        $count= ++$count;
        @endphp
            <url>
                <loc>{{ secure_url($beforeslug,['slug'=>$opinion->uuid]) }}</loc>
                @if($opinion->cover!=null && $opinion->cover_type!="EMBED" && $opinion->cover_type!="YOUTUBE")
                    @if($opinion->cover_type=="VIDEO")
                        <video:video>
                           <video:thumbnail_loc>{{ $opinion->thumbnail }}</video:thumbnail_loc>
                           <video:title>{{ 'Opinion on '.str_replace("#"," ",strip_tags($opinion->hash_tags)).' | '.substr($opinion->plain_body,0,30) }}</video:title>
                           <video:description>{{ substr($opinion->plain_body,0,2040) }}</video:description>
                           <video:content_loc>
                               {{ $opinion->cover }}</video:content_loc>
                           <video:publication_date>{{ $opinion->created_at->tz('UTC')->toAtomString() }}</video:publication_date>
                           <video:family_friendly>yes</video:family_friendly>
                           <video:uploader
                              info="{{ secure_url($username)}}">{{ $user.' - Opined'}}
                           </video:uploader>
                        </video:video>
                        @else
                            <image:image>
                            <image:loc>{{ $opinion->cover }}</image:loc>
                            </image:image>
                        @endif
                @endif
                <lastmod>{{ $opinion->updated_at->tz('UTC')->toAtomString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>1</priority>
               
            </url>
        @endforeach
</urlset>
