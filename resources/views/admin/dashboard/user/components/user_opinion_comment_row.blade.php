@foreach($data as $comment)
<tr id="{{ $comment->id }}">
    <td><a href="{{ route('admin.opinions',['searchBy'=>'id','searchQuery'=>$comment->short_opinion['id']]) }}">{{ '#'.$comment->short_opinion['id']}}</a></td>
    <td>{{ $comment->comment!=null?$comment->comment:'-' }}</td>
    <td>{{ \Carbon\Carbon::parse($comment->created_at)->format('l, j M Y , h:i:s A') }}</td>
    <td><span class="badge {{ $comment->is_active==1?'badge-success':'badge-danger' }}">{{ $comment->is_active==1?'Active':'Disabled' }}</span></td>
</tr>
@endforeach
