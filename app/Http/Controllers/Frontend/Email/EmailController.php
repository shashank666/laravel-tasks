<?php

namespace App\Http\Controllers\Frontend\Email;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Mailjet\Resources;
use Illuminate\Support\Facades\Storage;

require $_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php';
use App\Model\Post;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use App\Model\ShortOpinion;
use App\Model\ArticleStatus;
use App\Model\ShortOpinionLike;
use DB;
use App\Model\User;
use App\Model\UserAccount;
use App\Model\UserContact;
use Carbon\Carbon;
use App\Http\Helpers\MailJetHelper;
use App\Notifications\MailJet\AccountCreated;
use App\Jobs\Digest\WeeklyDigestMailJob;
use App\Model\Employee;
use Jenssegers\Agent\Agent;
use Notification;
use FFMpeg;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Jobs\Opinion\TestVideoJob;
use Illuminate\Support\Arr;

class EmailController extends Controller
{
    public function update_post_slug(){
        $all_posts=Post::select('id','title','slug')->get();
        foreach($all_posts as $post){
            $new_slug=$this->create_slug($post->title);
            Post::where('id',$post->id)->update(['slug'=>$new_slug]);
        }
        return 'done';
    }

    public function testing_account(){
        $all_threads=DB::table('thread_opinions')->get()->pluck('thread_id')->toArray();
        $threads_to_delete=[];
        foreach($all_threads as $id){
            $found=DB::table('threads')->where('id',$id)->first();
            if(!$found){
                array_push($threads_to_delete,$id);
            }
        }
        DB::table('thread_opinions')->whereIn('thread_id',$threads_to_delete)->delete();
    }

    public function testing_ip(Request $request){
        $response=[];
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }else{
            $ip = $request->ip();
        }
        $response['ip']=$ip;
        $response['ua']=$request->header("user-agent");
        $agent = new Agent();
        $browser = $agent->browser();
        if($agent->isMobile()){
            $device_type='mobile';
        }else if($agent->isTablet()){
            $device_type='tablet';
        }else{
            $device_type='desktop';
        };

        $ip_url='http://www.geoplugin.net/json.gp?ip='.$ip;
        $json = file_get_contents($ip_url);
        $data = json_decode($json);

