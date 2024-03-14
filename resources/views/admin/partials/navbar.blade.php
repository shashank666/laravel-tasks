<nav class="navbar navbar-expand-lg navbar-light" id="topnav">
        <div class="container">


          <button class="navbar-toggler mr-auto" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <a class="navbar-brand mr-auto" href="{{route('admin.dashboard')}}">
            <img  src="/img/logo.png"  alt="..." class="navbar-brand-img">
          </a>

          <form class="form-inline mr-4 d-none d-lg-flex">
            <div class="input-group input-group-rounded input-group-merge" data-toggle="lists" data-lists-values='["name"]'>

              <!-- Input -->
              <input type="search" class="form-control form-control-prepended  search" placeholder="Search">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fe fe-search"></i>
                </div>
              </div>

            </div>
          </form>

          <div class="navbar-user">



            <div class="dropdown">

              <a href="#" class="avatar avatar-sm avatar-online dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="/public_admin/assets/img/avatars/default.png" alt="..." class="avatar-img rounded-circle">
              </a>

              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#modalDemo" data-toggle="modal">
                    Theme Settings
                </a>
                <a  href="{{route('admin.settings')}}" class="dropdown-item">Settings</a>
                <hr class="dropdown-divider">
                <a  href="{{ route('admin.logout') }}" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
              </div>

            </div>

          </div>

          <div class="collapse navbar-collapse mr-auto order-lg-first" id="navbar">

            <form class="mt-4 mb-3 d-md-none">
              <input type="search" class="form-control form-control-rounded" placeholder="Search" aria-label="Search">
            </form>

            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a  class="{{$menu=='index'?'nav-link active':'nav-link'}}" href="{{route('admin.dashboard')}}">
                  Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a  class="{{$menu=='android'?'nav-link active':'nav-link'}}" href="{{route('admin.android_dashboard')}}">
                    Android
                </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='posts'?'nav-link active':'nav-link'}}" href="{{route('admin.posts')}}">
                    Articles
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='threads'?'nav-link active':'nav-link'}}" href="{{route('admin.threads')}}">
                    Threads
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='opinions'?'nav-link active':'nav-link'}}" href="{{route('admin.opinions')}}">
                    Opinions
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='polls'?'nav-link active':'nav-link'}}" href="{{route('admin.poll_home')}}">
                    Polls
                  </a>
              </li>
              <!--<li class="nav-item">
                  <a  class="{{$menu=='users'?'nav-link active':'nav-link'}}" href="{{route('admin.users')}}">
                    Users
                  </a>
              </li> -->
              
              <li class="nav-item">
                  <a  class="{{$menu=='users'?'nav-link active':'nav-link'}}" href="{{route('admin.users')}}">
                    Users
                  </a>
              </li>

              <li class="nav-item">
                  <a  class="{{$menu=='rsm'?'nav-link active':'nav-link'}}" href="{{route('admin.payment.home')}}">
                    RSM
                  </a>
              </li>
              
              
               <li class="nav-item">
                    <a  class="{{$menu=='messages'?'nav-link active':'nav-link'}}" href="{{route('admin.unread_messages')}}">
                      Messages
                    </a>
                </li>
                
               
              <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      More
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                      @if(Auth::guard('admin')->user()->super==1)
                      <a class="dropdown-item" href="{{route('admin.administration')}}">Employees</a>
                      <a class="dropdown-item" href="{{route('admin.adminlist')}}">Admins</a>
                      @endif
                      <hr class="dropdown-divider">
                      <a  class="{{$menu=='category'?'nav-link active':'nav-link'}}" href="{{route('admin.categories')}}">Categories</a>
                      <a  class="{{$menu=='filemanager'?'nav-link active':'nav-link'}}" href="{{route('admin.filemanager.index')}}">Files</a>
                      <a  class="{{$menu=='pushmanager'?'nav-link active':'nav-link'}}" href="{{route('admin.push.index')}}">Push</a>
                      <a  class="{{$menu=='email'?'nav-link active':'nav-link'}}" href="{{route('admin.email.index')}}">Email</a>
              </li>
              
            </ul>

          </div>

        </div>
      </nav>
