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
        error: function(err) {

        }
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
            '<a href="/@' + commentUser.username +  '" style="text-decoration:none;"><img class="rounded-circle" src="' + commentUser.image + '" height="64" width="64"/></a>' +
            '</div>' +
            '<div class="col-lg-7 col-md-7 col-sm-12 col-12">' +
            '<div class="media-body ml-3 mr-3 text-md-left text-sm-center text-center">' +
            '<a href="/@' + commentUser.username + '" style="text-decoration:none;color:#212121;"><h5 class="mt-0">' + commentUser.name + '</h5></a>' +
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
        error: function(response) {

        }
    });
}



$(document).ready(function() {
    loadComments(1);
});

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
        error: function(err) {

        }
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
        '<a href="/@' + commentUser.username + '" style="text-decoration:none;"><img class="rounded-circle" src="' + commentUser.image + '" height="64" width="64"/></a>' +
        '</div>' +
        '<div class="col-lg-7 col-md-7 col-sm-12 col-12 text-md-left text-sm-center text-center">' +
        '<div class="media-body ml-3 mr-3">' +
        '<a href="/@' + commentUser.username + '" style="text-decoration:none;color:#212121;"><h5 class="mt-0">' + commentUser.name + '</h5></a>' +
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
