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

Route::group(['middleware' => ['auth']], function() {
    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'Admin\UsersController@index')->name('users_index');
        Route::get('/edit/{id}', 'Admin\UsersController@edit')->name('users_edit');
        Route::put('/edit/{id}', 'Admin\UsersController@update')->name('users_update');
        Route::get('/create', 'Admin\UsersController@index')->name('users_create'); // TODO:!+ MethodIsNotImplementedException


        Route::get('/{id}/permissions', 'Admin\UserPermissionsController@index')->name('users_permission');
        Route::delete('/{user_id}/permissions/{permission_id}', 'Admin\UserPermissionsController@permission_revoke')->name('users_permission_revoke');
        Route::post('/{user_id}/permissions/{permission_id}', 'Admin\UserPermissionsController@permission_create')->name('users_permission_create');
        Route::get('/{user_id}/permissions/grant', 'Admin\UserPermissionsController@permission_grant')->name('users_permission_grant');
    });
});

