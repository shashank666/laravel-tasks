<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\Follower;
use App\Model\Post;
use App\Model\ShortOpinion;
use App\Model\GamificationReward;
use App\Model\Point;
use App\Model\Achievement;
use App\Model\UserAchievement;
use App\Model\ShortOpinionComment;
use App\Model\Comment;
use App\Model\PollResults;
use App\Events\UserProfileViewCounterEvent;
use Carbon\Carbon;
use App\Model\ShortOpinionLike;
use App\Model\Thread;
use App\Model\ThreadFollower;
use DB;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // function for get user profile by user id
    public function profile(Request $request,$id){
        try{
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::select('id','name','username','unique_id','image','cover_image','is_active','bio','keywords','gender','facebook_url','twitter_url','linkedin_url','instagram_url','youtube_channel_url','website_url','views','registered_as_writer','created_at','updated_at')
            ->where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                if(Auth::check()){
                    $is_followed= in_array($profile_user->id,Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                    $is_blocked = in_array($profile_user->id,Auth::user()->user->blocked_users->pluck('blocked_id')->toArray())?1:0;
                }else{
                    $is_followed=0;
                    $is_blocked=0;
                }
                $profile_user->is_followed=$is_followed;
                $profile_user->is_blocked = $is_blocked;
                $profile_user->followers_count= Follower::where(['leader_id'=>$profile_user->id,'is_active'=>1])->count();
                $profile_user->following_count= Follower::where(['follower_id'=>$profile_user->id,'is_active'=>1])->count();
                $profile_user->opinion_count = ShortOpinion::where(['user_id'=>$profile_user->id,'is_active'=>1])->count();

        

                $this->remove_null($profile_user);
                $response=array('status'=>'success','result'=>1,'profile_user'=>$profile_user);
                return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'No user found');
                return response()->json($response,200);
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    // function for get user followers by user id
    public function followers(Request $request,$id){
        try{
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $followers=[];
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $followers=Follower::where(['leader_id'=>$profile_user->id,'is_active'=>1])->orderBy('created_at','desc')->with('follower')->paginate(12);
                $formatted=$followers->getCollection()->transform(function($follower,$key){
                    $this->remove_null($follower);
                    $user=$follower['follower'];
                    $this->remove_null($user);
                    if(Auth::check()){
                     $is_followed=in_array($user->id,Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                     }else{
                         $is_followed=0;
                     }
                      $user->is_followed=$is_followed;
                      $custom_user= [
                         'id' => $follower->id,
                         'follower_id'=> $follower->follower_id,
                         'leader_id'=> $follower->leader_id,
                         'is_active'=> $follower->is_active,
                         'created_at'=>Carbon::parse($follower->created_at)->toDateTimeString(),
                         'updated_at'=>Carbon::parse($follower->updated_at)->toDateTimeString(),
                         'user'=>$user
                     ];
                     return $custom_user;
                 });
                 $meta=$this->get_meta($followers);
                 $response=array('status'=>'success','result'=>1,'followers'=>$formatted,'meta'=>$meta);
                 return response()->json($response, 200);

            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

      // function for get user followings by user id
    public function following(Request $request,$id){
        try{
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }

            $followers=[];
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $following=Follower::where(['follower_id'=>$profile_user->id,'is_active'=>1])->orderBy('created_at','desc')->with('leader')->paginate(12);
                $formatted=$following->getCollection()->transform(function($following_user,$key){
                    $this->remove_null($following_user);
                    $user=$following_user['leader'];
                    $this->remove_null($user);
                    if(Auth::check()){
                     $is_followed=in_array($user->id,Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                     }else{
                         $is_followed=0;
                     }
                      $user->is_followed=$is_followed;
                      $custom_user= [
                         'id' => $following_user->id,
                         'follower_id'=> $following_user->follower_id,
                         'leader_id'=> $following_user->leader_id,
                         'is_active'=> $following_user->is_active,
                         'created_at'=>Carbon::parse($following_user->created_at)->toDateTimeString(),
                         'updated_at'=>Carbon::parse($following_user->updated_at)->toDateTimeString(),
                         'user'=>$user
                     ];
                     return $custom_user;
                 });
                 $meta=$this->get_meta($following);
                 $response=array('status'=>'success','result'=>1,'following'=>$formatted,'meta'=>$meta);
                 return response()->json($response, 200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

      // function for get user latest posts by user id
    public function latest_posts(Request $request,$id){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $posts=Post::where(['user_id'=>$profile_user->id,'status'=>1,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name')->orderBy('created_at','desc')->paginate(12);
                $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
                $meta=$this->get_meta($posts);
                $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
                return response()->json($response, 200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

      // function for get user trending by user id
    public function trending_posts(Request $request,$id){
        try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $posts=Post::where(['user_id'=>$profile_user->id,'status'=>1,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name')->orderBy('views','desc')->paginate(12);
                $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
                $meta=$this->get_meta($posts);
                $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
                return response()->json($response, 200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

      // function for get user latest opinion by user id
    public function latest_opinions(Request $request,$id){
       try{
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
           

              // Rejected Opinions Start
                        $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1])->orderBy('created_at','desc')->get();
                        $rej_opinion_id = [];

                        foreach ($rej_opinions as $rej_opinion) {
                            //$rej_opinion = $rej_opinion->links;
                            $rej_opinion_dummy=json_decode($rej_opinion->links);
                                foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                                   
                                   if($r_opinion->image=="null"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                   }

                                    
                                }
                        }
                        $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>""])->orderBy('created_at','desc')->get();
                        foreach ($img_opinions as $img_opinion) {
                            array_push($rej_opinion_id,$img_opinion->id);
                        }
                       // var_dump($rej_opinion_id);
                        // Rejected opinion end
                        
                $opinions=ShortOpinion::where(['user_id'=>$profile_user->id,'is_active'=>1])->whereNotIn('short_opinions.id',$rej_opinion_id)->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->paginate(12);
                foreach ($opinions as $opinion){
                  if($opinion->links!=null){
                        $opinion_dummy=json_decode($opinion->links);
                        foreach ($opinion_dummy as $index=>$r_opinion) {
                   
                           if($r_opinion->status=="error"){
                                $opinion->links = "null";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                            }
                            elseif($r_opinion->image==null){
                            $r_opinion->image="https://weopined.com/img/noimg.png";
                            $r_opinion->imageWidth=640;
                            $r_opinion->imageHeight=300;
                            $opinion->links = "[".json_encode($r_opinion)."]";
                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                            }
                        }
                    }
                }
                $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
                $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
               $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
               $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
              
                $my_liked_opinionids=Auth::check()?$this->my_liked_opinionids(Auth::user()->user_id):[];
                $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids, $Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids){
                    return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
                });
                $meta=$this->get_meta($opinions);
                $response=array('status'=>'success','result'=>1,'feed'=>$formatted,'meta'=>$meta);
                return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }
         }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Getting all the user points from the db
    public function get_points(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $point=Point::where(['user_id'=>$id])->first();
                $response=array('status'=>'success','result'=>1,'points'=>$point,'user_id'=>$id);
                return response()->json($response, 200);
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Getting all the achievements from the db
    public function get_achievements(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $achievement=DB::table('achievements')->get();
                $response=array('status'=>'success','result'=>1,'achievement'=>$achievement);
                return response()->json($response, 200);
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_user_achievements(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $userAchievement=UserAchievement::where(['user_id'=>$id])->get();
                $response=array('status'=>'success','result'=>1,'userAchievement'=>$userAchievement);
                return response()->json($response, 200);
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    //Updating email verified achievement for different user
    public function update_email_points(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $user=User::where(['id'=>$id, 'is_active'=>1])->first();
                if($user->email_verified==1) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>125])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>125,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$id,
                                'comment_points'=>0,
                                'reward_points'=>100,
                                'agree_points'=>0,
                                'follower_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>100,
                                ]);
                            }
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Email Verified Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Email Verified Achievement Already Unlocked');
                        return response()->json($response, 200);
                    }
                } else {
                    $response=array('status'=>'success','result'=>1,'message'=>'Email Verified Achievement not unlocked for this user');
                    return response()->json($response, 200);
                }
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating follower achievement for different user
    public function update_follower_achievement(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $get_followers = count(Follower::where(['leader_id'=>$id])->get());
                if($get_followers>=10 && $get_followers<30) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>109])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>109,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$id,
                                'agree_points'=>0,
                                'comment_points'=>0,
                                'follower_points'=>0,   
                                'reward_points'=>100,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>100,
                                ]);
                            }
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Acceptable Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Acceptable Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                }
                if($get_followers>=30 && $get_followers<100) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>110])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>110,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+200,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+200,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>200,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Admirable Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Admirable Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                }
                if($get_followers>=100  &&  $get_followers<500) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>111])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>111,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+500,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+500,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>500,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Wanted Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Wanted Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                    
                }
                if($get_followers>=500) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>112])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>112,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+1000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+1000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>1000,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Most Wanted Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Most Wanted Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                } else {
                    $response=array('status'=>'success','result'=>1,'message'=>'Not have enough followers to unlock achievement');
                    return response()->json($response, 200);
                }
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            } 
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating follower achievement for different user
    public function update_following_achievement(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $get_following = count(Follower::where(['follower_id'=>$id, 'is_active'=>1])->get());
                if($get_following>=20 && $get_following<100) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>106])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>106,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$id,
                                'agree_points'=>0,
                                'comment_points'=>0,
                                'follower_points'=>0,   
                                'reward_points'=>100,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>100,
                                ]);
                            }
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Friendly Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Friendly Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                }

                if($get_following>=100 && $get_following<300) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>107])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>107,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+200,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+200,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>200,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Affectionate Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Affectionate Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                }

                if($get_following>=300) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>108])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>108,
                            'user_id'=>$id
                        ]);
                        $point=Point::where(['user_id'=>$id])->first();
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+500,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+500,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>500,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Sociable Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Sociable Achievement already Unlocked');
                        return response()->json($response, 200);
                    }
                } else {
                    $response=array('status'=>'success','result'=>1,'message'=>'Not following enough users to unlock achievement');
                    return response()->json($response, 200);
                }
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            } 
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating daily points achievement for different user
    public function update_dailyPoints_achievement(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $point=Point::where(['user_id'=>$id])->first();
                if($point!=null) {
                    if($point->daily_points>=1000 && $point->daily_points<5000) {
                        $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>113])->first();
                        if($achievement==null) {
                            UserAchievement::create([
                                'achievements_id'=>113,
                                'user_id'=>$id
                            ]); 
                            Point::where(['user_id'=>$id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>100,
                                ]);
                            }
                            $response=array('status'=>'success','result'=>1,'message'=>'Achiever Achievement Unlocked');
                            return response()->json($response, 200);
                        } else {
                            $response=array('status'=>'success','result'=>1,'message'=>'Achiever Achievement already unlocked');
                            return response()->json($response, 200);
                        }
                    }
                    if($point->daily_points>=5000) {
                        $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>114])->first();
                        if($achievement==null) {
                            UserAchievement::create([
                                'achievements_id'=>114,
                                'user_id'=>$id
                            ]);
                            Point::where(['user_id'=>$id])->update([
                                'reward_points'=>$point->reward_points+500,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>$point->daily_points+500,
                                ]);
                            } else {
                                Point::where(['user_id'=>$id])->update([
                                    'daily_points'=>500,
                                ]);
                            }
                            $response=array('status'=>'success','result'=>1,'message'=>'Overachiever Achievement Unlocked');
                            return response()->json($response, 200);
                        } else {
                            $response=array('status'=>'success','result'=>1,'message'=>'Overachiever Achievement already unlocked');
                            return response()->json($response, 200);
                        }
                    } 
                    else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Not achieved the required daily points');
                        return response()->json($response, 200);
                    }
                }
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            } 
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating opinions achievement for different user
    public function update_opinion_achievement(Request $request,$id) {
        try {
            if(!isset($id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'user id is required');
                return response()->json($response,200);
            }
            $profile_user=User::where(['id'=>$id,'is_active'=>1])->first();
            if(!empty($profile_user)){
                $opinion=ShortOpinion::where(['user_id'=>$id,'is_active'=>1])->count();
                $videoOpinion=ShortOpinion::where(['user_id'=>$id,'cover_type'=>'EMBED','is_active'=>1])->count();
                $nonVideoOpinion=$opinion-$videoOpinion;
                $articleComment=Comment::where(['user_id'=>$id,'is_active'=>1])->count();
                $opinionComment=ShortOpinionComment::where(['user_id'=>$id,'is_active'=>1])->count();
                $pollVote=PollResults::where(['user_id'=>$id,'is_active'=>1])->count();
                if($videoOpinion>=1 && $nonVideoOpinion>=1 && $articleComment>=1 && $opinionComment>=1 && $pollVote>=1) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>103])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$id])->first();
                        UserAchievement::create([
                            'achievements_id'=>103,
                            'user_id'=>$id
                        ]); 
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+100,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+100,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>100,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Explorer Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Explorer Achievement already unlocked');
                        return response()->json($response, 200);
                    }    
                    
                } else if($videoOpinion>=10 && $nonVideoOpinion>=10 && $articleComment>=25 && $opinionComment>=50 && $pollVote>=10) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>104])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$id])->first();
                        UserAchievement::create([
                            'achievements_id'=>104,
                            'user_id'=>$id
                        ]); 
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+1000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+1000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>1000,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Scout Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Scout Achievement already unlocked');
                        return response()->json($response, 200);
                    }
                } else if($videoOpinion>=25 && $nonVideoOpinion>=50 && $articleComment>=50 && $opinionComment>=100 && $pollVote>=50) {
                    $achievement=UserAchievement::where(['user_id'=>$id, 'achievements_id'=>105])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$id])->first();
                        UserAchievement::create([
                            'achievements_id'=>105,
                            'user_id'=>$id
                        ]); 
                        Point::where(['user_id'=>$id])->update([
                            'reward_points'=>$point->reward_points+5000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>$point->daily_points+5000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$id])->update([
                                'daily_points'=>5000,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Champion Achievement Unlocked');
                        return response()->json($response, 200);
                    } else {
                        $response=array('status'=>'success','result'=>1,'message'=>'Champion Achievement already unlocked');
                        return response()->json($response, 200);
                    }
                } else {
                    $response=array('status'=>'success','result'=>1,'message'=>'Not have enough credits to unlock the achievement');
                    return response()->json($response, 200);
                }
            } else {
                $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                return response()->json($response,200);
            } 
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function user_followed_threads(Request $request, $id){
		
		try{
		$followed_threadids=ThreadFollower::where(['user_id'=>$id,'is_active'=>1])->pluck('thread_id')->toArray();
		$threads=Thread::
            select('id','name','views')
            ->where('is_active',1)
            ->whereIn('id',$followed_threadids)
             ->orderBy('Id','desc')
			 ->get();
			 $Cnt=count($threads);
            $response=array('status'=>'success','result'=>1,'Count'=>$Cnt,'threads'=>json_decode($threads));
            return response()->json($response, 200);
		 }catch(\Exception $e){
			   $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
				return response()->json($response, 500);
			 
		 }	 
			  
	  }

      public function is_thread_followed(Request $request, $id){

        try{
            $userid = Auth::user()->user_id;
            $followed_threadids=ThreadFollower::where(['user_id'=>$userid,'is_active'=>1])->pluck('thread_id')->toArray();
            //Get thread from id (thread name), it's unique
            $thread_id = Thread::where(['name'=>$id, 'is_active'=>1])->pluck('id')->first();

            if(in_array($thread_id,$followed_threadids)){
                $response=array('status'=>'success','result'=>1,'isFollowed'=>1,'thread_id'=>$thread_id);
                return response()->json($response, 200);
            }else{
                $response=array('status'=>'success','result'=>1,'isFollowed'=>0,'thread_id'=>$thread_id);
                return response()->json($response, 200);
            }


        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }

      }


}
