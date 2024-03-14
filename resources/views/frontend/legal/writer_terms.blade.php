@extends('frontend.layouts.app')
@section('title',"Writter's Terms - Opined")
@section('description',"Writter's Terms")
@section('keywords',"Writter's Terms")

@push('meta')
<link rel="canonical" href="http://www.weopined.com/legal/writer_terms" />
<link href="http://www.weopined.com/legal/writer_terms" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Writter's Terms - Opined">
<meta name="twitter:description" content="Opined Writter's Terms">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Writter's Terms - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="http://www.weopined.com/legal/writer_terms" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Opined Writter's Terms" />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@if(Auth::user() && Auth::user()->registered_as_writer==0)
@push('scripts')

 @if($company_ui_settings->show_google_ad=='1')
 {!!  $company_ui_settings->google_adcode!!}
 @endif

<script>
    $(document).on('click','#btn-agree',function(){
        if($('#agree').is(':checked')){
            window.location.href='/me/payment';
        }else{
            $('.invalid-feedback').css('display','block');
        }
    });

    $(document).on('change','#agree',function(){
        if($('#agree').is(':checked')){
            $('.invalid-feedback').css('display','none');
            $('#btn-agree').removeAttr('disabled');
        }else{
            $('.invalid-feedback').css('display','block');
            $('#btn-agree').attr('disabled','disabled');
        }
    });
</script>
@endpush
@endif

@section('content')
    <div class="row mt-4">
        <div class="offset-xl-2 col-xl-8  offset-lg-2 col-lg-8 offset-md-2 col-md-8  col-sm-12 col-12 text-justify" style="background: white;box-shadow: 5px 5px #f1eded;">
            {!!$company->writers_terms!!}
            <!--@if($company_ui_settings->show_google_ad=='1' && $google_ad)
                <div class="mt-3">{!! $google_ad->ad_code !!}</div>
            @endif-->
        </div>
    </div>
    <hr>

    <div class="row mt-4">
        <div class="offset-md-2 col-md-8 col-12">
            @if(Auth::user()  && Auth::user()->registered_as_writer==0)
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="agree" required>
                <label class="form-check-label" for="agree">I have carefully read and agreed to Opined Writer&apos;s Terms.</label>
                <div class="invalid-feedback">
                    please select the checkbox
                </div>
            </div>
            <button type="button" class="btn btn-primary btn-block" id="btn-agree" disabled>NEXT</button>
            @endif
            @if(Auth::guest())
            <button type="button" class="btn btn-primary btn-block" onclick="openLoginModal();">NEXT</button>
            @endif
           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                <div class="mt-3">{!! $google_ad->ad_code !!}</div>
            @endif-->
        </div>
    </div>

@endsection


