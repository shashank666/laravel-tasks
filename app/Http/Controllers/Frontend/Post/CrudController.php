<?php

namespace App\Http\Controllers\Frontend\Post;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Image;
use File;
use Illuminate\Support\Arr;
use App\Model\Category;
use App\Model\Thread;
use App\Model\Keyword;

use App\Model\CategoryThread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\ThreadOpinion;
use App\Model\Post;
use App\Model\OfferPost;
use App\Model\PostThreads;
use App\Model\ReportPost;
use App\Model\Comment;
use App\Model\Bookmark;
use App\Model\RsmOffer;
use App\Model\Like;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\Follower;
use App\Events\PostViewCounterEvent;
use Notification;
use App\Notifications\Frontend\PostCreated;
use App\Jobs\AndroidPush\PostCreatedJob;
use DB;
use Carbon\Carbon;
use Mail;
use App\Mail\Post\PostAppriciationMail;
use App\Mail\OfferPost\OfferMail;
use App\Mail\OfferPost\RegisterWriterMail;
use App\Mail\OfferPost\WordcountMail;
use App\Jobs\Post\PostCreatedMailJob;
use App\Http\Helpers\MailJetHelper;

class CrudController extends Controller
{
   public $categories,$threads;

   public function __construct()
   {

       $post=new Post();
       $latest_posts=$post->get_latest();

       $thread=new Thread();
       $this->threads=$thread->latest_threads();
       $google_ad=DB::table('google_ads')->where(['id'=>1,'is_active'=>1])->first();
       View::share('google_ad',$google_ad);
       View::share('threads',$this->threads);
       View::share('latest_posts',$latest_posts);
       $this->middleware('auth',['except'=>['show','get_user_bookmark_postids','get_user_liked_postids']]);
   }

    public function get_posts_likes(Request $request,$slug){
        $post=Post::where('slug',$slug)->where(['status'=>1,'is_active'=>1])->first();
        $post_likes=Like::where('post_id',$post->id)
        ->where('is_active',1)
        ->with('user')
        ->orderBy('liked_at','desc')
        ->paginate(20);
        if($post_likes->total()>0){
            $followingids=Auth::guest()?[]:auth()->user()->active_followings->pluck('id')->toArray();
                $output='';
                $output_li='';
                for($i=0;$i<count($post_likes);$i++){
                    if(!empty($post_likes[$i]->user["username"]) && !empty($post_likes[$i]->user["unique_id"])){
                        $start='<li class="list-group-item">'.
                        '<div class="media align-items-center mb-2">'.
                                '<a class="mr-3" href="/@'.$post_likes[$i]->user["username"].'"><img class="rounded-circle" src="'.$post_likes[$i]->user["image"].'" height="50" width="50" alt="Go to the profile of '.$post_likes[$i]->user["name"].'"  onerror="this.onerror=null;this.src="/img/avatar.png";"></a>'.
                                '<div class="media-body">'.
                                    '<div class="d-flex justify-content-between align-items-center w-100">'.
                                            '<a  href="/@'.$post_likes[$i]->user["username"].'" style="color:#212121;">'.ucfirst($post_likes[$i]->user["name"]).'</a>';
                                            if($post_likes[$i]->user["id"]!=Auth::user()->id){
                                                if(!in_array($post_likes[$i]->user["id"],$followingids)){
                                                    $middle='<button data-userid="'.$post_likes[$i]->user["id"].'" class="followbtn followbtn_'.$post_likes[$i]->user["id"].' btn btn-sm btn-outline-success" style="display:block">Add To Circle <span><i class="fas fa-user-plus ml-2"></i><span></button>'.
                                                    '<button  data-userid="'.$post_likes[$i]->user["id"].'" class="followingbtn followingbtn_'.$post_likes[$i]->user["id"].' btn btn-sm btn-success" style="display:none">In Your Circle <span><i class="fas fa-check ml-2"></i><span></button>';
                                                }else{
                                                    $middle='<button  data-userid="'.$post_likes[$i]->user["id"].'" class="followbtn followbtn_'.$post_likes[$i]->user["id"].' btn btn-sm btn-outline-success" style="display:none">Add To Circle <span><i class="fas fa-user-plus ml-2"></i><span></button>'.
                                                    '<button  data-userid="'.$post_likes[$i]->user["id"].'" class="followingbtn followingbtn_'.$post_likes[$i]->user["id"].' btn btn-sm btn-success" style="display:block">In Your Circle <span><i class="fas fa-check ml-2"></i><span></button>';
                                                }
                                        }else{
                                            $middle='';
                                        }

                            $end='</div>'.
                                '</div>'   .
                        '</div>'.
                        '</li>';

                        $li=$start.$middle.$end;
                        $output_li=$output_li.$li;
                    }
                }

                $ul='<ul class="list-group list-group-flush">'.$output_li.'</ul>';
                if($post_likes->nextPageUrl()==null){
                    $button='';
                    $output=$output_li.$button;
                }else{
                    $nextpage=$post_likes->currentPage()+1;
                    $button=' <button class="btn btn-sm btn-primary btn-block loadmore_likes" data-nextpage="'. $nextpage.'">Load More</button>';
                    $output=$output_li.$button;
                }

            echo $output;
        }else{
            $output='<ul class="list-group list-group-flush"><li class="list-group-item"><p>No one liked this opinion yet.</p></li></ul>';
            echo $output;
        }
    }


