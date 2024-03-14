<!DOCTYPE html>
<html
    xmlns="https://www.w3.org/1999/xhtml"
    xml:lang="en" lang="{{ app()->getLocale()}}" dir="ltr"
    xmlns:og="https://ogp.me/ns#"
    xmlns:fb="https://www.facebook.com/2008/fbml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv = "Content-Type" content = "text/html;charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="https://www.weopined.com/favicon.png">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')"/>
    <meta name="keywords" content="@yield('keywords')"/>
    <meta name="copyright" content="Copyright &copy; {{ Carbon\Carbon::now()->format('Y') }} www.weopined.com , All Rights Reserved"/>
    <meta name = "revised" content = "Opined, {{ Carbon\Carbon::now('Asia/Kolkata') }}" />
    <meta name="robots" content="index,follow"/>
    <meta name="generator" content="Opined" />

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="opined">
    <meta name="p:domain_verify" content="ad94e576a7bb1d00fd9e1d5d28209d46"/>
    


    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style"  content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="opined">
    <link rel="apple-touch-icon" href="https://www.weopined.com/favicon.png">
    <link rel="apple-touch-startup-image" href="https://www.weopined.com/favicon.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="https://www.weopined.com/favicon.png">
    <meta name="msapplication-TileColor" content="#fff">
    <meta name="theme-color" content="#fff">
    <meta name="msapplication-navbutton-color" content="#fff">

    @stack('meta')

    <!--<link rel='dns-prefetch' href='//gravatar.com' />-->

    <link href="https://www.weopined.com/vendor/bootstrap/css/bootstrap.min.css?<?php echo time();?>" rel="stylesheet" type="text/css" >
    {{-- <link href="https://www.weopined.com/css/custom/main.min.css?<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="https://www.weopined.com/vendor/videojs/video-js.css" rel="stylesheet" type="text/css"/>

 --}}


    @stack('styles')
   {{--   <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-117679931-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-117679931-1');
    </script>  --}}
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KC9KRMP');
    </script>
    <!-- End Google Tag Manager -->
    <!-------------------- Schema Informations ------------------------->
    <script type="application/ld+json" data-schema="Organization">
    {
        "@context": "http://schema.org",
        "@type": "Organization",
          "name": "Opined - Where Every Opinion Matters!",
          "description": "Opined is a meaningful social media network that connects people with the same interest to explore and discuss individual opinions that can help users to gain relevant and holistic information.",
          "url": "https://www.weopined.com/",
          "potentialAction": {
          "@type": "SearchAction",
          "target": "https://www.weopined.com/search?q={q}",
          "query-input": "required name=q"
          },
        "logo": "https://www.weopined.com/img/logo.png",
        "founder": {
          "@type": "Person",
          "name": "Vipul Gajera"
        },
        "email": "reach-us@weopined.com",
        "address": {
          "@type": "PostalAddress",
          "addressLocality": "Mumbai",
          "addressRegion": "India"
        },
        "sameAs": [
          "https://www.facebook.com/weopined",
          "https://www.twitter.com/weopined",
          "https://www.linkedin.com/company/opined",
        "https://www.instagram.com/weopined",
        "https://www.youtube.com/channel/UCWSXlJxAHRpH8pCiah5dYmQ"
        ]
      }
    </script>
    <script type='application/ld+json' data-schema="Organisation">
        {
        "@context":"https://schema.org",
        "@graph":[{"@type":["Organization","Place"],
        "@id":"https://weopined.com/#organization",
        "name":"Opined Online Business Services Private Limited","url":"https://www.weopined.com/",
        "sameAs":["https://www.facebook.com/weopined/",
                  "https://twitter.com/weopined",
                  "https://www.linkedin.com/company/opined",
                  "https://www.instagram.com/weopined",
                  "https://www.youtube.com/channel/UCWSXlJxAHRpH8pCiah5dYmQ"],
        "logo":{"@type":"ImageObject","@id":"https://www.weopined.com/#logo",
        "url":"https://www.weopined.com/img/logo.png",
        "width":1170,
        "height":433,
        "caption":"Opined Online Business Services Private Limited"},
        "image":{"@id":"https://www.weopined.com/#logo"},
        "location":{"@id":"https://www.weopined.com/#local-place"}},
        {"@type":"WebSite","@id":"https://www.weopined.com/#website",
            "url":"https://www.weopined.com/",
            "name":"Opined Online Business Services Private Limited",
            "publisher":{"@id":"https://www.weopined.com/#organization"},
            "potentialAction":{
                  "@type":"SearchAction",
                  "target":"https://www.weopined.com/search??s={search_term_string}",
                  "query-input":"required name=search_term_string"}},
        {"@type":"WebPage","@id":"https://www.weopined.com/#webpage",
          "url":"https://www.weopined.com/",
          "inLanguage":"en-US",
          "name":"Opined - Where Every Opinion Matters!",
          "isPartOf":{"@id":"https://www.weopined.com/#website"},
          "about":{"@id":"https://www.weopined.com/#organization"},
          "description":"Opined is a meaningful social media network that connects people with the same interest to explore and discuss individual opinions that can help users to gain relevant and holistic information."},
        {"@type":"Place","@id":"https://www.weopined.com/#local-place",
            
            "openingHoursSpecification":[{"@type":"OpeningHoursSpecification","dayOfWeek":"Monday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Tuesday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Wednesday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Thursday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Friday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Saturday","opens":"00:00","closes":"23:59"},{"@type":"OpeningHoursSpecification","dayOfWeek":"Sunday","opens":"00:00","closes":"23:59"}]}]}
      </script>
      <script type="application/ld+json">
        {"@context": "http://schema.org",
          "@type": "BreadcrumbList","itemListElement": 
        [{"@type": "ListItem","position": 1,"item": 
          {"@id": "https://www.weopined.com/","name": "Home"}}]}
      </script>
      <script type="application/ld+json">
        {"@context":"http://schema.org",
          "@type":"SiteNavigationElement",
            "name":[ "#youropinionmatterstoo", "#india", "#FightAgainstRape", "#womenrights", "#myopinion", "#MakeAnImpact", "#Economy", "#Children", "#health"],
            "url":[  "https://www.weopined.com/thread/youropinionmatterstoo", "https://www.weopined.com/thread/india/",  "https://www.weopined.com/thread/FightAgainstRape",  "https://www.weopined.com/thread/womenrights",  "https://www.weopined.com/thread/myopinion",  "https://www.weopined.com/thread/MakeAnImpact",  "https://www.weopined.com/thread/Economy/",  "https://www.weopined.com/thread/Children",  "https://www.weopined.com/thread/health"]}
      </script>
  <!------------------- End of Schema Informations ----------------->
    {{-- <script type="text/javascript" src="/js/web/core.min.js"></script>
    <!--<script type="text/javascript" src="/js/custom/main.js?<?php echo time();?>"></script>-->
	<script type="text/javascript" src="/js/custom/main.min.js?<?php echo time();?>"></script>
    <script type="text/javascript" src="/js/custom/share.js"></script>
    <script defer type="text/javascript" src='/vendor/videojs/video.min.js'></script> --}}
    {{-- <script src="/js/yall.min.js"></script> --}}
    <script>
      document.addEventListener("DOMContentLoaded", yall);
    </script>
   
</head>

<body style="background: #ffffff;">
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KC9KRMP" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    {{--  @include('frontend.partials.header')  --}}
       {{-- OFFER MODAL
         @include('frontend.partials.offer_modal')
     --}}


    

     @yield('content')
    </main>


</body>
</html>
