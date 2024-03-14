$(document).on('click','.followbtn',function(){
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
    userid=$(this).attr('data-userid');
    button='.followbtn_'+userid;
    $(button).attr('disabled','disabled');
    manageFollow(userid,button);
    }
});

$(document).on('click','.followingbtn',function(){
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
    userid=$(this).attr('data-userid');
    button='.followingbtn_'+userid;
    manageFollow(userid,button);
    }
});



function manageFollow(userid,button){
   var token=$("input[name=_token]").val();
    $.ajax({
        url:'/me/manage_follow',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{_token:token,userid:userid},
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