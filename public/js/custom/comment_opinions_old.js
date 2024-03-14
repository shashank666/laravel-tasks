

 $(document).on('click','.editComment',function(){
    var comment_id=parseInt($(this).attr('id').slice(7));
    $('#ctext-'+comment_id).css('display','none');
    $('#edit-comment-'+comment_id).css('display','block');
    $('#btn-uc-'+comment_id).css('display','inline');
    $('#btn-cancleEdit-'+comment_id).css('display','inline');
    $(this).css('display','none');
});

$(document).on('click','.deleteComment',function(){
    var deleteid=$(this).attr('id');
    var comment_id=parseInt(deleteid.slice(7));
    var token=$("input[name=_token]").val();
    var opinion_id=parseInt($(this).attr('name').slice(3));
    var commentCount=parseInt($('#comment_count_'+opinion_id).text());

    $.ajax({
        url:'/delete_my_comment',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'post',
        data:{comment_id:comment_id,opinion_id:opinion_id},
        dataType:'json',
        success:function(response){
            console.log(response)
            if(response.status=='success'){
                $('#comment-'+comment_id).remove();
                newCommentCount=commentCount-1;
                $('#comment_count_'+opinion_id).text(newCommentCount);
            }
        },
        error:function(err){

        }
    });
});


$(document).on('click','.cancleEdit',function(){
    var comment_id=parseInt($(this).attr('id').slice(15));
    $(this).css('display','none');
    $('#edit-comment-'+comment_id).css('display','none');
    $('#btn-uc-'+comment_id).css('display','none');
    $('#ctext-'+comment_id).css('display','block');
    $('#btn-ec-'+comment_id).css('display','inline');
});

function loadComments(loadMoreBtn,opinionID,page){
    if(page>1){
        $(loadMoreBtn).text('Loading ...');
        $(loadMoreBtn).addClass('disabled');
    }
    $.ajax({
        url:'/load_comments?page='+page,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'GET',
        dataType:'json',
        data:{opinion_id:opinionID},
        success:function(response){
            if(response.comments.next_page_url==null){
                $(loadMoreBtn).attr('data-nextpage','');
                $(loadMoreBtn).css('display','none');
            }else{
                var nextP=response.comments.next_page_url.split("?page=")[1];
                $(loadMoreBtn).attr('data-nextpage',nextP);
                $(loadMoreBtn).removeClass('disabled');
                $(loadMoreBtn).text('Load More Comments');
                $(loadMoreBtn).css('display','block');
            }
            if(response.comments.data.length>0){
                displayComments(response.userid,response.comments.data,'#comments-'+opinionID);
            }
        },
        error:function(response){

        }
    });
    return false;
}


$(document).on('click','.showComment',function(){
    var opinionID=$(this).attr('data-showcomment');
    var collapseDiv=$('#show-comments-'+opinionID);
    $('#show-comments-'+opinionID).collapse('toggle');
    $('#show-comments-'+opinionID).on('shown.bs.collapse',function (e) {
        if($('#comments-'+opinionID).children().length==0){
            loadComments('#loadMore-'+opinionID,opinionID,1);
        }
    });
});


function displayComments(userID,commentsArray,appendDiv){
    var appendVariable='';
    for(var i=0;i<commentsArray.length;i++){
        var commentId=commentsArray[i].id;
        var commentBody=commentsArray[i].comment;
        var commentUpdateAt=commentsArray[i].updated_at;
        var commentUser=commentsArray[i].user;
        var commentOpinionId=commentsArray[i].short_opinion_id;

        comment= '<li class="list-group-item" style="padding: .50rem 0;"  id="comment-'+commentId+'">'+
                    '<div class="media">'+
                        '<img class="d-flex mr-3 rounded-circle" src="'+commentUser.image+'" height="40" width="40"/>'+
                            '<div class="mr-3 media-body" >'+
                                '<h6 class="mt-0 mb-0">'+commentUser.name+'</h6>'+
                                '<p id="ctext-'+commentId+'">'+commentBody+'</p>'+
                                '<textarea id="edit-comment-'+commentId+'" class="mt-2 form-control" rows="2" name="comment" placeholder="add your comment ..." minlength="6" required="" autocomplete="off"  style="display:none" autofocus>'+commentBody+'</textarea>'+
                            '</div>'+
                            '<div class="float-right">'+
                            '<p class="text-muted" style="font-size:14px;margin-bottom:0px;">'+commentUpdateAt+'</p>';
            buttons='';
            if(userID>0){
                if(userID==commentUser.id){

                 buttons =  '<span style="font-size: 18px;cursor: pointer;" class="mr-2 text-primary editComment" id="btn-ec-'+commentId+'" name="ec-'+commentOpinionId+'"><i class="fas fa-pencil-alt"></i></span>'+
                 '<span style="font-size: 18px;cursor: pointer;display:none" class="mr-2 text-success updateComment" id="btn-uc-'+commentId+'" name="uc-'+commentOpinionId+'"><i class="far fa-check-circle"></i></span>'+
                 '<span style="font-size: 18px;cursor: pointer;display:none" class="mr-2 text-warning cancleEdit" id="btn-cancleEdit-'+commentId+'"><i class="far fa-times-circle"></i></span>'+
                 '<span style="font-size: 18px;cursor: pointer;" class="text-danger deleteComment" id="btn-dc-'+commentId+'" name="dc-'+commentOpinionId+'"><i class="far fa-trash-alt"></i></span>';
                }
            }

               end = '</div>'+'</div>'+'</li>';

        commentCard=comment+buttons+end;
        $(appendDiv).append(commentCard);
    }
}



