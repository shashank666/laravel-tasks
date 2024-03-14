<?php

namespace App\Http\Controllers\Tester;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Nexmo\Laravel\Facade\Nexmo;
use DB;
use Carbon\Carbon;

class TesterController extends Controller
{

    public function dashboard(){
        return view('tester.dashboard');
    }

    public function nexemo(){
        return view('tester.sms.nexemo');
    }

    public function nexemo_sendsms(Request $request){
        $validator = Validator::make($request->all(),[
            'to' => ['required'],
            'from'=>['required'],
            'text'=>['required','string']
        ]);

        if ($validator->fails()) {
            $response=array('status'=>'error','result'=>0,'errors'=>implode(',', $validator->errors()->all()));
            return response()->json($response, 200);
        }else{
            try{
                $to = preg_replace('/\D+/','',$request->input('to'));
                $message=Nexmo::message()->send([
                    'to'   => $to,
                    'from' => $request->input('from'),
                    'text' => $request->input('text')
                ]);
                $nexemo_response = $message->getResponseData();

                $response=array('status'=>'success','result'=>1,'data'=>$nexemo_response);
                return response()->json($response, 200);
            }catch(\Exeption $e){
                $response=array('status'=>'error','result'=>0,'errors'=> $e);
                return response()->json($response, 500);
            }

        }
    }

}
