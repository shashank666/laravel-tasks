<?php

namespace App\Http\Controllers\Api\Post;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\Post;
use App\Model\Comment;
use App\Model\Thread;
use App\Model\Keyword;
use App\Model\User;
use App\Model\UserDevice;
use App\Model\ShortOpinion;
use App\Model\ReportPost;
use App\Model\FileManager;
use App\Model\Like;

use App\Events\PostViewCounterEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Arr;
use Carbon\Carbon;
use DB;
use Mail;
use App\Mail\Post\PostAppriciationMail;
use App\Mail\OfferPost\OfferMail;
use App\Mail\OfferPost\RegisterWriterMail;
use App\Mail\OfferPost\WordcountMail;
use App\Mail\Post\PostCreatedMail;

use Notification;
use App\Notifications\Frontend\PostCreated;
use App\Jobs\AndroidPush\PostCreatedJob;

use App\Http\Helpers\MailJetHelper;
use ImageOptimizer;
use App\Jobs\Resize\ResizeImageJob;
use Illuminate\Contracts\Bus\Dispatcher;

class CrudController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['read']]);
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'title'=>'required',
                'categories'=>'required',
                'status'=>'required',
                'cover'=>'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                if($request->hasFile('cover')){
                    $uniqueid=uniqid();
                    $original_name=$request->file('cover')->getClientOriginalName();
                    $size=$request->file('cover')->getSize();
                    $extension=$request->file('cover')->getClientOriginalExtension();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                    $imagepath=url('/storage/cover/'.$filename);
                    $path=$request->file('cover')->storeAs('public/cover',$filename);
                    $size=$this->optimize_image($extension,'cover',$filename,$size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COVER',$size,$extension,Auth::user()->user_id);
                    $request->request->add(['coverimage' => $imagepath]);
                    try{
                        $job = (new ResizeImageJob(storage_path('app/public/cover/'.$filename),storage_path('app/public/cover/'),[[350, 250]]))->onQueue('default');
                        app(Dispatcher::class)->dispatch($job);
                    }catch(\Exception $e){}
                }else{
                    $request->request->add(['coverimage' => $request->input('imageurl')]);
                }

                $post=$this->save_post(new Post(),$request->all(),uniqid());
                $this->remove_null($post);

                $opinionThreads=[];
                if($request->has('tags') && strlen($request->input('tags'))>0){
                    $opinionThreads=$this->create_threads($post,$request->input('categories'),$request->input('tags'));
                }

                if($request->has('keywords') && strlen($request->input('keywords'))>0){
                    $postKeywords=$this->create_keywords($post,$request->input('keywords'));
                }

                if($post->status=='0'){
                    $response=array('status'=>'success','result'=>1,'message'=>'Draft saved','post'=>$post);
                    return response()->json($response,200);
                }else{

                    if(count($opinionThreads)>0){
                    $opinion=new ShortOpinion();
                    $this->create_opinion_from_post($post,Auth::user()->user_id,$opinion,uniqid(),$opinionThreads,'create');
                    }

                    $this->send_appriciation_email_to_user($post);
                    /* OFFER EMAIL FUNCTION CALLS

                     $this->send_email_to_user();
                        if(str_word_count($post->plainbody)<400){
                            $this->send_email_for_post_wordcount($post);
                        }
                        if(Auth::user()->user->registered_as_writer==0){
                            $this->send_email_for_register_as_writer($post);
                        }
                    */

                    $this->notify_followers($post,'create');

                    $response=array('status'=>'success','result'=>1,'message'=>'Article published','post'=>$post);
                    return response()->json($response,200);
                }
           }
         }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function read(Request $request,$id){
       try{
            $user_id=-1;
            $my_liked_postids=[];
            $my_bookmarked_postids=[];

            if($request->header('Authorization')){
                $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                $my_liked_postids=$this->my_liked_postids($user_id);
                $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
            }

            $post=Post::where(['id'=>$id,'status'=>1,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name','keywords:id,name')->first();
            if(!empty($post)){
                event(new PostViewCounterEvent($post,$request->ip()));
                $formatted_post=$this->formatted_post($post,$my_liked_postids,$my_bookmarked_postids);
                if($user_id>0){
                    $followers=User::find($user_id)->active_followings->pluck('id')->toArray();
                    $post_user_id=$post->user['id'];
                    $is_followed=in_array($post_user_id,$followers)?1:0;
                    $formatted_post['user']['is_followed']=$is_followed;
                }else{
                    $is_followed=0;
                    $formatted_post['user']['is_followed']=0;
                }

                $comments=Comment::where(['parent_id'=>0,'is_active'=>1,'post_id'=>$post->id])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(4)->get();
                foreach($comments as $comment){
                    $this->remove_null($comment);
                    unset($comment['replies']);
                }
                $share_urls=array(
                    'twitter'=>"https://twitter.com/share?text=".$post->title."&url=https://www.weopined.com/opinion/".$post->slug,
                    'facebook'=>"https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/".$post->slug,
                    'linkedin'=>"https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/".$post->slug,
                    'url'=>"https://www.weopined.com/opinion/".$post->slug
                );
                $formatted_post['updated_at'] = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                $response=array('status'=>'success','result'=>1,'post'=>$formatted_post,'comments'=>$comments,'share_urls'=>$share_urls);
                return response()->json($response,200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                return response()->json($response,200);
            }
         }
         catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function read_by_slug(Request $request,$slug){
        try{
             $user_id=-1;
             $my_liked_postids=[];
             $my_bookmarked_postids=[];
 
             if($request->header('Authorization')){
                 $user_id=$this->get_user_from_api_token($request->header('Authorization'));
                 $my_liked_postids=$this->my_liked_postids($user_id);
                 $my_bookmarked_postids=$this->my_bookmarked_postids($user_id);
             }
 
             $post=Post::where(['slug'=>$slug,'status'=>1,'is_active'=>1])->with('user:id,name,username,unique_id,image,bio','categories:id,name,image','threads:id,name','keywords:id,name')->first();
             if(!empty($post)){
                 event(new PostViewCounterEvent($post,$request->ip()));
                 $formatted_post=$this->formatted_post($post,$my_liked_postids,$my_bookmarked_postids);
                 if($user_id>0){
                     $followers=User::find($user_id)->active_followings->pluck('id')->toArray();
                     $post_user_id=$post->user['id'];
                     $is_followed=in_array($post_user_id,$followers)?1:0;
                     $formatted_post['user']['is_followed']=$is_followed;
                 }else{
                     $is_followed=0;
                     $formatted_post['user']['is_followed']=0;
                 }
 
                 $comments=Comment::where(['parent_id'=>0,'is_active'=>1,'post_id'=>$post->id])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(4)->get();
                 foreach($comments as $comment){
                     $this->remove_null($comment);
                     unset($comment['replies']);
                 }
                 $share_urls=array(
                     'twitter'=>"https://twitter.com/share?text=".$post->title."&url=https://www.weopined.com/opinion/".$post->slug,
                     'facebook'=>"https://facebook.com/sharer/sharer.php?u=https://www.weopined.com/opinion/".$post->slug,
                     'linkedin'=>"https://www.linkedin.com/shareArticle?mini=true&url=https://www.weopined.com/opinion/".$post->slug,
                     'url'=>"https://www.weopined.com/opinion/".$post->slug
                 );
                 $formatted_post['updated_at'] = array('date'=>Carbon::now(),'timezone_type'=>3,'timezone'=>'Asia/Kolkata');
                 $response=array('status'=>'success','result'=>1,'post'=>$formatted_post,'comments'=>$comments,'share_urls'=>$share_urls);
                 return response()->json($response,200);
             }else{
                 $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                 return response()->json($response,200);
             }
          }
          catch (\Exception $e) {
             $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
             return response()->json($response, 500);
         }
     }

    public function edit(Request $request,$id){
        try{

                $post=Post::where(['id'=>$id,'user_id'=>Auth::user()->user_id,'is_active'=>1])->with('user:id,name,username,unique_id,image','categories:id,name,image','threads:id,name')->first();
                if(!empty($post)){
                    $response=array('status'=>'success','result'=>1,'post'=>$post);
                    return response()->json($response,200);
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                    return response()->json($response,200);
                }
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'id'=>'required',
                'title'=>'required',
                'categories'=>'required',
                'cover'=>'nullable|mimes:jpeg,jpg,png,gif|max:5120',
                'status'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $post=Post::where(['id'=>$request->input('id'),'user_id'=>Auth::user()->user_id,'is_active'=>1])->first();
                if(!empty($post)){

                    if(strcmp($post->plainbody,$request->input('plainbody'))!=0){
                        $update=Post::where('id',$post->id)->update(['plagiarism_checked'=>0]);
                    }

                    if($request->hasFile('cover')){
                        $uniqueid=uniqid();
                        $original_name=$request->file('cover')->getClientOriginalName();
                        $size=$request->file('cover')->getSize();
                        $extension=$request->file('cover')->getClientOriginalExtension();
                        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
                        $imagepath=url('/storage/cover/'.$filename);
                        $path=$request->file('cover')->storeAs('public/cover',$filename);
                        $size=$this->optimize_image($extension,'cover',$filename,$size);
                        $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'POST_COVER',$size,$extension,Auth::user()->user_id);
                        $request->request->add(['coverimage' => $imagepath]);
                        try{
                            $job = (new ResizeImageJob(storage_path('app/public/cover/'.$filename),storage_path('app/public/cover/'),[[350, 250]]))->onQueue('default');
                            app(Dispatcher::class)->dispatch($job);
                        }catch(\Exception $e){}
                    }else{
                        if($request->has('imageurl')){
                            $request->request->add(['coverimage' => $request->input('imageurl')]);
                        }else{
                            $request->request->add(['coverimage' => $post->coverimage]);
                        }
                    }

                    $unique_id=$post->uuid;
                    $new_post=$this->save_post($post,$request->all(),$unique_id);

                    $opinionThreads=[];
                    if($request->has('tags') && strlen($request->input('tags'))>0){
                        $opinionThreads=$this->create_threads($new_post,$request->input('categories'),$request->input('tags'));
                    }else{
                        $new_post->threads()->sync([]);
                    }

                    if($request->has('keywords') && strlen($request->input('keywords'))>0){
                        $postKeywords=$this->create_keywords($new_post,$request->input('keywords'));
                    }else{
                        $new_post->keywords()->sync([]);
                    }

                    $status=$request->input('status');
                    $opinion=ShortOpinion::where('post_id',$new_post->id)->first();
                    if($opinion){
                        $this->create_opinion_from_post($new_post, Auth::user()->user_id,$opinion,$opinion->uuid,$opinionThreads,'update');
                    }else{
                        if(count($opinionThreads)>0){
                        $opinion=new ShortOpinion();
                        $this->create_opinion_from_post($new_post, Auth::user()->user_id,$opinion,uniqid(),$opinionThreads,'create');
                        }
                    }

                    if($status=='0'){
                        $this->is_active_mode_changer($new_post->id,$status,'draft');
                        $this->remove_null($new_post);
                        $response=array('status'=>'success','result'=>1,'message'=>'Draft saved','post'=>$new_post);
                        return response()->json($response,200);
                    }else{
                        $this->is_active_mode_changer($new_post->id,$status,'published');
                        /* OFFER EMAIL FUNCTION CALLS
                        $this->send_email_to_user();
                        if(str_word_count($new_post->plainbody)<400){
                            $this->send_email_for_post_wordcount($new_post);
                        }
                        if(Auth::user()->user->registered_as_writer==0){
                            $this->send_email_for_register_as_writer($new_post);
                        } */

                         // $this->notify_followers($newPost,'update');

                        $this->remove_null($new_post);
                        $response=array('status'=>'success','result'=>1,'message'=>'Article published','post'=>$new_post);
                        return response()->json($response,200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                    return response()->json($response, 200);
                }
           }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function destroy(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                $post=Post::where('id',$request->input('post_id'))->first();
                if(!empty($post)){
                    if($post->user_id==Auth::user()->user_id)
                    {
                        DB::transaction(function () use($post){

                            DB::table('posts')->where('id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('category_posts')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('post_threads')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('post_keywords')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('likes')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('views')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('bookmarks')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('comments')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            $comments=DB::table('comments')->where('post_id', '=', $post->id)->get();
                            foreach($comments as $comment){
                                DB::table('comments_likes')->where('comment_id',$comment->id)->update(['is_active' => 0]);
                            }
                            DB::table('report_posts')->where('post_id', '=', $post->id)->update(['is_active' => 0]);
                            DB::table('notifications')
                            ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
                            ->orWhere('data','like','%"event":"COMMENTED_ON_ARTICLE"%')
                            ->orWhere('data','like','%"event":"ARTICLE_LIKED"%')
                            ->where('data','like','%"post_id":'.$post->id.'%')
                            ->delete();

                            $opinion=ShortOpinion::where('post_id', '=', $post->id)->first();
                            if($opinion){
                                $opinion->is_active = 0;
                                $opinion->save();
                                DB::table('thread_opinions')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                                DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                                $opinion_comments=DB::table('short_opinion_comments')->where('short_opinion_id',$opinion->id)->get();
                                foreach($opinion_comments as $op_comment){
                                    DB::table('short_opinion_comments_likes')->where('comment_id',$op_comment->id)->update(['is_active'=>0]);
                                }
                                DB::table('short_opinion_likes')->where('short_opinion_id',$opinion->id)->update(['is_active'=>0]);
                                DB::table('notifications')
                                ->where('data','like','%"event":"OPINION_LIKED"%')
                                ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                                ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                                ->delete();
                            }
                    });

                    $response=array('status'=>'success','result'=>1,'message'=>'Article deleted');
                    return response()->json($response,200);
                    }else{
                        $response=array('status'=>'error','result'=>0,'errors'=>'Access denied');
                        return response()->json($response, 200);
                    }
                }else{
                    $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                    return response()->json($response, 200);
                }
           }
        }
        catch (\Exception $e) {
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }


    public function likes(Request $request,$id){
        try{
            $post=Post::where(['id'=>$id,'status'=>1,'is_active'=>1])->first();
            if(!empty($post)){
                $post_likes=Like::where('post_id',$post->id)->where('is_active',1)->with('user:id,name,username,unique_id,image')->has('user')->orderBy('liked_at','desc')->paginate(20);
                $formatted_likes=$post_likes->getCollection()->transform(function($like,$key){
                    if($like->user){
                        $this->remove_null($like);
                        $is_followed=0;
                        if(Auth::check()){
                            $is_followed=in_array($like->user->id, Auth::user()->user->active_followings->pluck('id')->toArray())?1:0;
                        }else{
                            $is_followed=0;
                        }
                        unset($like['ip_address']);
                        unset($like['user_agent']);
                        $like->user['is_followed']=$is_followed;
                        return $like;
                    }
                });
                $meta=$this->get_meta($post_likes);
                $response=array('status'=>'success','result'=>1,'post_likes'=>$formatted_likes,'meta'=>$meta);
                return response()->json($response, 200);
            }else{
                $response=array('status'=>'error','result'=>0,'errors'=>'Article not found');
                return response()->json($response, 200);
            }
         }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function report(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'post_id'=>'required',
                'flag'=>'required'
            ]);

            if($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',',$validator->errors()->all()));
                return response()->json($response, 200);
           }else{
                    //var_dump($request->input('post_id'));
                    $flag_reason=explode("--",$request->input('flag'));
                    $report_post=new ReportPost();
                    //$report_post->post_id=$request->input('reportpost');
                    $report_post->post_id=$request->input('post_id');
                    $report_post->reported_user_id=Auth::user()->user_id;
                    $report_post->report_flag=(int)$flag_reason[0];
                    $report_post->report_reason=$flag_reason[1];
                    $report_post->save();

                    $response=array('status'=>'success','result'=>1,'message'=>'Issue successfully reported.');
                    return response()->json($response,200);
           }
        }catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    protected function save_post(Post $post,array $data,$unique_id){

        $totalLength=strlen($data['plainbody']);
        $read_time=1+round($totalLength/375);
        $slug = $this->create_slug($data['title']);
        $categories=explode(',',$data['categories']);

        $post->title=$data['title'];
        $post->slug=$slug;
        $post->uuid=$unique_id;
        $post->coverimage=isset($data['coverimage'])?$data['coverimage']:null;
        $post->body=isset($data['body'])?$data['body']:null;
        $post->plainbody=isset($data['plainbody'])?$data['plainbody']:null;
        $post->user_id=Auth::user()->user_id;
        $post->status=isset($data['status'])?$data['status']:1;
        $post->readtime=$read_time;
        $post->platform=isset($data['platform'])?$data['platform']:'android';
        $post->save();
        $post->categories()->sync($categories);
        return $post;
    }

    protected function create_threads($post,$categories,$threads)
    {
       $opinionThreads=[];
       $categories=explode(',',$categories);
       $threads=explode(',',$threads);
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


    protected function create_opinion_from_post($post,$user_id,$opinion,$uniqueid,$opinionThreads,$event)
    {
        $body='';
        $cpanel_body='';
        foreach($opinionThreads as $thread){
           $body=$body.' <a href="https://weopined.com/thread/'.$thread->name.'" class="thread_link" data-id="'.$thread->id.'">#'.$thread->name.'</a>';
           $cpanel_body=$cpanel_body.' <a href="/cpanel/thread/view/'.$thread->id.'" class="thread_link">#'.$thread->name.'</a>';
        }

        $hash_tags=implode(',', preg_filter('/^/', '#',Arr::pluck($opinionThreads, 'name')));

       $links=array();
       $info['status']='OK';
       $info['title']=$post->title;
       $info['description']=str::limit($post->plainbody,40,'...');
       $info['url']=url('/opinion/'.$post->slug);
       $info['type']='link';
       $info['image']=$post->coverimage;
       $info['providerName']='Opined';
       $info['providerUrl']='https://www.weopined.com';
       $info['providerIcon']='https://www.weopined.com/favicon.png';
       array_push($links,$info);

       $opinion->uuid=$uniqueid;
       $opinion->body=$body;
       $opinion->plain_body=url('/opinion/'.$post->slug);
       $opinion->cpanel_body=$cpanel_body;
       $opinion->hash_tags=isset($hash_tags)?$hash_tags:null;
       $opinion->cover=NULL;
       $opinion->cover_type='none';
       $opinion->links=json_encode($links);
       $opinion->user_id=$user_id;
       $opinion->type="post";
       $opinion->post_id=$post->id;
       $opinion->is_active=1;
       $opinion->platform="android";
       $opinion->save();

       if(count($opinionThreads)>0){
           $threadIds=Arr::pluck($opinionThreads, 'id');
           $opinion->threads()->sync($threadIds);
       }

    }

    protected function is_active_mode_changer($post_id,$is_active,$event){
        DB::transaction(function () use($post_id,$is_active,$event){

            DB::table('category_posts')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            DB::table('post_threads')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            DB::table('post_keywords')->where('post_id', '=', $post_id)->update(['is_active' =>$is_active]);

            DB::table('likes')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            DB::table('views')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            DB::table('bookmarks')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            DB::table('comments')->where('post_id', '=', $post_id)->update(['is_active' => $is_active]);
            $comments=DB::table('comments')->where('post_id', '=', $post_id)->get();
            foreach($comments as $comment){
                DB::table('comments_likes')->where('comment_id',$comment->id)->update(['is_active' =>$is_active]);
            }
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
                DB::table('notifications')
                ->where('data','like','%"event":"OPINION_LIKED"%')
                ->orWhere('data','like','%"event":"COMMENTED_ON_OPINION"%')
                ->where('data','like','%"opinion_id":'.$opinion->id.'%')
                ->update(['is_active'=>$is_active]);
            }
        });
    }


     // function for send email to user (post author) , after published post article
     protected function send_appriciation_email_to_user($post){
        try{
             //Mail::send(new PostAppriciationMail(Auth::user()->user,$post));
             $mailJET=new MailJetHelper();
             $mailJET->send_post_appriciation_mail(Auth::user()->user,$post);
        }
        catch(\Exception $e){}
    }


    /**********************************************************************************************************/
        /* EMAIL FUNCTIONS RELATED TO OFFER POST (CURRENTLY NOT IN USE)*/
    /**********************************************************************************************************/


     // function for send email to user (post author) , after published post article
     protected function send_email_to_user(){
        try{  Mail::send(new OfferMail(Auth::user()->user));}
        catch(\Exception $e){}
    }

    // function for send email to user(post author) who is not registered as writer yet.
    protected function send_email_for_register_as_writer(Post $post){
        try{  Mail::send(new RegisterWriterMail(Auth::user()->user,$post));}
        catch(\Exception $e){}
    }

    // function for send email to  user(post author) , if published post has word count less than 400
    protected function send_email_for_post_wordcount(Post $post){
        try{  Mail::send(new WordcountMail(Auth::user()->user,$post));}
        catch(\Exception $e){}
    }

    protected function notify_followers($post,$event)
    {
        $followers=auth()->user()->user->active_followers;
        $follower_ids=count($followers)>0?$followers->pluck('id')->toArray():[];
        if($event=='update'){
            DB::table('notifications')
            ->where('data','like','%"event":"ARTICLE_PUBLISHED"%')
            ->where('data','like','%"post_id":'.$post->id.'%')
            ->delete();
        }
        $fcm_tokens=UserDevice::select('gcm_token')->distinct()->whereIn('user_id',$follower_ids)->whereNotNull('api_token')->whereNotNull('gcm_token')->where('is_active',1)->pluck('gcm_token')->toArray();
        try{
            foreach(array_chunk($fcm_tokens,100) as $chunk) {
                dispatch(new PostCreatedJob($post,Auth::user()->user,$chunk));
            }
            foreach($followers as $follower){
                Notification::send($follower,new PostCreated($post,Auth::user()->user,$fcm_tokens));
            }
        }catch(\Exception $e){}

    }

}
