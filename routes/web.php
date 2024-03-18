<?php


Route::group(['namespace' => 'Frontend\Post'],function () {

    Route::get('/index','PostsController@index');
    // Route::get('/','HomepageController@threads_show')->name('home');
    Route::get('/home','HomepageController@threads_show')->name('homepage');
    // Route::get('/publish','CrudController@publish')->name('publish');
    // Route::get('/article','PostsController@article');
    Route::get('/latest','PostsController@get_latest_posts');
    // Route::get('/contest','ContestController@get_contest_details');
    Route::get('/trending','PostsController@get_trending_posts');
    Route::get('/mostliked','PostsController@get_mostliked_posts');
    Route::get('/circle','PostsController@get_circle_posts');
    Route::get('/interested','PostsController@get_interested_posts');
    Route::post('/rsm_opined','PostsController@updateUserClick')->name('admin.update_user_click');
    
    Route::get('/topics','PostsController@get_all_category_by_group');
    Route::get('/topic/{slug}','PostsController@get_posts_by_category');
Route::post('/user_card','PostsController@get_user_card')->name('get_user_card');;
    // CRUD comments
    Route::group(['prefix' => 'comments'], function() {
        Route::get('/load','CommentsController@load');
        Route::get('/load/replies','CommentsController@replies');
        Route::post('/create','CommentsController@store');
        Route::post('/delete','CommentsController@destroy');
        Route::post('/update','CommentsController@update');
        Route::post('/like','CommentsController@like');
    });
});
Route::group(['namespace' => 'Frontend'],function () {
    Route::get('admin/shashank/7777/message', 'MessageController@showForm')->name('message.form');
Route::post('/message', 'MessageController@store')->name('message.store');
});



Route::get('/','Frontend\Contest\ContestController@get_contest_details')->name('home');

Route::group(['namespace'=>'Frontend\Contest','prefix' => 'contest'], function() {
    // Route::get('/edit/{slug}','CrudController@edit')->name('edit');
    // Route::get('/write','CrudController@create')->name('write');
    // Route::any('/upload','CrudController@upload')->name('upload_image');
    // Route::post('/autosave','CrudController@autosave')->name('autosave');
    // Route::post('/savebefore','CrudController@storebefore')->name('storebefore');
    // Route::post('/save','CrudController@store')->name('store');
    // Route::post('/update','CrudController@update')->name('update');
    // Route::delete('/delete','CrudController@destroy')->name('delete');
    // Route::post('/report','CrudController@report')->name('report');
    // Route::post('/{slug}/likes','CrudController@get_posts_likes')->name('post_likes');
   // Route::post('/{slug}/disagree','CrudController@get_posts_disagree')->name('post_disagree');
    Route::get('/{slug}','CrudController@show')->name('contest')->name('individual-contest');
    Route::get('/dummy/{slug}','CrudController@showReady')->name('blog_post_ready');
});


Route::get('/feed','Frontend\Opinion\FeedController@feed')->name('feed');
Route::post('/opinion_likes_count','Frontend\Opinion\ShortOpinionsController@get_opinion_likes')->name('opinion_likes_count');
Route::post('/opinion_disagree_count','Frontend\Opinion\ShortOpinionsController@get_opinion_disagree')->name('opinion_disagree_count');
Route::post('/share_count_update','Frontend\Opinion\ShortOpinionsController@updateShareCount')->name('share_count_update');

Route::group(['namespace'=>'Frontend\Thread','prefix' => 'threads'], function() {
    Route::get('/','ThreadsController@trending');
    Route::get('/trending','ThreadsController@trending');
    Route::get('/latest','ThreadsController@latest');
    Route::get('/circle','ThreadsController@circle');
    Route::get('/followed','ThreadsController@followed');
});

Route::group(['namespace'=>'Frontend\Polls','prefix' => 'polls'], function() {
    Route::get('/','PollsController@showPolls')->name('polls');
    Route::post('/create','PollsController@create')->name('createPoll');
    Route::get('/{slug}','PollsController@get_polls_individual')->name('individual-poll');
    Route::post('vote/store','PollsController@store')->name('poll-vote');
    Route::delete('/{poll}','PollsController@destroy')->name('deletePoll');
});

Route::group(['namespace'=>'Frontend\Post','prefix' => 'opinion'], function() {
    Route::get('/edit/{slug}','CrudController@edit')->name('edit');
    Route::get('/write','CrudController@create')->name('write');
    Route::any('/upload','CrudController@upload')->name('upload_image');
    Route::post('/autosave','CrudController@autosave')->name('autosave');
    Route::post('/savebefore','CrudController@storebefore')->name('storebefore');
    Route::post('/save','CrudController@store')->name('store');
    Route::post('/update','CrudController@update')->name('update');
    Route::delete('/delete','CrudController@destroy')->name('delete');
    Route::post('/report','CrudController@report')->name('report');
    Route::post('/{slug}/likes','CrudController@get_posts_likes')->name('post_likes');
    // Route::get('/{slug}','CrudController@show')->name('blog_post');
    Route::get('/dummy/{slug}','CrudController@showReady')->name('blog_post_ready');
});


