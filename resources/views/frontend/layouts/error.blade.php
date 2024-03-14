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
    <meta name="robots" content="noindex"/>
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

    <link rel='dns-prefetch' href='//gravatar.com' />

    <link href="https://www.weopined.com/vendor/bootstrap/css/bootstrap.min.css?<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="https://www.weopined.com/css/custom/main.min.css?<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="/css/custom/main.css?<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="https://www.weopined.com/vendor/videojs/video-js.css" rel="stylesheet" type="text/css"/>




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

    <script type="application/ld+json" data-schema="Organization">
    {
  "@context": "http://schema.org",
  "@type": "Organization",
  "name": "Opined - Where Every Opinion Matters!",
    "description": "Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions on top trending topics and stories. Every day, thousands of people share, read, discuss and write their opinion on Opined.",
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
    "https://plus.google.com/112201696846039745199",
    "https://www.linkedin.com/company/opined"
  ]
}
    </script>
    <script type="text/javascript" src="https://www.weopined.com/js/web/core.min.js"></script>
    <script type="text/javascript" src="https://www.weopined.com/js/custom/main.js?<?php echo time();?>"></script>
    <script defer type="text/javascript" src='https://www.weopined.com/vendor/videojs/video.min.js'></script>
    <script src="/js/yall.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", yall);
    </script>
    @if(Auth::check() && $company_ui_settings->webpush_notification==1)
        <script src="/js/enable-push.js?<?php echo time()?>" type="text/javascript"></script>
    @endif

    <script>
        const au="{{ Auth::check()?1:0 }}";
        const wpe="{{ $company_ui_settings->webpush_notification }}";

        const cev="{{ $company_ui_settings->check_email_verified}}";
        const cmv="{{ $company_ui_settings->check_mobile_verified}}";
        const ev="{{ Auth::check()?Auth::user()->email_verified:0 }}";
        const mv="{{ Auth::check()?Auth::user()->mobile_verified:0 }}";


        $(document).on('click','#menu-phone-codes .dropdown-item', function(){
            $('.phonecode_label').text($(this).attr('data-value'));
            $('input[name="phone_code"]').val($(this).attr('data-value'));
            $(".phonecode_label").dropdown('toggle');
        });

        $(document).on('change','input[name="country_phonecode"]',function(){
            $('input[name="phone_code"]').val($(this).val());
            $('.phonecode_label').text($(this).val());
            $('#phonecodeModal').modal('hide');
        });


        function filter(){
            var input, filter, ul, li, a, i, txtValue;
            filter = $("#searchPhoneCode").val().toUpperCase();
            let length=$('#menu-phone-codes').children('div').length;
            let hidden = 0
            $('#menu-phone-codes').children('div').each(function () {
                if (this.innerText.toUpperCase().indexOf(filter) > -1) {
                    $(this).css('display',"inline");
                } else {
                    $(this).css('display',"none");
                    hidden++;
                }
            });
            if (hidden === length) {$('#empty').show()}
            else {$('#empty').hide()}
        }

        function showNotificationModal(){
            $('#notificationsModal').modal('show');
            localStorage.setItem("notification_modal",new Date().getTime())
        }

        if(au==0){
            if(localStorage.getItem("login_modal") === null){
                remindToLogin();
            }else{
                let threshold=Number(localStorage.getItem("login_modal")) + 20*60*1000;
                if(new Date().getTime()>=threshold){
                    remindToLogin();
                }
            }
        }

        function remindToLogin(){
            setTimeout(function(){
                openLoginModal();
                localStorage.setItem("login_modal",new Date().getTime())
            },20000);
        }

    </script>
    @if(Request::path()!='/')
    <script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
        $('[data-toggle="tooltip"]').click(function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
        });

        $(window).scroll(function(){
            if ($(this).scrollTop() > 100) {
                $('.scrollup').fadeIn();
            } else {
                $('.scrollup').fadeOut();
            }
        });

        $('.scrollup').click(function(){
            $("html, body").animate({ scrollTop: 0 }, 600);
            return false;
        });

        var countries = @json($countries);

        if(localStorage.getItem("loc") === null){
         $.ajax({
             url:'https://geoip-db.com/json/',
             type:'GET',
             dataType:'json',
             success:function(response){
                 localStorage.setItem('loc',JSON.stringify(response));
                 let selectedCountry=countries.find(country => country.code === response.country_code);
                 $('.phonecode_label').text(selectedCountry.phone_code);
                 $('input[name="phone_code"]').val(selectedCountry.phone_code);
                 $('#'+selectedCountry.code).attr('checked',true);

             },
             error:function(err){}
         });
         }else{
             let loc=JSON.parse(localStorage.getItem('loc'));
             let selectedCountry=countries.find(country => country.code === loc.country_code);
             $('.phonecode_label').text(selectedCountry.phone_code);
             $('input[name="phone_code"]').val(selectedCountry.phone_code);
             $('#'+selectedCountry.code).attr('checked',true);
         }

         if(wpe==1 && au==1){
            if(Notification.permission!=="granted"){
                if(localStorage.getItem("notification_modal")===null){
                    showNotificationModal();
                }else{
                    let threshold=Number(localStorage.getItem("notification_modal")) + 24*60*60*1000;
                    if(new Date().getTime()>=threshold){
                        showNotificationModal();
                    }
                }
            }
        }


    });
    </script>
    @endif

    @if($company_ui_settings->adblocker==1)
    <script>
           function adBlockNotDetected() {}
           function adBlockDetected() {$('#adblockDetecterModel').modal('show');}
           if(typeof fuckAdBlock !== 'undefined' || typeof FuckAdBlock !== 'undefined') {adBlockDetected();} else {
               var importFAB = document.createElement('script');
               importFAB.onload = function() {
                   fuckAdBlock.onDetected(adBlockDetected)
                   fuckAdBlock.onNotDetected(adBlockNotDetected);
               };
               importFAB.onerror = function() {
                   adBlockDetected();
               };
               importFAB.integrity = 'sha256-xjwKUY/NgkPjZZBOtOxRYtK20GaqTwUCf7WYCJ1z69w=';
               importFAB.crossOrigin = 'anonymous';
               importFAB.src = 'https://cdnjs.cloudflare.com/ajax/libs/fuckadblock/3.2.1/fuckadblock.min.js';
               document.head.appendChild(importFAB);
           }
   </script>
   @endif
