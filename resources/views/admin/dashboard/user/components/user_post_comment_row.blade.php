@foreach($data as $comment)
<tr id="{{ $comment->id }}">
    <td>
        <a href="{{ route('admin.blog_post',['id'=>$comment->post['id']]) }}">
            <img src="{{ $comment->post['coverimage'] }}" height="100" width="100" class="rounded"/>
        </a>
    </td>
    <td><a href="{{ route('admin.blog_post',['id'=>$comment->post['id']]) }}">{{ '#'.$comment->post['id'].' - '.$comment->post['title'] }}</a></td>
    <td>{{ $comment->comment!=null?$comment->comment:'-' }}</td>
    <td>@if($comment->media!=null)
        <img src="{{ $comment->media}}" height="200" width="200" />
        @else
        <p>-</p>
        @endif
    </td>
    <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('l, j M Y , h:i:s A') }}</td>
    <td><span class="badge {{ $comment->is_active==1?'badge-success':'badge-danger' }}">{{ $comment->is_active==1?'Active':'Disabled' }}</span></td>
</tr>
@endforeach
