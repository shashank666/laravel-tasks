    <div class="card shadow-sm mb-3">
          <div class="card-body">
              <div class="align-items-center text-center">
                 <a href="{{ route('user_profile', ['username' => $user->username]) }}" title="Go to the profile of {{ucfirst($user->name)}}"><img src="{{$user->image==null?'/storage/profile/avatar.jpg':$user->image}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"  class="rounded-circle" height="72" width="72" alt="Go to the profile of {{ucfirst($user->name)}}"></a>
                <div>
                    <div class="text-center">
                      <a class="d-block text-dark" href="{{ route('user_profile', ['username' => $user->username]) }}"  title="Go to the profile of {{ucfirst($user->name)}}"><strong>{{ucfirst($user->name)}}</strong></a>
                      <a class="d-block text-muted" href="{{ route('user_profile', ['username' => $user->username]) }}"><span style="font-style:normal;font-size:14px;">{{'@'.$user->username}}</span></a>

                        <center class="mt-2">
                            @if(Request::path()=='me/in_circle')
                            <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="display:{{!in_array($user->id,$followingids)?'block':'none'}}">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                            <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="display:{{in_array($user->id,$followingids)?'block':'none'}}">Following <span><i class="fas fa-check ml-2"></i><span></button>
                            @endif

                            @if(Request::path()=='me/circle')
                            <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success followbtn" style="display:none">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                            <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success followingbtn" style="display:{{Request::path()=='me/circle'?'block':'none'}}">Following<span><i class="fas fa-check ml-2"></i><span></button>
                            @endif

                            @if(Request::path()!='me/circle' && Request::path()!='me/in_circle')
                            @if(Auth::guest())
                            <button class="btn btn-sm btn-outline-success" onclick=" $('#forgotPasswordModal').modal('show');" data-toggle="tooltip" data-placement="top" title="Please Login To Add {{ucfirst($user->name)}} To Your Circle">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                            @else
                                @if($user->id != Auth::user()->id)
                                <button data-userid="{{ $user->id }}"  class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="display:{{!in_array($user->id,$followingids)?'block':'none'}}">Follow <span><i class="fas fa-user-plus ml-2"></i><span></button>
                                <button data-userid="{{ $user->id }}"  class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="display:{{in_array($user->id,$followingids)?'block':'none'}}">Following <span><i class="fas fa-check ml-2"></i><span></button>
                                @endif
                            @endif
                            @endif
                        </center>

                    </div>

                </div>
              </div>
          </div>
    </div>
