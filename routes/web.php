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

    Route::get('/',array('uses' => 'Admin\AdminController@viewLogin'));
    Route::post('/authenticate',array('uses' => 'Auth\LoginController@login'));
    Route::get('/logout',array('uses' => 'Auth\LoginController@logout'));
    Route::get('/dashboard',function(){
        return view('admin.dashboard');
    });

    Route::group(['prefix' => 'category'],function(){
        Route::get('create',array('uses' => 'Admin\CategoryController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\CategoryController@createCategory'));
        Route::get('edit/{category}',array('uses' => 'Admin\CategoryController@getEditView'));
        Route::post('edit/{category}',array('uses' => 'Admin\CategoryController@editCategory'));
        Route::get('manage',array('uses' => 'Admin\CategoryController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\CategoryController@categoryListing'));
        Route::get('change-status/{category}',array('uses' => 'Admin\CategoryController@changeCategoryStatus'));
    });
    Route::group(['prefix' => 'summary'],function(){
        Route::get('manage',array('uses' => 'Admin\SummaryController@getManageView'));
        Route::get('create',array('uses' => 'Admin\SummaryController@getCreateView'));
        Route::get('edit/{summary}',array('uses' => 'Admin\SummaryController@getEditView'));
        Route::post('create',array('uses' => 'Admin\SummaryController@createSummary'));
        Route::post('edit/{summary}',array('uses' => 'Admin\SummaryController@editSummary'));
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
        Route::get('conversion',array('uses' => 'Admin\UnitsController@getCreateConversionView'));
    });

});

Auth::routes();
