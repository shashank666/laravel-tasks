@foreach($employees as $index=>$employee)
<tr id="" onclick="window.location.href=''" style="cursor:pointer">
    <td>{{$employee->id}}</td>
    <td>{{$employee->image}}</td>
    <td>{{$employee->name}}</td>
    <td>{{$employee->email}}</td>
    <td>{{$employee->cmpemail}}</td>
    <td>{{$employee->mobile}}</td>
    <td>{{$employee->dateofbirth}}</td>
    <td>{{$employee->position}}</td>
    
    <td>{{$employee->dateofjoin}}</td>
    <td>{{$employee->dateofrelease}}</td>
    <td>@if($employee->cpanel==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    <td>{{ Carbon\Carbon::parse($employee->created_at)->toDayDateTimeString() }}</td>
    <td style="padding: 0.9375rem 0.9375rem 0rem 0rem;"><a class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="top" title="Edit Employee" href="{{route('admin.edit_employee',['id'=>$employee->id])}}"><i class="fe fe-edit-2"></i></a></td>
    <td style="padding: 0.9375rem 0.9375rem 0rem 0rem;"><a class="btn btn-danger btn-sm delete" data-toggle="tooltip" data-placement="top" title="Delete Employee" href="{{route('admin.delete_employee',['id'=>$employee->id])}}"><i class="fe fe-trash"></i></a></td>
    @if($employee->cpanel==1)
    <td style="padding: 0.9375rem 0.9375rem 0rem 0rem;"><a class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Desable Panel" href="{{route('admin.desable_panel',['id'=>$employee->id])}}"><i class="fa fa-times"></i></a></td>
    @else
    <td style="padding: 0.9375rem 0.9375rem 0rem 0rem;"><a class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Invite For Panel" href="{{route('admin.send_invitation',['id'=>$employee->id])}}"><i class="fa fa-handshake"></i></a></td>
    @endif
    

    
</tr>
@endforeach
