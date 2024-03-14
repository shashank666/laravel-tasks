<?php

namespace App\Http\Controllers\Admin\Opinion;
use Illuminate\Support\Str;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
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

use App\Model\Post;
use App\Model\Follower;
use App\Model\Bookmark;
use App\Model\Like;
use DB;
use App\Events\ThreadViewCounterEvent;
use Image;

use Session;


use Notification;
use App\Notifications\Frontend\ShortOpinionLiked;
use App\Notifications\Frontend\ThreadLiked;
use App\Notifications\Frontend\ShortOpinionCreated;

use App\Jobs\AndroidPush\ShortOpinionLikedJob;
use App\Jobs\AndroidPush\ThreadLikedJob;
use App\Jobs\AndroidPush\ShortOpinionCreatedJob;

use \Carbon\Carbon;
use App\Http\Helpers\VideoStream;


class OpinionContentController extends Controller{

    public function __construct()
    {
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
    }

    public function index(Request $request){

       
        $user_id_test = rand(4406,4805);
      
       
        $user_test = User::where(['id'=>$user_id_test])->first();
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();
        //$followingids = $user_test->active_followings->pluck('id')->toArray();
        
        //$liked_threads=$this->get_user_liked_threadids();
            return view('admin.dashboard.opinion.write_opinion_1');
        
    }


