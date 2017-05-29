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
    Route::group(['prefix' => 'material'],function(){
        Route::get('manage',array('uses' => 'Admin\MaterialController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\MaterialController@materialListing'));
        Route::get('create',array('uses' => 'Admin\MaterialController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\MaterialController@createMaterial'));
        Route::get('edit/{material}',array('uses' => 'Admin\MaterialController@getEditView'));
        Route::post('edit/{material}',array('uses' => 'Admin\MaterialController@editMaterial'));
        Route::get('change-status/{material}',array('uses' => 'Admin\MaterialController@changeMaterialStatus'));
        Route::post('check-name',array('uses' => 'Admin\MaterialController@checkMaterialName'));
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
        Route::post('listing',array('uses' => 'Admin\UnitsController@unitsListing'));
        Route::get('create',array('uses' => 'Admin\UnitsController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\UnitsController@createUnit'));
        Route::get('edit/{unit}',array('uses' => 'Admin\UnitsController@getEditView'));
        Route::post('edit/{unit}',array('uses' => 'Admin\UnitsController@editUnit'));
        Route::group(['prefix' => 'conversion'],function(){
            Route::get('create',array('uses' => 'Admin\UnitsController@getCreateConversionView'));
            Route::post('create',array('uses' => 'Admin\UnitsController@createConversion'));
            Route::get('edit/{units}',array('uses' => 'Admin\UnitsController@getEditConversionView'));
            Route::post('edit/{units}',array('uses' => 'Admin\UnitsController@editConversion'));
            Route::post('listing',array('uses' => 'Admin\UnitsController@unitConversionsListing'));
        });
        Route::get('change-status/{unit}',array('uses' => 'Admin\UnitsController@changeUnitStatus'));
    });
    Route::group(['prefix' => 'summary'],function(){
        Route::get('manage',array('uses' => 'Admin\SummaryController@getManageView'));
        Route::get('create',array('uses' => 'Admin\SummaryController@getCreateView'));
        Route::get('edit',array('uses' => 'Admin\SummaryController@getEditView'));
    });
    Route::group(['prefix' => 'tax'],function(){
        Route::get('create',array('uses' => 'Admin\TaxController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\TaxController@createTax'));
        Route::get('edit/{tax}',array('uses' => 'Admin\TaxController@getEditView'));
        Route::post('edit/{tax}',array('uses' => 'Admin\TaxController@editTax'));
        Route::get('manage',array('uses' => 'Admin\TaxController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\TaxController@taxListing'));
        Route::get('change-status/{tax}',array('uses' => 'Admin\TaxController@changeTaxStatus'));
    });

});