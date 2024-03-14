@extends('frontend.layouts.app')
@section('title', 'Your Opinions Stats - Opined')
@section('description','View the statistics for all opinons you write on Opined.')
@section('keywords','Stats')

@push('meta')
<link rel="canonical" href="https://www.weopined.com/me/performance" />
<link href="https://www.weopined.com/me/performance" rel="alternate" reflang="en" />


<!-- Twitter Card data -->
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@weopined">
<meta name="twitter:title" content="Your Article's Performance  - Opined">
<meta name="twitter:description" content="View the statistics for all opinons you write on Opined.">
<meta name="twitter:creator" content="@weopined">
<meta name="twitter:image" content="https://www.weopined.com/favicon.png">

<!-- Open Graph data -->
<meta property="og:title" content="Your Article's Performance  - Opined"/>
<meta property="og:type" content="website" />
<meta property="og:url" content="https://www.weopined.com/me/performance" />
<meta property="og:image" content="https://www.weopined.com/favicon.png" />
<meta property="og:description" content="View the statistics for all opinons you write on Opined." />
<meta property="og:site_name" content="Opined" />
<meta property="og:locale" content="en_US" />
<meta property="fb:app_id" content="1766000746745688" />
@endpush

@push('styles')
@endpush

@push('scripts')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
@endpush

@section('content')
<h1 class="mt-2">Article Performance</h1>
<p class="mb-5 text-secondary">Click the article below to view performance in chart</p>
<div class="container">

        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li  class="nav-item"> <a class="nav-link active" href="#tab_likes_chart" role="tab" data-toggle="tab">Likes<span class="ml-2"><i class="far fa-thumbs-up"></i></span></a></li>
            <li  class="nav-item"><a class="nav-link" href="#tab_views_chart" role="tab" data-toggle="tab">Views<span class="ml-2"><i class="fas fa-eye"></i></span></a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active show" id="tab_likes_chart">
                <div class="tab-body mt-4">
                      <div id="likes_chart"  style="height:320px;width:100%;"></div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="tab_views_chart">
                <div class="tab-body mt-4">
                      <div id="views_chart"  style="height:320px;width:100%;"></div>
                </div>
            </div>
        </div>
      <div class="text-center" style="padding-top: 12px;">
        <div class="btn-group" role="group" aria-label="Statistics Buttons">
            <button type="button" class="btn btn-sm btn-primary btn-next-prev" data-from="0" data-to="0" data-postid="0" data-btn="prev" id="btn_prev">Prev 30 Days</button>
            <button type="button" class="btn btn-sm btn-primary btn-next-prev" data-from="0" data-to="0" data-postid="0" data-btn="next" id="btn_next" disabled>Next 30 Days</button>
        </div>
        <p id="error" class="text-danger" style="display:none;">please click on article below to view performance</p>
      </div>


    @if($posts->total()>0)
    <div class="row mt-5">
         <div class="offset-xl-1 col-xl-10 offset-lg-1 col-lg-10 offset-md-1 col-md-10 col-12">
            @foreach($posts as $post)
              @include('frontend.posts.components.stats-post-card')
            @endforeach
        </div>
    </div>
    <div class="row">
        <div class="col align-self-center">
            {{ $posts->links('frontend.posts.components.pagination') }}
        </div>
    </div>
    @endif

    <input type="hidden" value="{{  implode(',',array_pluck($dates,'date')) }}" id="dates"/>
    <input type="hidden" value="{{  implode(',',array_pluck($dates,'likes')) }}" id="likes"/>
    <input type="hidden" value="{{  implode(',',array_pluck($dates,'views')) }}" id="views"/>

</div>

