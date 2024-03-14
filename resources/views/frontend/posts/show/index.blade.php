@extends('frontend.layouts.app')
@section('title','Opined - Where Every Opinion Matters !')
@section('description','Welcome to Opined, a platform to enable every person to read, write and discuss individual opinions
on top trending topics and stories. Every day, thousands of people share, read, discuss and write
their opinion on Opined.')
@section('keywords','')

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
@endpush


 @push('scripts')
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
        <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending Threads<span><a href="/threads/trending" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>

        <div class="row mt-2 mb-5">
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                @include('frontend.opinions.crud.create_home')
            </div>
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            @include('frontend.opinions.components.threads_block')
            </div>
        </div>

        @if(Auth::check() && count($circle_threads)>0)
        <h4 class="pb-3 mb-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending in For You</h4>
        <div class="row mb-5">
                @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
                {{--  @foreach($circle_threads as $index=>$thread)
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6" style="padding-left:8px;padding-right:8px;">
                    <a  href="/thread/{{$thread->name}}" title="{{ '#'.$thread->name}}">
                    <div class="thread_card card bg-light mb-3 p-2 text-center shadow-sm" style="border:0px;">
                        <h6 class="text-truncate" style="color:{{$colors[array_rand($colors,1)]}}">{{'#'.$thread->name}}</h4>
                        <span class="text-secondary"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</small></span>
                    </div>
                    </a>
                </div>
                @endforeach  --}}
                @foreach($circle_threads as $index=>$circle_thread)
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 col-6" style="padding-left:8px;padding-right:8px;">
                    <a  href="/thread/{{$circle_thread->thread->name}}" title="{{ '#'.$circle_thread->thread['name']}}">
                    <div class="thread_card card bg-light mb-3 p-2 text-center shadow-sm" style="border:0px;">
                        <h6 class="text-truncate" style="color:{{$colors[array_rand($colors,1)]}}">{{'#'.$circle_thread->thread->name}}</h4>
                        <span class="text-secondary"><small><i class="far fa-comment-alt mr-2"></i>{{ $circle_thread->thread->opinions_count }} Opinions</small></span>
                    </div>
                    </a>
                </div>
                @endforeach
        </div>
        @endif

        <h4 class="pb-3 mb-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Latest Opinions</h4>
        <div class="card-columns mb-5">
            @foreach($latest_opinions as $opinion)
            @include('frontend.opinions.components.home_opinion_card',['user'=>$opinion->user])
            @endforeach
        </div>


        @foreach($trending_threads_with_opinions as $index=>$thread)
        <h4 class="pb-3 mb-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">{{ '#'.$thread->name }}<span><a href="/thread/{{$thread->name}}" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
        <div class="card-columns mb-5">
                @foreach($thread->opinions as $opinion)
                @include('frontend.opinions.components.home_opinion_card',['user'=>$opinion->user])
                @endforeach
        </div>
        @endforeach

      @if(count($trending_posts)>0)
      <div class="trending_posts">
          <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending Articles<span><a href="/trending" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
          <div class="row mb-5">
              @foreach($trending_posts as $post)
                  @include('frontend.posts.components.post_horizontal')
              @endforeach
          </div>
      </div>
      @endif

      @if(Auth::check())
            @if(count($followed_category_posts)>0)
            <div class="followed_category_posts">
                <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">You may like<span><a href="/interested" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
                <div class="row mb-5">
                    @foreach($followed_category_posts as $post)
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12 portfolio-item mb-4">
                            @include('frontend.posts.components.post-card')
                        </div>
                    @endforeach
                </div>
                <div>
            @endif

            @if(count($circle_latest_posts)>0)
            <div class="circle_posts">
              <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">For You<span><a href="/circle" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
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

      @if(count($mostliked_posts)>0)
      <div class="mostliked_posts">
          <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Most Liked Articles<span><a href="/mostliked" class="btn btn-sm waves-effect waves-float float-right" style="background-color:#244363;color:#fff;outline:none;"><i class="fas fa-arrow-right"></i></a></span></h4>
          <div class="row mb-5">
              @foreach($mostliked_posts as $post)
                  @include('frontend.posts.components.post_horizontal')
              @endforeach
          </div>
      </div>
      @endif


      @if(count($other_categories_posts)>0)
      <div class="other_topics_posts">
          <h4 class="pb-3 mb-4 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">From Various Topics</h4>
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

