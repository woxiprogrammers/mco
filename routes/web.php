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

    Route::group(['prefix' => 'category'],function(){
        Route::get('create',array('uses' => 'Admin\CategoryController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\CategoryController@getEditView'));
        Route::get('manage',array('uses' => 'Admin\CategoryController@getManageView'));
    });
    Route::group(['prefix' => 'material'],function(){
        Route::get('manage',array('uses' => 'Admin\MaterialController@getManageView'));
        Route::get('create',array('uses' => 'Admin\MaterialController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\MaterialController@getEditView'));
    });
    Route::group(['prefix' => 'product'],function(){
        Route::get('manage',array('uses' => 'Admin\ProductController@getManageView'));
        Route::get('create',array('uses' => 'Admin\ProductController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\ProductController@getEditView'));
    });
    Route::group(['prefix' => 'profitMargin'],function(){
        Route::get('manage',array('uses' => 'Admin\ProfitMarginController@getManageView'));
        Route::get('create',array('uses' => 'Admin\ProfitMarginController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\ProfitMarginController@getEditView'));
    });
    Route::group(['prefix' => 'units'],function(){
        Route::get('manage',array('uses' => 'Admin\UnitsController@getManageView'));
        Route::get('create',array('uses' => 'Admin\UnitsController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\UnitsController@getEditView'));
    });
    Route::group(['prefix' => 'summary'],function(){
        Route::get('manage',array('uses' => 'Admin\SummaryController@getManageView'));
        Route::get('create',array('uses' => 'Admin\SummaryController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\SummaryController@getEditView'));
    });
});