        $response['browser_version']=$agent->version($browser);
        $response['device_os_name']=$agent->platform();
        $response['device_name']=$agent->device();
        $response['device_type']=$device_type;
        $response['is_robot']=$agent->isRobot();
        $response['location']= $data;
        return response()->json($response);
    }


    public function testing(){


        /* $contactsIDS=UserContact::select('user_id')->get()->pluck('user_id')->toArray();
        $allusers=User::whereNotIn('id',$contactsIDS)->get();
        foreach($allusers as $user){
            User::where('id',$user->id)->update(['contacts_saved'=>0,'contacts_saved_at'=>NULL]);
        } */
        /* $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        $opinions=ShortOpinion::where(['is_active'=>1,'type'=>'opinion'])->whereNotNull('plain_body')->orderBy('created_at','desc')->with('user')->distinct('plain_body')->take(4)->get();
        $latest_posts=Post::where(['status'=>1,'is_active'=>1])->with('user','categories')->orderBy('created_at','desc')->take(4)->get();
        $trending_threads= ThreadOpinion::where('is_active',1)
        ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
        ->whereBetween('created_at',[$from,$to])
        ->with('thread')
        ->groupBy('thread_id')
        ->orderBy('count','desc')
        ->take(6)
        ->get();
        $final_response=[];
        User::whereIn('id',[48])->select('id','name','email')->where('is_active',1)->orderBy('id')->chunk(100, function ($users) use($final_response,$opinions,$latest_posts,$trending_threads){
            $message_body=[];
            foreach($users as $user){
                $message=[
                    'From' => [
                        'Email' => "notification@weopined.com",
                        'Name' => "Opined"
                    ],
                    'To' => [
                        [
                            'Email' => $user->email,
                            'Name' =>$user->name
                        ]
                    ],
                    'ReplyTo'=>[
                        'Email'=>'no-reply@weopined.com',
                        'Name'=>'No Reply'
                    ],
                    'Subject' => "What's New on Opined",
                    'HTMLPart' => (String) view('frontend.email.digest.weekly')->with(['user' =>$user,
                            'opinions'=>$opinions,
                            'latest_posts'=>$latest_posts,
                            'trending_threads'=>$trending_threads
                            ])
                ];
                array_push($message_body,$message);
            }
            $body=['Messages' =>$message_body];
            $mj = new \Mailjet\Client(getenv('MAILJET_PUBLIC_KEY'), getenv('MAILJET_SECRET_KEY'),true,['version' => 'v3.1']);
            $response = $mj->post(Resources::$Email,['body' => $body]);
            array_push($final_response,$response->getData());
            return true;
        });
        return response()->json($final_response); */
        //$users=User::select('id','name','email')->where('is_active',1)->get();
        //$response->success() && var_dump($response->getData());
        //return $response->getData();
    }
         //   'ReplyTo'=>'no-reply@weopined.com'

    public function demo($id){
        $mailJET=new MailJetHelper();

        $user=User::where('id',6362)->first();
        $users=User::where('id',6362)->get();

        if($id==1){
            $token='asdadadadssad';
            $response=$mailJET->send_account_created_mail($user,$token);
            return $response;
           // try{
              //  Notification::send($user,new AccountCreated($user,'asdfasdf1234'));
          //  }catch(\Exception $e){}

           /*  return view('frontend.email.auth.account')
            ->with(['name' =>'Saurabh',
                    'verify_url'=>'https://asd/asdadsda']); */
        }else if($id==2){
          /*   return view('frontend.email.auth.welcome')
            ->with(['name' =>'Saurabh']); */
            $response=$mailJET->send_welcome_mail($user);
            return $response;
        }else if($id==3){
            /* return view('frontend.email.auth.reset')
            ->with(['name' =>'Saurabh',
            'reset_url'=>'https://asd/asdadsda']); */
            $response=$mailJET->send_reset_password_mail($user,'https://asd/asdadsda');
            return $response;
        }else if($id==4){
            $vurl='asdadadadssad';
            $response=$mailJET->send_verify_email_mail($user,$vurl);
            return $response;
           /*  return view('frontend.email.auth.verifyemail')
            ->with(['name' =>'Saurabh',
            'email'=>'asdf@zxc.com',
            'verify_url'=>'https://asd/asdadsda']); */
        }else if($id==5){
           /*  return view('frontend.email.auth.verifysuccess')
            ->with(['name' =>'Saurabh',
            'email'=>'asdf@zxc.com']); */
            $response=$mailJET->send_account_verified_mail($user);
            return $response;
        }else if($id==6){
            /* return view('frontend.email.support.contactus')
            ->with(['name' =>'Saurabh',
                    'email'=>'asdf@zxc.com',
                    'subject'=>'Demo subject is is sadsa sda',
                    'body'=>'All-rounder James Neesham and Colin de Grandhomme shared the highest sixth-wicket partnership by a New Zealand pair in World Cup history. The duo achieved the feat against Pakistan in the 2019 World Cup on Wednesday, registering 132 runs. The previous record of 91 runs was set by Colin de Grandhomme and Kane Williamson against South Africa on June 19.'
                    ]); */
            $message=array(
                    'name' =>'Saurabh',
                    'email'=>'asdf@zxc.com',
                    'subject'=>'Demo subject is is sadsa sda',
                    'message'=>'All-rounder James Neesham and Colin de Grandhomme shared the highest sixth-wicket partnership by a New Zealand pair in World Cup history. The duo achieved the feat against Pakistan in the 2019 World Cup on Wednesday, registering 132 runs. The previous record of 91 runs was set by Colin de Grandhomme and Kane Williamson against South Africa on June 19.'
            );
            $response=$mailJET->send_contactus_mail($message);
            return $response;
        }else if($id==7){
            $post=Post::where('id',608)->with('user')->first();
            /*$response=$mailJET->send_post_appriciation_mail($user,$post);
            return $response;*/
             return view('frontend.email.post.appriciate')
            ->with(['name' =>'Saurabh',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']); 
        }else if($id==8){
            $post=Post::where('id',3512)->with('user')->first();
            $response=$mailJET->send_blog_post_mail($users,$post,$post->user);
            return $response;
            /* $post=Post::where('id',51)->with('user')->first();
            return view('frontend.email.post.blogpost')
                    ->with([
                            'post_link'=>'https://www.weopined.com/opinion/'.$post['slug'],
                            'post_title'=>$post['title'],
                            'post_user'=>ucfirst($post->user['name']),
                            'post_userlink'=>'https://www.weopined.com/@'.$post->user['username'].'/'.$post->user['unique_id'],
                            'post_createdat'=>$post['created_at'],
                            'post_cover'=>$post['coverimage'],
                            'post_body'=>str::limit($post['plainbody'],200,' ...  read more on Opined')
                            ]); */
        }else if($id==9){
            $from=Carbon::now()->subDays(30);
            $to=Carbon::now();
            $opinions=ShortOpinion::where(['is_active'=>1,'type'=>'opinion'])->whereNotNull('plain_body')->orderBy('created_at','desc')->with('user')->distinct('plain_body')->take(6)->get();
            /*$opinions=ShortOpinion::where(['is_active'=>1,'platform'=>'website','type'=>'opinion'])->whereNotNull('plain_body')->orderBy('created_at','desc')->with('user')->distinct('plain_body')->take(4)->get();*/
            $latest_posts=Post::where(['status'=>1,'is_active'=>1])->with('user','categories')->orderBy('created_at','desc')->take(2)->get();

            $thread_ids=DB::table('thread_opinions')->where('is_active',1)->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))->whereBetween('created_at',[$from,$to])->groupBy('thread_id')->orderBy('count','desc')->take(6)->get()->pluck('thread_id')->toArray();
            $placeholders = implode(',',array_fill(0, count($thread_ids), '?'));
            $trending_threads=Thread::where('is_active',1)->whereIn('id',$thread_ids)->orderByRaw("field(id,{$placeholders})", $thread_ids)->get();

            foreach($trending_threads as $thread){
                $thread->opinions_count=ThreadOpinion::where(['thread_id'=>$thread->id,'is_active'=>1])->count();
            }
            $profile_user=User::where(['is_active'=>1])->first();
            if($profile_user){
              $contributors = DB::table('short_opinions')
                ->leftJoin('users', 'users.id', '=', 'short_opinions.user_id')
                ->where(['short_opinions.is_active'=>1, 'users.is_active'=>1])
                ->whereBetween('short_opinions.created_at',[Carbon::now()->subDays(7),Carbon::now()])
                ->groupBy('short_opinions.user_id')
                ->select('users.id','users.name','users.username','users.unique_id','users.is_active','users.email','users.bio','users.image', DB::raw("COUNT(short_opinions.user_id) as count_opinion"))
                ->orderBy('count_opinion', 'desc')
                ->limit(4)
                ->get();
            }
            $top_polls_trending = DB::table('poll_results')
                ->leftJoin('polls', 'polls.id', '=', 'poll_results.poll_id')
                ->where('polls.visibility',1)
                ->whereBetween('poll_results.created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->groupBy('poll_results.poll_id')
                ->select('polls.*', DB::raw("COUNT(poll_results.poll_id) as count_top_poll"))
                ->orderBy('count_top_poll','desc')
                ->limit(3)
                ->get();

            //return response()->json(array('status'=>'success',$users,$opinions,$latest_posts,$trending_threads));


            
            //dispatch((new WeeklyDigestMailJob($opinions,$latest_posts,$trending_threads,$users))->onQueue('digest'));
            //return 'done';

          return view('frontend.email.digest.weekly')
          ->with(['user' =>$user,
                'opinions'=>$opinions,
                'latest_posts'=>$latest_posts,
                'trending_threads'=>$trending_threads,
                'contributors'=>$contributors,
                'top_polls_trending'=>$top_polls_trending,
                ]); 
               // $response=$mailJET->send_digest_mail($users,$opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending);
                //return $response;


        }

        else if($id==10){
            $string = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12);
             return view('admin.email.auth.invitemail')
            ->with(['name' =>'Aman',
            'email'=>'asdf@zxc.com',
            'verify_url'=>'https://asd/asdadsda/'.$string]); 

             /*$vurl= $string;
            $response=$mailJET->send_invite_email_mail($user,$vurl);
            return $response;*/
        }
        else if($id==11){
            $string = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',12)),0,12);
            return view('admin.email.auth.resetpassword')
            ->with(['name' =>'Aman',
            'email'=>'asdf@zxc.com',
            'verify_url'=>'https://asd/asdadsda/'.$string]); 
            /* $vurl= $string;
            $response=$mailJET->send_reset_password_admin_mail($user,$vurl);
            return $response;*/
        }
        else if($id==12){
            $post=Post::where('id',604)->with('user')->first();
            /*$response=$mailJET->send_post_plagiarism_mail($user,$post);
            return $response;*/
             return view('frontend.email.rsm.plagiarism')
            ->with(['name' =>'Aman Goutam',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']);

    }

    else if($id==13){
            $post=Post::where('id',604)->with('user')->first();
            /*$response=$mailJET->send_post_reject_mail($user,$post,$article_status);
            return $response;*/
             return view('frontend.email.rsm.reject')
            ->with(['name' =>'Aman Goutam',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']);

    }
    else if($id==14){
            $post=Post::where('id',604)->with('user')->first();
            /*$response=$mailJET->send_post_inform_mail($user,$post);
            return $response;*/
             return view('frontend.email.rsm.inform')
            ->with(['name' =>'Aman Goutam',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']);

    }
    else if($id==15){
            $post=Post::where('id',604)->with('user')->first();
            /*$response=$mailJET->send_post_selected_mail($user,$post);
            return $response;*/
             return view('frontend.email.rsm.selected')
            ->with(['name' =>'Aman Goutam',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']);

    }
    else if($id==16){
            $user=Post::where('id',608)->with('user')->first();
            /*$response=$mailJET->send_post_appriciation_mail($user,$post);
            return $response;*/
             return view('frontend.email.rsm.payment')
            ->with(['name' =>'Saurabh',
                'ammount' =>'10',
            'post_title'=>'Post Title is Awesome',
            'post_link'=>'https://www.weopined.com/opinion/postststs']); 
        }
    else if($id==17){
            $email_otp='741258';
             $response=$mailJET->send_verify_account_email_mail($user,$email_otp);
             return $response;
            // return view('frontend.email.auth.verify_account')
            // ->with(['name' =>'Aman',
            // 'email_otp'=>$email_otp]);
        }   
    else if($id==18){
            $from=Carbon::now()->subDays(1);
            $to=Carbon::now();
            $users_like = ShortOpinionLike::where(['is_active'=>1])->whereBetween('liked_at', [$from , $to])->get()->toArray();
            $likesby_shortopinion_id = [];
            // $users_id = [];
            // $short_opinion_id = [];
            // foreach ($users_like as $index => $user_like) {
            //     array_push($users_id,$user_like['short_opinion']['user_id']);
            //     array_push($short_opinion_id,$user_like['short_opinion']['id']);
            // }
            // // var_dump(array_unique($users_id));
             // var_dump($users_like);
             foreach ($users_like as $index => $user_like) {
                $users_like_count = ShortOpinionLike::where(['short_opinion_id'=>$user_like['short_opinion_id'],'is_active'=>1])->whereBetween('liked_at', [$from , $to])->get()->count();
                 $array = Arr::add([$user_like['short_opinion_id'] => $users_like_count], null, null);
                 if(!in_array($array, $likesby_shortopinion_id))
                        {
                          array_push($likesby_shortopinion_id, $array);

                        }
                 
             }
             var_dump($likesby_shortopinion_id);
             
            // return response()->json(array('status'=>'success',$users_like));
           
               // $response=$mailJET->send_digest_mail($users,$opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending);
                //return $response;

    }
    }
    public function myIP(Request $request){
        //$myIP = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return  $_SERVER["HTTP_CF_CONNECTING_IP"];
        }else {
            dd($request);
        }
    }
    public function testing_video_transcoding(Request $request){
        if($request->hasFile('cover')){
            $file = $request->file('cover');
            $uniqueid=uniqid();
            $original_name=$file->getClientOriginalName();
            $size=$file->getSize();
            $extension=$file->getClientOriginalExtension();

            $duration_exceeded=$this->check_video_duration_exceeded($file->getRealPath());
            if($duration_exceeded){
                $response=array('status'=>'error','result'=>0,'errors'=>'Video duration is exceeded , more than 3 minutes not allowed');
                return response()->json($response, 200);
            }
            else if(!in_array(strtolower($extension),['mp4','webm']))
            {
                $response=array('status'=>'error','result'=>0,'errors'=>'Video format is invalid');
                return response()->json($response, 200);
            }else{
                $filename_without_ext=Carbon::now()->format('Ymd').'_'.$uniqueid;
                $filename=$filename_without_ext.'.'.$extension;
                $videopath=url('/storage/videos/'.$filename);
                $path=$file->storeAs('public/videos',$filename);
                $cover=$videopath;

                $job = (new TestVideoJob($path,'5d9c799961d5b',48))->onQueue('videos');
                app(Dispatcher::class)->dispatch($job);
                return response()->json(array('status'=>'done','job_id'=>$job));
                    /* $new_video_name=$filename_without_ext.'_transcoded.'.$extension;
                    try{
                        //$getID3 = new \getID3;
                        //$output = $getID3->analyze(storage_path('app/public/videos/'.$filename));
                        //FFMpeg::open($path)->save(new FFMpeg\Format\Video\X264('aac', 'libx264'),storage_path('app/public/videos/'.$new_video_name));
                    }catch(\Exception $e){

                    } */
            }
        }else{
            $response=array('status'=>'error','result'=>0,'errors'=>'Video file is required');
            return response()->json($response, 200);
        }

    }
}
