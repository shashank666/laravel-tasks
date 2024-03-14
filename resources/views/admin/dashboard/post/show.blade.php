@extends('admin.layouts.app')
@section('title','Post #'.$post->id)
@push('styles')
<link href="/public_admin/assets/libs/noty/noty.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush
@push('scripts')
<script src="/public_admin/assets/libs/noty/noty.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/sweetalert2/sweetalert2.min.js"></script>

<script>

      $(document).on('change','#monetise',function(){
        var post_id=$('#post_id').val();
        var user_id=$('#user_id').val();
        if($(this).is(':checked')){
            var monetise=1;
        }else{
            var monetise=0;
        }
        $.ajax({
            url:"{{route('admin.monetisation')}}",
            type:"POST",
            data:{'post_id':post_id,'user_id':user_id,'monetise':monetise},
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(response){
                if(response.status=='1'){
                    $('.doller').css("color","green");
                }else if(response.status=='0'){
                    $('.doller').css("color","red");
                }
                new Noty({
                    theme:'sunset',
                    type:response.status,
                    text: response.message,
                    timeout:3500,
                }).show();
            },error:function(err){
                new Noty({
                    theme:'sunset',
                    type:'error',
                    text: 'FAILED TO UPDATE SETTING',
                    timeout:3500,
                }).show();
            }
        });
    });


    $(document).on('click','#submit_review',function(){
            var post_id=$('#post_id').val();
            var user_id=$('#user_id').val();
            var backlink=$('#backlink').val();
            var promo_review=$('#promo_review').val();
            $.ajax({
                url:"{{route('admin.check_for_eligibility')}}",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type:'POST',
                data:{post_id:post_id,user_id:user_id,backlink:backlink,promo_review:promo_review},
                dataType:'json',
                success:function(response){
                  if(response.promo_review=='0' || response.backlink=='0'){
                    $('.promo').remove();
                    $('.back_link').remove();
                    $('.submit_review').remove();
                    $('.beforetest').remove();
                    $('.failed_check').show();
                    $('.testfailed').show();
                    }
                  else{
                    $('.promo').remove();
                    $('.back_link').remove();
                    $('.submit_review').remove();
                    $('.beforetest').remove();
                    $('.aftertest').show();
                    $('.plegiarism_check').show();
                  }
                },error:function(response){
                    
                }
            });
        });

        $(document).on('click',"#confirm_delete",function(){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $('#post_delete_form').submit();
                    }
                })
        });
    $(document).ready(function(){

         $('.plegiarism_check').hide();
         $('.aftertest').hide();
         $('.failed_check').hide();
         $('.testfailed').hide();
        $.fn.checkValidation = function(){
            
            
         }

        $("#promo").on('change', function() {
               $('#promo_review').val(this.value); 
                  $.fn.checkValidation(); 
                });
        $("#back_link").on('change', function() {
               $('#backlink').val(this.value); 
                  $.fn.checkValidation(); 
                });
    });
</script>

@endpush
@section('content') 

