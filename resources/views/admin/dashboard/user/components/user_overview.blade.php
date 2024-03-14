<div class="card">
    <div class="card-header">
        <h4 class="card-header-title">Profile Information</h4>
    </div>
    <table class="table">
        <tbody>
           <tr><td>IMAGE</td><td><img src="{{ $user->image!=null?$user->image:'/img/avatar.png' }}" height="150" width="150" style="border-radius:50%;"/></td></tr>
           <tr><td>ID</td><td>{{ $user->id }}</td></tr>
           <tr><td>NAME</td><td>{{ $user->name }}</td></tr>
           <tr><td>MOBILE</td><td>{{ $user->mobile!=null?$user->mobile:'-' }}</td></tr>
           <tr><td>MOBILE VERIFIED</td><td><span class="p-2 badge {{ $user->mobile_verified==1?'badge-success':'badge-danger'}}">{{ $user->mobile_verified==1?'Verified':'Not Verified'}}</span></td></td></tr>
           <tr><td>EMAIL</td><td>{{ $user->email }}</td></tr>
           <tr><td>EMAIL VERIFIED</td><td> <span class="p-2 badge {{ $user->email_verified==1?'badge-success':'badge-danger'}}">{{ $user->email_verified==1?'Verified':'Not Verified'}}</span></td></tr>
           <tr><td>REGISTER WRITER</td><td> <span class="p-2 badge {{ $user->registered_as_writer==1?'badge-success':'badge-danger'}}">{{ $user->registered_as_writer==1?'Yes':'No'}}</span></td></tr>
           <tr><td>USERNAME</td><td>{{ $user->username }}</td></tr>
           <tr><td>UNIQUEID</td><td>{{ $user->unique_id }}</td></tr>
           <tr><td>ACCOUNT STATUS</td><td> <span class="p-2 badge {{ $user->is_active==1?'badge-success':'badge-danger'}}">{{ $user->is_active==1?'Active':'Blocked'}}</span></td></td></tr>
           <tr><td>LAST LOGIN AT</td><td>{{ $user->last_login_at!=null?$user->last_login_at:'-' }}</td></tr>
           <tr><td>LAST LOGIN IP</td><td>{{ $user->last_login_ip!=null?$user->last_login_ip:'-' }}</td></tr>
           <tr><td>REGISTERED AT</td><td>{{ $user->created_at }}</td></tr>
           <tr><td>REGISTERED USING</td><td>{{ strtoupper($user->provider) }}</td></tr>
           <tr><td>REGISTER PLATFORM</td><td> {{ $user->platform }} </td></tr>
           <tr><td>ABOUT</td><td>{{ $user->bio!=null?$user->bio:'-' }}</td></tr>
           <tr><td>FACEBOOK PAGE</td><td>{{ $user->facebook_url!=null?$user->facebook_url:'-' }}</td></tr>
           <tr><td>GOOGLEPLUS PAGE</td><td>{{ $user->googleplus_url!=null?$user->googleplus_url:'-' }}</td></tr>
           <tr><td>LINKEDIN PAGE</td><td>{{ $user->linkedin_url!=null?$user->linkedin_url:'-' }}</td></tr>
           <tr><td>TWITTER PAGE</td><td>{{ $user->twitter_url!=null?$user->twitter_url:'-' }}</td></tr>
           <tr><td>PROFILE URL</td><td><a target="_blank" href="{{ route('user_profile',['username'=>$user->username]) }}">{{  'https://weopined.com/@'.$user->username }}</td></tr>
           <tr><td>PROFILE VIEWS</td><td>{{ $user->views}}</td></tr>
           <tr><td>SUSCRIBED NEWSLETTER</td><td>{{ $user->is_subscribed==1?'Yes':'No' }}</td></tr>
        </tbody>
    </table>
</div>