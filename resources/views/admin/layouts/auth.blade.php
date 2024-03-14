<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="/public_admin/assets/fonts/feather/feather.min.css"> 
    <link rel="stylesheet" href="/public_admin/assets/css/theme.min.css" id="stylesheetLight">

    @stack('styles')

    <script src="/public_admin/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/public_admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>  

    <title>@yield('title')</title>
    <link rel="icon" href="/opined.ico" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>body { display: none; }</style>

    <script>
      $(document).ready(function(){
          $('.password_toggle').click(function(){
              let name=$(this).attr('data-toggle');
              if($('input[name='+name+']').attr("type")==="password"){
                $('input[name='+name+']').attr("type","text");
              }else{
                $('input[name='+name+']').attr("type","password");
              }
          });
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

      });
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
  <body class="d-flex align-items-center bg-auth border-top border-top-2 border-primary">

    @yield('content')
   
    @include('admin.auth.modal_phonecode')

  </body>
</html>