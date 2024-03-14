@foreach($data as $bookmark)
<tr id="{{ $bookmark->id }}">
    <td>
        <a href="{{ route('admin.blog_post',['id'=>$bookmark->post['id']]) }}">
            <img src="{{ $bookmark->post['coverimage'] }}" height="100" width="100" class="rounded"/>
        </a>
    </td>
    <td><a href="{{ route('admin.blog_post',['id'=>$bookmark->post['id']]) }}">{{ '#'.$bookmark->post['id'].' - '.$bookmark->post['title'] }}</a></td>
    <td>{{ \Carbon\Carbon::parse($bookmark->bookmarked_at)->format('l, j M Y , h:i:s A') }}</td>
    <td><span class="badge {{ $bookmark->is_active==1?'badge-success':'badge-danger' }}">{{ $bookmark->is_active==1?'Active':'Disabled' }}</span></td>
</tr>
@endforeach
