@extends('frontend.layouts.app')
@section('title',ucfirst($category->name).' - Opined')
@section('description',ucfirst($category->name).' on Opined :'.$category->description)
@section('keywords',ucfirst($category->name))

@push('meta')
<link rel="canonical" href="https://www.weopined.com/topic/{{$category->slug}}" />
<link href="https://www.weopined.com/topic/{{$category->slug}}" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="{{ucfirst($category->name)}} - Opined">
<meta name="twitter:description" content="{{ucfirst($category->name)}} on Opined :{{$category->description}}">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="{{ucfirst($category->name)}} - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/topic/{{$category->slug}}" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="{{ucfirst($category->name)}} on Opined :{{$category->description}}" />
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
<h1>{{ucfirst($category->name)}}
<span class="float-right">
@if(Auth::guest())
<button class="btn btn-outline-success" onclick="openLoginModal();" data-toggle="tooltip" data-placement="left" title="Please Login to Follow {{$category->name}}">Follow</button>
@else
<button id="cf_{{$category->id}}" type="button" class="btn btn-outline-success cf_btn" data-toggle="tooltip" data-placement="left" title="Follow {{$category->name}}" style="display:{{!in_array($category->id,auth()->user()->followed_categories->pluck('category_id')->toArray())?'block':'none'}};">Follow</button>
<button id="cu_{{$category->id}}" type="button" class="btn btn-success cu_btn" style="display:{{in_array($category->id,auth()->user()->followed_categories->pluck('category_id')->toArray())?'block':'none'}};">Following</button>
@endif
</span>
</h1>


<div class="row">
    <div class="col-md-8 col-12">
            @if(count($category_threads)>0)
                    <h5 class="pb-3 my-4 border-bottom font-weight-light text-dark">Latest Threads</h5>
                    <div class="row">
                        @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
                        @foreach($category_threads as $thread)
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-12">
                                <div class="card mb-3 shadow-sm bg-light" id="{{$thread->id}}" style="border:0px;">
                                        <div class="card-body">
                                            <a href="/thread/{{$thread->name}}" class="text-center">
                                                <h5 class="mb-0 text-truncate" style="color:{{ $colors[array_rand($colors,1)] }}">{{'#'.$thread->name}}</h5>
                                                <div class="text-secondary text-center"><small><i class="far fa-comment-alt mr-2"></i>{{ $thread->opinions_count }} Opinions</small></div>
                                            </a>
                                        </div>
                                </div>
                        </div>
                        @endforeach
                    </div>
            @endif
           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            @endif-->
            @if($posts->total()>0)
            <input id="path" type="hidden" value="{{Request::path()}}" />
            <input id="totalpage" type="hidden" value="{{ceil($posts->total()/$posts->perPage())}}" />
            <h5 class="pb-3 mt-5 mb-3 border-bottom font-weight-light text-dark">Latest Articles</h3>
                <div class="row" id="append-div">
                        @include('frontend.posts.mixcards.big_medium_card_sidebar')
                </div>
            @include('frontend.partials.spinner')
            @endif
    </div>
    <div class="col-md-4 col-12" style="position: -webkit-sticky; position: sticky;top: 2vw;">
            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
            <div class="mt-3">
                {!! $google_ad->ad_code !!}
            </div>
            <div class="mt-3">
                    {!! $google_ad->ad_code !!}
            </div>
            @endif
    </div>
</div>


@if($posts->total()>0)
@include('frontend.partials.loadmorescript')
@endif

@endsection

