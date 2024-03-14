<?php

namespace App\Http\Controllers\Admin\Dashboard;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use DB;
use Carbon\Carbon;
use App\Model\Message;
use App\Model\MessageReply;
use Mail;
use App\Mail\Support\ReplyToUserMail;
use Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require $_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php';


class MessagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        View::share('menu','messages');
        $read_count=Message::where('mark_read',1)->count();
        $unread_count=Message::where('mark_read',0)->count();
        $starred_count=Message::where('starred',1)->count();
        View::share('read_count',$read_count);
        View::share('unread_count',$unread_count);
        View::share('starred_count',$starred_count);
    }

    public function starMessage(Request $request){
            $message_id=$request->input('message_id');
            $message=Message::where('id',$message_id)->first();
            if($message->starred==0){
                $message->starred=1;
                $response['star']='added';
            }else{
                $message->starred=0;
                $response['star']='removed';
            }
            $message->save();
            return response()->json($response);
    }

    public function sendReplyToUser(Request $request){

        $this->validate($request,[
            'message_id'=>'required',
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255',
            'subject'=>'required',
            'message'=>'required'
        ]);

        $reply=MessageReply::create([
            'message_id'=>$request->input('message_id'),
            'subject'=>$request->input('subject'),
            'message'=>$request->input('message'),
        ]);
        try{
            $body=file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../resources/views/admin/email/reply.html');
            $body=str_replace("<%NAME%>",ucfirst($request->input('name')),$body);
            $body=str_replace("<%MESSAGE%>",$request->input('message'),$body);
            $mail = new PHPMailer(true);

            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.zoho.com';
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true;
            $mail->Username = Config::get('app.company')->contact_email;
            $mail->Password = Config::get('app.company')->contact_password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom(Config::get('app.company')->contact_email,'Opined Support Team');
            $mail->addReplyTo(Config::get('app.company')->contact_email, 'Opined');

            //Content
            $mail->Subject =  $request->input('subject');
            $mail->msgHTML($body);
            $mail->isHTML(true);
            $mail->addAddress($request->input('email'),$request->input('name'));
            if(!$mail->send()) {  $reply->update(['email_sent'=>0]);} else {  $reply->update(['email_sent'=>1]);}
            $mail->clearAddresses();
            $mail->ClearAllRecipients();
            return redirect()->back();
         }
        catch(\Exception $e){
            $reply->update(['email_sent'=>0]);
            return redirect()->back();
        }
    }

    public function showStarredMessage(Request $request){
        $section='starred';
        $messages=Message::where('starred',1)->with('reply')->orderBy('created_at','desc')->paginate(20);
        if($request->ajax()){
            $view = (String) view('admin.dashboard.messages.message_row',compact('section','messages'));
            return response()->json(['html'=>$view]);
        }else{
        return view('admin.dashboard.messages.index',compact('section','messages'));
        }
    }

    public function showUnreadMessages(Request $request){
        $section='unread';
        $messages=Message::where('mark_read',0)->with('reply')->orderBy('created_at','desc')->paginate(20);
        if($request->ajax()){
            $view = (String) view('admin.dashboard.messages.message_row',compact('section','messages'));
            return response()->json(['html'=>$view]);
        }else{
        return view('admin.dashboard.messages.index',compact('section','messages'));
        }
    }

    public function showReadMessages(Request $request){
        $section='read';
        $messages=Message::where('mark_read',1)->with('reply')->orderBy('updated_at','desc')->paginate(20);
        if($request->ajax()){
            $view = (String) view('admin.dashboard.messages.message_row',compact('section','messages'));
            return response()->json(['html'=>$view]);
        }else{
        return view('admin.dashboard.messages.index',compact('section','messages'));
        }
    }

    public function markAsReadMessage(Request $request){
        Message::where('id',$request->input('id'))->update(['mark_read'=>1]);
        return redirect()->back();
    }

    public function markAllReadMessage(){
        Message::update(['mark_read'=>1]);
        return redirect()->back();
    }

    public function deleteMessages(){
        Message::delete();
        MessageReply::delete();
        return redirect()->back();
    }

    public function deleteMessageById(Request $request){
        Message::where('id',$request->input('id'))->delete();
        MessageReply::where('message_id',$request->input('id'))->delete();
        return redirect()->back();
    }


}
