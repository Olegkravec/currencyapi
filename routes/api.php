<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function () {
    Route::post('/signin', 'API\UserResourceController@signin')->name('user.signin');
    Route::resource("users", "API\UserResourceController");
    Route::get('/subscriptions/plans', 'API\SubscriptionsResourceController@getPlans')->name('subscriptions.getPlans')->middleware("auth:api");
    Route::resource("subscriptions", "API\SubscriptionsResourceController")->middleware("auth:api");
    Route::get('/payments/create', 'API\PaymentsAPIController@create')->name('payments.create')->middleware("auth:api");
    Route::post('/payments/methods', 'API\PaymentsAPIController@storeMethod')->name('payments.storeMethod')->middleware("auth:api");

    Route::get('/currencies', 'API\CurrenciesController@getAll')->name('currencies.all');
    Route::get('/currencies/{pair}', 'API\CurrenciesController@getPair')->name('currencies.pair');
});


