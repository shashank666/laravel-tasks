<?php

namespace App\Jobs\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use DB;
use App\Model\User;
use App\Model\EmailManager;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;
    public $email_id;
    public $timeout = 14400; // 4 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_id)
    {
       $this->email_id=$email_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $failed_emails=DB::table('failed_emails')->get()->pluck('email')->toArray();
        $emailManager=EmailManager::where(['id'=>$this->email_id,'is_active'=>1])->first();
        if($emailManager){

            $subject=$emailManager->email_subject;
            $htmlpart=(String) view('admin.email.send')->with(['email_subject'=>$emailManager->email_subject,'email_content'=>$emailManager->email_content]);

            if($emailManager->email_to_type=='specific'){
                $emails=explode(',',$emailManager->email_to);
                User::select('id','name','email')->whereIn('email',$emails)->whereNotIn('email',$failed_emails)->where('is_active',1)->chunk(10,function($users) use($subject,$htmlpart){
                    $data=$this->set_mail_data($users,$subject,$htmlpart);
                    $this->send_mail_to_chunk($data);
                    usleep(5000);
                });
                $emailManager->status='completed';
                $emailManager->save();
            }else{
                if($emailManager->email_to=='all'){
                    User::select('id','name','email')->whereNotIn('email',$failed_emails)->where('is_active',1)->chunk(10,function($users) use($subject,$htmlpart){
                        $data=$this->set_mail_data($users,$subject,$htmlpart);
                        $this->send_mail_to_chunk($data);
                        usleep(5000);
                    });
                }else if($emailManager->email_to=='website'){
                    User::select('id','name','email')->whereNotIn('email',$failed_emails)->where(['platform'=>'website','is_active'=>1])->chunk(10,function($users) use($subject,$htmlpart){
                        $data=$this->set_mail_data($users,$subject,$htmlpart);
                        $this->send_mail_to_chunk($data);
                        usleep(5000);
                    });
                }else{
                    User::select('id','name','email')->whereNotIn('email',$failed_emails)->where(['platform'=>'android','is_active'=>1])->chunk(10,function($users) use($subject,$htmlpart){
                        $data=$this->set_mail_data($users,$subject,$htmlpart);
                        $this->send_mail_to_chunk($data);
                        usleep(5000);
                    });
                }
                $emailManager->status='completed';
                $emailManager->save();
            }
        }
    }

    protected function set_mail_data($users,$subject,$htmlpart){
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
                'Subject' => $subject,
                'HTMLPart' => $htmlpart
            ];
            array_push($message_body,$message);
        }
        $mail_data=['Messages' =>$message_body];
        return $mail_data;
    }

    protected function send_mail_to_chunk($data){
        $header = array("Content-Type:application/json");
        $body=json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'https://api.mailjet.com/v3.1/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERPWD, getenv('MAILJET_PUBLIC_KEY').':'.getenv('MAILJET_SECRET_KEY'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
    }

}
