<?php

namespace App\Http\Controllers\Admin\Opinion;
use Illuminate\Support\Str;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Model\Category;
use App\Model\CategoryThread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\ThreadOpinion;
use App\Model\Thread;
use App\Model\User;
use App\Model\Shares;
use App\Model\UserDevice;
use DB;
use Session;
use Carbon\Carbon;
use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Notifications\Frontend\ShortOpinionCreated;

use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;


class OpinionsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','opinions');
    }

    public function index(Request $request){

        $count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $count['total']=DB::table('short_opinions')->count();

        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();

        $likes=$request->has('likes_count')?$request->query('likes_count'):0;
        $likes_operator=$request->has('likes_operator')?$request->query('likes_operator'):'>=';

        $comments=$request->has('comments_count')?$request->query('comments_count'):0;
        $comments_operator=$request->has('comments_operator')?$request->query('comments_operator'):'>=';

        $platform=$request->has('platform')?$request->query('platform'):'website,android';
        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'id';
        $DBsearchQuery=$searchBy=='id' || $searchBy=='user_id' || $searchBy=='thread_id' ? $searchQuery:'%'.$searchQuery.'%';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        $filter_threads=$request->has('threads')?explode(',',trim($request->query('threads'))):[];

        $query = ShortOpinion::query();
        $query->whereIn('platform',explode(',',$platform));
        $query->whereIn('is_active',explode(',',$is_active));
        $query->whereBetween('created_at',[$from,$to]);
        $query->with(['threads','user:id,name,username,unique_id,image']);
        $query->withCount(['likes','comments','shares']);
        $query->has('likes',$likes_operator,$likes);
        $query->has('comments',$comments_operator,$comments);

        if(isset($DBsearchQuery) && strlen($DBsearchQuery)>0 && $searchBy!=null){
                if($searchBy=='user_name'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('name', 'like',$DBsearchQuery);
                    });
                }else if($searchBy=='user_id'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('id', '=',$DBsearchQuery);
                    });
                }else if($searchBy=='thread_id'){
                    $query->whereHas('threads', function ($q) use ($DBsearchQuery) {
                        $q->where('id','=',$DBsearchQuery);
                    });
                }else if($searchBy=='thread_name'){
                    $query->whereHas('threads', function ($q) use ($DBsearchQuery) {
                        $q->where('name','like',$DBsearchQuery);
                    });
                }else if($searchBy=='id'){
                    $query->where('id','=',$DBsearchQuery);
                }
        }


        if($filter_threads && $filter_threads[0]){
            $query->whereHas('threads', function ($q) use ($filter_threads) {
                $q->whereIn('threads.id',$filter_threads);
            });
        }

        $query->orderBy($sortBy,$sortOrder);
        $opinions = $query->paginate($limit);

        if($request->has('json') && $request->query('json')==1){
            return response()->json($opinions);
        }

         if($request->ajax()){
            $view = (String) view('admin.dashboard.opinion.opinion',compact('opinions'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.opinion.index',compact('count','opinions','searchQuery','searchBy','sortOrder','sortBy','platform','is_active','limit','page','from','to','likes','likes_operator','comments','comments_operator','filter_threads'));
        }
    }

    public function get_opinion_shares(Request $request){
       // var_dump($request->opinion_id);
        $opinion=ShortOpinion::where('id',$request->opinion_id)->where(['is_active'=>1])->first();
        
        $opinion_shares=Shares::where(['short_opinion_id'=>$opinion->id, 'is_active'=>1])->orderBy('shared_at','desc')->with('user')->paginate(20);
        if($opinion_shares->total()>0){
            
                $output='';
                $output_li='';
                for($i=0;$i<count($opinion_shares);$i++){
                    if(!empty($opinion_shares[$i]->user["username"]) && !empty($opinion_shares[$i]->user["unique_id"])){
                        $start='<li class="list-group-item">'.
                        '<div class="media align-items-center mb-2">'.
                                '<a class="mr-3" href="/cpanel/user/'.$opinion_shares[$i]->user["id"].'"><img class="rounded-circle" src="'.$opinion_shares[$i]->user["image"].'" height="50" width="50" alt="Go to the profile of '.$opinion_shares[$i]->user["name"].'"  onerror="this.onerror=null;this.src="/img/avatar.png";"></a>'.
                                '<div class="media-body">'.
                                    '<div class="d-flex justify-content-between align-items-center w-100">'.
                                            '<a  href="/cpanel/user/'.$opinion_shares[$i]->user["id"].'" style="color:#212121;">'.ucfirst($opinion_shares[$i]->user["name"]).'</a>';
                                            
                                            $middle='';
                                        

                            $end='</div>'.
                                '</div>'   .
                        '</div>'.
                        '</li>';

                        $li=$start.$middle.$end;
                        $output_li=$output_li.$li;
                    }
                }

                $ul='<ul class="list-group list-group-flush">'.$output_li.'</ul>';
                if($opinion_shares->nextPageUrl()==null){
                    $button='';
                    $output=$output_li.$button;
                }else{
                    $nextpage=$opinion_shares->currentPage()+1;
                    $button=' <button class="btn btn-sm btn-primary btn-block loadmore_likes" data-nextpage="'. $nextpage.'">Load More</button>';
                    $output=$output_li.$button;
                }

            echo $output;
        }else{
            $output='<ul class="list-group list-group-flush"><li class="list-group-item"><p>No one Shared this opinion yet.</p></li></ul>';
            echo $output;
        }
    }



    public function get_opinion_likes(Request $request){
       // var_dump($request->opinion_id);
        $opinion=ShortOpinion::where('id',$request->opinion_id)->where(['is_active'=>1])->first();
        
        $opinion_likes=ShortOpinionLike::where('short_opinion_id',$opinion->id)
        ->where('is_active',1)
        ->with('user')
        ->orderBy('liked_at','desc')
        ->paginate(20);
        //var_dump($opinion_likes->total());
        if($opinion_likes->total()>0){
            
                $output='';
                $output_li='';
                for($i=0;$i<count($opinion_likes);$i++){
                    if(!empty($opinion_likes[$i]->user["username"]) && !empty($opinion_likes[$i]->user["unique_id"])){
                        $start='<li class="list-group-item">'.
                        '<div class="media align-items-center mb-2">'.
                                '<a class="mr-3" href="/cpanel/user/'.$opinion_likes[$i]->user["id"].'"><img class="rounded-circle" src="'.$opinion_likes[$i]->user["image"].'" height="50" width="50" alt="Go to the profile of '.$opinion_likes[$i]->user["name"].'"  onerror="this.onerror=null;this.src="/img/avatar.png";"></a>'.
                                '<div class="media-body">'.
                                    '<div class="d-flex justify-content-between align-items-center w-100">'.
                                            '<a  href="/cpanel/user/'.$opinion_likes[$i]->user["id"].'" style="color:#212121;">'.ucfirst($opinion_likes[$i]->user["name"]).'</a>';
                                            
                                            $middle='';
                                        

                            $end='</div>'.
                                '</div>'   .
                        '</div>'.
                        '</li>';

                        $li=$start.$middle.$end;
                        $output_li=$output_li.$li;
                    }
                }

                $ul='<ul class="list-group list-group-flush">'.$output_li.'</ul>';
                if($opinion_likes->nextPageUrl()==null){
                    $button='';
                    $output=$output_li.$button;
                }else{
                    $nextpage=$opinion_likes->currentPage()+1;
                    $button=' <button class="btn btn-sm btn-primary btn-block loadmore_likes" data-nextpage="'. $nextpage.'">Load More</button>';
                    $output=$output_li.$button;
                }

            echo $output;
        }else{
            $output='<ul class="list-group list-group-flush"><li class="list-group-item"><p>No one liked this opinion yet.</p></li></ul>';
            echo $output;
        }
    }
    public function showDesableOpinion(Request $request){
        $count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $count['total']=DB::table('short_opinions')->count();

        $opinions=ShortOpinion::with('user','shares')->where(['is_active'=>0])->orderBy('updated_at','desc')->paginate(24);
        if($request->ajax()){
            $view = (String) view('admin.dashboard.user.desabled_opinion_row',compact('opinions'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.opinion.desabled_opinion',compact('opinions','count'));
        }
    }

    // function for adding and removing likes by userid , opinion_id
    public function like_opinion(Request $request)
    {
        $opinion_id=$request->input('opinion_id');
        $short_opinion=ShortOpinion::where(['id'=>$opinion_id,'is_active'=>1])->with('user')->first();
        $admin_id =  Auth::guard('admin')->user()->id;
                if($admin_id==4){
                  $user_id_test = rand(4406,4505);
                }
                elseif($admin_id==5){
                  $user_id_test = rand(4506,4605);
                }
                elseif($admin_id==6){
                  $user_id_test = rand(4606,4705);
                }
        $user_is = User::where(['id'=>$user_id_test])->first();
        $lk_user_id= $user_is->id;
        ShortOpinion::where(['id'=>$short_opinion->id])->update(['last_updated_at'=>Carbon::now()]);
        $Liked=ShortOpinionLike::where(['user_id'=>$user_is->id,'short_opinion_id'=>$opinion_id])->first();
        if($Liked){
            $user_is->likes()->detach($opinion_id);
            DB::table('notifications')
            ->where('data','like','%"event":"OPINION_LIKED"%')
            ->where('data','like','%"opinion_id":'.$opinion_id.'%')
            ->where('data','like','%"sender_id":'.$user_is->id.'%')
            ->delete();
            if($request->ajax()){
            $response=array('status'=>'like');
            return response()->json($response);
            }
        }else{
            $user_is->likes()->attach($opinion_id);
            $this->notify_followers($short_opinion,'ShortOpinionLiked',$lk_user_id);

            if($request->ajax()){
                $response=array('status'=>'liked');
                return response()->json($response);
            }
        }
    }

     protected function notify_followers($object,$event,$lk_user_id){
        $user_is = User::where(['id'=>$lk_user_id])->first();
        $followers=$user_is->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        if($event=='ShortOpinionLiked' && $object->user && $object->user->id!==$user_is->id && !in_array($object->user->id,$follower_ids)){
            array_push($follower_ids,$object->user->id);
            $followers->push($object->user);
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            if($event=='ThreadLiked'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ThreadLikedJob($object,$user_is,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ThreadLiked($object,$user_is,$fcm_tokens));
                }
            }else if($event=='ShortOpinionCreated'){
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionCreatedJob($object,$user_is,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionCreated($object,$user_is,$fcm_tokens));
                }
            }else{
                foreach(array_chunk($fcm_tokens,100) as $chunk){
                    dispatch(new ShortOpinionLikedJob($object,$user_is,$chunk));
                }
                foreach($followers as $follower){
                    Notification::send($follower,new ShortOpinionLiked($object,$user_is,$fcm_tokens));
                }
            }
        }catch(\Exception $e){}

    }

    public function trending(Request $request){

        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $count['total']=DB::table('short_opinions')->count();
        //$trending=ShortOpinion::where(['is_active'=>1])->whereBetween('created_at',[$from,$to])->with('user')->withCount('likes','comments','shares')->orderBy('likes_count','desc')->orderBy('comments_count', 'desc')->orderBy('shares_count', 'desc')->paginate(20);

        $opinions = ShortOpinion::with('user')
            ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'short_opinions.id')
            ->leftJoin('shares', 'shares.short_opinion_id', '=', 'short_opinions.id')
            ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'short_opinions.id')
            ->whereBetween('short_opinions.created_at',[$from,$to])
            ->select('short_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
            ->groupBy('short_opinions.id')
            ->orderBy('count','desc')
            ->paginate(20);

        if($request->has('json') && $request->query('json')==1){
            return response()->json($opinions);
        }

         if($request->ajax()){
            $view = (String) view('admin.dashboard.opinion.opinion',compact('opinions'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.opinion.trending',compact('count','opinions'));
        }
    }

     public function latest_updated(Request $request){

        $count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $count['total']=DB::table('short_opinions')->count();
        

        $opinions = ShortOpinion::with(['threads','user:id,name,username,unique_id,image'])
            ->withCount(['likes','comments','shares'])
            ->orderBy('updated_at','desc')
            ->paginate(20);

        if($request->has('json') && $request->query('json')==1){
            return response()->json($opinions);
        }

         if($request->ajax()){
            $view = (String) view('admin.dashboard.opinion.opinion',compact('opinions'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.opinion.latest_updated',compact('count','opinions'));
        }
    }


    public function lockdown_offer(Request $request){

        $from=Carbon::createSafe(2020, 4, 28, 0, 0, 0);
        //$from=Carbon::now()->subDays(120);
        $to=Carbon::now();
        $count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $count['total']=DB::table('short_opinions')->count();
        //$trending=ShortOpinion::where(['is_active'=>1])->whereBetween('created_at',[$from,$to])->with('user')->withCount('likes','comments','shares')->orderBy('likes_count','desc')->orderBy('comments_count', 'desc')->orderBy('shares_count', 'desc')->paginate(20);

        $opinions = ShortOpinion::with('user')->withCount('likes')
            ->where(['cover_type'=>'VIDEO','is_active'=>1])
            ->whereBetween('created_at',[$from,$to])
            ->orderBy('likes_count','desc')
            ->paginate(20);
          //  var_dump($opinions);
        if($request->has('json') && $request->query('json')==1){
            return response()->json($opinions);
        }

         if($request->ajax()){
            $view = (String) view('admin.dashboard.opinion.opinion',compact('opinions'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.opinion.lockdown_offer',compact('count','opinions'));
        }
    }

}
