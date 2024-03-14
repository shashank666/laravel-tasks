/* (1) Variables & Constants
   (2) Sidebar functions
   (3) General Funcitons
   (4) Auth functions
   (5) Post functions (Write,Like,Bookmark,Report,Delete,Category Follow,User Follow,Comment)
   (6) Gif functions
   (7) Opinion functions
   (8) Notification funcitons
 */

 /*---------------------(1) Variables & Constants----------------------*/

const verification=[
    {
         "title":"Verify your Email",
         "text":"<p>you haven't verified your email address yet. please verify your email address.</p>",
         "buttons":'<div class="text-center"><button class="my-1 btn btn-warning" data-dismiss="modal" onclick="openVerifyEmailModal();">Verify Email Address</button></div>'
     },
     {
         "title":"Verify your Mobile",
         "text":"<p>you haven't verified your mobile number yet. please verify your mobile number.</p>",
         "buttons":'<div class="text-center"><button class="my-1 btn btn-warning"  data-dismiss="modal" onclick="openVerifyMobileModal();">Verify Mobile Number</button></div>',
     },
     {
        "title":"Verify your Email & Mobile",
        "text":"<p>you haven't verified your email address and mobile number yet. please verify your email address and mobile number.</p>",
        "buttons":'<div class="text-center"><button class="my-1 btn btn-warning" data-dismiss="modal" onclick="openVerifyEmailModal();">Verify Email Address</button><br/><button class="my-1 btn btn-warning"  data-dismiss="modal" onclick="openVerifyMobileModal();">Verify Mobile Number</button></div>'
     }
 ];

 const colors=['#f44336','#e91e63','#9c27b0','#5e35b1','#3f51b5','#2196f3','#03a9f4','#03a9f4','#00897b','#4caf50','#689f38','#9e9d24','#f57f17','#ff9800','#ff5722','#5d4037','#757575','#455a64'];

 /*---------------------(2) Sidebar functions------------------------*/
$(document).on('click', '.sidebar-toggle', function() {
    var sidebar = $('#sidebar');
    var overlay = $('.sidebar-overlay');

    sidebar.toggleClass('open');
    if ((sidebar.hasClass('sidebar-fixed-left') || sidebar.hasClass('sidebar-fixed-right')) && sidebar.hasClass('open')) {
        overlay.addClass('active');
    } else {
        overlay.removeClass('active');
    }
});

$(document).on('click', '.sidebar-overlay', function() {
    $(this).removeClass('active');
    $('#sidebar').removeClass('open');
});

/*---------------------  (3) General Funcitons ----------------------*/
function ShowMessageModal(title, message) {
    $('#message_modal_title').text(title);
    $('#message_modal_body').html("<p>" + message + "</p>");
    $('#message_modal').modal('show');
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}

/*------------------------ (4) Auth functions ------------------------------*/

function openVerifyEmailModal() { $('#verifyEmailModal').modal('show'); };
function closeVerifyEmailModal() { $('#verifyEmailModal').modal('hide'); };

function openVerifyMobileModal() { $('#verifyMobileModal').modal('show'); };
function closeVerifyMobileModal() { $('#verifyMobileModal').modal('hide'); };

function openVerifyOTPModal() { $('#verifyOTPModal').modal('show'); };
function closeVerifyOTPModal() { $('#verifyOTPModal').modal('hide');};

function openRegisterModal() { $('#registerModal').modal('show'); };
function closeRegisterModal() { $('#registerModal').modal('hide');};

function openLoginModal() { $('#forgotPasswordModal').modal('show'); };
function closeLoginModal() { $('#forgotPasswordModal').modal('hide');};

function openForgotPasswordModal() { $('#forgotPasswordModal').modal('show'); };
function closeForgotPasswordModal() { $('#forgotPasswordModal').modal('hide'); };


function openVerifyEmailMobileModal(title,text,buttons){
    $('#verifyEMTitle').text(title);
    $('#verifyEmailMobileModalBody').empty();
    $('#verifyEmailMobileModalBody').append(text);
    $('#verifyEmailMobileModal').modal('show');
}

function closeVerifyEmailMobileModal(){ $('#verifyEmailMobileModal').modal('hide');}


function ShowLoginError(errors) {
    $('#loginModal').modal('show');
    $(".login_response").css("background-color", '#f2dede');
    $(".login_response").css("color", '#a94442');
    $(".login_response").css("visibility", 'visible');
    $(".login_response").show();
    $(".login_response").html(errors);
    $(".login_response").fadeOut(2500);
};

function registerUser(form,url) {
    $.ajax({
        type: 'POST',
        url: url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: $(form).serialize(),
        dataType: 'json',
        success: function(data) {

            if (data.status == "error") {
                errorsHtml = '';
                $.each(data.errors, function(key, value) {
                    errorsHtml = errorsHtml + value[0] + '<br/>';
                });
                $(".login_response").css("background-color", '#f2dede');
                $(".login_response").css("color", '#a94442');
                $(".login_response").css("visibility", 'visible');
                $(".login_response").show();
                $(".login_response").html(errorsHtml);
                $(".login_response").fadeOut(2500);
            } else {
                $(".login_response").css("background-color", '#dff0d8');
                $(".login_response").css("color", '#3c763d');
                $(".login_response").css("visibility", 'visible');
                $(".login_response").show();
                $(".login_response").html(data.message);
                if(cmv==1){
                    $('#userid').val(data.user_id);
                    $(".login_response").fadeOut(1500, function() {
                        $('#verify-otp-form').attr('action','/register');
                        $('#resend-otp-form').attr('action','/resendOTP');
                        closeRegisterModal();
                        openVerifyOTPModal();
                    });
                }else{
                    $(".login_response").fadeOut(1500, function() {
                        window.location.reload();
                    });
                }
            }
        },
        error: function(data) {
            $(".login_response").css("background-color", '#d9edf7');
            $(".login_response").css("color", '#31708f');
            $(".login_response").css("visibility", 'visible');
            $(".login_response").show();
            $(".login_response").html('Oops !! Something Went Wrong , Please Try Again Later.');
            $(".login_response").fadeOut(2500);
        }
    });
};

$(document).on('submit','#verify-otp-form',function(e){
    e.preventDefault();
    var url=$('#verify-otp-form').attr('action');
    $('#btnVerifyOTP').attr('disabled','disabled');
    $('#btnResendOTP').attr('disabled','disabled');

    $.ajax({
        url:url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type:'POST',
        dataType:'json',
        data:{otp:$('#otp').val(),userid:$('#userid').val()},
        success:function(data){
            $('#btnVerifyOTP').removeAttr('disabled');
            $('#btnResendOTP').removeAttr('disabled');

            if (data.status == "error") {
                errorsHtml = '';
                $.each(data.errors, function(key, value) {
                    errorsHtml = errorsHtml + value[0] + '<br/>';
                });
                $(".otp_response").css("background-color", '#f2dede');
                $(".otp_response").css("color", '#a94442');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(errorsHtml);
                $(".otp_response").fadeOut(2500);
            }

            if(data.status=='success') {
                $('#userid').val(data.user_id);
                $(".otp_response").css("background-color", '#dff0d8');
                $(".otp_response").css("color", '#3c763d');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(data.message);
                $(".otp_response").fadeOut(1500, function() {
                    closeVerifyOTPModal();
                    window.location.reload();
                });
            }
        },
        error:function(response){
            $('#btnVerifyOTP').removeAttr('disabled');
            $('#btnResendOTP').removeAttr('disabled');

            $(".otp_response").css("background-color", '#d9edf7');
            $(".otp_response").css("color", '#31708f');
            $(".otp_response").css("visibility", 'visible');
            $(".otp_response").show();
            $(".otp_response").html('Oops !! Something Went Wrong , Please Try Again Later.');
            $(".otp_response").fadeOut(2500);
        }
    });
});


