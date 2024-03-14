<?php

namespace App\Http\Controllers\Api\Polls;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Model\UserDevice;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use App\Events\ThreadViewCounterEvent;
use App\Jobs\AndroidPush\PollVotedJob;
use Carbon\Carbon;
use App\Model\Polls;
use App\Model\PollResults;
use App\Model\PollRelation;
use App\Model\PollThread;
use App\Model\Thread;
use App\Model\PollType;
use App\Model\PollMultipleChoiceOption;
use Notification;
use App\Notifications\Frontend\pollVoted;
use Illuminate\Notifications\Notification as NotificationsNotification;

/*use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
*/
class PollsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request){
        try{
             

           if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            }
 
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

     $polls= Polls::where(['visibility'=>1,'is_active'=>1])->orderBy('created_at','desc')->get();

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'Polls'=>$polls
                             ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function index2(Request $request){
        try{
             
            $user_id=null;
           if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            }
 
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

     $polls= Polls::where(['visibility'=>1,'is_active'=>1])->orderBy('created_at','desc')->get();
    

            $formatted_poll=[];
            $total_votes=0;
            foreach($polls as $poll){
                $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
                $formatted=$this->formatted_polls($poll,$user_id,$total_votes);
                array_push($formatted_poll,$formatted);
            }

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'Polls'=>$formatted_poll
                             ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function index3(Request $request){
        try{
             
            $user_id=null;
           if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            }
 
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

     $polls= Polls::where(['visibility'=>0,'is_active'=>1])->orderBy('created_at','desc')->get();
    

            $formatted_poll=[];
            $total_votes=0;
            foreach($polls as $poll){
                $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
                $formatted=$this->formatted_polls($poll,$user_id,$total_votes);
                array_push($formatted_poll,$formatted);
            }

            return response()->json([
                'status'=>'success',
                'result'=>1,
                'Polls'=>$formatted_poll
                             ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    

    public function user_polls(Request $request){
        try{
             

            $user_id=null;
           if($request->header('Authorization')){
            $user_name = $this->get_user_name_from_api_token($request->header('Authorization'));
            $user_id=$this->get_user_from_api_token($request->header('Authorization'));
        }
 
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

     $polls= Polls::where(['visibility'=>1,'is_active'=>1,'user_id'=>$user_name])->orderBy('created_at','desc')->get();

      $formatted_poll=[];
      $total_votes=0;
      foreach($polls as $poll){
        $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
          $formatted=$this->formatted_polls($poll,$user_id,$total_votes);
          array_push($formatted_poll,$formatted);
      }

      return response()->json([
          'status'=>'success',
          'result'=>1,
          'Polls'=>$formatted_poll
                       ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
    public function other_user_polls(Request $request){
        try{
             

            $user_name= $request->header('username');

 
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();

     $polls= Polls::where(['visibility'=>1,'is_active'=>1,'user_id'=>$user_name])->orderBy('created_at','desc')->get();

     $formatted_poll=[];
     $total_votes=0;
     foreach($polls as $poll){
        $total_votes = PollResults::where(['poll_id'=>$poll->id,'visibility'=>1,'is_active'=>1])->count();
         $formatted=$this->formatted_polls($poll,$user_name,$total_votes);
         array_push($formatted_poll,$formatted);
     }

     return response()->json([
         'status'=>'success',
         'result'=>1,
         'Polls'=>$formatted_poll
                      ]);

         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }
 public function get_polls_individual(Request $request){
	 
	 
	 //[]=;//input('slug');
	 $slug= $request->header('slug');//$slug;
	
if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                          }
 	
        $poll=Polls::where(['slug'=>$slug])->first();
        //$ip_address=//isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        //$check_user = $request->cookie('ip');
        if($poll){
			  //$user_id = Auth::user()->id;
            $restrict = PollResults::where(['poll_id'=>$poll->id,'user_id'=>$user_id,'is_active'=>1])->first();

       /*     if(Auth::user() && $check_user!=null){
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
            }*/
			
        $related_poll = PollRelation::where(['poll_id'=>$poll->id,'is_active'=>1])->take(6)->get();
        $related_polls = [];
         foreach($related_poll as $index=>$relatedpoll){
            $poll_rel=Polls::where(['id'=>$relatedpoll->rel_poll_id,'is_active'=>1])->first();
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
            return response()->json([
                'status'=>'success',
                'result'=>1,
                'total_votes'=>$total_votes,
                    'poll_result_up'=>$poll_result_up,
                    'poll_result_down'=>$poll_result_down,
                    'poll_result_neutral'=>$poll_result_neutral
                     ]);
            //var_dump($poll_result_up);
            //return view('frontend.polls.crud.result-read',compact('poll','poll_result_up','poll_result_down','poll_result_neutral','voting_type','total_votes','related_polls','related_threads'));
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
               $poll_options_chart[] = json_encode($poll_options_chart);
               $poll_options = PollMultipleChoiceOption::where(['poll_id'=>$poll->id,'is_active'=>1])->get();

               //var_dump($poll_options);
         
            //var_dump($poll_options);
			   $total_votes = PollResults::where(['poll_id'=>$poll->id,'is_active'=>1])->count();
			return response()->json([
						'status'=>'success',
						'result'=>1,
						'total_votes'=>$total_votes,
                        'poll_options'=>json_encode($poll_options),
                        'total_options'=>sizeof($poll_options_chart),
							'poll_options_chart'=>$poll_options_chart
                             ]);


               // $poll_options = PollResults::where(['poll_id'=>$poll->id,'is_active'=>1])->with('mcpsoptions')->groupBy('mcps_id')->get();
                 
                //var_dump($poll_options);
             
                //return view('frontend.polls.crud.result-read',compact('poll','related_polls','related_threads','total_votes','poll_options','poll_options_chart','olor_code'));
            }
            else if($restrict==null && $poll->poll_type == "UDN"){
               // return view('frontend.polls.crud.read',compact('poll','related_polls','related_threads'));
			   return response()->json([
						'status'=>'success',
						'result'=>1,
							'related_polls'=>json_encode($related_polls)
                             ]);

            }
            else if($restrict==null && $poll->poll_type == "MCPS"){
                
                if($poll->visibility==1){
                $poll_options = PollMultipleChoiceOption::where(['poll_id'=>$poll->id,'is_active'=>1])->get();
				
				return response()->json([
						'status'=>'success',
						'result'=>1,
							'poll_options'=>json_encode($poll_options)
                             ]);
                }else{
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
                   $poll_options_chart[] = json_encode($poll_options_chart);
                   $poll_options = PollMultipleChoiceOption::where(['poll_id'=>$poll->id,'is_active'=>1])->get();
    
                   //var_dump($poll_options);
             
                //var_dump($poll_options);
                   $total_votes = PollResults::where(['poll_id'=>$poll->id,'is_active'=>1])->count();
                return response()->json([
                            'status'=>'success',
                            'result'=>1,
                            'total_votes'=>$total_votes,
                            'poll_options'=>json_encode($poll_options),
                            'total_options'=>sizeof($poll_options_chart),
                                'poll_options_chart'=>$poll_options_chart
                                 ]);
    
                }


                //var_dump($poll_options);
                //return view('frontend.polls.crud.read',compact('poll','related_polls','related_threads','poll_options'));
            }
            else{
               $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }        
    }
    else{
          $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
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
      $voting=$request->input('voting');
      $req_poll = Polls::where(['id'=>$poll_id,'is_active'=>1])->first();
      
      
      if(Auth::user()){
        // $user_id = Auth::user()->id;
            if($request->header('Authorization')){
            $user_id=$this->get_user_from_api_token($request->header('Authorization'));
            $user = User::where(['id'=>$user_id,'is_active'=>1])->first();
            $slug = $url."/".$user->username;
            }
      }
      else{
        $user_id = null;
        $slug = null;
      } 

      $user_found=User::find($req_poll->user_id);
      try{
          $users_fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',[$req_poll->user_id])->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
          dispatch(new PollVotedJob($user,$users_fcm_tokens,$url, $req_poll->title));
          Notification::send($user_found,new pollVoted($user,$users_fcm_tokens));
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
        //return redirect()->back()->withCookie(cookie('ip', $ip_address, $minutes));
    }

    public function create_poll(Request $request) {
        try {
            if($request->header('Authorization')){
                // $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $user_name = $this->get_user_from_api_token($request->header('Authorization'));
            }
            $validator=Validator::make($request->all(), [
                'title'=>'required',
                'description'=>'required',
                'option1'=>'required',
                'option2'=>'required',
                'poll_duration'=>'required',
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
            } else {
                if(Auth::user()) {
                    $slugtemp = rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('title')))),'-');
                    $poll_duration = $request->input('poll_duration');
                    $poll_end_date=Carbon::now()->addDays($poll_duration);
                
                    $polls_temp  = Polls::whereRaw("slug REGEXP '^{$slugtemp}([0-9]*)?$'")->get();
                    $count = count($polls_temp);
                    if($count > 0) {
                        $slug = $slugtemp.$count;
                    } else {
                        $slug = $slugtemp;
                    }
                    $title=$request->input('title');
                    $description=$request->input('description');
                    $poll_type='MCPS';
                    $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
                    $polltype = PollType::select('id')->where(['is_active'=>1,'type'=>$poll_type])->first();
                    $polltype_id = $polltype->id;
                    $poll=new Polls();
                    $poll->user_id=$user_name;
                    $poll->title=$title;
                    $poll->slug= $slug;
                    $poll->description=$description;
                    $poll->poll_type= $poll_type;
                    $poll->polltype_id= $polltype_id;
                    $poll->ip_address=$ip_address;
                    $poll->is_active=1;
                    $poll->visibility=1;
                    $poll->end_date=$poll_end_date;
                    $poll->save();
                    $option3=$request->input('option3');
                    $option4=$request->input('option4');
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
                    if($option4!=null) {
                        $poll_option=new PollMultipleChoiceOption();
                        $poll_option->poll_id=$poll_id;
                        $poll_option->options=$option4;
                        $poll_option->color_code='#000000';
                        $poll_option->is_active=1;
                        $poll_option->save();
                    }
                    $response=array('status'=>'success','result'=>1,'message'=>'Poll Created','poll'=>$poll,'user_id'=>$user_name);
                    return response()->json($response, 200);
                } else {
                    $response=array('status'=>'error','result'=>0,'errors'=>'Please sign in to create a poll');
                    return response()->json($response, 200);
                }
            }

        } catch (Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }	

}
