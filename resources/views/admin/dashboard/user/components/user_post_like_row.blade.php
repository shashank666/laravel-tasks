@foreach($data as $like)
                <tr id="{{ $like->id }}">
                    <td>
                        <a href="{{ route('admin.blog_post',['id'=>$like->post['id']]) }}">
                            <img src="{{ $like->post['coverimage'] }}" height="100" width="100" class="rounded"/>
                        </a>
                    </td>
                    <td><a href="{{ route('admin.blog_post',['id'=>$like->post['id']]) }}">{{ '#'.$like->post['id'].' - '.$like->post['title'] }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($like->liked_at)->format('l, j M Y , h:i:s A') }}</td>
                    <td>{{ $like->ip_address!=null?$like->ip_address:'-' }}</td>
                    <td>{{ $like->user_agent!=null?$like->user_agent:'-'}}</td>
                    <td><span class="badge {{ $like->is_active==1?'badge-success':'badge-danger' }}">{{ $like->is_active==1?'Active':'Disabled' }}</span></td>
                </tr>
@endforeach
