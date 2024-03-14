@foreach($data as $user)
<tr id={{ $user->follower['id'] }}>
    <td><a href="{{ route('admin.user_details',['id'=>$user->follower['id']]) }}"><img src="{{$user->follower['image']}}" height="90" width="90" class="rounded-circle"/></a></td>
    <td><a href="{{ route('admin.user_details',['id'=>$user->follower['id']]) }}">
        <h5>{{  '#'.$user->follower['id'].'-'.$user->follower['name']}}</h5></a>
    </td>
    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('l, j M Y , h:i:s A') }}</td>
    <td><span class="badge {{ $user->is_active==1?'badge-success':'badge-danger' }}">{{ $user->is_active==1?'Active':'Disabled' }}</span></td>
</tr>
@endforeach
