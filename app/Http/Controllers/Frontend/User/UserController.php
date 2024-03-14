<?php

namespace App\Http\Controllers\Frontend\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Model\User;
use App\Model\Category;
use App\Model\CategoryFollower;
use App\Model\Tag;
use App\Model\Post;
use App\Model\ShortOpinion;
use App\Model\Comment;
use App\Model\PollResults;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Achievement;
use App\Model\UserAchievement;
use App\Model\Point;
use App\Model\Like;
use Carbon\Carbon;
use DB;
use Config;
use Session;
use App\Events\UserProfileViewCounterEvent;


class UserController extends Controller
{


    public function __construct()
    {

    }


    // function for get user's profile
    public function get_user_profile(Request $request,$username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            event(new UserProfileViewCounterEvent($profile_user));
            $posts=Post::where(['is_active'=>1,'status'=>1,'user_id'=>$profile_user->id])->with('user','categories')->orderBy('created_at','desc')->take(4)->get();
            $short_opinions=ShortOpinion::where(['user_id'=>$profile_user->id,'is_active'=>1,'community_id'=>0])->with('user')->orderBy('created_at','desc')->paginate(20);
            if(Auth::check()){
                if($profile_user->id==auth()->user()->id){
                    return redirect('me/profile');
                }
                $followingids = auth()->user()->active_followings->pluck('id')->toArray();
                $liked=auth()->user()->likes->pluck('id')->toArray();
                $disliked=auth()->user()->Disagree->pluck('id')->toArray();
            }else{
                $followingids =[];
                $liked=[];
                $disliked=[];
            }
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            $achievements=Achievement::get();
            $user_achievements=UserAchievement::where(['user_id'=>$profile_user->id])->get();
            $followers=Follower::where(['leader_id'=>$profile_user->id, 'is_active'=>1])->get();
            $following=Follower::where(['follower_id'=>$profile_user->id, 'is_active'=>1])->get();
            $point=Point::where(['user_id'=>$profile_user->id])->first();
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


            

            $this->update_follower_achievements($profile_user->id);
            $this->update_following_achievements($profile_user->id);
            $this->update_email_points($profile_user->id);
            $this->update_opinions_achievements($profile_user->id);
            $this->update_dailyPoints_achievements($profile_user->id);
            $section='profile';
            if($request->ajax()){
                $view = (String) view('frontend.profile.components.opinions',compact('profile_user','short_opinions','followingids','liked','disliked'));
                return response()->json(['html'=>$view]);
            }else{
                return view('frontend.profile.user',compact('profile_user','posts','bookmarked_posts','liked_posts','followingids','short_opinions','liked','disliked','section','achievements','user_achievements','followers','following','width','rank'));
            }
        }else{
            abort(404);
        }
    }

    // function for get user's followers
    public function get_user_followers(Request $request,$username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            $users = DB::table('followers')
            ->leftJoin('users', 'users.id', '=', 'followers.follower_id')
            ->where('followers.leader_id', '=',$profile_user->id)
            ->where('followers.is_active',1)
            ->select('users.id','users.name','users.username','users.unique_id','users.email','users.bio','users.image')
            ->paginate(9);

            if(Auth::check()){
                $followingids = auth()->user()->active_followings->pluck('id')->toArray();
            }else{
                $followingids =[];
            }
            $section='in_circle';
            if($request->ajax()){
                $view = (String) view('frontend.profile.components.usersloop_three_col',compact('users','followingids'));
                return response()->json(['html'=>$view]);
            }else{
                return view('frontend.profile.user',compact('profile_user','users','followingids','section'));
            }
        }else{
            abort(404);
        }
    }

     // function for get user's following
    public function get_user_following(Request $request,$username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            $users = DB::table('followers')
            ->leftJoin('users', 'users.id', '=', 'followers.leader_id')
            ->where('followers.follower_id', '=',$profile_user->id)
            ->where('followers.is_active',1)
            ->select('users.id','users.name','users.username','users.unique_id','users.email','users.bio','users.image')
            ->paginate(9);

        if(Auth::check()){
            $followingids = auth()->user()->active_followings->pluck('id')->toArray();
        }else{
            $followingids =[];
        }
        $section='circle';
            if($request->ajax()){
                $view = (String) view('frontend.profile.components.usersloop_three_col',compact('users','followingids'));
                return response()->json(['html'=>$view]);
            }else{
                return view('frontend.profile.user',compact('profile_user','users','followingids','section'));
            }
        }else{
            abort(404);
        }
    }

    // function for get user's Articles
    public function get_user_article($username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
        $posts=Post::where(['status'=>1,'is_active'=>1,'user_id'=>$profile_user->id])->with('categories','user')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->my_posts_pagination);
        return view('frontend.profile.user-article',compact('profile_user','posts'));
        }
    }
     // function for get user's latest posts
    public function get_user_latestposts($username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            $posts=Post::where(['user_id'=>$profile_user->id,'status'=>1,'is_active'=>1])->with('categories')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->user_posts_pagination);
            $section='latest';
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            $followingids=Auth::check()?auth()->user()->active_followings->pluck('id')->toArray():[];
            return view('frontend.profile.user',compact('profile_user','posts','bookmarked_posts','liked_posts','followingids','section'));
        }else{
            abort(404);
        }
    }

     // function for get user's trending posts
    public function get_user_trendingposts($username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            $posts=Post::where(['user_id'=>$profile_user->id,'status'=>1,'is_active'=>1])->with('categories')->orderBy('views','desc')->paginate(12);
            $section='trending';
            $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
            $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
            $followingids=Auth::check()?auth()->user()->active_followings->pluck('id')->toArray():[];
            return view('frontend.profile.user',compact('profile_user','posts','bookmarked_posts','liked_posts','followingids','section'));
        }else{
            abort(404);
        }
    }

    // function for get user's short opinions
    public function get_user_shortopinions(Request $request,$username){
        $profile_user=User::where(['username'=>$username,'is_active'=>1])->first();
        if($profile_user){
            $posts=ShortOpinion::where('user_id',$profile_user->id)->where(['is_active'=>1,'community_id'=>0])->with('user')->orderBy('created_at','desc')->paginate(12);
            if(Auth::check()){
                $followingids = auth()->user()->active_followings->pluck('id')->toArray();
                $liked=auth()->user()->likes->pluck('id')->toArray();
                $disliked=auth()->user()->Disagree->pluck('id')->toArray();
            }else{
                $followingids =[];
                $disliked=[];
            }
            $section='posts';
            if($request->ajax()){

            }else{
                return view('frontend.opinions.show.user_opinions',compact('profile_user','followingids','posts','liked','disliked','section'));
            }
        }else{
            abort(404);
        }
    }

    public function update_following_achievements($user_id) {
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
            }
        }
    }

    public function update_follower_achievements($user_id) {
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
            }
        }
    }

    public function update_email_points($user_id) {
        $user=User::where('id',$user_id)->first();
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
            }
        }
    }

    public function update_dailyPoints_achievements($user_id) {
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
                }
            }
        }
    }

    public function update_opinions_achievements($user_id) {
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
            }
        }
    }


}
