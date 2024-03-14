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
    @if($user_earning->user->phone_code == "+91")
    <td><input type="hidden" name="user_name" id="user_name" value="{{ $user_earning->user_account->account_holdername }}"/>
        <input type="hidden" name="account_number" id="account_number" value="{{ $user_earning->user_account->account_no }}"/>
        <input type="hidden" name="user_account_ifsc" id="account_ifsc" value="{{ $user_earning->user_account->bank_ifsc_code }}"/>
        <input type="hidden" name="account_type" id="account_type" value="{{ $user_earning->user_account->account_type }}"/>
        <button class="btn btn-primary btn-sm payment-info">Show</button></td>
    @else
    <td><button class="btn btn-success btn-sm">Show</button></td>
    @endif
    <td>{{ number_format($user_earning->total_earning - $user_earning->total_paid, 2)}}</td>
    <td><button class="btn btn-info btn-sm payment-done">YES</button>
        <input type="hidden" name="user_id" id="user_id" value="{{ $user_earning->user->id }}"/></td>

</tr>
@endforeach
