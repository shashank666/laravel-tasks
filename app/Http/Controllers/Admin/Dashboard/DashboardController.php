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
use App\Model\UserDevice;
use DB;
use Illuminate\Support\Facades\Auth;

use DateTime;

use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','index');
    }

    public function index(Request $request){
        
        $user_count['today']=DB::table('users')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $user_count['yesterday']= DB::table('users')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $user_count['last_7_days']= DB::table('users')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $user_count['this_month']=DB::table('users')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $user_count['last_month']=DB::table('users')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $user_count['total']=DB::table('users')->count();
        $user_count['active']=DB::table('users')->where('is_active',1)->count();
        $user_count['disabled']=DB::table('users')->where('is_active',0)->count();
        $user_count['website_active']=DB::table('users')->where(['platform'=>'website','is_active'=>1])->count();
        $user_count['android_active']=DB::table('users')->where(['platform'=>'android','is_active'=>1])->count();
        $user_count['website_disabled']=DB::table('users')->where(['platform'=>'website','is_active'=>0])->count();
        $user_count['android_disabled']=DB::table('users')->where(['platform'=>'android','is_active'=>0])->count();


        $user_count['normal_users']=DB::table('users')->where('is_active',1)->where('registered_as_writer',0)->count();
        $user_count['writers_users']=DB::table('users')->where('is_active',1)->where('registered_as_writer',1)->count();


        $post_count['today']=DB::table('posts')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $post_count['yesterday']= DB::table('posts')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $post_count['last_7_days']= DB::table('posts')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $post_count['this_month']=DB::table('posts')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $post_count['last_month']=DB::table('posts')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $post_count['total']=DB::table('posts')->count();
        $post_count['active']=DB::table('posts')->where('is_active',1)->count();
        $post_count['disabled']=DB::table('posts')->where('is_active',0)->count();
        $post_count['website_active']=DB::table('posts')->where(['platform'=>'website','is_active'=>1])->count();
        $post_count['android_active']=DB::table('posts')->where(['platform'=>'android','is_active'=>1])->count();
        $post_count['website_disabled']=DB::table('posts')->where(['platform'=>'website','is_active'=>0])->count();
        $post_count['android_disabled']=DB::table('posts')->where(['platform'=>'android','is_active'=>0])->count();

        $thread_count['today']=DB::table('threads')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $thread_count['yesterday']= DB::table('threads')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $thread_count['last_7_days']= DB::table('threads')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $thread_count['this_month']=DB::table('threads')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $thread_count['last_month']=DB::table('threads')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $thread_count['total']=DB::table('threads')->count();
        $thread_count['active']=DB::table('threads')->where('is_active',1)->count();
        $thread_count['disabled']=DB::table('threads')->where('is_active',0)->count();

        $opinion_count['today']=DB::table('short_opinions')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $opinion_count['yesterday']= DB::table('short_opinions')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $opinion_count['last_7_days']= DB::table('short_opinions')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $opinion_count['this_month']=DB::table('short_opinions')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $opinion_count['last_month']=DB::table('short_opinions')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $opinion_count['total']=DB::table('short_opinions')->count();
        $opinion_count['active']=DB::table('short_opinions')->where('is_active',1)->count();
        $opinion_count['disabled']=DB::table('short_opinions')->where('is_active',0)->count();
        $opinion_count['website_active']=DB::table('short_opinions')->where(['platform'=>'website','is_active'=>1])->count();
        $opinion_count['android_active']=DB::table('short_opinions')->where(['platform'=>'android','is_active'=>1])->count();
        $opinion_count['website_disabled']=DB::table('short_opinions')->where(['platform'=>'website','is_active'=>0])->count();
        $opinion_count['android_disabled']=DB::table('short_opinions')->where(['platform'=>'android','is_active'=>0])->count();

        $polls_vote_count['today']=DB::table('poll_results')->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $polls_vote_count['yesterday']= DB::table('poll_results')->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        $polls_vote_count['last_7_days']= DB::table('poll_results')->where('is_active',1)->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $polls_vote_count['this_month']=DB::table('poll_results')->where('is_active',1)->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $polls_vote_count['last_month']=DB::table('poll_results')->where('is_active',1)->whereMonth('created_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('created_at', date('Y'))->count();
        $polls_vote_count['total']=DB::table('poll_results')->count();
        
  /*      $short_opinion_count['total']=DB::table('short_opinions')->where('links','=',null)->count();
        $short_opinion_count['active']=DB::table('short_opinions')->where(['is_active'=>1,'links'=>null])->count();
        $short_opinion_count['disabled']=DB::table('short_opinions')->where(['is_active'=>0,'links'=>null])->count();
        $short_opinion_count['website_active']=DB::table('short_opinions')->where(['platform'=>'website','is_active'=>1,'links'=>null])->count();
        $short_opinion_count['android_active']=DB::table('short_opinions')->where(['platform'=>'android','is_active'=>1,'links'=>null])->count();
        $short_opinion_count['website_disabled']=DB::table('short_opinions')->where(['platform'=>'website','is_active'=>0,'links'=>null])->count();
        $short_opinion_count['android_disabled']=DB::table('short_opinions')->where(['platform'=>'android','is_active'=>0,'links'=>null])->count();
*/
        $share_count['facebook']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"FACEBOOK"])->count();
        $share_count['whatsapp']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"WHATSAPP"])->count();
        $share_count['twitter']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"TWITTER"])->count();
        $share_count['linkedin']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"LINKEDIN"])->count();
        $share_count['opined']=DB::table('shares')->where(['is_active'=>1,'plateform'=>"EMBED"])->count();
        $share_count['total']=DB::table('shares')->where(['is_active'=>1])->count();

        $share_count['today']=DB::table('shares')->where('is_active',1)->whereRaw('Date(shared_at) = CURDATE()')->count();
        $share_count['yesterday']= DB::table('shares')->where('is_active',1)->whereDate('shared_at',Carbon::yesterday())->count();
        $share_count['last_7_days']= DB::table('shares')->where('is_active',1)->whereBetween('shared_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $share_count['this_week']= DB::table('shares')->where('is_active',1)->whereBetween('shared_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $share_count['this_month']=DB::table('shares')->where('is_active',1)->whereMonth('shared_at',Carbon::now()->month)->whereYear('shared_at', date('Y'))->count();
        $share_count['last_month']=DB::table('shares')->where('is_active',1)->whereMonth('shared_at',Carbon::now()->subMonth(1)->format('m'))->whereYear('shared_at', date('Y'))->count();
        $device_count['total']=UserDevice::select('user_id')->distinct()->where('is_active',1)->count();
        $device_count['today']=UserDevice::select('user_id')->distinct()->where('is_active',1)->whereRaw('Date(created_at) = CURDATE()')->count();
        $device_count['yesterday']=UserDevice::select('user_id')->distinct()->where('is_active',1)->whereDate('created_at',Carbon::yesterday())->count();
        if($request->has('post_limit')){
            $post_limit=$request->query('post_limit');
        }else{
            $post_limit=12;
        }
        $unread_count=DB::table('messages')->where('mark_read',0)->count();
        $reported_post=DB::table('report_posts')->where('mark_read',0)->count();
        /*$latest_posts=Post::orderBy('created_at','desc')->with('user','categories')->take($post_limit)->get();*/
        $today = Carbon::now();
        if($request->has('json') && $request->query('json')==1){
            return response()->json(array(
                'unread_count'=>$unread_count,
                'reported_post'=>$reported_post,
                 'user_count'=>$user_count,
                 'post_count'=>$post_count,
                 'thread_count'=>$thread_count,
                  'opinion_count'=>$opinion_count,
                    'share_count'=>$share_count,
                    'short_opinion_count'=>$short_opinion_count));
        }

        $admin_id =  Auth::guard('admin')->user()->id;
        if($admin_id==2){
            return redirect()->intended(route('admin.write.opinion_new'));
        }

        return view('admin.dashboard.index',compact('unread_count','reported_post','user_count','post_count','thread_count','opinion_count','today','share_count','device_count','polls_vote_count'));
    }

    public function topCategories(Request $request){
        $fromDate= DateTime::createFromFormat('d-m-Y H:i:s',$request->input('from'));
        $toDate= DateTime::createFromFormat('d-m-Y H:i:s',$request->input('to'));

        $top_categories=
        DB::table('category_posts')
        ->leftJoin('categories','category_posts.category_id','=','categories.id')
        ->where('category_posts.is_active',1)
        ->select('categories.*',DB::raw('COUNT(category_posts.category_id) as total'))
        ->whereBetween('category_posts.created_at',[$fromDate,$toDate])
        ->groupBy('category_posts.category_id')
        ->orderBy('total','desc')
        ->get();

        return response()->json($top_categories);
    }
}
