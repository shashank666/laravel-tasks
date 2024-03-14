<?php

namespace App\Http\Controllers\Api\News;

use App\Model\NewsHeadlines;
use Illuminate\Http\Request;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\OpinionViewCounterEvent;

class NewsApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['exfunc']]);
    }
   
    public function index(Request $request)
    {
        // Retrieve the 15 most recent news headlines by publishedAt
        //$headlines = NewsHeadlines::orderBy('created_at', 'desc')->take(20)->get();
        $headlines = NewsHeadlines::whereNotNull('description')
                          ->whereNotNull('url_to_image')
                          ->orderBy('created_at', 'desc')
                          ->take(20)
                          ->get();

        // Return the headlines as JSON response
      
        $response=array('status'=>'success','result'=>1,'headlines'=>$headlines);
        return response()->json($response, 200);
    }

    public function trending_headlines(Request $request)
    {
        // Retrieve the 15 most recent news headlines by publishedAt
        //$headlines = NewsHeadlines::orderBy('created_at', 'desc')->take(20)->get();
        $headlines = NewsHeadlines::whereNotNull('description')
        ->whereNotNull('url_to_image')
        ->withCount('short_opinions')
        ->orderBy('short_opinions_count', 'desc')
        ->take(10)
        ->get();
    

        // Return the headlines as JSON response
      
        $response=array('status'=>'success','result'=>1,'headlines'=>$headlines);
        return response()->json($response, 200);
    }
  
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function get_news_by_id(Request $request, $id)
    {
        //
        $headline = NewsHeadlines::find($id);
        if ($headline === null) {
            return response()->json(['error' => 'News headline not found'], 404);
        }
        return response()->json(['headline' => $headline]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function get_opinions_by_id(Request $request, $id)
    {
        $query = ShortOpinion::query();
        $query->with(['threads','user:id,name,username,unique_id,image']);
        $query->withCount(['likes','comments']);
    
        $query->where(['is_active'=>1,'news_id'=>$id]);
        $query->orderBy('last_updated_at','desc');
        $opinions= $query->paginate(12);
        //$following_for_opinion_userids=Auth::user()->user->active_followings->pluck('id')->toArray();
        //$liked_ids = ShortOpinionLike::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
          $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
         $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
        //$commented_ids = ShortOpinionComment::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
        $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
        $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
        

        foreach ($opinions as $opinion){
            event(new OpinionViewCounterEvent($opinion,$request->ip()));
            $Agree_ids = ShortOpinionLike::  where(['id'=>$opinion->id,'Agree_Disagree'=>1])->get();
            $Disagree_ids = ShortOpinionLike::  where(['id'=>$opinion->id,'Agree_Disagree'=>0])->get();
            $opinion->AgreeCnt=   ($Agree_ids->count()) ;
            $opinion->DisagreeCnt=   ($Disagree_ids->count()) ;
            if($opinion->links!=null){
                 
                $opinion_dummy=json_decode($opinion->links);
                foreach ($opinion_dummy as $index=>$r_opinion) {
            
                   if($r_opinion->status=="error"){
                        $opinion->links = "null";
                    }
                    elseif($r_opinion->image==null){
                    
                    $r_opinion->image="https://weopined.com/img/noimg.png";
                    $r_opinion->imageWidth=640;
                    $r_opinion->imageHeight=300;
                    
                    $opinion->links = "[".json_encode($r_opinion)."]";
                    
                }
                }
            }
        }
        $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
            $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids,$Agreeids,$Disagreeids, $my_agreed_opinionids,$my_disagreed_opinionids){
                unset($opinion->threads);
                return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
            });
            
            $meta=$this->get_meta($opinions);
            $response=array('status'=>'success','result'=>1,'opinions'=>$formatted, 'meta'=>$meta);
         return response()->json($response, 200);
    }

    public function get_opinions_by_id2(Request $request, $id)
    {
        $query = ShortOpinion::query();
        $query->with(['threads','user:id,name,username,unique_id,image']);
        $query->withCount(['likes','comments']);
    
        $query->where(['is_active'=>1,'news_id'=>$id]);
        $query->orderBy('last_updated_at','desc');
        $opinions= $query->paginate(12);
        //$following_for_opinion_userids=Auth::user()->user->active_followings->pluck('id')->toArray();
        //$liked_ids = ShortOpinionLike::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
          $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
         $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
        //$commented_ids = ShortOpinionComment::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
        $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
        $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
        

        foreach ($opinions as $opinion){
            event(new OpinionViewCounterEvent($opinion,$request->ip()));
            $Agree_ids = ShortOpinionLike::  where(['id'=>$opinion->id,'Agree_Disagree'=>1])->get();
            $Disagree_ids = ShortOpinionLike::  where(['id'=>$opinion->id,'Agree_Disagree'=>0])->get();
            $opinion->AgreeCnt=   ($Agree_ids->count()) ;
            $opinion->DisagreeCnt=   ($Disagree_ids->count()) ;
            if($opinion->links!=null){
                 
                $opinion_dummy=json_decode($opinion->links);
                foreach ($opinion_dummy as $index=>$r_opinion) {
            
                   if($r_opinion->status=="error"){
                        $opinion->links = "null";
                    }
                    elseif($r_opinion->image==null){
                    
                    $r_opinion->image="https://weopined.com/img/noimg.png";
                    $r_opinion->imageWidth=640;
                    $r_opinion->imageHeight=300;
                    
                    $opinion->links = "[".json_encode($r_opinion)."]";
                    
                }
                }
            }
        }
        $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
            $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids,$Agreeids,$Disagreeids, $my_agreed_opinionids,$my_disagreed_opinionids){
                unset($opinion->threads);
                return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
            });
            
            $meta=$this->get_meta($opinions);
            $response=array('status'=>'success','result'=>1,'feed'=>$formatted, 'meta'=>$meta);
         return response()->json($response, 200);
    }
}
