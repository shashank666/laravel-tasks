@foreach($opinions as $index=>$opinion)
<tr id="{{$opinion->id}}">
    <td>{{ $opinion->id }}</td>
    <td>
        
        <a href="{{ route('admin.user_details',['id'=>$opinion->user['id']]) }}">
             <img height="50" width="50" class="rounded-circle" src="{{ $opinion->user['image']!=null?$opinion->user['image']:'/img/avatar.png' }}"/>
                
        <h5>{{$opinion->user->name}}</h5>
        </a>
       
        
    </td>
    <td>
        
        <a href="{{ '/@'.$opinion->user['username'].'/opinion/'.$opinion->uuid }}">
        <h5>{{str::limit($opinion->plain_body,$limit = 90, $end = '...')}}</h5></a>
        
        
    </td>
    <td>
        {{$opinion->likes_count}}
    </td>
    <td>
        {{$opinion->created_at}}
    </td>
</tr>
@endforeach
