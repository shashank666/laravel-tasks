
/*---------------------FUNCTIONS FOR FILE CHOOSER -------------------*/

var finalFiles = {};

$(document).on('change','#file',function(e){
    finalFiles = {};
     i = 0;
    var files = e.target.files; //FileList object
     if ($('#file')[0].files.length>3){
        ShowMessageModal('Maximum 3 Photoes Allowed','You are not allowed to add more than 3 photoes.');
    }else{
        if($('#type').val()=='GIF' || $('#type').val()=='YOUTUBE'){
            $('#previewMedia').empty();
        }
        $('#previewMedia').css('display','');
        $('#type').val('IMAGE');
        $.each(files, function(i, file) {
        finalFiles[i]=file;
        var reader = new FileReader();
        reader.onload = function (e) {
         var img='<div class="col-lg-4 col-sm-6 col-12"  id="file_'+ i +'"><div class="card mb-2"><img src="'+e.target.result+'" style="height:150px;" class="rounded"><div class="card-img-overlay p-0"><button type="button" class="deleteFile btn btn-sm float-right text-white bg-danger py-1 px-2 rounded" aria-label="Close"><i class="fas fa-times"></i></button></div></div></div>';
         $('#previewMedia').append(img);
        }
        reader.readAsDataURL(file);
        });
    }
});


$(document).on('click','.deleteFile',function()
{
 var container = $(this).parents().get(2);
 var c_id=container.id;
 var index = c_id.split('_')[1];
  container.remove(); 
  delete finalFiles[index];
  //console.log($('#file').prop("files"));
});


function openFileChooser(){
    if($('#previewMedia').children('div').length < 3){
        $('#file').click();
    }else{
       ShowMessageModal('Maximum 3 Photoes Allowed','You can add maximum 3 photoes.');
    }
}

/*---------------------END OF FUNCTIONS FOR FILE CHOOSER -------------------*/



/*---------------------FUNCTIONS FOR YOUTUBE VIDEO MODAL -------------------*/

function openAddYoutubeVideoModal(){
    $('#add_youtube_video').modal('show');
}

function AddYouTubeVideo(){
    var url=$('#youtube_url').val();
       if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                var videoID=getParameterByName('v',url);
                var embedURL="https://www.youtube.com/embed/"+videoID;
                var videoiFrame='<div class="col-12"><iframe width="496" height="280" src="'+embedURL+'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
                $('#type').val('YOUTUBE');
                $('#cover').val(videoID);
                $('#previewMedia').empty();
                $('#previewMedia').append(videoiFrame);
                $('#previewMedia').css('display','block');
                $('#add_youtube_video').modal('hide');
                $('#youtube_url').val('');
            }
            else {
                ShowMessageModal('Invalid URL','Please add valid youtube video URL.');
            }
        }
}

/*--------------------END OF FUNCTIONS FOR YOUTUBE VIDEO MODAL--------------*/

/*--------------------FUNCTIONS FOR ADD EMBED CODE MODAL--------------------*/
function openAddEmbedCodeModal(){
    $('#add_embed_code').modal('show');
}

function AddEmbedCode(){
    var embedCode=$('#embed-code').val();
    console.log(embedCode);
    var iframePattern=new RegExp('(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))','g');
    var scriptPattern=new RegExp('(?:<script[^>]*)(?:(?:\/>)|(?:>.*?<\/script>))','g');
    if(embedCode.length>0 && (iframePattern.test(embedCode) || scriptPattern.test(embedCode))){
        var appendDiv='<iframe id="preview-embed" width="100%" height="100%"  style="min-height:500px;width:100%;height:100%;" frameBorder="0" scrolling="yes"></iframe>'
        $('#type').val('EMBED');
        $('#cover').val(embedCode);
        $('#previewMedia').empty();
        $('#previewMedia').append(appendDiv);
        $("#preview-embed").ready(function() {
            iframeDoc = document.getElementById('preview-embed').contentDocument;
            iframeDoc.open();
            iframeDoc.write(embedCode);
            iframeDoc.close();
        });
        $('#previewMedia').css('display','block');
        $('#add_embed_code').modal('hide');
        $('#embed-code').val('');
    }else{
         ShowMessageModal('Invalid Embed Code','Please add valid embed code.');
    }
}
/*-----------------END OF FUNCTIONS FOR ADD EMBED CODE MODAL--------------------*/


$(document).on('click','#btn_post',function(){
    if(au==0){
        $('#forgotPasswordModal').modal('show');
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                postOpinion();
            }
        }
    
        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                postOpinion();
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
                postOpinion();
            }
        }  
    } 
});

