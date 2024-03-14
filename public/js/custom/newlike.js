$(document).on('click','.like',function(){
    var likeid=$(this).attr('id');    
    
    var opinionid=parseInt(likeid.slice(5));
    var token=$("input[name=_token]").val();
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
        $.ajax({
        url:'/opinion/like',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{},
        dataType:'json',
        success:function(response){
            // if(response.status=="liked"){
            //     disagreeCount = disagreeCount-1;
            //     likeCount=likeCount+1;
            //     if(disagreeCount = -1){disagreeCount = 0;}
            //      $('.likeComment_'+commentID+'_off').css('display','none');
            //      $('.likeComment_'+commentID+'_on').css('display','inline');
            //      $('.disagreeComment_'+commentID+'_on').css('display','none');
            //      $('.disagreeComment_'+commentID+'_off').css('display','inline');
            //      $('.comment_likes_count_'+commentID).text(likeCount+' Agrees');
            //      $('.comment_disagree_count_'+commentID).text(disagreeCount+' Disagrees');
            //      //location.reload();
            // }
            // if(response.status=="like"){
            //     likeCount=likeCount-1;
            //     $('.likeComment_'+commentID+'_on').css('display','none');
            //     $('.likeComment_'+commentID+'_off').css('display','inline');
            //     $('.comment_likes_count_'+commentID).text(response.count+' Agrees');
            // }
            alert("new Like Initiated");
        },error:function(response){

        }
       });
    }
});