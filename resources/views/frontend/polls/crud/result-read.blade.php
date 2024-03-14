@extends('frontend.layouts.error')
@section('title',"Vote and Share your opinion on ".$poll->title." on Opined")
@section('description',str::limit($poll->description ,$limit = 150 , $end = '...'))
@section('keywords','opinion, opined, weopined, share opinion, opinion poll, share opinion and polls, share political opinion and polls, give voice to your words by opined, social media to share opinion, trending topics, trending stories, trending opinions, opinions that matters, social media, politics, sports, economy, business, entrepreneurship, video opinions, trending')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/polls/{{$poll->slug}}" />
<link href="https://www.weopined.com/polls/{{$poll->slug}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Vote and Share your opinion on {{$poll->title}} on Opined">
<meta name="twitter:description" content="{!! str::limit($poll->description ,$limit = 150 , $end = '...')!!}">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Vote and Share your opinion on {{$poll->title}} on Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/polls/{{$poll->slug}}" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="{!! str::limit($poll->description ,$limit = 150 , $end = '...')!!}" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
<style>
  .page-title-wrapper {
    display: none;
  }
  #profileurl{
      background-color:#fff;
  }
  @media (max-width: 767px) {
    .card-columns-opinion {
           -webkit-column-count: 1;
           -moz-column-count: 1;
           column-count: 1;
       }
   }
   @media (min-width: 576px) {
       .card-columns-opinion {
              -webkit-column-count: 2 !important ;
              -moz-column-count: 2 !important ;
              column-count: 2  !important;
          }
      }
   </style>
   <style type="text/css">
       .btn3d {
    position: relative;
    top: -6px;
    border: 0;
    transition: all 40ms linear;
    margin-top: 10px;
    margin-bottom: 10px;
    margin-left: 2px;
    margin-right: 2px;
}

.btn3d:active:focus,
.btn3d:focus:hover,
.btn3d:focus {
    -moz-outline-style: none;
    outline: medium none;
}

.btn3d:active,
.btn3d.active {
    top: 2px;
}

.btn3d.btn-white {
    color: #666666;
    box-shadow: 0 0 0 1px #ebebeb inset, 0 0 0 2px rgba(255, 255, 255, 0.10) inset, 0 8px 0 0 #f5f5f5, 0 8px 8px 1px rgba(0, 0, 0, .2);
    background-color: #fff;
}

.btn3d.btn-white:active,
.btn3d.btn-white.active {
    color: #666666;
    box-shadow: 0 0 0 1px #ebebeb inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, .1);
    background-color: #fff;
}

.btn3d.btn-default {
    color: #666666;
    box-shadow: 0 0 0 1px #ebebeb inset, 0 0 0 2px rgba(255, 255, 255, 0.10) inset, 0 8px 0 0 #BEBEBE, 0 8px 8px 1px rgba(0, 0, 0, .2);
    background-color: #f9f9f9;
}

.btn3d.btn-default:active,
.btn3d.btn-default.active {
    color: #666666;
    box-shadow: 0 0 0 1px #ebebeb inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, .1);
    background-color: #f9f9f9;
}

.btn3d.btn-primary {
    box-shadow: 0 0 0 1px #417fbd inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #4D5BBE, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #4274D7;
}

.btn3d.btn-primary:active,
.btn3d.btn-primary.active {
    box-shadow: 0 0 0 1px #417fbd inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #4274D7;
}

.btn3d.btn-success {
    box-shadow: 0 0 0 1px #31c300 inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #5eb924, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #78d739;
}

.btn3d.btn-success:active,
.btn3d.btn-success.active {
    box-shadow: 0 0 0 1px #30cd00 inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #78d739;
}

.btn3d.btn-info {
    box-shadow: 0 0 0 1px #00a5c3 inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #348FD2, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #39B3D7;
}

.btn3d.btn-info:active,
.btn3d.btn-info.active {
    box-shadow: 0 0 0 1px #00a5c3 inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #39B3D7;
}

