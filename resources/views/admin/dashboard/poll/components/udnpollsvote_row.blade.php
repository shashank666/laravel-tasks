@foreach($pollresults as $index=>$poll)
<tr id="{{$poll->id}}">
    <td>{{ $poll->id }}</td>
    <td>
        @if($poll->user_id != null)
        <h5 onclick="window.location.href='{{ route('admin.user_details',['id'=>$poll->user['id']]) }}'" style="cursor:pointer; color: #ff9800">{{ $poll->user['name'] }}</h5>
        @else
        <h5>Unknown</h5>
        @endif
    </td>
    <td>
        {{ ucfirst(strtolower($poll->voting_type)) }}
        
    </td>
    <td>
        {{ $poll->voting }}
    </td>
    <td>
        @if(count($poll->locations)>0)
         @foreach($poll->locations as $location)
         <p>{{ $location->city.', '.$location->state.', '.$location->country_name }}</p><br/>
         @endforeach
         @else
         -
        @endif
    </td>
    <td>{{ Carbon\Carbon::parse($poll->created_at)->toDayDateTimeString() }}</td>
    <!--<td>{{ $poll->ip_address!=null?$poll->ip_address:'-' }}</td>-->
    
    
</tr>
@endforeach
