<?php

namespace App\Http\Controllers\Frontend\Pages;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Model\Message;

use Mail;
use App\Mail\Support\ContactUsMail;


class PagesController extends Controller
{
    public function __construct()
    {

    }


    public function offer(){
        return view('frontend.pages.offer_not_available');
    }

    public function invitation(){
        if(Auth::check()){
           return redirect('/');
        }else{
           return view('frontend.pages.invitation');
        }
    }

     public function contactus(){
        return view('frontend.pages.contact');
     }

     public function error404(){
         return view('frontend.pages.404');
     }

     public function session_expired(){
        return view('frontend.pages.token_expired');
     }

     public function send_message(Request $request){
		 //dd($request);
        $this->validate($request,[
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255',
            'subject'=>'required',
            'message'=>'required'
        ]);
        $device = $request->input('device');
        if($device != null){
            return redirect()->back()->with(['message'=>'Your message has been successfully sent .']);
        }else{
            $message=new Message([
            'name' => $request->input('name'),
            'email' =>  $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
        ]);
        $message->save();
        try{
            Mail::send(new ContactUsMail($message));
         }
        catch(\Exception $e){}

        return redirect()->back()->with(['message'=>'Your message has been successfully sent .']);
        }
     }



}
