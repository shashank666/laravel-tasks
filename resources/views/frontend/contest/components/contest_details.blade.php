<!--<style>
    .swal-button {background-color: #ff9800!important;} .blog hr:first-child{margin-top:0}.blog img{max-width:660px;margin:0 15px 15px 0;position:relative}.blog img:not(:last-child),.blog p:not(:last-child),.blog table:not(:last-child){margin-bottom:24px}.blog blockquote{display:block;border-width:2px 0;border-style:solid;border-color:#eee;padding:1.5em 0;font-size: larger;text-align: center;font-style: italic;margin:1.5em 0;position:relative}.blog blockquote::before{content:'\201C';position:absolute;top:0;left:50%;transform:translate(-50%,-50%);background:#fff;width:3rem;height:2rem;font:6em/1.08em 'PT Sans',sans-serif;color:#666;text-align:center}.blog blockquote::after{content:"\2013 \2003" attr(cite);display:none;text-align:right;font-size:.875em}.blog ol,.blog pre,.blog ul{background-color:#f3f3f5;color:#212121;padding:16px}.blog ol li,.blog ul li{margin-left:16px;margin-right:16px}.blog ol li:not(:first-child),.blog ul li:not(:first-child){margin-top:8px}@media (max-width:575px){.opinion-title{font-size:1.5rem}.opinion-cover{width:100%;height:250px}.blog img{width:100%;height:250px}}@media (min-width:576px) and (max-width:767px){.opinion-title{font-size:1.75rem}.blog img{width:100%;height:250px}}@media (min-width:768px) and (max-width:991px){.opinion-title{font-size:2rem}.blog img{width:100%;height:350px}}@media (min-width:992px) and (max-width:1199px){.opinion-title{font-size:2rem}.opinion-cover{width:100%;height:350px}.blog img{max-width:100%;height:350px}}@media (min-width:1200px){.opinion-title{font-size:2.5rem}.opinion-cover{width:100%;height:450px}.blog img{max-width:100%;height:350px}
</style>-->
<style>
    .swal-button {background-color: #ff9800!important;} .blog hr:first-child{margin-top:0}.blog img{max-width:100%;margin:0 15px 15px 0;position:relative}.blog img:not(:last-child),.blog p:not(:last-child),.blog table:not(:last-child){margin-bottom:24px}.blog blockquote{display:block;border-width:2px 0;border-style:solid;border-color:#eee;padding:1.5em 0;font-size: larger;text-align: center;font-style: italic;margin:1.5em 0;position:relative}.blog blockquote::before{content:'\201C';position:absolute;top:0;left:50%;transform:translate(-50%,-50%);background:#fff;width:3rem;height:2rem;font:6em/1.08em 'PT Sans',sans-serif;color:#666;text-align:center}.blog blockquote::after{content:"\2013 \2003" attr(cite);display:none;text-align:right;font-size:.875em}.blog ol,.blog pre,.blog ul{background-color:#f3f3f5;color:#212121;padding:16px}.blog ol li,.blog ul li{margin-left:16px;margin-right:16px}.blog ol li:not(:first-child),.blog ul li:not(:first-child){margin-top:8px}@media (max-width:575px){.opinion-title{font-size:1.5rem}.opinion-cover{width:100%;height:250px}.blog img{width:100%;}}@media (min-width:576px) and (max-width:767px){.opinion-title{font-size:1.75rem}.blog img{width:100%;}}@media (min-width:768px) and (max-width:991px){.opinion-title{font-size:2rem}.blog img{width:100%;}}@media (min-width:992px) and (max-width:1199px){.opinion-title{font-size:2rem}.opinion-cover{width:100%;height:350px}.blog img{max-width:100%;}}@media (min-width:1200px){.opinion-title{font-size:2.5rem}.opinion-cover{width:100%;height:450px}.blog img{max-width:100%;}
</style>
<style type="text/css">
    .medium-insert-images.medium-insert-images-left{    
    max-width: 33.33%;
    float: left;
    margin: 0 30px 0 0;
    text-align: center;
    }
    .medium-insert-images figure{
        position: relative;
    }
    .medium-insert-images.medium-insert-images-right{    
    max-width: 33.33%;
    float: right;
    margin: 0 20px 0 30px;
    text-align: center;
    }
    .medium-insert-images-grid{
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: start;
    align-items: flex-start;
    -ms-flex-pack: center;
    justify-content: center;
    margin: 0.5em -0.5em;
    }
    .medium-insert-images-grid figure{
    width: 33.33%;
    display: inline-block;
    }
    .medium-insert-images-grid figure img{
    max-width: calc(100% - 1em);
    margin: 0.5em;
    }
    body{
        font-size: 1.2rem;
    }
    ::-moz-selection { /* Code for Firefox */
      
      background: #ff980052;
    }

    ::selection {
      
      background: #ff980052;
    }
</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
        $(window).on('load',function(){
            $('.blog-post span').removeAttr('style');
        });
        $(document).ready(function(){
            $(".disabled").click(function(){
            swal("We are sorry!", "Please Fill All The Required Fields Before Publishing The Article", "error");
            });
    });
</script>

    <div class="blog align-items-center ">
            <!-- Image location to be changed -->

                    
                    <img class="img-fluid border-bottom pt-3 pb-3 rounded opinion-cover lazy" src="/img/noimg.png" data-src="{{$contest->image}}"  alt="{{$contest->title}}" onerror="this.onerror=null;this.src='/img/noimg.png';"/>
    <!--<img class="img-fluid border-bottom pt-3 pb-3  rounded opinion-cover" src="{{$contest->image}}" alt="{{$contest->title}}" onerror="this.onerror=null;this.src='/img/noimg.png';" />--> 

    <h1 class="mt-4 opinion-title" style="font-family: 'Lora', serif;">{{$contest->title}}</h1>

    <div class="mb-3 mt-2 text-secondary border-bottom border-top pt-3 pb-3 d-flex flex-md-row flex-column justify-content-between">
       <span><h6>Start Date:</h6> <i class="far fa-calendar-alt mr-2"></i>{{$contest->start_date}}</span>
    </div>

    <div class="mb-3 mt-2 text-secondary border-bottom border-top pt-3 pb-3 d-flex flex-md-row flex-column justify-content-between">
        <span><h6>End Date: </h6> <i class="far fa-calendar-alt mr-2"></i>{{$contest->end_date}}</span>
    </div>
 
    <div class="lead d-flex flex-md-row flex-column align-items-center justify-content-between">
       
        <div class="text-md-left text-sm-center">
            
            <span class="ml-3" style="font-size:18px;font-weight:bold;color:#244363;">
                @if($contest->sharesCount<2)
                <span>{{ $contest->sharesCount}} Share</span>
                @else
                <span>{{ $contest->sharesCount}} Shares</span>
                @endif
            </span>
        </div>

        <div class="social-share text-center mt-md-0 mt-4 share-menu" data-post="{{$contest->id}}">
        
        @if(Auth::user())
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$contest->title}}&url=https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$contest->slug}}&title={{ $contest->title }}&source=Opined" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$contest->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-user="{{Auth::user()->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @else
        <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2 sharethis" target="_blank" role="button" href="https://twitter.com/share?text={{$contest->title}}&url=https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-linkedin waves-effect waves-circle waves-float mr-2 sharethis"  target="_blank" role="button" href="https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/{{$contest->slug}}&title={{ $contest->title }}&source=Opined" data-post="{{$contest->id}}" data-plateform="LINKEDIN"><span><i class="fab fa-linkedin-in"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float sharethis"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$contest->title}} .....Read more at Opined : https://www.weopined.com/opinion/{{$contest->slug}}" data-post="{{$contest->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        @endif
        </div>
    </div> 

    <hr>
    <input type="hidden" name="contest_id" id="contest_id" value="{{$contest->id}}">
    <div class="text-justify blog-post" style="font-family: 'Lora', serif;">
        @php
        $str_body = "$contest->body";
        $arr_body = (explode("<p>",$str_body));
        $ad= '<div class="mb-3 rsm_opined">
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle"
                     style="display:block; text-align:center;"
                     data-ad-layout="in-article"
                     data-ad-format="fluid"
                     data-ad-client="ca-pub-9171805278522999"
                     data-ad-slot="1338547262"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script></div>';
            array_splice( $arr_body, 3, 0, $ad );
            array_splice( $arr_body, 6, 0, $ad );
           echo implode(" ",$arr_body) 
        @endphp
    {{--{!!$contest->str_body!!}--}}
    </div>
    <input type="hidden" id="contestid" value="{{$contest->id}}" />
</div>