$(document).on('submit','#resend-otp-form',function(e){
    e.preventDefault();
    var url=$('#resend-otp-form').attr('action');
    $('#btnVerifyOTP').attr('disabled','disabled');
    $('#btnResendOTP').attr('disabled','disabled');
    $('#otp').val('');
    $.ajax({
        url:url,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type:'POST',
        dataType:'json',
        data:{userid:$('#userid').val()},
        success:function(data){
            $('#btnVerifyOTP').removeAttr('disabled');
            $('#btnResendOTP').removeAttr('disabled');

            if (data.status == "error") {
                errorsHtml = '';
                $.each(data.errors, function(key, value) {
                    errorsHtml = errorsHtml + value[0] + '<br/>';
                });
                $(".otp_response").css("background-color", '#f2dede');
                $(".otp_response").css("color", '#a94442');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(errorsHtml);
                $(".otp_response").fadeOut(2500);
            } else {
                $(".otp_response").css("background-color", '#dff0d8');
                $(".otp_response").css("color", '#3c763d');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(data.message);
                $(".otp_response").fadeOut(2500);
            }
        },
        error:function(response){
            $('#btnVerifyOTP').removeAttr('disabled');
            $('#btnResendOTP').removeAttr('disabled');

            $(".otp_response").css("background-color", '#d9edf7');
            $(".otp_response").css("color", '#31708f');
            $(".otp_response").css("visibility", 'visible');
            $(".otp_response").show();
            $(".otp_response").html('Oops !! Something Went Wrong , Please Try Again Later.');
            $(".otp_response").fadeOut(2500);
        }
    });
});

function sendOTP(event){
    event.preventDefault();
    $('#sendMobileOTP').attr('disabled','disabled');
    $.ajax({
        url:'/me/add/mobile',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        dataType:'json',
        data:{mobile:$('#add_mobile').val()},
        success:function(data){
            $('#sendMobileOTP').removeAttr('disabled');
            if (data.status == "error") {
                errorsHtml = '';
                $.each(data.errors, function(key, value) {
                    errorsHtml = errorsHtml + value[0] + '<br/>';
                });
                $(".otp_response").css("background-color", '#f2dede');
                $(".otp_response").css("color", '#a94442');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(errorsHtml);
                $(".otp_response").fadeOut(2500);
            } else {
                $('#add_mobile').val('');
                $(".otp_response").css("background-color", '#dff0d8');
                $(".otp_response").css("color", '#3c763d');
                $(".otp_response").css("visibility", 'visible');
                $(".otp_response").show();
                $(".otp_response").html(data.message);
                $(".otp_response").fadeOut(2500,function(){
                    $('#verify-otp-form').attr('action','/me/verify/mobile');
                    $('#resend-otp-form').attr('action','/me/resend/otp');
                    closeVerifyMobileModal();
                    openVerifyOTPModal();
                });
            }
        },
        error:function(data){
            $('#sendMobileOTP').removeAttr('disabled');
            $(".otp_response").css("background-color", '#d9edf7');
            $(".otp_response").css("color", '#31708f');
            $(".otp_response").css("visibility", 'visible');
            $(".otp_response").show();
            $(".otp_response").html('Oops !! Something Went Wrong , Please Try Again Later.');
            $(".otp_response").fadeOut(2500);
        }
    });
}

function submitRegisterForm() {
    if(cmv==1){
        var url='/create_account';
    }else{
        var url='/register'
    }
    registerUser('#registerForm',url);
};

function sendResetPasswordLink() {
    $('#btnSendEmail').attr('disabled', true);
    var pattern = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
    if (pattern.test($('#forgot-email').val())) {
        $.ajax({
            type: 'POST',
            url: '/password/email',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: $('#sendEmailForm').serialize(),
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.error == 'false') {
                    showEmailResponse('#dff0d8', '#3c763d', data.msg, 7500);
                    $('#sendEmailForm')[0].reset();
                    $('#btnSendEmail').attr('disabled', false);
                } else {
                    showEmailResponse('#f2dede', '#a94442', data.msg, 7500);
                    $('#btnSendEmail').attr('disabled', false);
                }
            },
            error: function(data) {
                showEmailResponse('#d9edf7', '#31708f', 'Oops !! Something Went Wrong ,Please Try Again Later.', 2500);
                $('#sendEmailForm')[0].reset();
                $('#btnSendEmail').attr('disabled', false);
            }
        });
    } else {
        $('#btnSendEmail').attr('disabled', false);
        showEmailResponse('#f2dede', '#a94442', 'Please Enter Valid Email', 5000);
    }
};

function showEmailResponse(bkgcolor, color, msg, time) {
    $(".response").css("background-color", bkgcolor);
    $(".response").css("color", color);
    $(".response").css("visibility", 'visible');
    $(".response").show();
    $(".response").html(msg);
    $(".response").fadeOut(time);
};

$(document).on('change', '#agree', function() {
    if ($(this).is(':checked')) { $('#btnRegister').removeAttr('disabled'); } else { $('#btnRegister').attr('disabled', 'disabled'); }
});

/*----------------------- (5) Post functions ---------------------------*/

/*--5.1 Post Likes functions------------------------------------------------------*/
$(document).on('click', '.like_post', function() {
    var postdivid = $(this).attr('id');
    var postid = parseInt(postdivid.slice(9));
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                  openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
             }else{
                callPostLikeAjax(postid, postdivid, 'inline');
             }
         }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
             openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                callPostLikeAjax(postid, postdivid, 'inline');
             }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
             openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
             openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
             openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                callPostLikeAjax(postid, postdivid, 'inline');
            }
        }
    }
});

function callPostLikeAjax(postid, postdivid, display) {

    $.ajax({
        url: '/me/manage_likes',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { postid: postid },
        dataType: 'json',
        success: function(response) {
            if (response.status == "liked") {
                $('.likepost_' + postid + '_off').css('display', 'none');
                $('.likepost_' + postid + '_on').css('display', display);
            }else{
                $('.likepost_' + postid + '_on').css('display', 'none');
                $('.likepost_' + postid + '_off').css('display', display);
            }
            $('.likes_count_' + postid).attr('data-count', response.count)
            $('.likes_count_' + postid).text("" + response.count);
        },
        error: function(response) {}
    });
}


$(document).on('click', '.btn_post_likes', function() {
    $('.post_likes').empty();
    $('#likesModal').modal('show');
    $('.loader').css('display', 'block');
    $.ajax({
        url: $(this).attr('data-posturl') + '/likes',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        dataType: 'text',
        success: function(response) {
            $('.loader').css('display', 'none');
            $('.post_likes').append(response);
        },
        error: function() {
            $('.loader').css('display', 'none');
        }
    });
});

$(document).on('click', '.loadmore_likes', function() {
    $(this).text('Loading...');
    $(this).attr('disabled', 'disabled');
    var nextpage = $(this).attr('data-nextpage');
    $.ajax({
        url: $('.btn_post_likes').attr('data-posturl') + '/likes?page=' + nextpage,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        dataType: 'text',
        success: function(response) {
            $('.loadmore_likes').remove();
            $('.post_likes').append(response);
        },
        error: function() {
            $(this).text('Load More');
            $(this).removeAttr('disabled');
        }
    });
});

