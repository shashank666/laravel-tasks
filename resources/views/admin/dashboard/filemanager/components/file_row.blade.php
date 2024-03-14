@foreach($db_entries as $file)
<tr id="{{ $file->id }}" style="cursor:pointer" data-path="{{ $file->path }}" data-extension="{{ $file->extension }}" data-inuse="{{ $file->file_in_use ? 1:0 }}" data-size="{{ $file->formatted_size}}" data-filename="{{ $file->name }}">
        <td>{{ $file->id }}</td>
        <th>{{ $file->extension }}</th>
        <th>
            @if(in_array(strtolower($file->extension),['jpg','jpeg','png','gif']))
            <img src="{{ $file->path }}" height="100" width="150" class="img-fluid rounded"/>
            @else
            <i class="fas fa-video"></i>
            @endif
        </th>
        <td>{{ $file->event }}</td>
        <td>{{ $file->formatted_size }}</td>
        <td>
            @if($file->file_in_use)
            <span class="badge badge-success">Yes</span>
            @else
            <span class="badge badge-danger">No</span>
            @endif
        </td>
        @if($file->uploaded_by!=null)
        <td><a  href="{{ route('admin.user_details',['id'=>$file->uploaded_by['id']]) }}">
            {{ $file->uploaded_by->name }}<br/>
            <img src="{{ $file->uploaded_by['image'] }}" height="56" width="56" class="rounded-circle"/>
        </a></td>
        @else
        <td>USER NOT FOUND</td>
        @endif
        <td>{{ \Carbon\Carbon::parse($file->created_at)->format('d-m-Y H:i:s') }}</td>
        <td><a href="{{ $file->path }}" target="_blank">{{ $file->path }}</a></td>

</tr>
@endforeach
