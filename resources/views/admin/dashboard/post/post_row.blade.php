@foreach($posts as $index=>$post)
<tr id="{{$post->id}}">
    <td>{{ $post->id }}</td>
    <td><a href="{{ route('admin.blog_post',['id'=>$post->id]) }}">
        <h5>{{$post->title}}</h5>
        <img src="{{$post->coverimage}}" height="90" width="120" class="rounded"/>
        </a>
    </td>
    @if($post->user!=null)
    <td><a  href="{{ route('admin.user_details',['id'=>$post->user['id']]) }}">
        {{ $post->user->name }}
        <img src="{{ $post->user['image'] }}" height="56" width="56" class="rounded-circle"/>
    </a></td>
    @else
    <td>USER NOT FOUND</td>
    @endif
    <td>{{ $post->created_at }}</td>
    <td>
        @if($post->platform=='website')<span class="badge badge-primary"><i class="fas fa-globe"></i></span>@else<span class="badge badge-success"><i class="fab fa-android"></i></span>@endif
    </td>

    <td>
        @if($post->status==1)<span class="badge badge-success">Published</span>@elseif($post->status==2)<span class="badge badge-warning">Previewed</span>@else<span class="badge badge-primary">Draft</span>@endif
    </td>
    <td>@if($post->is_active==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    <td>
            @if($post->plagiarism_checked==1)
            <span class="badge badge-success">Done</span>
            @else
            <span class="badge badge-warning">Pending</span>
            @endif
    </td>
    <td>    @if($post->plagiarism_checked==1)

                @if($post->is_monetised==1)
                <span class="badge badge-success" title="Monetised"><i class="fas fa-dollar-sign"></i></span>
                @else
                <span class="badge badge-danger" title="Not Monetised"><i class="fas fa-dollar-sign"></i></span>
                @endif
            @elseif($post->articleStatus!=null)
                @if($post->articleStatus->promo_review==0 || $post->articleStatus->backlink==0)
                <span class="badge badge-danger" title="Not Monetised"><i class="fas fa-dollar-sign"></i></span>
                @else
            <span class="badge" title="Not Checked For Monetisation" style="background: grey; color: #fff"><i class="fas fa-dollar-sign"></i></span>
                @endif
            @else
            <span class="badge" title="Not Checked For Monetisation" style="background: grey; color: #fff"><i class="fas fa-dollar-sign"></i></span>
            @endif
            
           
    </td>
    <td data-dbcount="{{ $post->likes }}">{{ $post->likesCount }}<span class="data-dbcount">{{ $post->likes }}</span></td>
    <td data-dbcount="{{ $post->views }}">{{$post->viewsCount}}<span class="data-dbcount">{{ $post->views }}</span></td>
    <td>{{ $post->commentsCount }}</td>
    <td>
    <a class="btn btn-primary btn-rounded-circle" href="{{route('admin.edit_post',['id'=>$post->id])}}"><i class="fe fe-edit-2"></i></a>
    </td>
</tr>
@endforeach
