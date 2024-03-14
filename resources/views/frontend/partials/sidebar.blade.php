<div class="sidebar-overlay"></div>
    <aside id="sidebar" class="sidebar sidebar-fixed-left" role="navigation">
            <div class="sidebar-header header-cover bg-dark">
                <div class="top-bar"></div>
                <div class="sidebar-image">
                    @if(Auth::user())
                      <img src="{{Auth::user()->image!==null?Auth::user()->image:'/storage/profile/avatar.jpg'}}" alt="{{ucfirst(Auth::user()->name)}}">
                    @else
                      <img src="/favicon.png">
                    @endif
                </div>
                @if(Auth::user())
                <a class="sidebar-brand"  href="{{ route('profile') }}">{{ucfirst(Auth::user()->name)}}</a>
                @else
                <a class="sidebar-brand" data-toggle="modal"  href="#loginModal">Welcome To Opined</a>
                @endif
       
         </div>
            <!-- Sidebar navigation -->
            <ul class="nav sidebar-nav">
                @if(Auth::guest())
                <li><a href="{{ route('login_form') }}" title="Login"><i class="sidebar-icon fas fa-sign-in-alt"></i>Login To Opined</a></li>
                <li><a href="{{ route('register_form') }}" title="Create Account"><i class="sidebar-icon fas fa-user-circle"></i>Create Account</a></li>
                @endif

                <!--<li><a href="/" title="Home"><i class="sidebar-icon fas fa-home"></i>Home</a></li>-->
                @if($company_ui_settings->invite_btn==1)
                <li><a href="#invitePeople" data-toggle="modal" data-target="#invitePeople" title="Invite People"><i class="sidebar-icon fas fa-paper-plane"></i>Invite People</a></li>
                @endif
                 @if (Auth::user())
                <li>
                    <a  href="{{ route('notifications') }}" title="Notifications">
                        <i class="sidebar-icon fas fa-bell"></i>Notifications
                        @if(Auth::user()->unreadNotifications->count()>0)
                        <span class="sidebar-badge badge-circle">{{Auth::user()->unreadNotifications->count()}}</span>
                        @endif
                    </a>
                </li>
                @endif
                <!--<li><a href="/latest" title="Latest Articles"><i class="sidebar-icon fas fa-fire"></i>Latest</a></li>
                <li><a href="/trending" title="Trending Articles"><i class="sidebar-icon fas fa-chart-line"></i>Trending</a></li>
                <li><a href="/mostliked" title="Most Liked Articles"><i class="sidebar-icon fas fa-thumbs-up"></i>Most Liked</a></li>
                <li><a href="/topics"  title="Topics"><i class="sidebar-icon fas fa-list"></i>Topics</a></li>    
                <li><a href="/threads" title="Threads"><i class="sidebar-icon fas fa-tag"></i>Threads</a></li> -->   

            @if (Auth::user())
                <!--<li><a href="{{ route('write') }}" title="Write an Article" class="write_opinion_link"><i class="sidebar-icon fas fa-pencil-alt"></i>Write an Article</a></li>
                {{--  <li><a href="{{ route('opinions') }}" title="My Articles"><i class="sidebar-icon fas fa-file-alt"></i>My Articles</a></li>-->  --}}
                {{--  @if(Auth::user()->registered_as_writer==1)        
                <li><a href="{{ route('drafts') }}" title="Drafts"><i class="sidebar-icon far fa-clipboard"></i>Drafts</a></li>
                @endif  --}}
                {{--  <li><a href="{{ route('bookmarks') }}" title="Bookmarks"><i class="sidebar-icon fas fa-bookmark"></i>Bookmarks</a></li>  --}}
              <!--  -->
                @if(Auth::user()->registered_as_writer==0)
                <li><a href="{{ route('writer_terms') }}" title="Register as Writer"><i class="sidebar-icon fas fa-user-shield"></i>Register as Writer</a></li> 
                @endif
                {{--  @if(Auth::user()->registered_as_writer==1)
                <li><a href="{{ route('stats') }}" title="Article Performance"><i class="sidebar-icon fas fa-chart-bar"></i>Article Performance</a></li> 
                @endif  --}}
               <!-- <li><a  href="{{ route('profile') }}" title="Profile"><i class="sidebar-icon fas fa-user"></i>Profile</a></li>-->
                <li><a  href="{{ route('settings') }}" title="Settings"><i class="sidebar-icon fas fa-cog"></i>Settings</a></li>
                @endif
                <li class="nav-item dropdown" style="padding-left: 5%">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More"><i class="idebar-icon fa fa-bullhorn mr-4"></i>
                        Policies
                        </a>
                        
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <!--<a class="dropdown-item p-2 header-menu-link" href="{{ route('opinions') }}" title="My Articles"><span  style="margin-right:12px;"><i class="fas fa-file-alt"></i></span>My Articles</a>
-->
                        <a class="dropdown-item p-2" href="{{ route('privacy_policy') }}" title="Privacy Policy"><span style="margin-right:12px;"></span>Privacy Policy</a>
                        <a class="dropdown-item p-2" href="{{ route('terms_of_service') }}" title="Terms of Service"><span style="margin-right:12px;"></span>Terms of Service</a>

                        </div>
                    </li>
                <li><a href="{{ route('contactus') }}" title="Contact Us"><i class="sidebar-icon fa fa-envelope"></i>Contact Us</a></li> 
                 @if (Auth::user())
                <li><a  href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="sidebar-icon fas fa-sign-out-alt"></i>Logout</a></li>
                 @endif
            </ul>

</aside>