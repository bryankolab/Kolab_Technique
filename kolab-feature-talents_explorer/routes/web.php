<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => true,
    'verify' => false

]);

// Application
Route::group([
    'namespace' => '\App\Http\Controllers\Admin',
    'middleware' => ['auth']
], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('app_dashboard');

    // Profile
    Route::get('profile', 'UserController@profile');
    Route::post('profile', 'UserController@avatarUpdate');

    // Hub
    Route::get('hub', 'HubController@index')->name('app_hub');

    // Planning
    Route::get('planning', 'PlanningController@index')->name('app_planning');

    // Projects
    Route::get('projects', 'ProjectsController@index')->name('app_projects');
    Route::get('projects/{id}', 'ProjectsController@details')->name('app_project_detail');

    // Talents
    Route::get('talents', 'TalentsController@index')->name('app_talents');

    // File
    Route::get('/project-file/{id}/{action?}', 'AdminController@generateFile')->name('get_file');

    // Talent Explorer
    Route::get('/explorer', 'ExplorerController@index')->name('app_explorer');
    Route::get('/explorer/profile', 'ExplorerController@profile')->name('app_explorer_view_self_profile');
    Route::get('/explorer/profile/{id}', 'ExplorerController@profile')->name('app_explorer_view_profile');
    Route::get('/explorer/membership', 'ExplorerController@membership')->name('app_explorer_membership');
    Route::get('/explorer/messenger', 'ExplorerController@messenger')->name('app_explorer_messenger');

    // QUOTES PAYMENTS
    Route::get('/explorer/messenger/quotes/{quote_id}/checkout', [\App\Http\Controllers\API\Explorer\ExplorerPaymentController::class, 'quotePaymentPage']);
    //Route::post('/explorer/messenger/quotes/{quote_id}/checkout', [\App\Http\Controllers\API\Explorer\ExplorerPaymentController::class, 'handleQuoteCheckout'])->name('explorer.quote.checkout');


});

//Stirpe Payment Checkout
Route::stripeWebhooks('/explorer/stripe/webhook');


/**
 * ROUTE API EXPLORER (Placée ici pour untiliser le middelware WEB pour l'auth des requêtes AJAX)
 * PROVISOIRE
 */
Route::group([
    'namespace' => '\App\Http\Controllers\API\Explorer',
    'as' => 'explorer.',
    'prefix' => 'api/explorer'
],
    function () {
        // Register Routes
        Route::post('register', 'ExplorerRegistrationRestController@create')->name('api_registration_register');

        Route::post('unlock-access', 'ExplorerRegistrationRestController@unlockAccess')->name('api_explorer_unlock');

        // Portfolio Routes
        Route::apiResource('portfolios', 'PortfolioRestController');
    }
);

/**
 * ROUTE API EXPLORER (Placée ici pour untiliser le middelware WEB pour l'auth des requêtes AJAX)
 * PROVISOIRE
 */
Route::group([
    'namespace' => '\App\Http\Controllers\API',
    'prefix' => 'api'
],
    function () {
        // User Routes
        Route::patch('user/password-change', 'UserRestController@passwordChange')->name('api_user_password_change');
        Route::post('user/picture-change', 'UserRestController@updatePicture')->name('api_user_picture_change');
        Route::patch('user', 'UserRestController@update')->name('api_user_update');
    }
);

Route::group([
    'namespace' => '\App\Http\Controllers\API\Explorer',
    'as' => 'api.explorer.missions.',
    'prefix' => 'api/explorer/missions'
],
    function () {
        // PROPOSITION
        Route::patch('propositions/{mission_proposition_id}', 'ExplorerMessengerRestController@patchMissionProposition')->name('patch_mission_proposition');
        Route::post('propositions', 'ExplorerMessengerRestController@postNewMissionProposition')->name('post_mission_proposition');

        // MESSAGES
        Route::get('conversations/{id}/messages', 'ExplorerMessengerRestController@getConversationMessages')->name('conversation_messages_get');
        Route::post('conversations/{conversation_id}/messages', 'ExplorerMessengerRestController@postConversationMessage')->name('conversation_messages_post');

        //QUOTES
        Route::post('conversations/{conversation_id}/quotes', 'ExplorerMessengerRestController@postNewQuote')->name('conversation_quote_post');

        // CONVERSATIONS
        Route::get('conversations', 'ExplorerMessengerRestController@getConversationsList')->name('conversations_list_get');
        Route::patch('conversations/{id}', 'ExplorerMessengerRestController@patchConversation')->name('patch_conversation');

        //MISSIONS
        Route::get('', 'ExplorerMessengerRestController@getClientProposedMission')->name('get_explorer_client_proposed_mission');
    }
);


// Auth
Route::group([
    'namespace' => '\App\Http\Controllers\Auth',
], function () {

    // Logout
    Route::get('logout', 'LoginController@logout')->name('app_logout');
    //Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

});
