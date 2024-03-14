<?php

namespace App\Repository;
use Carbon\Carbon;
use App\Model\ShortOpinion;
use DB;
use App\Model\ThreadFollower;
use App\Model\Thread;
use App\Model\ThreadOpinion;
use Illuminate\Support\Facades\Auth;


class Homepage
{

    CONST CACHE_KEY='HOMEPAGE';

    public function get_home_thread_opinions()
    {
        $key = "get_home_thread_opinions";
        $cacheKey = $this->getCacheKey($key);
        $from=Carbon::now()->subDays(30);
        $to=Carbon::now();

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($from,$to){
            return ThreadOpinion::where('thread_opinions.is_active',1)
            ->select('thread_id',DB::raw('COUNT(short_opinion_id) AS count'))
            ->whereBetween('thread_opinions.created_at',[$from,$to])
            ->join('threads','threads.id','=','thread_opinions.thread_id')
            ->groupBy('thread_id')
            ->orderBy('count','desc')
            ->take(9)
            ->get();
        });

        
    }

    public function trending_opinions()
    {
        $key = "trending_opinions";
        $cacheKey = $this->getCacheKey($key);
        $trending_from = Carbon::now()->subDays(30);
        $trending_to = Carbon::now();

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() use($trending_from,$trending_to){
            return ShortOpinion::with('user:id,name,username,unique_id,image')
            ->leftJoin('short_opinion_comments', 'short_opinion_comments.short_opinion_id', '=', 'short_opinions.id')
            ->whereBetween('short_opinion_comments.created_at',[$trending_from,$trending_to])
            ->leftJoin('shares', 'shares.short_opinion_id', '=', 'short_opinions.id')
            //->whereBetween('shares.shared_at',[$trending_from,$trending_to])
            ->leftJoin('short_opinion_likes', 'short_opinion_likes.short_opinion_id', '=', 'short_opinions.id')
            ->whereBetween('short_opinion_likes.liked_at',[$trending_from,$trending_to])
           // ->whereBetween('short_opinions.created_at',[$from,$to])
            ->where('short_opinions.is_active',1)
            ->select('short_opinions.*', DB::raw('(COUNT(short_opinion_comments.id)) + (COUNT(shares.id)) + (COUNT(short_opinion_likes.id)) as count'))
            ->groupBy('short_opinions.id')
            ->orderBy('count','desc')
            ->take(20)->get();
        });
        
    }
    public function latest_opinions()
    {
        # code...
        $key = "latest_opinions";
        $cacheKey = $this->getCacheKey($key);
       

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
            return ShortOpinion::where(['is_active'=>1])->with('user:id,name,username,unique_id,image')->orderBy('created_at','desc')->take(20)->get();
        });

    }
    public function followed_threads()
    {
        # code...
        $key = "followed_threads";
        $cacheKey = $this->getCacheKey($key);
       

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
            return ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->with('thread')->orderBy('created_at','desc')->take(6)->get();
        });

    }

    public function followed_threadids()
    {
        # code...
        $key = "followed_threadids";
        $cacheKey = $this->getCacheKey($key);
       

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
            return ThreadFollower::where(['user_id'=>Auth::user()->id,'is_active'=>1])->pluck('thread_id')->toArray();
        });

    }

    public function latest_threads()
    {
        # code...
        $key = "latest_threads";
        $cacheKey = $this->getCacheKey($key);
       

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
            return Thread::where('is_active',1)->withCount('comment','opinions')->has('opinions', '>', 0)->orderBy('created_at','desc')->take(6)->get();
        });

    }

    public function trending_threads()
    {
        # code...
        $key = "trending_threads";
        $cacheKey = $this->getCacheKey($key);
       

        return cache()->remember($cacheKey,Carbon::now()->addMinutes(5),function() {
            return Thread::whereBetween('created_at',[Carbon::now()->subDays(120),Carbon::now()])->where('is_active',1)->withCount('opinions')->has('opinions', '>', 2)->orderBy('opinions_count','desc')->take(6)->get();
        });

    }

    public function getCacheKey($key)
    {
        $key = strtoupper($key);

        return self::CACHE_KEY. ".$key";
    }
}
