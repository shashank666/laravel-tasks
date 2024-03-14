@extends('frontend.layouts.app')
@section('title','Opined - Where Every Opinion Matters !')
@section('description','Your opinion has power. Opined aims to put power back to common people where everyone can express and discuss opinions. Raise your voice and make a difference.')
@section('keywords','opinion, opined, weopined, share opinion, opinion poll, share opinion and polls, share political opinion and polls, give voice to your words by opined, social media to share opinion, trending topics, trending stories, trending opinions, opinions that matters, social media, politics, sports, economy, business, entrepreneurship, video opinions, trending')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/" />
<link href="https://www.weopined.com/" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Opined - Where Every Opinion Matters !">
<meta name="twitter:description" content="Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Opined - Where Every Opinion Matters !" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
<link href='/css/custom/user_card.css' rel='stylesheet' type='text/css'>
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
     

.tabs {
  display: block;
  display: -webkit-flex;
  display: -moz-flex;
  display: flex;
  -webkit-flex-wrap: wrap;
  -moz-flex-wrap: wrap;
  flex-wrap: wrap;
  margin: 0;
  overflow: hidden; }
  .tabs [class^="tab"] label,
  .tabs [class*=" tab"] label {
    color: #000;
    cursor: pointer;
    display: block;
    font-size: 1.1em;
    font-weight: 400;
    line-height: 1em;
    padding: 0.8rem 0;
    text-align: center; }
  .tabs [class^="tab"] [type="radio"],
  .tabs [class*=" tab"] [type="radio"] {
    border-bottom: 1px solid rgba(239, 237, 239, 0.5);
    cursor: pointer;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    display: block;
    width: 100%;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out; }
    .tabs [class^="tab"] [type="radio"]:hover, .tabs [class^="tab"] [type="radio"]:focus,
    .tabs [class*=" tab"] [type="radio"]:hover,
    .tabs [class*=" tab"] [type="radio"]:focus {
      border-bottom: 1px solid #060508; }
    .tabs [class^="tab"] [type="radio"]:checked,
    .tabs [class*=" tab"] [type="radio"]:checked {
      border-bottom: 2px solid #060508; }
    .tabs [class^="tab"] [type="radio"]:checked + div,
    .tabs [class*=" tab"] [type="radio"]:checked + div {
      opacity: 1; }
    .tabs [class^="tab"] [type="radio"] + div,
    .tabs [class*=" tab"] [type="radio"] + div {
      display: block;
      opacity: 0;
      padding: 2rem 0;
      width: 100%;
      -webkit-transition: all 0.3s ease-in-out;
      -moz-transition: all 0.3s ease-in-out;
      -o-transition: all 0.3s ease-in-out;
      transition: all 0.3s ease-in-out; }
  .tabs .tab-2 {
    width: 50%; }
    .tabs .tab-2 [type="radio"] + div {
      width: 200%;
      margin-left: 200%; }
    .tabs .tab-2 [type="radio"]:checked + div {
      margin-left: 0; }
    .tabs .tab-2:last-child [type="radio"] + div {
      margin-left: 100%; }
    .tabs .tab-2:last-child [type="radio"]:checked + div {
      margin-left: -100%; }
   </style>
@endpush

@push('scripts')
 {{--  @if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
  @endif  --}}
{{-- <script defer type="text/javascript" src="/js/custom/threads.js?<?php echo time();?>"></script> --}}

<script  src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
{{-- <script async src="/js/custom/opinion_comments.js?<?php echo time();?>" type="text/javascript"></script> --}}
{{-- <script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script> --}}
<script defer src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>
<script  src="/js/web/home.min.js" type="text/javascript"></script>
<script defer type="text/javascript" src="/js/jquery.caret-atwho.min.js?<?php echo time();?>"></script>
<script src='/js/custom/user_short_card.js' type='text/javascript'></script>
<script>
  $(document).ready(function(){

      /* OFFER MODAL
      $('#opined_introductory_offer').modal('show');
      */

        

      $('[data-toggle="tooltip"]').tooltip({ trigger: "hover" });
      $('[data-toggle="tooltip"]').click(function () {
        $('[data-toggle="tooltip"]').tooltip("hide");
      });

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
     <!--   <h4 class="pb-3 mb-4 font-weight-normal" style="color:#ff9800;border-bottom:1px solid #495057;">Trending Topics<span><a href="/threads/trending" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#ff9800;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
-->

@include('frontend.partials.modal_opinion_likes')
  <div class="page-title-wrapper">
    <h1 class="page-title">
        <span data-ui-id="page-title-wrapper">Opined - Where Every Opinion Matters ! - Opinions and Polls</span></h1>
</div>


        <div class="row mt-2 mb-5">
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
                @include('frontend.opinions.crud.create_home')

<!--               
        <h4 class="pb-3 mb-3 font-weight-normal latest_tab" style="color:#ff9800;border-bottom:1px solid #495057;">Latest Opinions</h4>
-->
        <div class="tabs">
            <div class="tab-1" >
              <label for="tab1-1">Trending Opinions</label>
              <input id="tab1-1" name="tabs-two" type="radio" checked="checked">
              <div class="card-columns card-columns-opinion trending_opinions">
        
                @foreach($trending_opinions as $index=>$opinion)
                
                @include('frontend.opinions.components.home_opinion_card',['user'=>$opinion->user])

                
                {{--  @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                    @if(($index%4)==0)
                    <div class="mb-3">
                      
                        {!! $google_native_ad->ad_code !!}
                    </div>
                    @endif
                @endif  --}}
                @endforeach
            </div>
            </div>
            {{--  <div class="tab-2">
              <label for="tab2-2">Latest Opinions</label>
              <input id="tab2-2" name="tabs-two" type="radio">
               <div class="card-columns card-columns-opinion latest_opinions">
        
                    @foreach($latest_opinions as $index=>$opinion)
                    
                    @include('frontend.opinions.components.home_opinion_card',['user'=>$opinion->user])

                    
                    @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                        @if(($index%4)==0)
                        <div class="mb-3">
                          
                            {!! $google_native_ad->ad_code !!}
                        </div>
                        @endif
                    @endif
                    @endforeach
                </div> 
            </div>
              --}}
          </div>

        

        
            </div>
         <!-- <div class="col-xl-1 col-lg-1 col-md-1" style="border-left: 1px solid black; left: 4%;
            height: auto;">
            
            </div>-->
        <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
              
              @if(Auth::check() && count($followed_threads)>0)
            
            <div class=" mb-5 trending-card">
            <h4 class="pb-3 mb-3 font-weight-normal header-card">Topics You Follow<span><a href="threads/followed" class="btn btn-sm float-right" style="background-color:white;color:#ff9800;outline:none;margin-left:25px">See All</a></span></h4>
                @php($colors=['#ff9800'])
                @foreach($followed_threads as $followed_thread)
                    <div class="col-12">
                    @include('frontend.threads.components.follow_thread_card_thread',['thread'=>$followed_thread->thread])
                    </div>
                @endforeach
            </div>
            @endif

            @if(Auth::check() && count($followed_threads)<1) 
                <div class=" mb-5 trending-card">
                <h4 class="pb-3 mb-3 font-weight-normal header-card">Topics You Follow<span><a
                        href="threads/followed" class="btn btn-sm float-right"
                        style="background-color:white;color:#ff9800;outline:none;width:100%;">See All</a></span></h4>
                    <a class="btn btn-block btn-outline-none text-light" href="/threads/latest"
                        style="color: black !important">Topics You Follow Will Appear Here</a>
                </div>
                @endif
                @if(Auth::guest())
                
                <div class=" mb-5 trending-card" style="margin-left: 0.5%; margin-right: 0.5%">
                <h4 class="pb-3 mb-3 font-weight-normal header-card">Topics You Follow<span><a href="#forgotPasswordModal" class="btn btn-sm float-right" data-toggle="modal" style="background-color:white;color:#ff9800;outline:none;width:100%;">See All</a></span></h4>
                <a class="btn btn-block" style="background: #ff9800;color: white !important;" data-toggle="modal" href="#forgotPasswordModal">Login to See What You Follow</a>
                </div>

                @endif

              @if(Auth::check() && count($circle_threads)>0)
                
                <div class=" mb-5 trending-card">
                <h4 class="pb-3 mb-4 font-weight-normal header-card">Trending For You<span><a href="/threads/trending" class="btn btn-sm float-right" style="background-color:white;color:#ff9800;outline:none;margin-left:55px">See All</a></span></h4>
                    @php($colors=['#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800','#ff9800'])
                    @foreach($circle_threads as $circle_thread)
                        <div class="col-12">
                        @include('frontend.threads.components.thread_card_thread',['thread'=>$circle_thread->thread])
                        </div>
                    @endforeach
                </div>
                

            @elseif(Auth::check() && count($circle_threads)<1)
            
                <div class=" mb-5 trending-card">
                <h4 class="pb-3 mb-3 font-weight-normal header-card trending-card">Trending For You<span><a href="threads/circle" class="btn btn-sm float-right" style="background-color:white;color:#ff9800;outline:none;margin-left:45px">See All</a></span></h4>
                
                <a class="btn btn-block btn-outline-none text-light"  href="" style="color: black !important">Follow Your Friends To See How They Are Opined</a>
               
            </div>
            @endif
            @if(Auth::guest())
                
                <div class=" mb-5 trending-card" style="margin-left: 0.5%; margin-right: 0.5%">
                <h4 class="pb-3 mb-3 font-weight-normal header-card">Trending For You<span><a href="#forgotPasswordModal" class="btn btn-sm float-right" data-toggle="modal" style="background-color:white;color:#ff9800;outline:none; margin-left:45px;width:100%">See All</a></span></h4>
                <a class="btn btn-block" style="background: #ff9800;color: white !important;" data-toggle="modal" href="#forgotPasswordModal">Login to See What's Trending Among Your Friends</a>
                </div>

                @endif
            
           {{--  @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif  --}}


           @if(count($threads)>0)
                
                <div class=" mb-5 trending-card">
                <h4 class="pb-3 pt-3 font-weight-normal header-card">Trending Topics<span><a href="/threads/trending" class="btn btn-sm float-right" style="background-color:white;color:#ff9800;outline:none;margin-bottom:0; margin-left:45px;">See All</a></span></h4>
                    @php($colors=['#ff9800'])
                    @foreach($threads as $trending_thread)
                        <div class="col-12">
                        @include('frontend.threads.components.thread_card_thread',['thread'=>$trending_thread->thread])
                        </div>
                    @endforeach
                </div>
                @endif
            <div style="position: -webkit-sticky;position: sticky;top:1vh;">
           {{--  @if(count($latest_threads)>0)
                <div>
                        
                        <div class="row trending-card">
                        <h4 class="pb-3 mb-3 font-weight-normal header-card">Latest Threads<span><a href="threads/latest" class="btn btn-sm float-right" style="background-color:white;color:#ff9800;outline:none; margin-left:65px">See All</a></span></h4>
                                @php($colors=['#ff9800'])
                                @foreach($latest_threads as $thread)
                                <div class="col-12">
                                @include('frontend.threads.components.thread_card_thread')
                                </div>
                                @endforeach
                        </div>
                </div>
                @endif  --}}
             </div>

            </div>

        </div>

        @foreach($trending_threads_with_opinions as $index=>$thread)
        <h4 class="pb-3 mb-3 font-weight-normal" style="color:#ff9800;border-bottom:1px solid #ff9800;">{{ '#'.$thread->name }}<span><a href="/thread/{{$thread->name}}" class="btn float-right" style="background-color:white;border:2px solid black;outline:none;"><i class="fas fa-arrow-right" style="color:black;"></i></a></span></h4>
        <div class="card-columns mb-5">
                @foreach($thread->opinions as $opinion)
                @include('frontend.opinions.components.home_opinion_card',['user'=>$opinion->user])
                @endforeach
        </div>
        
        @endforeach

      @include('frontend.posts.modals.modal_add_gif')
      @include('frontend.posts.modals.modal_add_gif')
      @include('frontend.opinions.components.youtube_video_modal')
      @include('frontend.opinions.components.embed_code_modal')
      @include('frontend.opinions.components.message_modal')
      @include('frontend.opinions.crud.delete')
  @endsection

