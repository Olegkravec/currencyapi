<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/subs', function (){
    $user = \App\User::find(2);

//    $res = $user->newSubscription('default')->create($user->defaultPaymentMethod()->id);
    \Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
    $plans = \Stripe\Plan::all(['active'=>true]);
    dd($plans);
})->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource("users", "Admin\UserResourceController");
    Route::resource("subscriptions", "Admin\SubscriptionsResourceController")->middleware("permission:see subscriptions");
    Route::group(['prefix' => 'users'], function () {
        Route::get('/{id}/permissions', 'Admin\UserPermissionsController@index')->name('users_permission');
        Route::delete('/{user_id}/permissions/{permission_id}', 'Admin\UserPermissionsController@permission_revoke')->name('users_permission_revoke');
        Route::post('/{user_id}/permissions/{permission_id}', 'Admin\UserPermissionsController@permission_create')->name('users_permission_create');
        Route::get('/{user_id}/permissions/grant', 'Admin\UserPermissionsController@permission_grant')->name('users_permission_grant');
    });
    Route::group(['prefix' => 'chats', "middleware" => "permission:can chatting with others"], function () {
        Route::get('/', 'Admin\ChatController@index')->name('chats_index');
        Route::get('/{chat_id}', 'Admin\ChatController@chats_conversion')->name('chats_conversion');
        Route::get('/new/{user_id}', 'Admin\ChatController@new_conversion')->name('new_conversion');
        Route::post("/fire/{room_id}", 'Admin\ChatController@fireMessage')->name('chats_fireMessage');
    });
});

