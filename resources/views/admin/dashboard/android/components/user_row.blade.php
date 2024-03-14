@foreach($model_users as $index=>$model_user)
<tr id="{{$model_user->user_id}}" onclick="window.location.href='{{ route('admin.user_details',['id'=>$model_user->user_id]) }}'" style="cursor:pointer">
    <td><h5>{{ucfirst($model_user->user->name)}}</h5></td>
    <td>{{$model_user->app_version}}</td>
    <td>{{$model_user->device_os_name}}</td>
    <td>{{$model_user->device_os_version}}</td>
    
    <td>
        {{  $model_user->user->mobile!=null?  $model_user->user->phone_code.'-'.$model_user->user->mobile:'-'}}<br/>
    </td>
    <td>
        @if(count($model_user->user->locations)>0)
         @foreach($model_user->user->locations as $location)
         <p>{{ $location->city.', '.$location->state.', '.$location->country_name }}</p><br/>
         @endforeach
         @else
         -
        @endif
    </td>
    <td>{{ Carbon\Carbon::parse($model_user->created_at)->toDayDateTimeString() }}</td>
</tr>
@endforeach