    // function to display create post page
    public function create()
    {
        $rsm_offer=RsmOffer::where('user_id',Auth::user()->id)->get();

        if(Auth::user()->email_verified==1){
            if(count($rsm_offer)>0){
            $flag = 1;
            return view('frontend.posts.crud.create',compact('flag'));
        }else{
        $flag = 0;
            return view('frontend.posts.crud.create',compact('flag'));
        }
    }
        else{
            return redirect('/');
        }
    }

    public function autosave(Request $request)
    {

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->input('title'))));
        if($request->input('postId')!=''){
            DB::table('posts')->where('id',$request->input('postId'))->update(['title' => $request->input('title'),'coverimage' => $request->input('coverimageurl'), 'body'=>$request->input('body'),'plainbody'=>$request->input('plainbody'),'slug'=>$slug]);

        }
        else{
        $post=new Post();
            $post=$this->save_post($post,
                            $request->input('title'),
                            uniqid(),
                            $request->input('coverimageurl'),
                            $request->input('body'),
                            $request->input('plainbody'),
                            $request->input('status'),
                            $request->input('categories'));
            

        return response()->json(array('status'=>'success','postId'=>$post->id));
        /*return view('frontend.posts.crud.create', compact('users'));*/
           }           
                     
                          
    }

    
    // function to save post
    public function store(Request $request)
    {
            if($request->input('post_id')!=''){
            DB::table('posts')->where('id',$request->input('post_id'))->delete();
        }
            $this->validate($request,[
                'title'=>'required',
                'categories'=>'required'
            ]);

            $post=new Post();
            $post=$this->save_post($post,
                            $request->input('title'),
                            uniqid(),
                            $request->input('coverimageurl'),
                            $request->input('body'),
                            $request->input('plainbody'),
                            $request->input('status'),
                            $request->input('categories'));

            $opinionThreads=[];
            if($request->has('hidden-tags') && strlen($request->input('hidden-tags'))>0){
                $opinionThreads=$this->create_threads($post,$request->input('categories'),$request->input('hidden-tags'));
            }

            if($request->has('hidden-keywords') && strlen($request->input('hidden-keywords'))>0){
                $postKeywords=$this->create_keywords($post,$request->input('hidden-keywords'));
            }


            if($request->input('status')=='0'){

                
                //$slug = $request->input('slug');
                 //return response()->json([
    //'message' => 'Success',
    //'slug' => $fullslug
//]);
                //return redirect('/opinion/{{$slug}}');
                //return view('frontend.posts.components.blog',compact('slug'));
                return redirect('/me/myallarticles')->with(['post'=>$post,'status'=>'draft','statusText'=>'Your Opinion has been saved as Draft']);
            }else if($request->input('status')=='2'){
                $slug = $post->slug;
                if(count($opinionThreads)>0){
                $opinion=new ShortOpinion();
                $this->dummy_opinion_from_post($request->getSchemeAndHttpHost(),$post, Auth::user()->id,$opinion,uniqid(),$opinionThreads,'create');
                }
                return redirect()->route('blog_post_ready', ['slug' => $slug]);
            }

            else{
                if(count($opinionThreads)>0 && $request->input('status')=='1'){
                $opinion=new ShortOpinion();
                $this->create_opinion_from_post($request->getSchemeAndHttpHost(),$post, Auth::user()->id,$opinion,uniqid(),$opinionThreads,'create');
                }

                $this->send_appriciation_email_to_user($post);
               /*  OFFER EMAIL FUNCTION CALLS

                $this->send_email_to_user();

                if(str_word_count($post->plainbody)<400){
                    $this->send_email_for_post_wordcount($post);
                }

                if(Auth::user()->registered_as_writer==0){
                    $this->send_email_for_register_as_writer($post);
                }
                */

                $this->notify_followers($post,'create');
                return redirect('/opinion/write')->with(['post'=>$post,'status'=>'published','statusText'=>'Your Opinion has been successfully published']);
            }
    }

    public function upload(Request $request)
    {
    //var_dump($_FILES['files']);

    $image = $_FILES['files']['tmp_name'][0]; 
    $ext = preg_match('/\./', $_FILES['files']['name'][0]) ? preg_replace('/^.*\./', '', $_FILES['files']['name'][0]) : '';

    //var_dump($ext);
    $png_url = "opined-".rand()."-".time().".".$ext;
    $path = public_path().'/storage/cover/' . $png_url;
    $url = '/storage/cover/' . $png_url;
    Image::make(file_get_contents($image))->save($path);   
   // echo url($url);
//    Storage::disk('s3')->put($path,(string)$thumb_opinion,'public');

    $files[] = array('name' => $_FILES['files']['name'][0],'size' => $_FILES['files']['size'][0],'type' => $_FILES['files']['type'][0], 'url' => $url);
    $response = array('files' => $files);
    return response()->json($response);
    //return response()->json(array('status'=>'success','message'=>'Image successfully uploaded','upload_url'=>$path));

/*
        $data = Input::all();
$png_url = "perfil-".time().".jpg";
$path = public_path() . "/img/" . $png_url;
$img = $data['files'];

$data = base64_decode($img);
$success = file_put_contents($path, $data);
print $success ? $png_url : 'Unable to save the file.';
       
*/
       /* $data = Input::all();
        var_dump($data);
    $png_url = "opined-".time().".png";
    $path = public_path().'img/' . $png_url;

    Image::make(file_get_contents($data->image))->save($path);     
    $response = array(
        'status' => 'success',
    );
    return Response::json( $response  );

    $file = base64_decode($request['image']);
        $folderName = 'public/img/';
        $safeName = str::random(10).'.'.'png';
        $destinationPath = public_path() . $folderName;
        $success = file_put_contents(public_path().'/img/'.$safeName, $file);
        print $success;
         
           

$file = $_FILES["files"];
        var_dump($file);

        $input = array('image' => $file);
        var_dump($input);
        $rules = array(
            'type' => 'mimes:jpeg,jpg,png,gif'
        );
        $validator = Validator::make($input, $rules);
        if ( $validator->fails()) {
            return Response::json(array('success' => false, 'errors' => $validator->getMessageBag()->toArray()));
        }

        $fileName = time() . '-' . implode("",$_FILES["files"]["name"]);
        var_dump($fileName);
        $destination = public_path('/storage/cover/');
        $request->file('file')->move($destination, $fileName);

        echo url('/storage/cover/'. $fileName);
*/
       // $inputName='files[]';
        //$inputName = $request->file('file');
        /*$file = Input::file('file');

        var_dump($file);
        $input = array('image' => $file);
        $rules = array(
            'image' => 'mimes:jpeg,jpg,png,gif'
        );
        $validator = Validator::make($input, $rules);
        if ( $validator->fails()) {
            return Response::json(array('success' => false, 'errors' => $validator->getMessageBag()->toArray()));
        }

        $fileName = time() . '-' . $file->getClientOriginalName();
        $destination = public_path('/storage/cover');
        $file->move($destination, $fileName);

        echo url('/storage/cover'. $fileName);*/
    }

    public function publish(Request $request){
        $slug = $_GET["slug"];
        DB::table('posts')->where('slug',$slug)->update(['status' => 1]);
        $post = DB::table('posts')->where('slug',$slug)->first();
        $user= User::where('id',$post->user_id)->first();
        //return response()->json(['message' => 'Success','slug' => $post->id]);
        DB::table('category_posts')->where('post_id', '=', $post->id)->update(['is_active' => 1]);
       DB::table('post_threads')->where('post_id', '=', $post->id)->update(['is_active' => 1]);
       DB::table('post_keywords')->where('post_id', '=', $post->id)->update(['is_active' => 1]);
        $opinion=ShortOpinion::where('post_id', '=', $post->id)->first();
                        if($opinion)
                        {
                            $opinion->is_active = 1;
                            $opinion->save();
                            DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active'=>1]);
                            DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->update(['is_active'=>1]);
                            $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->get();
                            foreach($opinion_comments as $op_comment){
                                DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>1]);
                            }
                            DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->update(['is_active'=>1]);
                            DB::table('notifications')
                            ->where('data','like','%"event":"OPINION_LIKED"%')
                            ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                            ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                            ->delete();
                        }
        try{
            //Mail::send(new AccountCreatedMail($user,$user->verify_token));
            $mailJET=new MailJetHelper();
            $mailJET->send_post_inform_mail($user,$post);
        }
        catch(\Exception $e){}
         
        return redirect()->route('blog_post', ['slug' => $slug]);
    }
    // function to show edit post page
    public function edit($slug)
    {
        $post=Post::where(['slug'=>$slug,'is_active'=>1])->with('categories:id','threads:name')->first();
        if($post){
              if(auth()->user()->id!=$post->user_id) { return redirect('/'); }
              else{
                $post->categoryids=$post->categories->pluck('category_id')->toArray();
                $post->threadnames = implode(',',$post->threads->pluck('name')->toArray());
               return view('frontend.posts.crud.update')->with('post',$post);
              }
        }else{
            abort(404);
        }
    }


    // function to update post
    public function update(Request $request)
    {
        if($request->input('post_id')!=''){
            DB::table('posts')->where('id',$request->input('post_id'))->delete();
        }
          $this->validate($request,[
            'title'=>'required'
        ]);

        $post=Post::where(['slug'=>$request->input('slug'),'user_id'=>auth()->user()->id,'is_active'=>1])->first();
        $old_post=$post;
        $uniqueid=$post->uuid;

        if(strcmp($old_post->plainbody,$request->input('plainbody'))!=0){
            $update=Post::where('id',$post->id)->update(['plagiarism_checked'=>0]);
        }

        $newPost=$this->save_post($post,
                        $request->input('title'),
                        $uniqueid,
                        $request->input('coverimageurl'),
                        $request->input('body'),
                        $request->input('plainbody'),
                        $request->input('status'),
                        $request->input('categories'));

        $opinionThreads=[];
        if($request->has('hidden-tags') && strlen($request->input('hidden-tags'))>0){
            $opinionThreads=$this->create_threads($newPost,$request->input('categories'),$request->input('hidden-tags'));
        }else{
            DB::table('post_threads')->where('post_id', '=', $newPost->id)->delete();
        }

        if($request->has('hidden-keywords') && strlen($request->input('hidden-keywords'))>0){
            $postKeywords=$this->create_keywords($post,$request->input('hidden-keywords'));
        }else{
            DB::table('post_keywords')->where('post_id', '=', $newPost->id)->delete();
        }

        $status=$request->input('status');
        $slugtemp = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $newPost->title)));
        $posts  = Post::whereRaw("slug REGEXP '^{$slugtemp}([0-9]*)?$'")->get();
        $count = count($posts);
        if($count > 1){
            $slug = $slugtemp.$count;
            }
        else{
            $slug = $slugtemp;
        }
         
        $opinion=ShortOpinion::where('post_id',$newPost->id)->first();
        if($opinion){
            $this->create_opinion_from_post($request->getSchemeAndHttpHost(),$newPost, Auth::user()->id,$opinion,$opinion->uuid,$opinionThreads,$slug,'update');
        }else{
            if(count($opinionThreads)>0){
            $opinion=new ShortOpinion();
            $this->create_opinion_from_post($request->getSchemeAndHttpHost(),$newPost, Auth::user()->id,$opinion,uniqid(),$opinionThreads,$slug,'create');
            }
        }

        
        DB::table('posts')->where('id', '=', $post->id)->update(['slug' => $slug]);
        if($status=='0'){
            $this->isActiveModeChanger($post->id,$status,'draft');
            return redirect('/me/myallarticles')->with(['post'=>$newPost,'status'=>'draft','statusText'=>'Your Opinion has been saved as Draft']);
        }
        else if($status =='2'){
                
                $this->isActiveModeChanger($post->id,$status,'priview');
                return redirect()->route('blog_post_ready', ['slug' => $slug]);
            }
            else{
            $this->isActiveModeChanger($post->id,$status,'published');

            /* OFFER EMAIL FUNCTION CALLS
            $this->send_email_to_user();
            if(str_word_count($post->plainbody)<400){
                $this->send_email_for_post_wordcount($post);
            }
            if(Auth::user()->registered_as_writer==0){
                $this->send_email_for_register_as_writer($post);
            }
            */

            //$this->notify_followers($newPost,'update');
            return redirect('/opinion/edit/'.$newPost->slug)->with(['post'=>$newPost,'status'=>'published','statusText'=>'Your Opinion has been successfully published']);
        }
    }

     // function to delete post by post id
     public function destroy(Request $request)
     {
            $post=Post::where('slug',$request->input('deleteid'))->first();
            if($post && $post->user_id==auth()->user()->id)
            {
                DB::transaction(function () use($post){

                    DB::table('posts')->where('id', '=', $post->id)->update(['is_active' => 0,'is_monetised'=>0]);
                    DB::table('category_posts')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('post_threads')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('post_keywords')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('shares')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('likes')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('views')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('bookmarks')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('monetisation')->where('post_id', '=', $post->id)->update(['is_monetised' => 0]);
                    $comments=DB::table('comments')->where('post_id', '=', $post->id)->get();
                    foreach($comments as $comment){
                        DB::table('comments_likes')->where('comment_id',$comment->id)->update(['is_active' => 0]);
                    }
                    DB::table('comments')->where('post_id', '=', $post->id)->update(['is_active' =>0]);
                    DB::table('report_posts')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                    DB::table('notifications')
                    ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
                    ->orWhere('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                    ->orWhere('data','like','%"event":"ARTICLE_LIKED"%')
                    ->where('data','like','%"post_id":'.$post->id.'%')
                    ->delete();

                    $opinion=ShortOpinion::where('post_id', '=', $post->id)->first();
                        if($opinion)
                        {
                            $opinion->is_active = 0;
                            $opinion->save();
                            DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                            DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                            $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->get();
                            foreach($opinion_comments as $op_comment){
                                DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>0]);
                            }
                            DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                            DB::table('shares')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                            DB::table('notifications')
                            ->where('data','like','%"event":"OPINION_LIKED"%')
                            ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                            ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                            ->delete();
                        }
                    });

                   if($request->ajax()){
                         return response()->json(array('status'=>'success','message'=>'post deleted'));
                   }else{
                     return redirect('/me/opinions');
                   }

            }
            else{
                 return redirect('/');
            }
     }

     // function to show post by post slug
     public function show($slug,Request $request)
     {
        $google_ad1=DB::table('google_ads')->where(['id'=>5,'is_active'=>1])->first();

        $old_slug=explode('-',$slug);
        array_pop($old_slug);
        $new_slug=implode('-',$old_slug);

        $post=Post::where(['slug'=>$slug,'status'=>1,'is_active'=>1])->orWhere(['slug'=>$new_slug])->with('user','categories','threads','keywords')->first();

        /*  OFFER COUNT
        $eligible_count=OfferPost::count();
        $remaining_count=100-$eligible_count;
        */

        if($post){
             $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
             event(new PostViewCounterEvent($post,$ip_address));
             $more_posts_by_category=[];
             $categories=$post->categories;
             foreach($categories as $index=>$category){
                 $category['top_5_posts']=$category->suggestions();
                 array_push($more_posts_by_category,$category);
             }
                
             $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
             $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
             $followingids=Auth::check()?auth()->user()->active_followings->pluck('id')->toArray():[];

             return view('frontend.posts.crud.read',compact('google_ad1','followingids','liked_posts','bookmarked_posts','post','more_posts_by_category'));
         }else{
             abort(404);
         }
     }

      // function to show post by post slug
     public function showReady($slug,Request $request)
     {
        $google_ad1=DB::table('google_ads')->where(['id'=>5,'is_active'=>1])->first();

        $old_slug=explode('-',$slug);
        array_pop($old_slug);
        $new_slug=implode('-',$old_slug);

        $post=Post::where(['slug'=>$slug,'status'=>2,'is_active'=>1])->orWhere(['slug'=>$new_slug])->with('user','categories','threads','keywords')->first();

        /*  OFFER COUNT
        $eligible_count=OfferPost::count();
        $remaining_count=100-$eligible_count;
        */

        if($post){
             $ip_address=isset($_SERVER["HTTP_CF_CONNECTING_IP"])?$_SERVER["HTTP_CF_CONNECTING_IP"]:$request->ip();
             event(new PostViewCounterEvent($post,$ip_address));
             $more_posts_by_category=[];
             $categories=$post->categories;
             foreach($categories as $index=>$category){
                 $category['top_5_posts']=$category->suggestions();
                 array_push($more_posts_by_category,$category);
             }
                
             $bookmarked_posts=Auth::check()?$this->my_bookmarked_postids(Auth::user()->id):[];
             $liked_posts=Auth::check()?$this->my_liked_postids(Auth::user()->id):[];
             $followingids=Auth::check()?auth()->user()->active_followings->pluck('id')->toArray():[];

             return view('frontend.posts.crud.ready_to_publish',compact('google_ad1','followingids','liked_posts','bookmarked_posts','post','more_posts_by_category'));
         }else{
             abort(404);
         }
     }


    //function for report post
    public function report(Request $request){
        $flag_reason=explode("--",$request->input('flag'));
        $report_post=new ReportPost();
        $report_post->post_id=$request->input('reportpost');
		$report_post->reported_user_id=auth()->user()->id;
	    $report_post->report_flag=(int)$flag_reason[0];
		$report_post->report_reason=$flag_reason[1];
        $report_post->save();
        return redirect()->back();
    }



    // function for saving post data in posts table
    protected function save_post($post,$title,$uniqueid,$coverimage,$body,$plain,$status,$categories)
    {
        $totalLength=strlen($plain);
        $readtime=1+round($totalLength/375);
        $slug = $this->create_slug($title);
        $post->title=$title;
        $post->slug=$slug;
        $post->uuid=$uniqueid;
        $post->coverimage=$coverimage;
        $post->body=$body;
        $post->plainbody=$plain;
        $post->user_id=auth()->user()->id;
        $post->status=$status;
        $post->readtime=$readtime;
        $post->save();
        $post->categories()->sync($categories);
        return $post;
    }


    protected function create_opinion_from_post($host,$post,$user_id,$opinion,$uniqueid,$opinionThreads,$slug,$event)
    {
        $body='';
        foreach($opinionThreads as $thread){
           $body=$body.' <a href="https://weopined.com/thread/'.$thread->name.'" class="thread_link">#'.$thread->name.'</a>';
        }

       $links=array();
       $info['status']='OK';
       $info['title']=$post->title;
       $info['description']=str::limit($post->plainbody,40,'...');
       $info['url']=$host.'/opinion/'.$slug;
       $info['type']='link';
       $info['image']=$post->coverimage;
       $info['providerName']='Opined';
       $info['providerUrl']='https://www.weopined.com';
       $info['providerIcon']='https://www.weopined.com/favicon.png';
       array_push($links,$info);

       $opinion->uuid=$uniqueid;
       $opinion->body=$body;
       $opinion->cover=NULL;
       $opinion->cover_type='none';
       $opinion->links=json_encode($links);
       $opinion->user_id=$user_id;
       $opinion->type="post";
       $opinion->post_id=$post->id;
       $opinion->is_active=1;
       $opinion->save();

       if(count($opinionThreads)>0){
           $threadIds=Arr::pluck($opinionThreads, 'id');
           $opinion->threads()->sync($threadIds);
       }

    }

