<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:360px">
    <div class="modal-content" style="border:0px;">
      <div class="modal-header" style="background-color:#244363;color:#fff;">
        <h5 class="modal-title" id="reportModalLabel">
          Report an Issue
          <i class="ml-2 fas fa-exclamation-circle"></i>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form  method="POST" id="reportForm" action="{{route('report')}}">  
                  <input type="hidden" name="reportpost" id="reportpost"/>      
                  {{ csrf_field() }}

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="1--REPORT SPAM" id="flag_1" checked/>
                  <label class="form-check-label" for="flag_1">Report Spam</label>
                  </div>
          
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="2--PORNOGRAPHY OR NUDITY" id="flag_21"/>
                  <label class="form-check-label" for="flag_21">Pornography or Nudity</label>
                  </div>

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="2--CONTENT INVOLVING MINORS" id="flag_22"/>
                  <label class="form-check-label" for="flag_22">Content Involving Minors</label>
                  </div>
        
                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="3--VIOLENT CONTENT" id="flag_31"/>
                  <label class="form-check-label" for="flag_31">Violent Content</label>
                  </div>

                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="3--SUICIDE OR SELF HARM" id="flag_32"/>
                  <label class="form-check-label" for="flag_32">Suicide or Self Harm</label>
                  </div>

                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="3--DANGEROUS THREATS" id="flag_33"/>
                  <label class="form-check-label" for="flag_33">Dangerous Threats</label>
                  </div>
        
                  <div class="form-check">
                  <input  class="form-check-input" type="radio" name="flag" value="4--HARASSING ME" id="flag_41"/>
                  <label  class="form-check-label" for="flag_41">Harassing Me</label>
                  </div>

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="4--HARASSING SOMEONE ELSE" id="flag_42"/>
                  <label class="form-check-label" for="flag_42">Harassing Someone Else</label>
                  </div>

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="4--HATEFUL OR VIOLENT TOWARDS A GROUP" id="flag_43"/>
                  <label  class="form-check-label"for="flag_43">Hateful or Violent Towards a Group</label>
                  </div>        
      
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="5--COPYRIGHT / LEGAL VIOLATION" id="flag_51"/>
                  <label class="form-check-label" for="flag_51">Copyright or Legal Violation</label>
                  </div>

                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="5--REGULATED GOOD OR SERVICES" id="flag_52"/>
                  <label class="form-check-label" for="flag_52">Regulated Good Or Services</label>
                  </div>
                  
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="5--ANOTHER POLICY VIOLATION" id="flag_53"/>
                  <label class="form-check-label" for="flag_53">Another Policy Violation</label>
                  </div>
                  
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="flag" value="5--I DON'T LIKE IT" id="flag_54"/>
                  <label class="form-check-label" for="flag_54">I do not like it</label>
                  </div>
          
                <button type="submit" class="btn btn-danger btn-sm btn-block mt-2">REPORT ISSUE</button>
            </form>
      </div>
    </div>
  </div>
</div>