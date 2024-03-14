$(document).on('click','.delete',function(){
    var deleteBtnID=$(this).attr('id');
    var deleteID=deleteBtnID.slice(7);
    $('#delete_id').val(deleteID);
    $('#deleteMyOpinion').modal('show');
});

$(document).on('click','.finaldelete',function(){
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
