
$(document).on('click', '.sharethis', function() {
        var opinion_id = parseInt($(this).attr('data-opinion'));
        var plateform = $(this).attr('data-plateform');
        var user_id = parseInt($(this).attr('data-user'));
        var post_id = parseInt($(this).attr('data-post'));
          $.ajax({
                url: '/share_count_update',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'POST',
                data: { opinion_id: opinion_id,user_id: user_id,post_id: post_id,plateform: plateform },
                dataType: 'text',
                success: function(response) {
                    
                },
                error: function() {
                    
                }
            });
        });

