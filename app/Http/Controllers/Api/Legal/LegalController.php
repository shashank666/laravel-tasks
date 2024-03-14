<?php

namespace App\Http\Controllers\Api\Legal;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Config;

class LegalController extends Controller
{
    public function privacy_policy(){
        try{
            $data=config('app.company')->privacy_policy;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function copyright_policy(){
        try{
            $data=config('app.company')->copyright_policy;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function trademark_policy(){
        try{
            $data=config('app.company')->trademark_policy;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function acceptable_use_policy(){
        try{
            $data=config('app.company')->acceptable_use_policy;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function writer_terms(){
        try{
            $data=config('app.company')->writers_terms;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function full_terms(){
        try{
            $data=config('app.company')->full_terms_of_service;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function terms_of_service(){
        try{
            $data=config('app.company')->terms_of_service;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

    public function article_guideline(){
        try{
            $data=config('app.company')->article_guideline;
            $data=strip_tags($data,'<h1><h2><h3><h4><h5><h6><span><ul><ol><li><p><strong><u><br>');
            $response=array('status'=>'success','result'=>1,'data'=>$data);
            return response()->json($response,200);
        }
        catch(\Exception $e){
            $response=array('status'=>'error','result'=>0,'errors'=>'Internal Server Error');
            return response()->json($response, 500);
        }
    }

}
