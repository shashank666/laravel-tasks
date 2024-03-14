@foreach($admins as $index=>$admin)
<tr id="" onclick="window.location.href=''" style="cursor:pointer">
    <td>{{$admin->id}}</td>
    
    <td>{{$admin->name}}</td>
    <td>{{$admin->email}}</td>
    
    
    
    <td>@if($admin->super==1)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Disabled</span>@endif</td>
    
    
    @if($admin->super==1)
    <td><a class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Remove From Super Admin" href="{{route('admin.desableSuperAdmin',['id'=>$admin->id])}}"><i class="fa fa-times"></i></a></td>
    @else
    <td><a class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Make Super Admin" href="{{route('admin.enableSuperAdmin',['id'=>$admin->id])}}"><i class="fa fa-bolt"></i></a></td>
    @endif
    

    
</tr>
@endforeach
