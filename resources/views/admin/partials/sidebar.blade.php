<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light" id="sidebar">
        <div class="container-fluid">

          <!-- Toggler -->
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarCollapse" aria-controls="sidebarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <!-- Brand -->
          <a class="navbar-brand" href="{{route('admin.dashboard')}}">
            <img src="/img/logo.png"  class="navbar-brand-img mx-auto" alt="...">
          </a>

          <!-- User (xs) -->
          <div class="navbar-user d-md-none">

            <!-- Dropdown -->
            <div class="dropdown">

              <!-- Toggle -->
              <a href="#" id="sidebarIcon" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="avatar avatar-sm avatar-online">
                  <img src="/public_admin/assets/img/avatars/default.png" class="avatar-img rounded-circle" alt="...">
                </div>
              </a>

              <!-- Menu -->
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="sidebarIcon">
                <a class="dropdown-item" href="#modalDemo" data-toggle="modal">
                    Theme Settings
                </a>
                <a  href="{{route('admin.settings')}}" class="dropdown-item">Settings</a>
                <a  href="{{ route('admin.logout') }}" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
              </div>
            </div>

          </div>

          <!-- Collapse -->
          <div class="collapse navbar-collapse" id="sidebarCollapse">

            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
              <div class="input-group input-group-rounded input-group-merge">
                <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="Search" aria-label="Search">
                <div class="input-group-prepend">
                  <div class="input-group-text">
                    <span class="fe fe-search"></span>
                  </div>
                </div>
              </div>
            </form>

            <!-- Navigation -->
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="{{$menu=='index'?'nav-link active':'nav-link'}}" href="{{route('admin.dashboard')}}">
                  <i class="fe fe-home"></i> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a  class="{{$menu=='android'?'nav-link active':'nav-link'}}" href="{{route('admin.android_dashboard')}}">
                    <i class="fab fa-android"></i>  Android
                </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='category'?'nav-link active':'nav-link'}}" href="{{route('admin.categories')}}">
                  <i class="fe fe-tag"></i> Categories
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='posts'?'nav-link active':'nav-link'}}" href="{{route('admin.posts')}}">
                      <i class="fe fe-file-text"></i> Posts
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='threads'?'nav-link active':'nav-link'}}" href="{{route('admin.threads')}}">
                      <i class="fe fe-hash"></i> Threads
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='opinions'?'nav-link active':'nav-link'}}" href="{{route('admin.opinions')}}">
                      <i class="fe fe-message-square"></i>Opinions
                  </a>
              </li>
              <li class="nav-item">
                  <a  class="{{$menu=='users'?'nav-link active':'nav-link'}}" href="{{route('admin.users')}}">
                      <i class="fe fe-user"></i>Users
                  </a>
              </li>
              <li class="nav-item">
                  <a class="{{$menu=='messages'?'nav-link active':'nav-link'}}" href="{{route('admin.unread_messages')}}">
                      <i class="fe fe-message-circle"></i>
                      <span>Messages</span>
                  </a>
              </li>
              <li class="nav-item">
                    <a  class="{{$menu=='filemanager'?'nav-link active':'nav-link'}}" href="{{route('admin.filemanager.index')}}">
                        <i class="fe fe-folder"></i>
                        <span>Files</span>
                    </a>
               </li>
               <li class="nav-item">
                <a  class="{{$menu=='pushmanager'?'nav-link active':'nav-link'}}" href="{{route('admin.push.index')}}">
                    <i class="fe fe-volume-2"></i>
                    <span>Push</span>
                </a>
                </li>
                <li class="nav-item">
                        <a  class="{{$menu=='email'?'nav-link active':'nav-link'}}" href="{{route('admin.email.index')}}">
                            <i class="fe fe-mail"></i>
                            <span>Email</span>
                        </a>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="navbar-divider my-3">

            <!-- Heading -->
            <h6 class="navbar-heading">
              Other
            </h6>

            <!-- Navigation -->
            <ul class="navbar-nav mb-md-4">
              <li class="nav-item">
                <a class="{{$menu=='settings'?'nav-link active':'nav-link'}}" href="{{route('admin.settings')}}">
                  <i class="fe fe-settings"></i> Settings
                </a>
              </li>
            </ul>

            <!-- Push content down -->
            <div class="mt-auto"></div>


            <!-- Customize -->
            <a href="#modalDemo" class="btn btn-block btn-primary mb-4" data-toggle="modal">
              <i class="fe fe-sliders mr-2"></i> Change Theme
            </a>



            <!-- User (md) -->
            <div class="navbar-user d-none d-md-flex" id="sidebarUser">

              <!-- Dropup -->
              <div class="dropup">

                <!-- Toggle -->
                <a href="#" id="sidebarIconCopy" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <div class="avatar avatar-sm avatar-online">
                    <img src="/public_admin/assets/img/avatars/default.png" class="avatar-img rounded-circle" alt="...">
                  </div>
                </a>

                <!-- Menu -->
                <div class="dropdown-menu" aria-labelledby="sidebarIconCopy">
                  <a href="profile-posts.html" class="dropdown-item">Profile</a>
                  <a href="settings.html" class="dropdown-item">Settings</a>
                  <hr class="dropdown-divider">
                  <a href="sign-in.html" class="dropdown-item">Logout</a>
                </div>

              </div>

              <!-- Icon -->
              <a href="#sidebarModalSearch" class="navbar-user-link" data-toggle="modal">
                <span class="icon">
                  <i class="fe fe-search"></i>
                </span>
              </a>

            </div>


          </div>

        </div>
      </nav>
