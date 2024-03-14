<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['namespace' => 'Api\Auth','middleware' => 'api','prefix'=>'auth'],function () {
    Route::post('check_account','AuthController@check_account');
    Route::post('login','AuthController@login');
    Route::post('mobile_login','AuthController@PhoneLogin');
    Route::post('register','AuthController@register');
    Route::post('register2','AuthController@register2');

    Route::post('add_mobile','AuthController@add_mobile');
    Route::post('resend_otp','AuthController@resend_otp');
    Route::post('verify_otp','AuthController@verify_otp');
    Route::post('password/email','AuthController@forgot_password');
    Route::post('social','AuthController@social_auth');
    Route::post('logout','AuthController@logout')->middleware('auth:api');
    Route::post('update_gcm_token','AuthController@update_gcm_token')->middleware('auth:api');
});


Route::group(['namespace' => 'Api\Post','prefix'=>'post'],function () {
    Route::get('index','PostsController@index');
    Route::get('latest','PostsController@get_latest_posts');
    Route::get('trending','PostsController@get_trending_posts');
    Route::get('mostliked','PostsController@get_mostliked_posts');
    Route::get('topics','PostsController@get_all_categories');
    Route::get('topic/{id}','PostsController@get_posts_by_category');
    Route::get('likes/{id}','CrudController@likes');
    Route::get('/read/{id}','CrudController@read');
    Route::get('/read_by_slug/{id}','CrudController@read_by_slug');
    Route::post('/create','CrudController@store');
    Route::get('/edit/{id}','CrudController@edit');
    Route::post('/update','CrudController@update');
    Route::post('/delete','CrudController@destroy');
    Route::post('/report','CrudController@report');

});
Route::group(['namespace' => 'Api\News','prefix'=>'news'],function () {
    Route::get('index','NewsApiController@index');
    Route::get('trending','NewsApiController@trending_headlines');
    Route::get('opinions/{id}','NewsApiController@get_opinions_by_id');
    Route::get('opinions2/{id}','NewsApiController@get_opinions_by_id2');
    Route::get('headline/{id}','NewsApiController@get_news_by_id');
});
Route::group(['namespace' => 'Api\Community','prefix'=>'community'],function () {
    Route::get('index','CommunityCrudController@index');
    Route::get('my_communities','CommunityCrudController@my_communities');
    Route::get('get_members/{community_id}','CommunityController@get_members');
    Route::get('get_opinions/{community_id}','CommunityController@get_opinions');
    Route::get('get_details/{community_id}','CommunityController@get_details');
    Route::get('get_details_uuid/{uuid}','CommunityController@get_details_uuid');
    Route::get('remove_member/{community_id}','CommunityController@remove_member');
    Route::post('/create','CommunityCrudController@store');
    Route::post('/update','CommunityCrudController@update');
    Route::post('/join','CommunityController@join');
});

Route::group(['namespace'=>  'Api\Post','prefix'=>'comment'],function(){
     Route::post('show','CommentsController@show');
     Route::post('replies','CommentsController@replies');
     Route::post('create','CommentsController@store');
     Route::post('update','CommentsController@update');
     Route::post('delete','CommentsController@destroy');
     Route::post('like','CommentsController@like');
     Route::post('report','CommentsController@report');
});


Route::group(['namespace'=>'Api\Opinion','prefix'=>'threads'],function(){
    Route::get('index','OpinionsController@index');
    Route::get('index2','OpinionsController@index2');
    Route::get('index3','OpinionsController@index3');



    Route::get('latest','OpinionsController@latest_threads_with_opinions');
    Route::get('trending','OpinionsController@trending_threads_with_opinions');
    Route::get('circle','OpinionsController@circle_threads_with_opinions');

    
    Route::get('trending_opinions','OpinionsController@get_trending_opinions');
    
    Route::get('/{category_id}','OpinionsController@get_threads_by_category');
    Route::get('/{category_id}/opinion','OpinionsController@get_opinions_by_category');
    Route::get('/{thread_name}/latest','OpinionsController@get_latest_opinions_by_thread_name');
    Route::get('/{thread_name}/trending','OpinionsController@get_trending_opinions_by_thread_name');
    Route::get('/{thread_name}/circle','OpinionsController@get_circle_opinions_by_thread_name');

    Route::post('/like','OpinionsController@like_thread');
    Route::post('/follow','OpinionsController@follow_thread');
    Route::post('/like_opinion','OpinionsController@like_opinion');
    Route::post('/AgreeDisagree_opinion','OpinionsController@Agree_disagree_opinion');

    
});

