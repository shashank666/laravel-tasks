<?php

namespace App\Http\Helpers;

require $_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php';
use \Mailjet\Resources;

class MailJetHelper {



    public function send_welcome_mail($user){
        
        $view=(String) view('frontend.email.auth.welcome')->with(['name' =>ucfirst($user['name'])]);
        $response=$this->send_mailjet($user->name,$user->email,"Welcome To Opined",$view);
        return $response;
    }

    public function send_account_created_mail($user,$token){
        $verify_url='https://www.weopined.com/me/verify/'.$token;
        $view=(String) view('frontend.email.auth.account')->with(['name' =>ucfirst($user['name']),'verify_url'=>$verify_url]);
        $response=$this->send_mailjet($user->name,$user->email,"Welcome To Opined! Please confirm your email address",$view);
        return $response;
    }

    public function send_verify_email_mail($user,$token){
        $verify_url='https://www.weopined.com/me/verify/'.$token;
        $view=(String) view('frontend.email.auth.verifyemail')->with(['name' =>ucfirst($user['name']),'email'=>$user->email,'verify_url'=>$verify_url]);
        $response=$this->send_mailjet($user->name,$user->email,"Verify Your Email Address",$view);
        return $response;
    }

     public function send_verify_account_email_mail($user,$email_otp){
        $view=(String) view('frontend.email.auth.verify_account')->with(['name' =>ucfirst($user['name']),'email'=>$user->email,'email_otp'=>$email_otp]);
        $response=$this->send_mailjet($user->name,$user->email,"Verify Your Email Address",$view);
        return $response;
    }

    public function send_account_verified_mail($user){
        $view=(String) view('frontend.email.auth.verifysuccess')->with(['name' =>ucfirst($user['name']),'email'=>$user->email]);
        $response=$this->send_mailjet($user->name,$user->email,"Email Successfully Verified",$view);
        return $response;
    }

    public function send_reset_password_mail($user,$reset_url){
        $view=(String) view('frontend.email.auth.reset')->with(['name' =>ucfirst($user['name']),'reset_url'=>$reset_url]);
        $response=$this->send_mailjet($user->name,$user->email,"Reset Password for Opined",$view);
        return $response;
    }

    public function send_contactus_mail($data){
        $view=(String) view('frontend.email.support.contactus')->with(['name' =>ucfirst($data['name']),'email'=>$data['email'],'subject'=>$data['subject'],'message'=>$data['message']]);
        $emailSubject=$data['name']." Message From Contact Us on Opined";
        $response=$this->send_mailjet('ReachUs Opined','reach-us@weopined.com',$emailSubject,$view);
        return $response;
    }

