<!DOCTYPE html>
<html
    xmlns="https://www.w3.org/1999/xhtml"
    xml:lang="en" lang="{{ app()->getLocale()}}" dir="ltr"
    xmlns:og="https://ogp.me/ns#"
    xmlns:fb="https://www.facebook.com/2008/fbml">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv = "Content-Type" content = "text/html;charset=UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" href="https://www.weopined.com/favicon.png">

    <title>{{ 'Opinion by '.$opinion->user['name'].' | Opined'}}</title>
    <meta name="description" content="Read opinion of {{ $opinion->user['name'] }} on Opined" />

    <meta name="copyright" content="Copyright &copy; {{ Carbon\Carbon::now()->format('Y') }} www.weopined.com , All Rights Reserved"/>
    <meta name = "revised" content = "Opined, {{ Carbon\Carbon::now('Asia/Kolkata') }}" />
    <meta name="robots" content="index,follow"/>
    <meta name="generator" content="Opined" />

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="opined">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style"  content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="opined">
    <link rel="apple-touch-icon" href="https://www.weopined.com/favicon.png">
    <link rel="apple-touch-startup-image" href="https://www.weopined.com/favicon.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="https://www.weopined.com/favicon.png">
    <meta name="msapplication-TileColor" content="#fff">
    <meta name="theme-color" content="#fff">
    <meta name="msapplication-navbutton-color" content="#fff">

    <link href="https://www.weopined.com/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="https://www.weopined.com/css/custom/main.min.css?<?php echo time();?>" rel="stylesheet" type="text/css"/>

    <script type="text/javascript" src="https://www.weopined.com/js/web/core.min.js"></script>
    <script type="text/javascript" src="https://www.weopined.com/js/custom/main.js?<?php echo time();?>"></script>
    <script>
    $(document).ready(function(){
        $('a').click(function(e){
            e.preventDefault();
            window.open($(this).attr("href"), '_blank');
        });
    });
    </script>

    <link rel="canonical" href="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" />
    <link href="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" rel="alternate" reflang="en" />

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{$opinion->user['name'].' | Opined'}}">
    <meta name="twitter:description" content="{{strip_tags($opinion->body)}}">
    <meta name="twitter:creator" content="@weopined">
    <meta property="twitter:image" content="{{$opinion->cover}}" />
    <meta name="twitter:image" content="http://www.weopined.com/favicon.png">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{$opinion->user['name'].' | Opined '}}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" />
	<!--<meta property="og:video" content="{{$opinion->cover}}" />
	<meta property="og:video:secure_url" content="{{$opinion->cover}}" />
	<meta property="og:video:type" content="video/mp4" />
	<meta property="og:video:width" content="400" />
	<meta property="og:video:height" content="300" />-->
	<meta property="og:image" content="{{$opinion->cover}}" />
	<meta property="og:image" content="{{$opinion->thumbnail}}" />
	<meta property="og:description" content="{{strip_tags($opinion->body)}}" />
    <meta property="og:site_name" content="Opined" />
    <meta property="og:locale" content="en_US" />
    <meta property="fb:app_id" content="1766000746745688" />

