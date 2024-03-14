@foreach($threads as $index=>$thread)
<tr class="thread-row" id="{{$thread->id}}" data-name="{{ $thread->name }}" data-isactive="{{ $thread->is_active }}" style="cursor:pointer" onclick="window.location.href='{{ route('admin.opinions',['threads'=>$thread->id]) }}'">
<td>{{ $thread->id }}</td>
<td>{{'#'.$thread->name}}</td>
<td>
  @if(count($thread->categories)>0)
    @foreach($thread->categories as $cat)
    <span class="badge badge-light p-2 mr-2">{{ $cat->name }}</span>
    @endforeach
   @else
   <p>No Categories Associated</p>
   @endif
</td>
<td>
    <a href="{{ route('admin.posts',['threads'=>$thread->id]) }}" class="btn btn-secondary {{ $thread->posts_count==0?'disabled':'' }}">{{ $thread->posts_count }}</a>
</td>
<td>
        <p>{{$thread->opinions_count}}</p>
</td>
<td>
    <p>{{$thread->likes_count}}</p>
</td>
<td>
    <p>{{$thread->followers_count}}</p>
</td>
<td>@if($thread->is_active==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
<td>{{ \Carbon\Carbon::parse($thread->created_at)->format('d-m-Y , h:m:s a') }}</td>
<td>
    <a class="btn btn-primary btn-rounded-circle" href="{{route('admin.edit_thread',['id'=>$thread->id])}}"><i class="fe fe-edit-2"></i></a>
</td>
</tr>
@endforeach


