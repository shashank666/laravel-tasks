
@foreach($posts as $post)
<div class="card mb-4 box-shadow" id="post-{{$post->id}}">
    <div class="card-body">
        @if($post->status!=1)
        <a href="/opinion/dummy/{{$post->slug}}" class="post_title"><h2 class="card-title">{{$post->title}}</h2></a>
        @else
        <a href="/opinion/{{$post->slug}}" class="post_title"><h2 class="card-title">{{$post->title}}</h2></a>
        <!--<a href="/opinion/{{$post->slug}}" class="post-body"><p class="card-text">{!!str::limit($post->plainbody,$limit = 260, $end = '...')!!}</p></a>-->
        @endif
   </div>
    <div class="card-footer bg-white">
          <span class="text-secondary float-left"><i class="far fa-calendar-alt mr-2"></i>{{$post->created_at}}</span>
        @if($post->status==0)
            <span class="text-secondary float-none" style="text-align: center; color: #ff9800 !important"><i class="fa fa-circle ml-4 mr-2"></i>Draft</span>
        @elseif($post->status==2)
        <span class="text-secondary float-none" style="text-align: center;color: #28a745 !important"><i class="fa fa-circle ml-4 mr-2"></i>Previewed</span>
        @else
        <span class="text-secondary float-none" style="text-align: center;color: blue !important"><i class="fa fa-circle ml-4 mr-2"></i>Published</span>
        @endif
        @if(Auth::user() && $post->user->id==Auth::user()->id)
            <div class="float-right">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.href='/opinion/edit/{{$post->slug}}'"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>
            <button type="button" class="btn btn-sm btn-outline-danger btn_delete_post" id="delete_{{$post->slug}}" name="delete_{{$post->id}}"><i class="far fa-trash-alt mr-2"></i>Delete</button>
            </div>
        @endif
        
    </div>
</div>
@endforeach