@extends('frontend.layouts.app')
@section('title','Offer - Opined')
@section('description','Offer of RSM')
@section('keywords','Opined Offer of RSM')

@push('meta')
<link rel="canonical" href="https://weopined.com/legal/offer-rsm" />
<link href="https://weopined.com/legal/offer-rsm" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Offer of RSM - Opined">
<meta name="twitter:description" content="Opined Offer of RSM">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Offer of RSM - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="https://weopined.com/legal/offer-rsm" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Offer of RSM" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
<style type="text/css">
  .change-color {
    -webkit-animation:colorchange 20s infinite alternate;
      
      
    }

    @-webkit-keyframes colorchange {
      0% {
        
        color: blue;
      }
      
      10% {
        
        color: #8e44ad;
      }
      
      20% {
        
        color: #1abc9c;
      }
      
      30% {
        
        color: #d35400;
      }
      
      40% {
        
        color: blue;
      }
      
      50% {
        
        color: #34495e;
      }
      
      60% {
        
        color: blue;
      }
      
      70% {
        
        color: #2980b9;
      }
      80% {
     
        color: #f1c40f;
      }
      
      90% {
     
        color: #2980b9;
      }
      
      100% {
        
        color: pink;
      }
    }
</style>
@endpush


@push('scripts')
 @if($company_ui_settings->show_google_ad=='1')
 {!!  $company_ui_settings->google_adcode!!}
 @endif
@endpush

@section('content')
       <div class="row">
        <div class="col-md-2">
                   <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        {!! $google_ad->ad_code !!}
                    @endif-->
        </div>
        <div class="col-xl-8 col-lg-8 col-md-8  col-sm-12 col-12" style="background: white;box-shadow: 5px 5px #f1eded;">
          <img src="/img/offer-rsm.png" class="img-fluid mb-3"/>
                {!!$company->offer!!}
                @if($offer_remain<100)
                <h3 style="text-align: center;"><span class="change-color">Hurry Up! </span></br>Only <span style="color: #ff9800">{{$offer_remain}}</span> articles remain.</h3>
                @endif
               <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        {!! $google_ad->ad_code !!}
                 @endif-->
        </div>
        <div class="col-md-2">
               <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                    {!! $google_ad->ad_code !!}
                @endif-->
        </div>
    </div>
@endsection
