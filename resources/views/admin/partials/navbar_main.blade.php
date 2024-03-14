<nav class="navbar navbar-expand-md navbar-light d-none d-md-flex" id="topbar">
        <div class="container-fluid">

          <form class="form-inline mr-4 d-none d-md-flex">
            <div class="input-group input-group-flush input-group-merge" data-toggle="lists" data-lists-values='["name"]'>

              <input type="search" class="form-control form-control-prepended search"  placeholder="Search" aria-label="Search">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fe fe-search"></i>
                </div>
              </div>

            </div>
          </form>

          <!-- User -->
          <div class="navbar-user">

            <!-- Dropdown -->
            <div class="dropdown mr-4 d-none d-md-flex">

              <!-- Toggle -->
              <a href="#" class="text-muted" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="icon active">
                  <i class="fe fe-bell"></i>
                </span>
              </a>

            </div>

            <!-- Dropdown -->
            <div class="dropdown">

              <!-- Toggle -->
              <a href="#" class="avatar avatar-sm avatar-online dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="/public_admin/assets/img/avatars/default.png" alt="..." class="avatar-img rounded-circle">
              </a>

              <!-- Menu -->
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#modalDemo" data-toggle="modal">
                    Theme Settings
                </a>
                <a href="{{route('admin.settings')}}" class="dropdown-item">Settings</a>
                <hr class="dropdown-divider">
                <a href="{{ route('admin.logout') }}" class="dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
              </div>

            </div>

          </div>

        </div>
      </nav>
