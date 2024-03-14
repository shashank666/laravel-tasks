<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    <link rel="icon" href="/opined.ico" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')
    <style>body { display: none; }</style>

    <link rel="stylesheet" href="/public_admin/assets/fonts/feather/feather.min.css">
    <link rel="stylesheet" href="/public_admin/assets/libs/select2/dist/css/select2.min.css">
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
    @stack('styles')

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
    @stack('scripts')

  </head>
  <body>

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
</html>