/*--5.2 Post Bookmark functions------------------------------------------------------*/

$(document).on('click', '.bookmark', function() {
    var bookmarkid = $(this).attr('id');
    var postid = parseInt(bookmarkid.slice(9));
    callBookmarkAjax(postid, bookmarkid, 'inline');
});

function callBookmarkAjax(postid, bookmarkid, display) {
    $.ajax({
        url: '/me/manage_bookmark',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { postid: postid },
        dataType: 'json',
        success: function(response) {
            if (response.status == "added") {
                $('.bookmark_' + postid + '_off').css('display', 'none');
                $('.bookmark_' + postid + '_on').css('display', display);
            }
            if (response.status == "removed") {
                $('.bookmark_' + postid + '_on').css('display', 'none');
                $('.bookmark_' + postid + '_off').css('display', display);
            }
        },
        error: function(response) {}
    });
}

/*--5.3 Post Report function------------------------------------------------------*/

$(document).on('click', '.report-button', function() {
    var btnid = $(this).attr('id');
    var postid = btnid.slice(7);
    $('#reportpost').val(postid);
    $('#reportModal').modal('show');
});

/*--5.4 Post Category follow functions------------------------------------------------------*/

$(document).on('click', '.cf_btn', function(e) {
    e.preventDefault();
	e.stopPropagation();
    buttonid = $(this).attr('id');
    categoryid = buttonid.slice(3);
    manageCategoryFollow(categoryid, buttonid);
});

$(document).on('click', '.cu_btn', function(e) {
    e.preventDefault();
	e.stopPropagation();
    buttonid = $(this).attr('id');
    categoryid = buttonid.slice(3);
    manageCategoryFollow(categoryid, buttonid);
});

function manageCategoryFollow(categoryid, buttonid) {
    $.ajax({
        url: '/me/manage_category_follow',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { categoryid: categoryid },
        dataType: 'json',
        success: function(response) {
            if (response.status == "unfollowed") {
                $('#' + buttonid).css('display', 'none');
                $('#cf_' + categoryid).css('display', 'block');
            }
            if (response.status == "followed") {
                $('#' + buttonid).css('display', 'none');
                $('#cu_' + categoryid).css('display', 'block');
            }
        },
        error: function(response) {}
    });
}

/*--5.5 Post User follow functions------------------------------------------------------*/
$(document).on('click','.followbtn',function(){
    if(au==0){
        openLoginModal();
    }else{
        userid=$(this).attr('data-userid');
        button='.followbtn_'+userid;
        $(button).attr('disabled','disabled');
        manageUserFollow(userid,button);
    }
});

$(document).on('click','.followingbtn',function(){
    if(au==0){
        openLoginModal();
    }else{
        userid=$(this).attr('data-userid');
        button='.followingbtn_'+userid;
        manageUserFollow(userid,button);
    }
});


function manageUserFollow(userid,button){
    $.ajax({
        url:'/me/manage_follow',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{userid:userid},
        dataType:'json',
        success:function(response){
            $(button).removeAttr('disabled');
            if(response.status=="unfollowed"){
                $('.followingbtn_'+userid).css('display','none');
                $('.followbtn_'+userid).css('display','inline');
            }else{
                $('.followbtn_'+userid).css('display','none');
                $('.followingbtn_'+userid).css('display','inline');
            }
        },error:function(response){
            $(button).removeAttr('disabled');
        }
    });
}

/*--5.6 Post Write Link function------------------------------------------------------*/

$(document).on('click','.write_opinion_link',function(event){
    event.preventDefault();
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==0 && cmv==0){
            window.location.href="/opinion/write";
        }

        if(au==1 && cev==1 && cmv==0){
           if(ev==0){
                 openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                window.location.href="/opinion/write";
            }
        }

       if(au==1 && cev==0 && cmv==1){
           if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
           }else{
             window.location.href="/opinion/write";
           }
       }

       if(au==1 && cev==1 &&  cmv==1){
           if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
           }
           if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
           }
           if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
           }
           if(mv==1 && ev==1){
            window.location.href="/opinion/write";
           }
       }
    }
})

/*--5.7 Post Delete functions------------------------------------------------------*/

$(document).on('click', '.btn_delete_post', function() {
    var deleteid = $(this).attr('id').slice(7);
    var postid = $(this).attr('name').slice(7);
    $('#del_id').val(deleteid);
    $('#op_id').val(postid);
    $('#deleteOpinionModal').modal('show');
});


$(document).on('click', '.finaldelete', function() {
    var postid = $('#op_id').val();
    var deleteid = $('#del_id').val();
    $.ajax({
        url: "/opinion/delete",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { deleteid: deleteid, _method: 'DELETE' },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                $('#post-' + postid).remove();
                $('#deleteOpinionModal').modal('hide');
            }
        },
        error: function(response) {
            $('#deleteOpinionModal').modal('hide');
        }
    });
});

/*--5.8 Post Write(Create,Update) Page functions------------------------------------------------------*/

$(document).on('keyup keypress', '#create_post_form', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
        e.preventDefault();
        return false;
    }
});

