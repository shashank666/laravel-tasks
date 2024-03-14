<?php

namespace App\Http\Controllers\Api\Opinion;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ThreadLike;
use App\Model\CategoryThread;
use App\Model\ThreadFollower;
use App\Model\ShortOpinion;
use App\Model\Achievement;
use App\Model\ShortOpinionLike;
use App\Model\ShortOpinionComment;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Model\Point;
use DB;
use App\Events\ThreadViewCounterEvent;
use App\Events\OpinionViewCounterEvent;

use Carbon\Carbon;
use App\Model\GamificationReward;
use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\AchievementUnlockedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;

class OpinionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api',['except'=>
        ['index',
        'get_trending_opinions_by_thread_id']
        ]);
    }

    public function index(Request $request){
        try{
            $latest_threads=[];
            $trending_threads=[];
            $circle_threads=[];
            $user_id=-1;
            $my_liked_opinionids=[];
			$my_agreed_opinionids=[];
			$my_disagreed_opinionids=[];
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
            }
			$my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
			$Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
        
            $latest_threads=Thread::select('id','name','views')
            ->where('is_active',1)
            ->withCount(['opinions','comment','followers'])
            ->has('opinions', '>', 0)
            ->orderBy('created_at','desc')
            ->take(6)
            ->get();


            foreach($latest_threads as $thread){
                $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
                $latest_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
                foreach($latest_thread_opinion_ids as $id){
                    $found=ShortOpinion::where(['id'=>$id,'is_active'=>1,'community_id'=>0])->with('user:id,name,username,unique_id,image')->first();
                    if($found){
						//$found->is_agreed=in_array($found->id,$my_agreed_opinionids); //$Agree_ids
						//$found->is_disagreed=in_array($found->id,$my_disagreed_opinionids); //$DisAgree_ids
                        // event(new OpinionViewCounterEvent($found,$request->ip()));
                        if($found->links!=null){
                            $opinion_dummy=json_decode($found->links);
                            foreach ($opinion_dummy as $index=>$r_opinion) {
                       
                               if($r_opinion->status=="error"){
                                    $found->links = "null";
                                    //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                }
                                elseif($r_opinion->image==null){
                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                $r_opinion->imageWidth=640;
                                $r_opinion->imageHeight=300;
                                $found->links = "[".json_encode($r_opinion)."]";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                               }
                            }
                        
                        }
                        $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
						//formatted_opinion($found,$my_liked_opinionids);//,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
                        $thread->opinion=$formatted;
                    }
                }
            }

            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

            $trending_threads_ids=DB::table('thread_opinions')->where('is_active',1)->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))->groupBy('thread_id')->orderBy('count','desc')->take(6)->get()->pluck('thread_id')->toArray();
            $placeholders = implode(',',array_fill(0, count($trending_threads_ids), '?'));
            $trending_threads=Thread::select('id','name','views')->where('is_active',1)->whereIn('id',$trending_threads_ids)->withCount(['opinions','comment','followers'])->has('opinions', '>', 0)->orderByRaw("field(id,{$placeholders})", $trending_threads_ids)->take(6)->get();

            foreach($trending_threads as $thread){
                $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
                $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
                foreach($trending_thread_opinion_ids as $id){
                    $found=ShortOpinion::where(['id'=>$id,'is_active'=>1,'community_id'=>0])->with('user:id,name,username,unique_id,image')->first();
                    if($found){
                        if($found->links!=null){
                            $opinion_dummy=json_decode($found->links);
                            foreach ($opinion_dummy as $index=>$r_opinion) {
                       
                               if($r_opinion->status=="error"){
                                    $found->links = "null";
                                    //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                }
                                elseif($r_opinion->image==null){
                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                $r_opinion->imageWidth=640;
                                $r_opinion->imageHeight=300;
                                $found->links = "[".json_encode($r_opinion)."]";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                               }
                            }
                        }
                        $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
						//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
                        $thread->opinion=$formatted;
                    }
                }
            }

             if($user_id!=-1){
                    $user=User::where(['id'=>$user_id,'is_active'=>1])->first();
                    if($user){
                        $following_ids=$user->active_followings->pluck('id')->toArray();

                        $short_opinion_ids=ShortOpinion::select('id')
                        ->where('is_active',1)
                        ->whereIn('user_id',$following_ids)
                        ->get()->pluck('id')->toArray();

                        $thread_ids=ThreadOpinion::select('thread_id')
                        ->where(['is_active'=>1])
                        ->whereIn('short_opinion_id',$short_opinion_ids)
                        ->distinct('thread_id')
                        ->get()->pluck('thread_id')->toArray();

                        $circle_threads=Thread::
                        select('id','name','views')
                        ->where('is_active',1)
                        ->whereIn('id',$thread_ids)
                        ->withCount(['opinions','comment','followers'])
                        ->has('opinions', '>', 0)
                        ->orderBy('opinions_count','desc')
                        ->take(6)
                        ->get();

                        foreach($circle_threads as $thread){
                            $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
                            $circle_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
                            foreach($circle_thread_opinion_ids as $id){
                                $found=ShortOpinion::where(['id'=>$id,'is_active'=>1,'community_id'=>0])->with('user:id,name,username,unique_id,image')->first();
                                if($found){
                                    if($found->links!=null){
                                            $opinion_dummy=json_decode($found->links);
                                            foreach ($opinion_dummy as $index=>$r_opinion) {
                                       
                                               if($r_opinion->status=="error"){
                                                    $found->links = "null";
                                                    //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                                }
                                                elseif($r_opinion->image==null){
                                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                                $r_opinion->imageWidth=640;
                                                $r_opinion->imageHeight=300;
                                                $found->links = "[".json_encode($r_opinion)."]";
                                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                               }
                                            }
                                      
                                        }
                                    $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);
                                    $thread->opinion=$formatted;
                                }
                            }
                        }
                    }
            }

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'latest_threads'=>$latest_threads,
                'trending_threads'=>$trending_threads,
                'circle_threads'=>$circle_threads
            ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error.');
            return response()->json($response, 500);
        }
    }
    public function index2(Request $request){
        try{
            $latest_threads=[];
            $trending_threads=[];
            $circle_threads=[];
            $user_id=-1;
            $my_liked_opinionids=[];
			$my_agreed_opinionids=[];
			$my_disagreed_opinionids=[];
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
            }
			$my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
			$Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
        
            // $latest_threads=Thread::select('id','name','views')
            // ->where('is_active',1)
            // ->withCount(['opinions','comment','followers'])
            // ->has('opinions', '>', 0)
            // ->orderBy('created_at','desc')
            // ->take(6)
            // ->get();


            // foreach($latest_threads as $thread){
            //     $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
            //     $latest_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
            //     foreach($latest_thread_opinion_ids as $id){
            //         $found=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
            //         if($found){
			// 			//$found->is_agreed=in_array($found->id,$my_agreed_opinionids); //$Agree_ids
			// 			//$found->is_disagreed=in_array($found->id,$my_disagreed_opinionids); //$DisAgree_ids
            //             // event(new OpinionViewCounterEvent($found,$request->ip()));
            //             if($found->links!=null){
            //                 $opinion_dummy=json_decode($found->links);
            //                 foreach ($opinion_dummy as $index=>$r_opinion) {
                       
            //                    if($r_opinion->status=="error"){
            //                         $found->links = "null";
            //                         //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                     }
            //                     elseif($r_opinion->image==null){
            //                     $r_opinion->image="https://weopined.com/img/noimg.png";
            //                     $r_opinion->imageWidth=640;
            //                     $r_opinion->imageHeight=300;
            //                     $found->links = "[".json_encode($r_opinion)."]";
            //                     //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                    }
            //                 }
                        
            //             }
            //             $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
			// 			//formatted_opinion($found,$my_liked_opinionids);//,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
            //             $thread->opinion=$formatted;
            //         }
            //     }
            // }

            $from=Carbon::now()->subDays(15);
            $to=Carbon::now();

            $trending_threads_ids=DB::table('thread_opinions')->where('is_active',1)->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))->groupBy('thread_id')->orderBy('count','desc')->take(6)->get()->pluck('thread_id')->toArray();
            $placeholders = implode(',',array_fill(0, count($trending_threads_ids), '?'));
            $trending_threads=Thread::select('id','name','views')->where('is_active',1)->whereIn('id',$trending_threads_ids)->withCount(['opinions','comment','followers'])->has('opinions', '>', 0)->orderByRaw("field(id,{$placeholders})", $trending_threads_ids)->take(6)->get();

            // foreach($trending_threads as $thread){
            //     $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
            //     $trending_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
            //     foreach($trending_thread_opinion_ids as $id){
            //         $found=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
            //         if($found){
            //             if($found->links!=null){
            //                 $opinion_dummy=json_decode($found->links);
            //                 foreach ($opinion_dummy as $index=>$r_opinion) {
                       
            //                    if($r_opinion->status=="error"){
            //                         $found->links = "null";
            //                         //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                     }
            //                     elseif($r_opinion->image==null){
            //                     $r_opinion->image="https://weopined.com/img/noimg.png";
            //                     $r_opinion->imageWidth=640;
            //                     $r_opinion->imageHeight=300;
            //                     $found->links = "[".json_encode($r_opinion)."]";
            //                     //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                    }
            //                 }
            //             }
            //             $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);
			// 			//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
            //             $thread->opinion=$formatted;
            //         }
            //     }
            // }

            //  if($user_id!=-1){
            //         $user=User::where(['id'=>$user_id,'is_active'=>1])->first();
            //         if($user){
            //             $following_ids=$user->active_followings->pluck('id')->toArray();

            //             $short_opinion_ids=ShortOpinion::select('id')
            //             ->where('is_active',1)
            //             ->whereIn('user_id',$following_ids)
            //             ->get()->pluck('id')->toArray();

            //             $thread_ids=ThreadOpinion::select('thread_id')
            //             ->where(['is_active'=>1])
            //             ->whereIn('short_opinion_id',$short_opinion_ids)
            //             ->distinct('thread_id')
            //             ->get()->pluck('thread_id')->toArray();

            //             $circle_threads=Thread::
            //             select('id','name','views')
            //             ->where('is_active',1)
            //             ->whereIn('id',$thread_ids)
            //             ->withCount(['opinions','comment','followers'])
            //             ->has('opinions', '>', 0)
            //             ->orderBy('opinions_count','desc')
            //             ->take(6)
            //             ->get();

            //             foreach($circle_threads as $thread){
            //                 $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
            //                 $circle_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
            //                 foreach($circle_thread_opinion_ids as $id){
            //                     $found=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
            //                     if($found){
            //                         if($found->links!=null){
            //                                 $opinion_dummy=json_decode($found->links);
            //                                 foreach ($opinion_dummy as $index=>$r_opinion) {
                                       
            //                                    if($r_opinion->status=="error"){
            //                                         $found->links = "null";
            //                                         //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                                     }
            //                                     elseif($r_opinion->image==null){
            //                                     $r_opinion->image="https://weopined.com/img/noimg.png";
            //                                     $r_opinion->imageWidth=640;
            //                                     $r_opinion->imageHeight=300;
            //                                     $found->links = "[".json_encode($r_opinion)."]";
            //                                     //array_push($rej_opinion_id,$rej_opinion->hash_tags);
            //                                    }
            //                                 }
                                      
            //                             }
            //                         $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);
            //                         $thread->opinion=$formatted;
            //                     }
            //                 }
            //             }
            //         }
            // }

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'latest_threads'=>$latest_threads,
                'trending_threads'=>$trending_threads,
                'circle_threads'=>$circle_threads
            ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error.');
            return response()->json($response, 500);
        }
    }
    public function index3(Request $request){
        try{
            $latest_threads=[];
            $trending_threads=[];
            $circle_threads=[];
            $user_id=-1;
            $my_liked_opinionids=[];
			$my_agreed_opinionids=[];
			$my_disagreed_opinionids=[];
            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
            }
			$my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
			$Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
			$Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
        
          

            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

             if($user_id!=-1){
                    $user=User::where(['id'=>$user_id,'is_active'=>1])->first();
                    if($user){
                        $following_ids=$user->active_followings->pluck('id')->toArray();

                        $short_opinion_ids=ShortOpinion::where(['is_active'=>1,'community_id'=>0])
                        ->whereIn('user_id',$following_ids)
                        ->take(6)
                        ->get();
    

                        // $thread_ids=DB::table('thread_opinions')->where(['is_active'=>1])
                        // ->whereIn('short_opinion_id',$short_opinion_ids)
                        // ->distinct('thread_id')
                        // ->select('thread_id')
                        // ->get()->pluck('thread_id')->toArray();

                        
                        
            


                        // foreach($circle_threads as $thread){
                        //     $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
                        //     $circle_thread_opinion_ids=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(1)->pluck('short_opinion_id')->toArray();
                        //     foreach($circle_thread_opinion_ids as $id){
                        //         $found=ShortOpinion::where(['id'=>$id,'is_active'=>1])->with('user:id,name,username,unique_id,image')->first();
                        //         if($found){
                        //             if($found->links!=null){
                        //                     $opinion_dummy=json_decode($found->links);
                        //                     foreach ($opinion_dummy as $index=>$r_opinion) {
                                       
                        //                        if($r_opinion->status=="error"){
                        //                             $found->links = "null";
                        //                             //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                        //                         }
                        //                         elseif($r_opinion->image==null){
                        //                         $r_opinion->image="https://weopined.com/img/noimg.png";
                        //                         $r_opinion->imageWidth=640;
                        //                         $r_opinion->imageHeight=300;
                        //                         $found->links = "[".json_encode($r_opinion)."]";
                        //                         //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                        //                        }
                        //                     }
                                      
                        //                 }
                        //             $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);
                        //             $thread->opinion=$formatted;
                        //         }
                                
                        //     }
                        // }
                        
                        $formatted_opinions=[];
                
                        foreach($short_opinion_ids as $found){
                            if($found){
                                if($found->links!=null){
                                        $opinion_dummy=json_decode($found->links);
                                        foreach ($opinion_dummy as $index=>$r_opinion) {
                                   
                                           if($r_opinion->status=="error"){
                                                $found->links = "null";
                                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                            }
                                            elseif($r_opinion->image==null){
                                            $r_opinion->image="https://weopined.com/img/noimg.png";
                                            $r_opinion->imageWidth=640;
                                            $r_opinion->imageHeight=300;
                                            $found->links = "[".json_encode($r_opinion)."]";
                                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                           }
                                        }
                                  
                                    }
                                $formatted=$this->formatted_opinion_AD($found,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);//formatted_opinion($found,$my_liked_opinionids);
                                array_push($formatted_opinions,$formatted);

                                
                            }
                        }
                       
                    }
            }

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'feed'=>$formatted_opinions
            ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error.');
            return response()->json($response, 500);
        }
    }


    public function new_feed(Request $request)
    {
        try {
            $from=Carbon::now()->subDays(15);
            $to=Carbon::now();

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
           			
            $rej_opinion_id = [];

            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                array_push($rej_opinion_id,$img_opinion->id);
            }
            $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
            
                $last60Days = Carbon::now()->subDays(60);

                $query = ShortOpinion::query();
                $query->with(['threads', 'user:id,name,username,unique_id,image']);
                $query->withCount(['likes', 'comments']);
                $query->whereNotIn('id', $rej_opinion_id);
                $query->where('last_updated_at', '>=', $last60Days);
                $query->where(['is_active' => 1, 'community_id' => 0]);
                $query->orderByDesc('likes_count');
                $query->orderByDesc('comments_count');
                $query->orderByDesc('score');
                $query->orderByDesc('last_updated_at');
                $opinions = $query->paginate(12);
            
    
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
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=> 'Internal Server Error New'.$e ); //+
			
            return response()->json($response, 500);
        }
    }

	
	public function feed(Request $request){
        try{

            $from=Carbon::now()->subDays(15);
            $to=Carbon::now();

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
           			
            $rej_opinion_id = [];

            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                array_push($rej_opinion_id,$img_opinion->id);
            }
            $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();

            $query = ShortOpinion::query();
            $query->with(['threads','user:id,name,username,unique_id,image']);
            $query->withCount(['likes','comments']);
            $query->whereHas('threads', function ($q) use ($followed_threadids,$rej_opinion_id,$blocked_users_ids) {
                $q->whereIn('threads.id',$followed_threadids)->whereNotIn('short_opinions.id',$rej_opinion_id)->whereNotIn('short_opinions.user_id',$blocked_users_ids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            })->orWhere(function($subquey) use ($following_userids,$rej_opinion_id,$blocked_users_ids){
                $subquey->whereIn('user_id',$following_userids)->whereNotIn('short_opinions.id',$rej_opinion_id)->whereNotIn('short_opinions.user_id',$blocked_users_ids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            })->orWhere(function($subqueys) use ($liked_ids,$rej_opinion_id,$blocked_users_ids){
                $subqueys->whereIn('id',$liked_ids)->whereNotIn('short_opinions.id',$rej_opinion_id)->whereNotIn('short_opinions.user_id',$blocked_users_ids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            })->orWhere(function($subques) use ($commented_ids,$rej_opinion_id,$blocked_users_ids){
                $subques->whereIn('id',$commented_ids)->whereNotIn('short_opinions.id',$rej_opinion_id)->whereNotIn('short_opinions.user_id',$blocked_users_ids)->where(['short_opinions.is_active'=>1,'short_opinions.community_id'=>0]);
            });
            $query->where(['is_active'=>1,'community_id'=>0]);
            $query->orderBy('last_updated_at','desc');
            $opinions= $query->paginate(12);
           
            if(count($opinions)<10 || count($following_userids)<2){
                $query = ShortOpinion::query();
                $query->with(['threads','user:id,name,username,unique_id,image']);
                $query->withCount(['likes','comments']);
                $query->whereNotIn('id',$rej_opinion_id);
                $query->where(['is_active'=>1,'community_id'=>0]);
                $query->orderBy('last_updated_at','desc');
                $opinions= $query->paginate(12);
            }


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
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=> 'Internal Server Error'  ); //+
			
            return response()->json($response, 500);
        }
    }


    // function for get threads orderby created_at with 3 opinions using pagination
    public function latest_threads_with_opinions(Request $request){
        try{

            $user_id=-1;
            $my_liked_opinionids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
            }
				
				$my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
				$my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
            // Rejected Opinions Start
            $rej_opinions = ShortOpinion::whereNotNull('links')->where(['community_id'=>0,'is_active'=>1])->whereNotIn('user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
            $rej_opinion_id = [];

            foreach ($rej_opinions as $rej_opinion) {
                //$rej_opinion = $rej_opinion->links;
                $rej_opinion_dummy=json_decode($rej_opinion->links);
                    foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                    //  $r_opinion->is_agreed=in_array($r_opinion->id,$my_agreed_opinionids); //$Agree_ids
					//$r_opinion->is_disagreed=in_array($r_opinion->id,$my_disagreed_opinionids); //$DisAgree_ids
                       if($r_opinion->status=="error"){
                            $string = str_replace('#', '', $rej_opinion->hash_tags);
                            $hash_tag = explode(',',$string);
                                foreach($hash_tag as $hash_tg) {
                                    array_push($rej_opinion_id, $hash_tg);
                                }
                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                        }
                        elseif($r_opinion->image=="null"){
                            $string = str_replace('#', '', $rej_opinion->hash_tags);
                            $hash_tag = explode(',',$string);
                                foreach($hash_tag as $hash_tg) {
                                    array_push($rej_opinion_id, $hash_tg);
                                }
                        //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                       }
                }
            }
           // var_dump($rej_opinion_id);
            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->whereNotIn('user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                $string = str_replace('#', '', $img_opinion->hash_tags);
                $hash_tag = explode(',',$string);
                            foreach($hash_tag as $hash_tg) {
                                array_push($rej_opinion_id, $hash_tg);
                            }
                //array_push($rej_opinion_id,$img_opinion->hash_tags);
            }

            //var_dump($rej_opinion_id);
            // Rejected opinion end

            $threads=Thread::
            select('id','name','views')
            ->where('is_active',1)
            ->whereNotIn('name',$rej_opinion_id)
            ->withCount(['opinions','comment','followers'])
            ->has('opinions', '>', 0)
            ->orderBy('created_at','desc')
            ->paginate(12);


            $formatted_threads=$threads->getCollection()->transform(function($thread, $key)  use ($my_liked_opinionids,$user_id ,$my_agreed_opinionids,$my_disagreed_opinionids,$blocked_users_ids){
                $opinions=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(3)->pluck('short_opinion_id')->toArray();
                $thread_opinions=[];
                foreach($opinions as $opinion){
                    $found=ShortOpinion::where(['id'=>$opinion,'is_active'=>1,'community_id'=>0])->whereNotIn('user_id',$blocked_users_ids)->with('user:id,name,username,unique_id,image')->first();
                //     foreach ($found as $found_op) {
                //         //$rej_opinion = $rej_opinion->links;

                // }
			//	$my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
				//$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
				
                    if($found){
						$Agree_ids = ShortOpinionLike::  where(['id'=>$found->id,'Agree_Disagree'=>1])->get();
						$Disagree_ids=ShortOpinionLike::  where(['id'=>$found->id,'Agree_Disagree'=>0])->get();
						$found->is_agreed=in_array($found->id,$my_agreed_opinionids); //$Agree_ids
						$found->is_disagreed=in_array($found->id,$my_disagreed_opinionids); //$DisAgree_ids
					//	$found->AgreeCnt=   (isset($Agree_ids)?$Agree_ids->count():0) ;
					//	$found->DisagreeCnt=   (isset($Disagree_ids)?$Disagree_ids->count():0) ;
				
                        if($found->links!=null){
                            $opinion_dummy=json_decode($found->links);
                            foreach ($opinion_dummy as $index=>$r_opinion) {
               
                               if($r_opinion->image==null){
                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                $r_opinion->imageWidth=640;
                                $r_opinion->imageHeight=300;
                                $found->links = "[".json_encode($r_opinion)."]";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                               }
                            }
                        
                        }
                        $formatted=$this->formatted_opinion_new($found,$my_liked_opinionids);//,$Agree_ids ,$Disagree_ids ,$my_agreed_opinionids,$my_disagreed_opinionids);
                        array_push($thread_opinions,$formatted);
                    }
                }
				     
               
                $custom_thread= [
                    'id' => $thread->id,
                    'name' => $thread->name,
                    'views'=>$thread->views,
                    'opinions_count'=>$thread->opinions_count+$thread->comment_count,
                    'followers_count'=>$thread->followers_count,
                    'opinions' => $thread_opinions,
                ];
                return $custom_thread;
                
            });

            $meta=$this->get_meta($threads);

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'threads_with_opinions'=>$formatted_threads,
                'meta'=>$meta
            ]);

           }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage().' '.$e->getLine());
            return response()->json($response, 500);
        }
    }

    // function for get threads orderby opinions_count with 3 opinions using pagination
    public function trending_threads_with_opinions(Request $request){
        try{
			$UserId=-1;
            $user_id=-1;
            $my_liked_opinionids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
				$UserId= $user_id;
            }



            /* $threads=Thread::select('id','name')
            ->where('is_active',1)
            ->withCount('opinions')
            ->has('opinions', '>', 0)
            ->orderBy('opinions_count','desc')
            ->paginate(12); */

            //->whereBetween('created_at',[$from,$to])

            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();
            $trending_threads_ids=DB::table('thread_opinions')->where('is_active',1)->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))->groupBy('thread_id')->orderBy('count','desc')->get()->pluck('thread_id')->toArray();
            $placeholders = implode(',',array_fill(0, count($trending_threads_ids), '?'));
            $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();

           
            
            // Rejected Opinions Start
            $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->whereNotIn('user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
            $rej_opinion_id = [];

            foreach ($rej_opinions as $rej_opinion) {
                //$rej_opinion = $rej_opinion->links;
                $rej_opinion_dummy=json_decode($rej_opinion->links);
                    foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                       
                       if($r_opinion->status=="error"){
                            $string = str_replace('#', '', $rej_opinion->hash_tags);
                            $hash_tag = explode(',',$string);
                                foreach($hash_tag as $hash_tg) {
                                    array_push($rej_opinion_id, $hash_tg);
                                }
                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                        }
                        elseif($r_opinion->image=="null"){
                        $string = str_replace('#', '', $rej_opinion->hash_tags);
                        $hash_tag = explode(',',$string);
                            foreach($hash_tag as $hash_tg) {
                                array_push($rej_opinion_id, $hash_tg);
                            }
                        //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                       }

                        
                    }
            }
           

            $img_opinions =  ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                $string = str_replace('#', '', $img_opinion->hash_tags);
                $hash_tag = explode(',',$string);
                            foreach($hash_tag as $hash_tg) {
                                array_push($rej_opinion_id, $hash_tg);
                            }
                //array_push($rej_opinion_id,$img_opinion->hash_tags);
            }
            
			$my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>$UserId])->pluck('short_opinion_id')->toArray();
			$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>$UserId])->pluck('short_opinion_id')->toArray();
			
            //var_dump($rej_opinion_id);
            // Rejected opinion end

            $threads=Thread::
            select('id','name','views')
            ->where('is_active',1)
            ->whereIn('id',$trending_threads_ids)
            ->whereNotIn('name',$rej_opinion_id)
            ->orderByRaw("field(id,{$placeholders})", $trending_threads_ids)
            ->withCount(['opinions','comment','followers'])
            ->has('opinions', '>', 0)
            ->orderBy('created_at','desc')
            ->paginate(12);
           




            $formatted_threads=$threads->getCollection()->transform(function($thread, $key)  use ($my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids,$blocked_users_ids){
                $opinions=ThreadOpinion::select('id','short_opinion_id','created_at')
                ->where(['thread_id'=>$thread->id,'is_active'=>1])
                ->orderBy('created_at','desc')
                ->take(3)
                ->pluck('short_opinion_id')
                ->toArray();
                $thread_opinions=[];
				


				
                foreach($opinions as $opinion){
                    $found=ShortOpinion::where(['id'=>$opinion,'is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->with('user:id,name,username,unique_id,image')->first();
                    if($found){
						
						$found->is_agreed=in_array($found->id,$my_agreed_ids); //$Agree_ids
						$found->is_disagreed=in_array($found->id,$my_disagreed_ids); //$DisAgree_ids
				
                        if($found->links!=null){
                            $opinion_dummy=json_decode($found->links);
                            foreach ($opinion_dummy as $index=>$r_opinion) {
                       
                               if($r_opinion->image==null){
                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                $r_opinion->imageWidth=640;
                                $r_opinion->imageHeight=300;
                                $found->links = "[".json_encode($r_opinion)."]";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                               }
                            }
                        
                        }
                        $formatted=$this->formatted_opinion_new($found,$my_liked_opinionids);
                        array_push($thread_opinions,$formatted);
                    }
                }
             
                $custom_thread= [
                    'id' => $thread->id,
                    'name' => $thread->name,
                    'views'=>$thread->views,
                    'opinions_count'=>$thread->opinions_count+$thread->comment_count,
                    'followers_count'=>$thread->followers_count,
                    'opinions' => $thread_opinions
                ];
                return $custom_thread;
            });

            $meta=$this->get_meta($threads);

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'threads_with_opinions'=>$formatted_threads,
                'meta'=>$meta
            ]);

          }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage().' '.$e->getLine());//$e->getMessage(). $e->getLine()
            return response()->json($response, 500);
        }
    }

    // function for get trending threads from logged users following orderby opinion_count with 3 opinions using pagination
    public function circle_threads_with_opinions(Request $request){
        try{
            $user_id=Auth::user()->user_id;
            $my_liked_opinionids=$this->my_liked_opinionids($user_id);
            $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();

            $following_ids=Auth::user()->user->active_followings->pluck('id')->toArray();
            $short_opinion_ids=ShortOpinion::select('id')
            ->where('is_active',1)
            ->whereIn('user_id',$following_ids)
            ->get()->pluck('id')->toArray();

            $thread_ids=ThreadOpinion::select('thread_id')
            ->where(['is_active'=>1])
            ->whereIn('short_opinion_id',$short_opinion_ids)
            ->distinct('thread_id')
            ->get()->pluck('thread_id')->toArray();

             // Rejected Opinions Start
            $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->orderBy('created_at','desc')->get();
            $rej_opinion_id = [];

            foreach ($rej_opinions as $rej_opinion) {
                //$rej_opinion = $rej_opinion->links;
                $rej_opinion_dummy=json_decode($rej_opinion->links);
                    foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                       
                       if($r_opinion->status=="error"){
                            $string = str_replace('#', '', $rej_opinion->hash_tags);
                            $hash_tag = explode(',',$string);
                                foreach($hash_tag as $hash_tg) {
                                    array_push($rej_opinion_id, $hash_tg);
                                }
                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                        }
                        elseif($r_opinion->image=="null"){
                        $string = str_replace('#', '', $rej_opinion->hash_tags);
                        $hash_tag = explode(',',$string);
                            foreach($hash_tag as $hash_tg) {
                                array_push($rej_opinion_id, $hash_tg);
                            }
                        //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                       }

                        
                    }
            }
			 
            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
            foreach ($img_opinions as $img_opinion) {
                $string = str_replace('#', '', $img_opinion->hash_tags);
                $hash_tag = explode(',',$string);
                            foreach($hash_tag as $hash_tg) {
                                array_push($rej_opinion_id, $hash_tg);
                            }
                //array_push($rej_opinion_id,$img_opinion->hash_tags);
            }
            $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }

            //var_dump($rej_opinion_id);
            // Rejected opinion end


            $threads=Thread::
            select('id','name','views')
            ->where('is_active',1)
            ->whereIn('id',$thread_ids)
            ->whereNotIn('name',$rej_opinion_id)
            ->withCount(['opinions','comment','followers'])
            ->has('opinions', '>', 0)
            ->orderBy('opinions_count','desc')
            ->paginate(12);

            // Rejected Opinions Start
            $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->get();
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
            $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }
            // Rejected opinion end

            $formatted_threads=$threads->getCollection()->transform(function($thread, $key)  use ($my_liked_opinionids){
                $opinions=ThreadOpinion::select('short_opinion_id')->where(['thread_id'=>$thread->id,'is_active'=>1])->orderBy('created_at','desc')->take(3)->pluck('short_opinion_id')->toArray();
                $thread_opinions=[];
				$my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
				$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
							
                foreach($opinions as $opinion){
                    $found=ShortOpinion::where(['id'=>$opinion,'is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->with('user:id,name,username,unique_id,image')->first();
                    if($found){
							
							$found->is_agreed=in_array($found->id,$my_agreed_ids); //$Agree_ids
							$found->is_disagreed=in_array($found->id,$my_disagreed_ids); //$DisAgree_ids
			
				/*$Agree_ids = ShortOpinionLike::  where(['id'=>$r_opinion->id,'Agree_Disagree'=>1])->get();
				$Disagree_ids = ShortOpinionLike::  where(['id'=>$r_opinion->id,'Agree_Disagree'=>0])->get();
                
				$r_opinion->AgreeCnt=   ($Agree_ids->count()) ;
				$r_opinion->DisagreeCnt=   ($Disagree_ids->count()) ;
				*/
                        if($found->links!=null){
                            $opinion_dummy=json_decode($found->links);
                            foreach ($opinion_dummy as $index=>$r_opinion) {
                       
                               if($r_opinion->image==null){
                                $r_opinion->image="https://weopined.com/img/noimg.png";
                                $r_opinion->imageWidth=640;
                                $r_opinion->imageHeight=300;
                                $found->links = "[".json_encode($r_opinion)."]";
                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                               }
                            }
                        
                        }
                        $formatted=$this->formatted_opinion_new($found,$my_liked_opinionids);
                        array_push($thread_opinions,$formatted);
                    }
                }
                $custom_thread= [
                    'id' => $thread->id,
                    'name' => $thread->name,
                    'views'=>$thread->views,
                    'opinions_count'=>$thread->opinions_count+$thread->comment_count,
                    'followers_count'=>$thread->followers_count,
                    'opinions' => $thread_opinions
                ];
                return $custom_thread;
            });

            $meta=$this->get_meta($threads);

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'threads_with_opinions'=>$formatted_threads,
                'meta'=>$meta
            ]);

        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }



    // function for get opinions orderby created_at by thread_id with pagination
    public function get_latest_opinions_by_thread_name(Request $request,$thread_name){
        try{
            if(!isset($thread_name)){
                $response=array('status'=>'error','result'=>0,'errors'=>'thread_name is required');
                return response()->json($response, 200);
            }else{

                $user_id=-1;
                $my_liked_opinionids=[];
                $followed_threadids=[];

                if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                }
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
                $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                   

                $thread=Thread::where('name',$thread_name)->withCount('comment')->where('is_active',1)->first();
                if($thread){
                        // Rejected Opinions Start
                            $rej_opinions = ShortOpinion::whereNotNull('links')->whereIn('user_id',$blocked_users_ids)->where(['is_active'=>1,'community_id'=>0])->orderBy('created_at','desc')->get();
                            $rej_opinion_id = [];

                            foreach ($rej_opinions as $rej_opinion) {
                                //$rej_opinion = $rej_opinion->links;
                                $rej_opinion_dummy=json_decode($rej_opinion->links);
                                    foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                                       
                                       if($r_opinion->status=="error"){
                                        array_push($rej_opinion_id,$rej_opinion->id);
                                        }
                                        elseif($r_opinion->image=="null"){
                                        array_push($rej_opinion_id,$rej_opinion->id);
                                       }

                                        
                                    }
                            }
                            $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->get();
                            foreach ($img_opinions as $img_opinion) {
                                array_push($rej_opinion_id,$img_opinion->id);
                            }

                            $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }
                            // Rejected opinion end
                        $followed_threadids=ThreadFollower::where(['user_id'=> $user_id,'is_active'=>1])->pluck('thread_id')->toArray();
                        $this->remove_null($thread);
                        $thread->is_followed=in_array($thread->id,$followed_threadids)?1:0;
                    

                        // $opinions = ShortOpinion::where(['thread_opinions.thread_id' => $thread->id, 'thread_opinions.is_active' => 1, 'short_opinions.community_id' => 0])
                        // ->whereNotIn('thread_opinions.short_opinion_id', $rej_opinion_id)
                        // ->leftJoin('thread_opinions', 'thread_opinions.short_opinion_id', '=', 'short_opinions.id')
                        // ->select('short_opinions.*')
                        // ->groupBy('thread.id')
                        // ->orderBy('thread_opinions.created_at', 'desc')
                        // ->paginate(12);

                        $opinions = ShortOpinion::where(['thread_opinions.thread_id' => $thread->id, 'thread_opinions.is_active' => 1, 'short_opinions.community_id' => 0])
                        ->whereNotIn('thread_opinions.short_opinion_id', $rej_opinion_id)
                        ->leftJoin('thread_opinions', 'thread_opinions.short_opinion_id', '=', 'short_opinions.id')
                        ->select('short_opinions.*')
                        ->groupBy('short_opinions.id')
                        ->orderBy('thread_opinions.created_at', 'desc')
                        ->paginate(12);

                        
                       
                  


						$my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
						$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
							

                        $rejected_opinions=$opinions->reject(function($opinion){
                            return $opinion->latest_opinion!=null;
                        })->values()->all();                  
                    $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids, $my_agreed_opinionids,$my_disagreed_opinionids){
                        unset($opinion->threads);
                        return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
                    });
                    $meta=$this->get_meta($opinions);
                   
                    $response=array('status'=>'success','result'=>1,'feed'=>$formatted, 'meta'=>$meta);
                    return response()->json($response, 200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'thread not found');
                    return response()->json($response, 200);
                }
            }
           }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage());
            return response()->json($response, 500);
        }
    }

    // function for get opinions orderby likes_count by thread_id with pagination
    public function get_trending_opinions_by_thread_name(Request $request,$thread_name){
        try{
            if(!isset($thread_name)){
                $response=array('status'=>'error','result'=>0,'errors'=>'thread_name is required');
                return response()->json($response, 200);
            }else{
                $user_id=-1;
                $my_liked_opinionids=[];
                $followed_threadids=[];
                $from=Carbon::now()->subDays(30);
                $to=Carbon::now();
                if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                }
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
				$my_agreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>1, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
				$my_disagreed_ids=ShortOpinionLike::  where(['Agree_Disagree'=>0, 'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
                $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>$user_id])->pluck('short_opinion_id')->toArray();
                   
                
                $thread=Thread::where('name',$thread_name)->withCount('comment')->where('is_active',1)->first();
                if($thread){

                    // Rejected Opinions Start
                        $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->orderBy('created_at','desc')->get();
                        $rej_opinion_id = [];

                        foreach ($rej_opinions as $rej_opinion) {
                            //$rej_opinion = $rej_opinion->links;
                            $rej_opinion_dummy=json_decode($rej_opinion->links);
                                foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                                   
                                    if($r_opinion->status=="error"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                    }
                                    elseif($r_opinion->image=="null"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                   }

                                    
                                }
                        }
                        $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->orderBy('created_at','desc')->get();
                        foreach ($img_opinions as $img_opinion) {
                            array_push($rej_opinion_id,$img_opinion->id);
                        }

                        $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }
                    // Rejected opinion end


                    $followed_threadids=ThreadFollower::where(['user_id'=> $user_id,'is_active'=>1])->pluck('thread_id')->toArray();
                    $this->remove_null($thread);
                    $thread->is_followed=in_array($thread->id,$followed_threadids)?1:0;



                $opinions = ShortOpinion::where(['thread_opinions.thread_id' => $thread->id, 'thread_opinions.is_active' => 1, 'short_opinions.community_id' => 0])
                ->whereNotIn('thread_opinions.short_opinion_id', $rej_opinion_id)
                ->leftJoin('thread_opinions', 'thread_opinions.short_opinion_id', '=', 'short_opinions.id')
                ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'short_opinions.id')
                ->leftJoin('shares', 'shares.short_opinion_id', '=', 'short_opinions.id')
                ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'short_opinions.id')
                ->select('short_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                ->groupBy('short_opinions.id')
                ->orderBy('count', 'desc')
                ->paginate(12);

        

               // $opinions=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->whereNotIn('short_opinion_id',$rej_opinion_id)->with('mostliked_opinion')->paginate(20);

                $rejected_opinions=$opinions->reject(function($opinion){
                    return $opinion->mostliked_opinion!=null;
                })->values()->all();

             
                $formatted=$opinions->getCollection()->transform(function($opinion,$key) use($my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids, $my_agreed_opinionids,$my_disagreed_opinionids){
                    unset($opinion->threads);
                    return $this->formatted_opinion_AD($opinion,$my_liked_opinionids,$my_agreed_ids,$my_disagreed_ids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
                });
               
                $meta=$this->get_meta($opinions);
               
                $response=array('status'=>'success','result'=>1,'feed'=>$formatted, 'meta'=>$meta);
                return response()->json($response, 200);
               
           
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'thread not found');
                    return response()->json($response, 200);
                }
            }
          }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error'.$e->getMessage());
            return response()->json($response, 500);
        }
    }

    public function get_trending_opinions(Request $request){
        try{
           
                $user_id=-1;
                $my_liked_opinionids=[];
                $followed_threadids=[];
                $from=Carbon::now()->subDays(30);
                $to=Carbon::now();
                if($request->header('Authorization')){
                    $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                    $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                }
                $following_for_opinion_userids=Auth::user()->user->active_followings->pluck('id')->toArray();
                  $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
                 $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
                $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
                $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();

                    // Rejected Opinions Start
                        $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->orderBy('created_at','desc')->get();
                        $rej_opinion_id = [];

                        foreach ($rej_opinions as $rej_opinion) {
                            //$rej_opinion = $rej_opinion->links;
                            $rej_opinion_dummy=json_decode($rej_opinion->links);
                                foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                                   
                                    if($r_opinion->status=="error"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                    }
                                    elseif($r_opinion->image=="null"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                   }

                                    
                                }
                        }
                        $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->orderBy('created_at','desc')->get();
                        foreach ($img_opinions as $img_opinion) {
                            array_push($rej_opinion_id,$img_opinion->id);
                        }
                        $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }
                        // Rejected opinion end
                // $followed_threadids=ThreadFollower::where(['user_id'=> $user_id,'is_active'=>1])->pluck('thread_id')->toArray();
                // $this->remove_null($thread);
                // $thread->is_followed=in_array($thread->id,$followed_threadids)?1:0;

                // event(new ThreadViewCounterEvent($thread));

                $opinions = ThreadOpinion::where(['thread_opinions.is_active'=>1,'short_opinions.community_id'=>0])
                ->whereNotIn('short_opinions.user_id',$blocked_users_ids)
                ->with('mostliked_opinion')
                ->leftJoin('short_opinions', 'short_opinions.id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('shares', 'shares.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->select('thread_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                ->groupBy('thread_opinions.id')
                ->orderBy('count','desc')
                ->take(40)
                ->get();
                
                
               
              $trending_opinions=[];
              $opinion_ids=[];
              foreach($opinions as $trending_thread_opinion)
                {  
                    
                        if(in_array($trending_thread_opinion->short_opinion_id,$opinion_ids)){
                           
                        }else{
                            array_push($opinion_ids,$trending_thread_opinion->short_opinion_id);
                            $trending_thread_opinion=ShortOpinion::where(['id'=>$trending_thread_opinion->short_opinion_id,'is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->with('user')->first();
                    
                          
                            if($trending_thread_opinion){
                                $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
                                try{
                                    if($trending_thread_opinion->links!=null){
                                        $opinion_dummy=json_decode($trending_thread_opinion->links);
                                        foreach ($opinion_dummy as $index=>$r_opinion) {
                                   
                                           if($r_opinion->status=="error"){
                                                $trending_thread_opinion->links = "null";
                                                //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                            }
                                            elseif($r_opinion->image==null){
                                            $r_opinion->image="https://weopined.com/img/noimg.png";
                                            $r_opinion->imageWidth=640;
                                            $r_opinion->imageHeight=300;
                                            $trending_thread_opinion->links = "[".json_encode($r_opinion)."]";
                                            //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                           }
                                        }
                                  
                                    }
                                  $formatted= $this->formatted_opinion_AD($trending_thread_opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
                               
                                array_push($trending_opinions,$formatted);
                            }catch(\Exception $e){
                                    echo "Message ".$e->getMessage();
                            }
                              }
                            
                        }
                      
                    }

             
                $response=array(
                   'status'=>'success',
                   'result'=>1,
                   'threads_with_opinions'=>$trending_opinions);

                   

                return response()->json($response, 200);
            
          }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    // function for get opinions from logged in users following orderby date by thread_id with pagination
    public function get_circle_opinions_by_thread_name(Request $request,$thread_name){
        try{
            if(!isset($thread_name)){
                $response=array('status'=>'error','result'=>0,'errors'=>'thread_name is required');
                return response()->json($response, 200);
            }else{
                $thread=Thread::where('name',$thread_name)->withCount('comment')->where('is_active',1)->first();
                $blocked_users_ids = Auth::user()->user->blocked_users->pluck('blocked_id')->toArray();
                if($thread){
                    // Rejected Opinions Start
                        $rej_opinions = ShortOpinion::whereNotNull('links')->where(['is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
                        $rej_opinion_id = [];

                        foreach ($rej_opinions as $rej_opinion) {
                            //$rej_opinion = $rej_opinion->links;
                            $rej_opinion_dummy=json_decode($rej_opinion->links);
                                foreach ($rej_opinion_dummy as $index=>$r_opinion) {
                                   
                                    if($r_opinion->status=="error"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                    }
                                    elseif($r_opinion->image=="null"){
                                    array_push($rej_opinion_id,$rej_opinion->id);
                                   }

                                    
                                }
                        }
                        $img_opinions = ShortOpinion::where(['is_active'=>1,'cover_type'=>'IMAGE','cover'=>"",'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->orderBy('created_at','desc')->get();
                        foreach ($img_opinions as $img_opinion) {
                            array_push($rej_opinion_id,$img_opinion->id);
                        }

                        $blocked_opinions = ShortOpinion::whereIn('short_opinions.user_id',$blocked_users_ids)->get();
                            foreach ($blocked_opinions as $blocked_opinion) {
                                array_push($rej_opinion_id,$blocked_opinion->id);
                            }
                        // Rejected opinion end
                $user_id=Auth::user()->user_id;
                $my_liked_opinionids=$this->my_liked_opinionids($user_id);
                $followed_threadids=ThreadFollower::where(['user_id'=> $user_id,'is_active'=>1])->pluck('thread_id')->toArray();
                $thread->is_followed=in_array($thread->id,$followed_threadids)?1:0;

                $following_ids=Auth::user()->user->active_followings->pluck('id')->toArray();
                $short_opinion_ids=ShortOpinion::select('id')
                ->where('is_active',1)
                ->whereIn('user_id',$following_ids)
                ->get()->pluck('id')->toArray();

                $this->remove_null($thread);
                $opinions=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])
                ->whereNotIn('short_opinion_id',$rej_opinion_id)
                ->with('latest_opinion')->whereIn('short_opinion_id',$short_opinion_ids)
                ->orderBy('created_at','desc')->paginate(20);

                    $rejected_opinions=$opinions->reject(function($opinion){
                        return $opinion->latest_opinion!=null;
                    })->values()->all();
                    $opinions_collection=$opinions->filter(function ($opinion,$key)  use ($my_liked_opinionids){
                        if($opinion->latest_opinion!=null){
                            $formatted_opinion=$this->formatted_opinion_new($opinion->latest_opinion,$my_liked_opinionids);
                            $comments=ShortOpinionComment::where(['parent_id'=>0,'is_active'=>1,'short_opinion_id'=>$opinion->short_opinion_id])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(2)->get();
                            $this->remove_null($comments);
                            $formatted_opinion->latest_comments=$comments;
                            if($formatted_opinion->links!=null){
                                        $opinion_dummy = $formatted_opinion->links;
                                        foreach ($opinion_dummy as $r_opinion) {
                                            if($r_opinion['image']==null){
                                               $r_opinion['image']="https://weopined.com/img/noimg.png";
                                                $r_opinion['imageWidth']=640;
                                                $r_opinion['imageHeight']=300; 
                                                $formatted_opinion->links = array($r_opinion);
                                            }
                                        }
                                   }
                            return $formatted_opinion;
                        }
                    });
                    $formatted_opinions=$opinions_collection->values()->pluck('latest_opinion')->all();
                    if(($opinions->total()-count($rejected_opinions))>0){
                        $cmnt_count = $thread->comment_count;
                    }
                    else{
                        $cmnt_count = 0;
                    }
                    $paginator=new LengthAwarePaginator($formatted_opinions,
                    $opinions->total()-count($rejected_opinions)+$cmnt_count,
                    $opinions->perPage(),
                    $opinions->currentPage(), [
                        'path' => \Request::url(),
                        'query' => [
                            'page' => $opinions->currentPage()
                        ]
                    ]);
                    $meta=$this->get_meta($paginator);

                    return response()->json([
                        'status'=>'success',
                        'result'=>1,
                        'thread'=>$thread,
                        'thread_opinions'=>$formatted_opinions,
                        'meta'=>$meta
                    ]);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'thread not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_threads_by_category(Request $request, $category_id){

        try{
            if(!isset($category_id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'thread_name is required');
                return response()->json($response, 200);
            }else{
                if($category_id!=0){
                    $thread_ids=CategoryThread::where(['is_active'=>1,'category_id'=>$category_id])->orderBy('created_at','desc')->get()->pluck('thread_id')->toArray();
                    $category_threads=Thread::whereIn('id',$thread_ids)->where('is_active',1)->withCount('opinions')->has('opinions','>',0)->orderBy('created_at','desc')->take(10)->get();
                    return response()->json([
                        'status'=>'success',
                        'result'=>1,
                        'category_threads'=>$category_threads,
                    ]);
                }else{

            

                    //Opinion Count in Last 15 days should new, not when threads  
                    $startDate = Carbon::now()->subDays(30);

                    $trending_threads = Thread::select('threads.*')
                        ->join('thread_opinions', 'threads.id', '=', 'thread_opinions.thread_id')
                        ->join('short_opinions', 'short_opinions.id', '=', 'thread_opinions.short_opinion_id')
                        ->join('users', 'users.id', '=', 'short_opinions.user_id')
                        ->where('short_opinions.is_active', 1)
                        ->where('thread_opinions.is_active', 1)
                        ->where('short_opinions.created_at', '>=', $startDate)
                        ->groupBy('threads.id')
                        ->orderByRaw('COUNT(thread_opinions.id) DESC')
                        ->take(10)
                        ->get();
                    



                    return response()->json([
                        'status'=>'success',
                        'result'=>1,
                        'category_threads'=>$trending_threads,
                    ]);
                }
               
                
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function get_opinions_by_category(Request $request, $category_id){

        try{
            if(!isset($category_id)){
                $response=array('status'=>'error','result'=>0,'errors'=>'thread_name is required');
                return response()->json($response, 200);
            }else{
                $thread_ids=CategoryThread::where(['is_active'=>1,'category_id'=>$category_id])->orderBy('created_at','desc')->get()->pluck('thread_id')->toArray();
                $category_threads=Thread::whereIn('id',$thread_ids)->where('is_active',1)->withCount('opinions')->has('opinions','>',0)->orderBy('created_at','desc')->take(10)->get();
                $Agreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>1])->pluck('short_opinion_id')->toArray();
               $Disagreeids = ShortOpinionLike::where([ 'Agree_Disagree'=>0])->pluck('short_opinion_id')->toArray();
              $my_agreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>1,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
              $my_disagreed_opinionids= ShortOpinionLike::where([ 'Agree_Disagree'=>0,'user_id'=>Auth::user()->user_id])->pluck('short_opinion_id')->toArray();
             
            
                $contest_board = ThreadOpinion::whereIn('thread_opinions.thread_id',$thread_ids)
                ->with('mostliked_opinion')
                ->leftJoin('short_opinions', 'short_opinions.id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('shares', 'shares.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'thread_opinions.short_opinion_id')
                ->select('thread_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
                ->groupBy('thread_opinions.id')
                ->orderBy('count','desc')
                ->paginate(10);
  
                $trending_opinions=[];
                foreach($contest_board as $trending_thread_opinion)
                  {  
                    $trending_thread_opinion=ShortOpinion::where(['id'=>$trending_thread_opinion->short_opinion_id,'is_active'=>1,'community_id'=>0])->whereNotIn('short_opinions.user_id',$blocked_users_ids)->with('user')->first();
                    if($trending_thread_opinion){
                        $my_liked_opinionids=$this->my_liked_opinionids(Auth::user()->user_id);
                        try{
                            if($trending_thread_opinion->links!=null){
                                $opinion_dummy=json_decode($trending_thread_opinion->links);
                                foreach ($opinion_dummy as $index=>$r_opinion) {
                           
                                   if($r_opinion->status=="error"){
                                        $trending_thread_opinion->links = "null";
                                        //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                    }
                                    elseif($r_opinion->image==null){
                                    $r_opinion->image="https://weopined.com/img/noimg.png";
                                    $r_opinion->imageWidth=640;
                                    $r_opinion->imageHeight=300;
                                    $trending_thread_opinion->links = "[".json_encode($r_opinion)."]";
                                    //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                                   }
                                }
                          
                            }
                          $formatted= $this->formatted_opinion_AD($trending_thread_opinion,$my_liked_opinionids,$Agreeids,$Disagreeids,$my_agreed_opinionids,$my_disagreed_opinionids);//,$my_agreed_opinionids,$my_disagreed_opinionids);
                       
                        array_push($trending_opinions,$formatted);
                    }catch(\Exception $e){
                            echo "Message ".$e->getMessage();
                    }
            
                       
                    }
                  }

                

                return response()->json([
                    'status'=>'success',
                    'result'=>1,
                    'opinions'=>$trending_opinions,
                ]);
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    // function for adding and removing likes by userid , opinion_id
    //Updating agree points for liking a opinion
    public function like_opinion(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $opinion_id=$request->input('opinion_id');
                $opinion=ShortOpinion::where(['id'=>$opinion_id,'is_active'=>1,'community_id'=>0])->with('user')->first();
                if($opinion){
                    $opinion_liked=ShortOpinionLike::where(['user_id'=>Auth::user()->user_id,'short_opinion_id'=>$opinion_id])->first();
                    if($opinion_liked){
                        Auth::user()->user->likes()->detach($opinion_id);
                        DB::table('notifications')
                        ->where('data','like','%"event":"OPINION_LIKED"%')
                        ->where('data','like','%"opinion_id":'.$opinion_id.'%')
                        ->where('data','like','%"sender_id":'.Auth::user()->user_id.'%')
                        ->delete();
                        $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                        $response=array('status'=>'success','result'=>0,'message'=>'Opinion like removed','total'=>$count);
                        $point=Point::where(['user_id'=>$opinion->user_id])->first();
                        Point::where(['user_id'=>$opinion->user_id])->update([
                            'agree_points'=>$point->agree_points-10,
                        ]);
                        if(Carbon::now()->diffInDays($point->updated_at)==0) {
                            Point::where(['user_id'=>$opinion->user_id])->update([
                                'daily_points'=>$point->daily_points-10,
                            ]);
                        }
                        return response()->json($response,200);
                    }else{
                        Auth::user()->user->likes()->attach($opinion_id);
                        $this->notify_followers_AD($opinion,'ShortOpinionLiked',$opinion_liked);
                        ShortOpinion::where(['id'=>$opinion->id])->update(['last_updated_at'=>Carbon::now()]);
                        $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>'Opinion liked','total'=>$count);
                        $point=Point::where(['user_id'=>$opinion->user_id])->first();
                        //Updating agree points for liking a opinion
                        if($point==null) {
                            Point::create([
                                'user_id'=>$opinion->user_id,
                                'agree_points'=>10,
                                'comment_points'=>0,
                                'follower_points'=>0,   
                                'reward_points'=>0,
                                'post_points'=>0,
                                'share_points'=>0,
                                'daily_points'=>10
                            ]);
                        } else {
                            Point::where(['user_id'=>$opinion->user_id])->update([
                                'agree_points'=>$point->agree_points+10,
                            ]);
                            if(Carbon::now()->diffInDays($point->updated_at)==0) {
                                Point::where(['user_id'=>$opinion->user_id])->update([
                                    'daily_points'=>$point->daily_points+10
                                ]);
                            } else {
                                Point::where(['user_id'=>$opinion->user_id])->update([
                                    'daily_points'=>10
                                ]);
                            }
                        }
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Opinion not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

//For Agree_Disagree
 public function Agree_disagree_opinion(Request $request)
    {
		$result_AD='';
        try{
				
            $validator = Validator::make($request->all(), [
                'opinion_id'=>'required'
            ]);
	

            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $opinion_id=$request->input('opinion_id');
				 $Agree_Disagree=$request->input('Agree_Disagree');
				 $result_AD=$Agree_Disagree;
								 
				 if($Agree_Disagree == "0" || $Agree_Disagree=="1"){
					 
				 }
				 else{
					   $response=array('status'=>'error','result'=>0,'message'=>'Agree Disagree Values are incorrect' );
                        return response()->json($response,200);
				 }
                $opinion=ShortOpinion::where(['id'=>$opinion_id])->with('user')->first();
				$Liked=ShortOpinionLike::where(['user_id'=>Auth::user()->user_id,'short_opinion_id'=>$opinion_id])->first();//,'Agree_Disagree'=>$Agree_Disagree])
		 
						if($Liked){
							if($Agree_Disagree == $Liked ->Agree_Disagree) //If prev agreedisagree value and currrent val is same then delete the record
							{			
								DB::table('short_opinion_likes')
								->where('id','=',$Liked-> id)
								 ->delete();
								$result_AD='';
							// //detach record
							 ($Agree_Disagree =='0') ? $result='disagreedel' :$result=' agreedel';		       

                        //Todo: Delete Rewards Earned
                                                
							}		
							else //update record
							{
								$result='';
								//$result= 'id=>'.$Liked->id . 'Agree_Disagree=>'. $Agree_Disagree;
								ShortOpinionLike::where(['id'=>$Liked->id])->update(['Agree_Disagree'=> $Agree_Disagree,'liked_at'=>Carbon::now()]);
								$this->notify_followers_AD($opinion,'ShortOpinionLiked',$Liked);
							}
						}
						else
						{
							//create new record
							$ShortOpinionLike = new ShortOpinionLike();
							$ShortOpinionLike ->user_id= Auth::user()->user_id;
							$ShortOpinionLike ->short_opinion_id= $opinion_id;
							$ShortOpinionLike ->Agree_Disagree= $Agree_Disagree;
							$ShortOpinionLike->save();
							($Agree_Disagree =='0')?  	$result='disagree' :$result='agree';

                      
                            $rewardAmount = 10;
                            $reward = new GamificationReward();
                            $reward->user_id = $opinion->user_id;
                            $reward->reward_type = 'agree_event';
                            $reward->reward_amount = $rewardAmount;
                            $reward->save();

                            //count number of agrees in opinion
                            $agree_count = ShortOpinionLike::where(['short_opinion_id'=>$opinion_id,'Agree_Disagree'=>'1'])->count();
                            $achievement_id = 0;
                            if($agree_count==5){
                                $achievement_id = 5;
                            }else if($agree_count==10){
                                $achievement_id = 11;
                            }else if($agree_count==50){
                                $achievement_id = 6;
                            }else if($agree_count==100){
                                $achievement_id = 8;
                            }else if($agree_count==500){
                                $achievement_id = 0;
                            }else if($agree_count==1000){
                                $achievement_id = 10;
                            }else{
                                $achievement_id = 0;
                            }

                            if($achievement_id!=0){
                                $achievement = DB::table('achievements')->where('achievement_id', $achievement_id)->first();
                                $ruser_id = $opinion->user_id;
                                
                                        $userHasUnlockedAchievement = DB::table('user_achievements')
                                            ->where('user_id', $ruser_id)
                                            ->where('achievements_id', $achievement_id)
                                            ->exists();
                                
                                        if (!$userHasUnlockedAchievement) {
                                            DB::table('user_achievements')->insert([
                                                'user_id' => $ruser_id,
                                                'achievements_id' => $achievement_id,
                                            ]);
                                
                                            $reward2 = new GamificationReward();
                                            $reward2->user_id = $ruser_id;
                                            $reward2->reward_type = 'achievement: ' . $achievement->title;
                                            $reward2->reward_amount = $achievement->reward;
                                            $reward2->save();
            
                                            $achievement2 = Achievement::where('achievement_id', $achievement_id)->first();
                                    
            
                                            $follower_ids =[$ruser_id,10362];
                                            $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
                                
                                            foreach(array_chunk($fcm_tokens,100) as $chunk){
                                                dispatch(new AchievementUnlockedJob($achievement2,Auth::user()->user,$chunk));
                                            }
                                        }
                            }
            
                    

                        $this->notify_followers_AD($opinion,'ShortOpinionLiked',$ShortOpinionLike);
                       
						}
				 
				 
				 //*******************************************************************************
                if($opinion){
                    $opinion_liked=ShortOpinionLike::where(['user_id'=>Auth::user()->user_id,'short_opinion_id'=>$opinion_id])->first();
                    if($opinion_liked){
                        //Auth::user()->user->likes()->detach($opinion_id);
                        DB::table('notifications')
                        ->where('data','like','%"event":"OPINION_LIKED"%')
                        ->where('data','like','%"opinion_id":'.$opinion_id.'%')
                        ->where('data','like','%"sender_id":'.Auth::user()->user_id.'%')
                        ->delete();
                        $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                        $response=array('status'=>'success','result'=>0,'message'=>'Opinion action Updated','total'=>$count,'Agree_Disagree'=> $result_AD);

                        
                        return response()->json($response,200);
                    }else{
                       // Auth::user()->user->likes()->attach($opinion_id);
                        ShortOpinion::where(['id'=>$opinion->id])->update(['last_updated_at'=>Carbon::now()]);
                        $count=DB::table('short_opinion_likes')->where(['short_opinion_id'=>$opinion_id])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>'Opinion action updated','total'=>$count,'Agree_Disagree'=> $result_AD);
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Opinion not found');
                    return response()->json($response, 200);
                }
            }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    // function for like thread
    public function like_thread(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'thread_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $thread=Thread::where('id',$request->input('thread_id'))->first();
                if($thread){
                    $thread_liked=ThreadLike::where(['user_id'=>Auth::user()->user_id,'thread_id'=>$thread->id])->first();
                    if($thread_liked){
                        Auth::user()->user->liked_thread()->detach($thread->id);
                        DB::table('notifications')
                        ->where('data','like','%"event":"THREAD_LIKED"%')
                        ->where('data','like','%"thread_id":'.$thread->id.'%')
                        ->where('data','like','%"sender_id":'.Auth::user()->user_id.'%')
                        ->delete();
                        $count=DB::table('thread_likes')->where(['thread_id'=>$thread->id])->count();
                        $response=array('status'=>'success','result'=>0,'message'=>'Thread like removed','total'=>$count);
                        return response()->json($response,200);
                    }else{
                        Auth::user()->user->liked_thread()->attach($thread->id);
                        $this->notify_followers($thread,'ThreadLiked');
                        $count=DB::table('thread_likes')->where(['thread_id'=>$thread->id])->count();
                        $response=array('status'=>'success','result'=>1,'message'=>'Thread liked','total'=>$count);
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Thread not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    // function for follow thread
    public function follow_thread(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'thread_id'=>'required'
            ]);
            if($validator->fails()) {
                    $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                    return response()->json($response, 200);
            }else{
                $thread=Thread::where('id',$request->input('thread_id'))->first();
                if($thread){
                    $thread_followed=ThreadFollower::where(['user_id'=>Auth::user()->user_id,'thread_id'=>$thread->id])->exists();
                    if($thread_followed){
                        Auth::user()->user->followed_thread()->detach($thread->id);
                        $response=array('status'=>'success','result'=>0,'message'=>'Thread follow removed');
                        return response()->json($response,200);
                    }else{
                        Auth::user()->user->followed_thread()->attach($thread->id);
                        $response=array('status'=>'success','result'=>1,'message'=>'Thread followed');
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Thread not found');
                    return response()->json($response, 200);
                }
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    protected function notify_followers($object,$event){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($event=='ShortOpinionLiked' && $object->user && $object->user->id!==Auth::user()->user_id && !in_array($object->user->id,$follower_ids)){
            array_push($follower_ids,$object->user->id);
            $followers->push($object->user);
        }
        $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            if($event=='ThreadLiked'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ThreadLikedJob($object,Auth::user()->user,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ThreadLiked($object,Auth::user()->user,$fcm_tokens));
                }
            }else{
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionLikedJob($object,Auth::user()->user,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionLiked($object,Auth::user()->user,$fcm_tokens));
                }
            }
        }catch(\Exception $e){}
    }
    protected function notify_followers_AD($object,$event,$liked){
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($event=='ShortOpinionLiked' && $object->user && $object->user->id!==Auth::user()->user_id && !in_array($object->user->id,$follower_ids)){
            array_push($follower_ids,$object->user->id);
            $followers->push($object->user);
        }
        $fcm_tokens=UserDevice::whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            if($event=='ThreadLiked'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ThreadLikedJob($object,Auth::user()->user,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ThreadLiked($object,Auth::user()->user,$fcm_tokens));
                }
            }else{
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionLikedJob($object,Auth::user()->user,$chunk,$liked));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionLiked($object,Auth::user()->user,$fcm_tokens,$liked));
                }
            }
        }catch(\Exception $e){}
    }



}
