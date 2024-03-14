<?php

namespace App\Http\Controllers\Api\Pages;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Config;

use Mail;
use App\Mail\Support\ContactUsMail;

use App\Model\Message;


class PagesController extends Controller
{
    public function contact_us(Request $request){
        try{
            $validator = Validator::make($request->all(),[
                'name'=>'required|string|max:255',
                'email'=>'required|string|email|max:255',
                'subject'=>'required',
                'message'=>'required'
            ]);

            if ($validator->fails()) {
                $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
                return response()->json($response, 200);
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
                    $response=array('status'=>'success','result'=>1,'message'=>'Your message has been successfully sent .');
                    return response()->json($response,200);
                }
                catch(\Exception $e){
                    $response=array('status'=>'error','result'=>0,'errors'=>'Failed to send message , please try again later.','extra'=>$e);
                    return response()->json($response,200);
                }
            }
         }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function about_us(Request $request){
        try{
            $data=Config::get('app.company')->aboutus;
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function header_testing(Request $request){
        $app_version=$request->headers->has('app-version')?$request->header('app-version'):'none';
        return response()->json(array('status'=>'success','header'=>$app_version),200);
    }

}
