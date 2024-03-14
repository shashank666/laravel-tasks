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

 /*---------------------SIDEBAR FUNCTIONS----------------------*/
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
/*---------------------END OF SIDEBAR FUNCTIONS----------------------*/
/*----------------------Likes view for opinion -----------------------*/

$(document).on('click', '.btn-opinion-likes-count', function() {
            
            var opinion_id = parseInt($(this).attr('data-opinion'));

            $('.opinion_likes').empty();
            $('#likesOpinionModal').modal('show');
            $('.loader').css('display', 'block');
            $.ajax({
                url: '/opinion_likes_count',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: { opinion_id: opinion_id },
                dataType: 'text',
                success: function(response) {
                    $('.loader').css('display', 'none');
                    $('.opinion_likes').append(response);
                },
                error: function() {
                    $('.loader').css('display', 'none');
                }
            });

    
        
        });
/*----------------------End of Likes view for opinion -----------------------*/

/*---------------------AUTH FUNCTIONS----------------------*/

function openVerifyEmailModal() { $('#verifyEmailModal').modal('show'); };
function closeVerifyEmailModal() { $('#verifyEmailModal').modal('hide'); };

function openVerifyMobileModal() { $('#verifyMobileModal').modal('show'); $('#verifyOTPModal').modal('hide'); };
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
                $(".login_response").fadeOut(5000);
            } else {
                $(".login_response").css("background-color", '#dff0d8');
                $(".login_response").css("color", '#3c763d');
                $(".login_response").css("visibility", 'visible');
                $(".login_response").show();
                $(".login_response").html(data.message);
                if(cmv==1){
                    console.log(data);
                    console.log('mobile',data.mobile_no);
                    $("#userid").val(data.user_id);
                    $("#mobileno").val(data.mobile_no);
                    $("#mobileno").html(data.mobile_no);
                    $(".login_response").fadeOut(1500, function() {
                        // $('#verify-otp-form').attr('action','/register');
                        // $('#resend-otp-form').attr('action','/resendOTP');
                       
                      closeRegisterModal();
                      window.location.reload();  
                
                        // openVerifyOTPModal();
                    });
                    
                }else{
                    $(".login_response").fadeOut(1500, function() {
                        window.location.reload();
                    });
                }
                $(".login_response").fadeOut(1500, function() {
                    window.location.reload();
                });
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
        data:{mobile:$('#add_mobile').val(),phone_code:$('#add_phone_code').val()},
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
    // if(cmv==1){
    //     var url='/create_account';
    // }else{
    //     var url='/register'
    // }
    var url='/create_account';
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
                    showEmailResponse('#dff0d8', '#3c763d', data.message, 7500);
                    $('#sendEmailForm')[0].reset();
                    $('#btnSendEmail').attr('disabled', false);
                } else {
                    showEmailResponse('#f2dede', '#a94442', data.message, 7500);
                    // showEmailResponse('#f2dede', '#a94442', data.msg, 7500);
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
/*---------------------END OF AUTH FUNCTIONS----------------------*/

/*---------------------BOOKMARK FUNCTIONS----------------------*/
$(document).on('click', '.bookmark', function() {
    var bookmarkid = $(this).attr('id');
    var postid = parseInt(bookmarkid.slice(9));
    var token = $("input[name=_token]").val();
    callBookmarkAjax(token, postid, bookmarkid, 'inline');
});

function callBookmarkAjax(token, postid, bookmarkid, display) {
    $.ajax({
        url: '/me/manage_bookmark',
        type: 'POST',
        data: { _token: token, postid: postid },
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
        error: function(response) {

        }
    });
}
/*---------------------END OF BOOKMARK FUNCTIONS----------------------*/


/*---------------------COMMENET FUNCTIONS----------------------*/
$(document).on('click','.create_opinion',function(){
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
/*---------------------END OF COMMENET FUNCTIONS----------------------*/


/*---------------------WRITE OPINION LINK FUNCTION------------------- */
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
/*---------------------END OF WRITE OPINION LINK FUNCTION------------------- */

/*---------------------LIKE POST FUNCTIONS----------------------*/
$(document).on('click', '.like_post', function() {
    var postdivid = $(this).attr('id');
    var postid = parseInt(postdivid.slice(9));
    var token = $("input[name=_token]").val();
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

/*---------------------END OF LIKE POST FUNCTIONS----------------------*/

/*---------------------REPORT POST FUNCTIONS----------------------*/
$(document).on('click', '.report-button', function() {
    var btnid = $(this).attr('id');
    var postid = btnid.slice(7);
    $('#reportpost').val(postid);
    $('#reportModal').modal('show');
});
/*---------------------END OF REPORT POST FUNCTIONS----------------------*/

/*---------------------CATEGORY FOLLOW FUNCTIONS----------------------*/
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
    var token = $("input[name=_token]").val();
    $.ajax({
        url: '/me/manage_category_follow',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { _token: token, categoryid: categoryid },
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
/*---------------------END OF CATEGORY FOLLOW FUNCTIONS----------------------*/

/*---------------------NOTIFICATIONS FUNCTIONS----------------------*/
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
/*---------------------END OF NOTIFICATIONS FUNCTIONS----------------------*/


/*----------------- FUNCTIONS FOR SHOWING GIF IMAGES IN MODAL----------------------*/

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
    var img='<div class="col-md-6 col-12"><div class="card mb-2"><img src="'+GIFimg+'" style="height:auto;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="btn btn-sm float-right text-white bg-danger py-1 px-2 rounded deleteGIF" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
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

/*--------------------END OF FUNCTIONS FOR SHOWING GIF -----------------*/

/*--------------------FUNCTIONS FOR EMBED OPINION ----------------------*/
$(document).on('click','.embed-opinion',function(){
    var url=$(this).attr('data-url');
    $('#embed-opinion').text(`<iframe src="${url}" height="700" width="100%" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"  onload='javascript:(function(o){o.style.height=o.contentWindow.document.body.scrollHeight+"px";}(this));' />`);
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
/*--------------------END OF FUNCTIONS FOR EMBED OPINION ---------------*/


/*---------------------GENERAL FUNCTIONS----------------------*/
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
/*---------------------END OF GENERAL FUNCTIONS----------------*/
