$(document).on('click','.like',function(){
    var likeid=$(this).attr('id');    
    var opinionid=parseInt(likeid.slice(5));
    var token=$("input[name=_token]").val();
    if(au==0){
        openLoginModal();
    }else{
        // if(au==1 && cev==1 && cmv==0){
        //     if(ev==0){
        //         openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
        //     }else{
        //         callLikeAjax(token,opinionid,likeid,'inline');
        //     }
        // }
    
        // if(au==1 && cev==0 && cmv==1){
        //     if(mv==0){
        //     openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
        //     }else{
        //         callLikeAjax(token,opinionid,likeid,'inline');
        //     }
        // }
    
        // if(au==1 && cev==1 &&  cmv==1){
        //     if(mv==0 && ev==0){
        //     openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
        //     }
        //     if(mv==1 && ev==0){
        //     openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
        //     }
        //     if(mv==0 && ev==1){
        //     openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
        //     }
        //     if(mv==1 && ev==1){
        //         callLikeAjax(token,opinionid,likeid,'inline');
        //     }
        // }  
        callLikeAjax(token,opinionid,likeid,'inline');
    }    
});

$(document).on('click','.dislike',function(){
    var dislikeid=$(this).attr('id');    
    var opinionid=parseInt(dislikeid.slice(8));
    var token=$("input[name=_token]").val();
    if(au==0){
        openLoginModal();
    }else{
        // if(au==1 && cev==1 && cmv==0){
        //     if(ev==0){
        //         openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
        //     }else{
        //         callDislikeAjax(token,opinionid,dislikeid,'inline');
        //     }
        // }
    
        // if(au==1 && cev==0 && cmv==1){
        //     if(mv==0){
        //     openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
        //     }else{
        //         callDislikeAjax(token,opinionid,dislikeid,'inline');
        //     }
        // }
    
        // if(au==1 && cev==1 &&  cmv==1){
        //     if(mv==0 && ev==0){
        //     openVerifyEmailMobileModal(verification[2]['title'],verification[2]['text'],verification[2]['buttons']);
        //     }
        //     if(mv==1 && ev==0){
        //     openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
        //     }
        //     if(mv==0 && ev==1){
        //     openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
        //     }
        //     if(mv==1 && ev==1){
        //         callDislikeAjax(token,opinionid, dislikeid,'inline');
        //     }
        // }  
        callDislikeAjax(token,opinionid, dislikeid,'inline');
    }    
});

$(document).on('click','.like_guest',function(){
   
    // alert("Please Login to Agree");
    // openLoginModal();
    $('#forgotPasswordModal').modal('show');

});
$(document).on('click','.dislike_guest',function(){
   
    // alert("Please Login to Agree");
    // openLoginModal();
    $('#forgotPasswordModal').modal('show');

});


function callLikeAjax(token,opinionid,likeid,display){
var likeCount=parseInt($('#agree_count_'+opinionid).text());
var dislikeCount=parseInt($('#disagree_count_'+opinionid).text());

$.ajax({
        url:'/opinion/like',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{_token:token,opinion_id:opinionid,Agree_Disagree: 1},
        async:false,
        cache: true,
        dataType:'json',
        success:function(response){
            if(response.status=="liked"){
                if(response.Agree_Disagree=='1'){
                    likeCount=likeCount+1;
                    if( $('#dislike_'+opinionid+'_on').css('display')==display){
                        dislikeCount = dislikeCount-1;
                    }
                   // if(dislikeCount = -1){ dislikeCount = 0;}
                    $('#like_'+opinionid+'_off').css('display','none');
                    $('#like_'+opinionid+'_on').css('display',display);
                    $('#dislike_'+opinionid+'_off').css('display',display);
                    $('#dislike_'+opinionid+'_on').css('display','none');
                    $('#disagree_count_'+opinionid).text(dislikeCount+" Disagrees");
                    $('#agree_count_'+opinionid).text(likeCount+" Agrees");
                }else{
                    alert("status"+response.status +" AD: "+response.Agree_Disagree);
                }
            }
            if(response.status=="like"){
                likeCount=likeCount-1;
                $('#like_'+opinionid+'_on').css('display','none');
                $('#like_'+opinionid+'_off').css('display',display);
                $('#agree_count_'+opinionid).text(likeCount+" Agrees");

            }

        },error:function(response){
        }
       });
    
}

function callDislikeAjax(token,opinionid,likeid,display){
    var likeCount=parseInt($('#agree_count_'+opinionid).text()); 
    var dislikeCount=parseInt($('#disagree_count_'+opinionid).text());     
    $.ajax({
        url:'/opinion/like',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        async:false,
        cache: true,
        data:{_token:token,opinion_id:opinionid ,Agree_Disagree: 0},
        dataType:'json',
        success:function(response){

            if(response.status=="liked"){
                if(response.Agree_Disagree=='0'){
                    dislikeCount = dislikeCount+1;
                    if($('#like_'+opinionid+'_on').css('display')==display){
                        likeCount=likeCount-1;
                    }
                    
                   // if(likeCount = -1){likeCount = 0;}
                    $('#dislike_'+opinionid+'_off').css('display','none');
                    $('#dislike_'+opinionid+'_on').css('display',display);
                    $('#like_'+opinionid+'_on').css('display','none');
                    $('#like_'+opinionid+'_off').css('display',display);
                    $('#disagree_count_'+opinionid).text(dislikeCount+" Disagrees");
                    $('#agree_count_'+opinionid).text(likeCount+" Agrees");
                }
            }
            if(response.status=="like"){
                dislikeCount=dislikeCount-1;
                $('#dislike_'+opinionid+'_on').css('display','none');
                $('#dislike_'+opinionid+'_off').css('display',display);
                $('#disagree_count_'+opinionid).text(dislikeCount+" Disagrees");

            }
        },error:function(response){
            
        }
       });
    }