@foreach($posts as $index=>$post)
<tr class="post-row" id="{{$post->id}}" data-name="{{ $post->title }}" data-isactive="{{ $post->is_active }}">

<td><a href="{{ route('blog_post',['slug'=>$post->post->slug]) }}" target="_blank">{{ str::limit($post->post->title,45,'...')}}</a></td>
<td>
 {{$post->post->ViewsCount}}
</td>
<td>
   {{$post->post->LikesCount}}
</td>
<td>
    {{$post->post->CommentsCount}}
</td>
<td>
    {{number_format($post->money,2)}}
</td>
</tr>
@endforeach


