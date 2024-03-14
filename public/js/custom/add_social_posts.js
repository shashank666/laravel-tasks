$(document).on('click', '#btnTweetLink', function() {
    var tweetLink = $('#tweetLink').val();
    var pattern = new RegExp('https://twitter.com/(.+?)/status/(.+?)', 'g')
    if((tweetLink != undefined || tweetLink != '') && tweetLink.match(pattern)){
        $.ajax({
            url: "https://api.twitter.com/1/statuses/oembed.json?url=" + tweetLink + "&hide_media=false",
            dataType: "jsonp",
            async: false,
            success: function(data) {
                appendSocialPost(data.html);
                $('#tweetLink').val('');
                $('#AddTwitterPost').modal('hide');
            }
        });
    }else{
        $('.twt-error').text('Please Enter Valid Tweet Link');
        $('#tweetLink').val('');
        $('.twt-error').show().fadeOut(5000);
    }
});

    $(document).on('click','#btnTweetEmbeded',function(){
        tweetEmbeded=$('#tweetEmbeded').val().trim();
        if((tweetEmbeded != undefined || tweetEmbeded != '') &&  tweetEmbeded.indexOf("twitter-tweet")>=0) {
            appendSocialPost(tweetEmbeded);
            $('#tweetEmbeded').val('');
            $('#AddTwitterPost').modal('hide');
        }else {
            $('.twt-error').text('Please Enter Valid Embed Tweet');
            $('#tweetEmbeded').val('');
            $('.twt-error').show().fadeOut(5000);
        }
    });

$(document).on('click', '#btnInstagramLink', function() {
    var instagramLink = $('#instagramLink').val();
    var pattern = new RegExp('https://www.instagram.com/p/(.+?)', 'g');
    if((instagramLink != undefined || instagramLink != '') &&  instagramLink.match(pattern)){
        $.ajax({
            url: "https://api.instagram.com/oembed/?maxwidth=730&url=" + instagramLink,
            dataType: "jsonp",
            async: false,
            success: function(data) {
                appendSocialPost(data.html);
                window.instgrm.Embeds.process();
                $('#instagramLink').val('');
                $('#AddInstagramPost').modal('hide');
            }
        });
    }else{
            $('.it-error').text('Please Enter Valid Instagram Link');
            $('#instagramLink').val('');
            $('.it-error').show().fadeOut(5000);
    }
});


    $(document).on('click','#btnInstagramEmbeded',function(){
        instagramEmbeded=$('#instagramEmbeded').val().trim();
        if((instagramEmbeded != undefined || instagramEmbeded != '') &&  instagramEmbeded.indexOf("instagram-media")>=0) {
            appendSocialPost(instagramEmbeded);
            window.instgrm.Embeds.process();
            $('#instagramEmbeded').val('');
            $('#AddInstagramPost').modal('hide');
        }else{
            $('.it-error').text('Please Enter Valid Instagram Embed Code');
            $('#instagramEmbeded').val('');
            $('.it-error').show().fadeOut(5000);
        }
    });



    $(document).on('click','#btnYoutubeLink',function(){
        var url=$('#youtubeLink').val();
       if (url != undefined || url != '') {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                var videoID=getParameterByName('v',url);
                var embedURL="https://www.youtube.com/embed/"+videoID;
                var videoiFrame='<div class="col-12"><iframe width="496" height="280" src="'+embedURL+'" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';
                appendSocialPost(videoiFrame);
                $('#youtubeLink').val('');
                $('#AddYoutubeVideo').modal('hide');
            }
            else {
                $('.yt-error').text('Please Enter Valid Youtube Video Link');
                $('#youtubeLink').val('');
                $('.yt-error').show().fadeOut(5000);
            }
        }
    });


    $(document).on('click','#btnYoutubeEmbeded',function(){
        var embedCode=$('#youtubeEmbeded').val().trim();
        if((embedCode != undefined || embedCode != '') && embedCode.indexOf("https://www.youtube.com/embed/") >= 0)
        {
            appendSocialPost(embedCode);
            $('#youtubeEmbeded').val('');
            $('#AddYoutubeVideo').modal('hide');
        }else{
                $('.yt-error').text('Please Enter Valid Youtube Emded Video Code');
                $('#youtubeEmbeded').val('');
                $('.yt-error').show().fadeOut(5000);
        }
    });

    $(document).on('click', '#btnOpinedEmbeded', function() {
        var opinedEmbeded = $('#opinedEmbeded').val().trim();
        var URLpattern = new RegExp('https://www.weopined.com/(.+?)/opinion/(.+?)', 'g');
        var iframePattern=new RegExp('(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))','g');

        if((opinedEmbeded != undefined || opinedEmbeded != '') &&  opinedEmbeded.match(iframePattern) &&  opinedEmbeded.match(URLpattern)){
                appendSocialPost(opinedEmbeded);
                $('#opinedEmbeded').val('');
                $('#AddOpinedOpinion').modal('hide');
        }else{
            $('.op-error').text('Please Enter Valid Embed Opinion');
            $('#opinedEmbeded').val('');
            $('.op-error').show().fadeOut(5000);
        }
    });


    function appendSocialPost(data){
        var data='<br/>'+data+'<br/>';
        CKEDITOR.instances.article_ckeditor.insertHtml(data);
        var range =  CKEDITOR.instances.article_ckeditor.createRange();
        range.moveToPosition( range.root, CKEDITOR.POSITION_BEFORE_END );
        CKEDITOR.instances.article_ckeditor.getSelection().selectRanges( [ range ] );
    }

