<?php

namespace App\Http\Controllers\Api\Community;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Community;
use App\Model\CommunityMember;
use App\Model\FileManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Events\ThreadViewCounterEvent;
use App\Events\OpinionViewCounterEvent;

use Carbon\Carbon;

use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use ImageOptimizer;
use App\Jobs\Resize\ResizeImageJob;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ThreadLike;
use App\Model\CategoryThread;
use App\Model\ThreadFollower;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionLike;
use App\Model\ShortOpinionComment;
use DB;


class CommunityController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['exfunc']]);
    }
    public function join(Request $request){
        //send back community Details
        try{
        
        $data = $request->all();

        $member=DB::table('community_members')->where(['user_id'=>Auth::user()->user_id,'community_id'=>$data['community_id']])->first();

        if($member){
            if($member->is_active===1){
            $response=array('status'=>'success','result'=>1,'message'=>'Member Exists');
            return response()->json($response, 200);
            }
            DB::table('community_members')->where(['user_id'=>Auth::user()->user_id,'community_id'=>$data['community_id']])->update(['is_active'=>1]);
            $response=array('status'=>'success','result'=>1,'message'=>'Member Added');
            return response()->json($response, 200);
        }else{
            $member = new CommunityMember();
            $member->community_id = $data['community_id'];
            $member->user_id =Auth::user()->user_id;
            $member->save();
            $response=array('status'=>'success','result'=>1,'message'=>'Member Added');
            return response()->json($response, 200);
        }

       
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function remove_member(Request $request, $community_id){
        //send back community Details
        try{
        $data = $request->all();
        $member = DB::table('community_members')->where(['community_id'=>$community_id,'user_id'=>Auth::user()->user_id])->update(['is_active'=>0]);
    
        
    

        $response=array('status'=>'success','result'=>1,'message'=>'Member Removed');
        return response()->json($response, 200);
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_details(Request $request, $community_id){
        //send back community Details
        try{
            $community = DB::table('communities')->where(['is_active'=>1,'id'=>$community_id])->first();
            //get One community

              
     
            try{
            $profile_user=User::where(['id'=>$community->user_id,'is_active'=>1])->first();
            }catch(\Exception $e){
            $profile_user=null;
            }
           
            $members_count=CommunityMember::where(['community_id'=>$community_id,'is_active'=>1])->count();
    
            $custom_format=[
                'id' => $community->id,
                'name' => $community->name,
                'uuid' => $community->uuid,
                'image' => $community->image,
                'cover_image' =>$community->cover_image,
                'user_id'=>$community->user_id,
                'contest_category'=>$community->contest_category,
                'description'=>$community->description,
                'user'=>$profile_user,
                'created_at'=>$community->created_at,
                'updated_at'=>$community->updated_at,
                'members_count' => $members_count,
    
            ];
        
            $response=array('status'=>'success','result'=>1,'community'=>$custom_format);
            return response()->json($response, 200);

        }catch(\Exception $e){
            echo " Error ".$e;
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_details_uuid(Request $request, $community_id){
        //send back community Details
        try{
            $community = DB::table('communities')->where(['is_active'=>1,'uuid'=>$community_id])->first();
            //get One community
            try{
                $profile_user=User::where(['id'=>$community->user_id,'is_active'=>1])->first();
            }catch(\Exception $e){
                $profile_user=null;
            }
           
            $members_count=CommunityMember::where(['community_id'=>$community->id,'is_active'=>1])->count();
    
            $custom_format=[
                'id' => $community->id,
                'name' => $community->name,
                'uuid' => $community->uuid,
                'image' => $community->image,
                'cover_image' =>$community->cover_image,
                'user_id'=>$community->user_id,
                'contest_category'=>$community->contest_category,
                'description'=>$community->description,
                'user'=>$profile_user,
                'created_at'=>$community->created_at,
                'updated_at'=>$community->updated_at,
                'members_count' => $members_count,
    
            ];
        
            $response=array('status'=>'success','result'=>1,'community'=>$custom_format);
            return response()->json($response, 200);

        }catch(\Exception $e){
            echo " Error ".$e;
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_opinions(Request $request, $community_id){
            $followed_threadids=ThreadFollower::where(['user_id'=>Auth::user()->user_id,'is_active'=>1])->pluck('thread_id')->toArray();
            $following_userids=Auth::user()->user->active_followings->pluck('id')->toArray();
            array_push($following_userids,Auth::user()->user_id);

            $following_for_opinion_userids=Auth::user()->user->active_followings->pluck('id')->toArray();
            $liked_ids = ShortOpinionLike::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
 			 $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
			 $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
            $commented_ids = ShortOpinionComment::whereIn('user_id',$following_for_opinion_userids)->where(['is_active'=>1])->pluck('short_opinion_id')->toArray();
            $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
			$my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
            //$rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1])->orderBy('created_at','desc')->get();
			//$Agree_id =[];			
            $rej_opinion_id = [];

            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>""])->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                array_push($rej_opinion_id,$img_opinion->id);
            }
          

            
                $query = ShortOpinion::query();
                $query->with(['threads','user:id,name,username,unique_id,image']);
                $query->withCount(['likes','comments']);
            
                $query->where(['is_active'=>1,'community_id'=>$community_id]);
                $query->orderBy('last_updated_at','desc');
                $opinions= $query->paginate(12);
        

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
        public function exfunc()
        {
            # code...
        }

        public function get_members(Request $request,$community_id){
            try{

            $members_id = DB::table('community_members')->where(['is_active'=>1,'community_id'=>$community_id])->orderBy('created_at','desc')->pluck('user_id')->toArray();

            $members =User::where(['is_active'=>1])->whereIn('id',$members_id)->get();
          
            
             $user_id=Auth::user()->user_id;
             $following_ids=User::find($user_id)->active_followings->pluck('id')->toArray();
    

            $formatted_users=collect($members)->map(function($user,$key) use($following_ids){
                return $this->formatted_user($following_ids,$user);
            });
            $response=array('status'=>'success','result'=>1,'members'=>$formatted_users);
            return response()->json($response, 200);

            }
            catch(\Exception $e){
                $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
                return response()->json($response, 500);
            }
        }


   
   
   
   
   
   
   
}
