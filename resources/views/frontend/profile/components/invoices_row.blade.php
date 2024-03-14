@foreach($user_invoices as $index=>$user_invoice)
<tr>
    <td>{{ $index+1 }}</td>
    <td>
        <h5>{{$user_invoice->user->name}}</h5>
        
    </td>
    
    <td>${{ $user_invoice->paid }}</td>
    <td><a href="{{route('individual_invoices',['billing_id'=>$user_invoice->billing_id])}}" target="_blank"><button class="btn btn-primary btn-sm">Show</button></a></td>
    <td>{{ $user_invoice->created_at }}</td>
</tr>
@endforeach
