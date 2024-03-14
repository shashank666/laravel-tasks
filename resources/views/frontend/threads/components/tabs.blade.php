<ul class="nav nav-tabs mb-5">
        <li class="nav-item">
            <a class="nav-link {{$section=='trending'?'active':''}}" href="/threads/trending">Trending Topics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$section=='latest'?'active':''}}" href="/threads/latest">Latest Topics</a>
        </li>
        @if(Auth::user())
        <li class="nav-item">
            <a class="nav-link {{$section=='circle'?'active':''}}" href="/threads/circle">Topics For You</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{$section=='followed'?'active':''}}" href="/threads/followed">Topics you Follow</a>
        </li>
        @endif
</ul>
