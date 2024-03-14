@foreach($shares as $index=>$share)
<tr id="{{$share->id}}">
    <td>{{ $share->id }}</td>
    <td>
        @if($share->user_id==0)
        <h5>Unknown</h5>
        @else
        <a href="{{ route('admin.user_details',['id'=>$share->user['id']]) }}">
             <img height="50" width="50" class="rounded-circle" src="{{ $share->user['image']!=null?$share->user['image']:'/img/avatar.png' }}"/>
                
        <h5>{{$share->user->name}}</h5>
        </a>
        @endif
        
    </td>
    <td>
        @if($share->short_opinion_id!=0)
        <a href="{{ '/@'.$share->short_opinion->user['username'].'/opinion/'.$share->short_opinion->uuid }}">
        <h5>{{str::limit($share->short_opinion['plain_body'],$limit = 40, $end = '...')}}</h5></a><br>
        <h5> by <a href="{{ '/@'.$share->short_opinion->user['username']}}" style="color: #ff9800">{{$share->short_opinion->user['name']}}</a></h5>
        @else
            @if($share->post['id']!=null)
            <a href="{{ route('admin.blog_post',['id'=>$share->post['id']]) }}">
            <h5>{{str::limit($share->post['title'],$limit = 40, $end = '...')}}</h5>
            <img src="{{$share->post['coverimage']}}" height="90" width="120" class="rounded" onerror="this.onerror=null;this.src='/img/No Preview Available.png';"/>
            </a><br>
            <h5> by <a href="{{ '/@'.$share->post->user['username']}}" style="color: #ff9800">{{$share->post->user['name']}}</a></h5>
            @else
            Not Available
            @endif
        @endif
        
    </td>
    <td>
        @if($share->short_opinion_id!=0)
        Short
        @else
        Article
        @endif
    <td>{{ $share->shared_at }}</td>
    <!--<td>{{ $share->ip_address!=null?$share->ip_address:'-' }}</td>-->
    <td><span class="badge {{ $share->is_active==1?'badge-success':'badge-danger' }}">{{ $share->is_active==1?'Active':'Disabled' }}</span></td>
    
</tr>
@endforeach
