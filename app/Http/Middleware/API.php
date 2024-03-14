<?php

namespace App\Http\Middleware;

use Closure;

class API
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public $opined_header='qazwsxedcrfv12345678vfrcdexswzaq87654321';
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Content-Range, Content-Disposition, Content-Description, X-Auth-Token');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Accept', 'application/json');
       
         if(!request()->headers->has('X-opined')){
            return response()->json(array('status'=>'error','result'=>0,'errors'=>'Headers Missing'),401);  
        } 

         if(request()->header('X-opined')!=$this->opined_header){
            return response()->json(array('status'=>'error','result'=>0,'errors'=>'Headers Mismatch'),401);  
        } 

        return $response;
    }
}
