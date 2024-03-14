$(document).on('click', '.btn_delete_post', function() {
    var deleteid = $(this).attr('id').slice(7);
    var postid = $(this).attr('name').slice(7);
    $('#del_id').val(deleteid);
    $('#op_id').val(postid);
    $('#deleteOpinionModal').modal('show');
});


$(document).on('click', '.finaldelete', function() {
    var postid = $('#op_id').val();
    var deleteid = $('#del_id').val();
    $.ajax({
        url: "/opinion/delete",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        data: { deleteid: deleteid, _method: 'DELETE' },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                $('#post-' + postid).remove();
                $('#deleteOpinionModal').modal('hide');
            }
        },
        error: function(response) {
            $('#deleteOpinionModal').modal('hide');
        }
    });
});
