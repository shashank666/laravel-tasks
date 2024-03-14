<div class="row">
    <div class="offset-md-2 col-md-8 col-12">
       <!--@if(ucfirst($profile_user->cover_image)!=null)-->
       @php
    $extn = preg_match('/\./', ucfirst($profile_user->cover_image)) ? preg_replace('/^.*\./', '', ucfirst($profile_user->cover_image)) : '';
    @endphp
    <div class="row" style="background-image: url('{{ucfirst($profile_user->cover_image)!==null?preg_replace('/.[^.]*$/', '',ucfirst($profile_user->cover_image)).'_760x200'.'.'.$extn:'/storage/cover_image/cover.png'}}');
                          background-position:center;
                          background-repeat:no-repeat;
                          background-size:cover;
                          height:200px;
                          width:auto;
                          border-top-left-radius:10px;border-top-right-radius:10px;">
     <!-- @else
        <div class="row" style="background-image: url('/img/cover-def.png');
                          background-position:center;
                          background-repeat:no-repeat;
                          background-size:cover;
                          height:200px;
                          width:auto;">

       @endif -->
      <div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12 mb-md-0 mb-3 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
            @if($profile_user->image!=null)
             @php
              $ext = preg_match('/\./', $profile_user->image) ? preg_replace('/^.*\./', '', $profile_user->image) : '';
              $path=$profile_user->image;
