@extends('frontend.layouts.app')
@section('title','Copyright Policy - Opined')
@section('description','Opined Copyright Policy')
@section('keywords','Opined Copyright Policy')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/legal/copyright_policy" />
<link href="http://www.weopined.com/legal/copyright_policy" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Copyright Policy - Opined">
<meta name="twitter:description" content="Opined Copyright Policy">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Copyright Policy - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/legal/copyright_policy" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Copyright Policy" />
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
                    <!--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
                        {!! $google_ad->ad_code !!}
                    @endif-->
        </div>
        <div class="col-xl-8 col-lg-8 col-md-8  col-sm-12 col-12" style="background: white;box-shadow: 5px 5px #f1eded;">
                {!!$company->copyright_policy!!}
           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                <div class="mt-3">{!! $google_ad->ad_code !!}</div>
            @endif-->
        </div>
        <div class="col-md-2">
                <!--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
                    {!! $google_ad->ad_code !!}
                @endif-->
        </div>

    </div>
@endsection
