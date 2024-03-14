<div class="row">
    <div class="offset-md-2 col-md-8 col-12">
      <h5 class="pb-2 my-4 font-weight-normal" style="color:#244363;border-bottom:2px solid black;">Activity</h5>    <div class="container">
        <div class="container">
        <div class="row">
          <div class="col-4">
            <div class="row" style="justify-content: center; font-size: 2rem;">
              <p id="nbr" style="margin-bottom: 0rem;">{{count($short_opinions)}}</p>
            </div>
            <div class="row" style="justify-content: center;">
               <a href="{{route('user_opinions',['username' => $profile_user->username])}}"><p style="color: black !important;">Opinions</p></a>
            </div>
          </div>
          <div class="col-4">
            <div class="row" style="justify-content: center; font-size: 2rem;">
              <p id="followers" style="margin-bottom: 0rem;">{{count($followers)}}</p>
            </div>
            <div class="row" style="justify-content: center;">
              <a href="{{route('user_in_circle',['username' => $profile_user->username])}}"><p style="color: black !important;">Followers</p></a>
            </div>
          </div>
          <div class="col-4">
            <div class="row" style="justify-content: center; font-size: 2rem;">
              <p id="following" style="margin-bottom: 0rem;">{{count($following)}}</p>
            </div>
            <div class="row" style="justify-content: center;">
              <a href="{{route('user_circle',['username' => $profile_user->username])}}"><p style="color: black !important;">Following</p></a>
            </div>
          </div>
          {{--  <div class="col-4">
            <div class="row" style="justify-content: center; font-size: 2rem;">
              <p id="art" style="margin-bottom: 0rem;">{{ count($posts) }}</p>
            </div>
            <div class="row" style="justify-content: center;">
              <a href="{{route('user_article',['username' => $profile_user->username])}}"><p style="color: black !important;">Articles</p></a>
            </div>
          </div>  --}}
        </div>
      </div>
    </div>
  </div>
  
  <script>
    var speed = 10;
  
    function incEltNbr(id) {
      elt = document.getElementById(id);
      endNbr = Number(document.getElementById(id).innerHTML);
      incNbrRec(0, endNbr, elt);
    }
  
    function incNbrRec(i, endNbr, elt) {
      if (i <= endNbr) {
        elt.innerHTML = i;
        setTimeout(function() { //Delay a bit before calling the function again.
          incNbrRec(i + 1, endNbr, elt);
        }, speed);
      }
    }
    
    incEltNbr("nbr");
    incEltNbr("followers");
    incEltNbr("following");
    incEltNbr("art");
  </script>