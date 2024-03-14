@extends('frontend.layouts.app')
@section('title',$contest->title." - Opined")
@section('description',str::limit($contest->plain_body,$limit = 150 , $end = '...'))



@push('meta')
<link rel="canonical" href="https://www.weopined.com/opinion/{{$contest->slug}}" />
<link href="https://www.weopined.com/opinion/{{$contest->slug}}" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{ str::limit($contest->title,$limit = 64, $end = '...') }} | Opined">
<meta name="twitter:description" content="{{str::limit($contest->plain_body,$limit = 150 , $end = '...')}}">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image:src" content="{{$contest->image}}">

<!-- Open Graph data -->
<meta property="og:title" content="{{  str::limit($contest->title,$limit = 64, $end = '...') }} | Opined"/>
<meta property="og:type" content="article" />
<meta property="og:url" content="https://www.weopined.com/opinion/{{$contest->slug}}" />
<meta property="og:image" content="{{$contest->image}}" />
<meta property="og:image:alt" content="{{$contest->title}}">
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="og:contentnid" content="{{$contest->id}}" />
<meta property="og:contenttype" content="article_listing" />
<meta property="og:publishdt" content="{{$contest->created_at}}" />
<meta property="og:uploadedby" content="opined" />

<meta property="article:publisher" content="https://www.facebook.com/weopined" />
<meta property="article:published_time" content="{{$contest->created_at}}" />
<meta property="article:modified_time" content="{{$contest->updated_at}}" />

<meta http-equiv="Last-Modified" content="{{Carbon\Carbon::parse($contest->updated_at)->toDayDateTimeString()}}" />
<meta name="Last-Modified" content="{{Carbon\Carbon::parse($contest->updated_at)->toDayDateTimeString()}}" />
<meta name="Last-Modified-Date" content="{{ Carbon\Carbon::parse($contest->updated_at)->toFormattedDateString()}}" />
<meta name="Last-Modified-Time" content="{{ Carbon\Carbon::parse($contest->updated_at)->format('h:i:s A') }} IST" />
<meta name="atdlayout" content="articlepage">

<meta property="fb:app_id" content="1766000746745688" />
<link href="/vendor/emojionearea/emojionearea.min.css" type="text/css" rel="stylesheet" />

@endpush


@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
{!! $company_ui_settings->google_adcode !!}
@endif
<script src="/vendor/emojionearea/emojionearea.min.js" type="text/javascript"></script>
<script src="/js/custom/comments.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>
<script src="/vendor/swiper/swiper.min.js" type="text/javascript"></script>
<script>
      $(document).ready(function(){

        $("#comment_textarea").emojioneArea({
            pickerPosition: "bottom"
        });

        var mySwiper = new Swiper ('.swiper-container', {
          init:true,
          autoplay: {
            delay: 3000,
          },
          speed:300,
          direction: 'horizontal',
          loop: true,
          navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        })
      });
      $(document).on('click','.rsm_opined',function(){
        var post_id=$('#contest_id').val();
        $.ajax({
            url:"/rsm_opined",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type:'POST',
            data:{contest_id:contest_id},
            dataType:'json',
            success:function(response){
              if(response.status=='success'){
                
               }
            },error:function(response){
                
            }
        });
    });

</script>
@endpush

@section('content')
@if($contest->is_active==1)
<div class="row">
  <div style="position: -webkit-sticky;position: sticky;top:1vh;">
    @include('frontend.contest.components.contest_details')
      <h4 class="pb-3 mb-3 font-weight-normal header-card">Leaderboard:</h4>  
        @php
          $count =1;   
        @endphp 
        @foreach($trending_opinions as $index=>$opinion)
          <div style="justify-content: center; align-items: center; display: flex;">
            <div class="col-sm-12 col-md-10 col-xl-8">
              <h5 class="p-3 mb-2 bg-primary text-white">Rank: {{ $count }}</h5>
              @include('frontend.contest.crud.leaderboard_card',['user'=>$opinion->user])
            </div>
          </div>
          <br>
          @php
            $count = $count+1;
          @endphp   
        @endforeach
  </div>
</div> 

<div id="fb-root"></div>
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12&appId=1766000746745688&autoLogAppEvents=1';
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>



{{-- <script src="https://apis.google.com/js/platform.js" async defer></script>
 <script src="https://platform.linkedin.com/in.js" type="text/javascript"></script> --}}

@endsection
@else
<script>
    newLocation();
    function newLocation() {
        window.location="/404";
    }
  </script>
@endif