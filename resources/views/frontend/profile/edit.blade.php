@extends('frontend.layouts.app')
@section('title','Edit Profile - Opined')

@push('styles')
<link href="/css/custom/edit-profile.css?<?php echo time();?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/vendor/datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
@endpush

@push('scripts')
@if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js" type="text/javascript"></script>
<script src="/vendor/datetimepicker/build/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="/js/autosize.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {

        $('#birthdate').datetimepicker({
            format:'DD/MM/YYYY',
            collapse: true,
            maxDate:moment(),
            useCurrent: false,
        });

        autosize($('#bio'));
    });

    $(document).on('click','#choosefile',function(e){
        e.preventDefault();
        $('#profileimage').click();
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#avatar-img').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on('change',"#profileimage",function(){
            var _this=this;
            var form = $('#upload-profileimage-form')[0];
            var formData = new FormData(form);
            if($('#profileimage')[0].files[0] !== undefined){
                $.ajax({
                    url:'/file/upload/USER_PROFILE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    type:'POST',
                    dataType: 'json',
                    data:formData,
                    cache : false,
                    contentType: false,
                    processData: false,
                    success:function(response){
                        if (response.status == 'success'){
                            $('#profileimageurl').val(response.image);
                            readURL(_this);
                        }
                    },error:function(err){

                    }
                });
            }
        });
</script>
@endpush