<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <h1 class="header-title">
                   {{ '#'.$post->id}}
                    @if($monetisation!=null)
                        @if($monetisation->is_monetised!=1)
                   <span class="float-right doller" title="Not Monetised" style="color: red"><i class="fas fa-dollar-sign"></i></span>
                        @else
                    <span class="float-right doller" title="Monetised" style="color: green"><i class="fas fa-dollar-sign"></i></span>
                        @endif
                    @endif
                    <span class="float-right mr-2 badge {{ $post->is_active==1?'badge-success':'badge-danger' }} badge-pill">{{ $post->is_active==1?'Visible':'Hidden' }}</span>
                    <span class="float-right mr-2 badge {{ $post->status==1?'badge-success':($post->status==2?'badge-warning':'badge-primary') }} badge-pill">{{ $post->status==1?'Published':($post->status==2?'Previewed':'Draft') }}</span>
                    </h1>

                </div>
               <div class="col-auto">

                    <a href="{{ route('admin.edit_post',['id'=>$post->id]) }}" class="btn btn-primary"><i class="fas fa-pencil-alt mr-2"></i>Edit Post</a>
                    <form style="display:none" id="post_delete_form" method="POST" action="{{ route('admin.delete_post') }}">
                            <input type="hidden" name="post_id" value="{{ $post->id }}"/>
                            {{csrf_field()}}
                    </form>
                    <form style="display:none" id="post_visibility_form" method="POST" action="{{ route('admin.post_visibility') }}">
                            {{ csrf_field() }}
                             <input type="hidden" name="post_id" value="{{ $post->id }}"/>
                             <input type="hidden" name="is_active" value="{{ $post->is_active }}"/>
                     </form>
                     @if($post->is_active==1)
                     <button class="btn btn-warning" onclick="document.getElementById('post_visibility_form').submit();"><i class="fas fa-eye-slash mr-2"></i>Disable (Hide) Post</button>
                     @else
                     <button class="btn btn-success" onclick="document.getElementById('post_visibility_form').submit();"><i class="fas fa-eye mr-2"></i>Activate Post</button>
                     @endif
                     
                    <button  class="btn btn-danger" id="confirm_delete"><i class="fas fa-trash-alt mr-2"></i>Permenent Delete Post</button>
               </div>
          </div>
            
           @include('admin.dashboard.post.components.post_menu',['section'=>'blogpost'])     
           
        </div>
        </div>
</div>

