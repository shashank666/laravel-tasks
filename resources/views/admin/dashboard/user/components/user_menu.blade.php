<ul class="list-group list-group-flush">
       <a class="list-group-item list-group-item-action {{ $tab=='profile'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'profile']) }}">PROFILE</a>
       <a class="list-group-item list-group-item-action {{ $tab=='payment'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'payment']) }}">PAYMENT DETAILS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='posts'?'active':'' }}" href="{{ route('admin.posts',['searchBy'=>'user_id','searchQuery'=>$user->id,'status'=>1]) }}" >POSTS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='drafts'?'active':'' }}" href="{{ route('admin.posts',['searchBy'=>'user_id','searchQuery'=>$user->id,'status'=>0]) }}" >DRAFTS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='likes'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'likes']) }}" >POST LIKES</a>
       <a class="list-group-item list-group-item-action {{ $tab=='post_comments'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'post_comments']) }}" >POST COMMENTS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='bookmarks'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'bookmarks']) }}" >POST BOOKMARKS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='opinions'?'active':'' }}" href="{{ route('admin.opinions',['searchBy'=>'user_id','searchQuery'=>$user->id]) }}" >OPINIONS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='opinion_comments'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'opinion_comments']) }}" >OPINION COMMENTS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='category'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'category']) }}">CATEGORY</a>
       <a class="list-group-item list-group-item-action {{ $tab=='followings'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'followings']) }}">FOLLOWINGS</a>
       <a class="list-group-item list-group-item-action {{ $tab=='followers'?'active':'' }}" href="{{ route('admin.user_details',['id'=>$user->id,'tab'=>'followers']) }}">FOLLOWERS</a>
</ul>
