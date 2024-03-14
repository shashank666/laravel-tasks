<div id="AddYoutubeVideo" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border:0px;">
            <div class="modal-header" style="background-color:#d32f2f;color:#fff;">
              <h5 class="modal-title">Add Youtube Video <span class="ml-2"><i class="fab fa-youtube"></i></span></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li  class="nav-item"> <a class="nav-link active" href="#tab_youtube_link" role="tab" data-toggle="tab">Video Link<span class="ml-2"><i class="fas fa-link"></i></spa></a></li>
                    <li  class="nav-item"><a class="nav-link" href="#tab_youtube_embeded" role="tab" data-toggle="tab">Embeded Code<span class="ml-2"><i class="fas fa-code"></i></span></a></li>
                </ul>
                <div class="mt-2 yt-error alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
                  </div>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active show" id="tab_youtube_link">
                            <div class="tab-body mt-4">
                                    <div class="form-group">
                                            <label for="youtubeLink">Paste a youtube video link here</label>
                                            <input type="url" placeholder="https://www.youtube.com/watch?v=asdf1234" class="form-control" id="youtubeLink" />
                                    </div> 
                                    <div class="form-group text-center">
                                    <button type="button" class="btn btn-success" id="btnYoutubeLink">Add Youtube Link</button>
                                    </div>
                            </div>    
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_youtube_embeded">
                        <div class="tab-body mt-4">
                            <div class="form-group">
                                <label for="youtubeEmbeded">Paste embeded video code here</label>
                                <textarea rows="4" placeholder='<iframe width="560" height="315" src="https://www.youtube.com/embed/asdf1234" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>' class="form-control" id="youtubeEmbeded"></textarea>
                            </div> 
                            <div class="form-group text-center">
                              <button type="button" class="btn btn-success" id="btnYoutubeEmbeded">Add Embeded Youtube Video</button>
                            </div>
                        </div>    
                    </div>
                </div>                           
            </div>
          </div>
        </div>
    </div>