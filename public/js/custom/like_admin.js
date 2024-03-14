$(document).on('click','.like',function(){
    var likeid=$(this).attr('id');    
    var opinionid=parseInt(likeid.slice(5));
    var token=$("input[name=_token]").val();
     callLikeAjax(token,opinionid,likeid,'inline');
           
    
         
});


function callLikeAjax(token,opinionid,likeid,display){
var likeCount=parseInt($('#like_count_'+opinionid).text());     
$.ajax({
        url:'/cpanel/opinion/like_opinion',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{_token:token,opinion_id:opinionid},
        dataType:'json',
        success:function(response){

            if(response.status=="liked"){
                likeCount=likeCount+1;
                 $('#like_'+opinionid+'_off').css('display','none');
                 $('#like_'+opinionid+'_on').css('display',display);
                 $('#like_count_'+opinionid).text(likeCount+" Likes");
            }
            if(response.status=="like"){
                likeCount=likeCount-1;
                $('#like_'+opinionid+'_on').css('display','none');
                $('#like_'+opinionid+'_off').css('display',display);
                $('#like_count_'+opinionid).text(likeCount+" Likes");
            }
        },error:function(response){
           
        }
       });
}