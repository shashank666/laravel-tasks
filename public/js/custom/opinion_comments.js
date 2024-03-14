var finalOpinionCommentsFiles = {};

$(document).on('click', '.btnloadMore', function() {
    let nextPage=parseInt($(this).attr('data-nextpage'));
    let opinionID=parseInt($(this).attr('data-opinion'));
    loadOpinionComments(opinionID,nextPage,'next');
});

$(document).on('click', '.btnloadPrev', function() {
    let prevPage=parseInt($(this).attr('data-prevpage'));
    let opinionID=parseInt($(this).attr('data-opinion'));
    loadOpinionComments(opinionID,prevPage,'prev');
});

$(document).on('click','.btnloadMoreReplies',function(){
    let nextPage=parseInt($(this).attr('data-nextpage'));
    let parent_id=parseInt($(this).attr('data-parentid'));
    let opinionID=parseInt($(this).attr('data-opinion'));
    loadOpinionCommentReplies('#replies_'+parent_id,opinionID,parent_id,nextPage);
});

$(document).on('click','.loadReplies',function(){
    let count=parseInt($(this).attr('data-count'));
    let commentID=parseInt($(this).attr('data-commentid'));
    let opinionID=parseInt($(this).attr('data-opinion'));
    let dataLoaded=parseInt($(this).attr('data-loaded'));
    if(count>0 && dataLoaded==0){
        $('#replies_'+commentID).css('display','block');
        $('#replies_'+commentID).empty();
        loadOpinionCommentReplies('#replies_'+commentID,opinionID,commentID,1);
    }
});

$(document).on('click','.showComment',function(){
    let opinionID=$(this).attr('data-opinion');
    loadOpinionComments(opinionID,1,'next');
});

function loadOpinionCommentReplies(appendDiv,opinionID,commentID,page){
    var opinion_id = parseInt(opinionID);
    if (page > 1) {
        $('#btnloadMore_'+commentID).text('Loading ...');
        $('#btnloadMore_'+commentID).addClass('disabled');
    }
    $.ajax({
        url: `/opinion/comments/load/replies?page=${page}&opinion_id=${opinion_id}&comment_id=${commentID}`,
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

function loadOpinionComments(opinion_id,page,event) {
    if (page > 1) {
        if(event=='next'){
            $('#btnloadMore_'+opinion_id).text('Loading ...');
            $('#btnloadMore_'+opinion_id).addClass('disabled');
            $('#btnloadPrev_'+opinion_id).addClass('disabled');
        }
        if(event=='prev'){
            $('#btnloadPrev_'+opinion_id).text('Loading ...');
            $('#btnloadPrev_'+opinion_id).addClass('disabled');
            $('#btnloadMore_'+opinion_id).addClass('disabled');
        }
    }
    $.ajax({
        url: `/opinion/comments/load?page=${page}&opinion_id=${opinion_id}`,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            //$('#btnloadMore_'+opinion_id).remove();
            $('.comments_div_'+opinion_id).empty();
            $('.comments_div_'+opinion_id).append(response.html);
        },
        error: function(response) {
            $('#btnloadMore_'+opinion_id).text('Load More Comments');
            $('#btnloadMore_'+opinion_id).removeClass('disabled');
        }
    });
}


$(document).on('click','.opinionAddComment',function(){
    if(au=='0'){
        $('#forgotPasswordModal').modal('show');
    }else{
        var opinionID=$(this).attr('data-opinion');
        $('#add_opinion_comment_form').attr('action','/opinion/comments/create');
        $('#comment_opinion_id').val(opinionID);
        $('#comment_parent_id').val(0);
        $('#addOpinionCommentModalLabel').text('Add Your Comment');
        $('#addOpinionCommentModal').modal('show');
    }
});


$(document).on('click','#btn_add_opnion_comment_image',function(){
    $('#commentMedia').click();
});

$(document).on('click','#btn_add_opinion_comment_gif',function(){
    openAddGIFModal('comment');
});


$(document).on('submit', '#add_opinion_comment_form', function(e) {
    e.preventDefault();
    if($(this).attr('action')=='/opinion/comments/create'){
        parentID=parseInt($('#comment_parent_id').val());
        opinionID=parseInt($('#comment_opinion_id').val());
        if(parentID>0){
            $('#replies_'+parentID).css('display','block');
            submitOpinionComment(opinionID,'#add_opinion_comment_form','#replies_'+parentID,'prepend');
            let prevReplyCount=parseInt($('#commentReplyCount_'+parentID).attr('data-count'));
            let newReplyCount=prevReplyCount+1;
            $('#commentReplyCount_'+parentID).html(newReplyCount+' Replies');
            $('#commentReplyCount_'+parentID).attr('data-count',newReplyCount);
            $('#loadReplies_'+parentID).attr('data-count',newReplyCount);
        }else{
            submitOpinionComment(opinionID,'#add_opinion_comment_form','.comments_div_'+opinionID,'prepend');
        }
    }else{
        $commentID=$('#comment_id').val();
        opinionID=parseInt($('#comment_opinion_id').val());
        submitOpinionComment(opinionID,'#add_opinion_comment_form','#comment_'+$commentID,'replaceWith')
    }
});

function submitOpinionComment(opinionID,Form,appendDiv,action){
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
                    $('#comment_count_'+opinionID).html(response.total_comments);
                }else{
                    $(appendDiv).replaceWith(response.comment);
                }
                $('#addOpinionCommentModal').modal('hide');
            }else{
                $('#commentError').css('display','');
                $('#commentError').html(response.message);
                $("#commentError").fadeOut(3500);
            }
        },
        error: function(xhr, textStatus, error) {
            // $('#commentError').css('display','');
            // $('#commentError').html('Error Posting Comment '+error + ' '+textStatus+' '+xhr.statusText);
            // $("#commentError").fadeOut(3500);
            $('#addOpinionCommentModal').modal('hide');
            location.reload();
        }
    });
}


