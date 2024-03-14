$(document).on('keyup keypress', '#create_post_form', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

$(document).on('keyup', '#title', function() {
    var textlen = 100 - $(this).val().length;
    $('#title_chars').text(textlen + "  Character Remaining");
});


$(document).on('click', '#btnPublish', function(e) {
    e.preventDefault();
    $('#status').val('1');
    $('#plainbody').val(CKEDITOR.instances.article_ckeditor.document.getBody().getText());
    $('.custom-error').empty();
    var title = $.trim($('#title').val());
    var topic = $.trim($('#category').val());
    var coverimage = $.trim($('#coverimageurl').val());
    var body = $.trim($('#plainbody').val());

    if (title === '') {
        $('.custom-error').append('<p class="alert alert-danger">Article Title Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else if (topic === '') {
        $('.custom-error').append('<p class="alert alert-danger">Article Topic Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else if (coverimage === '') {
        $('.custom-error').append('<p class="alert alert-danger">Cover Image Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else if (body === '') {
        $('.custom-error').append('<p class="alert alert-danger">Article Body Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else {
        $('#btnPublish').attr('disabled', 'disabled');
        $('#create_post_form').submit();
    }
});

$(document).on('click', '#btnSaveDraft', function(e) {
    e.preventDefault();
    $('#status').val('0');
    $('#plainbody').val(CKEDITOR.instances.article_ckeditor.document.getBody().getText());
    $('.custom-error').empty();
    var title = $.trim($('#title').val());
    var topic = $.trim($('#category').val());
    var coverimage = $.trim($('#coverimageurl').val());
    var body = $.trim($('#plainbody').val());

    if (title === '') {
        $('.custom-error').append('<p class="alert alert-danger">Article Title Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else if (topic === '') {
        $('.custom-error').append('<p class="alert alert-danger">Article Topic Is Required !!</p><br/>');
        $('.custom-error').css('display', 'block');
        $('.custom-error').fadeOut(5000);
    } else {
        $('#btnSaveDraft').attr('disabled', 'disabled');
        $('#create_post_form').submit();
    }
});


$(document).on('shown.bs.tab', '#image-tab a[data-toggle="tab"]', function(e) {
    var target = $(e.target).attr("href");
    if (target == '#tab_upload_image') {
        $('#imagelink').val('');
    } else {
        $('.custom-file-label').html('Upload Image (Maximum Size 2 MB)');
        $('#modalImagePreview').attr('src', '');
        $('#uploadPreviewDiv').css('display', 'none');
        $('#imagefile').val('');
        window.cropped=false;
        if(window.cropper){ window.cropper.destroy();}
        $('#btnCropGroup').css('display', 'none');
        $('#btnUploadImage').css('display', 'none');
    }
});


$(document).on('change', "#imagefile", function(e) {

    $('#btnCropGroup').css('display', 'none');
    $('#btnUploadImage').css('display', 'none');

    if ($('#imagefile')[0].files[0] !== undefined) {
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);

        var file_size = $('#imagefile')[0].files[0].size;
        if (file_size > 2097152) {
            $('#upload-response').removeClass();
            $('#upload-response').addClass('alert alert-danger');
            $('#upload-response').html('Filesize is more than 2 MB not allowed');
            $("#upload-response").fadeOut(4500, function() {
                $('#imagefile').val('');
                $('#uploadPreviewDiv').css('display', 'none');
                $('#modalImagePreview').attr('src', '');
            });
        } else {
            readURL(this, '#modalImagePreview');
            $('#uploadPreviewDiv').css('display', 'block');
            window.cropped=false;
            setTimeout(initCropper,100);
        }
    }
});

window.cropped=false;

function initCropper(){
    var image = document.getElementById('modalImagePreview');
    if(window.cropper){ window.cropper.destroy();}

    window.cropper = new Cropper(image, {
        viewMode: 1,
        modal:true,
        movable:false,
        rotatable:false,
        scalable:false,
        zoomable:false,
        zoomOnTouch:false,
        cropBoxResizable: false,
        dragMode: 'move',
        zoomOnWheel:false,
        toggleDragModeOnDblclick:false,
        minContainerWidth:120,
        minContainerHeight:120,
        minCropBoxWidth:120,
        minCropBoxHeight:120,
        background:false
    });
    $('#btnCropGroup').css('display','flex');
}

function CropImage(){
    window.cropped=true;
    var imgurl =  window.cropper.getCroppedCanvas(
        {
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }
    ).toDataURL();
    SkipCrop();
    $('#modalImagePreview').attr('src',imgurl);
}

function SkipCrop(){
    if(window.cropper){ window.cropper.destroy();}
    $('#btnCropGroup').css('display', 'none');
    $('#btnUploadImage').css('display', 'block');
}

$(document).on('click', '#btnUploadImage', function() {
    $('#btnUploadImage').attr('disabled', 'disabled');
    if(window.cropped && window.cropper){
        var formData = new FormData();
        var imgurl =  $('#modalImagePreview').attr('src');
        blob = window.dataURLtoBlob && window.dataURLtoBlob(imgurl);
        formData.append('coverimage', blob);

    }else{
        var form = $('#uploadImageForm')[0];
        var formData = new FormData(form);
    }

    $('#upload-response').removeClass();
    $('#upload-response').addClass('alert alert-info');
    $('#upload-response').html('Uploading...Please Wait');
    $('#upload-response').show();

    var modal_from=$('#modal_from').val().trim().toLowerCase();
    var AJAX_URL;
    if(modal_from=="coverimage"){
        AJAX_URL='/file/upload/POST_COVER';
    }else{
        AJAX_URL='/file/upload/BLOG_POST';
    }
    $('#tab_image_link').addClass('disabled');
    $.ajax({
        url:AJAX_URL,
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
                $('#imagefile').val('');
                $('#uploadPreviewDiv').css('display', 'none');
                $('#modalImagePreview').attr('src', '');
                $('#btnUploadImage').removeAttr('disabled');
                $('#upload-response').removeClass();
                $('#upload-response').addClass('alert alert-success');
                $('#upload-response').html(response.message);
                $('#tab_image_link').removeClass('disabled');
                setImage(modal_from,imgURL);
            }else{
                if(modal_from=="coverimage"){
                    $('#coverimageurl').val('');
                }
                $('#imagefile').val('');
                $('#uploadPreviewDiv').css('display', 'none');
                $('#modalImagePreview').attr('src', '');
                $('#btnUploadImage').removeAttr('disabled');
                $('#tab_image_link').removeClass('disabled');
                $('#upload-response').removeClass();
                $('#upload-response').addClass('alert alert-error');
                $('#upload-response').html('Failed to upload image , Please try again later');
                $("#upload-response").show().fadeOut(2500);
            }
        },
        error:function(err){
            if(modal_from=="coverimage"){
                $('#coverimageurl').val('');
            }
            $('#imagefile').val('');
            $('#uploadPreviewDiv').css('display', 'none');
            $('#modalImagePreview').attr('src', '');
            $('#btnUploadImage').removeAttr('disabled');
            $('#upload-response').removeClass();
            $('#tab_image_link').removeClass('disabled');
            $('#upload-response').addClass('alert alert-error');
            $('#upload-response').html('Failed to upload image , Please try again later');
            $("#upload-response").show().fadeOut(2500);
        }
    });
});

$(document).on('click', '#btnDelete', function() {
    $('#deleteOpinionModal').modal('show');
});



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

function CheckImageContentType(URL){
    $.ajax({
        type:'GET',
        url:URL,
        success: function(response, status, xhr){
            var ct = xhr.getResponseHeader("content-type") || "";
            if (xhr.status==200 && ct.indexOf('image') > -1) {
                return true;
            }
        },
        error:function(err){
            return false;
        }
    })
}


function CheckImageLink() {


    if ($('#imagelink').val().length > 0){
        var URL = $('#imagelink').val();
        var from=$('#modal_from').val();
       // var extension = URL.substr((URL.lastIndexOf('.') + 1));
       let event=from=='coverimage'?'POST_COVER':'BLOG_POST';

        if(URL.match(/\.(jpeg|jpg|gif|png)$/) != null && URL.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g)!=null){
                    $('#image-response').removeClass();
                    $('#image-response').addClass('mt-2 alert alert-info alert-dismissible fade show');
                    $('#image-response').text('Loading , please wait ...');
                    $('#image-response').show().fadeOut(3500);

                    $.ajax({
                        url:'/file/load_url/'+event,
                        type:'POST',
                        data:{url:URL},
                        dataType:'json',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response, status, xhr){
                            if (xhr.status==200) {
                                if(from=="coverimage"){
                                    setCoverImage(response.image);
                                }else{
                                    addImageToBlogPostBody(response.image);
                                }       
                                closeAddImageModal();                 
                            }
                        },
                        error:function(err){
                            $('#image-response').text('Error loading image from url');
                            $('#imagelink').val('');
                            $('#image-response').removeClass();
                            $('#image-response').addClass('mt-2 alert alert-danger alert-dismissible fade show');
                            $('#image-response').show().fadeOut(3500);
                        }
                    })
        }else{
            $('#image-response').text('Please Enter Valid Image Url');
            $('#imagelink').val('');
            $('#image-response').removeClass();
            $('#image-response').addClass('mt-2 alert alert-danger alert-dismissible fade show');          
            $('#image-response').show().fadeOut(3500);   
        }
    }
        else{
            $('#image-response').text('Please Enter Valid Image Url');
            $('#imagelink').val('');
            $('#image-response').removeClass();
            $('#image-response').addClass('mt-2 alert alert-danger alert-dismissible fade show');          
            $('#image-response').show().fadeOut(3500);
        }
}

function openAddImageModal(from) {
    if(from=="coverimage"){
        $('.add-image-title').html('Add Cover Image<span class="ml-2"><i class="far fa-image"></i></span>');
        $('.add-image-header').css('background-color','#ff9800');
        $('#modal_from').val('coverimage');
    }else{
        $('.add-image-title').html('Add Image<span class="ml-2"><i class="far fa-image"></i></span>');
        $('.add-image-header').css('background-color','#009688');
        $('#modal_from').val('blogimage');
    }
    $('#AddImageModal').modal('show');
};

function closeAddImageModal() {
    $('#imagelink').val('');
    $('#imagefile').val('');
    $('#modalImagePreview').attr('src','');
    $('#uploadPreviewDiv').css('display', 'none');
    $('#btnCropGroup').css('display', 'none');
    $('#btnUploadImage').css('display', 'none');
    $('.custom-file-label').html('Upload Image (Maximum Size 2 MB)');
    $('#AddImageModal').modal('hide');
    window.cropped=false;
    if(window.cropper){ window.cropper.destroy();}
};

function addImageToBlogPostBody(URL){
    var data='<br/><img src="'+URL+'" alt="..." /><br/>';
    CKEDITOR.instances.article_ckeditor.insertHtml(data);
    var range =  CKEDITOR.instances.article_ckeditor.createRange();
    range.moveToPosition( range.root, CKEDITOR.POSITION_BEFORE_END );
    CKEDITOR.instances.article_ckeditor.getSelection().selectRanges( [ range ] );
}

function setImage(from,imgURL){
    if(from == "blogimage"){
        $("#upload-response").show().fadeOut(2500, function() {
            addImageToBlogPostBody(imgURL);
            closeAddImageModal();
        });
    } else {
        $("#upload-response").show().fadeOut(2500, function() {
            setCoverImage(imgURL);
            closeAddImageModal();
        });
    }
}

function setCoverImage(imgURL){
    $('#coverimageurl').val(imgURL);
    $('#coverPreview').attr('src', imgURL);
    $('#coverPreviewDiv').css('display', 'block');
}