$(document).on('change', "#coverimage", function() {
    readURL(this, '#cover_preview');
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


$(document).on('shown.bs.tab', '#cover-image-tab a[data-toggle="tab"]', function(e) {
    var target = $(e.target).attr("href");
    if (target == '#tab_upload_image') {
        $('#imagelink').val('');
    } else {
        $('#modal_coverpreview').attr('src', '');
        $('#modal_coverpreview').css('display', 'none');
        $('#imagefile').val('');
        $('#btnUploadImage').css('display', 'none');
    }
});


$(document).on('change', "#imagefile", function() {
    if ($('#imagefile')[0].files[0] !== undefined) {
        var file_size = $('#imagefile')[0].files[0].size;
        if (file_size > 2097152) {
            $('#upload-response').removeClass();
            $('#upload-response').addClass('alert alert-danger');
            $('#upload-response').html('Filesize is more than 2 MB not allowed');
            $("#upload-response").fadeOut(4500, function() {
                $('#imagefile').val('');
                $('#modal_coverpreview').css('display', 'none');
                $('#modal_coverpreview').attr('src', '');
            });
        } else {
            readURL(this, '#modal_coverpreview');
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
        url:'/file/upload/POST_COVER',
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
                $('#coverimageurl').val(imgURL);
                $('#imagefile').val('');
                $('#modal_coverpreview').css('display', 'none');
                $('#modal_coverpreview').attr('src', '');
                $('#btnUploadImage').removeAttr('disabled');
                $('#upload-response').removeClass();
                $('#upload-response').addClass('alert alert-success');
                $('#upload-response').html(response.message);
                $("#upload-response").show().fadeOut(2500, function() {
                    $('#AddImageModal').modal('hide');
                    $('#coverPreview').attr('src', imgURL);
                    $('#coverPreviewDiv').css('display', 'block');
                });
            }else{
                $('#coverimageurl').val('');
                $('#imagefile').val('');
                $('#modal_coverpreview').css('display', 'none');
                $('#modal_coverpreview').attr('src', '');
                $('#btnUploadImage').removeAttr('disabled');
                $('#upload-response').removeClass();
                $('#upload-response').addClass('alert alert-error');
                $('#upload-response').html('Failed to upload image , Please try again later');
                $("#upload-response").show().fadeOut(2500);
            }
        },
        error:function(err){
            $('#coverimageurl').val('');
            $('#imagefile').val('');
            $('#modal_coverpreview').css('display', 'none');
            $('#modal_coverpreview').attr('src', '');
            $('#btnUploadImage').removeAttr('disabled');
            $('#upload-response').removeClass();
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


function CheckImageLink() {
    if ($('#imagelink').val().length > 0) {
        var URL = $('#imagelink').val();
        var extension = URL.substr((URL.lastIndexOf('.') + 1));
        if (extension == 'jpg' || extension == 'jpeg' || extension == 'png' || extension == 'gif') {
            $('#coverPreviewDiv').css('display', 'block');
            $('#coverPreview').attr('src', URL);
            $('#coverimageurl').val(URL);
            $('#AddImageModal').modal('hide');
        } else {
            $('.cover-error').text('Please Enter Valid Image Url');
            $('#imagelink').val('');
            $('.cover-error').show().fadeOut(3500);
        }
    } else {
        $('.cover-error').text('Please Enter Valid Image Url');
        $('#imagelink').val('');
        $('.cover-error').show().fadeOut(3500);
    }
}

function openAddImageModal() {
    $('#AddImageModal').modal('show');
};


    $(document).on('click', '#btnTweetLink', function() {
        var tweetLink = $('#tweetLink').val();
        var pattern = new RegExp('https://twitter.com/(.+?)/status/(.+?)', 'g')
        if((tweetLink != undefined || tweetLink != '') && tweetLink.match(pattern)){
            $.ajax({
                url: "https://api.twitter.com/1/statuses/oembed.json?url=" + tweetLink + "&hide_media=false",
                dataType: "jsonp",
                async: false,
                success: function(data) {
                    appendSocialPost(data.html);
                    $('#tweetLink').val('');
                    $('#AddTwitterPost').modal('hide');
                }
            });
        }else{
            $('.twt-error').text('Please Enter Valid Tweet Link');
            $('#tweetLink').val('');
            $('.twt-error').show().fadeOut(5000);
        }
    });

    $(document).on('click','#btnTweetEmbeded',function(){
        tweetEmbeded=$('#tweetEmbeded').val().trim();
        if((tweetEmbeded != undefined || tweetEmbeded != '') &&  tweetEmbeded.indexOf("twitter-tweet")>=0) {
            appendSocialPost(tweetEmbeded);
            $('#tweetEmbeded').val('');
            $('#AddTwitterPost').modal('hide');
        }else {
            $('.twt-error').text('Please Enter Valid Embed Tweet');
            $('#tweetEmbeded').val('');
            $('.twt-error').show().fadeOut(5000);
        }
    });

    $(document).on('click', '#btnInstagramLink', function() {
        var instagramLink = $('#instagramLink').val();
        var pattern = new RegExp('https://www.instagram.com/p/(.+?)', 'g');
        if((instagramLink != undefined || instagramLink != '') &&  instagramLink.match(pattern)){
            $.ajax({
                url: "https://api.instagram.com/oembed/?maxwidth=730&url=" + instagramLink,
                dataType: "jsonp",
                async: false,
                success: function(data) {
                    appendSocialPost(data.html);
                    window.instgrm.Embeds.process();
                    $('#instagramLink').val('');
                    $('#AddInstagramPost').modal('hide');
                }
            });
        }else{
            $('.it-error').text('Please Enter Valid Instagram Link');
            $('#instagramLink').val('');
            $('.it-error').show().fadeOut(5000);
        }
    });


    $(document).on('click','#btnInstagramEmbeded',function(){
        instagramEmbeded=$('#instagramEmbeded').val().trim();
        if((instagramEmbeded != undefined || instagramEmbeded != '') &&  instagramEmbeded.indexOf("instagram-media")>=0) {
            appendSocialPost(instagramEmbeded);
            window.instgrm.Embeds.process();
            $('#instagramEmbeded').val('');
            $('#AddInstagramPost').modal('hide');
        }else{
            $('.it-error').text('Please Enter Valid Instagram Embed Code');
            $('#instagramEmbeded').val('');
            $('.it-error').show().fadeOut(5000);
        }
    });



    $(document).on('click','#btnYoutubeLink',function(){
        var url=$('#youtubeLink').val();
       if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                var videoID=getParameterByName('v',url);
                var embedURL="https://www.youtube.com/embed/"+videoID;
                var videoiFrame='<div class="col-12"><iframe width="496" height="280" src="'+embedURL+'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
                appendSocialPost(videoiFrame);
                $('#youtubeLink').val('');
                $('#AddYoutubeVideo').modal('hide');
            }
            else {
                $('.yt-error').text('Please Enter Valid Youtube Video Link');
                $('#youtubeLink').val('');
                $('.yt-error').show().fadeOut(5000);
            }
        }
    });


    $(document).on('click','#btnYoutubeEmbeded',function(){
        var embedCode=$('#youtubeEmbeded').val().trim();
        if((embedCode != undefined || embedCode != '') && embedCode.indexOf("https://www.youtube.com/embed/") >= 0)
        {
            appendSocialPost(embedCode);
            $('#youtubeEmbeded').val('');
            $('#AddYoutubeVideo').modal('hide');
        }else{
            $('.yt-error').text('Please Enter Valid Youtube Emded Video Code');
            $('#youtubeEmbeded').val('');
            $('.yt-error').show().fadeOut(5000);
        }
    });

    $(document).on('click', '#btnOpinedEmbeded', function() {
        var opinedEmbeded = $('#opinedEmbeded').val().trim();
        var URLpattern = new RegExp('https://www.weopined.com/(.+?)/opinion/(.+?)', 'g');
        var iframePattern=new RegExp('(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))','g');

        if((opinedEmbeded != undefined || opinedEmbeded != '') &&  opinedEmbeded.match(iframePattern) &&  opinedEmbeded.match(URLpattern)){
                appendSocialPost(opinedEmbeded);
                $('#opinedEmbeded').val('');
                $('#AddOpinedOpinion').modal('hide');
        }else{
            $('.op-error').text('Please Enter Valid Embed Opinion');
            $('#opinedEmbeded').val('');
            $('.op-error').show().fadeOut(5000);
        }
    });



    function appendSocialPost(data){
        var data='<br/>'+data+'<br/>';
        CKEDITOR.instances.article_ckeditor.insertHtml(data);
        var range =  CKEDITOR.instances.article_ckeditor.createRange();
        range.moveToPosition( range.root, CKEDITOR.POSITION_BEFORE_END );
        CKEDITOR.instances.article_ckeditor.getSelection().selectRanges( [ range ] );
    }

/*--5.9 Post Comments functions------------------------------------------------------*/

$(document).on('click', '.deleteComment', function() {
    var deleteid = $(this).attr('id');
    var comment_div_id = deleteid.slice(7);
    var comment_id = parseInt(comment_div_id.slice(8));
    var token = $("input[name=_token]").val();
    var postid = parseInt($("#postid").val());
    $.ajax({
        url: '/delete_comment',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'post',
        data: { _token: token, comment_id: comment_id, post_id: postid },
        dataType: 'json',
        success: function(response) {
            console.log(response)
            if (response.status == 'success') {
                $('#' + comment_div_id).remove();
            }
        },
        error: function(err) {}
    });
});


function displayComments(postID, userID, commentsArray, appendDivClass, marginLeft) {
    var appendVariable = '';
    for (var i = 0; i < commentsArray.length; i++) {
        var commentId = commentsArray[i].id;
        var commentBody = commentsArray[i].comment;
        var commentUpdateAt = commentsArray[i].updated_at;
        var commentUser = commentsArray[i].user;
        var coomentPostId = commentsArray[i].post_id;

        comment = '<div class="card mb-3" id="comment-' + commentId + '">' +
            '<div class="card-body">' +
            '<div class="row media">' +
            '<div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
            '<a href="/@' + commentUser.username + '/' + commentUser.unique_id + '" style="text-decoration:none;"><img class="rounded-circle" src="' + commentUser.image + '" height="64" width="64"/></a>' +
            '</div>' +
            '<div class="col-lg-7 col-md-7 col-sm-12 col-12">' +
            '<div class="media-body ml-3 mr-3 text-md-left text-sm-center text-center">' +
            '<a href="/@' + commentUser.username + '/' + commentUser.unique_id + '" style="text-decoration:none;color:#212121;"><h5 class="mt-0">' + commentUser.name + '</h5></a>' +
            '<p id="ctext-' + commentId + '">' + commentBody + '</p>';

        editComment = '';
        if (userID > 0 && userID == commentUser.id) {
            editComment = '<textarea id="edit-comment-' + commentId + '" class="mt-2 form-control" rows="2" name="comment" placeholder="add your comment ..." minlength="6" required="true" autocomplete="off"  style="display:none" autofocus>' + commentBody + '</textarea>';
        }

        commentTime = '</div></div>' +
            '<div class="col-lg-4 col-md-4 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
            '<div class="float-xl-right float-lg-right float-md-right float-sm-none float-none">' +
            '<p class="text-muted" style="font-size:14px;">' + commentUpdateAt + '</p>';

        buttons = '';
        if (userID > 0) {
            if (userID == commentUser.id) {

                buttons = '<button type="button" class="mr-2 btn btn-sm btn-outline-primary editComment" id="btn-ec-' + commentId + '" name="ec-' + postID + '"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>' +
                    '<button type="button" style="display:none" class="mr-2 btn btn-sm btn-outline-success updateComment" id="btn-uc-' + commentId + '" name="uc-' + postID + '"><i class="fas fa-check mr-2"></i>Update</button>' +
                    '<button type="button" style="display:none" class="mr-2 btn btn-sm btn-outline-warning cancleEdit" id="btn-cancleEdit-' + commentId + '"><i class="fas fa-times mr-2"></i>Cancle</button>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger deleteComment" id="delete-comment-' + commentId + '"><i class="far fa-trash-alt mr-2"></i>Delete</button>';
            } else {
                buttons = '<button type="button" class="float-right btn btn-sm btn-outline-success replyComment" id="reply-comment-' + commentId + '" data-toggle="collapse" data-target="#reply-form-' + commentId + '" aria-expanded="false" aria-controls="reply-form-' + commentId + '"><i class="fas fa-reply mr-2"></i>Reply</button>';
            }
        } else {
            buttons = '<button type="button" class="float-right btn btn-sm btn-outline-success" onclick="openLoginModal();"><i class="fas fa-reply mr-2"></i>Reply</button>';
        }

        end = '</div></div>' +
            '</div>' +
            '</div>' +
            '</div>';

        replyForm =
            '<div class="mb-3">' +
            '<div class="collapse" id="reply-form-' + commentId + '">' +
            '<div class="card card-body bg-light mb-3">' +
            '<form class="reply_form" id="reply_form_' + commentId + '" action="/add_comment" method="post">' +
            '<input type="hidden" name="parent_id" value="' + commentId + '" />' +
            '<input type="hidden" name="post_id" value="' + coomentPostId + '" />' +
            '<div class="form-group row">' +
            '<div class="col-sm-10">' +
            '<textarea class="form-control comment_textarea" rows="2" name="comment" minlength="6" required placeholder="Reply to ' + commentUser.name + '"></textarea>' +
            '</div>' +
            '<div class="col-sm-2">' +
            '<button id="submit_reply_' + commentId + '" type="button" class="mt-2 float-right btn btn-primary submitReply">Post <i class="fas fa-paper-plane ml-2"></i></button>' +
            '</div>' +
            '</div>' +
            '</form>' +
            '</div>' +
            '</div>';
        '</div>';

        child = '<div class="ml-xl-5 ml-lg-5 ml-md-5 ml-sm-0 ml-0" id="child-of-' + commentId + '"></div>';

        commentCard = comment + editComment + commentTime + buttons + end + replyForm + child;
        $(appendDivClass).append(commentCard);
        if (commentsArray[i].replies.length > 0) {
            displayComments(postID, userID, commentsArray[i].replies, '#child-of-' + commentId, 'ml-5');
        }
    }
}

function loadComments(page) {
    var postid = parseInt($("#postid").val());
    if (page > 1) {
        $('#btnloadMore').text('Loading ...');
        $('#btnloadMore').addClass('disabled');
    }
    $.ajax({
        url: '/get_parent_comments?page=' + page,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType: 'json',
        data: {  postid: postid },
        success: function(response) {
            if (response.comments.next_page_url == null) {
                $('#btnloadMore').attr('data-nextpage', '');
                $('#btnloadMore').css('display', 'none');
            } else {
                var nextP = response.comments.next_page_url.split("?page=")[1];
                $('#btnloadMore').attr('data-nextpage', nextP);
                $('#btnloadMore').removeClass('disabled');
                $('#btnloadMore').text('Load More Comments');
                $('#btnloadMore').css('display', 'block');
            }
            if (response.comments.data.length > 0) {
                displayComments(postid, response.userid, response.comments.data, '.comments-div', 'ml-0')
            }
        },
        error: function(response) {}
    });
}


/*
$(document).ready(function() {
    loadComments(1);
}); */

$(document).on('click', '#btnloadMore', function() {
    loadComments($(this).attr('data-nextpage'));
});

$(document).on('click', '.editComment', function() {
    var comment_id = parseInt($(this).attr('id').slice(7));
    $('#ctext-' + comment_id).css('display', 'none');
    $('#edit-comment-' + comment_id).css('display', 'block');
    $('#btn-uc-' + comment_id).css('display', 'inline');
    $('#btn-cancleEdit-' + comment_id).css('display', 'inline');
    $(this).css('display', 'none');
    $('#delete-comment-' + comment_id).css('display', 'none');
});

$(document).on('click', '.cancleEdit', function() {
    var comment_id = parseInt($(this).attr('id').slice(15));
    $(this).css('display', 'none');
    $('#edit-comment-' + comment_id).css('display', 'none');
    $('#btn-uc-' + comment_id).css('display', 'none');
    $('#ctext-' + comment_id).css('display', 'block');
    $('#btn-ec-' + comment_id).css('display', 'inline');
    $('#delete-comment-' + comment_id).css('display', 'inline');
});


$(document).on('click', '.updateComment', function() {
    var comment_id = parseInt($(this).attr('id').slice(7));
    var newComment = $('#edit-comment-' + comment_id).val();
    var post_id = parseInt($(this).attr('name').slice(3))

    $.ajax({
        url: '/update_comment',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'post',
        data: { comment_id: comment_id, comment: newComment, post_id: post_id },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                $('#ctext-' + comment_id).text(newComment);
                $('#edit-comment-' + comment_id).css('display', 'none');
                $('#ctext-' + comment_id).css('display', 'block');
                $('#btn-cancleEdit-' + comment_id).css('display', 'none');
                $('#btn-uc-' + comment_id).css('display', 'none');
                $('#btn-ec-' + comment_id).css('display', 'inline');
                $('#delete-comment-' + comment_id).css('display', 'inline');
            }
        },
        error: function(err) {}
    });
});


function appendComment(response, appendDiv, postID) {

    var commentId = response.comment.id;
    var commentBody = response.comment.comment;
    var commentUpdateAt = response.comment.updated_at;
    var commentUser = response.comment.user;

    commentCard = '<div class="card mb-3" id="comment-' + commentId + '">' +
        '<div class="card-body">' +
        '<div class="row media">' +
        '<div class="col-xl-1 col-lg-1 col-md-1 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
        '<a href="/@' + commentUser.username + '/' + commentUser.unique_id + '" style="text-decoration:none;"><img class="rounded-circle" src="' + commentUser.image + '" height="64" width="64"/></a>' +
        '</div>' +
        '<div class="col-lg-7 col-md-7 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
        '<div class="media-body ml-3 mr-3">' +
        '<a href="/@' + commentUser.username + '/' + commentUser.unique_id + '" style="text-decoration:none;color:#212121;"><h5 class="mt-0">' + commentUser.name + '</h5></a>' +
        '<p id="ctext-' + commentId + '">' + commentBody + '</p>' +
        '<textarea id="edit-comment-' + commentId + '" class="mt-2 form-control" rows="2" name="comment" placeholder="add your comment ..." minlength="6" required="true" autocomplete="off"  style="display:none" autofocus>' + commentBody + '</textarea>' +
        '</div></div>' +
        '<div class="col-lg-4 col-md-4 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
        '<div class="float-xl-right float-lg-right float-md-right float-sm-none float-none">' +
        '<p  class="text-muted" style="font-size:14px;">' + commentUpdateAt + '</p>' +
        '<button type="button" class="mr-2 btn btn-sm btn-outline-primary editComment" id="btn-ec-' + commentId + '" name="ec-' + postID + '"><i class="fas fa-pencil-alt mr-2"></i>Edit</button>' +
        '<button type="button" style="display:none" class="mr-2 btn btn-sm btn-outline-success updateComment" id="btn-uc-' + commentId + '" name="uc-' + postID + '"><i class="fas fa-check mr-2"></i>Update</button>' +
        '<button type="button" style="display:none" class="mr-2 btn btn-sm btn-outline-warning cancleEdit" id="btn-cancleEdit-' + commentId + '"><i class="fas fa-times mr-2"></i>Cancle</button>' +
        '<button type="button" class="btn btn-sm btn-outline-danger deleteComment" id="delete-comment-' + commentId + '"><i class="far fa-trash-alt mr-2"></i>Delete</button>' +
        '</div></div>' +
        '</div>' +
        '</div>' +
        '</div>';
    $(appendDiv).append(commentCard);
}

function submitComment(Form, appendDiv) {
    $.ajax({
        url: '/add_comment',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        dataType: 'json',
        data: $(Form).serialize(),
        success: function(response) {
            if (response.status == 'success') {
                var postID = $('#comment_post_id').val();
                $(Form)[0].reset();
                appendComment(response, appendDiv, postID);
                $('.collapse').collapse('hide');
            }
        },
        error: function() {

        }
    });
}

$(document).on('submit', '#add_comment_form', function(e) {
    e.preventDefault();
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                submitComment('#add_comment_form', '.comments-div');
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                submitComment('#add_comment_form', '.comments-div');
            }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                submitComment('#add_comment_form', '.comments-div');
            }
        }
    }

});

