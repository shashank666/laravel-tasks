@foreach($comments as $index=>$comment)
<tr id="{{$comment->id}}">
    <td>{{ $comment->id }}</td>
    <td><h5>@if($comment->comment!=null)
            {{ $comment->comment }}
            @endif</h5>
        @if($comment->media!=null)
        <br/><br/><img src="{{ $comment->media }}" class="img-fluid rounded" alt="..." style="max-width:300px;max-height:300px"/>
        @endif</td>
    @if($comment->user!=null)
    <td><a  href="{{ route('admin.user_details',['id'=>$comment->user['id']]) }}">
        {{ $comment->user['name'] }}
        <img src="{{ $comment->user['image'] }}" height="56" width="56" class="rounded-circle" onerror="this.onerror=null;this.src='/img/profile-default-opined_100x100.png';"/>
    </a></td>
    @else
    <td>USER NOT FOUND</td>
    @endif
    
    <td><a href="{{ route('admin.blog_post',['id'=>$comment->post->id]) }}">
        <h5>{{$comment->post->title}}</h5>
        <img src="{{$comment->post->coverimage}}" height="90" width="120" class="rounded" onerror="this.onerror=null;this.src='/img/No Preview Available.png';"/>
        </a></td>
    <td>{{ $comment->created_at }}</td>
    <td>@if($comment->is_active==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    <td>@if($comment->is_active==1)
    <a class="btn btn-warning btn-rounded-circle" href="{{route('admin.desable_comment',['comment_id'=>$comment->id,'post_id'=>$comment->post->id])}}" title="Desable Comment"><i class="fa fa-ban"></i></a>
    @else
    <a class="btn btn-success btn-rounded-circle" href="{{route('admin.enable_comment',['comment_id'=>$comment->id,'post_id'=>$comment->post->id])}}" title="Enable Comment"><i class="fa fa-check-circle"></i></a>
    @endif
    </td>
    <td>
    <a class="btn btn-danger btn-rounded-circle confirmation"  href="{{route('admin.delete_comment',['comment_id'=>$comment->id,'post_id'=>$comment->post->id])}}" title="Delete Comment"><i class="fa fa-trash"></i></a>
    </td>
</tr>

@endforeach
