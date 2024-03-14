<?php

namespace App\Http\Controllers\Admin\User;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Model\User;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\UserAccount;
use App\Model\UserDevice;
use App\Model\UserContact;
use App\Model\Post;
use App\Model\Like;
use App\Model\Bookmark;
use App\Model\Comment;
use App\Model\Follower;
use App\Model\Category;
use App\Model\CategoryFollower;
use App\Model\Thread;
use App\Model\Employee;
use App\Model\DeletedUser;
use App\Model\Admin;
use DB;
use Carbon\Carbon;
use App\Exports\UsersExport;
use App\Exports\DeletedUserExport;
use Maatwebsite\Excel\Facades\Excel;




class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','users');
    }

    public function adminView(Request $request){
    
        $admins=DB::table("admins")->where('status','=',1)->get();

        if($request->ajax()){
            $view = (String) view('admin.dashboard.user.admin_row',compact('admins'));
            return response()->json(['html'=>$view]);
        }else{

            return view('admin.dashboard.user.superadmin',compact('admins'));

        }

    }

     public function enableSuperAdmin(Request $request,$adminid){
        

       
        DB::table("admins")->where('id','=',$adminid)->update(['super' => 1]);
       
        return redirect()->route('admin.adminlist')->with(['message'=>'Admin has been successfully added as super.']);
    }

    public function desableSuperAdmin(Request $request,$adminid){
        

       
        DB::table("admins")->where('id','=',$adminid)->update(['super' => 0]);
       
        return redirect()->route('admin.adminlist')->with(['message'=>'Admin has been successfully removed from super.']);
    }

    public function showUsers(Request $request){


        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        $qry_download = str_replace('?', '',str_replace($request->url(), '',$request->fullUrl()));

        
        //var_dump(date("Y-m-d h:i:s", strtotime($from)));

        $tody= Carbon::now()->startOfDay();
        $todylast= Carbon::now()->endOfDay();
        $yesterdy= Carbon::now()->yesterday();
        $yesterdaylast= Carbon::now()->yesterday()->endOfDay();
        $sevendays= Carbon::now()->subDays(7);
        $thismonth= Carbon::now()->firstOfMonth();
        $lastmonthstart= Carbon::now()->subMonth(1)->firstOfMonth();
        $lastmonthend= Carbon::now()->subMonth(1)->lastOfMonth()->endOfDay();

        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $email_verified=$request->has('email_verified')?$request->query('email_verified'):'0,1';
        $mobile_verified=$request->has('mobile_verified')?$request->query('mobile_verified'):'0,1';
        $registered_as_writer=$request->has('registered_as_writer')?$request->query('registered_as_writer'):'0,1';
        $register_provider=$request->has('provider')?$request->query('provider'):'email,facebook,google,twitter,linkedin';
        $platform=$request->has('platform')?$request->query('platform'):'website,android';


        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'name';
        $DBsearchQuery=$searchBy=='id'? $searchQuery:'%'.$searchQuery.'%';

        $users=
        User::whereIn('is_active',explode(',',$is_active))
        ->whereIn('registered_as_writer',explode(',',$registered_as_writer))
        ->whereIn('provider',explode(',',$register_provider))
        ->whereIn('platform',explode(',',$platform))
        ->where($searchBy, 'LIKE', $DBsearchQuery)
        ->whereIn('email_verified',explode(',',$email_verified))
        ->whereIn('mobile_verified',explode(',',$mobile_verified))
        ->whereBetween('created_at',[$from,$to])
        ->with('locations')
        ->orderBy($sortBy,$sortOrder)
        ->paginate($limit);

        $providers= array(
            0 => array('name'=>'facebook','color'=>'#3b5998','icon'=>'fab fa-facebook-f'),
            1 => array('name'=>'twitter','color'=>'#1da1f2','icon'=>'fab fa-twitter'),
            2 => array('name'=>'linkedin','color'=>'#0077b5','icon'=>'fab fa-linkedin-in'),
            3 => array('name'=>'google','color'=>'#ea4335','icon'=>'fab fa-google-plus-g'),
            4 =>array('name'=>'email','color'=>'#fbbc05','icon'=>'fas fa-envelope')
        );

        $users_by_providers=array();
        $email_verified_count=DB::table('users')->select('email_verified', DB::raw('count(*) as total'))->groupBy('email_verified')->having('email_verified',1)->count();
        $mobile_verified_count=DB::table('users')->select('mobile_verified', DB::raw('count(*) as total'))->groupBy('mobile_verified')->having('mobile_verified',1)->count();
        $both_verified_count=DB::table('users')->where(['email_verified'=>1,'mobile_verified'=>1])->count();

        $registered_writer_count=DB::table('users')->select('registered_as_writer', DB::raw('count(*) as total'))->groupBy('registered_as_writer')->having('registered_as_writer',1)->count();
        $today_verified=DB::table('users')->where(['email_verified'=>1,'mobile_verified'=>1])->whereRaw('Date(created_at) = CURDATE()')->count();

        foreach($providers as $provider){
            $count=DB::table('users')->select('provider', DB::raw('count(*) as total'))->groupBy('provider')->having('provider',$provider['name'])->first();
            $provider['total']=$count==null?0:$count->total;

            $today_count=DB::table('users')->select('provider', DB::raw('count(*) as total'))->whereRaw('Date(created_at) = CURDATE()')->groupBy('provider')->having('provider',$provider['name'])->first();
            $provider['today']=$today_count==null?0:$today_count->total;

            array_push($users_by_providers,$provider);
        }

        $today=DB::table('users')->whereRaw('Date(created_at) = CURDATE()')->count();
        $yesterday= DB::table('users')->whereDate('created_at',Carbon::yesterday())->count();
        $this_week= DB::table('users')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $last_7_days= DB::table('users')->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count();
        $this_month=DB::table('users')->whereMonth('created_at',Carbon::now()->month)->whereYear('created_at', date('Y'))->count();
        $last_month=DB::table('users')->whereMonth('created_at',Carbon::now()->subMonth()->format('m'))->whereYear('created_at', date('Y'))->count();
        $all=DB::table('users')->count();
        $deleted=DB::table('deleted_users')->count();
        $blocked=DB::table('users')->where('is_active',0)->count();

        $user_count['active']=DB::table('users')->where('is_active',1)->count();
        $user_count['disabled']=$blocked;
        $user_count['today']=$today;
        $user_count['today_verified']=$today_verified;
        $user_count['yesterday']=$yesterday;
        $user_count['last_7_days']=$last_7_days;
        $user_count['this_week']=$this_week;
        $user_count['this_month']=$this_month;
        $user_count['last_month']=$last_month;
        $user_count['all']=$all;
        $user_count['email_verified']=$email_verified_count;
        $user_count['mobile_verified']=$mobile_verified_count;
        $user_count['both_verified']=$both_verified_count;

        $user_count['blocked']=$blocked;
        $user_count['deleted']=$deleted;
        $user_count['registered_writer_count']=$registered_writer_count;

        $month_wise_count=DB::table("users")
        ->select(DB::raw('CONCAT(MONTHNAME(created_at), "-",  YEAR(created_at)) AS month_year'),
                DB::raw("MONTH(created_at) as month , YEAR(created_at) as year"),
                DB::raw("(COUNT(*)) as total"))
        ->orderBy(DB::raw("MONTH(created_at),YEAR(created_at)"))
        ->groupBy(DB::raw("MONTH(created_at),YEAR(created_at)"))
        ->get();

        if($request->ajax()){
            $view = (String) view('admin.dashboard.user.user_row',compact('users'));
            return response()->json(['html'=>$view]);
        }else{

            return view('admin.dashboard.user.index',compact('users_by_providers','user_count','users','month_wise_count',
            'from','to','is_active','email_verified','mobile_verified',
            'registered_as_writer','register_provider','platform',
            'sortBy','sortOrder','limit','searchBy','searchQuery', 'tody','yesterdy','yesterdaylast','sevendays','thismonth','lastmonthstart','lastmonthend','todylast','qry_download'));

        }

    }


    public function showEmployees(Request $request){

        $employees=DB::table("employee")->where('status','=',1)->get();
        $from=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();

        
        
        if($request->ajax()){
            $view = (String) view('admin.dashboard.employee.employee_row',compact('users'));
            return response()->json(['html'=>$view]);
        }else{

            return view('admin.dashboard.employee.employees',compact('from','to','employees'));

        }

    }

   public function showAddEmployeeForm(){
        return view('admin.dashboard.employee.add_employee');
    }

    public function addEmployee(Request $request){
        $this->validate($request,[
            'name'=>'required',
            
        ]);

        $name=$request->input('name');
        $email=$request->input('email');
        $phone_code=$request->input('phone_code');
        $mobile=$request->input('mobile');
        $dateofbirth=$request->input('dob');
        $position=$request->input('position');
        $dateofjoin=$request->input('joindate');
        $employee=new Employee();
        $this->saveEmployee($employee,$name,$email,$phone_code,$mobile,$dateofbirth,$position,$dateofjoin,0);
        return redirect()->route('admin.administration')->with(['message'=>'Employee has been successfully Created.']);
    }


    public function showDeletedUsers(Request $request){
        $users=DB::table('deleted_users')->orderBy('deleted_at','desc')->paginate(24);
        if($request->ajax()){
            $view = (String) view('admin.dashboard.user.deleted_user_row',compact('users'));
            return response()->json(['html'=>$view]);
        }else{
            return view('admin.dashboard.user.deleted',compact('users'));
        }
    }

    public function showUserDetailsById(Request $request,$id){
        $tab=$request->has('tab')?$request->query('tab'):'profile';
        $user=User::find($id);
        $data=array();
        if($user){

        $count['post_published_active']=DB::table('posts')->where(['user_id'=>$user->id,'status'=>1,'is_active'=>1])->count();
        $count['post_published_disabled']=DB::table('posts')->where(['user_id'=>$user->id,'status'=>1,'is_active'=>0])->count();
        $count['post_drafts_active']=DB::table('posts')->where(['user_id'=>$user->id,'status'=>0,'is_active'=>1])->count();
        $count['post_drafts_disabled']=DB::table('posts')->where(['user_id'=>$user->id,'status'=>0,'is_active'=>0])->count();
        $count['post_likes_active']=DB::table('likes')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['post_likes_disabled']=DB::table('likes')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['post_comments_active']=DB::table('comments')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['post_comments_disabled']=DB::table('likes')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['post_bookmarks_active']=DB::table('bookmarks')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['post_bookmarks_disabled']=DB::table('likes')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['opinion_active']=DB::table('short_opinions')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['opinion_disabled']=DB::table('short_opinions')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['opinion_likes_active']=DB::table('short_opinion_likes')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['opinion_likes_disabled']=DB::table('short_opinion_likes')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['opinion_comments_active']=DB::table('short_opinion_comments')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['opinion_comments_disabled']=DB::table('short_opinion_comments')->where(['user_id'=>$user->id,'is_active'=>0])->count();
        $count['followers_active']=DB::table('followers')->where(['leader_id'=>$user->id,'is_active'=>1])->count();
        $count['followers_disabled']=DB::table('followers')->where(['leader_id'=>$user->id,'is_active'=>0])->count();
        $count['followings_active']=DB::table('followers')->where(['follower_id'=>$user->id,'is_active'=>1])->count();
        $count['followings_disabled']=DB::table('followers')->where(['follower_id'=>$user->id,'is_active'=>0])->count();
        $count['category_followed_active']=DB::table('category_followers')->where(['user_id'=>$user->id,'is_active'=>1])->count();
        $count['category_followed_disabled']=DB::table('category_followers')->where(['user_id'=>$user->id,'is_active'=>0])->count();

        if($tab=='posts'){
            $data=Post::where(['user_id'=>$user->id,'status'=>1])->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='drafts'){
            $data=Post::where(['user_id'=>$user->id,'status'=>0])->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='likes'){
            $data=Like::where('user_id',$user->id)->with('post')->orderBy('liked_at','desc')->paginate(100);
        }


        if($tab=='bookmarks'){
            $data=Bookmark::where('user_id',$user->id)->with('post')->orderBy('bookmarked_at','desc')->paginate(100);
        }

        if($tab=='post_comments'){
            $data=Comment::where('user_id',$user->id)->with('post')->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='opinion_comments'){
            $data=ShortOpinionComment::where('user_id',$user->id)->with('short_opinion')->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='opinion_likes'){
            $data=ShortOpinionLike::where('user_id',$user->id)->with('short_opinion')->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='opinions'){
            $data=ShortOpinion::where('user_id',$user->id)->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='category'){
            $data=CategoryFollower::where('user_id',$user->id)->with('category')->orderBy('created_at','desc')->get();
        }

        if($tab=='threads_liked'){

        }

        if($tab=='threads_followed'){

        }

        if($tab=='followers'){
            $data=Follower::where('leader_id',$user->id)->with('follower')->orderBy('created_at','desc')->paginate(100);
        }

        if($tab=='followings'){
            $data=Follower::where('follower_id',$user->id)->with('leader')->orderBy('created_at','desc')->paginate(100);
        }


        if($tab=='payment'){
            $data=UserAccount::where('user_id',$user->id)->first();
        }

            if($request->has('json') && $request->query('json')==1){
                return response()->json(array('tab'=>$tab,'user'=>$user,'count'=>$count,'data'=>$data));
            }
            return view('admin.dashboard.user.details',compact('tab','user','data','count'));
        }else{
            return view('admin.error.404');
        }
    }

    public function showUserEmailForm(Request $request,$id){
        $user=User::find($id);
        if($user){
            return view('admin.dashboard.user.email',compact('user'));
        }
    }

    public function sendEmailToUser(Request $request){
        return redirect()->route('admin.user_details');
    }

    public function registeredWriters(Request $request){
        $writers= UserAccount::orderBy('created_at','desc')->with('user')->paginate(100);
        if($request->has('json') && $request->query('json')==1){
             return response()->json($writers);
        }
        return view('admin.dashboard.user.writers',compact('writers'));
     }

     public function downloadWriters(Request $request){
        $table = UserAccount::orderBy('created_at','desc')->with('user:id,name')->get();

        if($request->has('format')){
            if($request->query('format')=='csv' || 'excel'){
                $filename = $request->query('format')=='csv' ? "writers.csv":"writers.xls";
                $handle = fopen($filename, 'w+');
                fputcsv($handle, array('user_name','user_email','mobile','account_no','account_holdername','account_type','bank_name','bank_ifsc_code','country','state','address','zip_code','city'));
                foreach($table as $row) {
                    fputcsv($handle, array(
                        $row['user']['name'],
                        $row['user_email'],
                        $row['mobile'],
                        $row['account_no'],
                        $row['account_holdername'],
                        $row['account_type'],
                        $row['bank_name'],
                        $row['bank_ifsc_code'],
                        $row['country'],
                        $row['state'],
                        $row['address'],
                        $row['zip_code'],
                        $row['city']
                    ));
                }
                fclose($handle);
                if($request->query('format')=='csv'){
                    $headers = array('Content-Type' => 'text/csv');
                    return response()->download($filename, 'writers.csv', $headers);
                }
                if($request->query('format')=='excel'){
                    $headers = array('Content-type'=> 'application/vnd.ms-excel');
                    return response()->download($filename,'writers.xls',$headers);
                }
            }

            if($request->query('format')=='json'){
                $filename = "writers.json";
                $handle = fopen($filename, 'w+');
                fputs($handle, $table->toJson(JSON_PRETTY_PRINT));
                fclose($handle);
                $headers = array('Content-type'=> 'application/json');
                return response()->download($filename,'writers.json',$headers);
            }
        }else{
            return redirect()->back();
        }

    }

    public function blockUserAccount(Request $request,$id){
        DB::transaction(function () use($request,$id){

        $user_id=$id;
        $reason=$request->input('reason');
        $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
        $this->maintainPostLikeCount($liked_posts_ids,'decrement');

        $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
        $this->maintainPostViewCount($viewed_posts_ids,'decrement');
        DB::table('user_devices')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('user_contacts')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('likes')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('bookmarks')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('comments')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('short_opinions')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('short_opinion_comments')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('short_opinion_likes')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('poll_results')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('category_followers')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('followers')->where('follower_id', '=',$user_id)->orWhere('leader_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('notifications')->where('notifiable_id',$user_id)->update(['is_active' => 0]);
        DB::table('views')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        $posts=DB::table('posts')->select('id')->where('user_id', '=',$user_id)->get()->pluck('id')->toArray();
        for($i=0;$i<count($posts);$i++){
            DB::table('category_posts')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
            DB::table('post_threads')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
            DB::table('post_keywords')->where('post_id', '=', $posts[$i])->update(['is_active' => 0]);
            $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
            if($opinion)
            {
               DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active' => 0]);
            }
        }
        DB::table('posts')->select('id')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
        DB::table('users')->where('id', '=',$user_id)->update(['is_active' => 0,'blocked_reason'=>$reason]);
        DB::table('sessions')->where('user_id',$user_id)->delete();
        DB::table('push_subscriptions')->where('user_id', '=',$user_id)->delete();
        });
        return redirect()->back();
    }

    public function unblockUserAccount(Request $request,$id){
        DB::transaction(function () use($request,$id){
        $user_id=$id;
        $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
        $this->maintainPostLikeCount($liked_posts_ids,'increment');

        $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
        $this->maintainPostViewCount($viewed_posts_ids,'increment');
        DB::table('user_devices')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('user_contacts')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('likes')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('bookmarks')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('comments')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('short_opinions')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('short_opinion_comments')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('short_opinion_likes')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('poll_results')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('category_followers')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('followers')->where('follower_id', '=',$user_id)->orWhere('leader_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('notifications')->where('notifiable_id',$user_id)->update(['is_active' => 1]);
        DB::table('views')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
       $posts=DB::table('posts')->select('id')->where('user_id', '=',$user_id)->get()->pluck('id')->toArray();
        for($i=0;$i<count($posts);$i++){
            DB::table('category_posts')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);
            DB::table('post_threads')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);
            DB::table('post_keywords')->where('post_id', '=', $posts[$i])->update(['is_active' => 1]);

            $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
            if($opinion)
            {
               DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active' => 1]);
            }
        }
        DB::table('posts')->select('id')->where('user_id', '=',$user_id)->update(['is_active' => 1]);
        DB::table('users')->where('id', '=',$user_id)->update(['is_active' => 1]);
        });
        return redirect()->back();
    }

    public function deleteUserAccount(Request $request,$id){
        DB::transaction(function () use($request,$id){
            $user_id=$id;
            $user=DB::table('users')->where('id',$user_id)->first();
            $liked_posts_ids=DB::table('likes')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
            $this->maintainPostLikeCount($liked_posts_ids,'decrement');

            $viewed_posts_ids=DB::table('views')->select('post_id')->where('user_id', '=',$user_id)->get()->pluck('post_id')->toArray();
            $this->maintainPostViewCount($viewed_posts_ids,'decrement');
            DB::table('user_devices')->where('user_id', '=',$user_id)->delete();
            DB::table('user_contacts')->where('user_id', '=',$user_id)->delete();
            DB::table('push_subscriptions')->where('user_id', '=',$user_id)->delete();
            DB::table('notifications')->where('notifiable_id',$user_id)->delete();
            DB::table('views')->where('user_id', '=',$user_id)->delete();
            DB::table('likes')->where('user_id', '=',$user_id)->delete();
            DB::table('bookmarks')->where('user_id', '=',$user_id)->delete();
            DB::table('comments')->where('user_id', '=',$user_id)->delete();
            DB::table('notifications')->where('notifiable_id',$user_id)->delete();
            DB::table('short_opinion_comments')->where('user_id', '=',$user_id)->delete();
            DB::table('short_opinion_likes')->where('user_id', '=',$user_id)->delete();
            DB::table('poll_results')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
            DB::table('user_earning')->where('user_id', '=',$user_id)->update(['is_active' => 0]);
            DB::table('category_followers')->where('user_id', '=',$user_id)->delete();
            DB::table('followers')->where('follower_id', '=',$user_id)->orWhere('leader_id', '=',$user_id)->delete();
            $posts=DB::table('posts')->select('id')->where('user_id', '=',$user_id)->get()->pluck('id')->toArray();

            for($i=0;$i<count($posts);$i++){
                DB::table('category_posts')->where('post_id', '=', $posts[$i])->delete();
                DB::table('post_threads')->where('post_id', '=', $posts[$i])->delete();
                DB::table('post_keywords')->where('post_id', '=', $posts[$i])->delete();
                $opinion=ShortOpinion::where('post_id', '=', $posts[$i])->first();
                if($opinion)
                {
                DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->delete();
                }
            }
            DB::table('short_opinions')->where('user_id', '=',$user_id)->delete();
            DB::table('posts')->where('user_id', '=',$user_id)->delete();
            DB::table('report_posts')->where('reported_user_id', '=',$user_id)->delete();
            DB::table('password_resets')->where('email', '=',$user->email)->delete();
            DB::table('sessions')->where('user_id', '=',$user_id)->delete();
            DB::table('deleted_users')->insert(get_object_vars($user));
            DB::table('users')->where('id', '=',$user_id)->delete();
        });
        return redirect()->route('admin.users');
    }

    protected function maintainPostLikeCount($postids,$operation){
        foreach($postids as $id){
            $post=Post::find($id);
            if($post){
            $post->likes=$operation=='increment'?$post->likes+1:$post->likes-1;
            $post->save();
            }
        }
    }

    protected function maintainPostViewCount($postids,$operation){
        foreach($postids as $id){
            $post=Post::find($id);
            if($post){
            $post->views=$operation=='increment'?$post->views+1:$post->views-1;
            $post->save();
            }
        }
    }

    protected function saveEmployee(Employee $employee,$name,$email,$phone_code,$mobile,$dateofbirth,$position,$dateofjoin,$is_active){
        $employee->name=$name;
        $employee->email=$email;
        $employee->phone_code= $phone_code;
        $employee->mobile=$mobile;
        $employee->dateofbirth=$dateofbirth;
        $employee->dateofjoin=$dateofjoin;
        //$category->slug= $slug;
        $employee->position=$position;
        //$category->category_group=$group;
        //$employee->image=$imageurl;

        $employee->is_active=$is_active;
        $employee->save();
    }


    public function downloadUsers(Request $request) 
    {   
        $from_d=$request->has('from')?$request->query('from'):Carbon::create(2018, 1, 01, 0, 0, 0);
        $to_d=$request->has('to')?$request->query('to'):Carbon::now()->endOfDay();
        $from = (string) $from_d;
        $to = (string) $to_d;
        $is_active=$request->has('is_active')?$request->query('is_active'):'0,1';
        $email_verified=$request->has('email_verified')?$request->query('email_verified'):'0,1';
        $mobile_verified=$request->has('mobile_verified')?$request->query('mobile_verified'):'0,1';
        $registered_as_writer=$request->has('registered_as_writer')?$request->query('registered_as_writer'):'0,1';
        $register_provider=$request->has('provider')?$request->query('provider'):'email,facebook,google,twitter,linkedin';
        $platform=$request->has('platform')?$request->query('platform'):'website,android';


        $sortBy=$request->has('sortBy')?$request->query('sortBy'):'created_at';
        $sortOrder=$request->has('sortOrder')?$request->query('sortOrder'):'desc';

        $limit=$request->has('limit')?$request->query('limit'):24;
        $page=$request->has('page')?$request->query('page'):1;

        $searchQuery=$request->has('searchQuery') && strlen(trim($request->input('searchQuery')))>0 ? trim($request->input('searchQuery')):'';
        $searchBy=$request->has('searchBy')?$request->input('searchBy'):'name';
        $DBsearchQuery=$searchBy=='id'? $searchQuery:'%'.$searchQuery.'%';

       // return Excel::download(new UsersExport, 'users.xlsx', compact('from','to','is_active','email_verified','mobile_verified','registered_as_writer','register_provider','platform','sortBy','sortOrder','limit','page','searchQuery','searchBy','DBsearchQuery'));
       return (new UsersExport)->forUser($from,$to, $is_active, $email_verified, $mobile_verified, $registered_as_writer, $register_provider, $platform, $sortBy, $sortOrder, $limit, $page, $searchQuery, $searchBy, $DBsearchQuery)->download('users-'.date("d_m_y-H:i:s").'.xlsx');
    }

    public function downloadDeleted(Request $request) 
    {   
        return (new DeletedUserExport)->forUser()->download('deleted_users-'.date("d_m_y-H:i:s").'.xlsx');
    }
}
