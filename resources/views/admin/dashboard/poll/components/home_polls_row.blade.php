
<tr id="{{$top_poll->id}}">
    <td>{{ $top_poll->id }}</td>
    
    <td>
        <a href="{{route('admin.individual_poll',['id'=>$top_poll->id])}}">
        {{ $top_poll->title }}
        </a>
    </td>
    
    
    <td>
        {{ $top_poll->count_top_poll }}
        
    </td>
    
    
    
</tr>
