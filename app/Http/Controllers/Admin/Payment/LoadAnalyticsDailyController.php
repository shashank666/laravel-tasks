<?php

namespace App\Http\Controllers\Admin\Payment;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Analytics;
use Spatie\Analytics\Period;
use App\Model\AnalyticsData;
use App\Model\Post;
use App\Model\RsmUserPost;
use App\Model\UserEarning;
use App\Model\Monetisation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoadAnalyticsDailyController extends Controller
{

   public function loadAdsenseDaily(){
   // $startDate = Carbon::now()->subday(2);
   // $endDate = Carbon::now()->subday();
    $startDate = Carbon::now()->yesterday();
    $endDate = Carbon::now()->yesterday()->endOfDay();
    // //echo $startDate;
    $data1 = Analytics::performQuery(
    Period::create($startDate, $endDate),
     'ga:sessions',
     [
     'metrics' => 'ga:adsenseRevenue,ga:adsenseAdUnitsViewed,ga:adsenseAdsViewed,ga:adsenseAdsClicks,ga:adsensePageImpressions,ga:adsenseCTR,ga:adsenseECPM,ga:adsenseExits,ga:adsenseViewableImpressionPercent,ga:adsenseCoverage',
         'dimensions' => 'ga:pagePath,ga:dateHourMinute',
     ]
 );
//Storage::put('3.txt', $data1->rows[0]);
for ( $reportIndex = 0; $reportIndex < count($data1->rows); $reportIndex++ ) {
    if (Str::startsWith(($data1->rows)[$reportIndex][0], '/opinion/')){
        // //echo $reportIndex;
         AnalyticsData::create([
         'pagePath'=>($data1->rows)[$reportIndex][0],
     'dateHourMinute'=>date('Y-m-d h:i', strtotime(($data1->rows)[$reportIndex][1])),
     'adsenseRevenue'=>($data1->rows)[$reportIndex][2],
     'adsenseAdUnitsViewed'=>($data1->rows)[$reportIndex][3],
     'adsenseAdsViewed'=>($data1->rows)[$reportIndex][4],
     'adsenseAdsClicks'=>($data1->rows)[$reportIndex][5],
     'adsensePageImpressions'=>($data1->rows)[$reportIndex][6],
     'adsenseCTR'=>($data1->rows)[$reportIndex][7],
     'adsenseECPM'=>($data1->rows)[$reportIndex][8],
     'adsenseExits'=>($data1->rows)[$reportIndex][9],
     'adsenseViewableImpressionPercent'=>($data1->rows)[$reportIndex][10],
     'adsenseCoverage'=>($data1->rows)[$reportIndex][11]
         ]);
        sleep(0.2);
        
   }
    
}
     $adsense_post = AnalyticsData::whereBetween('dateHourMinute', [Carbon::now()->yesterday(),Carbon::now()->yesterday()->endOfDay()])->get();
     foreach ($adsense_post as $index => $adsense) {
     $post = Post::where(['is_active'=>1,'slug'=>ltrim($adsense->pagePath,"/opinion/")])->first();
     if($post){
         $rsm_user_post = RsmUserPost::where(['post_id'=>$post->id])->first();
         if($rsm_user_post){
             $total = $rsm_user_post->total_revenue + $adsense->adsenseRevenue;
             RsmUserPost::where(['post_id'=>$post->id])->update(['total_revenue'=>$total]);
             $check_monetisation = Monetisation::where(['post_id'=>$post->id,'is_monetised'=>1])->first();
             if($check_monetisation){
             RsmUserPost::where(['post_id'=>$post->id])->update(['money'=> $rsm_user_post->money + ($adsense->adsenseRevenue)/2]);
             $user_earning = UserEarning::where(['user_id'=>$post->user_id])->first();
                 if($user_earning){
                     $total_earning = $user_earning->total_earning + ($adsense->adsenseRevenue)/2;
                     UserEarning::where(['user_id'=>$post->user_id])->update(['total_earning'=>$total_earning]);
                     if(($user_earning->total_earning-$user_earning->total_paid)>=20){
                         UserEarning::where(['user_id'=>$post->user_id])->update(['threshold'=>1]);
                     }
                     else{
                        UserEarning::where(['user_id'=>$post->user_id])->update(['threshold'=>0]);
                     }
                 }
                 else{
                     $user_earning=new UserEarning();
                     $user_earning->user_id=$post->user_id;
                     $user_earning->total_earning=$total/2;
                     $user_earning->save();
                 }
             }
         }
         else{
             $create_rsm=new RsmUserPost();
             $create_rsm->post_id=$post->id;
             $create_rsm->user_id=$post->user_id;
             $create_rsm->total_revenue=$adsense->adsenseRevenue;
             $create_rsm->money=$adsense->adsenseRevenue/2;
             $create_rsm->save();
             $check_monetisation = Monetisation::where(['post_id'=>$post->id,'is_monetised'=>1])->first();
             if($check_monetisation){
             $user_earning = UserEarning::where(['user_id'=>$post->user_id])->first();
             if($user_earning){
                 $total_earning = $user_earning->total_earning + ($adsense->adsenseRevenue)/2;
                 UserEarning::where(['user_id'=>$post->user_id])->update(['total_earning'=>$total_earning]);
             }
             else{
                 $user_earning=new UserEarning();
                 $user_earning->user_id=$post->user_id;
                 $user_earning->total_earning=$adsense->adsenseRevenue/2;
                 $user_earning->save();
             }
         }
             }
         }
     }
    
   // return response()->json($post);
  }  

}
