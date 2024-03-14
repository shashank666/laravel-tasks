@extends('frontend.layouts.app')
@section('title','Write Your Article - Opined')
@section('description','Write your article, feel free and say your words , share your knowledge . Every day, thousands of people read, write discuss and share their opinion & article on Opined.')
@section('keywords','Write Article,Feel Free,Say Your Words,Share your Knowledge')

@push('meta')
<link rel="canonical" href="http://www.weopined.com/opinion/write" />
<link href="http://www.weopined.com/opinion/write" rel="alternate" reflang="en" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Write Your Article - Opined">
<meta name="twitter:description" content="Write your article,feel free and say your words , share your knowledge . Every day, thousands of people read, write discuss and share their opinion & article on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="http://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Write Your Article - Opined" />
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/opinion/write" />
<meta property="og:image" content="http://www.weopined.com/favicon.png" />
<meta property="og:description" content="Write your article ,feel free and say your words ,  share your knowledge . Every day, thousands of people read, write discuss and share their opinion & article on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
    <link rel="stylesheet" type="text/css" id="bootstrap-select" href="/vendor/bootstrap-select/bootstrap-select.min.css" />
    <link rel="stylesheet" type="text/css" href="/vendor/tagmanager/tagmanager.min.css"/>
    <link rel="stylesheet" type="text/css" href="/vendor/cropper/cropper.min.css" />
<link rel="stylesheet" href="/css/themes/test.css">
<link rel="stylesheet" href="/css/editor-insert.css">
<link rel="stylesheet" href="/css/editor.css">
   <!-- <link rel="stylesheet" href="/css/editor.css">
    <link rel="stylesheet" href="/css/themes/test.css">
    <link rel="stylesheet" href="/css/editor-insert.css">
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">-->
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/css/loading-btn.css"/>
<style type="text/css">
  .loadingimg{
  background-image:url(/img/opined.gif);
  }
</style>

<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor-insert-plugin/2.5.0/css/medium-editor-insert-plugin.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/medium-editor/5.23.3/css/medium-editor.min.css" />
-->
@endpush

    @push('scripts')
    @if($company_ui_settings->show_google_ad=='1')
    {!! $company_ui_settings->google_adcode !!}
    @endif
    <script type="text/javascript" src="/vendor/cropper/cropper.min.js" ></script>
    <script type="text/javascript" src="/vendor/cropper/jquery-cropper.js"></script>
    <script type="text/javascript" src="/vendor/cropper/canvas-to-blob.min.js"></script>

    <script type="text/javascript" src="/vendor/bootstrap-select/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="/vendor/tagmanager/tagmanager.min.js"></script>
    <script type="text/javascript" src="/vendor/bootstrap-3-typeahead/bootstrap3-typeahead.min.js"></script>
    <!--<script type="text/javascript" src="/vendor/ckeditor/ckeditor.js"></script>-->
    <script type="text/javascript">
    // $(window).on('load',function(){
    //     setTimeout ("$('#opined_rsm_offer').modal('show')", 3000);
    // });
