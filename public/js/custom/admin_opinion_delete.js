$(document).on('click','.delete',function(){
    var deleteBtnID=$(this).attr('id');
    var deleteID=deleteBtnID.slice(7);
    $('#delete_id').val(deleteID);
    $('#deleteMyOpinion').modal('show');
});

$(document).on('click','.finaldelete',function(){
    var deleteid=$('#delete_id').val();
    $.ajax({
        url:"/cpanel/thread/view/"+deleteid+"/delete",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{id:deleteid},
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

$(document).on('click','.disable',function(){
    
    var disableid=parseInt($(this).attr('data-id'));
    $.ajax({
        url:"/cpanel/thread/view/"+disableid+"/update",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type:'POST',
        data:{id:disableid},
        dataType:'json',
        success:function(response){
          if(response.status=='success'){

            if(response.active=='disable'){
                $('#disabled_'+disableid).css({"background-color":"#f6c343","color":"#000","border": "transparent"});
                $('#disabled_'+disableid).text("Disable Opinion");
                $('#activity_'+disableid).text("Active");
                $('#activity_'+disableid).css({"background-color":"#00d97e"});
            }
            if(response.active=='enable'){
                $('#disabled_'+disableid).css({"background-color":"#00d97e","color":"#fff","border": "transparent"});
                $('#disabled_'+disableid).text("Enable Opinion");
                $('#activity_'+disableid).text("Disabled");
                $('#activity_'+disableid).css({"background-color":"#e63757"});
                
            }
            
           }
        },error:function(response){
            
        }
    });
});

