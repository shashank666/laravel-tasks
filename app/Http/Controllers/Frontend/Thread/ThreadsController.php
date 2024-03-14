<?php

namespace App\Http\Controllers\Frontend\Thread;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use App\Model\Thread;
use App\Model\ShortOpinion;
use App\Model\ThreadOpinion;
use App\Model\ThreadFollower;

use Config;
use \Carbon\Carbon;
use DB;

class ThreadsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',['only'=>['circle']]);

        $colors=['#37474f','#ff9800','#0097a7','#5c007a','#00796b','#d32f2f','#5d4037','#827717','#689f38','#e91e63','#424242','#1565c0','#880e4f','#f57f17'];
        View::share('colors',$colors);

    }

    public function latest(Request $request){
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];
        $threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions','>','0')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.latest',compact('threads','followed_threads'));
    }

    public function trending(Request $request){

        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        $trending_threads=ThreadOpinion::where('is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->with('thread')
        ->groupBy('thread_id')
        ->having('count', '>' , 0)
        ->orderBy('count','desc')
        ->take(24)
        ->get();

        if(count($trending_threads)>0){
            $trending_threads_ids=$trending_threads->pluck('thread_id')->toArray();
            $threads=Thread::where('is_active',1)->whereNotIn('id',$trending_threads_ids)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }else{
            $threads=Thread::where('is_active',1)->withCount('opinions')->has('opinions','>=','2')->orderBy('opinions_count','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        }
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.trending',compact('trending_threads','threads','followed_threads'));
    }

    public function circle(Request $request){
        $following_ids=Auth::user()->active_followings->pluck('id')->toArray();
        $opinions_ids=ShortOpinion::whereIn('user_id',$following_ids)->where('is_active',1)->get()->pluck('id')->toArray();
        $thread_ids=ThreadOpinion::whereIn('short_opinion_id',$opinions_ids)->get()->pluck('thread_id')->toArray();

        $threads=Thread::whereIn('id',$thread_ids)->where('is_active',1)->withCount('opinions')->has('opinions','>','0')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];

        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.circle',compact('threads','followed_threads'));
    }

    public function followed(Request $request){
        $threads=ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->paginate(Config::get('app.company_ui_settings')->all_threads_pagination);
        $followed_threads=Auth::check()?$this->my_followed_threadids(Auth::user()->id):[];
        if($request->ajax()){
            $view = (String) view('frontend.threads.components.threads_loop2',compact('threads','followed_threads'));
            return response()->json(['html'=>$view]);
        }
        return view('frontend.threads.followed',compact('threads','followed_threads'));
    }
}
