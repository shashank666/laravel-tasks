@foreach($user_earnings as $index=>$user_earning)
<tr id="{{$user_earning->id}}">
    <td>{{ $user_earning->user->id }}</td>
    <td>
    	<a  href="{{ route('admin.user_details',['id'=>$user_earning->user->id]) }}">
        <h5>{{$user_earning->user->name}}</h5>
        </a>
    </td>
    <td>{{ $user_earning->user_account->user_email }}</td>
    <td>{{ $user_earning->user->phone_code }}</td>
    <td>{{ $user_earning->user->mobile }}</td>
    <td>{{ number_format($user_earning->total_earning - $user_earning->total_paid, 2)}}</td>
    <td>{{ $user_earning->updated_at }}</td>
</tr>
@endforeach