function postOpinion(){
    if($('#type').val()=='IMAGE'){
        var val="";
        var arr=[];
         $.each(finalFiles,function(i,file){
             arr.push(file.name);
         });
        var str=arr.join(",");
        $('#cover').val(str);
        $('#opinion_form').submit();
   }else{
      $('#opinion_form').submit();
   }
}

function getHashTags(inputText) {
    //var regex = /(?:^|\s)(?:#)([a-zA-Z\d]+)$/gm;
    var regex=/#\S+/g;
    var matches = [];
    matches=inputText.match(regex);
    return matches;
}

$(document).on('click','#btn_submit_opinion',function(){
   // var pattern = new RegExp(/(^|\s)#(\w+)/gi);
   if(au==0){
    $('#forgotPasswordModal').modal('show');
    }else{
        if(au==1 && cev==1 && cmv==0){
            if(ev==0){
                openVerifyEmailMobileModal(verification[0]['title'],verification[0]['text'],verification[0]['buttons']);
            }else{
                submitOpinion();
            }
        }

        if(au==1 && cev==0 && cmv==1){
            if(mv==0){
            openVerifyEmailMobileModal(verification[1]['title'],verification[1]['text'],verification[1]['buttons']);
            }else{
                submitOpinion();
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
                submitOpinion();
            }
        }  
    }
});

$(document).on('click','#btn_admin_submit_opinion',function(){
   // var pattern = new RegExp(/(^|\s)#(\w+)/gi);
   var hashtags=getHashTags($('#write_opinion').val().trim());
    if (hashtags && hashtags.length>0) {
        var FinalArr=[];
        for(var i=0;i<hashtags.length;i++){
            if(hashtags[i].length<4){
                FinalArr.push(0);
                ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
            }else{
                FinalArr.push(1);
            }
        }

         if(!FinalArr.includes(0)){
           
             if($('#type').val()=='IMAGE'){
                var val="";
                var arr=[];
                 $.each(finalFiles,function(i,file){
                     arr.push(file.name);
                 });
                var str=arr.join(",");
                $('#cover').val(str);
                $('#write_thread').submit();
           }else{
              $('#write_thread').submit();
           } 
        } 
    }else{
        ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
    }
});


function submitOpinion(){
    var hashtags=getHashTags($('#write_opinion').val().trim());
    if (hashtags && hashtags.length>0) {
        var FinalArr=[];
        for(var i=0;i<hashtags.length;i++){
            if(hashtags[i].length<4){
                FinalArr.push(0);
                ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
            }else{
                FinalArr.push(1);
            }
        }

         if(!FinalArr.includes(0)){
           
             if($('#type').val()=='IMAGE'){
                var val="";
                var arr=[];
                 $.each(finalFiles,function(i,file){
                     arr.push(file.name);
                 });
                var str=arr.join(",");
                $('#cover').val(str);
                $('#write_thread').submit();
           }else{
              $('#write_thread').submit();
           } 
        } 
    }else{
        ShowMessageModal('Please Enter Valid #Thread ','Please enter valid thread by typing # of minimum 3 characters.');
    }
}

$(document).on('click','.like_thread',function(){
   var thread_id=$(this).attr('data-thread'); 
   var like_count=parseInt($('#thread_likes_count').text());
   $.ajax({
      url:'/thread/like',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      type:'POST',
      data:{id:thread_id},
      dataType:'json',
      success:function(response){
          if(response.status=='liked'){
            $('.like_thread_off_'+thread_id).css('display','none');
            $('.like_thread_on_'+thread_id).css('display','inline');
            like_count=like_count+1;
            $('#thread_likes_count').text(like_count);
          } else{
            $('.like_thread_off_'+thread_id).css('display','inline');
            $('.like_thread_on_'+thread_id).css('display','none');
            like_count=like_count-1;
            $('#thread_likes_count').text(like_count);
          }
      },error:function(error){

      }  
   });
});

$(document).on('click','.follow_thread',function(){
    var thread_id=$(this).attr('data-thread'); 
    var follower_count=parseInt($('#thread_followers_count').text());
    $.ajax({
       url:'/opinion/thread/follow',
       headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
       type:'POST',
       data:{id:thread_id},
       dataType:'json',
       success:function(response){
           if(response.status=='followed'){
             $('.follow_thread_off_'+thread_id).css('display','none');
             $('.follow_thread_on_'+thread_id).css('display','inline');
             follower_count=follower_count+1;
            $('#thread_followers_count').text(follower_count);
           } else{
             $('.follow_thread_off_'+thread_id).css('display','inline');
             $('.follow_thread_on_'+thread_id).css('display','none');
             follower_count=follower_count-1;
             $('#thread_followers_count').text(follower_count);
           }
       },error:function(error){
 
       }  
    });
});

