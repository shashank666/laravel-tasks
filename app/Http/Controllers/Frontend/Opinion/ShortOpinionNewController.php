<?php

namespace App\Http\Controllers\Frontend\Opinion;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\ThreadFollower;
use App\Model\Thread;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionCommentLike;
use App\Events\OpinionViewCounterEvent;
use App\Model\ShortOpinionCommentDisagree;
use App\Notifications\Frontend\CommentedOnShortOpinion;
use App\Jobs\AndroidPush\CommentedOnShortOpinionJob;
use DB;
use Notification;
use Carbon\Carbon;

class ShortOpinionNewController extends Controller{
    public function get_opinion_by_id(Request $request,$username,$id){
        $opinion=ShortOpinion::where('uuid',$id)->with('user')->first();
        $liked=$this->get_user_liked_opinionids();
        $disliked=$this->get_user_disliked_opinionids();
        if($opinion){
            event(new OpinionViewCounterEvent($opinion,$request->ip()));
            return view('frontend.opinions.crud.read',compact('opinion','liked','disliked'));
        }
        else{
            abort(404);
        }
        
    }
    public function get_opinion_by_id2(Request $request,$id){
      try{
        $opinion=ShortOpinion::where('id',$id)->with('user')->first();
        $liked=$this->get_user_liked_opinionids();
        $disliked=$this->get_user_disliked_opinionids();
        if($opinion){
            event(new OpinionViewCounterEvent($opinion,$request->ip()));
            return view('frontend.opinions.crud.read',compact('opinion','liked','disliked'));
        }
        else{
           echo "No Opinion Found";

        }
        
      }catch(\Exception $e){
        return $e->getMessage();
      }
     
      
  }
    public function get_user_liked_opinionids(){
        if(Auth::check()){
          $liked_opinions=auth()->user()->likes->pluck('id')->toArray();
        }else{
          $liked_opinions=[];
        }
        return $liked_opinions;
    }
    public function get_user_disliked_opinionids(){
        if(Auth::check()){
          $liked_opinions=auth()->user()->Disagree->pluck('id')->toArray();
        }else{
          $liked_opinions=[];
        }
        return $liked_opinions;
    }

}