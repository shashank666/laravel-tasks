<ul class="nav nav-tabs nav-justified flex-md-row flex-sm-column flex-column my-4">
 <li class="nav-item">
    <a class="nav-link {{$section=='profile'?'active':''}}" href="{{route('user_profile',['username' => $profile_user->username])}}">Profile</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$section=='latest'?'active':''}}" href="{{route('user_latest_posts',['username' => $profile_user->username])}}">Articles</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{$section=='short_opinions'?'active':''}}" href="{{route('user_thread_opinions',['username' => $profile_user->username])}}">Opinions</a>
  </li>
</ul>