protected function dummy_opinion_from_post($host,$post,$user_id,$opinion,$uniqueid,$opinionThreads,$event)
    {
        $body='';
        foreach($opinionThreads as $thread){
           $body=$body.' <a href="https://weopined.com/thread/'.$thread->name.'" class="thread_link">#'.$thread->name.'</a>';
        }

       $links=array();
       $info['status']='OK';
       $info['title']=$post->title;
       $info['description']=str::limit($post->plainbody,40,'...');
       $info['url']=$host.'/opinion/'.$post->slug;
       $info['type']='link';
       $info['image']=$post->coverimage;
       $info['providerName']='Opined';
       $info['providerUrl']='https://www.weopined.com';
       $info['providerIcon']='https://www.weopined.com/favicon.png';
       array_push($links,$info);

       $opinion->uuid=$uniqueid;
       $opinion->body=$body;
       $opinion->cover=NULL;
       $opinion->cover_type='none';
       $opinion->links=json_encode($links);
       $opinion->user_id=$user_id;
       $opinion->type="post";
       $opinion->post_id=$post->id;
       $opinion->is_active=2;
       $opinion->save();

       if(count($opinionThreads)>0){
           $threadIds=Arr::pluck($opinionThreads, 'id');
           $opinion->threads()->sync($threadIds);
       }

    }
    protected function create_threads($post,$categories,$threads)
    {
       $opinionThreads=[];

       $threads=explode(' ',$threads);
       if(count($threads)>0){
           for($i=0;$i<count($threads);$i++){
               $threads[$i]=ltrim($threads[$i],'#');
               if(strlen($threads[$i])>2){
                $threadSlug=str::slug($threads[$i]);
                $threadFound=Thread::whereRaw('LOWER(`name`) = ?',[trim(strtolower($threads[$i]))])->first();
                if($threadFound){
                    array_push($opinionThreads,$threadFound);
                    $ThreadId=$threadFound->id;
                    $threadFound->categories()->syncWithoutDetaching($categories);
                }else{
                    $threadCreate=Thread::create(['name'=>$threads[$i],'slug'=>$threadSlug]);
                    $ThreadId=$threadCreate->id;
                    $thread=Thread::find($ThreadId);
                    $thread->categories()->sync($categories);
                    array_push($opinionThreads,$threadCreate);
                }
               }
           }
           $threadIds=Arr::pluck($opinionThreads, 'id');
           $post->threads()->sync($threadIds);
           return $opinionThreads;
       }
    }

    protected function create_keywords($post,$keywords){
        $postKeywords=[];
        $keywords=explode(',',$keywords);
        if(count($keywords)>0){
            for($i=0;$i<count($keywords);$i++){
                $keywords[$i]=ltrim($keywords[$i],'#');
                $keywordSlug=str::slug($keywords[$i]);
                $keywordFound=Keyword::whereRaw('LOWER(`name`) = ?',[trim(strtolower($keywords[$i]))])->first();
                if($keywordFound){
                    array_push($postKeywords,$keywordFound);
                    $KeywordId=$keywordFound->id;
                }else{
                    $keywordCreate=Keyword::create(['name'=>$keywords[$i],'slug'=>$keywordSlug]);
                    $KeywordId=$keywordCreate->id;
                    array_push($postKeywords,$keywordCreate);
                }
            }
            $keywordIds=Arr::pluck($postKeywords, 'id');
            $post->keywords()->sync($keywordIds);
            return $postKeywords;
        }
    }

    protected function isActiveModeChanger($post_id,$is_active,$event){
        DB::transaction(function () use($post_id,$is_active,$event){

               DB::table('category_posts')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('post_threads')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('post_keywords')->where('post_id', '=', $post_id)->update(['is_active' =>$is_active]);

               DB::table('shares')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('likes')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('views')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('bookmarks')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               $comments=DB::table('comments')->where('post_id', '=', $post_id)->get();
               foreach($comments as $comment){
                    DB::table('comments_likes')->where('comment_id',$comment->id)->update(['is_active' => $is_active]);
               }
               DB::table('comments')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
               DB::table('report_posts')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);

               DB::table('notifications')
                ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
                ->orWhere('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                ->orWhere('data','like','%"event":"ARTICLE_LIKED"%')
                ->where('data','like','%"post_id":'.$post_id.'%')
                ->update(['is_active' => $is_active]);

                $opinion=ShortOpinion::where('post_id', '=', $post_id)->first();
                if($opinion){
                   $opinion->is_active = $is_active;
                   $opinion->save();
                   DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                   DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                   $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->get();
                    foreach($opinion_comments as $op_comment){
                         DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>$is_active]);
                    }
                   DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                   DB::table('shares')->where('short_opinion_id',$opinion->id)->update(['is_active'=>$is_active]);
                   DB::table('notifications')
                   ->where('data','like','%"event":"OPINION_LIKED"%')
                   ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                   ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                   ->update(['is_active'=>$is_active]);
                  }
        });
    }


     protected function notify_followers($post,$event)
     {
        $followers=auth()->user()->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];

        $this->send_email_to_followers($post,Auth::user(),$followers);
        if($event=='update'){
            DB::table('notifications')
            ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
            ->where('data','like','%"post_id":'.$post->id.'%')
            ->delete();
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new PostCreatedJob($post,Auth::user(),$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new PostCreated($post,Auth::user(),$fcm_tokens));
            }
        }catch(\Exception $e){}

     }


    // function for send email to
    protected function send_email_to_followers($post,$postauthor,$followers){
        if(count($followers)>0){
            $job=(new PostCreatedMailJob($post,$postauthor,$followers))->delay(Carbon::now()->addMinutes(2));
            $jobdone=dispatch($job);
        }
    }

     // function for send email to user (post author) , after published post article
     protected function send_appriciation_email_to_user($post){
        try{
             //Mail::send(new PostAppriciationMail(Auth::user(),$post));
             $mailJET=new MailJetHelper();
             $mailJET->send_post_appriciation_mail(Auth::user(),$post);
        }
        catch(\Exception $e){}
    }

/**********************************************************************************************************/
    /* EMAIL FUNCTIONS RELATED TO OFFER POST (CURRENTLY NOT IN USE)*/
/**********************************************************************************************************/

    // function for send email to user (post author) , after published post article
    protected function send_email_to_user(){
        try{  Mail::send(new OfferMail(Auth::user()));}
        catch(\Exception $e){}
    }

    // function for send email to user(post author) who is not registered as writer yet.
    protected function send_email_for_register_as_writer(Post $post){
        try{  Mail::send(new RegisterWriterMail(Auth::user(),$post));}
        catch(\Exception $e){}
    }

    // function for send email to  user(post author) , if published post has word count less than 400
    protected function send_email_for_post_wordcount(Post $post){
        try{  Mail::send(new WordcountMail(Auth::user(),$post));}
        catch(\Exception $e){}
    }

/**********************************************************************************************************/

}
