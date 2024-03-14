<div class="modal fade" data-color="red" id="phonecodeModal" tabindex="-1" role="dialog" aria-labelledby="phonecodeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border:0px;">
      <div class="modal-header" style="background-color:#244363;color:#fff;">
        <h5 class="modal-title" id="phonecodeModalTitle">Select Your Phone Code</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height:400px">
            <div class="form-group">
            <input type="text" onkeyup="filter();" class="form-control" id="searchPhoneCode" placeholder="Search ..." autofocus="autofocus" autocomplete="none" />
            </div>

            <div  id="menu-phone-codes" class="row px-2" style="max-height:390px;overflow:scroll">
             @foreach($countries as $country)
                <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="country_phonecode" value="{{$country->phone_code}}" id="{{$country->code}}" style="cursor:pointer">
                  <label class="form-check-label" for="{{$country->code}}" style="cursor:pointer">
                   {{$country->name .' ('.$country->phone_code.')'}}
                  </label>
                </div>
                </div>
             @endforeach
             <div id="empty" class="dropdown-header" style="display:none;width:100%;">Not found</div>
            </div>
      </div>
    </div>
  </div>
</div>
