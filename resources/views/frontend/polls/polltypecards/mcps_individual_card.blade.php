<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
<!--<style type="text/css">
h1 {
  color: #18191b;
  margin-bottom: 2rem;
}

section {
  display: flex;
  /*flex-flow: row wrap;*/
  flex-flow: row;
}

section > div {
  flex: 1;
  padding: 0.5rem;
}

input[type="radio"] {
  display: none;
}
input[type="radio"]:not(:disabled) ~ label {
  cursor: pointer;
}
input[type="radio"]:disabled ~ label {
  color: #bcc2bf;
  border-color: #bcc2bf;
  box-shadow: none;
  cursor: not-allowed;
}

.labelopt {
  height: 100%;
  display: block;
  background: white;
  border: 2px solid #244363;
  border-radius: 20px;
  padding: 1rem;
  margin-bottom: 1rem;
  text-align: center;
  box-shadow: 0px 3px 10px -2px rgba(161, 170, 166, 0.5);
  position: relative;
}

input[type="radio"]:checked + label {
  background: #ff9800;
  color: white;
  box-shadow: 0px 0px 20px rgba(0, 255, 128, 0.75);
}
input[type="radio"]:checked + label::after {
  color: #3d3f43;
  font-family: "Font Awesome 5 Pro", "Font Awesome 5 Free";
  font-weight: 900;
  border: 2px solid #1dc973;
  content: "\f00c";
  font-size: 18px;
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  height: 32px;
  width: 32px;
  line-height: 32px;
  text-align: center;
  border-radius: 50%;
  background: white;
  box-shadow: 0px 2px 5px -2px rgba(0, 0, 0, 0.25);
}

@media only screen and (max-width: 700px) {
  section {
    flex-direction: column;
  }
}
</style>  -->
<style type="text/css">
  .inputGroup {
  background-color: #fff;
  display: block;
  margin: 10px 0;
  position: relative;
}
.inputGroup label {
  padding: 12px 30px;
  width: 100%;
  display: block;
  text-align: left;
  color: #3C454C;
  cursor: pointer;
  position: relative;
  z-index: 2;
  -webkit-transition: color 200ms ease-in;
  transition: color 200ms ease-in;
  overflow: hidden;
}
.inputGroup label:before {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  content: '';
  background-color: #ff9800;
  position: absolute;
  left: 50%;
  top: 50%;
  -webkit-transform: translate(-50%, -50%) scale3d(1, 1, 1);
          transform: translate(-50%, -50%) scale3d(1, 1, 1);
  -webkit-transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
  transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
  opacity: 0;
  z-index: -1;
}
.inputGroup label:after {
  width: 32px;
  height: 32px;
  content: '';
  border: 2px solid #D1D7DC;
  background-color: #fff;
  background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
  background-repeat: no-repeat;
  background-position: 2px 3px;
  border-radius: 50%;
  z-index: 2;
  position: absolute;
  right: 30px;
  top: 50%;
  -webkit-transform: translateY(-50%);
          transform: translateY(-50%);
  cursor: pointer;
  -webkit-transition: all 200ms ease-in;
  transition: all 200ms ease-in;
}
.inputGroup input:checked ~ label {
  color: #fff;
}
.inputGroup input:checked ~ label:before {
  -webkit-transform: translate(-50%, -50%) scale3d(56, 56, 1);
          transform: translate(-50%, -50%) scale3d(56, 56, 1);
  opacity: 1;
}
.inputGroup input:checked ~ label:after {
  background-color: #050505;
  border-color: #161616;
}
.inputGroup input {
  width: 32px;
  height: 32px;
  -webkit-box-ordinal-group: 2;
          order: 1;
  z-index: 2;
  position: absolute;
  right: 30px;
  top: 50%;
  -webkit-transform: translateY(-50%);
          transform: translateY(-50%);
  cursor: pointer;
  visibility: hidden;
}



body {
  background-color: #D1D7DC;
  font-family: 'Fira Sans', sans-serif;
}

*,
*::before,
*::after {
  box-sizing: inherit;
}

html {
  box-sizing: border-box;
}

code {
  background-color: #9AA3AC;
  padding: 0 8px;
}
</style>
<script>
  function buttonshow() {
  var x = document.getElementById("btn_poll_after");
  var y = document.getElementById("btn_poll_before");
  x.style.display = "block";
  y.style.display = "none";
}
</script>

            
<div class="">
    <form id="poll_voting" role="form"  action="{{route('poll-vote')}}" method="POST" enctype="multipart/form-data">
        {{csrf_field()}}
      
      <div class="form-group row">
          <label class="col-lg-3 col-md-3 col-sm-5 col-12 col-form-label"><span style = "color: #ff9800">Options: </span></label>
          <div class="col-lg-9 col-md-9 col-sm-7 col-12">
                @php($count = 1) 
                     @foreach($poll_options as $poll_option)
                     
                      <div class="inputGroup">
                        <input type="radio" id="radio{{$count}}" name="select" onclick="buttonshow();" value="{{$poll_option->id}}">
                            <label for="radio{{$count}}" class="labelopt">
                              <span class="mcps">{{$poll_option->options}}</span>
                              <!--<p>Awww, poor baby. Too afraid of the scary game sprites? I laugh at you.</p>-->
                            </label>
                      </div>
                    @php($count++)
                    @endforeach

              <!--<section>

                  @foreach($poll_options as $poll_option)
                  <div>
                    <input type="radio" id="{{$poll_option->options}}" name="select" onclick="buttonshow();" value="{{$poll_option->id}}">
                    <label for="{{$poll_option->options}}" class="labelopt">
                      <span class="mcps">{{$poll_option->options}}</span>
                      </label>
                  </div>
                  @endforeach
                  </section>-->
          </div>
      </div>


      <input type="hidden" name="voting_type" id="voting_type" value="MCPS" />
      <input type="hidden" name="voting_type_head" id="voting_type_head" value="MCPS" />
      <input type="hidden" name="poll_id" id="poll_id" value="{{$poll->id}}">
      <input type="hidden" name="url" id="url" value="{{$poll->slug}}">
      <button type="button" id="btn_poll_before" class="btn btn-success float-right disabled" style="">Vote <i class="fas fa-paper-plane"></i></button>
      <button type="submit" id="btn_poll_after" class="btn btn-success float-right" style="display: none">Vote <i class="fas fa-paper-plane"></i></button>
    </form>
    </div>