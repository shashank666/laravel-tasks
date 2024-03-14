$(document).ready(function(){

    // initialize tooltip
    $( ".viewinfo" ).tooltip({
      
        track:false,
        open: function( event, ui ) {
          var cardshow=parseInt($(this).attr('data-cardshow'));
              var id = this.id;
              var split_id = id.split('_');
              var userid = split_id[1];
              
              $.ajax({
                  url:'/user_card',
                  type:'post',
                  headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')},
                  data:{userid:userid},
                  success: function(response){
                      
                      // Setting content option
                      $(".user_"+cardshow).tooltip('option','content',response);
                        
                  }
              });
              ui.tooltip.hover(
              function () {
                $('.ui-tooltip').stop(true).fadeTo(400, 1);

               },

              function () {
                $('.ui-tooltip').fadeOut("100", function () {
                  //$( ".content span" ).attr('title','Please wait...');
                  //$( ".content span" ).tooltip();
                  //$('.ui-tooltip').stop(false).fadeTo(800, 1);
                  //$(".viewinfo").tooltip( "instance" );

                  $(this).remove();
                  //console.log("test");
                })
            }); 
        }
    });

    $(".viewinfo").mouseout(function(){
        // re-initializing tooltip
       
        $(this).attr('title','Please wait...');
        $(this).tooltip();
        $('.ui-tooltip').stop(false).fadeTo(400, 1);
        //$('.ui-tooltip').hide();
    });

    
});

