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


Route::group(['domain' => env('DOMAIN_NAME')], function(){
    Route::get('/', function () {
        return view('admin.login');
    });
    Route::get('/dashboard',function(){
        return view('admin.dashboard');
    });
      Route::post('/authenticate',array('uses' => 'Auth\LoginController@authenticate'));
    Route::group(['prefix' => 'category'],function(){
        Route::get('create',array('uses' => 'Admin\CategoryController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\CategoryController@getEditView'));
    });
});