</script>
    <script>
            $(document).ready(function() {
            $('#cover_preview').css('display','none');
/*
            var removeButtons = 'Print,Save,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Anchor,Font,Styles,About,ShowBlocks,BGColor,TextColor,Maximize,Language,BidiRtl,BidiLtr,Image,Flash,PasteText,PasteFromWord,PageBreak,Smiley';
            var toolbarGroups = [
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection','editing' ] },
                { name: 'basicstyles', groups: [ 'basicstyles'] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                { name: 'insert', groups: [ 'insert' ]},
                { name: 'links', groups: [ 'links' ] },
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] },
            ];
            //  extraPlugins: 'autogrow'
            CKEDITOR.replace('article_ckeditor',{
                height: '350px',
                scayt_autoStartup:true,
                scayt_maxSuggestions:3,
                toolbarGroups,
                removeButtons,
                extraPlugins: 'autocomplete,textmatch,button,panelbutton,textwatcher,panel,floatpanel,xml,ajax,emoji,mentions,dialog,dialogui,menu,menubutton,wsc,scayt,wordcount',
                removePlugins: 'image,forms,resize,elementspath',
                wordcount : {
                    showCharCount : true,
                    showWordCount : true
                },
                mentions: [
                  {
                    feed: function( options, callback ) {
                        data= $.get('/search/threads?q=' + encodeURIComponent( options.query )  ,function (data) {
                            if(data.threads){
                                return callback(data.threads);
                            }else{
                                return callback();
                            }
                        });
                    },
                    marker: '#',
                    itemTemplate: '<li data-id="{id}"><strong>{name}</strong></li>',
                    outputTemplate:`<a href="/thread/{slug}" data-name="{name}" class="thread_link">{name}</a>`,
                    minChars: 2
                  }
                ]
            });
*/
            var tagApi = $("#tags").tagsManager({
                delimiters: [9,13,32,44],
                maxTags :5,
                validator:function(){
                    if($("#tags").val().length>=3 && $("#tags").val().charAt(0)!=='#'){
                        return true;
                    }else{return false;}
                }
            });

            var keywordApi=$('#keywords').tagsManager({
                delimiters: [9,13,44],
                maxTags :10,
                validator:function(){
                    if($("#keywords").val().length>=3){
                        return true;
                    }else{return false;}
                }
            });



            $(".typeahead").typeahead({
                source: function (query, process) {
                    return $.get('/search/threads',{ q: query ,_token:$('meta[name="csrf-token"]').attr('content')},function (data) {
                    return process(data.threads);
                    });
                },
                afterSelect :function (item){
                    tagApi.tagsManager("pushTag", item.name);
                }
            });
});
    </script>
    <script type="text/javascript" src="/js/custom/write.js?<?php echo time()?>"></script>
    <script type="text/javascript" src="/js/custom/add_social_posts.js?<?php echo time()?>"></script>
    <script src="https://www.instagram.com/static/bundles/base/EmbedSDK.js/70de6f18b9b4.js"></script>
   <script src="/js/editor.js"></script>
   <!-- <script src="/js/editor.js"></script>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.runtime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.ui.widget@1.10.3/jquery.ui.widget.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.iframe-transport/1.0.1/jquery.iframe-transport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload.min.js"></script>
<script src="/js/editor-insert.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.12/handlebars.runtime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.ui.widget@1.10.3/jquery.ui.widget.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.iframe-transport/1.0.1/jquery.iframe-transport.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.28.0/js/jquery.fileupload.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/medium-editor-insert-plugin/2.5.0/js/medium-editor-insert-plugin.min.js"></script>
<script>  
 $(document).ready(function(){  
      function autoSave()  
      {     
            
            var post_id = $('#post_id').val();
           var title = $('#title').val();  
           var body = $('#article_ckeditor').val(); 
           var cover_imageurl =  $('#coverimageurl').val();
           var plain_body = $('#plainbodyauto').val();
           
           var status = 0;
           if(title != '' && body != '')  
           {  
                $.ajax({  
                     url:"{{route('autosave')}}",  
                     method:"POST",  
                     data:{title:title, body:body, status:status,_token: '{{ csrf_token() }}', postId:post_id, coverimageurl:cover_imageurl,plainbody:plain_body},  
                     dataType:"json",  
                     success:function(data)  
                     {  
                          if(data != '')  
                          {     
                               $('#post_id').val(data["postId"]);  
                          }  
						  $("#autoSave").fadeIn(3000, function () {
                              $(this).text("Changes Saved").fadeOut(3000);
                              $("#autoSave").append(" &#10004; ");
                              $("#autoSave").css("background-color", '#ff9800');
                                $("#autoSave").css("color", '#fff');
                            });

                          /*$('#autoSave').text("Saved as draft");  
                          setInterval(function(){  
                               $('#autoSave').text('');  
                          }, 5000); */ 
                     } 
                setInterval(function() {
                $("#autoSave").fadeIn(3000, function () {
                  $(this).text("Changes Saved").fadeOut(3000);
                  $("#autoSave").append(" &#10004; ");
                  $("#autoSave").css("background-color", '#ff9800');
                    $("#autoSave").css("color", '#fff');
                });
            }, 100000);  					 
                });  
           }  
           /* var i = 1;
            var sampleMessages = [ "Changes saved" , ""];
            setInterval(function() {
                var newText = sampleMessages[i++ % sampleMessages.length];
                $("#autoSave").fadeOut(500, function () {
                  $(this).text(newText).fadeIn(500);
                  $("#autoSave").append(" &#10004; ");
                  $("#autoSave").css("background-color", '#ff9800');
                    $("#autoSave").css("color", '#fff');
                });
            }, 1 * 5000);  */              
      }  

      setInterval(function(){   
           autoSave();   
           }, 100000);  
 });  
             
 </script>
    @endpush