Route::group(['namespace' => 'Api\Gamification', 'prefix' => 'rewards'], function () {

    // Add the routes for API callbacks in the Gamification Rewards controller
    Route::post('/comment', 'RewardsController@commentEvent');
    Route::post('/post', 'RewardsController@opinionPostingEvent');
    Route::post('/agree', 'RewardsController@agreeEvent');
    Route::get('/total/{user_id}', 'RewardsController@getTotalRewardAmount');
    
    Route::get('/rewards_detail/{user_id}', 'RewardsController@get_rewards_profile');
  //  Route::post('/follow', 'RewardsController@followEvent');

});

Route::group(['namespace'=>'Api\Polls','prefix'=>'Polls'],function(){
    Route::get('index','PollsController@index');
    Route::get('index2','PollsController@index2');
    Route::get('index3','PollsController@index3');

    Route::get('get_user_polls','PollsController@user_polls');
    
    Route::get('get_other_user_polls','PollsController@other_user_polls');
	Route::get('get_polls_individual','PollsController@get_polls_individual');
    Route::post('store', 'PollsController@store');
    Route::post('create', 'PollsController@create_poll');
	
});

Route::group(['namespace'=>  'Api\Polls','prefix'=>'Polls/comment'],function(){
    Route::post('show','PollsCommentsController@show');
    Route::post('replies','PollsCommentsController@replies');
    Route::post('create','PollsCommentsController@store');
    // Route::post('update','PollsCommentsController@update');
    // Route::post('delete','PollsCommentsController@destroy');
    Route::post('like','PollsCommentsController@like');
    Route::post('disagree','PollsCommentsController@disagree');
    // Route::post('report','PollsCommentsController@report');
});


Route::group(['namespace'=>  'Api\Opinion','prefix'=>'opinion'],function(){
    Route::get('feed','OpinionsController@feed');
    Route::get('feed2','OpinionsController@new_feed');
    Route::get('read/{id}','CrudController@read');
    Route::get('read2/{id}','CrudController@read_test');
    Route::get('read_uuid/{id}','CrudController@read_by_uuid');
    Route::get('edit/{id}','CrudController@edit');
    Route::get('likes/{id}','CrudController@likes');
    Route::get('Agree_Disagrees/{id}','CrudController@Agree_Disagrees');	
    Route::get('embed/{id}','CrudController@embed');
    Route::post('create','CrudController@store');
    Route::post('analyze','SentimentAnalysisController@analyzeSentiment');
    Route::post('update','CrudController@update');
    Route::post('delete','CrudController@destroy');
    Route::get('stream/video/{video_name}','CrudController@stream_video');
});

Route::group(['namespace'=>  'Api\Opinion','prefix'=>'opinion/comment'],function(){
    Route::post('show','CommentsController@show');
    Route::post('replies','CommentsController@replies');
    Route::post('create','CommentsController@store');
    Route::post('update','CommentsController@update');
    Route::post('delete','CommentsController@destroy');
    Route::post('like','CommentsController@like');
    Route::post('disagree','CommentsController@disagree');
    Route::post('report','CommentsController@report');
});


Route::group(['namespace' => 'Api\User','prefix'=>'user/{id}'],function (){
    Route::get('profile','UserController@profile');
    Route::get('followers','UserController@followers');
    Route::get('following','UserController@following');
    Route::get('posts/latest','UserController@latest_posts');
    Route::get('posts/trending','UserController@trending_posts');
    Route::get('opinions/latest','UserController@latest_opinions');
    Route::get('profile/points','UserController@get_points');
    Route::get('profile/achievements','UserController@get_achievements');
    Route::get('profile/user_achievements','UserController@get_user_achievements');
    Route::post('profile/update_email_points','UserController@update_email_points');
    Route::post('profile/update_follower_achievement','UserController@update_follower_achievement');
    Route::post('profile/update_following_achievement','UserController@update_following_achievement');
    Route::post('profile/update_dailyPoints_achievement','UserController@update_dailyPoints_achievement');
    Route::post('profile/update_opinion_achievement','UserController@update_opinion_achievement');
    Route::get('profile/user_followed_threads','UserController@user_followed_threads');
    Route::get('profile/is_thread_followed','UserController@is_thread_followed');



    
});