    public function send_post_appriciation_mail($user,$post){
        $emailSubject='Thank you '.ucfirst($user['name']).', for submitting your article on Opined !';
        $view=(String) view('frontend.email.post.appriciate')->with(['name' =>ucfirst($user['name']),'post_title'=>$post->title,'post_link'=>'https://www.weopined.com/opinion/'.$post->slug]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_post_plagiarism_mail($user,$post){
        $emailSubject='Sorry, your article is not eligible for RSM on Opined!';
        $view=(String) view('frontend.email.rsm.plagiarism')->with(['name' =>ucfirst($user['name']),'post_title'=>$post->title,'post_link'=>'https://www.weopined.com/opinion/'.$post->slug]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_post_reject_mail($user,$post){
        $emailSubject='Sorry, your article is not eligible for RSM on Opined!';
        $view=(String) view('frontend.email.rsm.reject')->with(['name' =>ucfirst($user['name']),'post_title'=>$post->title,'post_link'=>'https://www.weopined.com/opinion/'.$post->slug]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_post_inform_mail($user,$post){
        $emailSubject='Thank you '.ucfirst($user['name']).', for publishing your article on Opined!';
        $view=(String) view('frontend.email.rsm.inform')->with(['name' =>ucfirst($user['name']),'post_title'=>$post->title,'post_link'=>'https://www.weopined.com/opinion/'.$post->slug]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_post_selected_mail($user,$post){
        $emailSubject='Congratulations '.ucfirst($user['name']).', your article is eligible for RSM on Opined!';
        $view=(String) view('frontend.email.rsm.selected')->with(['name' =>ucfirst($user['name']),'post_title'=>$post->title,'post_link'=>'https://www.weopined.com/opinion/'.$post->slug]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_account_deleted_mail($user){
        $emailSubject='Opined Account Deleted';
        $view=(String) view('frontend.email.account.account_deleted');
        $this->send_mailjet($user->name,$user->email,$emailSubject,$view);
    }

    public function send_account_password_changed($user){
        $emailSubject="Opined Account Password Changed";
        $view=(String) view('frontend.email.account.password_changed')->with(['name' =>ucfirst($user['name'])]);
        $this->send_mailjet($user->name,$user->email,$emailSubject,$view);
    }


    public function send_blog_post_mail($users,$post,$post_author){
        $emailSubject=$post['title']." published by ".ucfirst($post_author['name']);
        $view=(String) view('frontend.email.post.blogpost')->with([
                'post_link'=>'https://www.weopined.com/opinion/'.$post['slug'],
                'post_title'=>$post['title'],
                'post_user'=>ucfirst($post_author['name']),
                'post_userlink'=>'https://www.weopined.com/@'.$post_author['username'],
                'post_createdat'=>$post['created_at'],
                'post_cover'=>$post['coverimage'],
                'post_body'=>str::limit($post['plainbody'],200,' ...  read more on Opined')
                ]);
        $response=$this->sendto_all_mailjet($users,$emailSubject,$view);
        return $response;
    }

    public function send_digest_mail($users,$opinions,$latest_posts,$trending_threads,$contributors,$top_polls_trending){
        $emailSubject="What's New on Opined";
        $view=(String) view('frontend.email.digest.weekly')->with(['opinions'=>$opinions,'latest_posts'=>$latest_posts,'trending_threads'=>$trending_threads,'contributors'=>$contributors,'top_polls_trending'=>$top_polls_trending]);
        $response=$this->sendto_all_mailjet($users,$emailSubject,$view);
        return $response;
    }

    public function send_notification_mail($users,$opinions){
        $emailSubject="Stay Safe Stay Healthy";
        $view=(String) view('frontend.email.notification.daily')->with(['opinions'=>$opinions]);
        $response=$this->sendto_all_mailjet($users,$emailSubject,$view);
        return $response;
    }

    public function send_invite_email_mail($user,$token){
        $verify_url='https://www.weopined.com/employee/activation/'.$token;
        $view=(String) view('admin.email.auth.invitemail')->with(['name' =>ucfirst($user['name']),'email'=>$user->cmpemail,'verify_url'=>$verify_url]);
        $response=$this->send_mailjet($user->name,$user->cmpemail,"Invitation",$view);
        return $response;
    }

    public function send_successfull_payment_mail($user,$ammount){
        $emailSubject='Congratulations! '.ucfirst($user['name']).', Your payment has been processed';
        $view=(String) view('frontend.email.rsm.payment')->with(['name' =>ucfirst($user['name']),'ammount'=>$ammount]);
        $response=$this->send_mailjet($user->name,$user->email,$emailSubject,$view);
        return $response;
    }

    public function send_reset_password_admin_mail($user,$token){
        $verify_url='https://www.weopined.com/cpanel/reset/'.$token;
        $view=(String) view('admin.email.auth.resetpassword')->with(['name' =>ucfirst($user['name']),'email'=>$user->email,'verify_url'=>$verify_url]);
        $response=$this->send_mailjet($user->name,$user->email,"Reset Password",$view);
        return $response;
    }

    public function send_from_emailmanager($users,$subject,$message){
        $view=(String) view('admin.email.send')->with(['email_subject'=>$subject,'email_content'=>$message]);
        $response=$this->sendto_all_mailjet($users,$subject,$view);
        return $response;
    }

    protected function send_mailjet($name,$email,$subject,$htmlpart){
        $message=
        ['Messages' => [[
                    'From' => [
                        'Email' => "notification@weopined.com",
                        'Name' => "Opined"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' =>$name
                        ]
                    ],
                    'ReplyTo'=>[
                        'Email'=>'no-reply@weopined.com',
                        'Name'=>'No Reply'
                    ],
                    'Subject' => $subject,
                    'HTMLPart' => $htmlpart
            ]]];
        $mj = new \Mailjet\Client(getenv('MAILJET_PUBLIC_KEY'), getenv('MAILJET_SECRET_KEY'),true,['version' => 'v3.1']);
        $response = $mj->post(Resources::$Email,['body' =>$message]);
        return $response->getData();
    }

    protected function sendto_all_mailjet($users,$subject,$htmlpart){
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
        $body=['Messages' =>$message_body];
        $mj = new \Mailjet\Client(getenv('MAILJET_PUBLIC_KEY'), getenv('MAILJET_SECRET_KEY'),true,['version' => 'v3.1']);
        $response = $mj->post(Resources::$Email,['body' => $body]);
        return $response->getData();
    }
}

?>
