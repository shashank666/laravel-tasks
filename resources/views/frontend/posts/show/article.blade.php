@extends('frontend.layouts.app')
@section('title','Articles - Opined - Where Every Opinion Matters !')
@section('description','Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined.')
@section('keywords','')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/" />
<link href="https://www.weopined.com/" rel="alternate" reflang="en" />
<!-- Change the title and href to your site -->
<link rel="alternate" type="application/rss+xml" title="Opined" href="/article/rss.xml" />
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
@endpush

@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif
<script type="text/javascript" src="/js/custom/threads.js?<?php echo time();?>"></script>
<script type="text/javascript" src="/js/jquery.caret-atwho.min.js?<?php echo time();?>"></script>

<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>

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
          data: 'http://weopined.com',
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
  
     <div class="container nav-scroller py-1 mb-2 shadow" style="margin-top: -14px; background: #f3f3f5;position: sticky;top: 51px;
    -webkit-position: sticky;">
    <div class="row">
    <div class="col-12">
        <nav class="nav d-flex justify-content-between font-weight-normal">
            <a class="p-2 text-secondary" href="/latest" title="Latest Articles">Latest</a>
            <a class="p-2 text-secondary" href="/trending" title="Trending Articles">Trending</a>
            <a class="p-2 text-secondary" href="/mostliked" title="Most Liked Articles">Most Liked</a>

            @if(count($categories)>6)
                @for($i=0;$i<6;$i++)
            <a class="p-2 text-secondary" href="/topic/{{$categories[$i]->slug}}" title="{{ucfirst($categories[$i]->name)}}">{{ucfirst($categories[$i]->name)}}</a>
                @endfor
            @else
                @for($i=0;$i<(count($categories));$i++)
            <a class="p-2 text-secondary" href="/topic/{{$categories[$i]->slug}}" title="{{ucfirst($categories[$i]->name)}}">{{ucfirst($categories[$i]->name)}}</a>
                @endfor
            @endif
           <a class="p-2 text-secondary" href="/topics" title="Explore Topics">More Topics</a>
           <a class="p-2 text-secondary d-md-none d-sm-inline d-inline" href="" title="Explore Topics">Write an Article</a>
           @if(Auth::user() && Auth::user()->registered_as_writer==0)
			<a class="btn btn-sm d-md-inline d-sm-none d-none mr-3 bg-opined-orange waves-effect waves-float text-white" href="{{ route('writer_terms') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;">
        @elseif(Auth::user() && Auth::user()->registered_as_writer==1)
        <a class="btn btn-sm d-md-inline d-sm-none d-none mr-3 bg-opined-orange waves-effect waves-float text-white" href="{{ route('write') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;">
          @else
          <a class="btn btn-sm d-md-inline d-sm-none d-none mr-3 write_opinion_link bg-opined-orange waves-effect waves-float text-white" href="{{ route('write') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;">
            @endif
        <span style="margin-right:8px;"><i class="fas fa-pencil-alt"></i></span>Write an Article</a>

        @if(Auth::user() && Auth::user()->registered_as_writer==0)
      <a class="btn btn-sm d-md-none d-sm-inline d-inline mr-3 bg-opined-orange waves-effect waves-float text-white" href="{{ route('writer_terms') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;position:absolute;right: -3%;">
        @elseif(Auth::user() && Auth::user()->registered_as_writer==1)
        <a class="btn btn-sm d-md-none d-sm-inline d-inline mr-3 bg-opined-orange waves-effect waves-float text-white" href="{{ route('write') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;position:absolute;right: -3%;">
          @else
          <a class="btn btn-sm d-md-none d-sm-inline d-inline mr-3 write_opinion_link bg-opined-orange waves-effect waves-float text-white" href="{{ route('write') }}" title="Write an Article" style="border-radius:5px;padding-top: 9px;position:absolute;right: -3%;">
            @endif
        <span style="margin-right:8px;"><i class="fas fa-pencil-alt"></i></span>Write an Article</a>
        </nav>
    </div>
    </div>
  </div>

      @if(count($latest_posts)>0)
      <div class="latest_posts" style="padding-top: 1.5%;">
          <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Latest Articles<span><a href="/latest" class="btn btn-sm waves-effect waves-float float-right btn-article"><i class="fas fa-arrow-right"></i></a></span></h4>
          <div class="row mb-5">
              @foreach($latest_posts as $post)
                  @include('frontend.posts.components.post_horizontal')
              @endforeach
          </div>
      </div>
      @endif
      @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
      @if(count($trending_posts)>0)
      <div class="trending_posts">
          <h4 class="pb-3 mb-4 pt-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending Articles<span><a href="/trending" class="btn btn-sm waves-effect waves-float float-right btn-article"><i class="fas fa-arrow-right"></i></a></span></h4>
          <div class="row mb-5">
              @foreach($trending_posts as $post)
                  @include('frontend.posts.components.post_horizontal')
              @endforeach
          </div>
      </div>
      @endif
      @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
      @if(Auth::check())
            @if(count($followed_category_posts)>0)
            <div class="followed_category_posts">
                <h4 class="pb-3 pt-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">You May Like<span><a href="/interested" class="btn btn-sm waves-effect waves-float float-right btn-article"><i class="fas fa-arrow-right"></i></a></span></h4>
                <div class="row mb-5">
                    @foreach($followed_category_posts as $post)
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 portfolio-item mb-4">
                            @include('frontend.posts.components.post-card')
                        </div>
                    @endforeach
                </div>
                <div>
            @endif
            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
            @if(count($circle_latest_posts)>0)
            <div class="circle_posts">
              <h4 class="pb-3 mb-4 pt-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">For You<span><a href="/circle" class="btn btn-sm waves-effect waves-float float-right btn-article"><i class="fas fa-arrow-right"></i></a></span></h4>
              <div class="row mb-5">
                  @foreach($circle_latest_posts as $post)
                      <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 portfolio-item mb-4">
                          @include('frontend.posts.components.post-card-big')
                      </div>
                  @endforeach
              </div>
            <div>
            @endif
      @endif
      @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif
      @if(count($mostliked_posts)>0)
      <div class="mostliked_posts">
          <h4 class="pb-3 mb-4 pt-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Most Liked Articles<span><a href="/mostliked" class="btn btn-sm waves-effect waves-float float-right btn-article"><i class="fas fa-arrow-right"></i></a></span></h4>
          <div class="row mb-5">
              @foreach($mostliked_posts as $post)
                  @include('frontend.posts.components.post_horizontal')
              @endforeach
          </div>
      </div>
      @endif
      @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif

      @if(count($other_categories_posts)>0)
      <div class="other_topics_posts">
          <h4 class="pb-3 mb-4 pt-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">From Various Topics</h4>
          <div class="row mb-5">
              @foreach($other_categories_posts as $index=>$post)
                @if($index<10)
                @include('frontend.posts.components.post_horizontal')
                @endif
              @endforeach
          </div>
      </div>
      @endif

      @include('frontend.posts.modals.modal_add_gif')
      @include('frontend.opinions.components.youtube_video_modal')
      @include('frontend.opinions.components.embed_code_modal')
      @include('frontend.opinions.components.message_modal')
      @include('frontend.opinions.crud.delete')
  @endsection

