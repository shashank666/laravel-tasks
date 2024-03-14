@extends('frontend.layouts.app')
@section('title','Terms Of Service - Opined')
@section('description','Opined Terms Of Service')
@section('keywords','Opined Terms Of Service')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/legal/terms_of_service" />
<link href="http://www.weopined.com/legal/terms_of_service" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Terms Of Service - Opined">
<meta name="twitter:description" content="Opined Terms Of Service">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Terms Of Service - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/legal/terms_of_service" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Terms Of Service" />
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
            <div class="offset-xl-2 col-xl-8  offset-lg-2 col-lg-8 offset-md-2 col-md-8  col-sm-12 col-12" style="background: white;box-shadow: 5px 5px #f1eded;">
                {!!$company->terms_of_service!!}
               <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                    <div class="mt-3">
                        {!! $google_ad->ad_code !!}
                        {!! $google_ad->ad_code !!}
                        {!! $google_ad->ad_code !!}
                    </div>
                @endif-->
        </div>
    </div>
@endsection