$(document).on('hidden.bs.modal','#addOpinionCommentModal',function(){
    $('#commentImagePreview').empty();
    $('#commentImagePreview').css('display','none');
    $('#commentImage').val('');
    $('#commentMedia').val('');
    $('#comment_id').val('');
    $('#opinion_comment_textarea').data("emojioneArea").setText('');
    finalOpinionCommentsFiles = {};
});

$(document).on('change','#commentMedia',function(e){
    finalOpinionCommentsFiles = {};
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
                    finalOpinionCommentsFiles[i]=file;
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
  delete finalOpinionCommentsFiles[index];
  $('#commentMedia').val('');
});

$(document).on('click','.removeCommentImage',function(){
    $('#commentMedia').val('');
    $('#commentImage').val('');
    $('#commentImagePreview').empty();
});

$(document).on('click','.replyComment',function(){
    var opinionID=parseInt($(this).attr('data-opinion'));
    var parentID=parseInt($(this).attr('data-parentid'));
    var title=$(this).attr('data-title');
    $('#add_opinion_comment_form').attr('action','/opinion/comments/create');
    $('#comment_opinion_id').val(opinionID);
    $('#comment_parent_id').val(parentID);
    $('#addOpinionCommentModalLabel').text(title);
    $('#addOpinionCommentModal').modal('show');
});

$(document).on('click','.editComment',function(){
    var commentID=parseInt($(this).attr('data-commentid'));
    var opinionID=parseInt($(this).attr('data-opinion'));
    var parentID=parseInt($(this).attr('data-parentid'));
    var title=$(this).attr('data-title');
    var media=$(this).attr('data-media');
    var comment=$(this).attr('data-comment');
    $('#add_opinion_comment_form').attr('action','/opinion/comments/update');
    $('#comment_id').val(commentID);
    $('#comment_opinion_id').val(opinionID);
    $('#comment_parent_id').val(parentID);
    $('#addOpinionCommentModalLabel').text(title);
    if(media=='NONE'){
        $('#commentImage').val('');
    }else{
        $('#commentImage').val(media);
        let img='<div class="col-md-6 col-12"><div class="card mb-2"><img src="'+media+'" style="height:250px;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="removeCommentImage btn btn-sm float-right text-white bg-danger py-1 px-2 rounded" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
        $('#commentImagePreview').append(img);
        $('#commentImagePreview').css('display','');
    }
    if(comment=='NONE'){$('#opinion_comment_textarea').data("emojioneArea").setText('');}else{$('#opinion_comment_textarea').data("emojioneArea").setText(comment);}
    $('#addOpinionCommentModal').modal('show');
});



