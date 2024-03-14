@extends('frontend.layouts.app')
@section('title','Edit Your Article - Opined')

    @push('meta')
        <meta name="posttags" content=<?php echo json_encode($post->threadnames);?>></meta>
        <meta name="postkeywords" content=<?php echo json_encode(implode(",",$post->keywords->pluck('name')->toArray()));?>></meta>
    @endpush

    @push('styles')
        <link rel="stylesheet" type="text/css" id="bootstrap-select" href="/vendor/bootstrap-select/bootstrap-select.min.css" />
        <link rel="stylesheet" type="text/css" href="/vendor/tagmanager/tagmanager.min.css"/>
        <link rel="stylesheet" type="text/css" href="/vendor/cropper/cropper.min.css" />
        <link rel="stylesheet" href="/css/editor.css">
        <link rel="stylesheet" href="/css/themes/test.css">
        <link rel="stylesheet" href="/css/editor-insert.css">
		<link rel="stylesheet" type="text/css" href="/css/loading.css"/>
        <link rel="stylesheet" type="text/css" href="/css/loading-btn.css"/>
        <style type="text/css">
          .loadingimg{
          background-image:url(/img/opined.gif);
          }
        </style>

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

    <script>
        $(document).ready(function() {
            $('#cover_preview').css('display','none');
            //Source
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
            // extraPlugins: 'autogrow'
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
                    return $.get('/search/threads',{ q: query ,_token:$('meta[name="csrf-token"]').attr('content')}, function (data) {
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
    @endpush

@section('content')
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
                       <!--<h1 class="my-4">Edit Your Article</h1>-->
                            
                            
                            <div class="preview-container">
                            <div id="coverPreviewDiv" style="margin-top:16px;margin-bottom:16px;display:block;">
                                <img src="{{$post->coverimage}}" id="coverPreview" style="width:920px; height:400px;" class="img-fluid previewAuth" onerror="this.onerror=null;this.src='/img/No Preview Available.png';" />
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-secondary"  onclick="openAddImageModal('coverimage');">Cover Image *<span class="ml-2"><i class="far fa-image"></i></span><span class="ml-2 text-secondary" data-toggle="tooltip" title="Ideal size for cover is 920 X 400" data-placement="right"><i class="fas fa-info-circle"></i></span></button>
                                
                            </div>
                            </div>

                            
                            <div class="row">
                                
                             <div class="col-md-12 ">
                                <div class="editable pt-2 title" data-placeholder="Title *" id="new-editor-title" style="font-size: 2.5rem;font-family: 'Lora', serif;">{{$post->title}}</div><hr>
                                <span id="title_span_max" style="display: none">Max 100 Characters are allowed</span>
                                <span id="title_span_min" style="display: none">Please Enter the Title of Article</span>
                            </div>
                            
                            <br><br>
                            <div class="col-md-12 pt-5">
                                <div class="editable pt-2 title" data-placeholder="Enter your text for Body *" id="new-editor" style="font-size: larger;font-family: 'Lora', serif;">{!!$post->body!!}</div>
                                <hr>
                                <span id=body_span_min style="display: none; color:red">* Minimum 300 Words to submit Article</span>
                                <span id=body_span_max style="display: none; color:red">* Maximum 1200 Words to submit Article</span>
                            </div>

                            

                    </div>
                            <form id="create_post_form" action="{{route('update')}}" method="post" enctype="multipart/form-data">
                                @include('frontend.partials.message')
                                {{csrf_field()}}

                                <div class="form-group" style="display: none">
                                        <label class="control-label" for="title" style="display:block;">
                                            <span>Article Title ( Max 100 Characters )</span>
                                            <span id="title_chars" style="float:right"></span>
                                        </label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{$post->title}}" maxlength="100"/>
                                </div>

                               


                               <!-- <div class="form-group">
                                    <label class="control-label" for="btnAddimage">Cover Image</label><br/>
                                    <button type="button" class="btn btn-outline-secondary"  onclick="openAddImageModal('coverimage');">Add Cover Image<span class="ml-2"><i class="far fa-image"></i></span></button>
                                </div>
                                -->
                                <input type="hidden"  id="coverimageurl" name="coverimageurl" value="{{$post->coverimage}}">
                                <!--
                                <div id="coverPreviewDiv" style="margin-top:16px;margin-bottom:16px;display:{{$post->coverimage!=null?'block':'none'}};">
                                    <img src="{{$post->coverimage}}" id="coverPreview" class="img-fluid"/>
                                </div>
                                -->
                                <div class="form-group" style="display: none">
                                    <label class="control-label" for="article_ckeditor">Article Body</label>
                                    <textarea class="form-control" id="article_ckeditor" name="body" rows="25" value="{{$post->body}}">{{$post->body}}</textarea>
                                   <!-- <div class="p-2 bg-light d-flex flex-md-row flex-column justify-content-between" style="border: 1px solid #ced4da;border-bottom-left-radius: .25rem;border-bottom-right-radius: .25rem;">
                                        <button type="button" style="color:#00897b" class="btn bg-light mr-md-2 mb-md-0 mb-2" onclick="openAddImageModal('blogimage');">Image<span class="ml-2"></span><i class="fas fa-image"></i></span></button>
                                        <button type="button" style="color:#1da1f2" class="btn bg-light mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddTwitterPost">Twitter Post<span class="ml-2"><i class="fab fa-twitter"></i></span></button>
                                        <button type="button"  style="color:#c13584" class="btn bg-light mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddInstagramPost">Instagram Post<span class="ml-2"><i class="fab fa-instagram"></i></span></button>
                                        <button type="button"  style="color:#d32f2f" class="btn bg-light  mr-md-2 mb-md-0 mb-2" data-toggle="modal" data-target="#AddYoutubeVideo">Youtube Video<span class="ml-2"><i class="fab fa-youtube"></i></span></button>
                                        <button type="button"  style="color:#ffa000" class="btn bg-light  mr-md-2 mb-md-0 mb-2"  onclick="openAddGIFModal('writepost');">Gif Image<span class="ml-2"><i class="fas fa-file"></i></span></button>
                                        <button type="button"  style="color:#244363" class="btn bg-light  mr-md-2 mb-md-0 mb-2"  data-toggle="modal" data-target="#AddOpinedOpinion">Opinion<span class="ml-2"><i class="far fa-comment-alt"></i></span></button>
                                    </div>-->
                                </div>

                                <input type="hidden" name="plainbody" id="plainbody" value="{{$post->plainbody}}"/>
                                <input type="hidden" name="status" id="status"/>
                                <input type="hidden" name="slug" value="{{$post->slug}}"/>
                            <div class="row">
                                <div class="col-md-4">
                                 <div class="form-group">
                                    <label class="control-label" for="category">Select Topic</label><br/>
                                    <select class="form-control selectpicker" id="category" name="categories[]" data-live-search="true" data-width="auto" dropupAuto="false" data-size="20" multiple>
                                            @foreach($categories as $category)
                                                @if(in_array($category->id,$post->categoryids))
                                                    <option value="{{$category->id}}" selected>{{$category->name}}</option>
                                                @else
                                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                                @endif
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
                                    <label class="control-label" for="tags">Add Tags</label>
                                    <input type="text" name="tags" id="tags" placeholder="Tags" class="typeahead tm-input form-control tm-input-info"/>
                                </div>
                                 </div>
                                </div>
                                <div class="d-flex flex-md-row flex-column justify-content-center my-5">
                                    @if(Auth::user() && Auth::user()->registered_as_writer==1)
                                    <div class="btn btn-default ld-ext-right running afterdraft mb-md-0 mb-2 ml-md-2" style="display: none; background-color: #007bff">
                                      Saving...
                                      <div class="ld ld-loader loadingimg"><center style="display: none;"><img src="/img/opined.gif" alt="Loading"></center></div>
                                    </div>
                                    <button type="button" class="btn btn-primary mb-md-0 mb-2 beforedraft" id="btnSaveDraft">Save As Draft<span class="ml-2"><i class="far fa-file-alt" aria-hidden="true"></i></span></button>
                                    @endif
                                    <div class="btn btn-default ld-ext-right running aftersubmit mb-md-0 mb-2 ml-md-2" style="display: none; background-color: #ff9800">
                                      Submittimg...
                                      <div class="ld ld-loader loadingimg"><center style="display: none;"><img src="/img/opined.gif" alt="Loading"></center></div>
                                    </div>
                                    <button type="submit" class="btn btn-success mb-md-0 mb-2 ml-md-2 disabled beforesubmit" id="btnPublish">Preview Article<span class="ml-2"><i class="fas fa-check" aria-hidden="true"></i></span></button>

                                    <button type="button" class="btn btn-danger mb-md-0 mb-2 ml-md-2" id="btnDelete">Delete Article<span class="ml-2"><i class="far fa-trash-alt" aria-hidden="true"></i></span></button>
                                </div>
                            </form>


                            <div class="modal fade" id="deleteOpinionModal" tabindex="-1" role="dialog" aria-labelledby="deleteOpinionModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteOpinionModalTitle">Delete Article ?</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                     <form method="POST" action="{{route('delete')}}" id="deleteOpinionForm">
                                    <div class="modal-body">
                                        Are you sure to delete this article  ??  You wont be able to revert this!
                                                {{csrf_field()}}
                                                {{ method_field('delete') }}
                                                <input type="hidden" name="deleteid" value="{{$post->slug}}"/>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger"><i class="far fa-trash-alt mr-2"></i>Delete</button>
                                    </div>
                                    </form>
                                    </div>
                                </div>
                            </div>

                            <div class="custom-error" style="display:none;margin-top:16px;"></div>

                           <!-- @if($company_ui_settings->show_google_ad=='1' && $google_ad)
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            <div class="mt-3">{!! $google_ad->ad_code !!}</div>
                            @endif-->
                    </div>
            </div>

            @include('frontend.posts.modals.modal_add_image')
            @include('frontend.posts.modals.modal_add_tweet')
            @include('frontend.posts.modals.modal_add_youtube')
            @include('frontend.posts.modals.modal_add_instagram')
            @include('frontend.posts.modals.modal_add_gif')
            @include('frontend.posts.modals.modal_add_opined')
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
                              url: "../upload",
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

        @endif
@endsection
