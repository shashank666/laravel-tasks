<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Model\Category;
use App\Model\Post;
use App\Model\User;
use App\Model\Thread;
use App\Model\CategoryThread;
use App\Model\Shares;
use DB;
use Illuminate\Support\Facades\Auth;

use DateTime;

use Carbon\Carbon;

class ShareController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','share');
    }

    public function index(Request $request){
        $facebook_count['today']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->whereRaw('Date(shared_at) = CURDATE()')->count();
        $facebook_count['yesterday']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->whereDate('shared_at',Carbon::yesterday())->count();
        $facebook_count['last_7_days']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $facebook_count['this_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $facebook_count['last_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $facebook_count['total']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->count();

        $whatsapp_count['today']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->whereRaw('Date(shared_at) = CURDATE()')->count();
        $whatsapp_count['yesterday']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->whereDate('shared_at',Carbon::yesterday())->count();
        $whatsapp_count['last_7_days']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $whatsapp_count['this_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $whatsapp_count['last_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $whatsapp_count['total']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->count();
        

        $twitter_count['today']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->whereRaw('Date(shared_at) = CURDATE()')->count();
        $twitter_count['yesterday']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->whereDate('shared_at',Carbon::yesterday())->count();
        $twitter_count['last_7_days']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $twitter_count['this_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $twitter_count['last_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $twitter_count['total']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->count();
        

        $linkedin_count['today']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->whereRaw('Date(shared_at) = CURDATE()')->count();
        $linkedin_count['yesterday']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->whereDate('shared_at',Carbon::yesterday())->count();
        $linkedin_count['last_7_days']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $linkedin_count['this_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $linkedin_count['last_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $linkedin_count['total']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->count();
        
        $opined_count['today']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->whereRaw('Date(shared_at) = CURDATE()')->count();
        $opined_count['yesterday']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->whereDate('shared_at',Carbon::yesterday())->count();
        $opined_count['last_7_days']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $opined_count['this_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $opined_count['last_month']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $opined_count['total']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->count();

        
        return view('admin.dashboard.share.index',compact('facebook_count','whatsapp_count','twitter_count','linkedin_count','opined_count'));
        
    }

    
     public function showByPlateform(Request $request,$plateform){
        $shares=Shares::where(['plateform'=>$plateform,'is_active'=>1])->orderBy('shared_at','desc')->with('user','short_opinion','post')->paginate(20);
        $month_wise_count=Shares::where(['plateform'=>$plateform,'is_active'=>1])
        ->select(DB::raw('CONCAT(MONTHNAME(shared_at), "-",  YEAR(shared_at)) AS month_year'),
                DB::raw("MONTH(shared_at) as month , YEAR(shared_at) as year"),
                DB::raw("(COUNT(*)) as total"))
        ->orderBy(DB::raw("MONTH(shared_at),YEAR(shared_at)"))
        ->groupBy(DB::raw("MONTH(shared_at),YEAR(shared_at)"))
        ->get();
        //var_dump($shares);
        if($shares){
            
            return view('admin.dashboard.share.plateform',compact('shares','plateform','month_wise_count'));
        }else{
            return view('admin.error.404');
        }
    }

    public function topOpinions(Request $request){
        $fromDate= DateTime::createFromFormat('d-m-Y H:i:s',$request->input('from'));
        $toDate= DateTime::createFromFormat('d-m-Y H:i:s',$request->input('to'));

        $top_opinions_byshare=
        Shares::where(['is_active'=>1])
        ->whereNotIn('short_opinion_id', [0])
        ->with('short_opinion')
        ->select('*',DB::raw('COUNT(id) as total'))
        ->whereBetween('shared_at',[$fromDate,$toDate])
        ->groupBy('short_opinion_id')
        ->orderBy('total','desc')
        ->get();
        return response()->json($top_opinions_byshare);
    }
    
}