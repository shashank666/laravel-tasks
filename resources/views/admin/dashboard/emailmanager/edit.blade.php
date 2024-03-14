@extends('admin.layouts.app')

@push('scripts')
<script src="/public_admin/assets/libs/jquery-validate/jquery.validate.min.js" type="text/javascript"></script>
<script src="/public_admin/assets/libs/ckeditor/ckeditor.js"></script>

  <script>

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

        $('#send_email_form').validate({
            rules: {
                'subject':{
                    required:true
                },
                'message':{
                    required:true
                }
            },
            messages: {
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
                        Edit Email Content
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
            <form method="POST" action="{{ route('admin.email.update') }}" id="send_email_form">
                {{ csrf_field() }}
                <div class="card-body">
                        <input type="hidden" name="email_id" value="{{ $email->id }}"/>
                        <div class="form-group">
                                <label>Subject :</label>
                                <input class="form-control" name="subject" type="text" value="{{ $email->email_subject }}" id="reply-subject" required/>
                        </div>

                        <div class="form-group">
                            <label>Message :</label>
                            <textarea id="ckeditor_email" name="message" required>
                                    {{$email->email_content}}
                            </textarea>
                        </div>

                        <div class="form-group">
                                <label>Add Images To Mail :</label>
                                <a href="#AddImageModal" data-toggle="modal" data-target="#AddImageModal" class="btn btn-outline-secondary">Add Image</a>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Update Mail & See Preview</button>
                </div>
            </form>

        </div>
    </div>

    @include('admin.dashboard.emailmanager.modal_upload_image')

@endsection