$(document).on('click','.loadMore',function(){
    var ButtonID=$(this).attr('id');
    var opinionID=ButtonID.slice(9);
    var page=$(this).attr('data-nextpage');
    loadComments('#'+ButtonID,opinionID,page);
});


$(document).on('click','.updateComment',function(){
    var comment_id=parseInt($(this).attr('id').slice(7));
    var newComment=$('#edit-comment-'+comment_id).val();
    var opinion_id=parseInt($(this).attr('name').slice(3))

    $.ajax({
        url:'/update_my_comment',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'post',
        data:{comment_id:comment_id,comment:newComment,opinion_id:opinion_id},
        dataType:'json',
        success:function(response){
            if(response.status=='success'){
                $('#ctext-'+comment_id).text(newComment);
                $('#edit-comment-'+comment_id).css('display','none');
                $('#ctext-'+comment_id).css('display','block');
                $('#btn-cancleEdit-'+comment_id).css('display','none');
                $('#btn-uc-'+comment_id).css('display','none');
                $('#btn-ec-'+comment_id).css('display','inline');
            }
        },
        error:function(err){

        }
    });
});



function appendComment(response,appendDiv,opinionID){

    var commentId=response.comment.id;
    var commentBody=response.comment.comment;
    var commentUpdateAt=response.comment.updated_at;
    var commentUser=response.comment.user;
    var commentCount=parseInt($('#comment_count_'+opinionID).text());


    commentCard='<li class="list-group-item" style="padding: .50rem 0;" id="comment-'+commentId+'">'+
                        '<div class="media">'+
                            '<img class="d-flex mr-3 rounded-circle" src="'+commentUser.image+'" height="40" width="40"/>'+
                            '<div class="media-body mr-3">'+
                                '<h6 class="mt-0 mb-0">'+commentUser.name+'</h6>'+
                                '<p id="ctext-'+commentId+'">'+commentBody+'</p>'+
                                '<textarea id="edit-comment-'+commentId+'" class="mt-2 form-control" rows="2" name="comment" placeholder="add your comment ..." minlength="6" required="" autocomplete="off"  style="display:none" autofocus>'+commentBody+'</textarea>'+
                            '</div>'+
                            '<div class="float-right">'+
                                '<p class="text-muted" style="font-size:14px;margin-bottom:0px;">'+commentUpdateAt+'</p>'+
                                '<span style="font-size: 18px;cursor: pointer;" class="mr-2 text-primary editComment" id="btn-ec-'+commentId+'" name="ec-'+opinionID+'"><i class="fas fa-pencil-alt"></i></span>'+
                                '<span style="font-size: 18px;cursor: pointer;display:none" class="mr-2 text-success updateComment" id="btn-uc-'+commentId+'" name="uc-'+opinionID+'"><i class="far fa-check-circle"></i></span>'+
                                '<span style="font-size: 18px;cursor: pointer;display:none" class="mr-2 text-warning cancleEdit" id="btn-cancleEdit-'+commentId+'"><i class="far fa-times-circle"></i></i></span>'+
                                '<span style="font-size: 18px;cursor: pointer;" class="text-danger deleteComment" id="btn-dc-'+commentId+'" name="dc-'+opinionID+'"><i class="far fa-trash-alt"></i></span>'+
                            '</div>'+
                        '</div>'+
                '</li>';
    $(appendDiv).prepend(commentCard);
    newCommentCount=commentCount+1;
    $('#comment_count_'+opinionID).text(newCommentCount);
}

function submitComment(Form,appendDiv,opinionID){
    $.ajax({
        url:'/comment_on_opinion',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        dataType:'json',
        data:$(Form).serialize(),
        success:function(response){
            if(response.status=='success'){
                $(Form)[0].reset();
                appendComment(response,appendDiv,opinionID);
            }
        },error:function(){

        }
    });
}

$(document).on('submit','.comment_opinion_form',function(e){
    e.preventDefault();
    var formid=$(this).attr('id');
    var opinionid=formid.slice(13);
    if(au==0){
        openLoginModal();
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                submitComment('#'+formid,'#comments-'+opinionid,opinionid);
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                submitComment('#'+formid,'#comments-'+opinionid,opinionid);
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
                submitComment('#'+formid,'#comments-'+opinionid,opinionid);
            }
        }
    }
});


