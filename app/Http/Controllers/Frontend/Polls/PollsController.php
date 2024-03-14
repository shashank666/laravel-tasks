<?php

namespace App\Http\Controllers\Frontend\Polls;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ThreadLike;
use App\Model\ThreadFollower;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\OfferPost;
use App\Model\Category;
use App\Model\Tag;
use App\Model\Shares;
use App\Model\CategoryFollower;
use App\Model\PollType;
use App\Model\Polls;
use App\Model\PollResults;
use App\Model\Post;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Like;
use App\Model\PollRelation;
use App\Model\PollThread;
use App\Model\PollMultipleChoiceOption;
use DB;
use App\Events\ThreadViewCounterEvent;
use Image;

use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Notifications\Frontend\ShortOpinionCreated;

use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;

use \Carbon\Carbon;
use App\Http\Helpers\VideoStream;
use App\Jobs\AndroidPush\PollVotedJob;
use App\Jobs\AndroidPush\UserFollowedJob;
use Embed\Providers\OEmbed\Poll;
use Illuminate\Support\Facades\DB as FacadesDB;
use Symfony\Component\Console\Input\Input;

class PollsController extends Controller
{

    
    public function __construct()
    {
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        
        $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
        $google_native_ad=DB::table('google_ads')->where(['id'=>2,'is_active'=>1])->first();
        View::share('google_ad',$google_ad);
        View::share('google_native_ad',$google_native_ad);
    }