Route::group(['namespace'=>'Frontend\Post','prefix' => 'search'], function() {
    Route::get('/','SearchController@search')->name('search');
    Route::get('/threads','SearchController@search_threads')->name('search_threads');
    Route::get('/users','SearchController@search_users')->name('search_users');
    Route::get('/topics','SearchController@search_topics')->name('search_topics');
});

    Route::group(['namespace'=>'Frontend\FileManager','prefix'=>'file'], function(){
    Route::post('/upload/{event}','FileManagerController@upload')->name('upload');
    Route::post('/load_url/{event}','FileManagerController@upload_by_url')->name('upload.url');

});

    Route::get('/demo/{id}','Frontend\Email\EmailController@demo');
    Route::get('/user/image/{user_id}','Frontend\FileManager\FileManagerController@serve_user_image');

    Route::group(['namespace' => 'Frontend\User', 'prefix' => 'me'],function () {
    Route::get('/profile','ProfileController@index')->name('profile');
    Route::post('/profile','ProfileController@update_bio');
    Route::get('/profile/edit','ProfileController@edit_profile')->name('edit_profile');
    Route::post('/profile/save','ProfileController@update_profile')->name('update_profile');
    Route::post('/profile/save_picture','ProfileController@update_profile_picture')->name('update_profile_picture');
    Route::post('/profile/save_cover','ProfileController@update_cover_picture')->name('update_cover_picture');

    Route::get('/bookmarks', 'MeController@get_bookmarks')->name('bookmarks');
    Route::get('/opinions','MeController@get_my_posts')->name('opinions');
    Route::get('/writer_corner','MeController@article_corner')->name('article_corner');
    Route::get('/drafts','MeController@get_my_drafts')->name('drafts');
    //Route::get('/previewed','MeController@get_my_previewed')->name('previewed');
    //Route::get('/published','MeController@get_my_published')->name('published');
    Route::get('/myallarticles','MeController@get_my_all_articles')->name('myallarticles');
    Route::post('/manage_bookmark', 'MeController@manage_bookmark');
    Route::post('/manage_likes', 'MeController@manage_likes')->name('manage_likes');
    Route::post('/manage_disagree', 'MeController@manage_disagree')->name('manage_disagree');
    Route::post('/manage_follow','MeController@manage_follow')->name('manage_follow');
    Route::post('/manage_category_follow','MeController@manage_category_follow')->name('manage_category_follow');
    Route::get('/in_circle','MeController@get_followers')->name('in_circle');
    Route::get('/circle','MeController@get_following')->name('circle');
    Route::get('/blocked','MeController@get_blocked')->name('blocked');
    Route::get('/performance','MeController@stats')->name('stats');
    Route::get('/article_performance','MeController@articlePerformance')->name('article_performance');
    Route::get('/invoices','MeController@invoice')->name('article_invoices');
    Route::get('/invoice/{billing_id}','MeController@individualInvoice')->name('individual_invoices');
    Route::post('/get_stats','MeController@get_post_stats')->name('get_post_stats');
    // routes/web.php




    Route::get('/notifications','NotificationsController@get_all_notifications_with_pagination')->name('notifications');
    Route::get('/unread_notifications','NotificationsController@get_all_unread_notifications')->name('unread_notifications');
    Route::post('/mark_as_read','NotificationsController@mark_as_read')->name('mark_as_read');
    Route::post('/delete_notifications','NotificationsController@delete_all_notifications')->name('delete_notifications');


    Route::get('/cities','PaymentController@search_cities')->name('search_cities');
    Route::get('/banks','PaymentController@search_banks')->name('search_banks');
    Route::get('/payment','PaymentController@payment_page')->name('show_payment_page');
    Route::get('/payment_show','PaymentController@payment_page_show')->name('payment_show');
    Route::post('/payment/{action}','PaymentController@save_payment')->name('payment');


    Route::get('/settings','SettingsController@settings')->name('settings');
    Route::get('/search_username','SettingsController@search_username')->name('search_username');
    Route::post('/update/username','SettingsController@update_username')->name('update_username');
    Route::post('/update/keywords','SettingsController@update_keywords')->name('update_keywords');
    Route::post('/update/name','SettingsController@update_name')->name('update_name');
    Route::post('/update/email','SettingsController@update_email')->name('update_email');
    Route::post('/update/password','SettingsController@update_password')->name('update_password');
    Route::post('/check_pass','SettingsController@checkPass')->name('checkPass');
    Route::post('/send/email_verification_link','SettingsController@send_me_verification_email')->name('send_email_verification_link');
    Route::get('/verify/{token}','SettingsController@verify_email')->name('verify_email');
    Route::post('/delete_sessions','SettingsController@clear_other_sessions')->name('delete_sessions');
    Route::post('/delete_account','SettingsController@delete_account')->name('delete_account');
    Route::post('/deactivate_account','SettingsController@deactivate_account')->name('deactivate_account');
    Route::post('/activate_account_check','SettingsController@sendOtpActivate')->name('activate_account_check');
    Route::post('/activate_account','SettingsController@activate_account')->name('activate_account');
    Route::post('/add/mobile','SettingsController@add_mobile')->name('add_mobile');
    Route::get('/subscription','SettingsController@subscribe')->name('subscription');
    Route::post('/resend/otp','SettingsController@resendOTP')->name('send_otp_again');
    Route::post('/verify/mobile','SettingsController@verify_mobile')->name('verify_mobile');

});