$(document).on('click', '.submitReply', function() {
    var commentID = $(this).attr('id').slice(13);
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                submitComment('#reply_form_' + commentID, '#child-of-' + commentID);
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                submitComment('#reply_form_' + commentID, '#child-of-' + commentID);
            }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                submitComment('#reply_form_' + commentID, '#child-of-' + commentID);
            }
        }
    }
});


        $(document).on('click','.comment_textarea,.create_opinion',function(){
            if(au==0){openLoginModal();}
            else{
                if(au==1 && cev==1 && cmv==0){
                    if(ev==0){
                        openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
                    }
                }

                if(au==1 && cev==0 && cmv==1){
                    if(mv==0){
                    openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
                    }
                }
                if(au==1 && cev==1 &&  cmv==1){
                    if(mv==0 && ev==0){
                    openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
                    }
                    if(mv==1 && ev==0){
                    openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
                    }
                    if(mv==0 && ev==1){
                    openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
                    }
                }
            }
        });

/*----------------------------(6) Gif functions ------------------------------*/

var DivGifCategories="";
var DivGifImages="";

function openAddGIFModal(openedFrom){
    $('#AddGIFModal').attr('data-openfrom',openedFrom);
    $('#searchGIF').val('');
    if(DivGifCategories==""){
    $(".loader").css('display','block');
    $('#GIFcategories').empty();
    $('#AddGIFModal').modal("show");
    $.ajax({
    url:"https://api.tenor.com/v1/categories?key=2W4LRR4J3AWZ&anon_id=3a76e56901d740da9e59ffb22b988242&locale=en_US&Type=featured&safesearch=off",
    dataType:'json',
    type:'GET',
    success:function(response){
        $(".loader").css('display','none');
        if(response && response.tags && response.tags.length>0){
                for(var i=0;i<response.tags.length;i++){
                    //console.log(response.tags);
                    var colorHEX=colors[Math.floor(Math.random() * colors.length)];
                    var GIFcategory='<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">'+
                                            '<div class="card mb-2 gifCategoryCard" id="'+response.tags[i].path+'" style="background-color:'+colorHEX+'">'+
                                                    '<h5 class="text-center py-1" style="color:#fff;textTransform:capitalize">'+response.tags[i].searchterm+'</h5>'+
                                                    '<img class="card-img-bottom" src="'+response.tags[i].image+'" style="height:100px;">'+
                                            '</div>'+
                                    '</div>';
                    DivGifCategories=DivGifCategories+GIFcategory;
                }
                var appendHTML='<div class="row">'+DivGifCategories+'</div>';
                $('#GIFcategories').append(appendHTML);
            }
    },error:function(response){
        $(".loader").css('display','none');
    }
    });
    }else{
        $(".loader").css('display','none');
        $('#GIFimages').empty();
        $('#GIFimages').css('display','none');
        $('#loadMoreGIF').css('display','none');
        $('#GIFcategories').css('display','block');
        $('#AddGIFModal').modal("show");
    }
}

