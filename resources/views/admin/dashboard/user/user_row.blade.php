@foreach($users as $index=>$user)
<tr id="{{$user->id}}" onclick="window.location.href='{{ route('admin.user_details',['id'=>$user->id]) }}'" style="cursor:pointer">
    <td>{{$user->id}}</td>
    <td><img src="{{$user->image}}" height="90" width="90" class="rounded-circle"/></td>
    <td><h5>{{$user->name}}</h5></td>
    <td>{{$user->email}}<br/>
        @if($user->email_verified==1)<span class="badge badge-success">Email Verified</span>@else<span class="badge badge-warning">Not Verified</span>@endif

    </td>
    <td>
        {{  $user->mobile!=null?  $user->phone_code.'-'.$user->mobile:'-'}}<br/>
        @if($user->mobile!=null)
        @if($user->mobile_verified==1)<span class="badge badge-success">Mobile Verified</span>@else<span class="badge badge-warning">Not Verified</span>@endif
        @endif
    </td>
    <td>@if($user->is_active==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    <td>{{ strtoupper($user->provider)}}</td>
    <td>
        @if($user->platform=='website')<span class="badge badge-primary"><i class="fas fa-globe"></i></span>@else<span class="badge badge-success"><i class="fab fa-android"></i></span>@endif
    </td>
    <td>
        @if(count($user->locations)>0)
         @foreach($user->locations as $location)
         <p>{{ $location->city.', '.$location->state.', '.$location->country_name }}</p><br/>
         @endforeach
         @else
         -
        @endif
    </td>
    <td>{{ Carbon\Carbon::parse($user->created_at)->toDayDateTimeString() }}</td>
</tr>
@endforeach