Route::group(['namespace' => 'Frontend\User','prefix' => '@{username}'], function () {
   Route::get('/','UserController@get_user_profile')->name('user_profile');
   Route::get('/in_circle','UserController@get_user_followers')->name('user_in_circle');
   Route::get('/circle','UserController@get_user_following')->name('user_circle');
   Route::get('/article','UserController@get_user_article')->name('user_article');
   Route::get('/opinions','UserController@get_user_shortopinions')->name('user_opinions');

   //Route::get('/latest','UserController@get_user_latestposts')->name('user_latest_posts');
   //Route::get('/trending','UserController@get_user_trendingposts')->name('user_trending_posts');
   //Route::get('/threads','UserController@get_user_shortopinions')->name('user_thread_opinions');
});


Route::group(['namespace' => 'Frontend\Pages'],function () {
    Route::get('/sitemap','SitemapController@index');
    Route::get('/sitemap/posts','SitemapController@posts');
    Route::get('/sitemap/categories','SitemapController@categories');
    Route::get('/sitemap/threads','SitemapController@threads');
    Route::get('/sitemap/opinions','SitemapController@opinions');
    Route::get('/sitemap/polls','SitemapController@polls');
    Route::get('/sitemap/news','SitemapController@article');

    Route::get('articles/rss','RssController@articles');

    Route::get('/offer','PagesController@offer')->name('offer');
    Route::get('/invitation','PagesController@invitation')->name('invitation');
    Route::get('/contactus','PagesController@contactus')->name('contactus');
    Route::post('/send_message','PagesController@send_message')->name('send_message');
    Route::get('/404','PagesController@error404')->name('404');
    Route::get('/session_expired','PagesController@session_expired')->name('session_expired');
});


Route::group(['namespace' => 'Frontend\Legal','prefix' => 'legal'],function () {
    Route::get('/privacy_policy','LegalController@privacy_policy')->name('privacy_policy');
    Route::get('/copyright_policy','LegalController@copyright_policy')->name('copyright_policy');
    Route::get('/trademark_policy','LegalController@trademark_policy')->name('trademark_policy');
    Route::get('/acceptable_use_policy','LegalController@acceptable_use_policy')->name('acceptable_use_policy');
    Route::get('/writer_terms','LegalController@writer_terms')->name('writer_terms');
    Route::get('/full_terms_of_service','LegalController@full_terms')->name('full_terms');
    Route::get('/terms_of_service','LegalController@terms_of_service')->name('terms_of_service');
    Route::get('/article_guideline','LegalController@article_guideline')->name('article_guideline');
    Route::get('/payment_terms','LegalController@payment_terms')->name('payment_terms');
    Route::get('/dos_donts','LegalController@do_dont')->name('do_dont');
    Route::get('/eligibility_of_rsm','LegalController@eligibility_of_rsm')->name('eligibility_of_rsm');
    Route::get('/offer-rsm','LegalController@offer')->name('offer-rsm');
    Route::get('/lockdown-offer','LegalController@lockdown_offer')->name('lockdown-offer');

});


Route::group(['namespace' => 'Frontend\Opinion', 'prefix' => '@{username}'],function (){
    Route::get('/opinion/{id}/share','ShortOpinionsController@share_opinion_by_id')->name('share_opinion');
    Route::get('/opinion/{id}','ShortOpinionNewController@get_opinion_by_id')->name('opinion');
});

Route::group(['namespace' => 'Frontend\Opinion'],function (){
    Route::post('thread/write','ShortOpinionsController@store')->name('write_short_opinion');
    Route::post('thread/like','ShortOpinionsController@like_thread')->name('like_thread');
    // Route::post('thread/follow','ShortOpinionsController@follow_thread')->name('follow_thread');
    Route::get('thread/{name}','ShortOpinionsController@get_opinions_by_thread')->name('thread');
    Route::get('thread/{name}/trending','ShortOpinionsController@get_opinions_by_thread_trending')->name('thread_trending');
    Route::get('thread/{name}/circle','ShortOpinionsController@get_opinions_by_thread_circle')->name('thread_circle');
    Route::get('thread/opinion/{id}','ShortOpinionNewController@get_opinion_by_id');
    // Route::post('like_opinion','ShortOpinionsController@like_opinion')->name('like_opinion');
    Route::post('Agree_opinion','ShortOpinionsController@Agree_disagree_opinion')->name('Agree_opinion');

    //get opinion by id 
  
    

    Route::delete('delete_opinion','ShortOpinionsController@delete_opinion')->name('delete_opinion');
});

