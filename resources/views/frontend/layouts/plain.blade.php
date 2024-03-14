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

    



    @stack('styles')
   
    @stack('scripts')
</head>

<body style="padding-top:4rem;background: #fff;">
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KC9KRMP" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>

     
     @if(Request::path()=='offer')
     <main role="main" class="pt-4">
     @else
     <main role="main" class="container" style="min-height:100vh;">
     @endif

    


     @yield('content')
    </main>


</body>
</html>