    public function store(Request $request)
    {

      
      $gender_op = "male";
      $user_test = User::whereBetween('id',[4406,4905])->inRandomOrder()->first();
      
        $type=$request->input('type');
        if($type=='IMAGE'){
            $files = $request->file('files');
            $finalimages=explode(',',$request->input('cover'));
            $imgs=array();
            foreach($files as $file){
                $filenameWithExt=$file->getClientOriginalName();
                if(in_array($filenameWithExt,$finalimages)){
                    $uniqueid=uniqid();
                    $original_name=$file->getClientOriginalName();
                    $original_size=$file->getSize();
                    $extension=$file->getClientOriginalExtension();
                    $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;

                    // Resize image in 314x240
                     $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_314x240'.'.'.$extension; 
                     $thumb_opinion = Image::make($file->getRealPath())->resize(314,  null, function ($constraint) {
    $constraint->aspectRatio();
});
                     $thumb_opinion->save('../storage/app/public/opinion/'.'/'.$imagename);
                     // End Of Resize image in 314x240

                     // Resize image in 500x320
                     $imagename = Carbon::now()->format('Ymd').'_'.$uniqueid.'_500x320'.'.'.$extension; 
                     $thumb_opinion = Image::make($file->getRealPath())->resize(500,  null, function ($constraint) {
                              $constraint->aspectRatio();
                          });
                     $thumb_opinion->save('../storage/app/public/opinion/'.'/'.$imagename);
                     // End Of Resize image in 500x320

                    $imagepath=url('/storage/opinion/'.$filename);
                    $path=$file->storeAs('public/opinion',$filename);
                    $size=$this->optimize_image($extension,'opinion',$filename,$original_size);
                    $this->save_file_to_db($uniqueid,$imagepath,$filename,$original_name,'OPINION_COVER_IMAGE',$size,$extension,$user_test->id);
                    array_push($imgs,$imagepath);
                }
            }
            $cover=implode(",",$imgs);
        }elseif($type=='YOUTUBE'){
            $cover=$request->input('cover');
        }elseif($type=='GIF'){
            $cover=$request->input('cover');
        }elseif($type=='EMBED'){
            $cover=$request->input('cover');
        }else{
            $cover=NULL;
        }
        $body_temp = $request->input('body');
        $blacklistArray = ['iframe'];
        $flag = false;
        foreach ($blacklistArray as $k => $v) {
          if (str::contains($body_temp, $v)) {
            $flag = true;
            break;
          }
        }

        if ($flag == true) {
          $body = strip_tags("$body_temp");
        }
        else{
          $body=$request->input('body');
        }
        $plain_body=$body;
        $cpanel_body=$body;
        $title = $request->input('title');
        
        $hash_pattern="/#(\w+)/";
        preg_match_all($hash_pattern, $body, $hashtags);
        $opinionThreads=[];

        if($request->has('thread')){
        $defaultThread=Thread::find($request->input('thread'));
        array_push($opinionThreads,$request->input('thread'));
        }

        if(count($hashtags[1])>0){
            $hash_tags_to_store=implode(',',array_map(function ($str) { return "#$str"; },$hashtags[1]));

            foreach ($hashtags[1] as $hashtag) {
                $threadFound=Thread::whereRaw('LOWER(`name`) = ?',[trim(strtolower($hashtag))])->first();
                    if($threadFound){
                        $ThreadId=$threadFound->id;
                    }else{
                        $threadCreate=Thread::create(['name'=>$hashtag,'slug'=>str::slug(trim($hashtag),'-')]);
                        $ThreadId=$threadCreate->id;
                    }
                array_push($opinionThreads,$ThreadId);
                $body=str_replace('#'.$hashtag,'<a href="https://weopined.com/thread/'.$hashtag.'" data-id="'.$ThreadId.'" class="thread_link">#'.$hashtag.'</a>',$body);
                $cpanel_body=str_replace('#'.$hashtag,'<a href="/cpanel/thread/view/'.$ThreadId.'" class="thread_link">#'.$hashtag.'</a>',$cpanel_body);
            }

            if($request->has('thread')){
                $hashtags[1]=array_map('strtolower', $hashtags[1]);
                if(!in_array(strtolower($defaultThread->name),$hashtags[1])){
                    $body=$body.' <a href="https://weopined.com/thread/'.$defaultThread->name.'" data-id="'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
                    $cpanel_body=$cpanel_body.' <a href="/cpanel/thread/view/'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
                }
            }

        }else{
            if($request->has('thread')){
            $hashtag=$defaultThread->name;
            $body=$body.' <a href="https://weopined.com/thread/'.$hashtag.'"  data-id="'.$defaultThread->id.'" class="thread_link">#'.$hashtag.'</a>';
            $cpanel_body=$cpanel_body.' <a href="/cpanel/thread/view/'.$defaultThread->id.'" class="thread_link">#'.$defaultThread->name.'</a>';
            $hash_tags_to_store='#'.$hashtag;
            }else{
                return redirect()->back()->withInput();
            }
        }

        $pattern  = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
        preg_match_all($pattern,$request->input('body'), $matches);
        $all_urls = $matches[0];
        if(count($all_urls)>0){
            $infolinks=array();
            foreach($all_urls as $url){
                $body=str_replace($url,'',$body);
                $info=$this->fetch_data_from_url($url);
                array_push($infolinks,$info);
            }
            $links_enc=json_encode($infolinks);
            $links_dummy=json_decode($links_enc);
              foreach ($links_dummy as $index=>$link_dummy) {
         
                 if($link_dummy->status=="error" || $link_dummy->image=="null"){
                      $links = NULL;
                      //array_push($rej_opinion_id,$rej_opinion->hash_tags);
                  }
                  else{
                    $links=json_encode($infolinks);
                  }

                }
        }else{
            $links=NULL;
        }

        $opinion=new ShortOpinion();
        $opinion->uuid=uniqid();
        $opinion->title=$title;
        $opinion->body=$body;
        $opinion->plain_body=$plain_body;
        $opinion->cpanel_body=$cpanel_body;
        $opinion->hash_tags=$hash_tags_to_store;

        $opinion->cover=$cover;
        $opinion->cover_type=$type;
        $opinion->links=$links;
        $opinion->user_id=$user_test->id;
        $opinion->save();

        if(count($opinionThreads)>0){
            $opinion->threads()->sync(array_unique($opinionThreads));
        }
    
        return redirect()->back();
        
    }

}