Route::group(['namespace' => 'Frontend\Opinion','prefix'=>'opinion'],function (){
    Route::get('stream/video/{video_name}','ShortOpinionsController@stream_video')->name('stream_video');
    Route::post('/comments/like','ShortOpinionCommentsController@like');
    Route::post('/like','ShortOpinionCommentsController@like_opinion');
    Route::post('/thread/follow','ShortOpinionCommentsController@follow_thread');
    Route::post('/comments/disagree','ShortOpinionCommentsController@disagree');
    Route::post('/comments/create','ShortOpinionCommentsController@store');
    Route::post('/comments/update','ShortOpinionCommentsController@update');
    Route::post('/comments/delete','ShortOpinionCommentsController@destroy');
    Route::get('/comments/load','ShortOpinionCommentsController@load');
    
    Route::get('/comments/load/replies','ShortOpinionCommentsController@replies');

});
Route::get('/opinion/{id}','Frontend\Opinion\ShortOpinionNewController@get_opinion_by_id2');

Route::get('/account/delete', 'Auth\LoginController@deleteAccountForm')->name('account.delete.form');
Route::post('/account/delete', 'Auth\LoginController@deleteAccountForm')->name('account.delete');

Route::get('/login_form','Auth\LoginController@showLoginForm')->name('login_form');
Route::get('/login_form2','Auth\LoginController@showLoginForm2')->name('login_form2');

Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
Auth::routes();
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::get('password/reset/{token}','Auth\ForgotResetPasswordController@showResetForm')->name('password.reset');
Route::get('password/reset/now/{token}','Auth\ForgotResetPasswordController@showResetApiForm')->name('password.reset.form');
Route::post('password/reset/change_password','Auth\ForgotResetPasswordController@resetnow')->name('password.reset.api');
Route::post('password/reset','Auth\ForgotResetPasswordController@reset')->name('password.reset');
Route::post('password/email','Auth\ForgotResetPasswordController@sendLink')->name('password.email')->middleware('guest');


Route::get('/register_form','Auth\RegisterController@showRegistrationForm')->name('register_form');
Route::get('/register_form2','Auth\RegisterController@showRegistrationForm2')->name('register_form2');

Route::post('/create_account','Auth\RegisterController@create_account')->name('create_account');
Route::post('/resendOTP','Auth\RegisterController@requestResendOTP')->name('resend_otp');


Route::group(['namespace' => 'Frontend\User'],function(){
    Route::post('/notification/subscribe','WebPushController@subscribe')->name('webpush_subscribe');
    Route::post('/notification/unsubscribe','WebPushController@unsubscribe')->name('webpush_unsubscribe');
    Route::get('/notification/test/{number}','WebPushController@testing');

});


/*--------------------------  ADMIN ROUTES ---------------------------------*/


