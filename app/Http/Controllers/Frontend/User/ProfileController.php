<?php

namespace App\Http\Controllers\Frontend\User;

use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Model\User;
use App\Model\Post;
use App\Model\ShortOpinion;
use App\Model\Achievement;
use App\Model\Follower;
use App\Model\Point;
use App\Model\PollResults;
use App\Model\ShortOpinionComment;
use App\Model\UserAchievement;
use App\Model\Comment;
use Carbon\Carbon;
use DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        View::share('google_ad',$google_ad);
    }

     // function for showing logged in user profile
     public function index(Request $request)
     {
         //My Personal Comment
        $bookmarked_posts=$this->my_liked_postids(Auth::user()->id);
        $liked_posts=$this->my_bookmarked_postids(Auth::user()->id);
        $latest_posts=Post::where(['user_id'=>Auth::user()->id,'status'=>1,'is_active'=>1])->with('categories')->orderBy('created_at','desc')->take(4)->get();
        $short_opinions=ShortOpinion::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('user')->orderBy('created_at','desc')->paginate(20);
        $liked=auth()->user()->likes->pluck('id')->toArray();
        $disliked=auth()->user()->Disagree->pluck('id')->toArray();
        $achievements=Achievement::get();
        $followers=Follower::where(['leader_id'=>Auth::user()->id, 'is_active'=>1])->get();
        $following=Follower::where(['follower_id'=>Auth::user()->id, 'is_active'=>1])->get();
        $posts=Post::where(['is_active'=>1,'user_id'=>Auth::user()->id])->get();
        $this->update_following_achievements();
        $this->update_follower_achievements();
        $this->update_email_points();
        $this->update_dailyPoints_achievements();
        $this->update_opinions_achievements();
        $user_achievements=UserAchievement::where(['user_id'=>Auth::user()->id])->get();
        $i=0;
        $unlock_achievements=[];
        foreach($achievements as $achievement){
            $i+=1;
            $user_achievements=UserAchievement::where(['user_id'=>Auth::user()->id])->get();
            foreach($user_achievements as $user_achievement) {
                if($user_achievement->achievements_id == $achievement->achievement_id) {
                    array_push($unlock_achievements, $achievements[$i-1]);
                    unset($achievements[$i-1]);
                }
            }
        }
        $point=Point::where(['user_id'=>Auth::user()->id])->first();
        if($point==null) {
            $total_point=0;
            $rank='Beginner';
            $width=0;
        } else {
            $total_point=$point->agree_points + $point->comment_points+$point->reward_points+$point->post_points+$point->follower_points+$point->share_points;
            if($total_point<1000) {
                $rank='Beginner';
                $width=($total_point*100)/1000;
            } else if($total_point>=1000 && $total_point<5000) {
                $rank='Conceptualiser';
                $width=($total_point*100)/5000;
            } else if($total_point>=5000 && $total_point<10000) {
                $rank='Competent';
                $width=($total_point*100)/10000;
            } else if($total_point>=10000 && $total_point<20000) {
                $rank='Proficient';
                $width=($total_point*100)/20000;
            } else {
                $rank='Influencer';
                $width=100;
            }
        }

        if ($request->ajax()) {
            $view = (String) view('frontend.profile.components.opinions',compact('short_opinions','liked','disliked'));
            return response()->json(['html'=>$view]);
        }else{
            return view('frontend.profile.index')->with([
                    'latest_posts'=>$latest_posts,
                    'bookmarked_posts'=>$bookmarked_posts,
                    'liked_posts'=>$liked_posts,
                    'short_opinions'=>$short_opinions,
                    'liked'=>$liked,
                    'disliked'=>$disliked,
                    'achievements'=>$achievements,
                    'followers'=>$followers,
                    'following'=>$following,
                    'unlock_achievements'=>$unlock_achievements,
                    'posts'=>$posts,
                    'width'=>$width,
                    'rank'=>$rank]);
        }

     }

    // function for show edit user profile page
    public function edit_profile(){
        return view('frontend.profile.edit');
    }

    // function for update user bio
    public function update_bio(Request $request){
        $this->validate($request,[
            'bio'=>'required'
        ]);
        $user=User::find(auth()->user()->id);
        $user->bio=$request->input('bio');
        $user->save();
        return redirect()->route('profile')->with(['message'=>'Your profile has been successfully updated.']);
    }

    // function for update user profile
    public function update_profile(Request $request){
        $this->validate($request,[
            'name'=>'required|string|max:255',
            'gender'=>'required',
            'birthdate'=>'required',
        ]);

        $userid=auth()->user()->id;
        $user=User::find($userid);

        $name=$request->input('name');
        $image=$request->input('profileimageurl')!=null?$request->input('profileimageurl'):'https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png';
        $birthdate=$request->has('birthdate') && $request->input('birthdate')!="null"?$request->input('birthdate'):null;
        $gender=$request->has('gender')?$request->input('gender'):null;
        $is_subscribed=$request->input('is_subscribed')=='on'?true:false;
        $keywords=$request->has('keywords') && $request->input('keywords')!="null"?$request->input('keywords'):null;
        $bio=$request->has('bio')?$request->input('bio'):null;
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
        $user->birthdate=$birthdate;
        $user->gender=$gender;
        $user->facebook_url=$facebook_url;
        $user->twitter_url=$twitter_url;
        $user->linkedin_url=$linkedin_url;
        $user->instagram_url=$instagram_url;
        $user->youtube_channel_url=$youtube_channel_url;
        $user->website_url=$website_url;
        $user->save();
        return redirect('/me/profile')->with(['message'=>'Your profile has been successfully updated.']);
    }


 // function for update user profile Picture
    public function update_profile_picture(Request $request){
        

        $userid=auth()->user()->id;
        $user=User::find($userid);

        $image=$request->input('profileimageurl')!=null?$request->input('profileimageurl'):'https://opined-s3.s3.ap-south-1.amazonaws.com/storage/app/public/profile/user.png';

        $user->image=$image;
        
        $user->save();
        $achievement=UserAchievement::where(['user_id'=>auth()->user()->id, 'achievements_id'=>123])->first();
        if($request->input('profileimageurl')!=null) {
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>123,
                    'user_id'=>auth()->user()->id
                ]);
                $point=Point::where(['user_id'=>auth()->user()->id])->first();
                if($point==null) {
                    Point::create([
                        'user_id'=>auth()->user()->id,
                        'agree_points'=>0,
                        'comment_points'=>0,
                        'follower_points'=>0,   
                        'reward_points'=>100,
                        'post_points'=>0,
                        'share_points'=>0,
                        'daily_points'=>100
                    ]);
                } else {
                    Point::where(['user_id'=>auth()->user()->id])->update([
                        'reward_points'=>$point->reward_points+100,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+100,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>100,
                        ]);
                    }
                }
            }
        }
        return redirect('/me/profile')->with(['message'=>'Your profile picture has been successfully updated.']);
    }

    // function for update user profile Picture
    public function update_cover_picture(Request $request){
        

        $userid=auth()->user()->id;
        $user=User::find($userid);

        $cover_image=$request->input('cover_imageurl')!=null?$request->input('cover_imageurl'):url('/storage/cover_image/cover_image.jpg');

        $user->cover_image=$cover_image;
        
        $user->save();
        return redirect('/me/profile')->with(['message'=>'Your Cover Image has been successfully updated.']);
    }

    public function update_following_achievements() {
        $get_following = count(Follower::where(['follower_id'=>Auth::user()->id, 'is_active'=>1])->get());
        if($get_following>=20 && $get_following<100) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>106])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>106,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                if($point==null) {
                    Point::create([
                        'user_id'=>Auth::user()->id,
                        'agree_points'=>0,
                        'comment_points'=>0,
                        'follower_points'=>0,   
                        'reward_points'=>100,
                        'post_points'=>0,
                        'share_points'=>0,
                        'daily_points'=>100
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'reward_points'=>$point->reward_points+100,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+100,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>100,
                        ]);
                    }
                }
            }
        }
        if($get_following>=100 && $get_following<300) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>107])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>107,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+200,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+200,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>200,
                    ]);
                }
            }
        }
        if($get_following>=300) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>108])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>108,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+500,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+500,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>500,
                    ]);
                }
            }
        }
    }

    public function update_follower_achievements() {
        $get_followers = count(Follower::where(['leader_id'=>Auth::user()->id])->get());
        if($get_followers>=10 && $get_followers<30) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>109])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>109,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                if($point==null) {
                    Point::create([
                        'user_id'=>Auth::user()->id,
                        'agree_points'=>0,
                        'comment_points'=>0,
                        'follower_points'=>0,   
                        'reward_points'=>100,
                        'post_points'=>0,
                        'share_points'=>0,
                        'daily_points'=>100
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'reward_points'=>$point->reward_points+100,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+100,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>100,
                        ]);
                    }
                }
            }
        }
        if($get_followers>=30 && $get_followers<100) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>110])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>110,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+200,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+200,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>200,
                    ]);
                }
            }
        }
        if($get_followers>=100  &&  $get_followers<500) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>111])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>111,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+500,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+500,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>500,
                    ]);
                }
            }
        }
        if($get_followers>=500) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>112])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>112,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+1000,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+1000,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>1000,
                    ]);
                }
            }
        }
    }

    public function update_email_points() {
        if(Auth::user()->email_verified==1) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>125])->first();
            if($achievement==null) {
                UserAchievement::create([
                    'achievements_id'=>125,
                    'user_id'=>Auth::user()->id
                ]);
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                if($point==null) {
                    Point::create([
                        'user_id'=>Auth::user()->id,
                        'comment_points'=>0,
                        'reward_points'=>100,
                        'agree_points'=>0,
                        'follower_points'=>0,
                        'post_points'=>0,
                        'share_points'=>0,
                        'daily_points'=>100
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'reward_points'=>$point->reward_points+100,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+100,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>100,
                        ]);
                    }
                }
            }
        }
    }

    public function update_dailyPoints_achievements() {
        $point=Point::where(['user_id'=>Auth::user()->id])->first();
        if($point!=null) {
            if($point->daily_points>=1000 && $point->daily_points<5000) {
                $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>113])->first();
                if($achievement==null) {
                    UserAchievement::create([
                        'achievements_id'=>113,
                        'user_id'=>Auth::user()->id
                    ]); 
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'reward_points'=>$point->reward_points+100,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+100,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>100,
                        ]);
                    }
                }
            }
            if($point->daily_points>=5000) {
                $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>114])->first();
                if($achievement==null) {
                    UserAchievement::create([
                        'achievements_id'=>114,
                        'user_id'=>Auth::user()->id
                    ]);
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'reward_points'=>$point->reward_points+500,
                    ]);
                    if(Carbon::now()->diffInDays($point->updated_at)==0) {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>$point->daily_points+500,
                        ]);
                    } else {
                        Point::where(['user_id'=>Auth::user()->id])->update([
                            'daily_points'=>500,
                        ]);
                    }
                }
            }
        }
    }

    public function update_opinions_achievements() {
        $opinion=ShortOpinion::where(['user_id'=>Auth::user()->id,'is_active'=>1])->count();
        $videoOpinion=ShortOpinion::where(['user_id'=>Auth::user()->id,'cover_type'=>'EMBED','is_active'=>1])->count();
        $nonVideoOpinion=$opinion-$videoOpinion;
        $articleComment=Comment::where(['user_id'=>Auth::user()->id,'is_active'=>1])->count();
        $opinionComment=ShortOpinionComment::where(['user_id'=>Auth::user()->id,'is_active'=>1])->count();
        $pollVote=PollResults::where(['user_id'=>Auth::user()->id,'is_active'=>1])->count();
        if($videoOpinion>=1 && $nonVideoOpinion>=1 && $articleComment>=1 && $opinionComment>=1 && $pollVote>=1) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>103])->first();
            if($achievement==null) {
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                UserAchievement::create([
                    'achievements_id'=>103,
                    'user_id'=>Auth::user()->id
                ]); 
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+100,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+100,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>100,
                    ]);
                }
            }
        } else if($videoOpinion>=10 && $nonVideoOpinion>=10 && $articleComment>=25 && $opinionComment>=50 && $pollVote>=10) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>104])->first();
            if($achievement==null) {
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                UserAchievement::create([
                    'achievements_id'=>104,
                    'user_id'=>Auth::user()->id
                ]); 
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+1000,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+1000,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>1000,
                    ]);
                }
            }
        } else if($videoOpinion>=25 && $nonVideoOpinion>=50 && $articleComment>=50 && $opinionComment>=100 && $pollVote>=50) {
            $achievement=UserAchievement::where(['user_id'=>Auth::user()->id, 'achievements_id'=>105])->first();
            if($achievement==null) {
                $point=Point::where(['user_id'=>Auth::user()->id])->first();
                UserAchievement::create([
                    'achievements_id'=>105,
                    'user_id'=>Auth::user()->id
                ]); 
                Point::where(['user_id'=>Auth::user()->id])->update([
                    'reward_points'=>$point->reward_points+5000,
                ]);
                if(Carbon::now()->diffInDays($point->updated_at)==0) {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>$point->daily_points+5000,
                    ]);
                } else {
                    Point::where(['user_id'=>Auth::user()->id])->update([
                        'daily_points'=>5000,
                    ]);
                }
            }
        }
    }
}