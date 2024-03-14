var finalCommentsFiles = {};

$(document).ready(function(){
    loadComments(1);
});

$(document).on('click', '#btnloadMore', function() {
    loadComments($(this).attr('data-nextpage'));
});

$(document).on('click','.btnloadMoreReplies',function(){
    let nextPage=parseInt($(this).attr('data-nextpage'));
    let parent_id=parseInt($(this).attr('data-parentid'));
    loadReplies('#replies_'+parent_id,parent_id,nextPage);
});

$(document).on('click','.loadReplies',function(){
    let count=parseInt($(this).attr('data-count'));
    let commentID=parseInt($(this).attr('data-commentid'));
    let dataLoaded=parseInt($(this).attr('data-loaded'));
    if(count>0 && dataLoaded==0){
        $('#replies_'+commentID).css('display','block');
        $('#replies_'+commentID).empty();
        loadReplies('#replies_'+commentID,commentID,1);
    }
});

function loadReplies(appendDiv,commentID,page){
    var post_id = parseInt($("#postid").val());
    if (page > 1) {
        $('#btnloadMore_'+commentID).text('Loading ...');
        $('#btnloadMore_'+commentID).addClass('disabled');
    }
    $.ajax({
        url: `/comments/load/replies?page=${page}&post_id=${post_id}&comment_id=${commentID}`,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#loadReplies_'+commentID).attr('data-loaded',1);
            $('#btnloadMore_'+commentID).remove();
            $(appendDiv).append(response.html);
        },
        error: function(response) {
             $('#btnloadMore_'+commentID).text('Load More Replies');
             $('#btnloadMore_'+commentID).removeClass('disabled');
        }
    });
}

function loadComments(page) {
    var post_id = parseInt($("#postid").val());
    if (page > 1) {
        $('#btnloadMore').text('Loading ...');
        $('#btnloadMore').addClass('disabled');
    }
    $.ajax({
        url: `/comments/load?page=${page}&post_id=${post_id}`,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#btnloadMore').remove();
            $('.comments-div').append(response.html);
        },
        error: function(response) {
            $('#btnloadMore').text('Load More Comments');
            $('#btnloadMore').removeClass('disabled');
        }
    });
}

$(document).on('click','#post_add_comment',function(){
    if(au=='0'){
        $('#forgotPasswordModal').modal('show');
    }else{
        var postID=parseInt($(this).attr('data-post'));
        $('#add_comment_form').attr('action','/comments/create');
        $('#comment_post_id').val(postID);
        $('#comment_parent_id').val(0);
        $('#addCommentModalLabel').text('Add Your Comment');
        $('#addCommentModal').modal('show');
    }
});

$(document).on('click','#btn_add_comment_image',function(){
    $('#commentMedia').click();
});

$(document).on('click','#btn_add_comment_gif',function(){
    openAddGIFModal('comment');
});


$(document).on('submit', '#add_comment_form', function(e) {
    e.preventDefault();
    if($(this).attr('action')=='/comments/create'){
        parentID=parseInt($('#comment_parent_id').val());
        if(parentID>0){
            $('#replies_'+parentID).css('display','block');
            submitComment('#add_comment_form','#replies_'+parentID,'prepend');
            let prevReplyCount=parseInt($('#commentReplyCount_'+parentID).attr('data-count'));
            let newReplyCount=prevReplyCount+1;
            $('#commentReplyCount_'+parentID).html(newReplyCount+' Replies');
            $('#commentReplyCount_'+parentID).attr('data-count',newReplyCount);
            $('#loadReplies_'+parentID).attr('data-count',newReplyCount);
        }else{
            submitComment('#add_comment_form','.comments-div','prepend');
        }
    }else{
        $commentID=$('#comment_id').val();
        submitComment('#add_comment_form','#comment_'+$commentID,'replaceWith')
    }
});

function submitComment(Form,appendDiv,action){
    var form = $(Form)[0];
    var formData = new FormData(form);
    $.ajax({
        url: $(Form).attr('action'),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache : false,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status == 'success') {
                $(Form)[0].reset();
                if(action=='prepend'){
                    $(appendDiv).prepend(response.comment);
                    $('.commentsTotalCount').html(response.total_comments);
                }else{
                    $(appendDiv).replaceWith(response.comment);
                }
                $('#addCommentModal').modal('hide');
            }else{
                $('#commentError').css('display','');
                $('#commentError').html(response.message);
                $("#commentError").fadeOut(3500);
            }
        },
        error: function() {
            $('#commentError').css('display','');
            $('#commentError').html('Error Posting Comment');
            $("#commentError").fadeOut(3500);
        }
    });
}


$(document).on('hidden.bs.modal','#addCommentModal',function(){
    $('#commentImagePreview').empty();
    $('#commentImagePreview').css('display','none');
    $('#commentImage').val('');
    $('#commentMedia').val('');
    $('#comment_id').val('');
    $('#comment_textarea').data("emojioneArea").setText('');
    finalCommentsFiles = {};
});