Route::group(['namespace' => 'Admin\Tester', 'prefix' => 'cpanel/tester'],function () {
    Route::get('/testOTP','TestController@testOTP')->name('admin.test_send_otp');
    Route::get('/email','TestController@testemail');
});


Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel'],function () {
    Route::get('/','DashboardController@index')->name('admin.dashboard');
    Route::post('/top_categories','DashboardController@topCategories')->name('admin.top_categories');
});
Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/share'],function () {
    Route::get('/','ShareController@index')->name('admin.share');
    Route::get('/{plateform}','ShareController@showByPlateform')->name('admin.show_by_plateform');
    Route::post('/top/top_opinions_byshare','ShareController@topOpinions')->name('admin.top_opinions_byshare');
   
});

Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/android'],function () {
    Route::get('/','AndroidController@index')->name('admin.android_dashboard');
    Route::get('/all','AndroidController@all')->name('admin.android_all');
    Route::get('/devices/{brand}','AndroidController@devices')->name('admin.android_devices');
    Route::get('/devices/{brand}/{model}','AndroidController@byModel')->name('admin.android_device_model');
});

Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/message'],function () {
    Route::get('/show/unread','MessagesController@showUnreadMessages')->name('admin.unread_messages');
    Route::get('/show/read','MessagesController@showReadMessages')->name('admin.read_messages');
    Route::get('/show/starred','MessagesController@showStarredMessage')->name('admin.starred_messages');
    Route::post('star','MessagesController@starMessage')->name('admin.star_message');
    Route::post('reply','MessagesController@sendReplyToUser')->name('admin.send_reply');
    Route::post('/mark_as_read','MessagesController@markAsReadMessage')->name('admin.message_mark_as_read');
    Route::post('/delete/all','MessagesController@deleteMessages')->name('admin.delete_all_messages');
    Route::post('/delete','MessagesController@deleteMessageById')->name('admin.delete_message');
});


Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/settings'],function () {
    Route::get('/','SettingsController@showSettingsPage')->name('admin.settings');
    Route::get('/company','SettingsController@showCompanySettings')->name('admin.company_settings');
    Route::get('/policy','SettingsController@showCompanyPolicySettings')->name('admin.company_policy');
    Route::post('/policy/update/{policy_type}','SettingsController@updateCompanyPolicy')->name('admin.update_policy');

    Route::get('/email','SettingsController@showEmailSettings')->name('admin.email_settings');
    Route::get('/ui','SettingsController@showUISettings')->name('admin.ui_settings');

    Route::post('/ui/adblocker','SettingsController@manageAdblocker')->name('admin.ui.adblocker');
    Route::post('/ui/webpush_notification','SettingsController@manageWebpushNotification')->name('admin.ui.webpush_notification');
    Route::post('/ui/show_google_ad','SettingsController@manageGoogleAd')->name('admin.ui.show_google_ad');
    Route::post('/ui/google_adcode','SettingsController@manageGoogleAdCode')->name('admin.ui.google_adcode');

    Route::post('/ui/invite','SettingsController@managerInviteButton')->name('admin.ui.invite');
    Route::post('/ui/pagination/{field}','SettingsController@managePagination')->name('admin.ui.pagination');
    Route::post('/ui/verification/{field}','SettingsController@manageVerification')->name('admin.ui.verification');
    Route::get('/android','SettingsController@showAndroidSettings')->name('admin.android_settings');
    Route::post('/android/force_logout','SettingsController@forceLogoutAllUsers')->name('admin.android_force_logout');
    Route::post('/android/pagination/{field}','SettingsController@manageAppPagination')->name('admin.app.pagination');

    Route::get('/personal','SettingsController@showPersonalSettings')->name('admin.personal_settings');
    Route::post('/personal/change_password','SettingsController@changePassword')->name('admin.personal.change_password');
    Route::get('/expirepassword','SettingsController@showExpirePassword')->name('admin.resetpassword');
    Route::post('/expirepasswordpost','SettingsController@expirePassword')->name('admin.resetpasswordpost');
});


Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/email'],function () {
    Route::get('/','EmailController@index')->name('admin.email.index');
    Route::get('/find_user','EmailController@find_users');
    Route::post('/upload','EmailController@upload_image')->name('admin.email.upload');
    Route::get('/create','EmailController@create_form')->name('admin.email.create_form');
    Route::get('/preview/{id}','EmailController@preview')->name('admin.email.preview');
    Route::get('/edit/{id}','EmailController@edit')->name('admin.email.edit');
    Route::post('/update','EmailController@update')->name('admin.email.update');
    Route::post('/create','EmailController@create')->name('admin.email.create');
    Route::post('/send','EmailController@schedule_and_send')->name('admin.email.send');
    Route::post('/stop','EmailController@stop')->name('admin.email.stop');
});


Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/push'],function(){
    Route::get('/','PushController@index')->name('admin.push.index');
    Route::post('/send','PushController@send')->name('admin.push.send');
});

Route::group(['namespace' => 'Admin\Payment', 'prefix' => 'cpanel/payment'],function(){
    Route::get('/','PaymentController@index')->name('admin.payment.home');
    Route::get('/payto','PaymentController@showPaymentForRsm')->name('admin.payment.payto');
    Route::post('/payment_success','PaymentController@PaymentSuccess')->name('admin.payment.pay');
    Route::get('/paid_users','PaymentController@showPaidForRsm')->name('admin.payment.paid_users');
    Route::get('/ad_analysis','PaymentController@showAdAnalysis')->name('admin.payment.ad_analysis');
});
    
    
Route::get('/data','Admin\Payment\LoadAnalyticsDailyController@loadAdsenseDaily')->name('admin.loadadsense');


Route::group(['namespace' => 'Admin\Dashboard', 'prefix' => 'cpanel/filemanager'],function () {
    Route::get('/','FileManagerController@index')->name('admin.filemanager.index');
    Route::post('/deleteFile','FileManagerController@deleteFile')->name('admin.filemanager.deleteFile');
});

Route::get('cpanel/write','Admin\Opinion\OpinionCrudController_1@index')->name('admin.write.opinion_new');

Route::get('/cpanel/opinion/create', 'Admin\Opinion\OpinionContentController@index')->name('opinion.create');
Route::post('/cpanel/opinion', 'Admin\Opinion\OpinionContentController@store')->name('admin.store');