<div class="container">
    <!--<div class="row">
        <div class="col-md-5 col-12">
                <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Offer Eligibility Criteria</h2>
                        </div>
                        <table class="table table-wrap">
                            <thead>
                                <tr>
                                    <th>400 Words</th>
                                    <th>50 Likes</th>
                                    <th>&lt; 20% Plagiarism</th>
                                    <th>Register Writer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ str_word_count($post->plainbody) }}</td>
                                    <td>{{ $post->likesCount }}</td>
                                    <td>{{ $post->plagiarism_percentage.' %' }}</td>
                                    <td>{{ $post->user['registered_as_writer']?'Yes':'No' }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        @if(str_word_count($post->plainbody)>=400)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                        @else
                                            <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->likesCount>=50)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->plagiarism_checked==1)
                                                @if($post->plagiarism_percentage<=20)
                                                    <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                                    @else
                                                    <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                                    @endif
                                            @else
                                            <i class="fas fa-exclamation col-orange" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->user['registered_as_writer']==1)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
        </div>
        <div class="col-md-3 col-12">
                <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Plagiarism Test
                                <span class="ml-2 p-2 badge {{  $post->plagiarism_checked==0?'badge-warning':'badge-success' }}">{{  $post->plagiarism_checked==0?'Pending':'Done' }}</span>
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($post->plagiarism_checked==0)
                                <b class="text-blue">Plagiarism is Not Checked For this Post </b>
                            @else
                                @if($post->is_plagiarized==0)
                                <b class="text-success">Plagiarism Result :  This Post is 0 % Plagiarised , 100 % Unique.</b>
                                @else
                                <b class="text-danger">Plagiarism Result :  This Post is {{ $post->plagiarism_percentage }} % Plagiarised , {{ 100 - $post->plagiarism_percentage }} % Unique.</b>
                                @endif
                            @endif
                        </div>
                </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#offer" style="cursor: pointer;">
                            <h4 class="card-header-title">Offer Eligibility Criteria</h2>
                        </div>
                        <table class="table table-wrap collapse" id="offer">
                            <thead>
                                <tr>
                                    <th>400 Words</th>
                                    <th>50 Likes</th>
                                    <th>&lt; 20% Plagiarism</th>
                                    <th>Register Writer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ str_word_count($post->plainbody) }}</td>
                                    <td>{{ $post->likesCount }}</td>
                                    <td>{{ $post->plagiarism_percentage.' %' }}</td>
                                    <td>{{ $post->user['registered_as_writer']?'Yes':'No' }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        @if(str_word_count($post->plainbody)>=400)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                        @else
                                            <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->likesCount>=50)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->plagiarism_checked==1)
                                                @if($post->plagiarism_percentage<=20)
                                                    <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                                    @else
                                                    <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                                    @endif
                                            @else
                                            <i class="fas fa-exclamation col-orange" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->user['registered_as_writer']==1)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#plagiarism" style="cursor: pointer;">
                            <h4 class="card-header-title">Plagiarism Test
                                <span class="ml-2 p-2 badge {{  $post->plagiarism_checked==0?'badge-warning':'badge-success' }}">{{  $post->plagiarism_checked==0?'Pending':'Done' }}</span>
                            </h4>
                        </div>
                        <div class="card-body collapse" id="plagiarism">
                            @if($post->plagiarism_checked==0)
                                <b class="text-blue">Plagiarism is Not Checked For this Post </b>
                            @else
                                @if($post->is_plagiarized==0)
                                <b class="text-success">Plagiarism Result :  This Post is 0 % Plagiarised , 100 % Unique.</b>
                                @else
                                <b class="text-danger">Plagiarism Result :  This Post is {{ $post->plagiarism_percentage }} % Plagiarised , {{ 100 - $post->plagiarism_percentage }} % Unique.</b>
                                @endif
                            @endif
                        </div>
                </div>
                <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#fake_like" style="cursor: pointer;">
                            <h4 class="card-header-title">Fake Likes
                                <span class="mr-2 badge badge-danger">
                                    {{ $fakeLikes }}
                                </span>
                            </h4>
                        </div>
                        <div class="card-body collapse" id="fake_like">
                                <button class="btn btn-sm btn-block btn-primary" data-toggle="modal" data-target="#addFakeLikesModal">Add Fake Like</button>
                                <br/> <button class="mt-2 btn btn-sm btn-block   btn-danger" data-toggle="modal" data-target="#removeFakeLikesModal">Remove Fake Likes</button>
                                <br/><button class="mt-2 btn btn-sm btn-block btn-danger" onclick="document.getElementById('delete_all_fake_likes').submit();">Remove All Fake Likes</button>

                                <form id="delete_all_fake_likes" method="POST" action="{{ route('admin.remove_all_fake_likes') }}" style="display:none">
                                    <input type="hidden" name="post_id" value="{{ $post->id }}"/>
                                    {{csrf_field()}}
                                </form>
                        </div>
                </div>
                <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#category_thread" style="cursor: pointer;">
                            <h4 class="card-header-title">Categories & Threads</h4>
                        </div>
                        <div class="card-body collapse" id = "category_thread">
                                <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Categories</td>
                                                <td>@foreach($post->categories as $index=>$category)
                                                    <a href="{{route('admin.posts',['categories'=>$category->id])}}" class="badge badge-primary p-2 mr-2">{{'#'.$category->id.'-'.$category->name}}</a>
                                                @endforeach </td>
                                            </tr>
                                            <tr>
                                                <td>Threads</td>
                                                <td>
                                                        @if(count($threads)>0)
                                                        @foreach($threads as $thread)
                                                        <a href="#" title="{{'#'.$thread->thread_name}}" class="badge badge-success p-2 mr-2">{{'#'.$thread->thread_name}}</a>
                                                        @endforeach
                                                        @else
                                                        No Threads Found For this Post
                                                        @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                </table>
                                
                        </div>
            </div>
        </div>
    </div>-->
    <div class="row">
        <div class="col-md-8 col-12">
<!--
            <div class="card">
                        <div class="card-header">
                            <h4 class="card-header-title">Categories & Threads</h4>
                        </div>
                        <div class="card-body">
                                <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Categories</td>
                                                <td>@foreach($post->categories as $index=>$category)
                                                    <a href="{{route('admin.posts',['categories'=>$category->id])}}" class="badge badge-primary p-2 mr-2">{{'#'.$category->id.'-'.$category->name}}</a>
                                                @endforeach </td>
                                            </tr>
                                            <tr>
                                                <td>Threads</td>
                                                <td>
                                                        @if(count($threads)>0)
                                                        @foreach($threads as $thread)
                                                        <a href="#" title="{{'#'.$thread->thread_name}}" class="badge badge-success p-2 mr-2">{{'#'.$thread->thread_name}}</a>
                                                        @endforeach
                                                        @else
                                                        No Threads Found For this Post
                                                        @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                </table>
                                
                        </div>
            </div>