<script type="text/javascript">
    $(window).on('load',function(){
        $('#verifyMobileModal').modal('show');
    });
</script>
    @stack('scripts')
</head>

<body style="padding-top:4rem;background: #f3f3f5;">
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KC9KRMP" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>

     @include('frontend.partials.header')
     @if(Request::path()=='offer')
     <main role="main" class="pt-4">
     @else
     <main role="main" class="container" style="min-height:100vh;">
     @endif

    @if(Auth::guest() && count($errors) > 0)
         @include('frontend.partials.message')
    @endif

    @if (session()->has('account'))
        <p class="alert {{session('alert-class') }} alert-dismissible fade show" role="alert">{{ session('account') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
        </p>
    @endif

    @if(Auth::check())
        @if($company_ui_settings->check_email_verified==1  && Auth::user()->email_verified==0)
        <div class="rounded mb-4 p-2 d-flex flex-md-row flex-column justify-content-between align-items-center" style="color: #856404;background-color: #fff3cd;border-color: #ffeeba;">
            <p class="align-center mb-0">Your registered email address is not verified ,  Please verify your email address .</p>
            <button class="my-1 btn  btn-warning" href="javascript:void(0);" onclick="event.preventDefault();document.getElementById('send-verification-form').submit();"><i class="far fa-envelope mr-2"></i>Verify Email Address</button>
            <form id="send-verification-form" class="d-none" method="POST" action="{{route('send_email_verification_link')}}">
                {{csrf_field()}}
            </form>
        </div>
        @include('frontend.auth.modal_verify_email')
        @endif

        @if($company_ui_settings->check_mobile_verified==1  && Auth::user()->mobile_verified==0)
        <div class="rounded mb-4 p-2 d-flex flex-md-row flex-column justify-content-between align-items-center" style="color:#856404;background-color:#fff3cd;border-color: #ffeeba;">
            <p class="align-center mb-0">Your registered mobile number is not verified ,  Please verify your mobile number .</p>
            <button class="my-1 btn  btn-warning" type="button" data-toggle="modal" data-target="#verifyMobileModal"><i class="fas fa-mobile-alt mr-2"></i>Verify Mobile Number</button>
        </div>
        @include('frontend.auth.modal_verify_mobile')
        
        @endif
    @endif

    @include('frontend.auth.modal_forgot')
    @include('frontend.auth.modal_verify_email_mobile')
    @include('frontend.auth.modal_verify_otp')

    {{-- OFFER MODAL
         @include('frontend.partials.offer_modal')
     --}}

    @include('frontend.posts.modals.modal_reportpost')

     @yield('content')
    </main>

 
      <!--@include('frontend.partials.footer')-->
  
      @include('frontend.partials.modal_search')

      @if($company_ui_settings->adblocker==1)
      @include('frontend.partials.adblock_detecter')
      @endif

      @if($company_ui_settings->invite_btn==1)
      @include('frontend.partials.invite_modal')
      @endif

      @include('frontend.auth.modal_login')
      @include('frontend.auth.modal_register')
      @include('frontend.auth.modal_phonecode')
      @include('frontend.opinions.modals.embed')

      @if(Auth::check() && $company_ui_settings->webpush_notification==1)
      @include('frontend.partials.notification_modal')
      @endif

</body>
</html>
