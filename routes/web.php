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
    Route::get('/dashboard',array('uses' => 'Admin\DashboardController@index'));

    Route::group(['prefix' => 'user'],function (){
        Route::get('create',array('uses' => 'User\UserController@getUserView'));
        Route::post('create',array('uses' => 'User\UserController@createUser'));
        Route::get('edit/{user}',array('uses' => 'User\UserController@getEditView'));
        Route::put('edit/{user}',array('uses' => 'User\UserController@editUser'));
        Route::get('manage',array('uses' => 'User\UserController@getManageView'));
        Route::post('listing',array('uses' => 'User\UserController@userListing'));
        Route::post('check-mobile',array('uses' => 'User\UserController@checkMobile'));
        Route::get('change-status/{user}',array('uses' => 'User\UserController@changeUserStatus'));
        Route::get('get-route-acls/{roleId}',array('uses' => 'User\UserController@getRoleAcls'));
        Route::group(['prefix' => 'project-site'],function(){
            Route::get('auto-suggest/{keyword}',array('uses' => 'User\UserController@projectSiteAutoSuggest'));
            Route::post('assign/{user}',array('uses' => 'User\UserController@assignProjectSites'));
        });
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
    Route::group(['prefix' => 'role'],function() {
        Route::get('create', array('uses' => 'Admin\RoleController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\RoleController@createRole'));
        Route::get('edit/{role}', array('uses' => 'Admin\RoleController@getEditView'));
        Route::post('edit/{role}', array('uses' => 'Admin\RoleController@editRole'));
        Route::get('manage', array('uses' => 'Admin\RoleController@getManageView'));
        Route::get('get-module/{role}',array('uses' => 'Admin\RoleController@getModules'));
        Route::post('module/listing',array('uses' => 'Admin\RoleController@getSubModules'));
        Route::post('listing', array('uses' => 'Admin\RoleController@roleListing'));
        Route::get('change-status/{role}', array('uses' => 'Admin\RoleController@changeRoleStatus'));
        Route::post('check-name', array('uses' => 'Admin\RoleController@checkRoleName'));
    });
    Route::group(['prefix' => 'extra-item'],function(){
        Route::get('create',array('uses' => 'Admin\ExtraItemController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\ExtraItemController@createExtraItem'));
        Route::get('edit/{extra_item}',array('uses' => 'Admin\ExtraItemController@getEditView'));
        Route::post('edit/{extra_item}',array('uses' => 'Admin\ExtraItemController@editExtraItem'));
        Route::get('manage',array('uses' => 'Admin\ExtraItemController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\ExtraItemController@extraItemListing'));
        Route::get('change-status/{extra_item}',array('uses' => 'Admin\ExtraItemController@changeExtraItemStatus'));
        Route::post('check-name',array('uses' => 'Admin\ExtraItemController@checkExtraItemName'));
    });
    Route::group(['prefix' => 'material'],function(){
        Route::get('manage',array('uses' => 'Admin\MaterialController@getManageView'));
        Route::post('listing',array('uses' => 'Admin\MaterialController@materialListing'));
        Route::get('create',array('uses' => 'Admin\MaterialController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\MaterialController@createMaterial'));
        Route::get('edit/{material}',array('uses' => 'Admin\MaterialController@getEditView'));
        Route::put('edit/{material}',array('uses' => 'Admin\MaterialController@editMaterial'));
        Route::post('change-status',array('uses' => 'Admin\MaterialController@changeMaterialStatus'));
        Route::post('check-name',array('uses' => 'Admin\MaterialController@checkMaterialName'));
        Route::get('auto-suggest/{keyword}',array('uses' => 'Admin\MaterialController@autoSuggest'));
        Route::post('basicrate_material', array('uses' => 'Admin\MaterialController@generateBasicRateMaterialPdf'));

    });
    Route::group(['prefix' => 'product'],function(){
        Route::get('manage',array('uses' => 'Admin\ProductController@getManageView'));
        Route::get('create',array('uses' => 'Admin\ProductController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\ProductController@createProduct'));
        Route::get('edit/{product}',array('uses' => 'Admin\ProductController@getEditView'));
        Route::get('copy/{product}',array('uses' => 'Admin\ProductController@getEditView'));
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
        Route::get('view/{bill}',array('uses' => 'Admin\BillController@viewBill'));
        Route::get('create',array('uses' => 'Admin\BillController@getCreateNewBillView'));
        Route::post('create',array('uses' => 'Admin\BillController@createBill'));
        Route::get('projects/{client}',array('uses' => 'Admin\BillController@getProjects'));
        Route::get('project-sites/{project}',array('uses' => 'Admin\BillController@getProjectSites'));
        Route::get('manage/project-site',array('uses' => 'Admin\BillController@getProjectSiteManageView'));
        Route::post('listing/project-site',array('uses' => 'Admin\BillController@ProjectSiteListing'));
        Route::post('approve', array('uses' => 'Admin\BillController@approveBill'));
        Route::get('current/{slug}/{bill}', array('uses' => 'Admin\BillController@generateCurrentBill'));
        Route::get('cumulative/invoice/{bill}', array('uses' => 'Admin\BillController@generateCumulativeInvoice'));
        Route::get('cumulative/excel-sheet/{bill}', array('uses' => 'Admin\BillController@generateCumulativeExcelSheet'));
        Route::post('image-upload/{billId}',array('uses'=>'Admin\BillController@uploadTempBillImages'));
        Route::post('display-images/{billId}',array('uses'=>'Admin\BillController@displayBillImages'));
        Route::post('delete-temp-product-image',array('uses'=>'Admin\BillController@removeTempImage'));
        Route::get('edit/{bill}', array('uses' => 'Admin\BillController@editBillView'));
        Route::post('edit/{bill}', array('uses' => 'Admin\BillController@editBill'));
        Route::post('cancel/{bill}', array('uses' => 'Admin\BillController@cancelBill'));
        Route::get('manage/{project_site}',array('uses' => 'Admin\BillController@getManageView'));
        Route::post('listing/{project_site}/{status}',array('uses' => 'Admin\BillController@billListing'));
        Route::post('product_description/create',array('uses' => 'Admin\BillController@createProductDescription'));
        Route::post('product_description/update',array('uses' => 'Admin\BillController@updateProductDescription'));
        Route::group(['prefix'=>'product'],function(){
            Route::get('get-descriptions/{quotation_id}/{keyword}',array('uses' => 'Admin\BillController@getProductDescription'));
        });
        Route::post('calculate-tax-amounts',array('uses' => 'Admin\BillController@calculateTaxAmounts'));
        Route::group(['prefix' => 'transaction'], function(){
            Route::post('create', array('uses' => 'Admin\BillController@saveTransactionDetails'));
            Route::post('listing/{billId}', array('uses' => 'Admin\BillController@billTransactionListing'));
            Route::get('detail/{bill_transaction}', array('uses' => 'Admin\BillController@billTransactionDetail'));
        });
    });

    Route::group(['prefix' => 'quotation'], function(){
        Route::get('create',array('uses'=> 'Admin\QuotationController@getCreateView'));
        Route::post('create',array('uses'=> 'Admin\QuotationController@createQuotation'));
        Route::get('manage/{status}',array('uses'=> 'Admin\QuotationController@getManageView'));
        Route::post('listing/{status}',array('uses'=> 'Admin\QuotationController@quotationListing'));
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
        Route::get('invoice/{quotation}/{slug}/{summary_slug}' ,array('uses' => 'Admin\QuotationController@generateQuotationPdf'));
        Route::get('summary/{quotation}' ,array('uses' => 'Admin\QuotationController@generateSummaryPdf'));
        Route::post('image-upload/{quotationId}',array('uses'=>'Admin\QuotationController@uploadTempWorkOrderImages'));
        Route::post('display-images/{quotationId}',array('uses'=>'Admin\QuotationController@displayWorkOrderImages'));
        Route::post('delete-temp-product-image',array('uses'=>'Admin\QuotationController@removeTempImage'));
        Route::post('get-work-order-form', array('uses'=> 'Admin\QuotationController@getWorkOrderForm'));
        Route::post('approve/{quotation}', array('uses'=> 'Admin\QuotationController@approve'));
        Route::post('disapprove/{quotation}', array('uses'=> 'Admin\QuotationController@disapprove'));
        Route::group(['prefix' => 'work-order'],function(){
            Route::post('edit/{work_order}',array('uses'=>'Admin\QuotationController@editWorkOrder'));
        });
        Route::group(['prefix' => 'product'],function(){
            Route::post('create/{product}',array('uses'=>'Admin\QuotationController@saveQuotationProduct'));
        });
        Route::post('get-quotation-product-view',array('uses' => 'Admin\QuotationController@getProductEditView'));
        Route::post('check-product-remove',array('uses' => 'Admin\QuotationController@checkProductRemove'));
        Route::group(['prefix' => 'extra-item'],function(){
            Route::post('create',array('uses'=>'Admin\QuotationController@addExtraItems'));
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
    Route::group(['prefix' => 'purchase'], function(){
        Route::group(['prefix' => 'material-request'], function(){
            Route::get('manage',array('uses'=> 'User\PurchaseController@getManageView'));
            Route::get('create',array('uses'=> 'User\PurchaseController@getCreateView'));
            Route::post('listing',array('uses'=> 'User\PurchaseController@getMaterialRequestListing'));
            Route::get('edit',array('uses'=> 'User\PurchaseController@editMaterialRequest'));
            Route::get('get-items',array('uses'=> 'User\PurchaseController@autoSuggest'));
            Route::post('get-units',array('uses'=> 'User\PurchaseController@getUnitsList'));
            Route::post('get-projects',array('uses'=> 'User\PurchaseController@getProjectsList'));
            Route::post('get-clients',array('uses'=> 'User\PurchaseController@getClientsList'));
            Route::post('get-users',array('uses'=> 'User\PurchaseController@getUsersList'));
            Route::post('create',array('uses'=> 'User\PurchaseController@createMaterialList'));
            Route::post('material-requestWise-listing',array('uses'=> 'User\PurchaseController@getMaterialRequestWiseListing'));
            Route::get('material-requestWise-listing-view',array('uses'=> 'User\PurchaseController@getMaterialRequestWiseListingView'));

        });
        Route::group(['prefix' => 'purchase-request'], function(){
            Route::get('manage',array('uses'=> 'Purchase\PurchaseRequestController@getManageView'));
            Route::get('create',array('uses'=> 'Purchase\PurchaseRequestController@getCreateView'));
            Route::get('edit/{status}',array('uses'=> 'Purchase\PurchaseRequestController@getEditView'));
        });
        Route::group(['prefix' => 'purchase-order'], function(){
            Route::get('manage',array('uses'=> 'Purchase\PurchaseOrderController@getManageView'));
            Route::get('create',array('uses'=> 'Purchase\PurchaseOrderController@getCreateView'));
            Route::get('edit',array('uses'=> 'Purchase\PurchaseOrderController@getEditView'));
        });
    });

    Route::group(['prefix' => 'inventory'], function(){
        Route::group(['prefix' => 'manage-inventory'], function(){
            Route::get('manage',array('uses'=> 'Inventory\InventoryManageController@getManageView'));
            Route::get('create',array('uses'=> 'Inventory\InventoryManageController@getCreateView'));
            Route::get('edit',array('uses'=> 'Purchase\PurchaseOrderController@getEditView'));
        });

    });

    Route::group(['prefix' => 'vendors'],function(){
        Route::get('manage',array('uses' => 'Admin\VendorController@getManageView'));
        Route::get('create',array('uses' => 'Admin\VendorController@getCreateView'));
        Route::post('create',array('uses' => 'Admin\VendorController@createVendor'));
        Route::get('edit/{vendor}',array('uses' => 'Admin\VendorController@getEditView'));
        Route::put('edit/{vendor}',array('uses' => 'Admin\VendorController@editVendor'));
        Route::get('get-materials/{category}',array('uses' => 'Admin\VendorController@getMaterials'));
        Route::post('get-city-info',array('uses'=> 'Admin\VendorController@getCityInfo'));
        Route::post('listing',array('uses'=> 'Admin\VendorController@vendorListing'));
        Route::post('check-name',array('uses'=> 'Admin\VendorController@checkVendorName'));
        Route::get('change-status/{vendor}',array('uses' => 'Admin\VendorController@changeVendorStatus'));
        Route::get('auto-suggest/{keyword}',array('uses' => 'Admin\VendorController@autoSuggest'));
    });

    Route::group(['prefix' => 'asset'], function(){
        Route::get('manage',array('uses'=> 'Admin\AssetManagementController@getManageView'));
        Route::get('create',array('uses'=> 'Admin\AssetManagementController@getCreateView'));
        Route::get('edit/{asset}',array('uses'=> 'Admin\AssetManagementController@getEditView'));
        Route::post('edit/{asset}',array('uses' => 'Admin\AssetManagementController@editAsset'));
        Route::post('create',array('uses' => 'Admin\AssetManagementController@createAsset'));
        Route::post('listing',array('uses'=> 'Admin\AssetManagementController@assetListing'));
        Route::post('image-upload',array('uses'=>'Admin\AssetManagementController@uploadTempAssetImages'));
        Route::post('display-images',array('uses'=>'Admin\AssetManagementController@displayAssetImages'));
        Route::post('delete-temp-product-image',array('uses'=>'Admin\AssetManagementController@removeAssetImage'));
        Route::post('check-name',array('uses'=> 'Admin\AssetManagementController@checkAssetName'));
        Route::get('change-status/{asset}',array('uses' => 'Admin\AssetManagementController@changeAssetStatus'));
    });

    Route::group(['prefix'=>'bank'],function() {
        Route::get('manage', array('uses' => 'Admin\BankController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\BankController@bankListing'));
        Route::get('create', array('uses' => 'Admin\BankController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\BankController@CreateBank'));
        Route::get('edit/{bank_info}', array('uses' => 'Admin\BankController@getEditView'));
        Route::put('edit/{bank_info}', array('uses' => 'Admin\BankController@editBank'));
        Route::get('change-status/{bank_info}', array('uses' => 'Admin\BankController@changeBankStatus'));
    });

    Route::group(['prefix' => 'checklist'], function(){
        Route::group(['prefix' => 'category-management'], function(){
            Route::get('manage',array('uses'=> 'Checklist\CategoryManagementController@getManageView'));
            Route::get('edit',array('uses'=> 'Checklist\CategoryManagementController@getEditView'));
            Route::post('listing',array('uses'=> 'Checklist\CategoryManagementController@getCategoryManagementListing'));
        });
        Route::group(['prefix' => 'checkList'],function(){
            Route::get('manage',array('uses' => 'Checklist\ChecklistController@getManageView'));
            Route::get('create',array('uses' => 'Checklist\ChecklistController@getCreateView'));
        });
    });
});