    public function create(Request $request) {
        $this->validate($request,[
            'title'=>'required',
            'description'=>'required',
            'option1'=>'required',
            'option2'=>'required',
        ]);
        $slugtemp = rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('title')))),'-');
        $polls_temp  = Polls::whereRaw("slug REGEXP '^{$slugtemp}([0-9]*)?$'")->get();
        $count = count($polls_temp);
        if($count > 0){
            $slug = $slugtemp.$count;
            }
        else{
            $slug = $slugtemp;
        }
        $title=$request->input('title');
        $description=$request->input('description');
        $poll_type='MCPS';
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $polltype = PollType::select('id')->where(['is_active'=>1,'type'=>$poll_type])->first();
        $polltype_id = $polltype->id;
        $poll=new Polls();
        $poll->user_id=Auth::user()->id;
        $poll->title=$title;
        $poll->slug= $slug;
        $poll->description=$description;
        $poll->poll_type= $poll_type;
        $poll->polltype_id= $polltype_id;
        $poll->ip_address=$ip_address;
        $poll->is_active=1;
        $poll->visibility=1;
        $poll->save();
        $option3=$request->input('option3');
        $option4=$request->input('option4');
        $color_code = "#000000";
        $poll = Polls::select('id')->where(['slug'=>$slug])->first();
        $poll_id = $poll->id;
        
        //Option 1
        $poll_option=new PollMultipleChoiceOption();
        $poll_option->poll_id=$poll_id;
        $poll_option->options=$request->input('option1');
        $poll_option->color_code='#000000';
        $poll_option->is_active=1;
        $poll_option->save();

        //Option 2
        $poll_option=new PollMultipleChoiceOption();
        $poll_option->poll_id=$poll_id;
        $poll_option->options=$request->input('option2');
        $poll_option->color_code='#000000';
        $poll_option->is_active=1;
        $poll_option->save();

        //Option 3
        if($option3!=null) {
            $poll_option=new PollMultipleChoiceOption();
            $poll_option->poll_id=$poll_id;
            $poll_option->options=$option3;
            $poll_option->color_code='#000000';
            $poll_option->is_active=1;
            $poll_option->save();
        }

        //Option 4
        if($option3!=null) {
            $poll_option=new PollMultipleChoiceOption();
            $poll_option->poll_id=$poll_id;
            $poll_option->options=$option4;
            $poll_option->color_code='#000000';
            $poll_option->is_active=1;
            $poll_option->save();
        }    
        return redirect()->route('polls');
    }

    public function showPolls(Request $request){
        
        //======For Trending Polls uncomment below this line and add the $trending_polls variable in compact===


        /*
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $trending_poll=PollResults::where('is_active',1)
        ->select('poll_id',DB::raw('COUNT(poll_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->groupBy('poll_id')
        ->having('count', '>' , 0)
        ->orderBy('count','desc')
        ->take(20)
        ->get();
       // var_dump($trending_polls[0]['poll_id']);

        $trending_polls = [];
         foreach($trending_poll as $index=>$trendingpoll){
            $poll_trndng=Polls::where(['id'=>$trendingpoll->poll_id,'is_active'=>1,'visibility'=>1])->first();
            if($poll_trndng){
                array_push($trending_polls,$poll_trndng);
            }
        }
        */


        $polls= Polls::where(['visibility'=>1,'is_active'=>1])->orderBy('created_at','desc')->paginate(20);

        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('polls'=>$polls));
        }
        
        //var_dump($polls);
        return view('frontend.polls.index',compact('polls'));
    }

    public function get_polls_individual(Request $request,$slug){
        $poll=Polls::where(['visibility'=>1,'slug'=>$slug])->first();
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $check_user = $request->cookie('ip');
        if($poll){
            if(Auth::user() && $check_user!=null){
            $restrict = PollResults::where(['poll_id'=>$poll->id,'ip_address'=>$check_user,'visibility'=>1,'is_active'=>1])->first();
            }
            else if(Auth::user() && $check_user==null){
            $user_id = Auth::user()->id;
            $restrict = PollResults::where(['poll_id'=>$poll->id,'user_id'=>$user_id,'visibility'=>1,'is_active'=>1])->first();
            }
            else if(Auth::guest() && $check_user!=null){
                $restrict = PollResults::where(['poll_id'=>$poll->id,'ip_address'=>$check_user,'visibility'=>1,'is_active'=>1])->whereBetween('created_at', [Carbon::now()->subMinutes(10), Carbon::now()])->first();
            }
            else{
                $restrict = null;
            }
        $related_poll = PollRelation::where(['poll_id'=>$poll->id,'is_active'=>1])->take(6)->get();
        $related_polls = [];
         foreach($related_poll as $index=>$relatedpoll){
            $poll_rel=Polls::where(['id'=>$relatedpoll->rel_poll_id,'is_active'=>1,'visibility'=>1])->first();
            if($poll_rel){
                array_push($related_polls,$poll_rel);
            }
        }
        $related_thread = PollThread::where(['poll_id'=>$poll->id,'is_active'=>1])->take(6)->get();
        $related_threads = [];
         foreach($related_thread as $index=>$relatedthread){
            $thread_rel=Thread::where(['id'=>$relatedthread->thread_id,'is_active'=>1])->withCount('comment','opinions')->has('opinions', '>', 0)->first();
            if($thread_rel){
                array_push($related_threads,$thread_rel);
            }
        }
        foreach ($related_threads as $thread) {
                     $thread->opinions_count = $thread->opinions_count + $thread->comment_count;
                }
        if($restrict!=null && $poll->poll_type == "UDN"){
          
            $voting_type = $restrict->voting_type;
            $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
            $poll_result_up = PollResults::where(['poll_id'=>$poll->id,'voting_type'=>"upvote",'visibility'=>1,'is_active'=>1])->sum('voting');
            $poll_result_down = PollResults::where(['poll_id'=>$poll->id,'voting_type'=>"downvote",'visibility'=>1,'is_active'=>1])->sum('voting');
            $poll_result_neutral = PollResults::where(['poll_id'=>$poll->id,'voting_type'=>"neutral",'visibility'=>1,'is_active'=>1])->sum('voting');
            //var_dump($poll_result_up);
            return view('frontend.polls.crud.result-read',compact('poll','poll_result_up','poll_result_down','poll_result_neutral','voting_type','total_votes','related_polls','related_threads'));
            }
            else if($restrict!=null && $poll->poll_type == "MCPS"){

                //$poll_options = PollMultipleChoiceOption::where(['poll_id'=>$poll->id,'is_active'=>1])->with('poll_result')->get();


                $poll_options = DB::table('poll_results')
                ->leftJoin('poll_multiple_choice_options', 'poll_multiple_choice_options.id', '=', 'poll_results.mcps_id')
                ->where('poll_results.poll_id','=',$poll->id)
                ->groupBy('poll_results.mcps_id')
                ->select('poll_results.*','poll_multiple_choice_options.*', DB::raw("COUNT(poll_results.mcps_id) as count_pollresult"))
                ->groupBy('poll_results.mcps_id')
                ->get();
                $poll_options_chart=array();
                 foreach ($poll_options as $result) {
                  $poll_options_chart[$result->options]=(int)$result->count_pollresult;
              }
               $color_code = [];
                     foreach($poll_options as $color_codes){
                        $code=$color_codes->color_code;
                        if($code){
                            array_push($color_code,$code);
                        }
                    }
                    //var_dump($color_code);
               //$poll_options_chart[] = json_encode($poll_options_chart);
               //var_dump($poll_options);
         
            //var_dump($poll_options);


               // $poll_options = PollResults::where(['poll_id'=>$poll->id,'is_active'=>1])->with('mcpsoptions')->groupBy('mcps_id')->get();
                 
                //var_dump($poll_options);
                $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
                return view('frontend.polls.crud.result-read',compact('poll','related_polls','related_threads','total_votes','poll_options','poll_options_chart','color_code'));
            }
            else if($restrict==null && $poll->poll_type == "UDN"){
                return view('frontend.polls.crud.read',compact('poll','related_polls','related_threads'));
            }
            else if($restrict==null && $poll->poll_type == "MCPS"){
                $poll_options = PollMultipleChoiceOption::where(['poll_id'=>$poll->id,'is_active'=>1])->get();
                //var_dump($poll_options);
                return view('frontend.polls.crud.read',compact('poll','related_polls','related_threads','poll_options'));
            }
            else{
                abort(404);
            }        
        } else {
            abort(404);
        }   
    }

    public function store(Request $request)
    {
      
      $voting_type=$request->input('voting_type');
      $voting_type_head=$request->input('voting_type_head');
      $poll_id=$request->input('poll_id');
      $url=$request->input('url');
      $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
      $minutes = 10; //in minutes
      $req_poll = Polls::where(['id'=>$poll_id,'is_active'=>1])->first();
      
      $user=null;
      if(Auth::user()){
        $user_id = Auth::user()->id;
        $user = User::where(['id'=>$user_id,'is_active'=>1])->first();
        $slug = $url."/".$user->username;
      }
      else{
        $user_id = null;
        $slug = null;
      } 

      try{
        // $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$req_poll->user_id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        // dispatch(new PollVotedJob($user,$users_fcm_tokens));
       }catch(\Exception $e){echo "".$e;}

        if($voting_type_head == "UDN"){
            $voting=$request->input('voting');
            $poll=new PollResults();
            
            $poll->user_id=$user_id;
            $poll->poll_id=$poll_id;
            $poll->slug=$slug;
            $poll->voting_type=$voting_type;

            $poll->voting=$voting;
            $poll->ip_address=$ip_address;
            $poll->save();
        }
        if($voting_type_head == "MCPS"){
            $mcps_id=$request->input('select');
            $poll=new PollResults();
            
            $poll->user_id=$user_id;
            $poll->poll_id=$poll_id;
            $poll->slug=$slug;
            $poll->voting_type=$voting_type;

            $poll->mcps_id=$mcps_id;
            $poll->ip_address=$ip_address;
            $poll->save();
        }

        // Auth::user()->user->followings()->attach($user_id);
       

        return redirect()->back()->withCookie(cookie('ip', $ip_address, $minutes));
    }

    public function destroy(Polls $poll, Request $request) {
        if($poll && $poll->user_id==Auth::user()->id) {
            PollMultipleChoiceOption::where('poll_id',$poll->id)->delete();
            PollResults::where('poll_id',$poll->id)->delete();
            PollThread::where('poll_id',$poll->id)->delete();
            $poll->delete();
            if($request->ajax()) {
                return response()->json(array('status'=>'success','message'=>'Poll Successfully Deleted'));
            } else {
                return redirect()->route('polls');
            }
        } else {
            if($request->ajax()) {
                return response()->json(array('status'=>'error','message'=>'Unauthorized User To Delete Poll'));
            } else {
                return redirect()->route('polls');
            }
        }
    }
}