.btn3d.btn-warning {
    box-shadow: 0 0 0 1px #d79a47 inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #D79A34, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #FEAF20;
}

.btn3d.btn-warning:active,
.btn3d.btn-warning.active {
    box-shadow: 0 0 0 1px #d79a47 inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #FEAF20;
}

.btn3d.btn-danger {
    box-shadow: 0 0 0 1px #b93802 inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #AA0000, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #D73814;
}

.btn3d.btn-danger:active,
.btn3d.btn-danger.active {
    box-shadow: 0 0 0 1px #b93802 inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #D73814;
}

.btn3d.btn-magick {
    color: #fff;
    box-shadow: 0 0 0 1px #9a00cd inset, 0 0 0 2px rgba(255, 255, 255, 0.15) inset, 0 8px 0 0 #9823d5, 0 8px 8px 1px rgba(0, 0, 0, 0.5);
    background-color: #bb39d7;
}

.btn3d.btn-magick:active,
.btn3d.btn-magick.active {
    box-shadow: 0 0 0 1px #9a00cd inset, 0 0 0 1px rgba(255, 255, 255, 0.15) inset, 0 1px 3px 1px rgba(0, 0, 0, 0.3);
    background-color: #bb39d7;
}

.btn-sm{
    padding: .15rem .25rem !important;
    font-size: .850rem !important;
    line-height: 1.2 !important;
    border-radius: .2rem !important;
}
.flex-box {
  display:flex;
  justify-content:space-between;
}
   </style>

@endpush

@push('scripts')
 @if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
  @endif
<!--<script defer type="text/javascript" src="/js/custom/threads.js?<?php echo time();?>"></script>

