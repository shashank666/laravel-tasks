@extends('frontend.layouts.app')
@section('title','Polls at Opined - Where Every Opinion Matters !')
@section('description','Opined Polls provides you with the opportunity to submit your opinions on pre-defined polls so that you can also take part in making the difference')
@section('keywords','opinion, opined, polls, weopined, share opinion, opinion poll, share opinion and polls, share political opinion and polls, give voice to your words by opined, social media to share opinion, trending topics, trending stories, trending opinions, opinions that matters, social media, politics, sports, economy, business, entrepreneurship, video opinions, trending')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/polls" />
<link href="https://www.weopined.com/polls" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Vote and Share your opinion on Opined">
<meta name="twitter:description" content="Opined Polls provides you with the opportunity to submit your opinions on pre-defined polls so that you can also take part in making the difference.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Vote and Share your opinion on Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/polls" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Polls provides you with the opportunity to submit your opinions on pre-defined polls so that you can also take part in making the difference." />
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
@endpush

@push('scripts')
 {{--  @if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
  @endif  --}}
<!--<script defer type="text/javascript" src="/js/custom/threads.js?<?php echo time();?>"></script>

<script async src="/js/custom/like.js?<?php echo time();?>" type="text/javascript"></script>
<script async src="/js/custom/delete_short_opinion.js?<?php echo time();?>" type="text/javascript"></script>
<script defer src="/js/custom/profile.js?<?php echo time();?>" type="text/javascript"></script>-->
<script async src="/js/web/home.min.js" type="text/javascript"></script>
<script defer type="text/javascript" src="/js/jquery.caret-atwho.min.js?<?php echo time();?>"></script>
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

  window.onload=function() {
    let add=document.getElementById('addOption');
    let options=document.getElementById('options');
    let optionArr=document.getElementsByClassName('option');

    add.addEventListener('click', function() {
      if(optionArr.length<=3) {
        let len=optionArr.length+1;
        let id="option"+len;
        let optionName="Option "+len;
        options.innerHTML += "<div class='form-group'><label for="+id+">"+optionName+"</label><input type='text' class='form-control option' name='" + id + "' id='" + id + "' placeholder='Eg: Drive Myself'></div>";
      } else {
        alert('Cannot add more than four options')
      }
    })
  }
</script>
@endpush


@section('content')
  <div class="page-title-wrapper">
    <h1 class="page-title">
      <span data-ui-id="page-title-wrapper">Polls at Opined - Where Every Opinion Matters ! Opinion and Polls</span>
    </h1>
  </div>

  <div class="card gradient-card border-0 rounded" style="background: transparent;">
    <div class="card-image" style="background-image: url(https://mdbootstrap.com/img/Photos/Horizontal/Work/4-col/img%20%2814%29.jpg);border-radius: 15px;">
      <div class="card-body text-white d-flex h-100 mask purple-gradient-rgba" style="text-align: justify;    background: linear-gradient( 33deg, rgba(98, 112, 129, 0.87) 10%, rgba(246, 151, 33, 0.87) 90%);border-radius: 15px;">
        Although expressing your opinion through video or text is the best way to raise your voice as it shows sentiments and passion, we understand that sometimes, you just want to share your opinions in binary forms. Opined Polls provides you with the opportunity to submit your opinions on pre-defined polls so that you can also take part in making the difference. After all, Opined is where every opinion matters!
      </div>
    </div>
  </div>

  @auth
    <div class="create-poll my-4" style="display:flex; justify-content:center;">
      <button
        type="button"
        class="btn btn-outline-primary btn-lg"
        data-toggle="modal"
        id="create"
        data-target="#createPollModal"
      >
        Create A Poll
      </button>
    </div>

    <div class="modal fade" id="createPollModal" tabindex="-1" role="dialog" aria-labelledby="createPollModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createPollModalLabel">Create A Poll</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form action="{{ route('createPoll') }}" method="post">
            @csrf
            <div class="form-group">
              <label for="title">Title*</label>
              <input
                type="text"
                class="form-control title"
                name="title"
                id="title"
                placeholder="Enter a title for your poll"
                value="{{old('title')}}"
                required
              >
            </div>
              <div class="form-group">
                <label for="description">Your Question*</label>
                <input
                  type="text"
                  class="form-control description"
                  name="description"
                  id="description"
                  placeholder="Eg: What transport do you use for travelling?"
                  value="{{old('description')}}"
                  required
                >
              </div>
              <div id="options">
                <div class="form-group">
                  <label for="option1">Option 1*</label>
                  <input
                    type="text"
                    class="form-control option"
                    name="option1"
                    id="option1"
                    placeholder="Eg: Public Transport"
                    value="{{old('option1')}}"
                    required
                  >
                </div>

                <div class="form-group">
                  <label for="option2">Option 2*</label>
                  <input
                    type="text"
                    class="form-control option"
                    name="option2"
                    id="option2"
                    placeholder="Eg: Drive Myself"
                    value="{{old('option2')}}"
                    required
                  >
                </div>
              </div>

              <div class="addOption">
                <button type="button" class="btn btn-secondary" id="addOption">+ Add Option</button>  
              </div>

              <div style="display:flex; justify-content:center;">
                <button type="submit" class="btn btn-primary btn-lg">Create</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endauth

  <div class="row mt-2 mb-5">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
       <h4 class="pb-3 mb-3 font-weight-normal" style="color:#495057;border-bottom:1px solid #ff9800;">Latest Polls</h4>
      @if(count($polls)>0)
        <div class="card-columns ">
            @foreach($polls as $index=>$poll)
              @include('frontend.polls.components.poll_cards',['user'=>$poll->user_id])
              {{--  @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                @if(($index%5)==0)
                  <div class="mb-3">
                    {!! $google_native_ad->ad_code !!}
                  </div>
                @endif
              @endif  --}}
            @endforeach
        </div>
      @else
        Sorry, No Polls To Show !
      @endif
    </div>
  </div>

<!-- For trending Polls -->
{{--
  <div class="row mt-2 mb-5">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
      <h4 class="pb-3 mb-3 font-weight-normal" style="color:#244363;border-bottom:1px solid #ff9800;">Trending Polls</h4>
      @if(count($trending_polls)>0)
        <div class="card-columns card-columns-opinion">
        
            @foreach($trending_polls as $index=>$poll)  
              @include('frontend.polls.components.poll_cards',['user'=>$poll->user])
              @if($company_ui_settings->show_google_ad=='1' && $google_native_ad)
                @if(($index%5)==0)
                  <div class="mb-3">
                    {!! $google_native_ad->ad_code !!}
                  </div>
                @endif
              @endif
            @endforeach
        </div>
        @else
          Sorry, No Polls To Show !
        @endif
    </div>
  </div>
--}}       
@endsection
