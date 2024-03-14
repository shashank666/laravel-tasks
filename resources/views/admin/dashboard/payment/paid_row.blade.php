@foreach($user_invoices as $index=>$user_invoice)
<tr id="{{$user_invoice->id}}">
    <td>{{ $user_invoice->user->id }}</td>
    <td>
        <a  href="{{ route('admin.user_details',['id'=>$user_invoice->user->id]) }}">
        <h5>{{$user_invoice->user->name}}</h5>
        </a>
    </td>
    <td>{{ $user_invoice->user->email }}</td>
    <td>{{ $user_invoice->user->phone_code }}</td>
    <td>{{ $user_invoice->user->mobile }}</td>
    <td>{{ $user_invoice->paid }}</td>
    <td>{{ $user_invoice->payment_refrence_number }}</td>
    <td>{{ $user_invoice->created_at }}</td>
</tr>
@endforeach
