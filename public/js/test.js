$(document).ready(function(){
  
            $('#title_span').hide();
               $('#body_span_min').hide();
              $('#body_span_max').hide();
              $('.medium-insert-buttons').addClass('hidethis');
            $.fn.checkValidation = function(){
            var regex = "/ +(?= )| $/g";
            var $numOfWords = $('#new-editor').text().replace(regex, ' ').split(' ').length;
            var $no_chars = $('#new-editor-title').text().length;
            var $coverPreview = $('img.previewAuth').attr('src');
            var strCheck = "storage/cover";
            var $categories = $('#category').val();
            if($numOfWords<29 || $numOfWords>1195 || $no_chars<1 || $coverPreview.indexOf(strCheck) == -1 || $categories<1){
                $('#btnPublish').addClass('disabled');
                }
             else{
                $('#btnPublish').removeClass('disabled');
             }
         }
         $.fn.checkValidation();
          $('#new-editor-title').hover(
             function(){ $('.medium-insert-buttons').addClass('hidethis') }
              )
            $( "#new-editor-title" ).keyup(function() {
               $('.medium-insert-buttons').addClass('hidethis');
            var $title_demo = $('#new-editor-title').text();

            var lastIndex = $title_demo.lastIndexOf("+");

            $title = $title_demo.substring(0, lastIndex);
            
            if($title.length>98){
              $('#title_span_max').show();
               $('#title_span_min').hide();
            }
            else if($title.length<1){
              $('#title_span_min').show();
              $('#title_span_max').hide();
            }
            else{
              $('#title_span_min').hide();
               $('#title_span_max').hide();
            }
            $('#title').val($title);
            $.fn.checkValidation();
            });
            
            $( "#new-editor" ).mouseover(function(){
              var $body_demo = $('#new-editor').html();
              var lastIndex = $body_demo.lastIndexOf("+");

            $body = $body_demo.substring(0, lastIndex);
            
              $('#article_ckeditor').val($body);
              $.fn.checkValidation();
            });
            $( "#new-editor" ).mouseout(function(){
              var $body = $('#new-editor').html();
              $('#article_ckeditor').val($body);
              $.fn.checkValidation();
            });
            $( "#new-editor" ).keyup(function() {
              $('.medium-insert-buttons').removeClass('hidethis');
            var $body = $('#new-editor').html();
            var regex = "/ +(?= )| $/g";
            var $numOfWords = $('#new-editor').text().replace(regex, ' ').split(' ').length;
            var $plainbody_demo = $('#new-editor').text();
             var lastIndex = $plainbody_demo.lastIndexOf("+");
             $plainbodyauto = $plainbody_demo.substring(0, lastIndex);
             $('#plainbodyauto').val($plainbodyauto);
            if($numOfWords<298){
              $('#body_span_min').show();
              $('#body_span_max').hide();
              
            }
            else if($numOfWords>1195){
              $('#body_span_min').hide();
              $('#body_span_max').show();
              
            }
            else{
              $('#body_span_min').hide();
              $('#body_span_max').hide();
              
            }
            $('#article_ckeditor').val($body);
            $.fn.checkValidation();
            
            });
            $('#btnPublish').on('click',function(e){
                e.preventDefault();
                if( $(this).hasClass('disabled') ){
                    
                    alert('Fill all the mandatory fields and be sure that your article should follow our Guidelines');
                }else{
                  $('.beforesubmit').hide();
                  $('.aftersubmit').show();
                  $('#autoSave').hide();
                    $('#status').val('2');
                    var $plainbody_demo = $('#new-editor').text();
                     var lastIndex = $plainbody_demo.lastIndexOf("+");
                     $plainbody = $plainbody_demo.substring(0, lastIndex);
                    $('#plainbody').val($plainbody);
                    var post_id = $('#post_id').val();
                    $("#create_post_form").submit();
                }
                });
            $('#btnSaveDraft').on('click',function(e){
                e.preventDefault();
                $('.beforedraft').hide();
                  $('.afterdraft').show();
                  $('#autoSave').hide();
                    $('#status').val('0');
                    var $plainbody_demo = $('#new-editor').text();
                     var lastIndex = $plainbody_demo.lastIndexOf("+");
                     $plainbody = $plainbody_demo.substring(0, lastIndex);
                    $('#plainbody').val($plainbody);
                    $("#create_post_form").submit();
                
             });
              // ensures this works for some older browsers
              MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
              // the element you want to observe. change the selector to fit your use case
              var img = document.querySelector('img.previewAuth')
               new MutationObserver(function onSrcChange(){
                // src attribute just changed!!! put code here
                $.fn.checkValidation();
              })
                .observe(img,{attributes:true,attributeFilter:["src"]})

              $("#category").change(function(){
                  //var selected = $('#category option:selected').val();
                  //alert(selected);
                  $.fn.checkValidation(); 
                          
              });

});