</head>
<body>
    <main>
            <div class="card shadow-sm m-2" id="opinion_{{$opinion->id}}">
                    <div class="card-header bg-white border-bottom-0">
                            <div class="media align-items-center">
                                <a href="{{ route('user_profile', ['username' => $opinion->user['username']]) }}" data-toggle="tooltip" data-placement="right" title="Go to the profile of {{ucfirst( $opinion->user['name'])}}"><img class="rounded-circle" src="{{ $opinion->user['image']}}" height="40" width="40" alt="{{ucfirst( $opinion->user['name'])}}" onerror="this.onerror=null;this.src='/img/avatar.png';"/></a>
                                 <div class="media-body">
                                    <div class="d-flex justify-content-between align-items-bottom w-100">
                                         <span class="ml-2"><a href="{{ route('user_profile', ['username' =>  $opinion->user['username']]) }}">{{ucfirst( $opinion->user['name'])}}</a></span>
                                         <span class="text-secondary">
                                         <small style="cursor:default;" data-toggle="tooltip" data-placement="top" title="{{Carbon\Carbon::parse($opinion->created_at)->toDayDateTimeString()}}">
                                            {{Carbon\Carbon::parse($opinion->created_at)->toFormattedDateString()}}
                                          </small>
                                         </span>
                                    </div>
                                 </div>
                        </div>
                    </div>
                    <div class="card-body py-0">
                        <p>{!!$opinion->body!!}</p>
                          @include('frontend.opinions.components.thread_link_card')

                        @if($opinion->cover_type!="none")
                        <div class="row">
                            <div class="col-12">
                                @if($opinion->cover_type=='YOUTUBE')
                                <div class="embed-responsive embed-responsive-4by3">
                                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$opinion->cover}}"></iframe>
                                </div>

                                @elseif($opinion->cover_type=='EMBED')
                                <div class="embed-responsive embed-responsive-4by3">
                                <iframe class="embed-responsive-item" srcdoc="{{$opinion->cover}}"></iframe>
                                </div>

                                @elseif($opinion->cover_type=='GIF')
                                <img class="img-fluid rounded" src="{{$opinion->cover}}" height="auto" width="auto"/>
                                @else

                                    @php($arr=explode(',',$opinion->cover))
                                    @if(count($arr)>1)
                                        <div id="carouselIndicators-{{$opinion->id}}" class="carousel slide" data-ride="carousel">
                                            <ol class="carousel-indicators">
                                                @foreach(explode(',',$opinion->cover) as $index=>$image)
                                                <li data-target="#carouselIndicators-{{$opinion->id}}" data-slide-to="0" class="{{$index==0?'active':''}}"></li>
                                                @endforeach
                                            </ol>

                                            <div class="carousel-inner">
                                            @foreach(explode(',',$opinion->cover) as $index=>$image)
                                                <div class="carousel-item {{$index==0?'active':''}}">
                                                    <img class="d-block w-100 rounded" src="{{$image}}" height="350"/>
                                                </div>
                                            @endforeach
                                            </div>

                                            <a class="carousel-control-prev" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span></a>
                                            <a class="carousel-control-next" href="#carouselIndicators-{{$opinion->id}}" role="button" data-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span></a>
                                        </div>
                                    @else
                                        <img class="img-fluid rounded" src="{{$opinion->cover}}" height="auto" width="auto"/>
                                    @endif

                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                     <div class="card-footer bg-white border-top-0">
                     <div class="like_comment_div">
                          <span class="mr-4" data-toggle="tooltip" data-placement="top" title="Please Login To Like"><i class="far fa-thumbs-up mr-1" style="color:#495057;font-size:20px;cursor:pointer"></i>
                                <span class="align-top"  style="color:#495057;">
                                {{$opinion->likesCount}}
                                </span>
                          </span>
                          <span class="mr-4" data-toggle="tooltip" data-placement="top" title="Please Login To Comment"  data-showcomment="{{$opinion->id}}" ><i class="far fa-comments mr-1" style="color:#00acc1;font-size:20px;cursor:pointer"></i>
                                <span class="align-top" style="color:#00acc1;">
                                {{$opinion->commentsCount}}
                                </span>
                          </span>

                        <span class="dropdown-toggle share-opinion float-right" id="share-{{$opinion->id}}" data-toggle="dropdown"  style="font-size:18px;color:#28A755;" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-share-alt mr-1" data-toggle="tooltip" data-placement="top" title="Share this Opinion"></i>{{$opinion->sharesCount}}
                        </span>
                        <div class="dropdown-menu share-menu dropdown-menu-right" id="share-menu-{{$opinion->id}}">
                            @if(Auth::user())
                                <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#3b5998"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}&via=weopined" style="color:#1da1f2"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#0077b5"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#128c7e"  data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                                <a class="embed-opinion dropdown-item sharethis" href="javascript:void(0);" data-url="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" style="color:#37474f" data-opinion="{{$opinion->id}}" data-user="{{Auth::user()->id}}" data-plateform="EMBED"><i class="fas fa-code mr-2" style="font-size:14px"></i>Embed</a>
                        @else
                                <a class="dropdown-item sharethis" target="_blank" href="https://facebook.com/sharer/sharer.php?u=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#3b5998"  data-opinion="{{$opinion->id}}" data-plateform="FACEBOOK"><i class="fab fa-facebook mr-2"></i>Share On Facebook</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://twitter.com/share?url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}&via=weopined" style="color:#1da1f2"  data-opinion="{{$opinion->id}}" data-plateform="TWITTER"><i class="fab fa-twitter mr-2"></i>Share On Twitter</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#0077b5"  data-opinion="{{$opinion->id}}" data-plateform="LINKEDIN"><i class="fab fa-linkedin mr-2"></i>Share On Linkedin</a>
                                <a class="dropdown-item sharethis" target="_blank" href="https://api.whatsapp.com/send?&text=http://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid}}" style="color:#128c7e"  data-opinion="{{$opinion->id}}" data-plateform="WHATSAPP"><i class="fab fa-whatsapp mr-2"></i>Share On Whatsapp</a>
                                <a class="embed-opinion dropdown-item sharethis" href="javascript:void(0);" data-url="https://www.weopined.com/{{ '@'.$opinion->user['username'].'/opinion/'.$opinion->uuid.'/share'}}" style="color:#37474f" data-opinion="{{$opinion->id}}" data-plateform="EMBED"><i class="fas fa-code mr-2" style="font-size:14px"></i>Embed</a>
                        @endif
                        </div>
                    </div>

                     </div>
            </div>
    </main>
</body>
</html>
