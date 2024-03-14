<?php

namespace App\Http\Controllers\Admin\Poll;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Model\Thread;
use App\Model\Post;
use App\Model\CategoryPost;
use App\Model\PollType;
use App\Model\Polls;
use App\Model\PollRelation;
use App\Model\PollThread;
use App\Model\PollResults;
use App\Model\PollMultipleChoiceOption;
use Carbon\Carbon;
use DB;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','polls');
    }

    public function index(Request $request){
        
        $polls= Polls::orderBy('created_at','desc')->paginate(20);
        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        
        $today= Carbon::now()->startOfDay();
        $yesterday= Carbon::now()->yesterday();
        $yesterday_last= Carbon::now()->yesterday()->endOfDay();
        $seven_days= Carbon::now()->subDays(7);
        $this_month= Carbon::now()->firstOfMonth();
        $last_month_start= Carbon::now()->subMonth(1)->firstOfMonth();
        $last_month_end= Carbon::now()->subMonth(1)->lastOfMonth()->endOfDay();

        $top_polls_trending = DB::table('poll_results')
                ->leftJoin('polls', 'polls.id', '=', 'poll_results.poll_id')
                ->whereBetween('poll_results.created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->groupBy('poll_results.poll_id')
                ->select('polls.*', DB::raw("COUNT(poll_results.poll_id) as count_top_poll"))
                ->orderBy('count_top_poll','desc')
                ->paginate(5);

        $top_polls = DB::table('poll_results')
                ->leftJoin('polls', 'polls.id', '=', 'poll_results.poll_id')
                ->groupBy('poll_results.poll_id')
                ->select('polls.*', DB::raw("COUNT(poll_results.poll_id) as count_top_poll"))
                ->orderBy('count_top_poll','desc')
                ->paginate(5);

        $top_locations_trending = DB::table('poll_results')
                ->leftJoin('user_location', 'user_location.user_id', '=', 'poll_results.user_id')
                ->whereBetween('poll_results.created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->groupBy('user_location.city')
                ->select('user_location.*', DB::raw("COUNT(user_location.city) as count_city"))
                ->orderBy('count_city','desc')
                ->paginate(6);

        $top_locations = DB::table('poll_results')
                ->leftJoin('user_location', 'user_location.user_id', '=', 'poll_results.user_id')
                ->groupBy('user_location.city')
                ->select('user_location.*', DB::raw("COUNT(user_location.city) as count_city"))
                ->orderBy('count_city','desc')
                ->paginate(6);

        //$pollresults= PollResults::with('locations')->orderBy('created_at','desc')->paginate(1);

        //var_dump($pollresults);

        $polls_count['today']=DB::table('polls')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $polls_count['yesterday']= DB::table('polls')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $polls_count['last_7_days']= DB::table('polls')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $polls_count['this_month']=DB::table('polls')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $polls_count['last_month']=DB::table('polls')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $polls_count['total']=DB::table('polls')->count();
        $polls_count['active']=DB::table('polls')->where('visibility',1)->count();
        $polls_count['disabled']=DB::table('polls')->where('visibility',0)->count();
        

        $polls_vote_count['today']=DB::table('poll_results')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $polls_vote_count['yesterday']= DB::table('poll_results')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $polls_vote_count['last_7_days']= DB::table('poll_results')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $polls_vote_count['this_month']=DB::table('poll_results')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $polls_vote_count['last_month']=DB::table('poll_results')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $polls_vote_count['total']=DB::table('poll_results')->count();
        $polls_vote_count['active']=DB::table('poll_results')->where('is_active',1)->count();
        
        

        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('polls'=>$polls));
        }
        
        //var_dump($polls);
        return view('admin.dashboard.poll.index',compact('polls','polls_count','polls_vote_count','top_polls','top_polls_trending','top_locations_trending','top_locations','today','from','to','yesterday','yesterday_last','seven_days','this_month','last_month_start','last_month_end'));
        
    }

    public function showPolls(Request $request){
        
        //$polls= Polls::with('locations')->withCount('pollresults')->orderBy('created_at','desc')->paginate(20);
        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $visibility=$request->has('visibility')?$request->query('visibility'):'0,1';
        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        
        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'id';
        $DBsearchQuery=$searchBy=='id' || $searchBy=='user_id'  ? $searchQuery:'%'.$searchQuery.'%';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        
        $query = Polls::query();
        $query->whereIn('is_active',explode(',',$is_active));
        $query->whereIn('visibility',explode(',',$visibility));        
        $query->whereBetween('created_at',[$from,$to]);
        $query->with(['pollresults','locations']);
        $query->withCount(['pollresults']);
        
        if(isset($DBsearchQuery) && strlen($DBsearchQuery)>0 && $searchBy!=null){
                if($searchBy=='user_name'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('name', 'like',$DBsearchQuery);
                    });
                }else if($searchBy=='user_id'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('id', '=',$DBsearchQuery);
                    });
                }else if($searchBy=='id'){
                    $query->where('id','=',$DBsearchQuery);
                }
        }
        $query->orderBy($sortBy,$sortOrder);
        $polls = $query->paginate($limit);

        $polls_count['today']=DB::table('polls')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $polls_count['yesterday']= DB::table('polls')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $polls_count['last_7_days']= DB::table('polls')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $polls_count['this_month']=DB::table('polls')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $polls_count['last_month']=DB::table('polls')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $polls_count['total']=DB::table('polls')->count();
        $polls_count['active']=DB::table('polls')->where('visibility',1)->count();
        $polls_count['disabled']=DB::table('polls')->where('visibility',0)->count();
        $polls_vote_count['total']=DB::table('poll_results')->count();
        
        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('polls'=>$polls));
        }
        
        //var_dump($polls);
        return view('admin.dashboard.poll.all',compact('polls','searchQuery','searchBy','visibility','polls_count','polls_vote_count'));
    }

    public function showPollVotes(Request $request){
        
        //$polls= Polls::with('locations')->withCount('pollresults')->orderBy('created_at','desc')->paginate(20);
        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $today= Carbon::now();
        
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        $platform=$request->has('platform')?$request->query('platform'):'website,android';
        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $visibility=$request->has('visibility')?$request->query('visibility'):'0,1';
        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        
        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'id';
        $DBsearchQuery=$searchBy=='id' || $searchBy=='user_id'  ? $searchQuery:'%'.$searchQuery.'%';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        
        $query = PollResults::query();
        $query->whereIn('is_active',explode(',',$is_active));
        $query->whereIn('visibility',explode(',',$visibility));
        $query->whereBetween('created_at',[$from,$to]);
        $query->with(['locations','mcpsoptions','polls']);
        
        if(isset($DBsearchQuery) && strlen($DBsearchQuery)>0 && $searchBy!=null){
                if($searchBy=='user_name'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('name', 'like',$DBsearchQuery);
                    });
                }else if($searchBy=='user_id'){
                    $query->whereHas('user', function ($q) use ($DBsearchQuery) {
                        $q->where('id', '=',$DBsearchQuery);
                    });
                }else if($searchBy=='id'){
                    $query->where('id','=',$DBsearchQuery);
                }
        }
        $query->orderBy($sortBy,$sortOrder);
        $polls = $query->paginate($limit);

        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('polls'=>$polls));
        }
        
        //var_dump($polls);
        return view('admin.dashboard.poll.votes',compact('polls','searchQuery','searchBy','sortOrder','sortBy','is_active','platform','limit','page','from','to','today','visibility'));
    }

    public function individualPoll(Request $request,$id){
        
        $pollresults= PollResults::where(['poll_id'=>$id])->with('locations','user','mcpsoptions')->orderBy('created_at','desc')->paginate(20);
        //$poll= PollResults::where(['is_active'=>1,'poll_id'=>$id])->first();
        //var_dump($poll);
        $poll = Polls::where(['id'=>$id])->with('locations')->first();
        $polls_result_count['today']=DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->whereRaw('Date(created_at) = CURDATE()')->count();
        $polls_result_count['yesterday']= DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->whereDate('created_at',Carbon::yesterday())->count();
        $polls_result_count['last_7_days']= DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $polls_result_count['this_month']=DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $polls_result_count['last_month']=DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $polls_result_count['total']=DB::table('poll_results')->where(['poll_id'=>$id])->count();
        $polls_result_count['active']=DB::table('poll_results')->where(['is_active'=>1,'poll_id'=>$id])->count();
        if($poll->poll_type=='MCPS'){
        $poll_options = DB::table('poll_results')
                ->leftJoin('poll_multiple_choice_options', 'poll_multiple_choice_options.id', '=', 'poll_results.mcps_id')
                ->where('poll_results.poll_id','=',$id)
                ->groupBy('poll_results.mcps_id')
                ->select('poll_results.*','poll_multiple_choice_options.*', DB::raw("COUNT(poll_results.mcps_id) as count_pollresult"))
                ->groupBy('poll_results.mcps_id')
                ->get();
                //var_dump("expression");
            $poll_options_chart=array();
             foreach ($poll_options as $result) {
              $poll_options_chart[$result->options]=(int)$result->count_pollresult;
          }
        
        }
        else{
            $poll_options = DB::table('poll_results')
                
                ->where('poll_id','=',$id)
                ->groupBy('voting_type')
                ->select('poll_results.*', DB::raw("SUM(voting) as count_pollresult"))
                ->groupBy('voting_type')
                ->get();
                $poll_options_chart=array();
             foreach ($poll_options as $result) {
              $poll_options_chart[ucfirst(strtolower($result->voting_type))]=(int)$result->count_pollresult;
          }
        }
        
        
         $poll_locations = DB::table('poll_results')
                ->leftJoin('user_location', 'user_location.user_id', '=', 'poll_results.user_id')
                ->where('poll_results.poll_id','=',$id)
                ->groupBy('user_location.city')
                ->select('user_location.city', DB::raw("COUNT(user_location.city) as count_city"))
                ->orderBy('count_city','desc')
                ->paginate(6);
        
         //var_dump(count($poll_locations));

        if($request->has('json') && $request->query('json')==1){
            return response()->json(array('polls'=>$pollresults));
        }
        
        //var_dump($polls);
        return view('admin.dashboard.poll.poll',compact('pollresults','polls_result_count','poll','poll_options_chart','poll_locations'));
    }


    public function pollVisibility(Request $request,$pollid){
        $poll=Polls::find($pollid);
        $visibility=$poll->visibility==0?1:0;
        if($poll){

            $polls=Polls::where('id',$poll->id)->update(['visibility' => $visibility,'paused_at'=>Carbon::now()]);
            PollThread::where('poll_id',$poll->id)->update(['is_active' => $visibility,'updated_at'=>Carbon::now()]);
            PollRelation::where('poll_id',$poll->id)->update(['is_active' => $visibility,'updated_at'=>Carbon::now()]);
            PollResults::where('poll_id',$poll->id)->update(['is_active' => $visibility,'visibility' => $visibility,'updated_at'=>Carbon::now()]);
            if($request->has('json') && $request->query('json')==1){
                return response()->json(array('polls'=>$polls));
            }
          return redirect()->back();
        }
    }

    public function showEditPollForm(Request $request,$pollid){
        $poll=Polls::where(['id'=>$pollid,'is_active'=>1])->first();
        
        $threads = Thread::where('is_active','=',1)->get();
        $polls = Polls::where(['is_active'=>1,'visibility'=>1])->whereNotIn('id', [$pollid])->get();
        if($poll){
            $selected_threads = [];
            $selected_threads=PollThread::where(['poll_id'=>$pollid,'is_active'=>1])->pluck('thread_id')->toArray();
            $selected_polls = [];
            $selected_polls=PollRelation::where(['poll_id'=>$pollid,'is_active'=>1])->pluck('rel_poll_id')->toArray();
            return view('admin.dashboard.poll.poll_relation',compact('poll','threads','polls','selected_threads','selected_polls'));
          
        }
    }

    
    public function showAddPollTypeForm(){
        return view('admin.dashboard.poll.create_type');
    }

    public function showSelectPollType(){
        
        $polltypes = PollType::where(['is_active'=>1])->get();
        return view('admin.dashboard.poll.crud.index',compact('polltypes'));
    }

    public function showAddPollForm(Request $request){
        $polltype = $request->input('polltype');
        if($polltype == "UDN"){
        return view('admin.dashboard.poll.crud.udn.create',compact('polltype'));
        }
        elseif($polltype == "MCPS"){
            return view('admin.dashboard.poll.crud.mcps.create',compact('polltype'));
        }
    }

    
    public function createPollType(Request $request){
        $this->validate($request,[
            'type'=>'required',
            ]);

        $type=$request->input('type');
        $description=$request->input('description');
        $slug = str::slug($request->input('type'),'-');
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $opt1=$request->input('opt1');
        $opt2=$request->input('opt2');
        $opt3=$request->input('opt3');
        
        $polltype=new PollType();
        $polltype->type=$type;
        $polltype->slug= $slug;
        $polltype->description=$description;
        $polltype->ip_address=$ip_address;
        $polltype->opt1=$opt1;
        $polltype->opt2=$opt2;
        $polltype->opt3=$opt3;
        $polltype->is_active=1;
        $polltype->save();
        return redirect()->route('admin.polls');
    }

    public function createPoll(Request $request){
        $this->validate($request,[
            'title'=>'required',
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
        $poll_type=$request->input('polltype');
        //$slug = str::slug($request->input('title'),'-');
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $polltype = PollType::select('id')->where(['is_active'=>1,'type'=>$poll_type])->first();
        $polltype_id = $polltype->id;
        $poll=new Polls();
        $poll->user_id="Opined Team";
        $poll->title=$title;
        $poll->slug= $slug;
        $poll->description=$description;
        $poll->poll_type= $poll_type;
        $poll->polltype_id= $polltype_id;
        $poll->ip_address=$ip_address;
        $poll->is_active=1;
        $poll->save();
        if($poll_type == "UDN"){
            return redirect()->route('admin.polls');
        }
        else if($poll_type == "MCPS"){
            $options = $request->input('options');
            $color_code = $request->input('color');
            $poll = Polls::select('id')->where(['slug'=>$slug])->first();
            $poll_id = $poll->id;
            for($i = 0; $i < count($options); $i++) {
            $poll_option=new PollMultipleChoiceOption();
            $poll_option->poll_id=$poll_id;
            $poll_option->options=$options[$i];
            $poll_option->color_code=$color_code[$i];
            $poll_option->is_active=1;
            $poll_option->save();
            //var_dump($poll_option->polls());
            }
            return redirect()->route('admin.polls');
        
    }
        
    }
    public function updatePoll(Request $request){
        $this->validate($request,[
            'poll_id'=>'required',
            ]);

        $poll_id=$request->input('poll_id');
        $description=$request->input('description');
        $threads = $request->input('threads');
        $polls = $request->input('polls');
        $enablenote = $request->input('enablenote');
        $poll_result_note = $request->input('note');
        $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
        $poll_update = Polls::where(['id'=>$poll_id])->update(['description' => $description,'poll_result_note'=>$poll_result_note,'enablenote'=>$enablenote]);
        if($threads){
        PollThread::where(['poll_id'=>$poll_id])->delete();
          for($i = 0; $i < count($threads); $i++) {
          $poll_threads=new PollThread();
          $poll_threads->thread_id = $threads[$i];
          $poll_threads->poll_id = $poll_id;
          //var_dump($threads[$i]);
          $poll_threads->save();
        }
    }
        if($polls){
        PollRelation::where(['poll_id'=>$poll_id])->delete();
        for($i = 0; $i < count($polls); $i++) {
          $poll_relation=new PollRelation();
          $poll_relation->rel_poll_id = $polls[$i];
          $poll_relation->poll_id = $poll_id;
          //var_dump($threads[$i]);
          $poll_relation->save();
        }
    }
        //$poll_threads=new Polls();
        //$poll_threads=$this->save_threads($poll_threads,$poll_id,$threads);

        
        return redirect()->route('admin.polls');
    }


}
