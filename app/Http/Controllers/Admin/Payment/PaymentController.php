<?php

namespace App\Http\Controllers\Admin\Payment;
use Illuminate\Support\Str;

include_once($_SERVER['DOCUMENT_ROOT'].'/../vendor/copyleaks/php-plagiarism-checker/autoload.php');
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

use App\Model\Category;
use App\Model\Post;
use App\Model\OfferPost;
use App\Model\Like;
use App\Model\Thread;
use App\Model\Keyword;
use App\Model\PostThreads;
use App\Model\CategoryThread;
use App\Model\ShortOpinion;
use App\Model\ShortOpinionComment;
use App\Model\ShortOpinionLike;
use App\Model\ThreadOpinion;
use App\Model\ReportPost;
use App\Model\Comment;
use App\Model\Bookmark;
use App\Model\User;
use App\Model\Follower;
use App\Model\Notification;
use App\Model\CommentLike;
use App\Model\ArticleStatus;
use App\Model\ArticlePlagiarism;
use App\Model\Monetisation;
use App\Model\UserEarning;
use App\Model\UserInvoice;
use App\Model\RsmUserPost;
use DB;
use Session;
use Carbon\Carbon;
use App\Http\Helpers\MailJetHelper;

use Copyleaks\CopyleaksCloud;
use Copyleaks\CopyleaksProcess;

use Mail;
use App\Mail\OfferPost\PlagiarismMail;
use App\Mail\OfferPost\PaymentMail;

class PaymentController extends Controller
{


    public function __construct()
    {   
        $this->middleware('auth:admin');
        View::share('menu','rsm');
    }



    public function index(Request $request){
         $user_earnings=UserEarning::where(['is_active'=>1])->with('user','user_account')->orderBy('total_earning','desc')->paginate(50);
         $to_pay=UserEarning::where(['is_active'=>1,'threshold'=>1])->with('user','user_account')->orderBy('total_earning','desc')->count();
        if($request->has('json') && $request->query('json')==1){
            return response()->json($user_earnings);
        }
        return view('admin.dashboard.payment.index',compact('user_earnings','to_pay'));
    }

    public function showAdAnalysis(Request $request){
         $user_earnings=RsmUserPost::where(['is_active'=>1])->with('user','post')->groupBy('post_id')->orderBy('updated_at','desc')->paginate(50);
         $to_pay=UserEarning::where(['is_active'=>1,'threshold'=>1])->with('user','user_account')->orderBy('total_earning','desc')->count();
        if($request->has('json') && $request->query('json')==1){
            return response()->json($user_earnings);
        }
        return view('admin.dashboard.payment.ad_analysis',compact('user_earnings','to_pay'));
    }

     public function showPaymentForRsm(Request $request){
         $user_earnings=UserEarning::where(['is_active'=>1,'threshold'=>1])->with('user','user_account')->orderBy('total_earning','desc')->paginate(50);
         if($request->has('json') && $request->query('json')==1){
            return response()->json($user_earnings);
        }
         return view('admin.dashboard.payment.topay',compact('user_earnings'));
     }

     public function PaymentSuccess(Request $request){
        $user_id=$request->input('user_pay');
        $ammount=$request->input('paid_ammount');
        $payment_refrence_number=$request->input('payment_refrence_number');
        $earning_data = UserEarning::where(['user_id'=>$user_id])->first();
        
        $stamp = date("ym-dh-is");
        $random_id_length = 4;
        $characters = '0123456789';
          $randomString = '';
          for ($i = 0; $i < $random_id_length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
          }
        $rndid = $randomString;
        $billing_id = $stamp ."-". $rndid;
        $paid_ammount=new UserInvoice();
        $paid_ammount->user_id=$user_id;
        $paid_ammount->paid=$ammount;
        $paid_ammount->payment_refrence_number=$payment_refrence_number;
        $paid_ammount->billing_id=$billing_id;
        $paid_ammount->save();
        $user=User::where('id',$user_id)->first();
        UserEarning::where(['user_id'=>$user_id])->update(['total_paid'=>$earning_data->total_paid + $ammount, 'threshold'=>0]);
        try{
            //Mail::send(new AccountCreatedMail($user,$user->verify_token));
            $mailJET=new MailJetHelper();
            $mailJET->send_successfull_payment_mail($user,$ammount);
        }
        catch(\Exception $e){}
        if($request->ajax()){
                return response()->json(array('status'=>'success','total_paid'=>$earning_data->total_paid + $ammount));
        }else{
                return redirect()->back();
            }
    }

    
    public function showPaidForRsm(Request $request){
         $user_invoices=UserInvoice::where(['is_active'=>1])->with('user','user_earning')->orderBy('created_at','desc')->paginate(50);
         if($request->has('json') && $request->query('json')==1){
            return response()->json($user_invoices);
        }
         return view('admin.dashboard.payment.paid',compact('user_invoices'));
     }


}
