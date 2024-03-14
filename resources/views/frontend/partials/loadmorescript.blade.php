<script>
    window.loading  = false;
    window.totalpage = parseInt($('#totalpage').val());
    if(window.totalpage==1){
        window.page=1;
        window.hasMore = false;
    }else{
        window.page=2;
        window.hasMore = true;
    }


    $(window).scroll(function() {
        if (loading || !hasMore) return;
        if ($(window).scrollTop() + $(window).height() > $(document).height()-500){
            loadResults(page);
        }

    });

    function loadResults(page) {
            window.loading = true;
            $('#spinner').css('display','block');
           $.ajax({
                url:"/"+$('#path').val()+"/?page="+page,
                type: "GET",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType: "json",
                success: function(response, textStatus, xhr) {
                    if(xhr.status==200){
                        window.loading = false;
                        $('#spinner').css('display','none');
                        if(page<totalpage){
                            window.page++;
                            window.hasMore=true;
                        }else{
                            window.hasMore=false;
                        }
                        $('#append-div').append(response.html);
                    }else{
                        $('#spinner').css('display','none');
                        window.loading = false;
                        return;
                    }
                },
                error:function(){
                    $('#spinner').css('display','none');
                     window.loading = false;
                }
            });
    }
</script>
