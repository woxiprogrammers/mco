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

    Route::group(['prefix' => 'user'],function (){
        Route::get('create',array('uses' => 'User\UserController@getUserView'));
        Route::post('create',array('uses' => 'User\UserController@createUser'));
        Route::get('edit/{user}',array('uses' => 'User\UserController@getEditView'));
        Route::post('edit/{user}',array('uses' => 'User\UserController@editUser'));
        Route::get('manage',array('uses' => 'User\UserController@getManageView'));
        Route::post('listing',array('uses' => 'User\UserController@userListing'));
        Route::get('change-status/{user}',array('uses' => 'User\UserController@changeUserStatus'));
    });

    Route::group(['prefix' => 'client'],function (){
        Route::get('create',array('uses' => 'Client\ClientController@getClientView'));
        Route::post('create',array('uses' => 'Client\ClientController@createClient'));
        Route::get('edit/{client}',array('uses' => 'Client\ClientController@getEditView'));
        Route::post('edit/{client}',array('uses' => 'Client\ClientController@editClient'));
        Route::get('manage',array('uses' => 'Client\ClientController@getManageView'));
        Route::post('listing',array('uses' => 'Client\ClientController@clientListing'));
        Route::get('change-status/{client}',array('uses' => 'Client\ClientController@changeClientStatus'));
    });

    Route::group(['prefix' => 'category'],function(){
        Route::get('create',array('uses' => 'Admin\CategoryController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\CategoryController@createCategory'));
        Route::get('edit/{category}',array('uses' => 'Admin\CategoryController@getEditView'));
        Route::post('edit/{category}',array('uses' => 'Admin\CategoryController@editCategory'));
        Route::get('manage',array('uses' => 'Admin\CategoryController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\CategoryController@categoryListing'));
        Route::get('change-status/{category}',array('uses' => 'Admin\CategoryController@changeCategoryStatus'));
        Route::post('check-name',array('uses' => 'Admin\CategoryController@checkCategoryName'));
    });
    Route::group(['prefix' => 'material'],function(){
        Route::get('manage',array('uses' => 'Admin\MaterialController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\MaterialController@materialListing'));
        Route::get('create',array('uses' => 'Admin\MaterialController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\MaterialController@createMaterial'));
        Route::get('edit/{material}',array('uses' => 'Admin\MaterialController@getEditView'));
        Route::put('edit/{material}',array('uses' => 'Admin\MaterialController@editMaterial'));
        Route::get('change-status/{material}',array('uses' => 'Admin\MaterialController@changeMaterialStatus'));
        Route::post('check-name',array('uses' => 'Admin\MaterialController@checkMaterialName'));
        Route::get('auto-suggest/{keyword}',array('uses' => 'Admin\MaterialController@autoSuggest'));
        Route::post('basicrate_material', array('uses' => 'Admin\MaterialController@generateBasicRateMaterialPdf'));

    });
    Route::group(['prefix' => 'product'],function(){
        Route::get('manage',array('uses' => 'Admin\ProductController@getManageView'));
        Route::get('create',array('uses' => 'Admin\ProductController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\ProductController@createProduct'));
        Route::get('edit/{product}',array('uses' => 'Admin\ProductController@getEditView'));
        Route::post('edit/{product}',array('uses' => 'Admin\ProductController@editProduct'));
        Route::get('get-materials/{category}',array('uses' => 'Admin\ProductController@getMaterials'));
        Route::post('material/listing',array('uses' => 'Admin\ProductController@getMaterialsDetails'));
        Route::post('listing',array('uses' => 'Admin\ProductController@productListing'));
        Route::get('change-status/{product}',array('uses' => 'Admin\ProductController@changeProductStatus'));
        Route::get('auto-suggest/{keyword}',array('uses' => 'Admin\ProductController@autoSuggest'));
        Route::post('check-name',array('uses' => 'Admin\ProductController@checkProductName'));
        Route::get('product-analysis-pdf/{product}', array('uses' => 'Admin\ProductController@generateProductAnalysisPdf'));
    });
    Route::group(['prefix' => 'profit-margin'],function(){
        Route::get('manage',array('uses' => 'Admin\ProfitMarginController@getManageView'));
        Route::get('create',array('uses' => 'Admin\ProfitMarginController@getCreateView'));
        Route::get('edit/{profit_margin}',array('uses' => 'Admin\ProfitMarginController@getEditView'));
        Route::post('edit/{profit_margin}',array('uses' => 'Admin\ProfitMarginController@editProfitMargin'));
        Route::post('create',array('uses' => 'Admin\ProfitMarginController@createProfitMargin'));
        Route::post('listing',array('uses' => 'Admin\ProfitMarginController@profitMarginListing'));
        Route::get('change-status/{profit_margin}',array('uses' => 'Admin\ProfitMarginController@changeProfitMarginStatus'));
        Route::post('check-name',array('uses' => 'Admin\ProfitMarginController@checkProfitMarginName'));
    });
    Route::group(['prefix' => 'units'],function(){
        Route::get('manage',array('uses' => 'Admin\UnitsController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\UnitsController@unitsListing'));
        Route::get('create',array('uses' => 'Admin\UnitsController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\UnitsController@createUnit'));
        Route::get('edit/{unit}',array('uses' => 'Admin\UnitsController@getEditView'));
        Route::put('edit/{unit}',array('uses' => 'Admin\UnitsController@editUnit'));
        Route::post('check-name',array('uses' => 'Admin\UnitsController@checkUnitName'));
        Route::group(['prefix' => 'conversion'],function(){
            Route::get('create',array('uses' => 'Admin\UnitsController@getCreateConversionView'));
            Route::post('create',array('uses' => 'Admin\UnitsController@createConversion'));
            Route::get('edit/{unit_conversion}',array('uses' => 'Admin\UnitsController@getEditConversionView'));
            Route::put('edit/{unit_conversion}',array('uses' => 'Admin\UnitsController@editConversion'));
            Route::post('listing',array('uses' => 'Admin\UnitsController@unitConversionsListing'));
        });
        Route::get('change-status/{unit}',array('uses' => 'Admin\UnitsController@changeUnitStatus'));
        Route::post('convert',array('uses' => 'Admin\UnitsController@convertUnits'));
    });
    Route::group(['prefix' => 'summary'],function(){
        Route::get('manage',array('uses' => 'Admin\SummaryController@getManageView'));
        Route::get('create',array('uses' => 'Admin\SummaryController@getCreateView'));
        Route::get('edit/{summary}',array('uses' => 'Admin\SummaryController@getEditView'));
        Route::post('create',array('uses' => 'Admin\SummaryController@createSummary'));
        Route::post('edit/{summary}',array('uses' => 'Admin\SummaryController@editSummary'));
        Route::get('manage',array('uses' => 'Admin\SummaryController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\SummaryController@summaryListing'));
        Route::get('change-status/{summary}',array('uses' => 'Admin\SummaryController@changeSummaryStatus'));
        Route::post('check-name',array('uses' => 'Admin\SummaryController@checkSummaryName'));
    });
    Route::group(['prefix' => 'tax'],function(){
        Route::get('create',array('uses' => 'Admin\TaxController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\TaxController@createTax'));
        Route::get('edit/{tax}',array('uses' => 'Admin\TaxController@getEditView'));
        Route::post('edit/{tax}',array('uses' => 'Admin\TaxController@editTax'));
        Route::get('manage',array('uses' => 'Admin\TaxController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\TaxController@taxListing'));
        Route::get('change-status/{tax}',array('uses' => 'Admin\TaxController@changeTaxStatus'));
        Route::post('check-name',array('uses' => 'Admin\TaxController@checkTaxName'));
    });

    Route::group(['prefix' => 'bill'],function(){
        Route::get('create/{project_site}',array('uses' => 'Admin\BillController@getCreateView'));
        Route::get('view/{bill}',array('uses' => 'Admin\BillController@editBill'));
        Route::get('create',array('uses' => 'Admin\BillController@getCreateNewBillView'));
        Route::post('create',array('uses' => 'Admin\BillController@createBill'));
        Route::get('projects/{client}',array('uses' => 'Admin\BillController@getProjects'));
        Route::get('project-sites/{project}',array('uses' => 'Admin\BillController@getProjectSites'));
        Route::get('manage',array('uses' => 'Admin\BillController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\BillController@billListing'));
        Route::post('approve', array('uses' => 'Admin\BillController@approveBill'));
        Route::get('current/invoice/{bill}', array('uses' => 'Admin\BillController@generateCurrentBill'));
        Route::get('cumulative/invoice/{bill}', array('uses' => 'Admin\BillController@generateCumulativeInvoice'));
        Route::post('image-upload/{billId}',array('uses'=>'Admin\BillController@uploadTempBillImages'));
        Route::post('display-images/{billId}',array('uses'=>'Admin\BillController@displayBillImages'));
        Route::post('delete-temp-product-image',array('uses'=>'Admin\BillController@removeTempImage'));
    });

    Route::group(['prefix' => 'quotation'], function(){
        Route::get('create',array('uses'=> 'Admin\QuotationController@getCreateView'));
        Route::post('create',array('uses'=> 'Admin\QuotationController@createQuotation'));
        Route::get('manage',array('uses'=> 'Admin\QuotationController@getManageView'));
        Route::post('listing',array('uses'=> 'Admin\QuotationController@quotationListing'));
        Route::post('get-products',array('uses'=> 'Admin\QuotationController@getProducts'));
        Route::post('get-materials', array('uses' => 'Admin\QuotationController@getMaterials'));
        Route::post('get-profit-margins', array('uses' => 'Admin\QuotationController@getProfitMargins'));
        Route::post('get-product-detail',array('uses'=> 'Admin\QuotationController@getProductDetail'));
        Route::post('add-product-row',array('uses'=> 'Admin\QuotationController@addProductRow'));
        Route::post('check-project-site-name',array('uses'=> 'Admin\QuotationController@checkProjectSiteName'));
        Route::post('get-project-sites',array('uses'=> 'Admin\QuotationController@getProjectSites'));
        Route::post('check-project-name',array('uses'=> 'Admin\QuotationController@checkProjectNames'));
        Route::post('get-projects',array('uses'=> 'Admin\QuotationController@getProjects'));
        Route::get('edit/{quotation}',array('uses'=> 'Admin\QuotationController@getEditView'));
        Route::put('edit/{quotation}',array('uses'=> 'Admin\QuotationController@editQuotation'));
        Route::post('get-product-calculations',array('uses'=> 'Admin\QuotationController@calculateProductsAmount'));
        Route::post('image-upload/{quotationId}',array('uses'=>'Admin\QuotationController@uploadTempWorkOrderImages'));
        Route::post('display-images/{quotationId}',array('uses'=>'Admin\QuotationController@displayWorkOrderImages'));
        Route::post('delete-temp-product-image',array('uses'=>'Admin\QuotationController@removeTempImage'));
        Route::post('get-work-order-form', array('uses'=> 'Admin\QuotationController@getWorkOrderForm'));
        Route::post('approve/{quotation}', array('uses'=> 'Admin\QuotationController@approve'));
        Route::group(['prefix' => 'work-order'],function(){
            Route::post('edit/{work_order}',array('uses'=>'Admin\QuotationController@editWorkOrder'));
        });
    });

    Route::group(['prefix' => 'project'], function(){
        Route::get('create',array('uses'=> 'Admin\ProjectController@getCreateView'));
        Route::post('create',array('uses'=> 'Admin\ProjectController@createProject'));
        Route::get('manage',array('uses'=> 'Admin\ProjectController@getManageView'));
        Route::post('listing',array('uses'=> 'Admin\ProjectController@projectListing'));
        Route::post('check-name',array('uses'=> 'Admin\ProjectController@checkProjectName'));
        Route::get('change-status/{project}',array('uses' => 'Admin\ProjectController@changeProjectStatus'));
        Route::get('edit/{project}',array('uses' => 'Admin\ProjectController@getEditView'));
        Route::put('edit/{project}',array('uses' => 'Admin\ProjectController@editProject'));
    });
});

