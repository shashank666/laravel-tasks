    <div id="AddTwitterPost" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border:0px;">
            <div class="modal-header" style="background-color:#1da1f2;color:#fff;">
              <h5 class="modal-title">Add Twitter Post <span class="ml-2"><i class="fab fa-twitter"></i></span></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li  class="nav-item"> <a class="nav-link active" href="#tab_tweet_link" role="tab" data-toggle="tab">Tweet Link<span class="ml-2"><i class="fas fa-link"></i></spa></a></li>
                    <li  class="nav-item"><a class="nav-link" href="#tab_tweet_embeded" role="tab" data-toggle="tab">Embeded Tweet<span class="ml-2"><i class="fas fa-code"></i></span></a></li>
                </ul>
                <div class="mt-2 twt-error alert alert-danger alert-dismissible fade show" role="alert" style="display:none;"></div>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active show" id="tab_tweet_link">
                            <div class="tab-body mt-4">
                                    <div class="form-group">
                                            <label for="tweetLink">Paste a tweet link here</label>
                                            <input type="url" placeholder="https://twitter.com/user/status/1234567890" class="form-control" id="tweetLink" />
                                    </div> 
                                    <div class="form-group text-center">
                                    <button type="button" class="btn btn-success" id="btnTweetLink">Add Tweet Link</button>
                                    </div>
                            </div>    
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_tweet_embeded">
                        <div class="tab-body mt-4">
                            <div class="form-group">
                                <label for="tweetEmbeded">Paste embeded tweet here</label>
                                <textarea rows="6" placeholder='<blockquote class="twitter-tweet" data-lang="en"><p dir="ltr">Tweet Here</p><a href="https://twitter.com/user/status/1234567890">June 13, 2018</a></blockquote><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>' class="form-control" id="tweetEmbeded"></textarea>
                            </div> 
                            <div class="form-group text-center">
                              <button type="button" class="btn btn-success" id="btnTweetEmbeded">Add Embeded Tweet</button>
                            </div>
                        </div>    
                    </div>
                </div>                           
            </div>
          </div>
        </div>
    </div>