<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>
<script defer src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>-->
<script async src="/js/web/home.min.js" type="text/javascript"></script>
<script defer type="text/javascript" src="/js/jquery.caret-atwho.min.js?<?php echo time();?>"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
<script>
  $(document).ready(function(){
    
     $(".incrementup").click(function(e){
            e.preventDefault();
                var upvote = parseInt($(".upvote").text());
                var downvote = parseInt($(".downvote").text());
                    if(upvote >= 0 && downvote == 0){
                    var upvote = upvote + 1;
                      if(upvote <= 3){
                        $('#voting').val(upvote);
                        $(".upvote").text(upvote);
                        $("#voting_type").val("upvote");
                        var neutral = 0;
                        $(".neutral").text(neutral);
                      }
                  }
                  else if(downvote >= 0){
                     downvote = downvote - 1;
                            if(downvote>=0){
                                $('#voting').val(downvote);
                                $(".downvote").text(downvote);
                                $("#voting_type").val("downvote");
                                var neutral = 0;
                                $(".neutral").text(neutral);
                            }
                  }
                  if(upvote == 0 && downvote == 0){
                  $('#btn_poll_before').show();
                  $('#btn_poll_after').hide();
              }
              else{
                $('#btn_poll_before').hide();
                $('#btn_poll_after').show();
            }
              });
        $(".incrementdown").click(function(e){
        e.preventDefault();
        var upvote = parseInt($(".upvote").text());
        var downvote = parseInt($(".downvote").text());
        if(downvote >= 0 && upvote == 0){
            var downvote = downvote + 1;
            if(downvote<=3){
            $(".downvote").text(downvote);
            $('#voting').val(downvote);
            $("#voting_type").val("downvote");
            var neutral = 0;
            $(".neutral").text(neutral);
            }
        }
           else if(upvote >= 0){
                upvote = upvote - 1;
                if(upvote>=0){
                    $('#voting').val(upvote);
                    $(".upvote").text(upvote);
                    $("#voting_type").val("upvote");
                    var neutral = 0;
                    $(".neutral").text(neutral);
                }
             }
            if(upvote == 0 && downvote == 0){
                  $('#btn_poll_before').show();
                  $('#btn_poll_after').hide();
              }
            else{
                $('#btn_poll_before').hide();
                $('#btn_poll_after').show();
            }   
       });

      $(".incrementneutral").click(function(e){
            e.preventDefault();
            var neutral = 1;
            var downvote = 0;
            var upvote = 0;
            $(".upvote").text(upvote);
            $(".downvote").text(downvote);
            $(".neutral").text(neutral);
            $('#voting').val(neutral);
            $("#voting_type").val("neutral");
            $('#btn_poll_before').hide();
            $('#btn_poll_after').show();

        });

      $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
      $('[data-toggle="tooltip"]').click(function () {
        $('[data-toggle="tooltip"]').tooltip("hide");
      });



      /* OFFER MODAL
      $('#opined_introductory_offer').modal('show');
      */

      

      $('#btn_submit_opinion').attr('disabled','disabled');
      $('#write_opinion').atwho({
          at: "#",
          limit: 200,
          searchKey : 'name',
          data: 'https://weopined.com',
          callbacks: {
              remoteFilter: function(query, callback) {
                  $.getJSON("/search/threads", {
                      q: query
                  }, function(data) {
                      callback(data.threads);
                  });
              },
              afterMatchFailed: function(at, el) {
                  // 32 is spacebar
                  if (at == '#') {
                      tags.push(el.text().trim().slice(1));
                      this.model.save(tags);
                      this.insert(el.text().trim());
                      return false;
                  }
              }
          }
      });

      /*
      $('#marquee-vertical').marquee({
        delay: 0,
        timing: 50
      });
      */

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

  $(document).on('keyup','#write_opinion', function () {
    if($(this).val().trim() != "" && $(this).val().trim().length>0) {
      $("#btn_submit_opinion").removeAttr('disabled');
    } else {
      $("#btn_submit_opinion").attr('disabled','disabled');
    }
  });
</script>
@endpush


@section('content')
     <!--   <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending Threads<span><a href="/threads/trending" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
-->

<div class="page-title-wrapper">
    <h1 class="page-title">
        <span data-ui-id="page-title-wrapper">Polls at Opined - Where Every Opinion Matters !</span></h1>
</div>
<div class="row">
    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
            {{--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif--}}
    </div>

    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
      <div class="card gradient-card border-0 rounded" style="background: transparent;">
        <div class="card-image" style="background-image: url(https://mdbootstrap.com/img/Photos/Horizontal/Work/4-col/img%20%2814%29.jpg);border-radius: 15px;">

        <div class="card-body text-white d-flex h-100 mask purple-gradient-rgba" style="text-align: justify;    background: linear-gradient( 33deg, rgba(98, 112, 129, 0.87) 10%, rgba(246, 151, 33, 0.87) 90%);border-radius: 15px;">
         
          Although expressing your opinion through video or text is the best way to raise your voice as it shows sentiments and passion, we understand that sometimes, you just want to share your opinions in binary forms. Opined Polls provides you with the opportunity to submit your opinions on pre-defined polls so that you can also take part in making the difference. After all, Opined is where every opinion matters!
          
        </div>
      </div>
      </div>
        @include('frontend.polls.components.result_card',['user'=>$poll->user])
    </div>

    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
            {{--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif--}}
    </div>

</div>
@if(count($related_threads)>0)
      <div class="row mt-4">  
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
          <h4>Related Threads:</h4> 
          <div class="card-columns">
          @foreach($related_threads as $index=>$thread)
            @include('frontend.polls.components.related_thread_card',['user'=>$poll->user])
          @endforeach
        </div>
        </div>
      </div>
  @endif
@if(count($related_polls)>0)
    <div class="row mt-4">  
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
          <h4>Related Polls:</h4> 
          <div class="card-columns">
          @foreach($related_polls as $index=>$poll)
            @include('frontend.polls.components.related_poll_card',['user'=>$poll->user])
          @endforeach
        </div>
        </div>
      </div> 
    @endif   
  @endsection

