@foreach($users as $index=>$user)
<tr id="{{$user->id}}">
    <td>{{$user->id}}</td>
    <td><img src="{{$user->image}}" height="90" width="90" class="rounded-circle"/></td>
    <td><h5>{{$user->name}}</h5></td>
    <td>{{$user->email}}<br/>
        @if($user->email_verified==1)<span class="badge badge-success">Email Verified</span>@else<span class="badge badge-warning">Not Verified</span>@endif

    </td>
    <td>
        {{  $user->mobile!=null? $user->mobile:'-'}}<br/>
        @if($user->mobile!=null)
        @if($user->mobile_verified==1)<span class="badge badge-success">Mobile Verified</span>@else<span class="badge badge-warning">Not Verified</span>@endif
        @endif
    </td>
    <td>@if($user->is_active==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    <td>{{ strtoupper($user->provider)}}</td>
    <td>{{ strtoupper($user->platform) }}</td>
    <td>{{ strtoupper($user->delete_reason) }}</td>
    <td>{{ Carbon\Carbon::parse($user->created_at)->toDayDateTimeString() }}</td>
	<td>{{ Carbon\Carbon::parse($user->deleted_at)->toDayDateTimeString() }}</td>
</tr>
@endforeach