Route::group(['namespace' => 'Admin\Category', 'prefix' => 'cpanel/category'],function () {
    Route::get('/all','CategoryController@showCategories')->name('admin.categories');
    Route::get('/add','CategoryController@showAddCategoryForm')->name('admin.add_category');
    Route::post('/create','CategoryController@createCategory')->name('admin.create_category');
    Route::get('/edit/{id}','CategoryController@showEditCategoryForm')->name('admin.edit_category');
    Route::post('/save','CategoryController@updateCategory')->name('admin.save_category');
    Route::post('/delete','CategoryController@deleteCategory')->name('admin.delete_category');
});

Route::group(['namespace' => 'Admin\Poll', 'prefix' => 'cpanel/poll'],function () {
    Route::get('/','PollController@index')->name('admin.poll_home');
    Route::get('/all','PollController@showPolls')->name('admin.polls');
    Route::get('/votes','PollController@showPollVotes')->name('admin.poll_votes');
    Route::get('/show/{id}','PollController@individualPoll')->name('admin.individual_poll');
    Route::get('/add-poll-type','PollController@showAddPollTypeForm')->name('admin.show_add_poll_type');
    Route::get('/select-type','PollController@showSelectPollType')->name('admin.show_select_poll_type');
    Route::post('/add-poll','PollController@showAddPollForm')->name('admin.show_add_poll');
    Route::post('/create-polltype','PollController@createPollType')->name('admin.add_poll_type');
    Route::post('/create-poll','PollController@createPoll')->name('admin.add_poll');
    Route::get('/visibility/{id}','PollController@pollVisibility')->name('admin.poll_visibility');
    Route::get('/edit/{id}','PollController@showEditPollForm')->name('admin.poll_edit');
    Route::post('/update-poll','PollController@updatePoll')->name('admin.update_poll');
});

Route::group(['namespace' => 'Admin\Thread', 'prefix' => 'cpanel/thread'],function () {
    Route::get('/all','ThreadsController@showThreads')->name('admin.threads');
    Route::get('/add','ThreadsController@showAddThreadForm')->name('admin.add_thread');
    Route::post('/create','ThreadsController@createThread')->name('admin.create_thread');
    Route::get('/edit/{id}','ThreadsController@showEditThreadForm')->name('admin.edit_thread');
    Route::post('/save','ThreadsController@updateThread')->name('admin.save_thread');
    Route::post('/manage_visibility','ThreadsController@manageVisibilityThread')->name('admin.thread_visibility');
    Route::post('/delete','ThreadsController@deleteThread')->name('admin.delete_thread');
    Route::post('view/{id}/update','ThreadsController@updateOpinionVisibility')->name('admin.update_opinion_visibility');
    Route::post('view/{id}/delete','ThreadsController@deleteOpinion')->name('admin.delete_opinion');

});


Route::group(['namespace' => 'Admin\Opinion', 'prefix' => 'cpanel/opinion'],function () {
    Route::get('/all','OpinionsController@index')->name('admin.opinions');
    Route::get('/trending','OpinionsController@trending')->name('admin.opinions.trending');
    Route::get('/updated','OpinionsController@latest_updated')->name('admin.opinions.updated');
    Route::get('/write','OpinionCrudController@index')->name('admin.write.opinion');
    Route::post('thread/write','OpinionCrudController@store')->name('admin.write_short_opinion');
    Route::post('thread/write','OpinionContentController@store')->name('admin.write_short_opinion2');
    Route::get('/desable','OpinionsController@showDesableOpinion')->name('admin.desable_opinions');
    Route::post('/comments/like','OpinionsCommentController@like');
    Route::post('/comments/disagree','OpinionsCommentController@disagree');
    Route::post('/comments/create','OpinionsCommentController@store');
    Route::post('/comments/update','OpinionsCommentController@update');
    Route::post('/comments/delete','OpinionsCommentController@destroy');
    Route::post('/comments/desable','OpinionsCommentController@desable');
    Route::post('/comments/enable','OpinionsCommentController@enable');
    Route::get('/comments/load','OpinionsCommentController@load');
    Route::get('/comments/load/replies','OpinionsCommentController@replies');
    Route::post('/like_opinion','OpinionsController@like_opinion')->name('like_opinion');
    Route::post('/disagree_opinion','OpinionsController@disagree_opinion')->name('disagree_opinion');

    Route::post('/opinion_likes','OpinionsController@get_opinion_likes')->name('opinion_likes');
    Route::post('/opinion_disagree','OpinionsController@get_opinion_disagree')->name('opinion_disagree');
    Route::post('/opinion_shares','OpinionsController@get_opinion_shares')->name('opinion_shares');

    Route::get('/lockdownoffer','OpinionsController@lockdown_offer')->name('admin.opinions.lockdown_offer');
});

