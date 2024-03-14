<div class="row">
  <div class="offset-md-2 col-md-8 col-12">
    <!-- @if(Auth::user()->cover_image!=null)-->
    @php
    $extn = preg_match('/\./', Auth::user()->cover_image) ? preg_replace('/^.*\./', '', Auth::user()->cover_image) : '';
    @endphp
    <div class="row" style="background-image: url('{{Auth::user()->cover_image!==null?preg_replace('/.[^.]*$/', '',Auth::user()->cover_image).'_760x200'.'.'.$extn:'/storage/cover_image/cover.png'}}');
                          background-position:center;
                          background-repeat:no-repeat;
                          background-size:cover;
                          height:200px;
                          width:auto;border-top-left-radius:20px;border-top-right-radius:20px;">
      <!-- @else
        <div class="row" style="background-image: url('/img/cover-def.png');
                          background-position:center;
                          background-repeat:no-repeat;
                          background-size:cover;
                          height:200px;
                          width:auto;">

       @endif -->
      <div class="col-xl-1 col-lg-1 col-md-2 col-sm-12 col-12 mb-md-0 mb-3 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
        @if(Auth::user()->image!=null)
        <div class="form-group row" style="margin-left: -29px; position: absolute;bottom: -15px;">
          <div class="col-sm-9">
            <form id="upload-profileimage-form" action="{{ route('upload',['event'=>'USER_PROFILE']) }}" method="POST" enctype="multipart/form-data">
              <div class="hero-avatar">
                @php
                $ext = preg_match('/\./', Auth::user()->image) ? preg_replace('/^.*\./', '', Auth::user()->image) : '';
                $path=Auth::user()->image;
                $string="/profile";
                $substring="avatar_thumb"
                @endphp
                <div class="avatar" style="z-index:7;"><img style="margin-left:30%;" id="avatar-img" src="@php
                                            if(strpos($path, $string) !== false || strpos($path, $substring) !== false){
                                            @endphp
                                                {{preg_replace('/.[^.]*$/', '',Auth::user()->image).'_100x100'.'.'.$ext}}
                                            @php
                                            }
                                            else{
                                            @endphp
                                            {{Auth::user()->image}}
                                            @php
                                        }
                                            @endphp" alt="{{ucfirst(Auth::user()->name)}}" class="avatar-image" onerror="this.onerror=null;this.src='https://opined-s3.s3.ap-south-1.amazonaws.com/images/favicon.png';" alt="{{Auth::user()->name}}"></div>
                <div class="hero-avatarPicker" style="border: white solid;margin-left:20%;z-index:7;">
                  <input type="file" accept=".jpg,.jpeg,.png" id="profileimage" name="profileimage" style="display:none;outline: none;" />
                  <button class="button button--light button--chromeless u-baseColor--buttonLight button--withIcon button--withSvgIcon u-lineHeight100 is-touched" title="Update Your Profile Image" id="choosefile">
                    <span class="svgIcon svgIcon--65px">
                      <svg class="svgIcon-use" width="65" height="65" viewBox="0 0 65 65">
                        <g fill-rule="evenodd">
                          <path d="M10.61 44.486V23.418c0-2.798 2.198-4.757 5.052-4.757h6.405c1.142-1.915 2.123-5.161 3.055-5.138L40.28 13.5c.79 0 1.971 3.4 3.073 5.14 0 .2 6.51 0 6.51 0 2.856 0 5.136 1.965 5.136 4.757V44.47c-.006 2.803-2.28 4.997-5.137 4.997h-34.2c-2.854.018-5.052-2.184-5.052-4.981zm5.674-23.261c-1.635 0-3.063 1.406-3.063 3.016v19.764c0 1.607 1.428 2.947 3.063 2.947H49.4c1.632 0 2.987-1.355 2.987-2.957v-19.76c0-1.609-1.357-3.016-2.987-3.016h-7.898c-.627-1.625-1.909-4.937-2.28-5.148 0 0-13.19.018-13.055 0-.554.276-2.272 5.143-2.272 5.143l-7.611.01z"></path>
                          <path d="M32.653 41.727c-5.06 0-9.108-3.986-9.108-8.975 0-4.98 4.047-8.966 9.108-8.966 5.057 0 9.107 3.985 9.107 8.969 0 4.988-4.047 8.974-9.107 8.974v-.002zm0-15.635c-3.674 0-6.763 3.042-6.763 6.66 0 3.62 3.089 6.668 6.763 6.668 3.673 0 6.762-3.047 6.762-6.665 0-3.616-3.088-6.665-6.762-6.665v.002z"></path>
                        </g>
                      </svg>
                    </span>
                  </button>
                </div>
              </div>
            </form>
            <form id="edit_profile_form" class="form-horizontal" action="{{route('update_profile_picture')}}" method="POST">
              {{csrf_field()}}
              <input type="hidden" id="profileimageurl" name="profileimageurl" value="{{ Auth::user()->image }}" />
              <button type="submit" id="submitform" class="btn btn-sm btn-success" style="display: none; margin-left: 19px;  background: transparent;">Update</button>
            </form>
          </div>
        </div>
        <!--
              <img class="rounded-circle" style="margin-left: -13px; position: absolute;bottom: 1px;" src="{{Auth::user()->image}}" alt="{{Auth::user()->name}}"  height="100" width="100" onerror="this.onerror=null;this.src='/img/avatar.png';"/>-->
        @else
        <img class="rounded-circle" style="margin-left: -13px; position: absolute;bottom: 1px;border: #fff solid;" src="https://opined-s3.s3.ap-south-1.amazonaws.com/images/favicon.png" alt="{{Auth::user()->name}}" height="100" width="100" onerror="this.onerror=null;this.src='https://opined-s3.s3.ap-south-1.amazonaws.com/images/favicon.png';" />
        @endif
      </div>
      <div class="col-xl-11 col-lg-11 col-md-10 col-sm-12 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
        <div style="position: absolute;left: 94%;top: -16%;">
          <form id="upload-cover_image-form" action="{{ route('upload',['event'=>'USER_COVER']) }}" method="POST" enctype="multipart/form-data">
            <input type="file" accept=".jpg,.jpeg,.png" id="cover_image" name="cover_image" style="display: none" />
            <button class="button button--light button--chromeless u-baseColor--buttonLight  is-touched" style="line-height: 2rem !important;margin-top: 100%;" title="Update Your Cover Image" id="choosecover">
              <img class="cover-back" src="/img/camera_icon.png" height="30" width="30" />
            </button>
          </form>

          <form id="edit_profile_form_cover" class="form-horizontal" action="{{route('update_cover_picture')}}" method="POST">
            {{csrf_field()}}
            <input type="hidden" id="cover_imageurl" name="cover_imageurl" value="{{ Auth::user()->cover_image }}" />
            <button type="submit" id="submitcover" class="btn btn-sm btn-success btn-outline-none" style="display: none;margin-left: -24px; background: transparent;">Update</button>
          </form>

        </div>
        <h2 class="text-sm-left username-text text-center font-weight-bold pl-lg-3 pl-xl-0 pl-md-0 pl-sm-0 pl-xs-0" style="z-index:7;">
          {{Auth::user()->name}}
          <div style="position: absolute;bottom: 0%;left:33%;">
            <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeNameModal" role="button" style="background: #faebd700;color: aliceblue; cursor: pointer;" title="Change Name">
              <span><i class="fas fa-pencil-alt"></i></span>
            </a>
          </div>
        </h2>

        <br>
        <br>
        <i class="pl-lg-3 pl-xl-0 pl-md-0 pl-sm-0 pl-xs-0 username-sec-text" style="z-index:7;">
          {{'@'.Auth::user()->username}}
          <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeUsernameModal" role="button" style="background: #faebd700;color: aliceblue; cursor: pointer;" title="Change User Name">
            <span><i class="fas fa-pencil-alt"></i></span>
          </a>
        </i>
        <i class="user-keywords new-keywords"><strong>
            <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeKeywordModal" role="button" style="cursor: pointer;" title="Change Three Word">
              @if(Auth::user()->keywords==null || strlen(Auth::user()->keywords)==0)
              Three Words That Describe You
              @else
              {{Auth::user()->keywords}}
              @endif
            </a>
          </strong></i>
      </div>
      <!-- For Mobile View -->
      <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12 mb-md-0 mb-3 d-md-none d-sm-inline d-inline">
        @if(Auth::user()->image!=null)
        <div class="form-group row">

          <div class="col-sm-9">
            <form id="upload-profileimage-form" action="{{ route('upload',['event'=>'USER_PROFILE']) }}" method="POST" enctype="multipart/form-data">
              <div class="hero-avatar">
                <div class="avatar"><img id="avatar-img" src="{{Auth::user()->image!==null?Auth::user()->image:'/storage/profile/avatar.jpg'}}" class="avatar-image" alt="{{Auth::user()->name}}"></div>
                <div class="hero-avatarPicker" style="width: 20%;height: auto;left: 3.3%;">
                  <input type="file" accept=".jpg,.jpeg,.png" id="profileimage" name="profileimage" style="display:none;outline: none;" />
                  <button class="button button--light button--chromeless u-baseColor--buttonLight button--withIcon button--withSvgIcon u-lineHeight100 is-touched" title="Update Your Profile Image" id="choosefile">
                    <span class="svgIcon svgIcon--65px">
                      <svg class="svgIcon-use" width="65" height="65" viewBox="0 0 65 65">
                        <g fill-rule="evenodd">
                          <path d="M10.61 44.486V23.418c0-2.798 2.198-4.757 5.052-4.757h6.405c1.142-1.915 2.123-5.161 3.055-5.138L40.28 13.5c.79 0 1.971 3.4 3.073 5.14 0 .2 6.51 0 6.51 0 2.856 0 5.136 1.965 5.136 4.757V44.47c-.006 2.803-2.28 4.997-5.137 4.997h-34.2c-2.854.018-5.052-2.184-5.052-4.981zm5.674-23.261c-1.635 0-3.063 1.406-3.063 3.016v19.764c0 1.607 1.428 2.947 3.063 2.947H49.4c1.632 0 2.987-1.355 2.987-2.957v-19.76c0-1.609-1.357-3.016-2.987-3.016h-7.898c-.627-1.625-1.909-4.937-2.28-5.148 0 0-13.19.018-13.055 0-.554.276-2.272 5.143-2.272 5.143l-7.611.01z"></path>
                          <path d="M32.653 41.727c-5.06 0-9.108-3.986-9.108-8.975 0-4.98 4.047-8.966 9.108-8.966 5.057 0 9.107 3.985 9.107 8.969 0 4.988-4.047 8.974-9.107 8.974v-.002zm0-15.635c-3.674 0-6.763 3.042-6.763 6.66 0 3.62 3.089 6.668 6.763 6.668 3.673 0 6.762-3.047 6.762-6.665 0-3.616-3.088-6.665-6.762-6.665v.002z"></path>
                        </g>
                      </svg>
                    </span>
                  </button>
                </div>
              </div>
            </form>
            <form id="edit_profile_form" class="form-horizontal" action="{{route('update_profile_picture')}}" method="POST">
              {{csrf_field()}}
              <input type="hidden" id="profileimageurl" name="profileimageurl" value="{{ Auth::user()->image }}" />
              <button type="submit" id="submitform" class="btn btn-success" style="display: none"><i class="fa fa-check" aria-hidden="true"></i></button>
            </form>
          </div>
        </div>


        <!--              <img class="rounded-circle" src="{{Auth::user()->image}}" alt="{{Auth::user()->name}}"  height="95" width="95" onerror="this.onerror=null;this.src='/img/avatar.png';"/>-->
        @else
        <img class="rounded-circle" src="/storage/profile/avatar.jpg" alt="{{Auth::user()->name}}" height="auto" width="22%" onerror="this.onerror=null;this.src='/img/avatar.png';" />
        @endif
      </div>

      <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12 col-12 d-md-none d-sm-inline d-inline">
        <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeNameModal" role="button" style="cursor: pointer;" title="Change Name">
          <h2 class="text-sm-left text-center font-weight-bold" style="position: absolute;left:27%;font-size: 5vw;top: -8.5rem; color: #ff9800; background: #244363;border: 1px solid;border-radius: 8px;padding: 0px 7px 0px 7px;">{{Auth::user()->name}}
          </h2>
        </a>
        <br>
        <i style="position: absolute;left:27%;font-size: 3vw;top: -82pt; color: #ff9800; background: #244363;border: 1px solid;border-radius: 8px;padding: 0px 7px 0px 7px;">
          {{'@'.Auth::user()->username}}

          <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeUsernameModal" role="button" style="background: #faebd700;color: aliceblue; cursor: pointer;font-size: 9px;" title="Change User Name">
            <i class="fas fa-pencil-alt"></i>
          </a>
        </i>
        <i style="position: absolute;bottom: 15px; color: #ff9800; border: 1px #ff9800 solid;border-radius: 18px;padding: 5px;background: #244363;background-clip: padding-box;">

          <a class="btn btn-sm btn-outline-none" data-toggle="modal" data-target="#changeKeywordModal" role="button" style="cursor: pointer;" title="Change Three Word">
            @if(Auth::user()->keywords==null || strlen(Auth::user()->keywords)==0)
            Three Words That Describe You
            @else
            {{Auth::user()->keywords}}
            @endif
          </a>
        </i>
        <div style="position: absolute;right: 6px;bottom: 1px;">
          <form id="upload-cover_image-form" action="{{ route('upload',['event'=>'USER_COVER']) }}" method="POST" enctype="multipart/form-data">
            <input type="file" accept=".jpg,.jpeg,.png" id="cover_image" name="cover_image" style="display: none" />
            <button class="button button--light button--chromeless u-baseColor--buttonLight  is-touched" style="line-height: 44px!important;margin-top: 25px;" title="Update Your Cover Image" id="choosecover">
              <img class="cover-back" src="/img/camera_icon.png" height="30" width="30" />
            </button>
          </form>

          <form id="edit_profile_form" class="form-horizontal" action="{{route('update_cover_picture')}}" method="POST">
            {{csrf_field()}}
            <input type="hidden" id="cover_imageurl" name="cover_imageurl" value="{{ Auth::user()->cover_image }}" />
            <button type="submit" id="submitcover" class="btn btn-sm btn-success" style="display: none;margin-left: -24px;">Update</button>
          </form>

        </div>

      </div>
      <!-- End Mobile View -->
    </div>
    <div class="row">
      <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="background-color:white;border-bottom-left-radius:20px;border-bottom-right-radius:20px;">
        <p class="text-secondary text-sm-left text-center profile-text-since">Opined member since {{ Carbon\Carbon::parse(Auth::user()->created_at)->format('F , Y') }}
          <span class="ml-md-4 md-sm-2 md-2" style="position: absolute;right: 1px;">
            <a class="btn btn-sm btn-light" href="{{route('edit_profile')}}" role="button" style="margin-right:1.25rem;">
              <span><i class="fas fa-pencil-alt"></i></span>
              <span class="ml-2 d-md-inline d-none">Edit Profile</span>
            </a>
          </span>
        </p>
        {{--  <div class="profile-position row" style="display: flex; justify-content: center; flex-direction:column;">
          <div class="row" style="justify-content: center;border-top:20px solid #f3f3f5;padding-top:40px;">
            <h5 style="color: #131414; font-size: 2rem;">{{$rank ?? ''}}</h5>
          </div>
          <div class="profress-wrapper row" style="justify-content: center;
          border-bottom:20px solid #f3f3f5;padding-bottom:50px;"> 
            <div id="myProgress" style="width: 90%; background-color: #f7e0ca; border-radius: 5px;">
              <div id="myBar" style="width: {{$width ?? ''}}%; height: 13px; background-color: #e57e26;border-radius: 8px;"> 
              </div>
            </div>
          </div>
        </div>  --}}
        <p class="lead text-sm-left text-center mt-2">
          @if(Auth::user()->bio==null || strlen(Auth::user()->bio)==0)
          <a href="{{ route('edit_profile') }}" style="color:#616161">Write a description about yourself</a>
          @else
          <i class="pb-3 mb-3 font-weight-normal" style="font-size: 25px;">About Me</i>
          <br>{{ Auth::user()->bio}}
          @endif
        </p>
        <div class="row">
          <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
            <p class="text-sm-left text-center">
              @if(Auth::user()->website_url!==null)
              <a target="_blank" href="{{Auth::user()->website_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Web"><i class="fas fa-globe mr-3" style="color:#00796b;font-size:20px"></i></a>
              @endif
              @if(Auth::user()->facebook_url!==null)
              <a target="_blank" href="{{Auth::user()->facebook_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Facebook"><i class="fab fa-facebook mr-3" style="color:#3b5998;font-size:20px"></i></a>
              @endif

              @if(Auth::user()->twitter_url!==null)
              <a target="_blank" href="{{Auth::user()->twitter_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Twitter"><i class="fab fa-twitter  mr-3" style="color:#1da1f2;font-size:20px"></i></a>
              @endif


              @if(Auth::user()->linkedin_url!=null)
              <a target="_blank" href="{{Auth::user()->linkedin_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Linkedin"><i class="fab fa-linkedin mr-3" style="color:#0077b5;font-size:20px"></i></a>
              @endif

              @if(Auth::user()->instagram_url!==null)
              <a target="_blank" href="{{Auth::user()->instagram_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Instagram"><i class="fab fa-instagram  mr-3" style="color:#e1306c;font-size:20px"></i></a>
              @endif

              @if(Auth::user()->youtube_channel_url!==null)
              <a target="_blank" href="{{Auth::user()->youtube_channel_url}}" data-toggle="tooltip" data-placement="top" title="{{Auth::user()->name}} on Youtube"><i class="fab fa-youtube  mr-3" style="color:#ff0000;font-size:20px"></i></a>
              @endif

              @if(Auth::user()->website_url==null &&
              Auth::user()->facebook_url==null &&
              Auth::user()->twitter_url==null &&
              Auth::user()->linkedin_url==null &&
              Auth::user()->instagram_url==null &&
              Auth::user()->youtube_channel_url==null)

              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your website link"><i class="fas fa-globe mr-3" style="color:#616161;font-size:20px"></i></a>
              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your facebook profile link"><i class="fab fa-facebook  mr-3" style="color:#616161;font-size:20px"></i></a>
              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your twitter profile link"><i class="fab fa-twitter  mr-3" style="color:#616161;font-size:20px"></i></a>
              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your linkedin profile link"><i class="fab fa-linkedin  mr-3" style="color:#616161;font-size:20px"></i></a>
              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your instagram profile link"><i class="fab fa-instagram mr-3" style="color:#616161;font-size:20px"></i></a>
              <a href="{{ route('edit_profile') }}" data-toggle="tooltip" data-placement="top" title="Add your youtube channel link"><i class="fab fa-youtube mr-3" style="color:#616161;font-size:20px"></i></a>
              @endif

            </p>
          </div>
          <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">
            <!--<hr class="my-2">-->
            <p class="text-sm-right text-right">

              @if(Request::path()=='me/in_circle' || Request::path()=='me/circle')
              <a style="font-size:16px" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2 mr-2" href="{{route('profile')}}"><span><i class="fa fa-comment mr-2"></i></span>Opinions</a>
              @endif

              @if(count(Auth::user()->active_followings)>0 && Request::path()=='me/profile' || Request::path()=='me/in_circle')
              <a style="font-size:16px" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2 mr-md-4" href="{{route('circle')}}"><span><i class="fas fa-user-plus mr-2"></i></span>{{count(Auth::user()->active_followings)}} Following</a>
              @endif

              @if(count(Auth::user()->active_followers)>0 && Request::path()=='me/profile' || Request::path()=='me/circle')
              <a style="font-size:16px" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2" href="{{route('in_circle')}}"><span><i class="fas fa-users mr-2"></i></span> {{count(Auth::user()->active_followers)}} Followers</a>
              @endif

            </p>
          </div>
          <!-- FOR MOBILES AND TABLET --->

          <p class="text-sm-right text-right">
            @if(Request::path()=='me/in_circle' || Request::path()=='me/circle')
          <div class="col-sm-6 col-6 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
            <a style="font-size:3.5vw" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2 mr-md-4" href="{{route('profile')}}"><span><i class="fa fa-comment mr-2"></i></span>Opinions</a>
          </div>
          @endif
          @if(count(Auth::user()->active_followings)>0 && Request::path()=='me/profile' || Request::path()=='me/in_circle')
          <div class="col-sm-6 col-6 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
            <a style="font-size:3.5vw" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2 mr-md-4" href="{{route('circle')}}"><span><i class="fas fa-user-plus mr-2"></i></span>{{count(Auth::user()->active_followings)}} Following</a>
          </div>
          @endif
          @if(count(Auth::user()->active_followers)>0 && Request::path()=='me/profile' || Request::path()=='me/circle')
          <div class="col-sm-6 col-6 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
            <a style="font-size:3.5vw" class="text-center font-weight-normal text-secondary d-lg-inline d-sm-block d-block mb-2" href="{{route('in_circle')}}"><span><i class="fas fa-users mr-2"></i></span>{{count(Auth::user()->active_followers)}} Followers</a>
          </div>
          @endif
          </p>
        </div>
        <!-- END FOR MOBILES AND TABLET --->
      </div>
      <div class="input-group input-group-sm my-3">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="fas fa-link mr-2"></i>
            Opined Profile Link
          </span>
        </div>
        <input id="profileurl" type="text" class="form-control" value="{{url('/@'.Auth::user()->username)}}" readonly />
        <div class="input-group-append">
          <button class="btn bg-opined-dark-blue text-white" type="button" id="copyProfileLink"><i class="far fa-copy" style="color:black;"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>