@foreach($user_earnings as $index=>$user_earning)
<tr id="{{$user_earning->id}}">
    <td>{{ $user_earning->post->id }}</td>
    <td><a href="{{ route('admin.blog_post',['id'=>$user_earning->post->id]) }}">
    	{{ $user_earning->post->title }}
    </a></td>
    <td>
         @if($user_earning->user!=null)
        <a  href="{{ route('admin.user_details',['id'=>$user_earning->user->id]) }}">
        <h5>{{$user_earning->user->name}}</h5>
        </a>
        @else
        NOT FOUND
        @endif
    </td>
    <td>{{ number_format($user_earning->total_revenue, 2)}}</td>
    <td>{{ number_format($user_earning->money, 2)}}</td>
    <td>{{ $user_earning->updated_at }}</td>
</tr>
@endforeach
