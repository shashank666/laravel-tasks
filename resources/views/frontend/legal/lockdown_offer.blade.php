@extends('frontend.layouts.app')
@section('title','Win Prizes while Lockdown - Opined')
@section('description','Join the Opined contest by recording your video on Opined App and win amazing prizes.')
@section('keywords','Opined Share Opinion and get Prizes while lockdown')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/legal/lockdown-offer" />
<link href="http://www.weopined.com/legal/lockdown_offer" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Win Prizes while Lockdown - Opined">
<meta name="twitter:description" content="Join the Opined contest by recording your video on Opined App and win amazing prizes.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/img/LockDownContest.png">

<!-- Open Graph data -->
<meta property="og:title" content="Win Prizes while Lockdown - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/legal/lockdown-offer" />
<meta property="og:image" content="http://www.weopined.com/img/LockDownContest.png" />
<meta property="og:description" content="Join the Opined contest by recording your video on Opined App and win amazing prizes." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
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
          <div style="display: -webkit-flex;display: -ms-flex;display: flex; justify-content: center;-ms-align-items: center;align-items: center; ">
            <a href="https://play.google.com/store/apps/details?id=com.app.weopined" target="_blank"><img src="/img/LockDownContest.png" class="img-fluid mb-3 mt-2" style="text-align: center;" /></a>
          </div>
                {!!$company->lockdown_offer!!}
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
