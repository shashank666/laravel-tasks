<div class="card box-shadow mb-3">
     <div class="card-body "  style="padding-bottom: 3px;">
         <form id="opinion_form" role="form"  action="{{route('write_short_opinion')}}" method="POST" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                 <div class="row">
                 <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12">
                    <textarea name="body" class="form-control create_opinion" id="write_opinion" rows="3" placeholder="Write Your Opinion About {{$thread->name}}" onfocus="this.placeholder = ''"onblur="this.placeholder = 'Write Your Opinion About {{$thread->name}}'" maxlength="240" required></textarea>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 d-xl-inline d-lg-inline d-md-inline d-sm-none d-none">

                <input type="hidden" id="type" name="type" value="none"/>
                <input type="hidden" id="thread" name="thread" value="{{$thread->id}}"/>
                <input type="hidden" id="cover" name="cover" />
                <input type="file" accept=".jpg,.jpeg,.png,.gif" id="file" name="files[]" style="display:none;outline:none;" multiple/>


                
                    <span style="font-size:1.5vw;" id="btn_add_image" class="mr-2 text-primary" data-toggle="tooltip" title="Add Photos" data-placement="top" onclick="openFileChooser();"><i class="far fa-image" style="color:#495057"></i></span>
                    <span style="font-size:1.4vw;" id="btn_add_gif" class="mr-2 text-success" data-toggle="tooltip" title="Add GIF" data-placement="top" onclick="openAddGIFModal('writeopinion');"><i class="far fa-sticky-note" style="color:#495057"></i></span>
                     <!--<span style="font-size:24px;" id="btn_add_youtube" class="mr-2 text-danger" data-toggle="tooltip" title="Add YouTube Video" data-placement="top" onclick="openAddYoutubeVideoModal();"><i class="fab fa-youtube"></i></span>-->
                     <span style="font-size:1.3vw;" id="btn_add_embed" class="mr-2 text-dark" data-toggle="tooltip" title="Add Embed Code" data-placement="top" onclick="openAddEmbedCodeModal();"><i class="fas fa-code" style="color:#495057"></i></span>
                    <button type="button" id="btn_post" style="width: 80%;height: 40%;margin-top: 16px;margin-right: 16px;" class="btn btn-sm btn-success float-right">Post <i class="fas fa-paper-plane"></i></button>
                 </div>
                <!-- FOR MOBILE -->

                     <div class="col-sm-12 col-12 d-xl-none d-lg-none d-md-none d-sm-inline d-inline">
                   
                     <span style="font-size:2rem;" id="btn_add_image" class="mr-2 text-primary" data-toggle="tooltip" title="Add Photos" data-placement="top" onclick="openFileChooser();"><i class="far fa-image" style="color:#495057"></i></span>
                    <span style="font-size:1.9rem;" id="btn_add_gif" class="mr-2 text-success" data-toggle="tooltip" title="Add GIF" data-placement="top" onclick="openAddGIFModal('writeopinion');"><i class="far fa-sticky-note" style="color:#495057"></i></span>
                     <!--<span style="font-size:24px;" id="btn_add_youtube" class="mr-2 text-danger" data-toggle="tooltip" title="Add YouTube Video" data-placement="top" onclick="openAddYoutubeVideoModal();"><i class="fab fa-youtube"></i></span>-->
                     <span style="font-size:1.8rem;" id="btn_add_embed" class="mr-2 text-dark" data-toggle="tooltip" title="Add Embed Code" data-placement="top" onclick="openAddEmbedCodeModal();"><i class="fas fa-code" style="color:#495057"></i></span>
                    <button type="button" id="btn_post" style="width: 84px;height: 36px;margin-top: 16px;margin-right: 16px;" class="btn btn-sm btn-success float-right">Post <i class="fas fa-paper-plane"></i></button>
                </div>   
                </div>  
            </div>
                 <!-- END FOR MOBILE -->
            <div class="row" id="previewMedia" style="display:none"></div>
        </form>
     </div>
</div>