Route::group(['namespace' => 'Admin\Post', 'prefix' => 'cpanel/posts'],function () {

    Route::get('/all','PostsController@showPosts')->name('admin.posts');
    Route::get('/allcomment','PostsController@allComment')->name('admin.posts_allcomment');
    Route::get('/desablecomment/{post_id}/{comment_id}','PostsController@desableComment')->name('admin.desable_comment');
    Route::get('/enablecomment/{post_id}/{comment_id}','PostsController@enableComment')->name('admin.enable_comment');
    Route::get('/deletecomment/{post_id}/{comment_id}','PostsController@deleteComment')->name('admin.delete_comment');
    Route::get('/view/{id}','PostsController@showBlogPost')->name('admin.blog_post');
    Route::get('/view/{id}/likes','PostsController@showPostLikes')->name('admin.post_likes');
    Route::post('/view/{id}/likes/delete','PostsController@deletePostLikes')->name('admin.delete_post_like');
    Route::post('/view/{id}/likes/update','PostsController@updatePostLikes')->name('admin.update_post_like');

    Route::get('/view/{id}/disagree','PostsController@showPostDisagree')->name('admin.post_disagree');
    Route::post('/view/{id}/disagree/delete','PostsController@deletePostDisagree')->name('admin.delete_post_disagree');
    Route::post('/view/{id}/disagree/update','PostsController@updatePostDisagree')->name('admin.update_post_disagree');

    Route::get('/plagiarism_check/{id}','PostsController@check_for_plagiarism')->name('admin.check_for_plagiarism');
    Route::get('/view_plagiarism/{id}','PostsController@view_plagiarism')->name('admin.view_plagiarism');
    Route::get('/plagiarised_checked/{id}/{plagiarised}/{plagiarism_average}','PostsController@plagiarised_checked')->name('admin.plagiarised_checked');
    Route::post('/monetisation','PostsController@manageMonetisation')->name('admin.monetisation');


    Route::get('/plagiarism_checkv/','PostsController@check_for_plagiarismv3')->name('admin.check_for_plagiarismv3');
    

    Route::get('/write','PostsController@showWritePostForm')->name('admin.write_post');
    Route::post('/create','PostsController@createPost')->name('admin.create_post');
    Route::get('/edit/{id}','PostsController@showEditPostForm')->name('admin.edit_post');
    Route::post('/upload/{id}','PostsController@uploadPostImage')->name('admin.upload_post_cover');
    Route::post('/save','PostsController@updatePost')->name('admin.save_post');
    Route::post('/manage_visibility','PostsController@manageVisibilityPost')->name('admin.post_visibility');
    Route::post('/delete','PostsController@deletePost')->name('admin.delete_post');

    Route::get('/issues','PostsController@reportIssues')->name('admin.report_issues');
    Route::post('/issues/close','PostsController@closeReportedIssues')->name('admin.close_issue');
    Route::post('/issues/delete','PostsController@deleteReportedIssues')->name('admin.delete_issue');
    Route::post('/issues/delete_all','PostsController@deleteAllReportedIssues')->name('admin.delete_all_issues');


    Route::get('/offer','PostsController@showOfferEligiblePosts')->name('admin.offer_posts');
    Route::post('/offer/delete','PostsController@deleteOfferEligiblePosts')->name('admin.delete_offer_posts');
    Route::post('/offer_payment','PostsController@sendPaymentMailToUser')->name('admin.send_payment_mail');


    Route::post('/add_fake_likes','PostsController@add_fake_likes')->name('admin.add_fake_likes');
    Route::post('/remove_fake_likes','PostsController@remove_fake_likes')->name('admin.remove_fake_likes');
    Route::post('/remove_all_fake_likes','PostsController@remove_all_fake_likes')->name('admin.remove_all_fake_likes');

    Route::post('/add_fake_disagree','PostsController@add_fake_disagree')->name('admin.add_fake_disagree');
    Route::post('/remove_fake_disagree','PostsController@remove_fake_disagree')->name('admin.remove_fake_disagree');
    Route::post('/remove_all_fake_disagree','PostsController@remove_all_fake_disagree')->name('admin.remove_all_fake_disagree');

    Route::post('/check_for_eligibility','PostsController@check_for_eligibility')->name('admin.check_for_eligibility');

});

Route::group(['namespace' => 'Admin\Employee', 'prefix' => 'cpanel/employee'],function () {
    Route::get('/','AdminEmployeeController@showEmployees')->name('admin.administration');
    Route::get('/add','AdminEmployeeController@showAddEmployeeForm')->name('admin.EmployeeForm');
    Route::post('/addemployee','AdminEmployeeController@addEmployee')->name('admin.create_employee');
    Route::get('/editemployee/{id}','AdminEmployeeController@showEditEmployeeForm')->name('admin.edit_employee');
    Route::post('/save','AdminEmployeeController@updateEmployee')->name('admin.save_employee');
    Route::get('/deleteemployee/{id}','AdminEmployeeController@deleteEmployee')->name('admin.delete_employee');
    Route::get('/desable/{id}','AdminEmployeeController@desablePanel')->name('admin.desable_panel');
    Route::get('/invite/{id}','AdminEmployeeController@send_invitation')->name('admin.send_invitation');
    
});
    