@section('content')
  @if($flag!=1)
@include('frontend.partials.rsm_modal')
@endif

            @if(session()->has('status'))
              @if(session('status')=='draft')
                <div class="row">
                        <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10 col-sm-12 col-12">
                            <p class="alert alert-info alert-dismissible fade show" role="alert">{{session('statusText')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <p>
                            <br/>

                            <div class="jumbotron">
                                    <h2>Your article saved as draft .
                                        <span style="float:right;">
                                            <a class="btn btn-primary" href="/opinion/edit/{{session('post')->slug}}">
                                                <span><i class="fas fa-pencil-alt"></i></span>
                                                Edit Article
                                            </a>
                                        </span>
                                    </h2>
                            </div>
                        </div>
                </div>
              @endif

              @if(session('status')=='published')
                <div class="row">
                    <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10 col-sm-12 col-12">
                            <p class="alert alert-success alert-dismissible fade show" role="alert">{{session('statusText')}}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <p>
                            <br/>

                            <div class="jumbotron">
                                <a href="/opinion/{{session('post')->slug}}" style="color:#212121;"><h1 class="display-4">{{session('post')->title}}</h1></a>
                                <hr class="my-4">
                                <center>
                                <p>Its Time To Share Your Article</p>
                                <p class="lead">
                                    <a class="btn text-white" style="background-color:#1da1f2;" target="_blank" role="button" href="https://twitter.com/share?url=https://www.weopined.com/opinion/{{session('post')->slug}}"><span><i class="fab fa-twitter-square mr-2"></i></span>Share On Twitter</a>
                                    <a class="btn text-white" style="background-color:#3b5998;" target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{session('post')->slug}}"><span><i class="fab fa-facebook-square  mr-2"></i></span>Share On Facebook</a>
                                    <a class="btn text-white" style="background-color:#0077b5;" target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{session('post')->slug}}"><span><i class="fab fa-linkedin  mr-2"></i></span> Share On Linkedin </a>
                                </p>
                                </center>
                            </div>

                    </div>
                </div>
              @endif
            @else
                <div class="row" id="writeOpinionDiv">

                    <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10  col-sm-12 col-12">
                            <!--<h1 class="my-4">Write Your Article</h1>-->
                            
                            
                            <div class="preview-container">
                            <div id="coverPreviewDiv" style="margin-top:16px;margin-bottom:16px;display:block;">
                                <img src="/img/No Preview Available.png" id="coverPreview" style="width:920px; height:400px;" class="img-fluid previewAuth"/>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-secondary"  onclick="openAddImageModal('coverimage');">Cover Image *<span class="ml-2"><i class="far fa-image"></i></span><span class="ml-2 text-secondary" data-toggle="tooltip" title="Ideal size for cover is 920 X 400" data-placement="right"><i class="fas fa-info-circle"></i></span></button>
                                
                            </div>
                            </div>

                            
                            <div class="row">
                                
                             <div class="col-md-12 ">
                                <div class="editable pt-2 title" data-placeholder="Title *" id="new-editor-title" style="font-size: 2.5rem;font-family: 'Lora', serif;"></div><hr>
                                <span id="title_span_max" style="display: none">Max 100 Characters are allowed</span>
                                <span id="title_span_min" style="display: none">Please Enter the Title of Article</span>
                            </div>
                            
                            <br><br>
                            <div class="col-md-12 pt-5">
                                <div class="editable pt-2 title" data-placeholder="Enter your text for Body *" id="new-editor" style="font-size: larger;font-family: 'Lora', serif;"></div>
                                <hr>
                                <span id=body_span_min style="display: none; color:red">* Minimum 300 Words to submit Article</span>
                                <span id=body_span_max style="display: none; color:red">* Maximum 1200 Words to submit Article</span>
                            </div>

                            

                    </div>
                            <form id="create_post_form" action="{{route('store')}}" method="post" enctype="multipart/form-data" class="mt-3">
                                @include('frontend.partials.message')
                                {{csrf_field()}}

                                <div class="form-group" style="display: none">
                                        <label class="control-label" for="title" style="display:block;">
                                            <span>Article Title ( Max 100 Characters )</span>
                                            <span id="title_chars" style="float:right"></span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" maxlength="100"/>
                                </div>

                                

                                <!--<div class="form-group">
                                    <label class="control-label" for="btnAddimage">Cover Image</label><br/>
                                    <button type="button" class="btn btn-outline-secondary"  onclick="openAddImageModal('coverimage');">Add Cover Image<span class="ml-2"><i class="far fa-image"></i></span></button>
                                </div>
                                        

                                <input type="hidden"  id="coverimageurl" name="coverimageurl">

                                <div id="coverPreviewDiv" style="margin-top:16px;margin-bottom:16px;display:none;">
                                    <img src="#" id="coverPreview" class="img-fluid"/>
                                </div>
                                -->
                                <input type="hidden"  id="coverimageurl" name="coverimageurl">
                                <div class="form-group" style="display: none">
                                    <label class="control-label" for="article_ckeditor">Article Body</label>
                                    <textarea class="form-control" id="article_ckeditor" name="body" rows="25"></textarea>
                                    <!--<div class="p-2 bg-light d-flex flex-md-row flex-column justify-content-between" style="border: 1px solid #ced4da;border-bottom-left-radius: .25rem;border-bottom-right-radius: .25rem;">
                                        <button type="button" style="color:#00897b" class="btn bg-light mr-md-2 mb-md-0 mb-2" onclick="openAddImageModal('blogimage');">Image<span class="ml-2"></span><i class="fas fa-image"></i></span></button>
                                        <button type="button" style="color:#1da1f2" class="btn bg-light mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddTwitterPost">Twitter Post<span class="ml-2"><i class="fab fa-twitter"></i></span></button>
                                        <button type="button"  style="color:#c13584" class="btn bg-light mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddInstagramPost">Instagram Post<span class="ml-2"><i class="fab fa-instagram"></i></span></button>
                                        <button type="button"  style="color:#d32f2f" class="btn bg-light  mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddYoutubeVideo">Youtube Video<span class="ml-2"><i class="fab fa-youtube"></i></span></button>
                                        <button type="button"  style="color:#ffa000" class="btn bg-light  mr-md-2 mb-md-0 mb-2"  onclick="openAddGIFModal('writepost');">Gif Image<span class="ml-2"><i class="fas fa-file"></i></span></button>
                                        <button type="button"  style="color:#244363" class="btn bg-light  mr-md-2 mb-md-0 mb-2"  data-toggle="modal" data-target="#AddOpinedOpinion">Opinion<span class="ml-2"><i class="far fa-comment-alt"></i></span></button>
                                    </div>-->
                                </div>
                                <div class="form-group">  
                                     <input type="hidden" name="post_id" id="post_id" value="" />  
                                      
                                </div>
                                <div style="border-radius: 9px;padding: 10px;bottom: 3%;right: 3%;position: fixed;z-index: 3000;" id="autoSave"></div> 
                                <input type="hidden" name="plainbody" id="plainbody" />
                                <input type="hidden" name="plainbodyauto" id="plainbodyauto" />
                                <input type="hidden" name="status" id="status" />
                            <div class="row">
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="category">Select Category *</label>
                                    <select class="form-control selectpicker " data-live-search="true" id="category" name="categories[]" multiple>
                                            @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="keywords">Add Keywords
                                        <span class="ml-2 text-secondary" data-toggle="tooltip" title="Please press ENTER to add keyword" data-placement="right"><i class="fas fa-info-circle"></i></span>
                                    </label>
                                    <input type="text" name="keywords" id="keywords" placeholder="Keywords" class="tm-input form-control tm-input-light"/>
                                </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="tags">Add Tags <span class="ml-2 text-secondary" data-toggle="tooltip" title="Please press ENTER to add Tags" data-placement="right"><i class="fas fa-info-circle"></i></span></label>
                                    <input type="text" name="tags" id="tags" placeholder="Tags" class="typeahead tm-input form-control tm-input-info"/>
                                </div>
                                </div>
                            </div>
                                Please make sure that you follow our <a href="/legal/article_guideline" target="_blank">Opinion Article Guidelines</a> before publishing your article. Opined reserves right to remove any article, which does not follow the <a href="/legal/article_guideline" target="_blank">Opinion Article Guidelines</a>, from our website.
                                <br><span style="color: red">*</span> Defines mandatory fields.
                                <div class="d-flex flex-md-row flex-column justify-content-center my-5">
                                    <div class="btn btn-default ld-ext-right running aftersubmit mb-md-0 mb-2 ml-md-2" style="display: none; background-color: #ff9800">
                                      Submitting...
                                      <div class="ld ld-loader loadingimg"><center style="display: none;"><img src="/img/opined.gif" alt="Loading"></center></div>
                                    </div>
                                    <button type="submit" class="btn btn-success mb-md-0 mb-2 disabled beforesubmit" id="btnPublish" >Preview Your Article<span class="ml-2"><i class="fas fa-check"></i></span></button>

                                    @if(Auth::user() && Auth::user()->registered_as_writer==1)
                                    <div class="btn btn-default ld-ext-right running afterdraft mb-md-0 mb-2 ml-md-2" style="display: none; background-color: #007bff">
                                      Saving...
                                      <div class="ld ld-loader loadingimg"><center style="display: none;"><img src="/img/opined.gif" alt="Loading"></center></div>
                                    </div>
                                    <button type="button" class="btn btn-primary mb-md-0 mb-2 ml-md-2 beforedraft" id="btnSaveDraft">Save As Draft<span class="ml-2"><i class="far fa-file-alt"></i></span></button>
                                    @endif
                                </div>
                            </form>

                            <div class="custom-error" style="display:none;margin-top:16px;"></div>

                           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            @endif
                          -->

                    </div>
                </div>

            @include('frontend.posts.modals.modal_add_image')
            @include('frontend.posts.modals.modal_add_tweet')
            @include('frontend.posts.modals.modal_add_youtube')
            @include('frontend.posts.modals.modal_add_instagram')
            @include('frontend.posts.modals.modal_add_gif')
            @include('frontend.posts.modals.modal_add_opined')

            @endif

            <script>var editor = new MediumEditor('.editable', {
                spellcheck: true,
                buttonLabels: 'fontawesome',
                targetBlank: true,
                toolbar: {
                    buttons: ['bold','italic','underline','unorderedlist','h2','anchor','quote'],
                    diffLeft: 0,
                    diffTop: -5
                        }
                        
                    });
                $(function () {
                    $('.editable').mediumInsert({
                        editor: editor,
                        addons: {
                            images: {

                            fileUploadOptions: {
                              url: "upload",
                              formData: {
                                    type: 'post',
                                    enctype: "multipart/form-data",
                                    _token: '{{ csrf_token() }}',
                                    acceptFileTypes: /(.|\/)(gif|jpe?g|png)$/i,
                                  },
                              }
                              
                              }, 
                            embeds: { 
            label: '<span class="fa fa-paperclip"></span>',
            placeholder: 'Paste Opined, YouTube, Facebook, Twitter or Instagram link and press Enter',
            captions: true,
            captionPlaceholder: 'Type caption (optional)', 
            oembedProxy: null,  
             }                      }
                    });
            });
            </script>
