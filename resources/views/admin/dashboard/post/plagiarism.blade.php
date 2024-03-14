@extends('admin.layouts.app')
@section('title',"Test")

@push('styles')

@endpush

@push('scripts')

@endpush

@section('content')
<a href="#" id="scroll" style="display: none;"><span></span></a>
    <div class="header">
            <div class="container">
              <div class="header-body">
                <div class="row align-items-end">
                    <div class="col">
                        <h6 class="header-pretitle">
                        Plagiarism Check for
                        </h6>
                        <h3 class="header-title">
                        {{$post->title}}
                        </h3>
                    </div>
                   <div class="col-auto">
                     @if($article_status!=null)
                        @if($article_status->plagiarised==null)
                        <span class="ask_final">Is this Article Plagiarised?</span>
                            <div class="btn-group" role="group" aria-label="Plagiarism">
                                <a class="btn btn-danger" href="{{route('admin.plagiarised_checked',['id'=>$post->id,'plagiarised'=>1,'plagiarism_average'=>$plagiarism_average])}}">YES</a>
                                <a class="btn btn-success" href="{{route('admin.plagiarised_checked',['id'=>$post->id,'plagiarised'=>0,'plagiarism_average'=>$plagiarism_average])}}">NO</a>
                                
                        </div>
                        @endif
                    @endif
                    <span style="background: {{$color_code}};padding: 11px;color: white;border-radius: 10px;">Plagiarism: {{$plagiarism_average}} %</span>
                </div>
                </div>
              </div>
            </div>
    </div>
<div class="container">
<div class="row"> 
@foreach($results as $index=>$result)
<div class="col-md-12 col-sm-12">
    <div class="card">
         <div class="card-body">
            <h4>Title: {{$result->Title}}</h4>
            <p>Intro: {{$result->Introduction}}</p>
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-4">From: <a href="{{$result->URL}}" target="_blank">{{$result->URL}}</a></h5>
                    <span style="background: #F44336;color: #fff;padding: 7px;margin: 15%;border: dotted white;">Plagiarism : {{$result->Percents}}%</span>
                    <span style="background: #ffd700;padding: 7px;margin: 15%;border: dotted white;">Copied Words: {{$result->NumberOfCopiedWords}} Words</span>
                </div>
            </div>
        </div>
         <div class="card-footer opinion-footer">
            <div class="row">
                <div class="col-12" style="text-align: center;">
                    <a href="{{$result->ComparisonReport}}" target="_blank">
                    <button class="btn btn-sm btn-primary"  name="" id="" type="button">Comparison Report</button></a>
                     <a href="{{$result->CachedVersion}}" target="_blank">
                    <button onclick="window.open.href ='{{$result->CachedVersion}}'" class="btn btn-sm btn-primary"  name="" id="" type="button">Cached Version Report</button>
                    </a>
                     <a href="{{$result->EmbededComparison}}" target="_blank">
                    <button onclick="window.open.href ='{{$result->EmbededComparison}}'" class="btn btn-sm btn-primary"  name="" id="" type="button">Embeded Comparison Report</button>
                </a>
                   
                </div>
            </div>
         </div>
    </div>
</div>
@endforeach
</div>
</div>
@endsection