Route::group(['namespace' => 'Api\User','middleware' => 'auth:api','prefix'=>'me'],function () {
    Route::get('profile','ProfileController@get_user_profile');
    //Api route for getting user points
    Route::get('profile/points','ProfileController@get_points');
    //Api route for getting achievements
    Route::get('profile/get_achievements','ProfileController@get_achievements');
    //Api route for getting user achievements
    Route::get('profile/get_user_achievements','ProfileController@get_user_achievements');
    //Api route for updating email verified achievement
    Route::post('profile/update_email_points', 'ProfileController@update_email_points');
    //Api route for updating count of follower achievement
    Route::post('profile/update_follower_achievement', 'ProfileController@update_follower_achievement');
    //Api route for updating following somebody achievement
    Route::post('profile/update_following_achievement', 'ProfileController@update_following_achievement');
    //Api route for updating daily points achievement
    Route::post('profile/update_dailyPoints_achievements', 'ProfileController@update_dailyPoints_achievements');
    //Api route for updating opinion achievement
    Route::post('profile/update_opinions_achievements', 'ProfileController@update_opinions_achievements');
    //API route for categories
    Route::get('profile/get_categories','ProfileController@get_category');

    

    Route::post('profile/update','ProfileController@update_user_profile');
    Route::post('profile/update_image','ProfileController@updateUserImage');
    Route::post('profile/update_cover','ProfileController@upload_and_update_cover');
    Route::post('profile/report','ProfileController@report');
    Route::post('profile/block','ProfileController@block_user');
    Route::post('profile/unblock','ProfileController@unblock_user');



    Route::get('payment/show','PaymentController@get_user_payment');
    Route::post('payment/save','PaymentController@save_user_payment');

    Route::post('email/update','SettingsController@update_email');
    Route::post('email/verification','SettingsController@send_verification_link');
    Route::post('mobile/update','SettingsController@update_mobile');
    Route::post('username/update','SettingsController@update_username');
    Route::post('password/update','SettingsController@update_password');
    Route::post('sessions/clear','SettingsController@clear_sessions');
    Route::post('account/delete','SettingsController@delete_account');

    Route::get('posts','MeController@posts');
    Route::get('drafts','MeController@drafts');
    Route::get('opinions','MeController@opinions');
    Route::get('profile_images','MeController@get_profile_images');
    Route::get('followers','MeController@followers');
    Route::get('following','MeController@following');
    Route::get('bookmarks','MeController@bookmarks');
    Route::get('followed_category','MeController@get_followed_category');

    Route::post('manage_bookmark','MeController@manage_bookmark');
    Route::post('manage_likes','MeController@manage_likes');
    Route::post('manage_follow','MeController@manage_follow');
    Route::post('manage_category_follow','MeController@manage_category_follow');
    Route::post('manage_categories','MeController@manage_categories');
    //new API
    Route::get('MyThreads','MeController@ThreadsIFollow');
    Route::post('unlock','MeController@unlock');

    Route::get('performance','MeController@performance');
    Route::post('performance','MeController@post_performance');

    Route::get('notifications/all','NotificationsController@all_notifications');
    Route::get('notifications/unread','NotificationsController@unread_notifications');
    Route::post('notifications/mark_as_read','NotificationsController@mark_as_read');
    Route::post('notifications/delete','NotificationsController@delete_notification');
    Route::post('notifications/delete_all','NotificationsController@delete_all_notifications');

    Route::post('contacts','ContactsController@store');
    Route::get('contacts/follow','ContactsController@follow');
    Route::get('contacts/invite','ContactsController@invite');
    Route::post('contacts/reject_invite','ContactsController@reject_invite');
    Route::post('contacts/reject_follow','ContactsController@reject_follow');

});



Route::group(['namespace'=>'Api\Search','middleware' => 'api','prefix'=>'search'],function(){
    Route::get('/','SearchController@search');
    Route::get('/category','SearchController@search_category');
    Route::get('/thread','SearchController@search_thread');
    Route::get('/post','SearchController@search_post');
    Route::get('/user','SearchController@search_user');
    Route::get('/bank','SearchController@search_bank');
    Route::get('/city','SearchController@search_city');
});

Route::group(['namespace' => 'Api\Pages','middleware' => 'api','prefix' => 'pages'],function () {
    Route::get('/about_us','PagesController@about_us');
    Route::post('/contact_us','PagesController@contact_us');
});


Route::group(['namespace' => 'Api\Legal','middleware' => 'api','prefix' => 'legal'],function () {
    Route::get('/privacy_policy','LegalController@privacy_policy');
    Route::get('/copyright_policy','LegalController@copyright_policy');
    Route::get('/trademark_policy','LegalController@trademark_policy');
    Route::get('/acceptable_use_policy','LegalController@acceptable_use_policy');
    Route::get('/writer_terms','LegalController@writer_terms');
    Route::get('/full_terms_of_service','LegalController@full_terms');
    Route::get('/terms_of_service','LegalController@terms_of_service');
    Route::get('/article_guideline','LegalController@article_guideline');
});



Route::group(['namespace' => 'Api\Pages','prefix' => 'testing'],function () {
    Route::get('/header','PagesController@header_testing');
});
