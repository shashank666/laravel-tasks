<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <link rel="icon" href="/opined.ico" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')
    

    <link rel="stylesheet" href="/public_admin/assets/fonts/feather/feather.min.css">
    <link rel="stylesheet" href="/public_admin/assets/css/theme.min.css" id="stylesheetLight">
    <link rel="stylesheet" href="/public_admin/assets/css/theme-dark.min.css" id="stylesheetDark">
    <link href="/vendor/videojs/video-js.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
    #scroll {
    position:fixed;
    right:10px;
    bottom:10px;
    cursor:pointer;
    width:50px;
    height:50px;
    background-color:#244363;
    text-indent:-9999px;
    display:none;
    -webkit-border-radius:60px;
    -moz-border-radius:60px;
    border-radius:60px
}
#scroll span {
    position:absolute;
    top:50%;
    left:50%;
    margin-left:-8px;
    margin-top:-12px;
    height:0;
    width:0;
    border:8px solid transparent;
    border-bottom-color:#ffffff;
}
#scroll:hover {
    background-color:#ff9800;
    opacity:1;filter:"alpha(opacity=100)";
    -ms-filter:"alpha(opacity=100)";
}
</style>
    <!--<link rel='dns-prefetch' href='//gravatar.com' />-->

    <link href="https://www.weopined.com/vendor/bootstrap/css/bootstrap.min.css?<?php echo time();?>" rel="stylesheet" type="text/css">
    <link href="https://www.weopined.com/css/custom/main.min.css?<?php echo time();?>" rel="stylesheet" type="text/css"/>
    <link href="https://www.weopined.com/vendor/videojs/video-js.css" rel="stylesheet" type="text/css"/>




    @stack('styles')
  
  <!------------------- End of Schema Informations ----------------->
    <script type="text/javascript" src="/js/web/core.min.js"></script>
    <!--<script type="text/javascript" src="/js/custom/main.js?<?php echo time();?>"></script>-->
	<script type="text/javascript" src="/js/custom/main.min.js?<?php echo time();?>"></script>
    <script type="text/javascript" src="/js/custom/share.js"></script>
    <script defer type="text/javascript" src='/vendor/videojs/video.min.js'></script>
    <script src="/js/yall.min.js"></script>
    <script src="/public_admin/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/public_admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public_admin/assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v5.8.2/js/all.js"></script>
    <script type="text/javascript" src='/vendor/videojs/video.min.js'></script>
    <script type="text/javascript">
        $(document).ready(function(){ 
        $(window).scroll(function(){ 
            if ($(this).scrollTop() > 100) { 
                $('#scroll').fadeIn(); 
            } else { 
                $('#scroll').fadeOut(); 
            } 
        }); 
        $('#scroll').click(function(){ 
            $("html, body").animate({ scrollTop: 0 }, 600); 
            return false; 
        }); 
    });
    </script>
    @if(Request::path()!='/cpanel/login' || Request::path()!='/cpanel/register')
    <script type="text/javascript">
        var warningTimeout = 540000;
        var timoutNow = 60000;
        var warningTimerID,timeoutTimerID;

        function startTimer() {
            // window.setTimeout returns an Id that can be used to start and stop a timer
            warningTimerID = window.setTimeout(warningInactive, warningTimeout);
        }

        function warningInactive() {
            window.clearTimeout(warningTimerID);
            timeoutTimerID = window.setTimeout(IdleTimeout, timoutNow);
            $('#modalAutoLogout').modal('show');
        }

        function resetTimer() {
            window.clearTimeout(timeoutTimerID);
            window.clearTimeout(warningTimerID);
            startTimer();
        }

        // Logout the user.
        function IdleTimeout() {
            document.getElementById('logout-form').submit();
        }

        function setupTimers () {
            document.addEventListener("mousemove", resetTimer, false);
            document.addEventListener("mousedown", resetTimer, false);
            document.addEventListener("keypress", resetTimer, false);
            document.addEventListener("touchmove", resetTimer, false);
            document.addEventListener("onscroll", resetTimer, false);
            startTimer();
        }
        
        $(document).on('click','#btnStayLoggedIn',function(){
            resetTimer();
            $('#modalAutoLogout').modal('hide');
        });

        $(document).ready(function(){
            setupTimers();
        });
   </script>
    @endif
    <script>
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

<body style="background: #f3f3f5;">
    
    

@if(Request::path()!='/cpanel/login' || Request::path()!='/cpanel/register')
    @include('admin.partials.modal_logout')
    @endif

    @include('admin.partials.theme-setting')
    @include('admin.partials.modal_search')
    @include('admin.partials.modal_notifications')
    @include('admin.auth.modal_phonecode')
    @include('admin.partials.sidebar')
    @include('admin.partials.navbar')
    <div class="main-content">
    @include('admin.partials.navbar_main')
    @yield('content')
    </div>
    <script src="/public_admin/assets/js/theme.js"></script>

  </body>
      @include('frontend.opinions.modals.embed')

     

</body>
</html>
