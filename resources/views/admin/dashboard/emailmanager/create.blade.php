@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" type="text/css" href="/public_admin/assets/libs/tagmanager/tagmanager.min.css"/>
<style>
        .tt-query, /* UPDATE: newer versions use tt-input instead of tt-query */
        .tt-hint {

            width: 396px;
            height: 30px;
            padding: 8px 12px;
            font-size: 24px;
            line-height: 30px;
            border: 2px solid #ccc;
            border-radius: 8px;
            outline: none;
        }

        .tt-query { /* UPDATE: newer versions use tt-input instead of tt-query */
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }

        .tt-hint {
            color: #999;
            display: none;
        }

        .tt-menu { /* UPDATE: newer versions use tt-menu instead of tt-dropdown-menu */
            width: 422px;
            margin-top: 12px;
            padding: 8px 0;
            background-color: #fff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
        }

        .tt-suggestion {
            padding: 3px 20px;
            font-size: 18px;
            line-height: 24px;
        }

        .tt-suggestion.tt-is-under-cursor { /* UPDATE: newer versions use .tt-suggestion.tt-cursor */
            color: #fff;
            background-color: #0097cf;

        }

        .tt-suggestion p {
            margin: 0;
        }
</style>
@endpush


@push('scripts')
<script src="/public_admin/assets/libs/jquery-validate/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/public_admin/assets/libs/tagmanager/tagmanager.min.js"></script>
<script src="/public_admin/assets/libs/ckeditor/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.2.1/bloodhound.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.2.1/typeahead.jquery.min.js"></script>
  <script>
    function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
    }

    function readURL(input, targetDivID) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(targetDivID).attr('src', e.target.result);
                $(targetDivID).css('display', 'block');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function closeAddImageModal() {
        $('#image').val('');
        $('#modalImagePreview').attr('src','');
        $('#uploadPreviewDiv').css('display', 'none');
        $('#btnUploadImage').css('display', 'none');
        $('.custom-file-label').html('Upload Image (Maximum Size 2 MB)');
        $('#AddImageModal').modal('hide');
    };


    $(document).ready(function () {

        CKEDITOR.replace('ckeditor_email', {
            extraPlugins: 'autogrow',
        });

        var tagApi = $("#custom").tagsManager({
            delimiters: [9,13,32,44],
            onlyTagList:true,
            validator:function(){
                if($("#custom").val().length>=2 && validateEmail($("#custom").val())){
                    return true;
                }else{return false;}
            }
        });

        let csrf_token=$('meta[name="csrf-token"]').attr('content');
        var bloodhoundSuggestions = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace("email"),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: `/cpanel/email/find_user?_token=${csrf_token}&q=%QUERY`,
                wildcard: '%QUERY',
                filter: function (data) {
                    if (data) {
                        return $.map(data.users, function (object) {
                            return object;
                        });
                    } else {
                        return {};
                    }
                },
            }
          });


        $('#custom').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
          },
          {
            name: 'users',
            source: bloodhoundSuggestions ,
            limit: 100,
            displayKey: 'email',
            templates: {
                suggestion: function(item) {
                    console.log(item);
                    let str='<div class="media">'+
                            '<img src="'+item.image+'" height="36" width="36" class="rounded-circle mr-3" alt="...">'+
                            '<div class="media-body">'+
                              '<h5 class="mt-0">'+item.name+'</h5>'+
                               '<small class="text-secondary">'+item.email+'</small>'+
                            '</div>'+
                          '</div>';
                    return str;
                },
                pending: function (query) {
                    return '<div class="p-2">Loading...</div>';
                }
            }
          });

          $('#custom').bind('typeahead:selected', function(obj, datum) {
            tagApi.tagsManager("pushTag", datum.email);
            $('#custom').typeahead('val','');
        });

        $('#send_email_form').validate({
            rules: {
                'to': {
                    required: true
                },
                'subject':{
                    required:true
                },
                'message':{
                    required:true
                }
            },
            messages: {
                to:{
                    required:"To is required !",
                },
                subject: {
                  required:"Subject is required !",
                },
                message:{
                    required:"Message is required !",
                }
            },
            submitHandler: function(form) {
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

        $('#div_specific_users').css('display','block');
        $('#div_to_users').css('display','none');

        $("input[name='email_to_type']").change(function(e){
           if(e.target.value=='predefined'){
                $('#div_to_users').css('display','block');
                $('#div_specific_users').css('display','none');
            }else{
                $('#div_specific_users').css('display','block');
                $('#div_to_users').css('display','none');
            }
        });



        $(document).on('change', "#image", function(e) {
            $('#btnUploadImage').css('display', 'none');
            if ($('#image')[0].files[0] !== undefined) {
                var fileName = e.target.files[0].name;
                $('.custom-file-label').html(fileName);
                var file_size = $('#image')[0].files[0].size;
                if (file_size > 2097152) {
                    $('#upload-response').removeClass();
                    $('#upload-response').addClass('alert alert-danger');
                    $('#upload-response').html('Filesize is more than 2 MB not allowed');
                    $("#upload-response").fadeOut(4500, function() {
                        $('#image').val('');
                        $('#uploadPreviewDiv').css('display', 'none');
                        $('#modalImagePreview').attr('src', '');
                    });
                } else {
                    readURL(this, '#modalImagePreview');
                    $('#uploadPreviewDiv').css('display', 'block');
                    $('#btnUploadImage').css('display', 'block');
                }
            }
        });

        $(document).on('click', '#btnUploadImage', function() {
            $('#btnUploadImage').attr('disabled', 'disabled');
            var form = $('#uploadImageForm')[0];
            var formData = new FormData(form);
            $('#upload-response').removeClass();
            $('#upload-response').addClass('alert alert-info');
            $('#upload-response').html('Uploading...Please Wait');
            $('#upload-response').show();

            $.ajax({
                url:'/cpanel/email/upload',
                type:'POST',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:formData,
                cache : false,
                contentType: false,
                processData: false,
                success:function(response){
                    if (response.status == 'success') {
                        var imgURL = response.image;
                        $('#image').val('');
                        $('#uploadPreviewDiv').css('display', 'none');
                        $('#modalImagePreview').attr('src', '');
                        $('#btnUploadImage').removeAttr('disabled');
                        $('#upload-response').removeClass();
                        $('#upload-response').addClass('alert alert-success');
                        $('#upload-response').html(response.message);
                        addImageToBlogPostBody(imgURL);
                        closeAddImageModal();
                    }else{
                        $('#image').val('');
                        $('#uploadPreviewDiv').css('display', 'none');
                        $('#modalImagePreview').attr('src', '');
                        $('#btnUploadImage').removeAttr('disabled');
                        $('#upload-response').removeClass();
                        $('#upload-response').addClass('alert alert-error');
                        $('#upload-response').html('Failed to upload image , Please try again later');
                        $("#upload-response").show().fadeOut(2500);
                    }
                },
                error:function(err){
                    $('#image').val('');
                    $('#uploadPreviewDiv').css('display', 'none');
                    $('#modalImagePreview').attr('src', '');
                    $('#btnUploadImage').removeAttr('disabled');
                    $('#upload-response').removeClass();
                    $('#upload-response').addClass('alert alert-error');
                    $('#upload-response').html('Failed to upload image , Please try again later');
                    $("#upload-response").show().fadeOut(2500);
                }
            });
        });



    });

    function addImageToBlogPostBody(URL){
        var data='<br/> <table width="100%" style="max-width:640px;"><tr><td><img src="'+URL+'" width="100%" /></td></tr></table><br/>';
        CKEDITOR.instances.ckeditor_email.insertHtml(data);
        var range =  CKEDITOR.instances.ckeditor_email.createRange();
        range.moveToPosition( range.root, CKEDITOR.POSITION_BEFORE_END );
        CKEDITOR.instances.ckeditor_email.getSelection().selectRanges( [ range ] );
    }

</script>
@endpush

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="header-title">
                        Send Email To Users
                        </h1>
                    </div>
                    <div class="col-auto">

                    </div>
                </div>
            </div>
    </div>
    <div class="container my-5">
        @include('admin.partials.message')


        <div class="card">
            <form method="POST" action="{{ route('admin.email.create') }}" id="send_email_form">
                {{ csrf_field() }}
                <div class="card-body">
                        <div class="form-group">
                                <label>Select Send To :</label>
                                 <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="specific" name="email_to_type" class="custom-control-input" value="specific" checked>
                                    <label class="custom-control-label" for="specific">Specific Users</label>
                                  </div>
                                  <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="predefined" name="email_to_type" class="custom-control-input" value="predefined">
                                    <label class="custom-control-label" for="predefined">Predefined Users Set</label>
                                  </div>
                        </div>

                        <div class="form-group" id="div_to_users">
                                <label>To :</label>
                                <select class="form-control" name="to">
                                    <option value="all">All Users</option>
                                    <option value="website">Website Users</option>
                                    <option value="android">App Users</option>

                                </select>
                            </div>
                        <div class="form-group" id="div_specific_users">
                            <label  for="custom">Send To Specific Users</label><br/>
                            <input type="text" name="custom" id="custom" placeholder="Users" class="typeahead tm-input form-control"/>
                        </div>

                        <div class="form-group">
                                <label>Subject :</label>
                                <input class="form-control" name="subject" type="text" value="" id="reply-subject" required/>
                        </div>

                        <div class="form-group">
                            <label>Message :</label>
                            <textarea id="ckeditor_email" name="message" required>
                            </textarea>
                        </div>

                        <div class="form-group">
                                <label>Add Images To Mail :</label>
                                <a href="#AddImageModal" data-toggle="modal" data-target="#AddImageModal" class="btn btn-outline-secondary">Add Image</a>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Create Mail & See Preview</button>
                </div>
            </form>

        </div>
    </div>

    @include('admin.dashboard.emailmanager.modal_upload_image')

@endsection
