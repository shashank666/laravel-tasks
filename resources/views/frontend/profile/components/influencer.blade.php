@php
  $count=0;
@endphp
  @foreach($influencers as $user)
  @if(ucfirst($user->id)!=auth()->user()->id)
    <div class="trending-card-individual card-body d-flex flex-row justify-content-between align-items-center" style="padding:5px">
      <div class="col-12">
          <div class="row">
            @php($colors=['rgba(36,67,99,255)'])
             <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mb-2" style="padding-left:8px;padding-right:8px;">
               <div class="thread_card p-2 text-center bg-card" style="border:0px;margin-bottom: 0.25rem!important;">
                <div class="row">
                    <div class="col-md-2 pr-4">
                      <a href="{{ route('user_profile', ['username' => $user->username]) }}" title="Go to the profile of {{ucfirst($user->name)}}"><img src="{{$user->image==null?'/storage/profile/avatar.jpg':$user->image}}"  onerror="this.onerror=null;this.src='/img/avatar.png';"  class="rounded-circle" height="36" width="36" alt="Go to the profile of {{ucfirst($user->name)}}"></a>
                    </div>
                    <div class="col-md-6" style="padding-top: 5px;">
                        <a href="{{ route('user_profile', ['username' => $user->username]) }}"  title="Go to the profile of {{ucfirst($user->name)}}">
                           <h6 class="text-truncate" style="text-align: left;color:{{$colors[array_rand($colors,1)]}}">{{ucfirst($user->name)}}
                        </h6></a>
                        <a class="d-block text-muted" href="{{ route('user_profile', ['username' => $user->username]) }}"></a>
                    </div>
      
                   <div class="col-md-2" >
      
                      @if(Request::path()=='me/in_circle')
                      <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="display:{{!in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-user-plus"></i><span></button>
                      <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="display:{{in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-check"></i><span></button>
                      @endif
                      @if(Request::path()=='me/circle')
                      <button data-userid="{{ $user->id }}" class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success followbtn" style="display:none"><span><i class="fas fa-user-plus"></i><span></button>
                      <button data-userid="{{ $user->id }}" class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success followingbtn" style="display:{{Request::path()=='me/circle'?'block':'none'}}"><span><i class="fas fa-check "></i><span></button>
                      @endif
                      @if(Request::path()!='me/circle' && Request::path()!='me/in_circle')
                        @if(Auth::guest())
                        <button class="btn btn-sm btn-outline-success" onclick=" $('#forgotPasswordModal').modal('show');" data-toggle="tooltip" data-placement="top" title="Please Login To Add {{ucfirst($user->name)}} To Your Circle"><span><i class="fas fa-user-plus "></i><span></button>
                        @else
                            @if($user->id != Auth::user()->id)
                            <button data-userid="{{ $user->id }}"  class="followbtn followbtn_{{$user->id}} btn btn-sm btn-outline-success" style="display:{{!in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-user-plus "></i><span></button>
                            <button data-userid="{{ $user->id }}"  class="followingbtn followingbtn_{{$user->id}} btn btn-sm btn-success" style="display:{{in_array($user->id,$followingids)?'block':'none'}}"><span><i class="fas fa-check "></i><span></button>
                            @endif
                        @endif
                      @endif
                    </div>
                  </div>
                 </div>
            </div>
          </div>
      </div>
    </div>
    
    @if(($count++)==10)
    @break
    @endif
    @endif
  @endforeach

