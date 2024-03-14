@foreach($messages as $row)
<tr>
    <td>
        <div style="background:#fff4e5;padding:12px;max-width:60%;border-radius:8px;">
            <p>From :  {{$row->name}}
                <br/>
                <small>{{$row->email}}</small>
            </p>

            <b>Subject : {{$row->subject}}</b>
            <p>Message : {{$row->message}}</p>

            <div style="text-align:right">
                    <button class="btn btn-rounded-circle btn-success btn-reply" data-messageid="{{ $row->id }}" data-name="{{$row->name}}" data-email="{{$row->email}}" data-subject="{{$row->subject}}"><i class="fe fe-corner-up-right"></i></button>
                    @if($section=='unread')
                    <form id="msg_form_{{$row->id}}" method="POST" action="{{route('admin.message_mark_as_read')}}" style="display:none;">{{csrf_field()}}<input type="hidden" name="id" value="{{$row->id}}"/></form>
                    <button class="btn btn-rounded-circle btn-primary" onclick="event.preventDefault();document.getElementById('msg_form_{{$row->id}}').submit();"><i class="fe fe-check"></i></button>
                    @endif
                    <button class="btn btn-star btn-rounded-circle btn-warning" data-messageid="{{ $row->id }}">
                        <i class="fe fe-star star-on-{{ $row->id }}" style="display:{{ $row->starred==0?'none':'block' }}"></i>
                        <i class="fe fe-star star-off-{{ $row->id }}" style="display:{{ $row->starred==1?'none':'block' }}"></i>
                    </button>
                    <form id="delete_msg_{{$row->id}}" method="POST" action="{{route('admin.delete_message')}}" style="display:none;">{{csrf_field()}}<input type="hidden" name="id" value="{{$row->id}}"/></form>
                    <button class="btn btn-rounded-circle btn-danger" onclick="event.preventDefault();document.getElementById('delete_msg_{{$row->id}}').submit();"><i class="fe fe-trash-2"></i></button>
            </div>

            </div>
            <span class="badge badge-warning">
                    {{ Carbon\Carbon::parse($row->created_at)->toFormattedDateString() }}
            </span>

            @if(count($row->reply)>0)
            @foreach($row->reply as $reply)
            <div style="margin-top:24px;background:#f4f9ff;padding:12px;margin-left:40%;max-width:60%;border-radius:8px;">
                <b>Subject : {{ $reply->subject }}</b>
                <p>Reply : {{ $reply->message }}</p>
            </div>
            <span class="badge float-right" style="background:#b8daff">
                    {{ Carbon\Carbon::parse($reply->created_at)->toFormattedDateString() }}
            </span>
            @endforeach
            @endif

    </td>
</tr>
@endforeach
