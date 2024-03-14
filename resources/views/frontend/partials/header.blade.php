<style type="text/css">
  
 



</style>



<head>
<link rel="stylesheet" href="{{ URL::asset('css/app.css') }}" />
</head>


<script>
$(function(){
  $('a').each(function() {
    if ($(this).prop('href') == window.location.href) {
      $(this).addClass('current');
    }
  });
});
</script>

<div class="fixed-top bg-white shadow-sm" style="background: black; margin-top: -15px; height: 69px">
   <!-- <header class="container blog-header py-3">-->
    <header class="container py-3">
        <div class="row flex-nowrap justify-content-between align-items-center row-mob">
            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-4 col-4 col-mob">
                <a class="sidebar-toggle text-muted d-md-none d-sm-inline d-inline" href="javascript:void(0);"  title="Site Navigation" style="padding:8px;margin-right:16px;">
                    <i class="fas fa-bars" style="color: black!important;"></i>
                </a>
                <a class="blog-header-logo text-dark" href="/" title="Opined">
                  <div class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
                    <img src="/img/logo.png" id="logo" height="37px;" width="115px;" style="margin-top: 7px;" alt="Opined">
                  </div>
                  <div class="d-md-none d-sm-inline d-inline">
                    <img src="/img/Mobile-opined.png" id="logo" height="37px;" width="37px;" style="margin-top: 7px; margin-left: -12px;" alt="Opined">
                  </div>
                </a>
            </div>
            <div class="col-xl-7 col-lg-7 col-md-7 col-sm-8 col-8 d-flex justify-content-end align-items-center " style="margin-top: 5px;">
                
                @if (Auth::user())
               
              <div class="d-md-none d-sm-inline d-inline">
                <div class="btn-group">
                <a class="text-secondary mr-2 menubtn" style="font-size: 12px;data-toggle=modal" href="/feed" title="My Feed">Feed</a>
                <a class="text-secondary mr-2 menubtn" style="font-size: 12px; data-toggle=modal" href="/home" title="Topics on Opined">Explore</a>
                  {{--  <a class="text-secondary mr-3 menubtn" style="font-size: 12px; data-toggle=modal" href="/article" title="Get Articles on Opined">Articles</a>  --}}
                  <a class="text-secondary mr-3 menubtn" style="font-size: 12px; data-toggle=modal" href="/polls" title="Vote Polls on Opined">Polls</a>
                  {{--  <a class="text-secondary mr-3 menubtn contest-mob" style="font-size: 12px; data-toggle=modal" href="/contest" title="Checkout Contest on Opined">Contest</a>  --}}
                </div>
              </div>
              @php
              $extn = preg_match('/\./', Auth::user()->image) ? preg_replace('/^.*\./', '', Auth::user()->image) : '';
              $path=Auth::user()->image;
            $string="/profile";
            $substring="avatar_thumb"
              @endphp
                    <a  style="margin-top: 3px; text-decoration:none;" href="{{ route('profile') }}" title="My Profile" >
                        <span class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none"><img class ="img-mob" src="@php
    if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
    @endphp
        {{preg_replace('/.[^.]*$/', '',Auth::user()->image).'_40x40'.'.'.$extn}}
    @php
    }
    else{
    @endphp
    {{Auth::user()->image}}
    @php
}
    @endphp" alt="{{ucfirst(Auth::user()->name)}}" width="40" onerror="this.onerror=null;this.src='/img/avatar_thumb.png';" style="margin-top: -6px;border-radius: 50%!important;"></span>
                        <span class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn-name" >{{ucfirst(Auth::user()->name)}}</span>
                     </a>

                <a class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn" style="text-decoration:none;  data-toggle=modal" href="/feed" title="My Feed">My Feed</a>
                <a class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn data-toggle=modal" href="/home" title="Topics on Opined" style="text-decoration:none;">Explore</a>
                  {{--  <a class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn data-toggle=modal" href="/article" title="Get Articles on Opined" style="text-decoration:none;">Articles</a>  --}}
                  <a class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn data-toggle=modal" href="/polls" title="Vote Polls on Opined" style="text-decoration:none;">Polls</a>
                  {{--  <a class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3 menubtn data-toggle=modal" href="/contest" title="Checkout Contest on Opined" style="text-decoration:none;">Contest</a>  --}}

                @else
                  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3 font-change" href="/login_form" title="Login To Opined" style="border: none;background: none;"></i>Login</a>
                  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3"   href="/register_form" title="Get Started With Opined" style="border: none;background: none;">Get Started</a>
                  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3" href="/home" title="Topics on Opined" style="border: none;background: none;"></i>Explore</a>
                  {{--  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3" href="/article" title="Get Articles on Opined" style="border: none;background: none;">Articles</a>  --}}
                  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3" href="/polls" title="Vote Polls on Opined" style="border: none;background: none;">Polls</a>
                  {{--  <a class="btn btn-sm btn-outline-light d-md-inline d-sm-none d-none mr-3" href="/contest" title="Checkout Contest on Opined" style="border: none;background: none;">Contest</a>  --}}

                <div class="d-md-none d-sm-inline d-inline">
                  <div class="btn-group">
                    {{-- <a class="btn btn-sm btn-outline-light" href="/login_form" title="Login To Opined" style="border: none;background: none;"></i>Login</a>
                    <a class="btn btn-sm btn-outline-light" href="/register_form" title="Create New Account" style="border: none;background: none;"></i>Get Started</a> --}}
                  <a class="btn btn-sm btn-outline-light" href="/home" title="Topics on Opined" style="border: none;background: none;"></i>Explore</a>
                  {{--  <a class="btn btn-sm btn-outline-light mr-2" href="/article" title="Get Articles on Opined" style="border: none;background: none;">Articles</a>  --}}
                  <a class="btn btn-sm btn-outline-light mr-2" href="/polls" title="Vote Polls on Opined" style="border: none;background: none;">Polls</a>
                </div>
               </div>
                @endif
                
                  <a class="text-muted" href="#searchModal" data-toggle="modal" data-target="#searchModal" title="Search Opined" style="padding:8px;margin-right:8px;">
                    <i class="fas fa-search" style="color: #666666;width: 13px;height: 15px;;margin-left: -11px;"></i>
                </a>
                
              
                 @if($company_ui_settings->invite_btn==1)
                    <a class="text-muted d-md-inline d-sm-none d-none" href="#invitePeople" data-toggle="modal" data-target="#invitePeople" title="Invite People" style="padding:8px;margin-right:8px;">
                        <i class="far fa-paper-plane" style="color: #666666; width: 13px;height: 15px;margin-left: -11px;"></i>
                    </a>

                @endif
                @if (Auth::user())
                <ul class="navbar-nav d-md-inline d-sm-none d-none">
                  <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted" style="padding:8px;margin-right:8px;" id="notificationDropdown" role="button" data-toggle="dropdown" title="Notification" aria-haspopup="true" aria-expanded="false">
                    <i class="far fa-bell" style="color: #666666; width: 13px;height: 15px;margin-left: -11px;cursor:pointer;"></i>
                        @if(Auth::user()->unreadNotifications->count()>0)
                        <span class="badge badge-primary badge-pill align-top" id="notificationsCount">{{Auth::user()->unreadNotifications->count() }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationDropdown" style="width: 320px;margin:0px;padding:0px;">
                          <div class="heading px-2 py-2">
                          <span style="text-align:left;font-size:14px;">Recent Notifications</span>
                          </div>
                          <div class="dropdown-content-body">
                                @include('frontend.partials.notifications')
                          </div>
                    </div>
                    </li>
                </ul>
                <div class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" style="margin-top: 3px;" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More">
                        <!--
                         <span class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none"><img src="{{Auth::user()->image!==null?Auth::user()->image:'/storage/profile/avatar.jpg'}}" alt="{{ucfirst(Auth::user()->name)}}" height="32" width="32" class="rounded-circle"></span>
                     
                         <span class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none"><img src="{{Auth::user()->image!==null?Auth::user()->image:'/storage/profile/avatar.jpg'}}" alt="{{ucfirst(Auth::user()->name)}}" height="25" width="25" style="margin-top: -6px;border-radius: 45%!important;"></span>
                        <span class="d-xl-inline d-lg-inline d-md-inline d-sm-none d-none text-secondary mr-3" style="font-size: 20px;color: white !important;">{{ucfirst(Auth::user()->name)}}</span>
-->
                        <i class="fas fa-caret-down" style="color: #666666;width: 23px;height: 23px;margin-top: -4px;margin-left: -7px;"></i>
                        </a>
                        
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <!--<a class="dropdown-item p-2 header-menu-link" href="{{ route('opinions') }}" title="My Articles"><span  style="margin-right:12px;"><i class="fas fa-file-alt"></i></span>My Articles</a>
-->
                        {{--  <a class="dropdown-item p-2 header-menu-link" href="{{ route('bookmarks') }}" title="Bookmarks"><span style="margin-right:12px;"><i class="fas fa-bookmark"></i></span>Bookmarks</a>  --}}
                        {{--@if(Auth::user() && Auth::user()->registered_as_writer==1)
                        <a class="dropdown-item p-2 header-menu-link" href="{{ route('myallarticles') }}" title="My Articles"><span  style="margin-right:12px;"><i class="far fa-clipboard"></i></span>My Articles</a>
                        @endif
                        --}}
                        
                        <a class="dropdown-item p-2 header-menu-link d-md-none d-sm-block d-block" href="{{ route('notifications') }}" title="Notifications"><span style="margin-right:12px;"><i class="fas fa-bell"></i></span>Notifications</a>
                        <!--
                        <a class="dropdown-item p-2 header-menu-link" href="{{ route('profile') }}" title="Profile"><span  style="margin-right:9px;"><i class="fas fa-user"></i></span>Profile</a>-->
                        {{--  @if(Auth::user() && Auth::user()->registered_as_writer==0)
                        <a class="dropdown-item p-2 header-menu-link" href="{{ route('writer_terms') }}" title="Register as Writer"><span style="margin-right:8px;"><i class="fas fa-user-shield"></i></span>Register as Writer</a>
                        @endif  --}}

                        {{--  @if(Auth::user() && Auth::user()->registered_as_writer==1)
                        <a class="dropdown-item p-2 header-menu-link" href="{{ route('article_corner') }}" title="Article Performance"><span style="margin-right:8px;"><i class="far fa-clipboard"></i><!--<i class="fas fa-chart-bar"></i>--></span>Writer's Corner</a>
                        @endif  --}}

                        <a class="dropdown-item p-2 header-menu-link" href="{{ route('settings') }}" title="Settings"><span  style="margin-right:9px;"><i class="fas fa-cog"></i></span>Settings</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item p-2 header-menu-logout-link" href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><span  style="margin-right:9px;"><i class="fas fa-sign-out-alt"></i></span>Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            {{ csrf_field() }}
                        </form>

                        </div>
                    </li>
                </ul>
              </div>
                @endif
            </div>
        </div>
    </header>

    @if(!in_array(Request::path(),['opinion/write','me/profile','me/settings','me/profile/edit','me/circle','me/in_circle']) && (substr(Request::path(),0,1)!=='@') && (!Request::is('opinion/edit/*')))
    <!--<div class="container nav-scroller py-1 mb-2">
        <nav class="nav d-flex justify-content-between font-weight-normal">
            <a class="p-2 text-secondary" href="/home" title="Home">Explore</a>
            <a class="p-2 text-secondary" href="/latest" title="Latest Articles">Latest</a>
            <a class="p-2 text-secondary" href="/trending" title="Trending Articles">Trending</a>
            <a class="p-2 text-secondary" href="/mostliked" title="Most Liked Articles">Most Liked</a>

            @if(count($categories)>6)
                @for($i=0;$i<6;$i++)
            <a class="p-2 text-secondary" href="/topic/{{$categories[$i]->slug}}" title="{{ucfirst($categories[$i]->name)}}">{{ucfirst($categories[$i]->name)}}</a></li>
                @endfor
            @else
                @for($i=0;$i<count($categories);$i++)
            <a class="p-2 text-secondary" href="/topic/{{$categories[$i]->slug}}" title="{{ucfirst($categories[$i]->name)}}">{{ucfirst($categories[$i]->name)}}</a></li>
                @endfor
            @endif
           <a class="p-2 text-secondary" href="/topics" title="Explore Topics">More Topics</a></li>
        </nav>
    </div>-->
    @endif
    @include('frontend.partials.sidebar')
</div>
<!--
<script>
// When the user scrolls down 50px from the top of the document, resize the header's font size
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
    
        var myImg = document.getElementById("logo");
        var currWidth = myImg.clientWidth;
        
            myImg.style.width = (currWidth - 50) + "px";
        

  } else {
     myImg.style.width = (currWidth + 50) + "px";
  }
}
</script>
-->