<script type="text/javascript">
    input = document.querySelector('#new-editor-title');

    settings = {
      maxLen: 100,
    }

    keys = {
      'backspace': 8,
      'shift': 16,
      'ctrl': 17,
      'alt': 18,
      'delete': 46,
      // 'cmd':
      'leftArrow': 37,
      'upArrow': 38,
      'rightArrow': 39,
      'downArrow': 40,
    }

    utils = {
      special: {},
      navigational: {},
      isSpecial(e) {
        return typeof this.special[e.keyCode] !== 'undefined';
      },
      isNavigational(e) {
        return typeof this.navigational[e.keyCode] !== 'undefined';
      }
    }

    utils.special[keys['backspace']] = true;
    utils.special[keys['shift']] = true;
    utils.special[keys['ctrl']] = true;
    utils.special[keys['alt']] = true;
    utils.special[keys['delete']] = true;

    utils.navigational[keys['upArrow']] = true;
    utils.navigational[keys['downArrow']] = true;
    utils.navigational[keys['leftArrow']] = true;
    utils.navigational[keys['rightArrow']] = true;

    input.addEventListener('keydown', function(event) {
      let len = event.target.innerText.trim().length;
      hasSelection = false;
      selection = window.getSelection();
      isSpecial = utils.isSpecial(event);
      isNavigational = utils.isNavigational(event);
      
      if (selection) {
        hasSelection = !!selection.toString();
      }
      
      if (isSpecial || isNavigational) {
        return true;
      }
      
      if (len >= settings.maxLen && !hasSelection) {
        event.preventDefault();
        return false;
      }
      
    });
    
    </script>
    
@endsection