@section('content')
                <div class="row">
                    <div class="offset-md-1 col-md-10 col-sm-12 col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-opined-blue">
                            <h5 class="font-weight-normal text-white mb-0">Edit Profile</h5>
                        </div>
                        <div class="card-body mx-2">

                                @include('frontend.partials.message')
                               <!-- <form id="upload-profileimage-form" action="{{ route('upload',['event'=>'USER_PROFILE']) }}" method="POST" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="profileimage">
                                        <span class="mr-2"><i class="fas fa-image"></i></span>
                                        Image</label>
                                    <div class="col-sm-9">
                                        <div class="hero-avatar">
                                            <div class="avatar"><img id="avatar-img" src="{{Auth::user()->image!==null?Auth::user()->image:'/storage/profile/avatar.jpg'}}" class="avatar-image" alt="{{Auth::user()->name}}"></div>
                                            <div class="hero-avatarPicker">
                                                    <input type="file" accept=".jpg,.jpeg,.png" id="profileimage" name="profileimage" style="display:none;outline: none;"/>
                                                    <button class="button button--light button--chromeless u-baseColor--buttonLight button--withIcon button--withSvgIcon u-lineHeight100 is-touched" title="Upload an optional image" id="choosefile">
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
                                    </div>
                                </div>
                                </form>-->
                                <form id="edit_profile_form" class="form-horizontal" action="{{route('update_profile')}}" method="POST">
                                    {{csrf_field()}}
                                <input type="hidden" id="profileimageurl" name="profileimageurl" value="{{ Auth::user()->image }}"/>
                                <!--<div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="name">
                                        <span class="mr-2"><i class="fas fa-user"></i></span>Name</label>
                                    <div class="col-sm-5">-->
                                    <input type="hidden" class="form-control text-secondary" id="name" name="name" value="{{Auth::user()->name}}" required/>
                                    <!--</div>
                                </div>-->
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="email">
                                            <span class="mr-2"><i class="fas fa-envelope"></i></span>
                                        Email</label>
                                    <div class="col-sm-5">
                                    <p class="text-secondary">{{Auth::user()->email}}
                                    <span class="ml-md-3 ml-1"><a class="btn btn-sm btn-light" href="/me/settings"><i class="fas fa-pencil-alt mr-2"></i>Change</a></span>
                                    <p>
                                    </div>
                                </div>

                                <div class="form-group row">
                                        <label class="col-sm-3 col-form-label text-secondary" for="mobile">
                                                <span class="mr-2"><i class="fas fa-mobile-alt"></i></span>
                                            Mobile</label>
                                        <div class="col-sm-5">
                                        <p class="text-secondary">
                                            @if(Auth::user()->mobile!==null)
                                            {{  Auth::user()->phone_code.Auth::user()->mobile }}
                                            <span class="ml-md-3 ml-1"><a class="btn btn-sm btn-light"  href="/me/settings"><i class="fas fa-pencil-alt mr-2"></i>Change</a></span>
                                            @else
                                              You have not added your mobile number yet
                                              <span class="ml-md-3 ml-1"><a class="btn btn-sm btn-light"  href="/me/settings"><i class="fas fa-plus mr-2"></i>Add</a></span>
                                            @endif
                                        </p>
                                        </div>
                                 </div>


                                <!-- <div class="form-group row">
                                        <label class="col-sm-3 col-form-label text-secondary" for="keywords">
                                            <span class="mr-2"><i class="fa fa-bullhorn"></i></span>
                                            Three Words</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="keywords" name="keywords"  class="form-control text-secondary" placeholder="Three Words That Describe You" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Three Words That Describe You'"value="{{ Auth::user()->keywords!=null?(Auth::user()->keywords):null }}"/>
                                        </div>
                                </div>
                                 -->
                                 <input type="hidden" id="keywords" name="keywords"  class="form-control text-secondary" placeholder="Three Words That Describe You" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Three Words That Describe You'"value="{{ Auth::user()->keywords!=null?(Auth::user()->keywords):null }}"/>
                                <div class="form-group row">
                                        <label class="col-sm-3 col-form-label text-secondary" for="bio">
                                            <span class="mr-2"><i class="fas fa-address-card"></i></span>
                                            About You </label>
                                        <div class="col-sm-9">
                                        <textarea class="form-control text-secondary" id="bio" placeholder="Add a short bio." name="bio" rows="4">{{Auth::user()->bio!==null?Auth::user()->bio:''}}</textarea>
                                        </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="bio">
                                            <span class="mr-2"><i class="fas fa-venus-mars"></i></span>
                                            Gender</label>
                                    <div class="col-sm-9">
                                            <div class="form-check mr-3" style="display:inline;cursor:pointer">
                                                    <input class="form-check-input" type="radio" name="gender" id="male" value="male" style="cursor:pointer" required {{ Auth::user()->gender=='male'?'checked':'' }}>
                                                    <label class="form-check-label text-secondary" for="male" style="cursor:pointer">
                                                      Male
                                                    </label>
                                            </div>

                                            <div class="form-check mr-3" style="display:inline;cursor:pointer">
                                                    <input class="form-check-input" type="radio" name="gender" id="female" value="female"  style="cursor:pointer" required {{ Auth::user()->gender=='female'?'checked':'' }}>
                                                    <label class="form-check-label text-secondary" for="female" style="cursor:pointer">
                                                      Female
                                                    </label>
                                            </div>

                                            <div class="form-check mr-3" style="display:inline;cursor:pointer">
                                                    <input class="form-check-input" type="radio" name="gender" id="other" value="other" style="cursor:pointer" required {{ Auth::user()->gender=='other'?'checked':'' }}>
                                                    <label class="form-check-label text-secondary" for="other" style="cursor:pointer">
                                                      Other
                                                    </label>
                                            </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                        <label class="col-sm-3 col-form-label text-secondary" for="birthdate">
                                            <span class="mr-2"><i class="fas fa-birthday-cake"></i></span>
                                            Birthdate</label>
                                        <div class="col-sm-5">
                                            <input type="text" id="birthdate" name="birthdate"  class="form-control text-secondary" value="{{ Auth::user()->birthdate!=null?(Auth::user()->birthdate):null }}"/>
                                        </div>
                                </div>



                                <!--<div class="form-group row">
                                    <div class="offset-sm-3 col-sm-9">
                                        <div class="form-check">
                                            @if(Auth::user()->is_subscribed==1)
                                            <input type="checkbox" id="is_subscribed" name="is_subscribed" class="form-check-input" checked/>
                                            @else
                                            <input type="checkbox" id="is_subscribed" name="is_subscribed" class="form-check-input" />
                                            @endif
                                        <label for="is_subscribed" class="form-check-label text-secondary">Subscribe to News letter Email</label>
                                        </div>
                                    </div>
                                </div>
                                -->
                                <hr class="lead my-4"/>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="website_url">
                                            <span class="mr-2"><i class="fas fa-globe" size="1x"></i></span>Your Website</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="website_url" name="website_url" value="{{Auth::user()->website_url!==null?Auth::user()->website_url:''}}" placeholder="Add Your Website Link"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="facebook_url">
                                        <span class="mr-2"><i class="fab fa-facebook-square" size="1x"></i></span>
                                        Facebook Page</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="facebook_url" name="facebook_url" value="{{Auth::user()->facebook_url!==null?Auth::user()->facebook_url:''}}" placeholder="Add Your Facebook Page Link"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label text-secondary" for="twitter_url">
                                            <span class="mr-2"><i class="fab fa-twitter-square" size="1x"></i></span>
                                            Twitter Page</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="twitter_url" name="twitter_url" value="{{Auth::user()->twitter_url!==null?Auth::user()->twitter_url:''}}" placeholder="Add Your Twitter Page Link"/>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="control-label col-sm-3 text-secondary" for="linkedin_url">
                                            <span class="mr-2"><i class="fab fa-linkedin" size="1x"></i></span>
                                            Linkedin Page</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="linkedin_url" name="linkedin_url" value="{{Auth::user()->linkedin_url!==null?Auth::user()->linkedin_url:''}}" placeholder="Add Your Linkedin Page Link"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-sm-3 text-secondary" for="instagram_url">
                                            <span class="mr-2"><i class="fab fa-instagram" size="1x"></i></span>
                                            Instagram Page</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="instagram_url" name="instagram_url" value="{{Auth::user()->instagram_url!==null?Auth::user()->instagram_url:''}}" placeholder="Add Your Instagram Page Link"/>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="control-label col-sm-3 text-secondary" for="youtube_channel_url">
                                            <span class="mr-2"><i class="fab fa-youtube" size="1x"></i></span>Youtube Channel</label>
                                    <div class="col-sm-9">
                                    <input type="text" class="form-control text-secondary" id="youtube_channel_url" name="youtube_channel_url" value="{{Auth::user()->youtube_channel_url!==null?Auth::user()->youtube_channel_url:''}}" placeholder="Add Your Youtube Channel Link"/>
                                    </div>
                                </div>

                                <div class="form-group row mt-5">
                                    <div class="offset-sm-3 col-sm-6">
                                        <button type="submit" id="submitform" class="btn btn-success btn-block waves-effect waves-float">Update Profile</button>
                                    </div>
                                </div>
                            </form>

                            @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            @endif

                        </div>
                    </div>
                    </div>
                </div>
@endsection
