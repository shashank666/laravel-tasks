@foreach($likes as $like)
<tr id="{{ $like->id }}">
        <td>{{ $like->id }}</td>
        <td>
                @if($like->user!=null)
                <a href="{{ route('admin.user_details',['id'=>$like->user['id']]) }}">
                    <img height="50" width="50" class="rounded-circle" src="{{ $like->user['image']!=null?$like->user['image']:'/img/avatar.png' }}"/>
                </a>
                @else
                -
                @endif
        </td>
    <td>
        @if($like->user!=null)
        <a href="{{ route('admin.user_details',['id'=>$like->user['id']]) }}">
            <p>{{ "#".$like->user['id'] ." - ".$like->user['name'] }}</p>
            <p>{{ $like->user['email'] }}</p>
            <p>@if($like->user['is_active']==1)
                    <span class="badge badge-success">Account Active</span>
                    @else
                    <span class="badge badge-danger">Account Blocked {{ $like->user['blocked_reason'] }}</span>
                    @endif
            </p>
        </a>
        @else
        USER NOT FOUND
        @endif
    </td>
    <td>{{ \Carbon\Carbon::parse($like->liked_at)->format('l, j M Y , h:i:s A') }}</td>
    <td>{{ $like->ip_address!=null?$like->ip_address:'-' }}</td>
    <td>{{ $like->user_agent!=null? $like->user_agent:'-' }}</td>
    <td><span class="badge {{ $like->is_active==1?'badge-success':'badge-danger' }}">{{ $like->is_active==1?'Active':'Disabled' }}</span></td>
    <td>
        <button class="btn btn-danger btn-rounded-circle deleteLike" data-deleteid="{{ $like->id }}">
                <i class="fas fa-trash-alt"></i>
        </button>
    </td>
</tr>
@endforeach