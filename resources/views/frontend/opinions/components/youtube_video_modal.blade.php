
<!--Add Youtube Video Modal -->
<div class="modal fade" id="add_youtube_video" tabindex="-1" role="dialog" aria-labelledby="add_youtube_video" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_youtube_video">Add Youtube Video
        <span class="ml-2"><a href="https://www.youtube.com" target="_blank" class="text-danger"><i class="fab fa-youtube"></i></a></span>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       
        <div class="form-row">
            <div class="col-md-10">
                <div class="form-group">
                    <input type="url" class="form-control" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=abcdefg"/> 
                </div>
            </div>
            <div class="col-md-2">
            <button class="btn btn-primary" id="btnAddYouTubeVideo" onclick="AddYouTubeVideo();">Add</button>
            </div>
        </div>
        <div class="error">
        
        </div>

      </div>
    </div>
  </div>
</div>
