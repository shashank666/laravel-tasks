<div class="card shadow-sm mb-3">
<div class="card-body p-0">
<div class="media">
        <a href="/opinion/{{$post->slug}}">
        	@php
	        $ext_opinion = preg_match('/\./', $post->coverimage) ? preg_replace('/^.*\./', '', $post->coverimage) : '';
	        @endphp
	        <img class="lazy" src="/img/noimg.png" data-src="{{preg_replace('/.[^.]*$/', '',$post->coverimage).'_314x240'.'.'.$ext_opinion}}" height="100" width="100" alt="{{ $post->title }}" onerror="this.onerror=null;this.src='/img/noimg.png';"/>
        	<!--<img src="{{ $post->coverimage }}" height="100" width="100" alt="{{ $post->title }}"></a>-->
        <div class="media-body justify-content-between p-2">
          <a class="post_title" href="/opinion/{{$post->slug}}"><h5>{{ str::limit($post->title,$limit=40,'...') }}</h5></a>
          <p class="text-secondary mb-0" style="font-size:13px">Published by <a href="{{ route('user_profile',['username' =>$post->user->username])}}" style="color:#ff9800;">{{ucfirst($post->user->name)}}</a></p>
        </div>
</div>
</div>
</div>

