
  
  
    <div class="col-12">
        <div class="row">
          @php($colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'])
          @foreach($contributors as $user)
          @if(ucfirst($user->id)!=auth()->user()->id)
           <div class="col-sm-6 col-6 mb-2 d-xl-none d-lg-none d-md-none d-sm-inline d-inline" style="padding-left:8px;padding-right:8px;">
             <div class="thread_card card bg-light p-2 text-center shadow-sm bg-card" style="border:0px;margin-bottom: 0.25rem!important;">
              <div class="row">
              <div class="col-md-2">
              <a href="{{ route('user_profile', ['username' => $user->username]) }}" title="Go to the profile of {{ucfirst($user->name)}}"><img src="{{$user->image==null?'/storage/profile/avatar.jpg':$user->image}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"  class="rounded-circle" height="36" width="36" alt="Go to the profile of {{ucfirst($user->name)}}"></a>
            </div>
                  <div class="col-md-6" style="padding-top: 5px;">
                      <a href="{{ route('user_profile', ['username' => $user->username]) }}"  title="Go to the profile of {{ucfirst($user->name)}}">
                         <h6 class="text-truncate" style="color:{{$colors[array_rand($colors,1)]}}">{{ucfirst($user->name)}}
                      </h6></a>
                      <a class="d-block text-muted" href="{{ route('user_profile', ['username' => $user->username]) }}"></a>

                  </div>
                    
                 <div class="col-md-2" >
                       
                    @if(Request::path()=='me/in_circle')
                    <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="margin-left: 40%;display:{{!in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-user-plus"></i><span></button>
                    <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="margin-left: 40%;display:{{in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-check"></i><span></button>
                    @endif

                    @if(Request::path()=='me/circle')
                    <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success followbtn" style="margin-left: 40%;display:none"><span><i class="fas fa-user-plus"></i><span></button>
                    <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success followingbtn" style="margin-left: 40%;display:{{Request::path()=='me/circle'?'block':'none'}}"><span><i class="fas fa-check "></i><span></button>
                    @endif

                    @if(Request::path()!='me/circle' && Request::path()!='me/in_circle')
                      @if(Auth::guest())
                      <button class="btn btn-sm btn-outline-success" onclick=" $('#forgotPasswordModal').modal('show');" data-toggle="tooltip" data-placement="top" title="Please Login To Add {{ucfirst($user->name)}} To Your Circle"><span><i class="fas fa-user-plus "></i><span></button>
                      @else
                          @if($user->id != Auth::user()->id)
                          <button data-userid="{{ $user->id }}"  class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="margin-left: 40%;display:{{!in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-user-plus "></i><span></button>
                          <button data-userid="{{ $user->id }}"  class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="margin-left: 40%;display:{{in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-check "></i><span></button>
                          @endif
                      @endif
                    @endif
                  </div>
                </div>

               </div>

          </div>
           @endif
          @endforeach
        </div>
    </div>
   