$(document).on('change','#commentMedia',function(e){
    finalCommentsFiles = {};
     i = 0;
    var files = e.target.files; //FileList object
     if ($('#commentMedia')[0].files.length>0){
         $('#commentImagePreview').empty();
         $('#commentImage').val('');
        var file_size = $('#commentMedia')[0].files[0].size;
        if (file_size > 2097152) {
            $('#commentError').css('display','');
            $('#commentError').html('Filesize is more than 2 MB not allowed');
            $("#commentError").fadeOut(4500, function() {
                $('#commentMedia').val('');
                $('#commentImagePreview').css('display', 'none');
            });
        } else {
            $('#commentImagePreview').css('display','');
            $.each(files, function(i, file) {
                    finalCommentsFiles[i]=file;
                    var reader = new FileReader();
                    reader.onload = function (e) {
                    var img='<div class="col-md-6 col-12"  id="file_'+ i +'"><div class="card mb-2"><img src="'+e.target.result+'" style="height:250px;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="removeCommentFile btn btn-sm float-right text-white bg-danger py-1 px-2 rounded" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
                    $('#commentImagePreview').append(img);
                    }
                    reader.readAsDataURL(file);
            });
        }
    }
});


$(document).on('click','.removeCommentFile',function()
{
 var container = $(this).parents().get(2);
 var c_id=container.id;
 var index = c_id.split('_')[1];
  container.remove();
  delete finalCommentsFiles[index];
  $('#commentMedia').val('');
});

$(document).on('click','.removeCommentImage',function(){
    $('#commentMedia').val('');
    $('#commentImage').val('');
    $('#commentImagePreview').empty();
});

$(document).on('click','.replyComment',function(){
    var postID=parseInt($(this).attr('data-post'));
    var parentID=parseInt($(this).attr('data-parentid'));
    var title=$(this).attr('data-title');
    $('#add_comment_form').attr('action','/comments/create');
    $('#comment_post_id').val(postID);
    $('#comment_parent_id').val(parentID);
    $('#addCommentModalLabel').text(title);
    $('#addCommentModal').modal('show');
});

$(document).on('click','.editComment',function(){
    var commentID=parseInt($(this).attr('data-commentid'));
    var postID=parseInt($(this).attr('data-post'));
    var parentID=parseInt($(this).attr('data-parentid'));
    var title=$(this).attr('data-title');
    var media=$(this).attr('data-media');
    var comment=$(this).attr('data-comment');
    $('#add_comment_form').attr('action','/comments/update');
    $('#comment_id').val(commentID);
    $('#comment_post_id').val(postID);
    $('#comment_parent_id').val(parentID);
    $('#addCommentModalLabel').text(title);
    if(media=='NONE'){
        $('#commentImage').val('');
    }else{
        $('#commentImage').val(media);
        let img='<div class="col-md-6 col-12"><div class="card mb-2"><img src="'+media+'" style="height:250px;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="removeCommentImage btn btn-sm float-right text-white bg-danger py-1 px-2 rounded" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
        $('#commentImagePreview').append(img);
        $('#commentImagePreview').css('display','');
    }
    if(comment=='NONE'){$('#comment_textarea').data("emojioneArea").setText('');}else{$('#comment_textarea').data("emojioneArea").setText(comment);}
    $('#addCommentModal').modal('show');
});

$(document).on('click', '.deleteComment', function() {
    var comment_id = parseInt($(this).attr('data-commentid'));
    var post_id = parseInt($(this).attr('data-post'));
    var parent_id=parseInt($(this).attr('data-parentid'));
    $.ajax({
        url: '/comments/delete',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'post',
        data: { comment_id: comment_id, post_id: post_id },
        dataType: 'json',
        success: function(response) {

            if (response.status == 'success') {
                $('#comment_' + comment_id).remove();
                $('.commentsTotalCount').text(response.total_comments);
                $('#replies_'+comment_id).remove();

                let prevReplyCount=parseInt($('#commentReplyCount_'+parent_id).attr('data-count'));
                if(prevReplyCount!=0 & parent_id!=0){
                    let newReplyCount=prevReplyCount>0?prevReplyCount-1:0;
                    $('#commentReplyCount_'+parent_id).html(newReplyCount+' Replies');
                    $('#commentReplyCount_'+parent_id).attr('data-count',newReplyCount);
                    $('#loadReplies_'+parent_id).attr('data-count',newReplyCount);
                }
            }
        },
        error: function(err) {

        }
    });
});

$(document).on('click','.likeComment',function(){
    var commentID=$(this).attr('data-commentid');
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
        $.ajax({
        url:'/comments/like',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{comment_id:commentID},
        dataType:'json',
        success:function(response){
            if(response.status=="liked"){
                 $('.likeComment_'+commentID+'_off').css('display','none');
                 $('.likeComment_'+commentID+'_on').css('display','inline');
                 $('.comment_likes_count_'+commentID).text(response.count+' Likes');
            }
            if(response.status=="like"){
                $('.likeComment_'+commentID+'_on').css('display','none');
                $('.likeComment_'+commentID+'_off').css('display','inline');
                $('.comment_likes_count_'+commentID).text(response.count+' Likes');
            }
        },error:function(response){

        }
       });
    }
});
