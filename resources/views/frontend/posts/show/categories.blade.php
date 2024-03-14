@extends('frontend.layouts.app')
@section('title','Explore Topics - Opined')
@section('description','Explore various topics on Opined like Business,Current Affairs,Movies,Personalities,Politics,Sports. Follow and Get Updates on Topics of your choices.')
@section('keywords','Explore Topics,Business,Current Affairs,Movies,Personalities,Politics,Sports')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/topics" />
<link href="https://www.weopined.com/topics" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Explore Topics - Opined">
<meta name="twitter:description" content="Explore various topics on Opined like Business,Current Affairs,Movies,Personalities,Politics,Sports. Follow and Get Updates on Topics of your choices.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Explore Topics - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/topics" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="Explore various topics on Opined like Business,Current Affairs,Movies,Personalities,Politics,Sports. Follow and Get Updates on Topics of your choices." />
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
    <h1 class="mb-4">Explore Topics</h1>
    @foreach($categories_result as $category)
{{--      <h3 class="pb-3 mt-5 mb-4 border-bottom">{{$category['category_group']}}</h3>--}}
    <div class="card-columns">
        @php($heights=[200,250,300,340])
        @php($fontsizes=[20,28,36,40])

            @foreach($category['category_by_group'] as $index=>$single_category)

                <a href="/topic/{{$single_category['slug']}}">
                    <div class="card shadow-sm border-0">
                        <img class="card-img" src="{{$single_category['image']}}" width="700" height="{{  $heights[array_rand($heights,1)] }}" alt="{{$single_category['name']}}"/>
                        <div class="card-img-overlay d-flex flex-column justify-content-center align-items-center p-2" style="background-color:rgba(0,0,0,0.65)">
                            <p class="card-title text-white text-center font-weight-bolder" style="font-size:{{  $fontsizes[array_rand($fontsizes,1)].'px' }}">{{$single_category['name']}}</p>
                            @if(Auth::guest())
                                <button class="btn btn-sm btn-outline-success" onclick="event.preventDefault();openLoginModal();" data-toggle="tooltip" data-placement="top" title="Please Login To Follow {{$single_category['name']}}" style="width:150px;">Follow</button>
                                @else
                                <button id="cf_{{$single_category['id']}}" type="button" class="btn btn-sm btn-outline-success cf_btn" data-toggle="tooltip" data-placement="bottom" title="Follow {{$single_category['name']}}" style="width:150px;display:{{!in_array($single_category['id'],auth()->user()->followed_categories->pluck('category_id')->toArray())?'inline':'none'}};">Follow</button>
                                <button id="cu_{{$single_category['id']}}" type="button" class="btn btn-sm btn-success cu_btn" style="width:150px;display:{{in_array($single_category['id'],auth()->user()->followed_categories->pluck('category_id')->toArray())?'inline':'none'}};">Following</button>
                                @endif
                        </div>
                    </div>
                </a>

                @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                    @if($index%4==0)
                        <div class="mb-3">
                        {!!  $google_ad->ad_code!!}
                        </div>
                    @endif
                @endif
            @endforeach


    </div>
    @endforeach
@endsection