Route::group(['namespace' => 'Admin\Employee', 'prefix' => 'employee'],function () {
    Route::get('/activation/{token}','EmployeeController@activationPanel')->name('admin.activation_panel');
    Route::post('/setpassword','EmployeeController@checkInfoPanel')->name('admin.check_info_panel');
    Route::post('/panelsetup','EmployeeController@createPanel')->name('admin.create_panel');
    Route::post('/resendkey','EmployeeController@resendKey')->name('admin.resend_key');


    }); 

    Route::get('/community/join/{id}', function () {
        return redirect('https://play.google.com/store/apps/details?id=com.app.weopined&hl=en_IN&gl=US');
    });

Route::group(['namespace' => 'Admin\User', 'prefix' => 'cpanel/user'],function () {

    Route::get('/admins','UsersController@adminView')->name('admin.adminlist');
    Route::get('/enableSuperAdmin/{id}','UsersController@enableSuperAdmin')->name('admin.enableSuperAdmin');
    Route::get('/desableSuperAdmin/{id}','UsersController@desableSuperAdmin')->name('admin.desableSuperAdmin');

    Route::get('/all','UsersController@showUsers')->name('admin.users');
    Route::get('/userslocation','UsersController@showUsersByLocation')->name('admin.userslocation');
    Route::get('/deleted','UsersController@showDeletedUsers')->name('admin.deleted_users');
    Route::get('/deleted/download','UsersController@downloadDeleted')->name('admin.deleted_download');
    Route::get('/user/download','UsersController@downloadUsers')->name('admin.users_download');

    Route::get('/writers','UsersController@registeredWriters')->name('admin.writers');
    Route::get('/writers/download','UsersController@downloadWriters')->name('admin.writers_download');

    Route::get('/{id}','UsersController@showUserDetailsById')->name('admin.user_details');
    Route::get('/{id}/email','UsersController@showUserEmailForm')->name('admin.user_email');

    Route::post('/block/{id}','UsersController@blockUserAccount')->name('admin.block_user_account');
    Route::post('/unblock/{id}','UsersController@unblockUserAccount')->name('admin.unblock_user_account');
    Route::post('/delete/{id}','UsersController@deleteUserAccount')->name('admin.delete_user_account');

    Route::post('/send_email','UsersController@sendEmailToUser')->name('admin.send_user_email');
});


Route::group(['namespace' => 'Admin\Auth', 'prefix' => 'cpanel'],function () {
    // Admin Authentication Routes
    Route::get('/login','AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login','AdminLoginController@login');
    Route::post('/logout', 'AdminLoginController@logout')->name('admin.logout');

    //Admin Registration Routes
    Route::get('/register','AdminRegisterController@showRegisterForm')->name('admin.register');
    Route::post('/register', 'AdminRegisterController@store');

    // Password Reset Routes
    Route::get('/forgot-password','AdminForgotPasswordController@showForgotPassword')->name('admin.forgot-password');
    Route::post('/forgot-password', 'AdminForgotPasswordController@sendResetLinkEmail')->name('admin.send-reset-email');
    Route::get('/reset/{token}','AdminForgotPasswordController@resetCheck')->name('admin.reset_auth');
    Route::post('/resetkey','AdminForgotPasswordController@resetKey')->name('admin.reset_key');
    Route::post('/resetpassword','AdminForgotPasswordController@resetPassword')->name('admin.reset_password');

   // Route::get('/password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.reset-password');
   // Route::post('/password/reset', 'Auth\AdminResetPasswordController@reset');
});

Route::get('/image-upload', 'ImageUploadController@image_upload')->name('image.upload');
Route::post('/image-upload', 'ImageUploadController@upload_post_image')->name('upload.post.image');


Route::get('cpanel/write_login','Admin\Auth\AdminLoginController_1@showLoginForm')->name('admin.login_1');
Route::post('cpanel/write_login','Admin\Auth\AdminLoginController_1@login_1');
/*--------------------------END OF ADMIN ROUTES ------------------------------*/

Route::group(['namespace' => 'Tester','prefix'=>'cpanel/tester'],function(){
    Route::get('/','TesterController@dashboard');
    Route::get('/nexemo','TesterController@nexemo');
    Route::post('/nexemo','TesterController@nexemo_sendsms');
});


    Route::get('/logintester','Tester\HomeController@home');

    Route::get('authtest/{provider}', 'Tester\LogintestController@redirectToProvider');
    Route::get('authtest/{provider}/callback', 'Tester\LogintestController@handleProviderCallback');
    Auth::routes();
