<ul class="list-unstyled" id="notifications-list" style="max-height:320px;overflow-y:scroll;">
        @if(Auth::user()->my_notifications()->count()>0)
        @foreach(Auth::user()->my_notifications() as $notification)
        @php
        $ext = preg_match('/\./', $notification->sender_image) ? preg_replace('/^.*\./', '', $notification->sender_image) : '';
        $path=$notification->sender_image;
        $string="/storage/profile";
        $substring="avatar_thumb"
        @endphp

        <li class="notification-list" id="{{$notification->id}}">
                <a href="">
                    <img class="float-left mr-2 rounded-circle" src="
                    @php
                        if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
                        @endphp
                            {{preg_replace('/.[^.]*$/', '',$notification->sender_image).'_40x40'.'.'.$ext}}
                        @php
                        }
                        else{
                        @endphp
                        {{$notification->sender_image}}
                        @php
                    }
                        @endphp" height="40" width="40" alt="{{$notification->sender_name}}" onerror="this.onerror=null;this.src='/img/avatar_thumb.png';"/>

                </a>
                <a href="{{$notification->data->notification->action_url}}" class="text-muted">
                    <div class="notification-content">
                        <small class="notification-timestamp float-right">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('jS F , Y')}}
                        </small>
                        <div class="notification-heading">{{$notification->sender_name}}</div>
                        <div class="notification-text">
                            {{$notification->data->notification->message}}
                        </div>
                    </div>
                </a>
        </li>
        @endforeach
       <li class="notification-list">
            <div class="notification-content" style="padding-bottom:8px;">
            <a href="javascript:void(0);" class="more-link float-left" id="mark_as_read" onclick="markAsRead();">Mark as Read</a>
            <a href="/me/notifications" class="more-link float-right">See All</a>
            </div>
        </li>
        @else
        <li class="notification-list text-center">
        <p>No Notifications Available.</p>
        </li>
        @endif

</ul>