function closeGIFModal(){
    $('#AddGIFModal').modal("hide");
}

function getGIF(url,from){
    if(from=='loadmore'){
        $('#loadMoreGIF').attr('disabled','disabled');
        $('#loadMoreGIF').text('Loading...');
    }else{
        $(".loader").css('display','block');
    }

    $.ajax({
    url:url,
    dataType:'json',
    type:'GET',
    success:function(response){
        $(".loader").css('display','none');
        if(response && response.results && response.results.length>0){
            $('#loadMoreGIF').attr('data-url',url+response.next);
            $('#loadMoreGIF').attr('data-pos',response.next);
            if(response.next==0){  $('#loadMoreGIF').css('display','none');}
            else{  $('#loadMoreGIF').css('display','block'); }

            if(from=='loadmore'){
                $('#loadMoreGIF').removeAttr('disabled');
                $('#loadMoreGIF').text('Load More');
            }else{
                $(".loader").css('display','none');
                $('#GIFimages').empty();
            }
                $('#GIFcategories').css('display','none');
                DivGifImages='';
                $('#GIFimages').css('display','block');
                for(var i=0;i<response.results.length;i++){
                    var colorHEX=colors[Math.floor(Math.random() * colors.length)];
                    var GIFimages=  '<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">'+
                                        '<div id="'+response.results[i].media[0].gif.url+'" class="gifImageCard card mb-2" style="cursor:pointer;background-color:'+colorHEX+'">'+
                                            '<img class="card-img" src="'+response.results[i].media[0].tinygif.url+'" style="height:100px;">'+
                                        '</div>'+
                                    '</div>';
                    DivGifImages=DivGifImages+GIFimages;
                }
                $('#GIFimages').append('<div class="row">'+DivGifImages+'</div>');
        }else{
            $(".loader").css('display','none');
            $('#loadMoreGIF').removeAttr('disabled');
            $('#loadMoreGIF').text('Load More');
            $('#loadMoreGIF').css('display','none');
        }
    },error:function(response){
            if(from=='loadmore'){
                $('#loadMoreGIF').removeAttr('disabled');
                $('#loadMoreGIF').text('Load More');
            }else{
                $(".loader").css('display','none');
            }
    }
    });
}

