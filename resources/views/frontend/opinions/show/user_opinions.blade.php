@extends('frontend.layouts.app')
@section('title',$profile_user->name."'s Opinions - Opined")
@section('description','This is the list of articles that you have published on Opined.')
@section('keywords','My Articles,Published Articles')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/opinions" />
<link href="https://www.weopined.com/me/opinions" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{$profile_user->name}}'s Opinions - Opined">
<meta name="twitter:description" content="This is the list of opinions that you have published on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{$profile_user->name}}'s Opinions - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/opinions" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="This is the list of opinions that you have published on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('scripts')
 <script type='text/javascript' src='/js/custom/delete.js'></script>

 @if($company_ui_settings->show_google_ad=='1')
 {!!  $company_ui_settings->google_adcode!!}
 @endif
@endpush



@section('content')
<h1 class="mb-5">{{$profile_user->name}}'s Opinions</h1>
    <div class="row">
        
             
    @if(count($posts)>0)
    <input id="path" type="hidden" value="{{Request::path()}}" />
    <input id="totalpage" type="hidden" value="{{ceil($posts->total()/$posts->perPage())}}" />

    <div class="row">
        <div class="col md-0">
            <div class="card-columns" id="append-div">
            @foreach($posts as $opinion)
                @include('frontend.opinions.components.profile_opinion_card',['user'=>$opinion->user])
            @endforeach
            </div>
            @include('frontend.partials.spinner')
        </div>
    </div>
    @include('frontend.opinions.crud.delete')
    @include('frontend.partials.loadmorescript')
@endif
@endsection

