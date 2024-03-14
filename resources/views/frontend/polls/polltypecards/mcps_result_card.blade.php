<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
      //$result = (array) json_decode($json);
       var record={!! json_encode($poll_options_chart) !!};
       //var colors = {!! json_encode($poll_options) !!};
       //console.log(record);
      // console.log(colors);
       // Create our data table.
       var data = new google.visualization.DataTable();
        data.addColumn('string', 'Options');
       data.addColumn('number', 'Votes');
       for(var k in record){
            var v = record[k];
           
             data.addRow([k,v]);
          //console.log(v);
          }
        var options = {
          title: 'Result',
          //pieSliceValue: "none",
          tooltip: { trigger: 'none' },
          is3D: true,
          fontSize: 17,
          //chartArea:{left:20,top:20,width:'100%',height:'100%'},
          chartArea:{left:30,width:'100%',height:'300'},
          backgroundColor: '#fff',
          /*colors: ['#e2431e', '#d3362d', '#e7711b',
                   '#e49307', '#e49307', '#b9c246'],*/
          //colors: [""],
        };
        //console.log(options)
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
      //$result = (array) json_decode($json);
       var record={!! json_encode($poll_options_chart) !!};
       //var colors = {!! json_encode($poll_options) !!};
       //console.log(record);
      // console.log(colors);
       // Create our data table.
       var data = new google.visualization.DataTable();
        data.addColumn('string', 'Options');
       data.addColumn('number', 'Votes');
       for(var k in record){
            var v = record[k];
           
             data.addRow([k,v]);
          //console.log(v);
          }
        var options = {
          title: 'Result',
          is3D: true,
          fontSize: 17,
          //chartArea:{left:20,top:20,width:'100%',height:'100%'},
          chartArea:{left:30,width:'100%',height:'300'},
          backgroundColor: '#fff',
          /*colors: ['#e2431e', '#d3362d', '#e7711b',
                   '#e49307', '#e49307', '#b9c246'],*/
          //colors: [""],
        };
        //console.log(options)
        var chart = new google.visualization.PieChart(document.getElementById('vote_loggedin'));
        chart.draw(data, options);
      }
    </script>
  @if($poll->enablenote == 1)
<div class="jumbotron text-center" style="padding: 2rem;margin-bottom: 0rem;">
  <h4 class="display-4">Thank You!</h4>
  <p class="lead"><strong>Your response for "<span style="color:#ff9800; font-weight: 700;">{{$poll->title}}</span>" has been submitted</strong><br/> {{$poll->poll_result_note}}</p>
</div>
@else
  
  <div class="row">
    <div class="col-md-12 col-12" >
      @if(Auth::user())
      <div id="vote_loggedin" style="width: 100%; height: 350px;"></div>
      @else
      <div id="piechart_3d" style="width: 100%; height: 350px;"></div>
      @endif
    </div>
  </div>
  

@endif
</div> 
   
      <div class="card-footer" style="background: #fafafa;">
      <a class="btn btn-circle btn-twitter waves-effect waves-circle waves-float mr-2" target="_blank" role="button" href="https://twitter.com/share?text={{$poll->title}} LIVE POLL : Vote and Share your opinion on Opined&url=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="TWITTER"><span><i class="fab fa-twitter"></i></span></a>
        <a class="btn btn-circle btn-facebook waves-effect waves-circle waves-float mr-2"  target="_blank" role="button" href="https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/polls/{{$poll->slug}}" data-post="{{$poll->id}}" data-plateform="FACEBOOK"><span><i class="fab fa-facebook-f"></i></span></a>
        <a class="btn btn-circle btn-whatsapp  waves-effect waves-circle waves-float"  target="_blank" role="button" href="https://api.whatsapp.com/send?&text={{$poll->title}} : https://www.weopined.com/polls/{{$poll->slug}} LIVE POLL : Vote and Share your opinion on Opined" data-post="{{$poll->id}}" data-plateform="WHATSAPP"><span><i class="fab fa-whatsapp"></i></span></a>
        <div class="float-right" style="margin-top: 1%; font-size: 1rem">
          <span style="color: rgb(17, 17, 17)">Total Votes: </span>
          @if(Auth::guest())
          <a data-toggle="modal" href="#loginModal"> Login To View Total Vote </a>
          @else
          <span style="color: #ff9800">{{$total_votes}}</span>
          @endif
        </div>
      </div>
      <div class="row">
        <div class="col-md-12" ><span style="color: red">*</span><span> Only logged-in user's vote will be counted in final results</span>
        </div>
      </div>
 </div>