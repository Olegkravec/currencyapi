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
        Route::get('/create', 'Admin\UsersController@index')->name('users_create');
    });
});

