@extends('admin.layouts.app')
@section('title','Edit Post #'.$post->id)

@push('meta')
<meta name="posttags" content=<?php echo json_encode($post->threads);?>></meta>
<meta name="postkeywords" content=<?php echo json_encode(implode(",",$post->keywords->pluck('name')->toArray()));?>></meta>
@endpush

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/tagmanager/tagmanager.min.css"/>
@endpush

@push('scripts')
<script type="text/javascript" src="/public_admin/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script>
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
</script>
<script src="/public_admin/assets/libs/jquery-validate/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/public_admin/assets/libs/tagmanager/tagmanager.min.js"></script>
<script type="text/javascript" src="/public_admin/assets/libs/bootstrap-3-typeahead/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript" src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>

<script>
$(document).ready(function() {

    var removeButtons = 'Print,Save,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Anchor,Font,Styles,About,ShowBlocks,BGColor,TextColor,Maximize,Language,BidiRtl,BidiLtr,SpecialChar,Table';
    var toolbarGroups = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection','editing' ] },
        { name: 'basicstyles', groups: [ 'basicstyles'] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
        { name: 'insert', groups: [ 'insert' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'others', groups: [ 'others' ] },
    ];

    CKEDITOR.replace('article_ckeditor',{
        toolbarGroups,
        removeButtons,
        extraPlugins: 'autogrow',
        removePlugins: 'resize'
    });


            var threadsArr=$('meta[name=posttags]').attr("content").split(',');
            var tagApi = $("#tags").tagsManager({
                prefilled: threadsArr,
                delimiters: [9,13,32,44],
                maxTags	:5,
                validator:function(){
                    if($("#tags").val().length>=3 && $("#tags").val().charAt(0)!=='#'){
                        return true;
                    }else{return false;}
                }
            });

            var keywordsArr=$('meta[name=postkeywords]').attr("content").split(',');
            var keywordApi=$('#keywords').tagsManager({
                prefilled: keywordsArr,
                delimiters: [9,13,44],
                maxTags	:10,
                validator:function(){
                    if($("#keywords").val().length>=3){
                        return true;
                    }else{return false;}
                }
            });

        $(".typeahead").typeahead({
            source: function (query, process) {
                return $.get('/search/threads', { q: query }, function (data) {
                return process(data.threads);
                });
            },
            afterSelect :function (item){
                tagApi.tagsManager("pushTag", item.name);
            }
        });

        $("#is_plagiarized").change(function(){
            if($(this).is(':checked')){
                $(this).val(1);
            }else{
                $(this).val(0);
                $('#plagiarism_percentage').val(0);
            }
        });

        $("#status").change(function(){
            if($(this).is(':checked')){
                $(this).val(1);
            }else{
                $(this).val(0);
            }
        });

        $('#tabs_add_image li a').click(function (e) {

        });

        $('#tabs_add_image').on('shown.bs.tab', function (e) {
            e.preventDefault();
            if(e.target.href.split('#')[1]=='tab_upload'){
                $('#imagelink').val('');
            }else{
                $('#imagefile').val('');
                $('#btnUploadImage').css('display', 'none');
            }
        });

    $('#update_post_form').validate({
        rules: {

            'category': {
                required: true
            },
            'title':{
                required:true
            },
            'slug':{
                required:true
            }
        },
        messages: {

            category:{
                required:"Category is required !",
            },
            title: {
              required:"Title is required !",
            },
            slug:{
                required:"Slug - URL required !",
            }
        },
        submitHandler: function(form) {
            $('#plainbody').val(CKEDITOR.instances.article_ckeditor.document.getBody().getText());
            form.submit();
        },
        highlight: function (input) {
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.form-group').append(error);
        }
    });


    $('#plagiarism_checked').change(function(){
        if($(this).is(':checked')){
            $(this).val(1);
            $('#plagiarism_checked_label').text('Plagirism Checked');
            $('.plagirism-test').css('display','block');
        }else{
            $(this).val(0);
            $('#plagiarism_checked_label').text('Plagirism Not Checked');
            $('.plagirism-test').css('display','none');
        }
    });

});
</script>

<script type="text/javascript" src="/js/custom/write.js"></script>
<script type="text/javascript" src="/js/custom/main.js"></script>
<script type="text/javascript" src="/js/custom/add_social_posts.js"></script>
<script src="https://www.instagram.com/static/bundles/base/EmbedSDK.js/70de6f18b9b4.js"></script>
@endpush

@section('content')

<div class="header">
        <div class="container">
          <div class="header-body">
            <div class="row align-items-end">
                <div class="col">
                    <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                    <li  class="breadcrumb-item"><a href="{{route('admin.posts')}}">Posts</a></li>
                                    <li  class="breadcrumb-item"><a href="{{ route('admin.blog_post',['id'=>$post->id]) }}">{{ 'Post #'.$post->id }}</a></li>
                                    <li  class="breadcrumb-item active">Edit</li>
                            </ol>
                    </nav>
                </div>
               <div class="col-auto">
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

        </div>
        </div>
</div>


        <div class="container">

            @if(session()->has('status'))
                @if(session('status')=='draft')
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                            {{session('statusText')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                @endif

                @if(session('status')=='published')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{session('statusText')}}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                @endif
            @endif



            <form method="POST" id="update_post_form" action="{{ route('admin.save_post') }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Post Plagiarism Test</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                           <label>Status</label>
                                            <br/>
                                            <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="plagiarism_checked" id="plagiarism_checked" class="custom-control-input" value="{{ $post->plagiarism_checked==1?'1':'0' }}" {{ $post->plagiarism_checked=='1'?'checked':'' }}>
                                                    <label for="plagiarism_checked" class="custom-control-label" id="plagiarism_checked_label">{{ $post->plagiarism_checked==1?'Plagirism Checked':'Plagirism Not Checked' }}</label>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-4 plagirism-test" style="display:{{ $post->plagiarism_checked==1?'block':'none' }}">
                                    <div class="form-group">
                                            <label>Post Plagiarized</label>
                                            <div class="custom-control custom-switch">
                                                    <input class="custom-control-input" type="checkbox" name="is_plagiarized" id="is_plagiarized" value="{{ $post->is_plagiarized==1?1:0 }}" {{ $post->is_plagiarized=='1'?'checked':''  }}>
                                                    <label class="custom-control-label" for="is_plagiarized">Plagiarized</label>
                                            </div>
                                    </div>
                                </div>
                                <div class="col-md-4 plagirism-test" style="display:{{ $post->plagiarism_checked==1?'block':'none' }}">
                                    <div class="form-group">
                                        <label>Plagiarism Percentage %</label>
                                        <input type="number" class="form-control" name="plagiarism_percentage" id="plagiarism_percentage" min="0" max="100" value="{{ $post->plagiarism_percentage }}"/>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class="card">
                                <div class="card-header">
                                    <h4 class="card-header-title">Categories and Threads</h4>
                                </div>
                                <div class="card-body">
                                        <div class="form-group">
                                            <label>Categories</label>
                                            <select class="form-control show-tick" id="category" name="categories[]" data-live-search="true"  dropupAuto="false" data-toggle="select" data-size="20" multiple required>
                                                    @foreach($categories as $category)
                                                        @if(in_array($category->id,$post->categoryids))
                                                            <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                                        @else
                                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                                        @endif
                                                    @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                                <label  for="tags">Add Tags</label>
                                                <input type="text" name="tags" id="tags" placeholder="Tags" class="typeahead tm-input form-control"/>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label" for="keywords">Add Keywords</label>
                                            <input type="text" name="keywords" id="keywords" placeholder="Keywords" class="tm-input form-control tm-input-light"/>
                                        </div>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                            <div class="card">
                                    <div class="card-header">
                                            <h4 class="card-header-title">Post Status</h4>
                                    </div>
                                    <div class="card-body">
                                            <div class="form-group">
                                                    <label>Post Status</label>
                                                    <div class="custom-control custom-switch">
                                                            <input  type="checkbox" class="custom-control-input"  name="status" id="status" value="{{ $post->status==1?1:0}}" {{ $post->status=='1'?'checked':''}}/>
                                                            <label class="custom-control-label" for="status">Published</label>
                                                    </div>
                                            </div>
                                    </div>
                            </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                    <div class="card">
                            <div class="card-header">
                                <h4 class="card-header-title">Post Cover Image
                                    <span class="float-right"><button class="btn btn-sm btn-primary"  type="button" onclick="openAddImageModal();">Change Image ?</button></span>
                                </h4>
                            </div>
                            <div class="card-body">
                                    <div class="form-group">
                                            <label>Image URL</label>
                                            <input type="text" id="coverimage" class="form-control" name="coverimage" value="{{ $post->coverimage==NULL?'https://i.imgur.com/mXD4R7L.png':$post->coverimage}}" readonly/>
                                    </div>

                                    <div id="coverPreviewDiv" class="my-4">
                                            <img src="{{  $post->coverimage==NULL?'https://i.imgur.com/mXD4R7L.png':$post->coverimage}}" id="coverPreview" height="auto" width="500" class="img-fluid rounded" alt="..."/>
                                    </div>
                            </div>
                    </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                                <div class="card-header">
                                    <h4 class="card-header-title">Title & Body</h4>
                                </div>
                                <div class="card-body">

                                        <div class="form-group">
                                                <label>Title</label>
                                                <input class="form-control" type="text" value="{{ $post->title }}" id="title" name="title" maxlength="190" required/>
                                        </div>
                                        <div class="form-group">
                                                <label>URL Slug</label>
                                                <input class="form-control" type="text" value="{{ $post->slug }}" id="slug" name="slug" required/>
                                        </div>

                                        <div class="form-group">
                                                <label  for="article_ckeditor">Body</label>
                                                <textarea class="form-control" id="article_ckeditor" name="body" rows="25" value="{{$post->body}}">{{$post->body}}</textarea>
                                                <div  style="border: 1px solid #ced4da;border-bottom-left-radius: .25rem;border-bottom-right-radius: .25rem;">
                                                    <button type="button" style="color:#1da1f2;" class="btn btn-default mr-2" data-toggle="modal" data-target="#AddTwitterPost"><i class="fab fa-twitter"></i><span class="ml-2">Add Twitter Post</span></button>
                                                    <button type="button"  style="color:#c13584;" class="btn btn-default mr-2" data-toggle="modal" data-target="#AddInstagramPost"><i class="fab fa-instagram"></i><span class="ml-2">Add Instagram Post</span></button>
                                                    <button type="button"  style="color:#d32f2f;" class="btn btn-default mr-2" data-toggle="modal" data-target="#AddYoutubeVideo"><i class="fab fa-youtube"></i><span class="ml-2">Add Youtube Video</span></button>
                                                </div>
                                        </div>

                                        <input type="hidden" name="post_id" id="post_id" value="{{ $post->id }}" />
                                        <input type="hidden" name="plainbody" id="plainbody" value="{{$post->plainbody}}"/>
                                        <input type="hidden" name="user_id" id="user_id" value="{{$post->user['id']}}"/>

                                        <button class="btn btn-success btn-block" type="submit">Update Post</button>

                                </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        @include('admin.dashboard.post.modals.modal_add_image')
        @include('admin.dashboard.post.modals.modal_add_instagram')
        @include('admin.dashboard.post.modals.modal_add_tweet')
        @include('admin.dashboard.post.modals.modal_add_youtube')


@endsection
