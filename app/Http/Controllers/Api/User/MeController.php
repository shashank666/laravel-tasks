<?php


namespace App\Http\Controllers\Api\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\ShortOpinionLike;


use App\Model\Post;
use App\Model\OfferPost;
use App\Model\Like;
use App\Model\ShortOpinion;
use App\Model\Bookmark;
use App\Model\Category;
use App\Model\CategoryFollower;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Follower;
use App\Model\Thread;
use App\Model\ThreadFollower;
use App\Model\Point;
use App\Model\GamificationReward;
use Carbon\Carbon;
use DB;

use Mail;
use App\Mail\OfferPost\PostEligibleMail;

use Notification;
use App\Notifications\Frontend\UserFollowed;
use App\Notifications\Frontend\PostLiked;

use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\PostLikedJob;
use App\Jobs\AndroidPush\UserFollowedJob;
use App\Model\ThreadOpinion;

class MeController extends Controller
{
    public function posts(Request $request){
        try{
            $my_liked_postids=$this->my_liked_postids(Auth::user()->user_id);
            $my_bookmarked_postids=$this->my_bookmarked_postids(Auth::user()->user_id);

            $posts=Post::where(['user_id'=>Auth::user()->user_id,'status'=>1,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name')->paginate(12);
            $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
            return response()->json($response, 200);
         }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function drafts(Request $request){
        try{
            $my_liked_postids=$this->my_liked_postids(Auth::user()->user_id);
            $my_bookmarked_postids=$this->my_bookmarked_postids(Auth::user()->user_id);

            $posts=Post::where(['user_id'=>Auth::user()->user_id,'status'=>0,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name')->paginate(12);
            $formatted=$this->format_api_posts($posts,$my_liked_postids,$my_bookmarked_postids);
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
            return response()->json($response, 200);
         }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function opinions(Request $request){
        try{
            // Rejected Opinions Start
                        $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->orderBy('created_at','desc')->get();
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
                        $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->orderBy('created_at','desc')->get();
                        foreach ($img_opinions as $img_opinion) {
                            array_push($rej_opinion_id,$img_opinion->id);
                        }
                       // var_dump($rej_opinion_id);
                        // Rejected opinion end
            $opinions=ShortOpinion::where(['user_id'=>Auth::user()->user_id,'is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.id',$rej_opinion_id)->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->paginate(12);
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
            $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
            $my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
			$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
             $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
             $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
                   
            // $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids){
            //     return $this->formatted_opinion_new($opinion,$my_liked_opinionids);
            // });
            // $meta=$this->get_meta($opinions);
            // $response=array('status'=>'success','result'=>1,'opinions'=>$formatted,'meta'=>$meta);
            // return response()->json($response,200);
            $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids, $my_agreed_opinionids,$my_disagreed_opinionids){
                unset($opinion->threads);
                return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
            });
           
            $meta=$this->get_meta($opinions);
           
            $response=array('status'=>'success','result'=>1,'feed'=>$formatted, 'meta'=>$meta);
            return response()->json($response, 200);
        }catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function followers(Request $request){
       try{

            $followers=Follower::where(['leader_id'=>Auth::user()->user_id,'is_active'=>1])->orderBy('created_at','desc')->with('follower')->paginate(12);
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
            $response=array('status'=>'success','result'=>1,'followers'=>$followers,'meta'=>$meta);
            return response()->json($response, 200);
         }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function following(Request $request){
        try{
            $users=Follower::where(['follower_id'=>Auth::user()->user_id,'is_active'=>1])->orderBy('created_at','desc')->with('leader')->paginate(12);
            $formatted=$this->format_follower($users);
            $meta=$this->get_meta($users);
            $response=array('status'=>'success','result'=>1,'following'=>$users,'meta'=>$meta);
            return response()->json($response, 200);
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function bookmarks(Request $request){
       
            $my_liked_postids=$this->my_liked_postids(Auth::user()->user_id);
            $my_bookmarked_postids=$this->my_bookmarked_postids(Auth::user()->user_id);

            $bookmarks=Bookmark::where(['bookmarks.user_id'=>Auth::user()->user_id,'is_active'=>1])->with('post')->orderBy('bookmarked_at','desc')->paginate(12);
            $formatted= $bookmarks->getCollection()->transform(function($bookmark,$key) use($my_liked_postids,$my_bookmarked_postids){
                $this->remove_null($bookmark);
                $formatted_post=$this->formatted_test_post($bookmark->post,$my_liked_postids,$my_bookmarked_postids);
                $custom_bookmark=[
                    'id'=>$bookmark->id,
                    'user_id'=>$bookmark->user_id,
                    'post_id'=>$bookmark->post_id,
                    'is_active'=>$bookmark->is_active,
                    'bookmarked_at'=>$bookmark->bookmarked_at,
                    'post'=> $formatted_post
                ];
                return $custom_bookmark;
            });
            $meta=$this->get_meta($bookmarks);
            $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
            return response()->json($response, 200);
        
    }

    public function manage_bookmark(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $post_id=$request->input('post_id');
                $bookmark_found=Bookmark::where(['user_id'=>Auth::user()->user_id,'post_id'=>$post_id])->first();
                if($bookmark_found){
                    Auth::user()->user->bookmarks()->detach($post_id);
                    $response=array('status'=>'success','result'=>1,'message'=>'Bookmark removed');
                }else{
                    Auth::user()->user->bookmarks()->attach($post_id);
                    $response=array('status'=>'success','result'=>1,'message'=>'Bookmark added');
                }
                return response()->json($response,200);
            }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function manage_likes(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{
                $post_id=$request->input('post_id');
                $post=Post::where(['id'=>$post_id,'is_active'=>1])->with('user')->first();
                //$offer_posts=OfferPost::where('user_id',$post->user['id'])->get();

                $like_found=Like::where(['user_id'=>Auth::user()->user_id,'post_id'=>$post_id])->first();
                if($like_found){
                    DB::table('likes')->where(['post_id'=>$post_id,'user_id'=>Auth::user()->user_id])->delete();
                    $post->likes=$post->likes-1;
                    $post->save();
                    DB::table('notifications')
                    ->where('data','like','%"event":"ARTICLE_LIKED"%')
                    ->where('data','like','%"post_id":'.$post_id.'%')
                    ->where('data','like','%"sender_id":'.Auth::user()->user_id.'%')
                    ->delete();
                    DB::table('fake_likes')->where(['post_id'=>$post_id,'user_id'=>Auth::user()->user_id])->delete();
                    $count=DB::table('likes')->where(['post_id'=>$post_id])->count();
                    $response=array('status'=>'success','result'=>0,'message'=>'Article like removed','total'=>$count);
                    return response()->json($response, 200);
                }else{

                    if(Auth::user()->user->is_active && Auth::user()->user->mobile_verified==1){
                        $post->increment('likes');
                        DB::table('likes')->insert(['post_id'=>$post_id,'user_id'=>Auth::user()->user_id,'ip_address'=>$request->ip(),'user_agent'=>$request->header("user-agent")]);
                        $this->notify_followers($post,'PostLiked');

                     /*  OFFER CRITERIA CONDITION
                    if($post->likes>=50 && count($offer_posts)<=2 && !in_array($post->id,$offer_posts->pluck('post_id')->toArray()) && $post->is_active==1 && $post->status==1 && $post->user['registered_as_writer']==1 && str_word_count($post->plainbody)>=400 && $post->plagiarism_checked==1 && $post->is_plagiarized==0){
                        $eligible_post=new OfferPost();
                        $eligible_post->offer_id=1;
                        $eligible_post->post_id=$post->id;
                        $eligible_post->user_id=$post->user['id'];
                        $eligible_post->save();
                        $this->send_email_for_post_eligible($post,$post->user);
                     }
                    */

                     $count=DB::table('likes')->where(['post_id'=>$post_id])->count();
                     $response=array('status'=>'success','result'=>1,'message'=>'Article liked','total'=>$count);
                     return response()->json($response, 200);
                    }
                }
            }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    //Updating follow points when user follows somebody
    public function manage_follow(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{

                $user_id=$request->input('user_id');
                if($user_id==Auth::user()->user_id){
                    $response=array('status'=>'error','result'=>0,'errors'=>'You can not add your self to circle');
                    return response()->json($response,200);
                }

                $user_found=User::find($user_id);
                $user_already_followed=Follower::where(['follower_id'=>Auth::user()->user_id,'leader_id'=>$user_id])->first();
                if($user_found){
                    if($user_already_followed){
                        Auth::user()->user->followings()->detach($user_id);
                        DB::table('notifications')
                        ->where('notifiable_id',$user_found->id)
                        ->where('data','like','%"event":"ADDED_IN_CIRCLE"%')
                        ->where('data','like','%"follower_id":'.Auth::user()->user_id.'%')
                        ->delete();
                        //Updating follow points when user unfollows somebody
                        $point=Point::where(['user_id'=>$user_id])->first();
                        Point::where(['user_id'=>$user_id])->update([
                            'follower_points'=>$point->follower_points-50,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$user_id])->update([
                                'daily_points'=>$point->daily_points-50,
                            ]);
                        }
                        $response=array('status'=>'success','result'=>1,'message'=>'unfollowed');
                    }else{
                        Auth::user()->user->followings()->attach($user_id);
                        try{
                            $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$user_found->id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                            dispatch(new UserFollowedJob(Auth::user()->user,$users_fcm_tokens));
                            Notification::send($user_found,new UserFollowed(Auth::user()->user,$users_fcm_tokens));
                        }catch(\Exception $e){}

                        $response=array('status'=>'success','result'=>1,'message'=>'followed');
                        //Updating follow points when user follows somebody
                        $point=Point::where(['user_id'=>$user_id])->first();
                        if($point==null) {
                            Point::create([
                                'user_id'=>$user_id,
                                'agree_points'=>0,
                                'comment_points'=>0,
                                'follower_points'=>50, 
                                'reward_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>50
                            ]);
                        } else {
                            Point::where(['user_id'=>$user_id])->update([
                                'follower_points'=>$point->follower_points+50,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>$point->daily_points+50,
                                ]);
                            } else {
                                Point::where(['user_id'=>$user_id])->update([
                                    'daily_points'=>50,
                                ]);
                            }
                        }
                    }
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'User not found');
                    return response()->json($response,200);
                }
            }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function manage_category_follow(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'category_id'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            }else{

            $category_id=$request->input('category_id');
            $category_found=Category::find($category_id);
            $category_already_followed=CategoryFollower::where(['user_id'=>Auth::user()->user_id,'category_id'=>$category_id])->first();
            if($category_found){
                if($category_already_followed){
                    Auth::user()->user->follow_category()->detach($category_id);
                    $response=array('status'=>'success','result'=>1,'message'=>'Category unfollowed');
                }else{
                    Auth::user()->user->follow_category()->attach($category_id);
                    $response=array('status'=>'success','result'=>1,'message'=>'Category followed');
               }
               return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Category not found');
                return response()->json($response,200);
            }
          }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function manage_categories(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'categories'=>'required',
            ]);
            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $categories=explode(",",$request->input('categories'));
                Auth::user()->user->follow_category()->sync($categories);
                $response=array('status'=>'success','result'=>1,'message'=>'We will remember your interested topics');
                return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function performance(Request $request){
        try{
            $posts=Post::where(['user_id'=>Auth::user()->user_id,'status'=>1,'is_active'=>1])
            ->orderBy('created_at','desc')
            ->paginate(12);

            $formatted=$posts->getCollection()->transform(function($post,$key){
                $this->remove_null($post);
                $custom_post= [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug'=> $post->slug,
                    'uuid'=>$post->uuid,
                    'coverimage'=>$post->coverimage,
                    'views'=>$post->views,
                    'likes_count'=>$post->likes_count==null?0:$post->likes_count,
                    'comments_count'=>$post->comments_count==null?0:$post->comments_count,
                    'created_at'=>$post->created_at,
                    'updated_at'=>$post->updated_at,
                ];
                return $custom_post;
            });
            $meta=$this->get_meta($posts);
            $response=array('status'=>'success','result'=>1,'posts'=>$formatted,'meta'=>$meta);
            return response()->json($response,200);

        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_followed_category(Request $request){
        try{

            $category_already_followed=CategoryFollower::where(['user_id'=>Auth::user()->user_id])->get();


            $categorylist=[];
            foreach($category_already_followed as $cat){
                $category=DB::table('categories')->where(['id'=>$cat->category_id])->first();
                

                if($category){
                    if(!in_array($category,$categorylist)){
    
                        array_push($categorylist,$category);
                    }
                }
            }
            $response=array('status'=>'success','result'=>1,'category'=>$categorylist);

            return response()->json($response,200);
            
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }



    public function post_performance(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
                'from'=>'required',
                'to'=>'required',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
            $post_id=$request->input('post_id');
            $fromDate=Carbon::createFromFormat('Y-m-d',$request->input('from'));
            $toDate= Carbon::createFromFormat('Y-m-d',$request->input('to'));

            if($fromDate>$toDate){
                $response=array('status'=>'error','result'=>0,'errors'=>'from date must be less than to date');
                return response()->json($response, 200);
            }

            $post_found=Post::select('id','title','coverimage','created_at')->where(['id'=>$post_id,'user_id'=>Auth::user()->user_id,'is_active'=>1,'status'=>1])->first();
            if($post_found){
                $post_views=
                DB::table('views')
                ->where('post_id',$post_id)
                ->select(DB::raw('DATE_FORMAT(viewed_at,"%d %M %Y") as viewed_at'),DB::raw("(COUNT(*)) as total"))
                ->orderBy('viewed_at','desc')
                ->whereBetween('viewed_at',[$fromDate,$toDate])
                ->groupBy(DB::raw('Date(viewed_at)'))
                ->get();

                $post_likes=
                DB::table('likes')
                ->where('post_id',$post_id)
                ->select(DB::raw('DATE_FORMAT(liked_at,"%d %M %Y") as liked_at'),DB::raw("(COUNT(*)) as total"))
                ->orderBy('liked_at','desc')
                ->whereBetween('liked_at',[$fromDate,$toDate])
                ->groupBy(DB::raw('Date(liked_at)'))
                ->get();

                $dates = [];
                for($date = $fromDate; $date->lte($toDate); $date->addDay()) {
                    array_push($dates,array('date'=>$date->format('d F Y'),'likes'=>0,'views'=>0));
                }

                foreach($dates as $index=>$date){
                    foreach($post_likes as $post){
                       $post=get_object_vars($post);
                       if(strcmp($date['date'],$post['liked_at'])===0){
                            $dates[$index]['likes']=intval($post['total']);
                       }
                    }

                    foreach($post_views as $post){
                        $post=get_object_vars($post);
                        if(strcmp($date['date'],$post['viewed_at'])===0){
                             $dates[$index]['views']=intval($post['total']);
                        }
                     }
                }
                $this->remove_null($post_found);
                $response=array('status'=>'success','result'=>1,'post'=>$post_found,'stats'=>$dates);
                return response()->json($response, 200);
           }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                return response()->json($response, 200);
           }
        }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    /* FUNCTION TO SEND EMAIL FOR POST ELIGIBLE FOR OFFER

    protected function send_email_for_post_eligible(Post $post,User $user){
        try {
           Mail::send(new PostEligibleMail($user,$post));
        } catch (Exception $e) {}
    }
    */

    protected function notify_followers($post,$event){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($post->user->id!==Auth::user()->user_id && !in_array($post->user->id,$follower_ids)){
            array_push($follower_ids,$post->user->id);
            $followers->push($post->user);
        }

        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
       try{
            foreach(array_chunk($fcm_tokens,100) as $chunk){
                dispatch(new PostLikedJob($post,Auth::user()->user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new PostLiked($post,Auth::user()->user,$fcm_tokens));
            }
        }catch(\Exception $e){}
    }

	public function ThreadsIFollow(Request $request){
		
		try{
		$followed_threadids=ThreadFollower::where(['user_id'=>Auth::user()->user_id,'is_active'=>1])->pluck('thread_id')->toArray();
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

      public function get_profile_images(Request $request){
		
            try {
                $profileImages = DB::table('profile_images')
                    ->select('id', 'name', 'url')
                    ->get();

                $response = array(
                    'status' => 'success',
                    'result' => 1,
                    'profile_images' => $profileImages
                );

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response = array(
                    'status' => 'error',
                    'result' => 0,
                    'errors' => 'Internal Server Error'
                );

                return response()->json($response, 500);
            }

			  
	  }

      public function unlock(Request $request)
{
    try {
        $user_id = Auth::user()->user_id;
        $achievementId = $request->input('achievement_id');

        if (empty($achievementId)) {
            return response()->json(['message' => 'Achievement ID is missing'], 400);
        }

        $achievement = DB::table('achievements')->where('achievement_id', $achievementId)->first();

        $userHasUnlockedAchievement = DB::table('user_achievements')
            ->where('user_id', $user_id)
            ->where('achievements_id', $achievementId)
            ->exists();

        if (!$userHasUnlockedAchievement) {
            DB::table('user_achievements')->insert([
                'user_id' => $user_id,
                'achievements_id' => $achievementId,
            ]);

            $reward = new GamificationReward();
            $reward->user_id = $user_id;
            $reward->reward_type = 'achievement: ' . $achievement->title;
            $reward->reward_amount = $achievement->reward;
            $reward->save();

            return response()->json(['message' => 'Achievement unlocked', 'reward' => $reward], 200);
        }

        return response()->json(['message' => 'Achievement already unlocked'], 200);
    } catch (\Exception $e) {
        $response = [
            'status' => 'error',
            'result' => 0,
            'errors' => 'Internal Server Error: ' . $e->getMessage()
        ];
        return response()->json($response, 500);
    }
}

      

//     public function unlocked(Request $request)
// {
//     $user_id = Auth::user()->user_id;

//     $unlockedAchievements = DB::table('achievements')
//         ->join('user_achievements', 'achievements.id', '=', 'user_achievements.achievement_id')
//         ->where('user_achievements.user_id', $user_id)
//         ->get();

//     //return with status and result
//     $response = array(
//         'status' => 'success',
//         'result' => 1,
//         'unlocked_achievements' => $unlockedAchievements
//     );

//     return response()->json($response, 200);
// }



}
