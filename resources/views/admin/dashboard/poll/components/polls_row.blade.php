@foreach($polls as $index=>$poll)
<tr id="{{$poll->id}}">
    <td>{{ $poll->id }}</td>
    <td>
        
        <h5>Opined Team</h5>
        
        
    </td>
    <td>
        <a href="{{route('admin.individual_poll',['id'=>$poll->id])}}">
        {{ $poll->title }}
        </a>
    </td>
    
    <td>
        {{ $poll->description }}
    </td>
    <td>
        {{ $poll->poll_type }}
        
    </td>
    <td>
        {{ $poll->pollresults_count }}
        
    </td>
    <td><a href="{{route('admin.poll_edit',['id'=>$poll->id])}}"><span class="badge badge-warning">Edit</span></a></td>
    <td><a href="{{route('admin.poll_visibility',['id'=>$poll->id])}}"><span class="badge {{ $poll->visibility==1?'badge-success':'badge-danger' }}">{{ $poll->visibility==1?'Active':'Paused' }}</span></a></td>
    <td><span class="badge {{ $poll->enablenote==1?'badge-danger':'badge-success' }}">{{ $poll->enablenote==1?'Desabled':'Active' }}</span></a></td>
    
    {{--<td>
        @if(count($poll->locations)>0)
         @foreach($poll->locations as $location)
         <p>{{ $location->city.', '.$location->state.', '.$location->country_name }}</p><br/>
         @endforeach
         @else
         -
        @endif
    </td>--}}
    <td>{{ Carbon\Carbon::parse($poll->created_at)->toDayDateTimeString() }}</td>
    <!--<td>{{ $poll->ip_address!=null?$poll->ip_address:'-' }}</td>-->
    
    
</tr>
@endforeach