-->
            <div class="card">
                    <div class="card-body">

                            <div class="mb-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                @if($post->user!=null)
                                <a href="{{ route('admin.user_details',['id'=>$post->user['id']]) }}" class="avatar">
                                    <img src="{{ $post->user['image'] }}" alt="..." class="avatar-img rounded-circle">
                                </a>
                                @else
                                USER NOT FOUND
                                @endif
                                </div>
                                <div class="col ml-n2">
                                <h4 class="card-title mb-1">
                                     @if($post->user!=null)
                                    <a href="{{ route('admin.user_details',['id'=>$post->user['id']]) }}">{{  ucfirst($post->user['name']) }}</a>
                                    @endif
                                </h4>
                                <p class="card-text small text-muted">
                                    Post Publish DateTime : {{ \Carbon\Carbon::parse($post->created_at)->format('l , jS F Y , h:i:s A')   }} <br/>
                                    Post Last Updated : {{ \Carbon\Carbon::parse($post->updated_at)->format('l , jS F Y , h:i:s A')   }}
                                </p>
                                </div>
                            
                            </div>
                            </div>

                            <h2 class="mt-4">{{ $post->title }}</h2>
                            Word Count : {{ str_word_count($post->plainbody) }}
                            <br/> 
                            Post Link : <a target="_blank" href="/opinion/{{ $post->slug }}">{{ url('/opinion/'.$post->slug) }}</a>

                            <p class="text-center my-4">
                                    <img src="{{ $post->coverimage }}" alt="..." class="img-fluid rounded">
                            </p><br/>
                            <div class="text-justify">{!!$post->body!!}</div>

                    </div>
            </div>
        </div>

        <div class="col-md-4 col-12">
            <!--<div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">Post Visibility & Status</h4>
                    </div>
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Visibility<span class="float-right p-2 badge {{ $post->is_active==1?'badge-success':'badge-danger' }} badge-pill">{{ $post->is_active==1?'Visible':'Hidden' }}</span></li>
                                    <li class="list-group-item">Status<span class="float-right  p-2 badge {{ $post->status==1?'badge-success':($post->status==2?'badge-warning':'badge-primary') }} badge-pill">{{ $post->status==1?'Published':($post->status==2?'Previewed':'Draft') }}</span></li>
                            </ul>
                    </div>
            </div>-->
        
            <div class="card">
            @if($post->user['mobile_verified']!=1 || $post->user['email_verified']!=1)
                <div class="card-header" style="cursor: pointer;">
                            <h4 class="card-header-title">Elgibility status</h4>
                    </div>
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                       {{$post->user['mobile_verified']==0?"Mobile ":""}} 
                                       @if($post->user['mobile_verified']==0 && $post->user['email_verified']==0)
                                       and
                                       @endif
                                       {{$post->user['email_verified']==0?"Email":""}} not verified
                                    </li>
                            </ul>
                    </div>
            @else
                @if($article_status!=null)
                    @if($article_status->plagiarism_tested=='1')
                        @if($article_status->plagiarised=='1')
                        <div class="card-body">
                                <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <span title="RSM Guideline Violation" style="color: red"><i class="fas fa-dollar-sign"></i> Cannot Monetised</span>
                                        </li>
                                        <li class="list-group-item">
                                            Plagiarism<span class="float-right" title="Plagiarism">{{$article_status->plagiarism_percent}} %</span></li>
                                        </li>
                                </ul>
                        </div>
                        
                        @elseif($article_status->plagiarised=='0')
                            @if($monetisation!=null)
                            <form method="POST" action="{{route('admin.monetisation')}}">
                                    {{csrf_field()}}
                                    <input type="hidden" name="post_id" id="post_id" value="{{$post->id}}"/>
                                    <input type="hidden" name="user_id" id="user_id" value="{{$post->user['id']}}"/>
                                    <div class="custom-control custom-switch" style="text-align: center;margin: 25px;">
                                        @if($monetisation->is_monetised!=1)
                                        <input type="checkbox" class="custom-control-input" id="monetise" name="monetise" />
                                        @else
                                        <input type="checkbox" class="custom-control-input" id="monetise" name="monetise" checked/>
                                        @endif
                                        <label  class="custom-control-label" for="monetise">Monetise</label>

                                    </div>

                            </form>
                            @else
                            <form method="POST" action="{{route('admin.monetisation')}}">
                                    {{csrf_field()}}
                                    <input type="hidden" name="post_id" id="post_id" value="{{$post->id}}"/>
                                    <input type="hidden" name="user_id" id="user_id" value="{{$post->user['id']}}"/>
                                    <div class="custom-control custom-switch" style="text-align: center;margin: 25px;">
                                        
                                        <input type="checkbox" class="custom-control-input" id="monetise" name="monetise" />
                                        
                                        <label  class="custom-control-label" for="monetise">Monetise</label>
                                    </div>

                            </form>
                            @endif
                        @else
                        <div class="card-header" style="cursor: pointer;">
                                <h4 class="card-header-title">Plegiarism Test</h4>
                        </div>
                        <div class="card-body">
                                <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            
                                               
                                                Tested Already
                                                <button onclick="window.location.href ='../view_plagiarism/{{ $post->id }}'" class="btn btn-sm btn-primary btn-block"  name="" id="" type="button">Check Status</button>
                                            
                                        </li>
                                </ul>
                        </div>
                        @endif
                    @elseif($article_status->promo_review!=0 && $article_status->backlink!=0 )
                    <div class="card-header" style="cursor: pointer;">
                            <h4 class="card-header-title">Plegiarism Test</h4>
                    </div>
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        
                                           
                                            
                                            <button onclick="window.location.href ='../plagiarism_check/{{ $post->id }}'" class="btn btn-sm btn-primary btn-block"  name="" id="" type="button">Check Plagiarism</button>
                                        
                                    </li>
                            </ul>
                    </div>
                    @else
                    <div class="card-header" style="cursor: pointer;">
                            <h4 class="card-header-title">Elgibility status</h4>
                    </div>
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        NOT ELIGIBLE
                                    </li>
                            </ul>
                    </div>
                    @endif
                @else
                <div class="card-header" style="cursor: pointer;">
                            <h4 class="card-header-title beforetest">Is this Article have?</h4>
                            <h4 class="card-header-title aftertest">Plegiarism Test</h4>
                            <h4 class="card-header-title testfailed">Elgibility status</h4>
                        </div>
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item promo">Promo/Review?<span class="float-right">
                                        <select id="promo">
                                          <option value="">Select</option>
                                          @if($article_status!=null)
                                              @if($article_status->promo_review==0)
                                              <option value="0" selected>YES</option>
                                              <option value="1">NO</option>
                                              @else
                                              <option value="0">YES</option>
                                              <option value="1" selected>NO</option>
                                              @endif
                                          @else
                                          <option value="0">YES</option>
                                          <option value="1">NO</option>
                                          @endif
                                        </select>
                                      </span></li>
                                    <li class="list-group-item back_link">Backlink?<span class="float-right">
                                        <select id="back_link">
                                          <option value="">Select</option>
                                          @if($article_status!=null)
                                              @if($article_status->backlink==0)
                                              <option value="0" selected>YES</option>
                                              <option value="1">NO</option>
                                              @else
                                              <option value="0">YES</option>
                                              <option value="1" selected>NO</option>
                                              @endif
                                          @else
                                          <option value="0">YES</option>
                                          <option value="1">NO</option>
                                          @endif
                                        </select>
                                      </span></li>
                                    <li class="list-group-item submit_review">
                                        @if($article_status!=null)
                                            <input type="hidden" name="promo_review" id="promo_review" value="{{$article_status->promo_review}}"/>
                                            <input type="hidden" name="backlink" id="backlink" value="{{$article_status->backlink}}"/>
                                        @else
                                            <input type="hidden" name="promo_review" id="promo_review" value=""/>
                                            <input type="hidden" name="backlink" id="backlink" value=""/>
                                        @endif
                                            <input type="hidden" name="post_id" id="post_id" value="{{$post->id}}"/>
                                            <input type="hidden" name="user_id" id="user_id" value="{{$post->user['id']}}"/>
                                            <button class="btn btn-sm btn-primary btn-block"  name="submit_review" id="submit_review" type="submit">Submit</button>
                                        
                                    </li>

                                    <li class="list-group-item failed_check">
                                        
                                           
                                            <span>NOT ELIGIBLE</span>
                                        
                                    </li>

                                    <li class="list-group-item plegiarism_check">
                                        
                                           
                                            <button onclick="window.location.href ='../plagiarism_check/{{ $post->id }}'" class="btn btn-sm btn-primary btn-block"  name="" id="" type="button">Check Plagiarism</button>
                                        
                                    </li>
                            </ul>
                    </div>
                    
                    @endif
                @endif
            </div>
            <div class="card">
                    <div class="card-body">
                            <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="mr-3 far fa-thumbs-up"></i>Likes<span class="float-right badge badge-success badge-pill">{{ $post->likesCount }}</span></li>
                                    <li class="list-group-item"><i class="mr-3 fas fa-eye"></i>Views<span class="float-right badge badge-primary badge-pill">{{ $post->viewsCount }}</span></li>
                                    <li class="list-group-item"><i class="mr-3 far fa-comments"></i>Comments<span class="float-right badge badge-warning badge-pill">{{ $post->commentsCount }}</span></li>
                                    <li class="list-group-item"><i class="mr-3 far fa-bookmark"></i>Bookmarks<span class="float-right badge badge-info badge-pill">{{ $post->bookmarksCount }}</span></li>
                            </ul>
                    </div>
            </div>
            <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#category_thread" style="cursor: pointer;">
                            <h4 class="card-header-title">Categories, Threads & Keywords</h4>
                        </div>
                        <div class="card-body collapse" id = "category_thread">
                                <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Categories</td>
                                                <td>@foreach($post->categories as $index=>$category)
                                                    <a href="{{route('admin.posts',['categories'=>$category->id])}}" class="badge badge-primary p-2 mr-2">{{'#'.$category->id.'-'.$category->name}}</a>
                                                @endforeach </td>
                                            </tr>
                                            <tr>
                                                <td>Threads</td>
                                                <td>
                                                        @if(count($threads)>0)
                                                        @foreach($threads as $thread)
                                                        <a href="#" title="{{'#'.$thread->thread_name}}" class="badge badge-success p-2 mr-2">{{'#'.$thread->thread_name}}</a>
                                                        @endforeach
                                                        @else
                                                        No Threads Found For this Post
                                                        @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Keywords</td>
                                                <td>
                                                        @if(count($post->keywords)>0)
                                                        @foreach($post->keywords as $index=>$keyword)
                                                            {{'-'.$keyword->name}}</br>
                                                        @endforeach 
                                                        @else
                                                        No Keywords Found For this Post
                                                        @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                </table>
                                
                        </div>
            </div>
            <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#plagiarism" style="cursor: pointer;">
                            <h4 class="card-header-title">Plagiarism Test
                                <span class="ml-2 p-2 badge {{  $post->plagiarism_checked==0?'badge-warning':'badge-success' }}">{{  $post->plagiarism_checked==0?'Pending':'Done' }}</span>
                            </h4>
                        </div>
                        <div class="card-body collapse" id="plagiarism">
                            @if($post->plagiarism_checked==0)
                                <b class="text-blue">Plagiarism is Not Checked For this Post </b>
                            @else
                                @if($post->is_plagiarized==0)
                                <b class="text-success">Plagiarism Result :  This Post is 0 % Plagiarised , 100 % Unique.</b>
                                @else
                                <b class="text-danger">Plagiarism Result :  This Post is {{ $post->plagiarism_percentage }} % Plagiarised , {{ 100 - $post->plagiarism_percentage }} % Unique.</b>
                                @endif
                            @endif
                        </div>
                </div>
            <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#offer" style="cursor: pointer;">
                            <h4 class="card-header-title">Offer Eligibility Criteria</h2>
                        </div>
                        <table class="table table-wrap collapse" id="offer">
                            <thead>
                                <tr>
                                    <th>400 Words</th>
                                    <th>50 Likes</th>
                                    <th>&lt; 20% Plagiarism</th>
                                    <th>Register Writer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ str_word_count($post->plainbody) }}</td>
                                    <td>{{ $post->likesCount }}</td>
                                    <td>{{ $post->plagiarism_percentage.' %' }}</td>
                                    <td>{{ $post->user['registered_as_writer']?'Yes':'No' }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        @if(str_word_count($post->plainbody)>=400)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                        @else
                                            <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->likesCount>=50)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->plagiarism_checked==1)
                                                @if($post->plagiarism_percentage<=20)
                                                    <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                                    @else
                                                    <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                                    @endif
                                            @else
                                            <i class="fas fa-exclamation col-orange" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                    <td>
                                            @if($post->user['registered_as_writer']==1)
                                            <i class="fas fa-check text-success" style="font-size:20px;"></i>
                                            @else
                                                <i class="fas fa-times text-danger" style="font-size:20px;"></i>
                                            @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            
                <div class="card">
                        <div class="card-header" data-toggle="collapse" data-target="#fake_like" style="cursor: pointer;">
                            <h4 class="card-header-title">Fake Likes
                                <span class="mr-2 badge badge-danger">
                                    {{ $fakeLikes }}
                                </span>
                            </h4>
                        </div>
                        <div class="card-body collapse" id="fake_like">
                                <button class="btn btn-sm btn-block btn-primary" data-toggle="modal" data-target="#addFakeLikesModal">Add Fake Like</button>
                                <br/> <button class="mt-2 btn btn-sm btn-block   btn-danger" data-toggle="modal" data-target="#removeFakeLikesModal">Remove Fake Likes</button>
                                <br/><button class="mt-2 btn btn-sm btn-block btn-danger" onclick="document.getElementById('delete_all_fake_likes').submit();">Remove All Fake Likes</button>

                                <form id="delete_all_fake_likes" method="POST" action="{{ route('admin.remove_all_fake_likes') }}" style="display:none">
                                    <input type="hidden" name="post_id" value="{{ $post->id }}"/>
                                    {{csrf_field()}}
                                </form>
                        </div>
                </div>
                
            
        </div>
    </div>
</div>

    <div class="modal fade" id="addFakeLikesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
            <form method="POST" action="{{ route('admin.add_fake_likes') }}">
                {{csrf_field()}}
                <div class="modal-header">
                    <h4 class="modal-title" id="smallModalLabel">Add Some Fake Like</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="post_id" value="{{ $post->id }}" />
                    <div class="form-group">
                        <label>Enter Likes To Add</label>
                            <input type="number" class="form-control" name="add_fake" min="0" required/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Fake Likes</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeFakeLikesModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <form method="POST" action="{{ route('admin.remove_fake_likes') }}">
                {{csrf_field()}}
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="smallModalLabel">Remove Some Fake Like</h4>
                    </div>
                    <div class="modal-body">
                            <input type="hidden" name="post_id" value="{{ $post->id }}" />
                            <div class="form-group">
                                <label>Enter Likes To Remove</label>
                                <input type="number" class="form-control" name="remove_fake" min="0" max="{{ $fakeLikes  }}" required/>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Remove Fake Likes</button>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </form>
                </div>
            </div>
    </div>


@endsection