<script>
    var dates=$('#dates').val().split(',');
    var likes= $('#likes').val().split(',');
    var views= $('#views').val().split(',');

    $(document).ready(function(){

      var likes_chart=Highcharts.chart('likes_chart', {
          chart: {
              type: 'column'
          },
          title: {
              text: ''
          },
          xAxis: {
              categories:dates,
              tickInterval:4,
              crosshair: true
          },
          yAxis: {
            allowDecimals: false,
              title: {
                  text: 'Likes'
              }

          },
          plotOptions: {
              line: {
                  dataLabels: {
                      enabled: true
                  },
                  enableMouseTracking: true
              }
          },
          credits: {
            enabled: false
          },
          series: [{
              name: 'Likes',
              data: likes,
              color: '#ff9800',
          }]
      });

      var views_chart=Highcharts.chart('views_chart', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories:dates,
            tickInterval:4,
            crosshair: true
        },
        yAxis: {
            allowDecimals: false,
            title: {
                text: 'Views'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        credits: {
          enabled: false
        },
        series: [{
            name: 'Views',
            data: views,
            color: '#007bff',
        }]
      });
    });


    $(document).on('click','.postcard',function(){
      var post_id=$(this).attr('id').slice(5);
      $('.postcard').css('background-color','#fff');
      $(this).css('background-color','#f8f9fa');
      $('#error').css('display','none');
      $("html, body").animate({ scrollTop: 0 }, "slow");
      var toDate=moment().format("YYYY-MM-DD");
      var fromDate=moment().subtract(30, 'days').format("YYYY-MM-DD");
      getStats('none',fromDate,toDate,post_id);
    });


    $(document).on('click','.btn-next-prev',function(){
        var from=$(this).attr('data-from');
        var to=$(this).attr('data-to');
        var post_id=$(this).attr('data-postid');
        var btn=$(this).attr('data-btn');

        if(parseInt(post_id)==0){
          $('#error').css('display','block');
        }else{
          $('#error').css('display','none');
            getStats(btn,from,to,post_id);
        }
    });


    function getStats(btn,from,to,post_id){
        $.ajax({
            url:'{{ route("get_post_stats") }}',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type:'POST',
            dataType:'json',
            data:{
                post_id:post_id,
                btn:btn,
                from:from,
                to:to
            },
            success:function(response){
              if(response.status=='success'){

                if(response.to==moment().format("YYYY-MM-DD")){
                    $('#btn_next').attr('disabled','disabled');
                }else{
                  $('#btn_next').removeAttr('disabled')
                }

                if(btn=="next"){
                    if(response.to==moment().format("YYYY-MM-DD")){
                        $('#btn_next').attr('disabled','disabled');
                        $('#btn_prev').attr('data-from',moment(response.to).subtract(60, 'days').format("YYYY-MM-DD"));
                        $('#btn_prev').attr('data-to',moment(response.to).subtract(30, 'days').format("YYYY-MM-DD"));
                        $('#btn_prev').attr('data-postid',response.post_id);
                    }else{
                        $('#btn_next').attr('data-from',response.to);
                        $('#btn_next').attr('data-to',moment(response.to).add(30, 'days').format("YYYY-MM-DD"));
                        $('#btn_next').attr('data-postid',response.post_id);

                        $('#btn_prev').attr('data-from',moment(response.to).subtract(30, 'days').format("YYYY-MM-DD"));
                        $('#btn_prev').attr('data-to',response.to);
                        $('#btn_prev').attr('data-postid',response.post_id);
                    }
                }

                if(btn=="prev"){
                    $('#btn_next').attr('data-from',response.from);
                    $('#btn_next').attr('data-to',moment(response.from).add(30, 'days').format("YYYY-MM-DD"));
                    $('#btn_next').attr('data-postid',response.post_id);

                    $('#btn_prev').attr('data-from',moment(response.from).subtract(30, 'days').format("YYYY-MM-DD"));
                    $('#btn_prev').attr('data-to',response.from);
                    $('#btn_prev').attr('data-postid',response.post_id);
                }

                if(btn=="none"){
                    $('#btn_prev').attr('data-from',moment().subtract(60, 'days').format("YYYY-MM-DD"));
                    $('#btn_prev').attr('data-to',moment().subtract(30, 'days').format("YYYY-MM-DD"));
                    $('#btn_prev').attr('data-postid',response.post_id);
                }


                var stats_likes_chart=$('#likes_chart').highcharts();
                var stats_views_chart=$('#views_chart').highcharts();

                stats_likes_chart.xAxis[0].update({categories:response.dates},false);
                stats_likes_chart.series[0].update({data: response.likes}, false);

                stats_views_chart.xAxis[0].update({categories:response.dates},false);
                stats_views_chart.series[0].update({data: response.views}, false);
                stats_likes_chart.yAxis[0].isDirty = true;
                stats_likes_chart.redraw();
                stats_views_chart.yAxis[0].isDirty = true;
                stats_views_chart.redraw();

              }
            },error:function(err){

            }
        });
    }

</script>
@endsection