$string="/profile";
$substring="avatar_thumb"
              @endphp
              <img class="rounded-circle" src="@php
    if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
    @endphp
    {{preg_replace('/.[^.]*$/', '',$profile_user->image).'_100x100'.'.'.$ext}}
    @php
    }
    else{
    @endphp
    {{$profile_user->image}}
    @php
}
    @endphp" alt="{{$profile_user->name}}"  height="150" width="150" onerror="this.onerror=null;this.src='https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png';"style="margin-left: -13px; position: absolute;bottom: 1px;border: #fff solid; left:50%;bottom:-14%;z-index:7;" />
            @else
              <img class="rounded-circle" src="https://d20g1jo8qvj2jf.cloudfront.net/storage/app/public/profile/icons8-male-user-96.png" alt="{{$profile_user->name}}" height="150" width="150" onerror="this.onerror=null;this.src='https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png';" style="margin-left: -13px; position: absolute;bottom: 1px; border: #fff solid;" />
            @endif
      </div>

      <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
        <h2 class="text-sm-left text-center font-weight-normal username-text" style="z-index:7;margin-bottom:10px;">{{ucfirst($profile_user->name)}}</h2>
              <br>
              <br>
              <i style="z-index:7;" class="username-sec-text">
              {{'@'.ucfirst($profile_user->username)}}</i>
            
            @if(ucfirst($profile_user->keywords)!=null || strlen(ucfirst($profile_user->keywords)>0))
            <i style="position: absolute;right: -110px;bottom: -60px; color: black; border: 2px black solid;border-radius: 25px;padding: 10px;background: transparent;background-clip: padding-box;z-index:7;">
              {{ucfirst($profile_user->keywords)}}
             </i>
              @endif
              </div>
          <!-- For Mobile -->
           <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12 mb-md-0 mb-3 d-md-none d-sm-inline d-inline" >
             @if($profile_user->image!=null)
              <img class="rounded-circle" src="{{$profile_user->image}}" alt="{{$profile_user->name}}"  height="auto" width="22%" onerror="this.onerror=null;this.src='/img/avatar.png';"/>
            @else
              <img class="rounded-circle" src="/storage/profile/avatar.jpg" alt="{{$profile_user->name}}" height="auto" width="22%" onerror="this.onerror=null;this.src='/img/avatar.png';"/>
            @endif
       </div>

      <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12 col-12 d-md-none d-sm-inline d-inline">
              
                <h5 class="text-sm-left text-center font-weight-normal" style="position: absolute;left:27%;font-size: 5vw;top: -8.2rem; color: #ff9800; background: #244363;border: 1px solid;border-radius: 8px;padding: 0px 7px 0px 7px;">{{ucfirst($profile_user->name)}}
                </h5>
                <br>
              <i style="position: absolute;left:27%;font-size: 3vw;top: -79pt; color: #ff9800; background: #244363;border: 1px solid;border-radius: 8px;padding: 0px 7px 0px 7px;">
              {{'@'.ucfirst($profile_user->username)}}
              </i> 

               @if(ucfirst($profile_user->keywords)!=null || strlen(ucfirst($profile_user->keywords)>0))
              <i style="position: absolute;bottom: 15px; color: #ff9800; border: 1px #ff9800 solid;border-radius: 18px;padding: 5px;background: #244363;background-clip: padding-box;">
               {{ucfirst($profile_user->keywords)}}
             </i>
              @endif
              <div style="position: absolute;right: 6px;bottom: 1px;">
          </div>
        </div>
           <!-- End Mobile View -->
         </div>
     <div class="row">
       <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="background-color: white;border-bottom-left-radius:10px;border-bottom-right-radius:10px;">
          <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
              <p class="text-secondary text-sm-left text-center profile-text-since" style="margin-left:10px;">Opined member since {{ Carbon\Carbon::parse($profile_user->created_at)->format('F , Y') }}</p>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
              @if(Auth::guest())
                <button class="followbtn btn btn-sm btn-outline-primary" style="float: right;margin-top: 75px"><span><i class="fas fa-user-plus mr-2"></i><span>Follow</button>
                @else
                  {{--  <button data-userid="{{$profile_user->id  }}" class="followingbtn followingbtn_{{$profile_user->id}} btn btn-sm btn-primary" style="margin-top:30px;display:{{in_array($profile_user->id,$followingids)?'inline':'none'}};float: right;"><span><i class="fas fa-check mr-2"></i><span>Following </button>  --}}
                    <button data-userid="{{$profile_user->id  }}" class="followingbtn followingbtn_{{$profile_user->id}} btn btn-sm btn-primary" style="margin-top:70px;display:{{in_array($profile_user->id,$followingids)?'inline':'none'}};float: right;"><span><i class="fas fa-check mr-2"></i><span>Following </button>
                      <button data-userid="{{ $profile_user->id }}" class="followbtn followbtn_{{ $profile_user->id }} btn btn-sm btn-outline-primary" style="display:{{!in_array($profile_user->id,$followingids)?'inline':'none'}};float: right;margin-top:70px;"><span><i class="fas fa-user-plus mr-2"></i><span>Follow</button>    
              @endif
            </div>
          </div>

          {{--  <div class="profile-position row my-3" style="display: flex; justify-content: center; flex-direction:column;">
            <div class="row" style="justify-content: center;border-top:10px solid #f3f3f5;padding-top:40px;">
              <h5 style="color: #0f0f0f; font-size: 2rem;">{{$rank ?? ''}}</h5>
            </div>
            <div class="profress-wrapper row" style="justify-content: center;padding-bottom:40px;"> 
              <div id="myProgress" style="width: 90%; background-color: #f7e0ca; border-radius: 5px;">
                <div id="myBar" style="width: {{$width ?? ''}}%; height: 13px; background-color: #e57e26; border-radius: 8px;"> 
                </div>
              </div>
            </div>
          </div>  --}}

          <p class="lead text-sm-left text-center">{{$profile_user->bio==null?'':$profile_user->bio}}</p>
          <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
              <p class="text-sm-left text-center">

                @if($profile_user->website_url!==null)
                  <a target="_blank" href="{{$profile_user->website_url}}" data-toggle="tooltip" data-placement="top" title="{{$profile_user->name}} on Web"><i class="fas fa-globe mr-3" style="color:#00796b;font-size:18px"></i></a>
                @endif

                @if($profile_user->facebook_url!==null)
                  <a target="_blank" href="{{$profile_user->facebook_url}}" data-toggle="tooltip" data-placement="top" title="{{$profile_user->name}} on Facebook"><i class="fab fa-facebook mr-3" style="color:#3b5998;font-size:18px"></i></a>
                @endif

                @if($profile_user->twitter_url!==null)
                  <a target="_blank"  href="{{$profile_user->twitter_url}}" data-toggle="tooltip" data-placement="top" title="{{$profile_user->name}} on Twitter"><i class="fab fa-twitter  mr-3" style="color:#1da1f2;font-size:18px"></i></a>
                @endif


                @if($profile_user->linkedin_url!=null)
                  <a target="_blank" href="{{$profile_user->linkedin_url}}" data-toggle="tooltip" data-placement="top" title="{{$profile_user->name}} on Linkedin"><i class="fab fa-linkedin mr-3" style="color:#0077b5;font-size:18px"></i></a>
                @endif

                @if($profile_user->instagram_url!==null)
                  <a target="_blank"  href="{{$profile_user->instagram_url}}" data-toggle="tooltip" data-placement="top" title="{{$profile_user->name}} on Instagram"><i class="fab fa-instagram  mr-3" style="color:#e1306c;font-size:18px"></i></a>
                @endif

                @if($profile_user->youtube_channel_url!==null)
                  <a target="_blank"  href="{{ $profile_user->youtube_channel_url}}" data-toggle="tooltip" data-placement="top" title="{{ $profile_user->name}} on Youtube"><i class="fab fa-youtube  mr-3" style="color:#ff0000;font-size:18px"></i></a>
                @endif
              </p>
            </div>
             <!-- <hr class="my-2">-->
            {{--  <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
              <p class="lead text-sm-right text-right">
              	@if(count($profile_user->active_followings)>0)
                  <a  style="font-size:16px" class="text-center font-weight-normal text-secondary mb-2  mb-2 mr-md-4 d-sm-inline d-block"  href="{{route('user_circle',['username' => $profile_user->username])}}">{{$profile_user->name}} Following {{count($profile_user->active_followings)}}<span><i class="fas fa-users ml-2"></i></span></a>
                @endif

                @if(count($profile_user->active_followers)>0)
                  <a style="font-size:16px" class="text-center font-weight-normal text-secondary mb-2 d-sm-inline d-block"  href="{{route('user_in_circle',['username' => $profile_user->username])}}">{{count($profile_user->active_followers)}} Followers<span><i class="fas fa-user-plus ml-2"></i></span></a>
                @endif
              </p>
            </div>  --}}
          </div>
      </div>
    </div>
  </div>
</div>
</div>
