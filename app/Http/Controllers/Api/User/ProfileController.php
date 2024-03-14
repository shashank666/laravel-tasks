<?php

namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Model\User;
use App\Model\UserAchievement;
use App\Model\Point;
use App\Model\ShortOpinion;
use App\Model\Comment;
use App\Model\PollResults;
use App\Model\ShortOpinionComment;
use App\Model\ReportUser;
use App\Model\BlockedUser;
use App\Model\Follower;
use App\Model\FileManager;
use ImageOptimizer;
use App\Jobs\Resize\ResizeImageJob;
use App\Model\Achievement;
use Illuminate\Contracts\Bus\Dispatcher;
use Carbon\Carbon;

use DB;

use Exception;

class ProfileController extends Controller
{


    public function get_user_profile(Request $request){
        try{
            $user=Auth::user()->user;
            $user['followers_count']= Auth::user()->user->active_followers()->count();
            $user['following_count']= Auth::user()->user->active_followings()->count();
            $opinion_count=ShortOpinion::where(['user_id'=>$user->id])->count();
            $user['opinion_count']= $opinion_count;
            $this->remove_null($user);
            $response=array('status'=>'success','result'=>1,'profile'=>$user);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Getting all the user points from the db
    public function get_points(Request $request) {
        try {
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            }
            $point=Point::where(['user_id'=>$user_id])->first();
            $response=array('status'=>'success','result'=>1,'points'=>$point,'user_id'=>$user_id);
            return response()->json($response, 200);

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Getting all the achievements from the db
    public function get_achievements(Request $request) {
        try {
            $achievement=DB::table('achievements')->get();
            $response=array('status'=>'success','result'=>1,'achievement'=>$achievement);
            return response()->json($response, 200);

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

      //Getting all the achievements from the db
      public function get_category(Request $request) {
        try {
            $category=DB::table('categories')->get();
            $response=array('status'=>'success','result'=>1,'category'=>$category);
            return response()->json($response, 200);

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    //Getting all the user achievement from the db
    public function get_user_achievements(Request $request) {
        try {
            // $user_id = Auth::user()->id;
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            }
            $userAchievement=UserAchievement::where(['user_id'=>$user_id])->get();
            // $meta=$this->get_meta($userAchievement);
            $response=array('status'=>'success','result'=>1,'userAchievement'=>$userAchievement);
            return response()->json($response, 200);

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error', 'error'=>$e);
            return response()->json($response, 500);
        }
    }

    //To update user profile image upload achievement
    public function update_user_profile(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'gender'=>'required',
                'image'=>'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $user=User::where(['id'=>Auth::user()->user_id,'is_active'=>1])->first();
                if($user){
                    $name=$request->has('name')?$request->input('name'):$user->name;
                    if($request->hasFile('image')){
                        $uniqueid=uniqid();
                        $original_name=$request->file('image')->getClientOriginalName();
                        $size=$request->file('image')->getSize();
                        $extension=$request->file('image')->getClientOriginalExtension();
                        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                        $imagepath=url('/storage/profile/'.$filename);
                        $path=$request->file('image')->storeAs('public/profile',$filename);
                        $size=$this->optimize_image($extension,'profile',$filename,$size);
                        $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'USER_PROFILE',$size,$extension,Auth::user()->user_id);
                        $image=$imagepath;
                        try{
                            $job = (new ResizeImageJob(storage_path('app/public/profile/'.$filename),storage_path('app/public/profile/'),[[100,100],[40,40]]))->onQueue('default');
                            app(Dispatcher::class)->dispatch($job);
                        }catch(\Exception $e){}
                    }else{
                        $image=$user->image==null?url('/img/profile-default-opined.png'):$user->image;
                    }

                    $birthdate=$request->has('birthdate') && $request->input('birthdate')!=null?$request->input('birthdate'):$user->birthdate;
                    $gender=$request->has('gender')?$request->input('gender'):$user->gender;
                    $is_subscribed=$request->has('is_subscribed') && $request->input('is_subscribed')!=null?$request->input('is_subscribed'):$user->is_subscribed;
                    $keywords=$request->has('keywords')?$request->input('keywords'):null;
                    $bio=$request->has('bio')?$request->input('bio'):$user->bio;
                    $facebook_url=$request->has('facebook_url')?$request->input('facebook_url'):null;
                    $twitter_url=$request->has('twitter_url')?$request->input('twitter_url'):null;
                    $linkedin_url=$request->has('linkedin_url')?$request->input('linkedin_url'):null;
                    $instagram_url=$request->has('instagram_url')?$request->input('instagram_url'):null;
                    $youtube_channel_url=$request->has('youtube_channel_url')?$request->input('youtube_channel_url'):null;
                    $website_url=$request->has('website_url')?$request->input('website_url'):null;

                    $user->name=$name;
                    $user->image=$image;
                    $user->is_subscribed=$is_subscribed;
                    $user->keywords=$keywords;
                    $user->bio=$bio;
                    $user->facebook_url=$facebook_url;
                    $user->twitter_url=$twitter_url;
                    $user->linkedin_url=$linkedin_url;
                    $user->instagram_url=$instagram_url;
                    $user->youtube_channel_url=$youtube_channel_url;
                    $user->website_url=$website_url;
                    $user->birthdate=$birthdate;
                    $user->gender=$gender;
                    $saved=$user->save();

                    if($saved){
                        $this->remove_null($user);
                        $achievement=UserAchievement::where(['user_id'=>$user->id, 'achievements_id'=>123])->first();
                        if($user->image!=null) {
                            if($achievement==null) {
                                UserAchievement::create([
                                    'achievements_id'=>123,
                                    'user_id'=>$user->id
                                ]);
                                $point=Point::where(['user_id'=>$user->id])->first();
                                if($point==null) {
                                    Point::create([
                                        'user_id'=>$user->id,
                                        'agree_points'=>0,
                                        'comment_points'=>0,
                                        'follower_points'=>0,   
                                        'reward_points'=>100,
                                        'post_points'=>0,
                                        'share_points'=>0,
                                        'daily_points'=>100
                                    ]);
                                } else {
                                    Point::where(['user_id'=>$user->id])->update([
                                        'reward_points'=>$point->reward_points+100,
                                    ]);
                                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                        Point::where(['user_id'=>$user->id])->update([
                                            'daily_points'=>$point->daily_points+100,
                                        ]);
                                    } else {
                                        Point::where(['user_id'=>$user->id])->update([
                                            'daily_points'=>100,
                                        ]);
                                    }
                                }
                            }
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'Profile successfully updated','user'=>$user);
                        return response()->json($response, 200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Failed to update profile');
                        return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No user found');
                    return response()->json($response,200);
                }
           }
        }
         catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function upload_and_update_cover(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'cover_image'=>'required|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $user=User::where(['id'=>Auth::user()->user_id,'is_active'=>1])->first();
                if($user){
                    $uniqueid=uniqid();
                    $original_name=$request->file('cover_image')->getClientOriginalName();
                    $size=$request->file('cover_image')->getSize();
                    $extension=$request->file('cover_image')->getClientOriginalExtension();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath=url('/storage/cover_image/'.$filename);
                    $path=$request->file('cover_image')->storeAs('public/cover_image',$filename);
                    $size=$this->optimize_image($extension,'cover_image',$filename,$size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'USER_COVER',$size,$extension,Auth::user()->user_id);
                    $user->cover_image=$imagepath;
                    $saved=$user->save();
                    try{
                        $job = (new ResizeImageJob(storage_path('app/public/cover_image/'.$filename),storage_path('app/public/cover_image/'),[[760, 200]]))->onQueue('default');
                        app(Dispatcher::class)->dispatch($job);
                    }catch(\Exception $e){}
                    if($saved){
                        $this->remove_null($user);
                        $response=array('status'=>'success','result'=>1,'message'=>'Profile successfully updated','user'=>$user);
                        return response()->json($response, 200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Failed to update profile');
                        return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'No user found');
                    return response()->json($response,200);
                }
           }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //To update the email verification achievement
    //Call this when Profile Opens
    public function update_email_points(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                $user_id=$request->input('user_id');
                $user=User::where(['id'=>$user_id, 'is_active'=>1])->first();
                if($user->email_verified==1) {
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>125])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>125,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$user_id,
                                'comment_points'=>0,
                                'reward_points'=>100,
                                'agree_points'=>0,
                                'follower_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
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
                    $response=array('status'=>'success','result'=>0,'message'=>'Verify your email to unlock this achievement');
                    return response()->json($response, 200);
                }
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //To update the user follower achievements (user following count)
    public function update_follower_achievement(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                $user_id=$request->input('user_id');
                $get_followers = count(Follower::where(['leader_id'=>$user_id])->get());
                if($get_followers>=10 && $get_followers<30) {
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>109])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>109,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$user_id,
                                'agree_points'=>0,
                                'comment_points'=>0,
                                'follower_points'=>0,   
                                'reward_points'=>100,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>110])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>110,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+200,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+200,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>111])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>111,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+500,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+500,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>112])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>112,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+1000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+1000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $response=array('status'=>'success','result'=>1,'message'=>'Achievement not Unlocked');
                    return response()->json($response, 200);
                }
            }

        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //To update the user following achievements (user following count)
    public function update_following_achievement(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                $user_id=$request->input('user_id');
                $get_following = count(Follower::where(['follower_id'=>$user_id, 'is_active'=>1])->get());
                if($get_following>=20 && $get_following<100) {
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>106])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>106,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$user_id,
                                'agree_points'=>0,
                                'comment_points'=>0,
                                'follower_points'=>0,   
                                'reward_points'=>100,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>100
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>107])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>107,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+200,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+200,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>108])->first();
                    if($achievement==null) {
                        UserAchievement::create([
                            'achievements_id'=>108,
                            'user_id'=>$user_id
                        ]);
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+500,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+500,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $response=array('status'=>'success','result'=>1,'message'=>'No achievemnet unlocked');
                    return response()->json($response, 200);
                }
            }
        } catch (Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    
    //To update the user daily point achievement
    public function update_dailyPoints_achievements(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                $user_id=$request->input('user_id');
                $point=Point::where(['user_id'=>$user_id])->first();
                if($point!=null) {
                    if($point->daily_points>=1000 && $point->daily_points<5000) {
                        $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>113])->first();
                        if($achievement==null) {
                            UserAchievement::create([
                                'achievements_id'=>113,
                                'user_id'=>$user_id
                            ]); 
                            Point::where(['user_id'=>$user_id])->update([
                                'reward_points'=>$point->reward_points+100,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+100,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
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
                        $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>114])->first();
                        if($achievement==null) {
                            UserAchievement::create([
                                'achievements_id'=>114,
                                'user_id'=>$user_id
                            ]);
                            Point::where(['user_id'=>$user_id])->update([
                                'reward_points'=>$point->reward_points+500,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+500,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
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
            }
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //To update the opinion/poll/comment achievement
    public function update_opinions_achievements(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                $user_id=$request->input('user_id');
                $opinion=ShortOpinion::where(['user_id'=>$user_id,'is_active'=>1])->count();
                $videoOpinion=ShortOpinion::where(['user_id'=>$user_id,'cover_type'=>'EMBED','is_active'=>1])->count();
                $nonVideoOpinion=$opinion-$videoOpinion;
                $articleComment=Comment::where(['user_id'=>$user_id,'is_active'=>1])->count();
                $opinionComment=ShortOpinionComment::where(['user_id'=>$user_id,'is_active'=>1])->count();
                $pollVote=PollResults::where(['user_id'=>$user_id,'is_active'=>1])->count();
                if($videoOpinion>=1 && $nonVideoOpinion>=1 && $articleComment>=1 && $opinionComment>=1 && $pollVote>=1) {
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>103])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$user_id])->first();
                        UserAchievement::create([
                            'achievements_id'=>103,
                            'user_id'=>$user_id
                        ]); 
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+100,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+100,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>104])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$user_id])->first();
                        UserAchievement::create([
                            'achievements_id'=>104,
                            'user_id'=>$user_id
                        ]); 
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+1000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+1000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
                    $achievement=UserAchievement::where(['user_id'=>$user_id, 'achievements_id'=>105])->first();
                    if($achievement==null) {
                        $point=Point::where(['user_id'=>$user_id])->first();
                        UserAchievement::create([
                            'achievements_id'=>105,
                            'user_id'=>$user_id
                        ]); 
                        Point::where(['user_id'=>$user_id])->update([
                            'reward_points'=>$point->reward_points+5000,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points+5000,
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
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
            }
        } catch(Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function report(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
                'flag'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                    //var_dump($request->input('user_id'));
                    $flag_reason=explode("--",$request->input('flag'));
                    $report_user=new ReportUser();
                    //$report_user->user_id=$request->input('reportuser');
                    $report_user->user_id=$request->input('user_id');
                    $report_user->reported_user_id=Auth::user()->user_id;
                    $report_user->report_flag=(int)$flag_reason[0];
                    $report_user->report_reason=$flag_reason[1];
                    $report_user->save();

                    $response=array('status'=>'success','result'=>1,'message'=>'Issue successfully reported.');
                    return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function block_user(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                    //var_dump($request->input('user_id'));
                   
                    $block_user=new BlockedUser();
                    //$report_user->user_id=$request->input('reportuser');
                    $block_user->blocked_id=$request->input('user_id');
                    $block_user->user_id=Auth::user()->user_id;
                    $block_user->save();

                    $response=array('status'=>'success','result'=>1,'message'=>'User Blocked');
                    return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function unblock_user(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'user_id'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                   
                    DB::table('blocked_users')->where(['user_id'=>Auth::user()->user_id, 'blocked_id'=>$request->input('user_id')])->delete();
                    //$report_user->user_id=$request->input('reportuser');

                    $response=array('status'=>'success','result'=>1,'message'=>'User Unblocked');
                    return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function is_active_mode_changer($user_id,$is_active,$event){
        DB::transaction(function () use($user_id,$is_active,$event){
            DB::table('report_user')->where('user_id', '=', $user_id)->update(['is_active' => $is_active]);
        });
    }

    public function updateUserImage(Request $request)
        {
            $user = User::where(['id' => Auth::user()->user_id, 'is_active' => 1])->first();
            
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not found or not active'], 404);
            }

            $imageUrl = $request->input('url');

            $user->image = $imageUrl;
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'User image updated'], 200);
        }

}
