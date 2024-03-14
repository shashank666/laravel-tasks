<!--GIF Modal -->
<div class="modal fade" id="add_gif" tabindex="-1" role="dialog" aria-labelledby="add_gif" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_gif">Add GIF</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <div class="modal-body" style="max-height:500px;overflow-y:scroll;">
           <div class="form-group">
           <input class="form-control" type="text" placeholder="Search GIF..." id="search_gif"/> 
           </div>
           @include('frontend.partials.loader')
           <div id="GIFcategories"></div>
           <div id="GIFimages" style="display:none"></div>
           <button class="btn btn-primary btn-block" style="display:none" id="loadMoreGIF" data-url="none" data-pos="none">Load More</button>
        </div>
    </div>
  </div>
</div>