var searchRequest = null;
$(document).on('keyup','#searchGIF',function(){
    var minlength = 2;
    value = $('#searchGIF').val();
    if (value.length >= minlength){
        if (searchRequest != null)
            searchRequest.abort();
            $(".loader").css('display','block');
            var searchURL='https://api.tenor.com/v1/search?q='+value+'&locale=en_US&safesearch=off&key=LIVDSRZULELA&limit=9&anon_id=3a76e56901d740da9e59ffb22b988242&media_filter=minimal&ar_range=all';
            searchRequest = $.ajax({
            type: "GET",
            url: searchURL,
            dataType: "json",
            success: function(response){
                $(".loader").css('display','none');
                if(response && response.results && response.results.length>0){
                    $('#GIFcategories').css('display','none');
                    $('#GIFimages').empty();
                    DivGifImages='';
                    $('#GIFimages').css('display','block');
                    for(var i=0;i<response.results.length;i++){
                            var colorHEX=colors[Math.floor(Math.random() * colors.length)];
                            var GIFimages=  '<div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-6">'+
                                                '<div id="'+response.results[i].media[0].gif.url+'" class="gifImageCard card mb-2" style="cursor:pointer;background-color:'+colorHEX+'">'+
                                                    '<img class="card-img" src="'+response.results[i].media[0].tinygif.url+'" style="height:100px;">'+
                                                '</div>'+
                                            '</div>';
                            DivGifImages=DivGifImages+GIFimages;
                    }
                    $('#GIFimages').append('<div class="row">'+DivGifImages+'</div>');
                    $('#loadMoreGIF').attr('data-url',searchURL+"&pos="+response.next);
                    $('#loadMoreGIF').attr('data-pos',response.next);
                    $('#loadMoreGIF').css('display','block');
                }
            },error:function(err){
                $(".loader").css('display','none');
            }
        });
    }else{

    }
});

$(document).on('click','#loadMoreGIF',function(){
    var searchURL=$(this).attr('data-url');
    var pos=$(this).attr('data-pos');
    searchURL=updateQueryStringParameter(searchURL,'limit',9);
    searchURL=updateQueryStringParameter(searchURL,'pos',pos);
    getGIF(searchURL,'loadmore');
});

$(document).on('click','.gifCategoryCard',function(){
var url=$(this).attr('id');
var searchURL=updateQueryStringParameter(url,'limit',18);
getGIF(searchURL,'category');
});

$(document).on('click','.gifImageCard',function(){
    var GIFimg=$(this).attr('id');
    var img='<div class="col-12"><div class="card mb-2"><img src="'+GIFimg+'" style="height:auto;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="btn btn-sm float-right text-white bg-danger py-1 px-2 rounded deleteGIF" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
    console.log('open from',$('#AddGIFModal').attr('data-openfrom'));
    if($('#AddGIFModal').attr('data-openfrom')=='comment'){
        $('#commentImage').val(GIFimg);
        $('#commentImagePreview').empty();
        $('#commentImagePreview').append(img);
        $('#commentImagePreview').css('display','block');
    }
    else if($('#AddGIFModal').attr('data-openfrom')=='writepost'){
        appendSocialPost('<img src="'+GIFimg+'" style="height:auto;" class="rounded">');
    }
    else{
        $('#type').val('GIF');
        $('#cover').val(GIFimg);
        $('#previewMedia').empty();
        $('#previewMedia').append(img);
        $('#previewMedia').css('display','block');
    }
    $('#AddGIFModal').modal('hide');
});

$(document).on('click','.deleteGIF',function(){
    if($('#AddGIFModal').attr('data-openfrom')=='comment'){
        $('#commentImage').val('');
        $('#commentImagePreview').empty();
        $('#commentImagePreview').css('display','none');
    }else{
        $('#previewMedia').empty();
        $('#previewMedia').css('display','none');
    }
});

/*-------------------- (7) Opinion functions ----------------------*/

var finalFiles = {};

$(document).on('change','#file',function(e){
    finalFiles = {};
     i = 0;
    var files = e.target.files; //FileList object
     if ($('#file')[0].files.length>3){
        ShowMessageModal('Maximum 3 Photoes Allowed','You are not allowed to add more than 3 photoes.');
    }else{
        if($('#type').val()=='GIF' || $('#type').val()=='YOUTUBE'){
            $('#previewMedia').empty();
        }
        $('#previewMedia').css('display','');
        $('#type').val('IMAGE');
        $.each(files, function(i, file) {
        finalFiles[i]=file;
        var reader = new FileReader();
        reader.onload = function (e) {
         var img='<div class="col-lg-4 col-sm-6 col-12"  id="file_'+ i +'"><div class="card mb-2"><img src="'+e.target.result+'" style="height:150px;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="deleteFile btn btn-sm float-right text-white bg-danger py-1 px-2 rounded" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
         $('#previewMedia').append(img);
        }
        reader.readAsDataURL(file);
        });
    }
});


$(document).on('click','.deleteFile',function()
{
 var container = $(this).parents().get(2);
 var c_id=container.id;
 var index = c_id.split('_')[1];
  container.remove();
  delete finalFiles[index];
  //console.log($('#file').prop("files"));
});

function openFileChooser(){
    if($('#previewMedia').children('div').length < 3){
        $('#file').click();
    }else{
       ShowMessageModal('Maximum 3 Photoes Allowed','You can add maximum 3 photoes.');
    }
}

function openAddYoutubeVideoModal(){
    $('#add_youtube_video').modal('show');
}

function AddYouTubeVideo(){
    var url=$('#youtube_url').val();
       if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                var videoID=getParameterByName('v',url);
                var embedURL="https://www.youtube.com/embed/"+videoID;
                var videoiFrame='<div class="col-12"><iframe width="496" height="280" src="'+embedURL+'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
                $('#type').val('YOUTUBE');
                $('#cover').val(videoID);
                $('#previewMedia').empty();
                $('#previewMedia').append(videoiFrame);
                $('#previewMedia').css('display','block');
                $('#add_youtube_video').modal('hide');
                $('#youtube_url').val('');
            }
            else {
                ShowMessageModal('Invalid URL','Please add valid youtube video URL.');
            }
        }
}

function openAddEmbedCodeModal(){
    $('#add_embed_code').modal('show');
}

