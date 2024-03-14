<script>
$(document).ready(function(){
$("input[type='radio']").change(function(){
if($(this).val()=="I will come back later")
{
$("#suspend").show();
$("#delete").show();
$("#all").hide();
}
else
{
$("#suspend").hide();
$("#delete").hide();
$("#all").show();
}
});
});
</script>
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:360px">
    <div class="modal-content" style="border:0px;">
      <div class="modal-header" style="background-color:#244363;color:#fff;">
        <h5 class="modal-title" id="deleteAccountModalLabel">
          Reason
          <i class="ml-2 fas fa-exclamation-circle"></i>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form  method="POST" id="delete-account-form" action="{{route('delete_account')}}">     
                  {{ csrf_field() }}

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="I do not use Opined very frequently" id="flag_1" checked/>
                  <label class="form-check-label" for="flag_1">I do not use Opined very frequently</label>
                  </div>
          
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="I did not like Opined" id="flag_21"/>
                  <label class="form-check-label" for="flag_21">I did not like Opined</label>
                  </div>

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="Opined is not showing relevant content to me" id="flag_22"/>
                  <label class="form-check-label" for="flag_22">Opined is not showing relevant content to me</label>
                  </div>
        
                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="Not much of an activities" id="flag_31"/>
                  <label class="form-check-label" for="flag_31">Not much of an activities</label>
                  </div>

                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="Needs improvement" id="flag_32"/>
                  <label class="form-check-label" for="flag_32">Needs improvement</label>
                  </div>

                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="Too many ads" id="flag_33"/>
                  <label class="form-check-label" for="flag_33">Too many ads</label>
                  </div>
        
                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="I will come back later" id="flag_41"/>
                  <label  class="form-check-label" for="flag_41">I will come back later</label>
                  </div>
                  
                  <button class="btn btn-sm btn-warning float-left waves-effect waves-float mt-2" type="button" style="display:none;" name="suspend" id="suspend" onclick="deactivateAccount()">Deactivate Account
                  </button>
                  <form id="deactivate-account-form" class="d-none" method="POST" action="{{route('deactivate_account')}}">
                      {{csrf_field()}}
                  </form>
                  <button class="btn btn-sm btn-danger float-right waves-effect waves-float mt-2" type="button" style="display:none;" name="delete" id="delete" onclick="deleteParmanent()">No, Delete Account
                  </button>
                <button type="button" name="all" id="all" class="btn btn-danger btn-sm btn-block mt-2" onclick="deleteParmanent()">DELETE</button>
            </form>
      </div>
    </div>
  </div>
</div>