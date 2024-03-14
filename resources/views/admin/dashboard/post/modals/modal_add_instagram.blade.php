<div id="AddInstagramPost" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border:0px;">
            <div class="modal-header" style="background-color:#c13584;color:#fff;">
              <h5 class="modal-title">Add Instagram Post <span class="ml-2"><i class="fab fa-instagram"></i></span></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li  class="nav-item"> <a class="nav-link active" href="#tab_instagram_link" role="tab" data-toggle="tab">Post Link<span class="ml-2"><i class="fas fa-link"></i></span></a></li>
                    <li  class="nav-item"><a class="nav-link" href="#tab_instagram_embeded" role="tab" data-toggle="tab">Embeded Post<span class="ml-2"><i class="fas fa-code"></i></span></a></li>
                </ul>
                <div class="mt-2 it-error alert alert-danger alert-dismissible fade show" role="alert" style="display:none;"></div>
              
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active show" id="tab_instagram_link">
                            <div class="tab-body mt-4">
                                    <div class="form-group">
                                            <label for="instagramLink">Paste a instagram post link here</label>
                                            <input type="url" placeholder="https://www.instagram.com/p/asdf1234/" class="form-control" id="instagramLink" />
                                    </div> 
                                    <div class="form-group text-center">
                                    <button type="button" class="btn btn-success" id="btnInstagramLink">Add Instagram Link</button>
                                    </div>
                            </div>    
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_instagram_embeded">
                        <div class="tab-body mt-4">
                            <div class="form-group">
                                <label for="instagramEmbeded">Paste embeded code here</label>
                                <textarea rows="8" placeholder='<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/p/asdf1234/" data-instgrm-version="8" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:658px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:8px;"> <div style=" background:#F8F8F8; line-height:0; margin-top:40px; padding:62.5% 0; text-align:center; width:100%;"> <div style=" background:url(data:image/png;base64); display:block; height:44px; margin:0 auto -44px; position:relative; top:-22px; width:44px;"></div></div> <p style=" margin:8px 0 0 0; padding:0 4px;"> <a href="https://www.instagram.com/p/asdf1234/" style=" color:#000; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none; word-wrap:break-word;" target="_blank"></a></p> <p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;">A post shared by <a href="https://www.instagram.com/user" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px;" target="_blank"></a>  on <time style=" font-family:Arial,sans-serif; font-size:14px; line-height:17px;"></time></p></div></blockquote> <script async defer src="//www.instagram.com/embed.js"></script>' class="form-control" id="instagramEmbeded"></textarea>
                            </div> 
                            <div class="form-group text-center">
                              <button type="button" class="btn btn-success" id="btnInstagramEmbeded">Add Embeded Instragram Post</button>
                            </div>
                        </div>    
                    </div>
                </div>                           
            </div>
          </div>
        </div>
    </div>