function AddEmbedCode(){
    var embedCode=$('#embed-code').val();
    console.log(embedCode);
    var iframePattern=new RegExp('(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))','g');
    var scriptPattern=new RegExp('(?:<script[^>]*)(?:(?:\/>)|(?:>.*?<\/script>))','g');
    if(embedCode.length>0 && (iframePattern.test(embedCode) || scriptPattern.test(embedCode))){
        var appendDiv='<iframe id="preview-embed" width="100%" height="100%"  style="min-height:500px;width:100%;height:100%;" frameBorder="0" scrolling="yes"></iframe>'
        $('#type').val('EMBED');
        $('#cover').val(embedCode);
        $('#previewMedia').empty();
        $('#previewMedia').append(appendDiv);
        $("#preview-embed").ready(function() {
            iframeDoc = document.getElementById('preview-embed').contentDocument;
            iframeDoc.open();
            iframeDoc.write(embedCode);
            iframeDoc.close();
        });
        $('#previewMedia').css('display','block');
        $('#add_embed_code').modal('hide');
        $('#embed-code').val('');
    }else{
         ShowMessageModal('Invalid Embed Code','Please add valid embed code.');
    }
}

$(document).on('click','#btn_post',function(){
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                postOpinion();
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                postOpinion();
            }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                postOpinion();
            }
        }
    }
});

function postOpinion(){
    if($('#type').val()=='IMAGE'){
        var val="";
        var arr=[];
         $.each(finalFiles,function(i,file){
             arr.push(file.name);
         });
        var str=arr.join(",");
        $('#cover').val(str);
        $('#opinion_form').submit();
   }else{
      $('#opinion_form').submit();
   }
}

function getHashTags(inputText) {
    //var regex = /(?:^|\s)(?:#)([a-zA-Z\d]+)$/gm;
    var regex=/#\S+/g;
    var matches = [];
    matches=inputText.match(regex);
    return matches;
}

$(document).on('click','#btn_submit_opinion',function(){
   // var pattern = new RegExp(/(^|\s)#(\w+)/gi);
   if(au==0){
    openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                submitOpinion();
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                submitOpinion();
            }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                submitOpinion();
            }
        }
    }
});


function submitOpinion(){
    var hashtags=getHashTags($('#write_opinion').val().trim());
    if (hashtags && hashtags.length>0) {
        var FinalArr=[];
        for(var i=0;i<hashtags.length;i++){
            if(hashtags[i].length<4){
                FinalArr.push(0);
                ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
            }else{
                FinalArr.push(1);
            }
        }

         if(!FinalArr.includes(0)){

             if($('#type').val()=='IMAGE'){
                var val="";
                var arr=[];
                 $.each(finalFiles,function(i,file){
                     arr.push(file.name);
                 });
                var str=arr.join(",");
                $('#cover').val(str);
                $('#write_thread').submit();
           }else{
              $('#write_thread').submit();
           }
        }
    }else{
        ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
    }
}

$(document).on('click','.like',function(){
    var likeid=$(this).attr('id');
    var opinionid=parseInt(likeid.slice(5));
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                callLikeOpinionAjax(opinionid,likeid,'inline');
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                callLikeOpinionAjax(opinionid,likeid,'inline');
            }
        }

        if(au==1 && cev==1 &&  cmv==1){
            if(mv==0 && ev==0){
            openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
            }
            if(mv==1 && ev==0){
            openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }
            if(mv==0 && ev==1){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }
            if(mv==1 && ev==1){
                callLikeOpinionAjax(opinionid,likeid,'inline');
            }
        }
    }
});


function callLikeOpinionAjax(opinionid,likeid,display){
var likeCount=parseInt($('#like_count_'+opinionid).text());
$.ajax({
        url:'/like_opinion',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{opinion_id:opinionid},
        dataType:'json',
        success:function(response){
            if(response.status=="liked"){
                likeCount=likeCount+1;
                 $('#like_'+opinionid+'_off').css('display','none');
                 $('#like_'+opinionid+'_on').css('display',display);
                 $('#like_count_'+opinionid).text(likeCount);
            }
            if(response.status=="like"){
                likeCount=likeCount-1;
                $('#like_'+opinionid+'_on').css('display','none');
                $('#like_'+opinionid+'_off').css('display',display);
                $('#like_count_'+opinionid).text(likeCount);
            }
        },error:function(response){}
       });
}

$(document).on('click','.like_thread',function(){
   var thread_id=$(this).attr('data-thread');
   var like_count=parseInt($('#thread_likes_count').text());
   $.ajax({
      url:'/thread/like',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      type:'POST',
      data:{id:thread_id},
      dataType:'json',
      success:function(response){
          if(response.status=='liked'){
            $('.like_thread_off_'+thread_id).css('display','none');
            $('.like_thread_on_'+thread_id).css('display','inline');
            like_count=like_count+1;
            $('#thread_likes_count').text(like_count);
          } else{
            $('.like_thread_off_'+thread_id).css('display','inline');
            $('.like_thread_on_'+thread_id).css('display','none');
            like_count=like_count-1;
            $('#thread_likes_count').text(like_count);
          }
      },error:function(error){}
   });
});

$(document).on('click','.follow_thread',function(){
    var thread_id=$(this).attr('data-thread');
    var follower_count=parseInt($('#thread_followers_count').text());
    $.ajax({
       url:'/thread/follow',
       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       type:'POST',
       data:{id:thread_id},
       dataType:'json',
       success:function(response){
           if(response.status=='followed'){
             $('.follow_thread_off_'+thread_id).css('display','none');
             $('.follow_thread_on_'+thread_id).css('display','inline');
             follower_count=follower_count+1;
            $('#thread_followers_count').text(follower_count);
           } else{
             $('.follow_thread_off_'+thread_id).css('display','inline');
             $('.follow_thread_on_'+thread_id).css('display','none');
             follower_count=follower_count-1;
             $('#thread_followers_count').text(follower_count);
           }
       },error:function(error){

       }
    });
});

    $(document).on('click','.delete-opinion-modal',function(){
        var deleteBtnID=$(this).attr('id');
        var deleteID=deleteBtnID.slice(7);
        $('#delete_id').val(deleteID);
        $('#deleteMyOpinion').modal('show');
    });

    $(document).on('click','.delete-opinion',function(){
        var deleteid=$('#delete_id').val();
        $.ajax({
            url:"/delete_opinion",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type:'POST',
            data:{deleteid:deleteid,_method:'DELETE'},
            dataType:'json',
            success:function(response){
                if(response.status=='success'){
                    $('#opinion_'+deleteid).remove();
                    $('#deleteMyOpinion').modal('hide');
                }
            },error:function(response){
                $('#deleteMyOpinion').modal('hide');
            }
        });
    });


$(document).on('click','.embed-opinion',function(){
    var url=$(this).attr('data-url');
    $('#embed-opinion').text(`<iframe src=${url} height="500" width="100%" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" />`);
    $('#copy-to-clipboard').text('Copy Embed Code');
    $('#EmbedOpinionShareModal').modal('show');
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

$(document).on('click','#copy-to-clipboard',function(){
    var target=$(this).attr('data-target');
    $(target).focus();
    $(target).select();
    copyToClipboard(target);
    $(this).text('Copied')
});

/*--- (8) Notification funcitons ----------------------------------------------*/

function getUnreadNotifications() {
    $.ajax({
        url: '/me/unread_notifications',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType: 'text',
        success: function(response) {
            if (response.length > 0) {
                $('#notifications-list').empty();
                $('#notifications-list').append(response);
            }
        },
        error: function(error) {}
    });
}

function markAsRead() {
    $.ajax({
        url: '/me/mark_as_read',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') { $('#notificationsCount').css('display', 'none'); }
        },
        error: function(error) {}
    });
}