$(document).on('click', '.deleteComment', function() {
    var comment_id = parseInt($(this).attr('data-commentid'));
    var opinion_id = parseInt($(this).attr('data-opinion'));
    var parent_id=parseInt($(this).attr('data-parentid'));
    $.ajax({
        url: '/opinion/comments/delete',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'post',
        data: { comment_id: comment_id, opinion_id: opinion_id },
        dataType: 'json',
        success: function(response) {
            console.log(response)
            if (response.status == 'success') {
                $('#comment_' + comment_id).remove();
                $('#comment_count_'+opinion_id).text(response.total_comments);
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
    var likeCount=parseInt($('.comment_likes_count_'+commentID).text()); 
    var disagreeCount=parseInt($('.comment_disagree_count_'+commentID).text()); 
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
        $.ajax({
        url:'/opinion/comments/like',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{comment_id:commentID},
        dataType:'json',
        success:function(response){
            if(response.status=="liked"){
                if($('.disagreeComment_'+commentID+'_on').css('display')=='inline'){
                    disagreeCount = disagreeCount-1;
                }
                likeCount=likeCount+1;
                // if(disagreeCount = -1){disagreeCount = 0;}
                 $('.likeComment_'+commentID+'_off').css('display','none');
                 $('.likeComment_'+commentID+'_on').css('display','inline');
                 $('.disagreeComment_'+commentID+'_on').css('display','none');
                 $('.disagreeComment_'+commentID+'_off').css('display','inline');
                 $('.comment_likes_count_'+commentID).text(likeCount+' Agrees');
                 $('.comment_disagree_count_'+commentID).text(disagreeCount+' Disagrees');
                 //location.reload();
            }
            if(response.status=="like"){
                likeCount=likeCount-1;
                $('.likeComment_'+commentID+'_on').css('display','none');
                $('.likeComment_'+commentID+'_off').css('display','inline');
                $('.comment_likes_count_'+commentID).text(response.count+' Agrees');
            }
        },error:function(response){

        }
       });
    }
});

$(document).on('click','.disagreeComment',function(){
    var commentID=$(this).attr('data-commentid');
    var likeCount=parseInt($('.comment_likes_count_'+commentID).text()); 
    var disagreeCount=parseInt($('.comment_disagree_count_'+commentID).text()); 
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
        $.ajax({
        url:'/opinion/comments/disagree',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{comment_id:commentID},
        dataType:'json',
        success:function(response){
            if(response.status=="disagreed"){
                disagreeCount = disagreeCount+1;
                if($('.likeComment_'+commentID+'_on').css('display')=='inline'){
                    likeCount = likeCount-1;
                }
                // if(likeCount = -1){likeCount = 0;}
                 $('.disagreeComment_'+commentID+'_off').css('display','none');
                 $('.disagreeComment_'+commentID+'_on').css('display','inline');
                 $('.likeComment_'+commentID+'_on').css('display','none');
                 $('.likeComment_'+commentID+'_off').css('display','inline');
                 $('.comment_likes_count_'+commentID).text(likeCount+' Agrees');
                 $('.comment_disagree_count_'+commentID).text(disagreeCount+' Disagrees');
                 //location.reload();
            }
            if(response.status=="disagree"){
                disagreeCount=disagreeCount-1;
                $('.disagreeComment_'+commentID+'_on').css('display','none');
                $('.disagreeComment_'+commentID+'_off').css('display','inline');
                $('.comment_disagree_count_'+commentID).text(response.count+' Disagrees');
            }
        },error:function(response){

        }
       });
    }
});