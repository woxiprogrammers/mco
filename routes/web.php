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

use Illuminate\Support\Facades\Route;

Route::group(['domain' => env('DOMAIN_NAME')], function () {
    Route::get('/', array('uses' => 'Admin\AdminController@viewLogin'));
    Route::post('/authenticate', array('uses' => 'Auth\LoginController@login'));
    Route::get('/logout', array('uses' => 'Auth\LoginController@logout'));

    Route::get('/dashboard', array('uses' => 'Admin\DashboardController@index'));

    Route::post('/change-project-site', array('uses' => 'Auth\LoginController@changeProjectSite'));
    Route::group(['prefix' => 'user'], function () {
        Route::get('change-password', array('uses' => 'User\UserController@getChangePasswordView'));
        Route::post('change-password', array('uses' => 'User\UserController@changePassword'));
        Route::get('create', array('uses' => 'User\UserController@getUserView'));
        Route::post('create', array('uses' => 'User\UserController@createUser'));
        Route::post('get-permission', array('uses' => 'User\UserController@getPermission'));
        Route::get('edit/{user}', array('uses' => 'User\UserController@getEditView'));
        Route::put('edit/{user}', array('uses' => 'User\UserController@editUser'));
        Route::get('manage', array('uses' => 'User\UserController@getManageView'));
        Route::post('listing', array('uses' => 'User\UserController@userListing'));
        Route::post('check-mobile', array('uses' => 'User\UserController@checkMobile'));
        Route::post('check-email', array('uses' => 'User\UserController@checkEmail'));
        Route::get('change-status/{user}', array('uses' => 'User\UserController@changeUserStatus'));
        Route::get('get-route-acls/{roleId}', array('uses' => 'User\UserController@getRoleAcls'));
        Route::group(['prefix' => 'project-site'], function () {
            Route::get('auto-suggest/{keyword}', array('uses' => 'User\UserController@projectSiteAutoSuggest'));
            Route::post('assign/{user}', array('uses' => 'User\UserController@assignProjectSites'));
        });
    });

    Route::group(['prefix' => 'salary-distribution'], function () {
        Route::get('manage', array('uses' => 'Admin\SalaryDistributionController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\SalaryDistributionController@salaryDistributionListing'));
    });

    Route::group(['prefix' => 'client'], function () {
        Route::get('create', array('uses' => 'Client\ClientController@getClientView'));
        Route::post('create', array('uses' => 'Client\ClientController@createClient'));
        Route::get('edit/{client}', array('uses' => 'Client\ClientController@getEditView'));
        Route::post('edit/{client}', array('uses' => 'Client\ClientController@editClient'));
        Route::get('manage', array('uses' => 'Client\ClientController@getManageView'));
        Route::post('listing', array('uses' => 'Client\ClientController@clientListing'));
        Route::get('change-status/{client}', array('uses' => 'Client\ClientController@changeClientStatus'));
    });

    Route::group(['prefix' => 'address'], function () {
        Route::get('create', array('uses' => 'Admin\AddressController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\AddressController@createAddress'));
        Route::get('edit/{address}', array('uses' => 'Admin\AddressController@getEditView'));
        Route::post('edit/{address}', array('uses' => 'Admin\AddressController@editAddress'));
        Route::get('manage', array('uses' => 'Admin\AddressController@getAddressManageView'));
        Route::post('listing', array('uses' => 'Admin\AddressController@addressListing'));
        Route::post('get-states', array('uses' => 'Admin\AddressController@getStates'));
        Route::post('get-cities', array('uses' => 'Admin\AddressController@getCities'));
        Route::get('change-status/{address}', array('uses' => 'Admin\AddressController@changeAddressStatus'));
    });
    Route::get('script', array('uses' => 'Admin\CategoryController@getData'));
    Route::group(['prefix' => 'category'], function () {
        Route::get('create', array('uses' => 'Admin\CategoryController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\CategoryController@createCategory'));
        Route::get('edit/{category}', array('uses' => 'Admin\CategoryController@getEditView'));
        Route::post('edit/{category}', array('uses' => 'Admin\CategoryController@editCategory'));
        Route::get('manage', array('uses' => 'Admin\CategoryController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\CategoryController@categoryListing'));
        Route::get('change-status/{category}', array('uses' => 'Admin\CategoryController@changeCategoryStatus'));
        Route::post('check-name', array('uses' => 'Admin\CategoryController@checkCategoryName'));
    });

    Route::group(['prefix' => 'role'], function () {
        Route::get('create', array('uses' => 'Admin\RoleController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\RoleController@createRole'));
        Route::get('edit/{role}', array('uses' => 'Admin\RoleController@getEditView'));
        Route::post('edit/{role}', array('uses' => 'Admin\RoleController@editRole'));
        Route::get('manage', array('uses' => 'Admin\RoleController@getManageView'));
        Route::get('get-module/{role}', array('uses' => 'Admin\RoleController@getModules'));
        Route::post('module/listing', array('uses' => 'Admin\RoleController@getSubModules'));
        Route::post('listing', array('uses' => 'Admin\RoleController@roleListing'));
        Route::get('change-status/{role}', array('uses' => 'Admin\RoleController@changeRoleStatus'));
        Route::post('check-name', array('uses' => 'Admin\RoleController@checkRoleName'));
    });

    Route::group(['prefix' => 'extra-item'], function () {
        Route::get('create', array('uses' => 'Admin\ExtraItemController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\ExtraItemController@createExtraItem'));
        Route::get('edit/{extra_item}', array('uses' => 'Admin\ExtraItemController@getEditView'));
        Route::post('edit/{extra_item}', array('uses' => 'Admin\ExtraItemController@editExtraItem'));
        Route::get('manage', array('uses' => 'Admin\ExtraItemController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\ExtraItemController@extraItemListing'));
        Route::get('change-status/{extra_item}', array('uses' => 'Admin\ExtraItemController@changeExtraItemStatus'));
        Route::post('check-name', array('uses' => 'Admin\ExtraItemController@checkExtraItemName'));
    });

    Route::group(['prefix' => 'material'], function () {
        Route::get('manage', array('uses' => 'Admin\MaterialController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\MaterialController@materialListing'));
        Route::get('create', array('uses' => 'Admin\MaterialController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\MaterialController@createMaterial'));
        Route::get('edit/{material}', array('uses' => 'Admin\MaterialController@getEditView'));
        Route::post('image-upload', array('uses' => 'Admin\MaterialController@uploadTempMaterialImages'));
        Route::post('display-images', array('uses' => 'Admin\MaterialController@displayMaterialImages'));
        Route::post('delete-temp-product-image', array('uses' => 'Admin\MaterialController@removeMaterialImage'));
        Route::put('edit/{material}', array('uses' => 'Admin\MaterialController@editMaterial'));
        Route::post('change-status', array('uses' => 'Admin\MaterialController@changeMaterialStatus'));
        Route::post('check-name', array('uses' => 'Admin\MaterialController@checkMaterialName'));
        Route::get('auto-suggest/{keyword}', array('uses' => 'Admin\MaterialController@autoSuggest'));
        Route::post('basicrate_material', array('uses' => 'Admin\MaterialController@generateBasicRateMaterialPdf'));
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('manage', array('uses' => 'Admin\ProductController@getManageView'));
        Route::get('create', array('uses' => 'Admin\ProductController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\ProductController@createProduct'));
        Route::get('edit/{product}', array('uses' => 'Admin\ProductController@getEditView'));
        Route::get('copy/{product}', array('uses' => 'Admin\ProductController@getEditView'));
        Route::post('edit/{product}', array('uses' => 'Admin\ProductController@editProduct'));
        Route::get('get-materials/{category}', array('uses' => 'Admin\ProductController@getMaterials'));
        Route::post('material/listing', array('uses' => 'Admin\ProductController@getMaterialsDetails'));
        Route::post('listing', array('uses' => 'Admin\ProductController@productListing'));
        Route::get('change-status/{product}', array('uses' => 'Admin\ProductController@changeProductStatus'));
        Route::get('auto-suggest/{keyword}', array('uses' => 'Admin\ProductController@autoSuggest'));
        Route::post('check-name', array('uses' => 'Admin\ProductController@checkProductName'));
        Route::get('product-analysis-pdf/{product}', array('uses' => 'Admin\ProductController@generateProductAnalysisPdf'));
    });

    Route::group(['prefix' => 'profit-margin'], function () {
        Route::get('manage', array('uses' => 'Admin\ProfitMarginController@getManageView'));
        Route::get('create', array('uses' => 'Admin\ProfitMarginController@getCreateView'));
        Route::get('edit/{profit_margin}', array('uses' => 'Admin\ProfitMarginController@getEditView'));
        Route::post('edit/{profit_margin}', array('uses' => 'Admin\ProfitMarginController@editProfitMargin'));
        Route::post('create', array('uses' => 'Admin\ProfitMarginController@createProfitMargin'));
        Route::post('listing', array('uses' => 'Admin\ProfitMarginController@profitMarginListing'));
        Route::get('change-status/{profit_margin}', array('uses' => 'Admin\ProfitMarginController@changeProfitMarginStatus'));
        Route::post('check-name', array('uses' => 'Admin\ProfitMarginController@checkProfitMarginName'));
    });

    Route::group(['prefix' => 'units'], function () {
        Route::get('manage', array('uses' => 'Admin\UnitsController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\UnitsController@unitsListing'));
        Route::get('create', array('uses' => 'Admin\UnitsController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\UnitsController@createUnit'));
        Route::get('edit/{unit}', array('uses' => 'Admin\UnitsController@getEditView'));
        Route::put('edit/{unit}', array('uses' => 'Admin\UnitsController@editUnit'));
        Route::post('check-name', array('uses' => 'Admin\UnitsController@checkUnitName'));
        Route::group(['prefix' => 'conversion'], function () {
            Route::get('create', array('uses' => 'Admin\UnitsController@getCreateConversionView'));
            Route::post('create', array('uses' => 'Admin\UnitsController@createConversion'));
            Route::get('edit/{unit_conversion}', array('uses' => 'Admin\UnitsController@getEditConversionView'));
            Route::put('edit/{unit_conversion}', array('uses' => 'Admin\UnitsController@editConversion'));
            Route::post('listing', array('uses' => 'Admin\UnitsController@unitConversionsListing'));
        });
        Route::get('change-status/{unit}', array('uses' => 'Admin\UnitsController@changeUnitStatus'));
        Route::post('convert', array('uses' => 'Admin\UnitsController@convertUnits'));
    });

    Route::group(['prefix' => 'summary'], function () {
        Route::get('manage', array('uses' => 'Admin\SummaryController@getManageView'));
        Route::get('create', array('uses' => 'Admin\SummaryController@getCreateView'));
        Route::get('edit/{summary}', array('uses' => 'Admin\SummaryController@getEditView'));
        Route::post('create', array('uses' => 'Admin\SummaryController@createSummary'));
        Route::post('edit/{summary}', array('uses' => 'Admin\SummaryController@editSummary'));
        Route::get('manage', array('uses' => 'Admin\SummaryController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\SummaryController@summaryListing'));
        Route::get('change-status/{summary}', array('uses' => 'Admin\SummaryController@changeSummaryStatus'));
        Route::post('check-name', array('uses' => 'Admin\SummaryController@checkSummaryName'));
    });

    Route::group(['prefix' => 'tax'], function () {
        Route::get('create', array('uses' => 'Admin\TaxController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\TaxController@createTax'));
        Route::get('edit/{tax}', array('uses' => 'Admin\TaxController@getEditView'));
        Route::post('edit/{tax}', array('uses' => 'Admin\TaxController@editTax'));
        Route::get('manage', array('uses' => 'Admin\TaxController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\TaxController@taxListing'));
        Route::get('change-status/{tax}', array('uses' => 'Admin\TaxController@changeTaxStatus'));
        Route::post('check-name', array('uses' => 'Admin\TaxController@checkTaxName'));
    });

    Route::group(['prefix' => 'bill'], function () {
        Route::get('create/{project_site}', array('uses' => 'Admin\BillController@getCreateView'));
        Route::get('view/{bill}', array('uses' => 'Admin\BillController@viewBill'));
        Route::get('create', array('uses' => 'Admin\BillController@getCreateNewBillView'));
        Route::post('create', array('uses' => 'Admin\BillController@createBill'));
        Route::get('projects/{client}', array('uses' => 'Admin\BillController@getProjects'));
        Route::get('project-sites/{project}', array('uses' => 'Admin\BillController@getProjectSites'));
        Route::get('manage/project-site', array('uses' => 'Admin\BillController@getProjectSiteManageView'));
        Route::post('listing/project-site', array('uses' => 'Admin\BillController@ProjectSiteListing'));
        Route::post('approve', array('uses' => 'Admin\BillController@approveBill'));
        Route::get('current/{slug}/{bill}', array('uses' => 'Admin\BillController@generateCurrentBill'));
        Route::get('cumulative/invoice/{bill}', array('uses' => 'Admin\BillController@generateCumulativeInvoice'));
        Route::get('cumulative/excel-sheet/{bill}', array('uses' => 'Admin\BillController@generateCumulativeExcelSheet'));
        Route::post('image-upload/{billId}', array('uses' => 'Admin\BillController@uploadTempBillImages'));
        Route::post('display-images/{billId}', array('uses' => 'Admin\BillController@displayBillImages'));
        Route::post('delete-temp-product-image', array('uses' => 'Admin\BillController@removeTempImage'));
        Route::get('edit/{bill}', array('uses' => 'Admin\BillController@editBillView'));
        Route::post('edit/{bill}', array('uses' => 'Admin\BillController@editBill'));
        Route::post('cancel/{bill}', array('uses' => 'Admin\BillController@cancelBill'));
        Route::get('manage/{project_site}', array('uses' => 'Admin\BillController@getManageView'));
        Route::post('listing/{project_site}/{status}', array('uses' => 'Admin\BillController@billListing'));
        Route::post('product_description/create', array('uses' => 'Admin\BillController@createProductDescription'));
        Route::post('product_description/update', array('uses' => 'Admin\BillController@updateProductDescription'));
        Route::group(['prefix' => 'product'], function () {
            Route::get('get-descriptions/{quotation_id}/{keyword}', array('uses' => 'Admin\BillController@getProductDescription'));
        });
        Route::post('calculate-tax-amounts', array('uses' => 'Admin\BillController@calculateTaxAmounts'));
        Route::group(['prefix' => 'transaction'], function () {
            Route::post('create', array('uses' => 'Admin\BillController@saveTransactionDetails'));
            Route::post('listing/{billId}', array('uses' => 'Admin\BillController@billTransactionListing'));
            Route::get('detail/{bill_transaction}', array('uses' => 'Admin\BillController@billTransactionDetail'));
            Route::post('change-status', array('uses' => 'Admin\BillController@changeBillTransactionStatus'));
        });
        Route::group(['prefix' => 'reconcile'], function () {
            Route::post('add-transaction', array('uses' => 'Admin\BillController@addReconcileTransaction'));
            Route::post('hold-listing', array('uses' => 'Admin\BillController@getHoldReconcileListing'));
            Route::post('retention-listing', array('uses' => 'Admin\BillController@getRetentionReconcileListing'));
        });
    });

    Route::group(['prefix' => 'quotation'], function () {
        Route::get('create', array('uses' => 'Admin\QuotationController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\QuotationController@createQuotation'));
        Route::get('manage/{status}', array('uses' => 'Admin\QuotationController@getManageView'));
        Route::post('listing/{status}', array('uses' => 'Admin\QuotationController@quotationListing'));
        Route::post('get-products', array('uses' => 'Admin\QuotationController@getProducts'));
        Route::post('get-materials', array('uses' => 'Admin\QuotationController@getMaterials'));
        Route::post('get-profit-margins', array('uses' => 'Admin\QuotationController@getProfitMargins'));
        Route::post('get-product-detail', array('uses' => 'Admin\QuotationController@getProductDetail'));
        Route::post('add-product-row', array('uses' => 'Admin\QuotationController@addProductRow'));
        Route::post('check-project-site-name', array('uses' => 'Admin\QuotationController@checkProjectSiteName'));
        Route::post('get-project-sites', array('uses' => 'Admin\QuotationController@getProjectSites'));
        Route::post('check-project-name', array('uses' => 'Admin\QuotationController@checkProjectNames'));
        Route::post('get-projects', array('uses' => 'Admin\QuotationController@getProjects'));
        Route::get('edit/{quotation}', array('uses' => 'Admin\QuotationController@getEditView'));
        Route::put('edit/{quotation}', array('uses' => 'Admin\QuotationController@editQuotation'));
        Route::post('get-product-calculations', array('uses' => 'Admin\QuotationController@calculateProductsAmount'));
        Route::get('invoice/{quotation}/{slug}/{summary_slug}', array('uses' => 'Admin\QuotationController@generateQuotationPdf'));
        Route::get('summary/{quotation}', array('uses' => 'Admin\QuotationController@generateSummaryPdf'));
        Route::post('image-upload/{quotationId}', array('uses' => 'Admin\QuotationController@uploadTempWorkOrderImages'));
        Route::post('display-images/{quotationId}', array('uses' => 'Admin\QuotationController@displayWorkOrderImages'));
        Route::post('delete-temp-product-image', array('uses' => 'Admin\QuotationController@removeTempImage'));
        Route::post('get-work-order-form', array('uses' => 'Admin\QuotationController@getWorkOrderForm'));
        Route::post('approve/{quotation}', array('uses' => 'Admin\QuotationController@approve'));
        Route::post('disapprove/{quotation}', array('uses' => 'Admin\QuotationController@disapprove'));
        Route::group(['prefix' => 'work-order'], function () {
            Route::post('edit/{work_order}', array('uses' => 'Admin\QuotationController@editWorkOrder'));
        });
        Route::group(['prefix' => 'product'], function () {
            Route::post('create/{product}', array('uses' => 'Admin\QuotationController@saveQuotationProduct'));
        });
        Route::post('get-quotation-product-view', array('uses' => 'Admin\QuotationController@getProductEditView'));
        Route::post('check-product-remove', array('uses' => 'Admin\QuotationController@checkProductRemove'));
        Route::group(['prefix' => 'extra-item'], function () {
            Route::post('create', array('uses' => 'Admin\QuotationController@addExtraItems'));
        });
        Route::post('remove-opening-balance', array('uses' => 'Admin\QuotationController@openingBalanceRemove'));
        Route::post('opening-balance-save', array('uses' => 'Admin\QuotationController@openingBalanceSave'));
    });

    Route::group(['prefix' => 'project'], function () {
        Route::get('create', array('uses' => 'Admin\ProjectController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\ProjectController@createProject'));
        Route::get('manage', array('uses' => 'Admin\ProjectController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\ProjectController@projectListing'));
        Route::post('check-name', array('uses' => 'Admin\ProjectController@checkProjectName'));
        Route::get('change-status/{project}', array('uses' => 'Admin\ProjectController@changeProjectStatus'));
        Route::get('edit/{project}', array('uses' => 'Admin\ProjectController@getEditView'));
        Route::put('edit/{project}', array('uses' => 'Admin\ProjectController@editProject'));
        Route::group(['prefix' => 'advance-payment'], function () {
            Route::post('create', array('uses' => 'Admin\ProjectController@addAdvancePayment'));
            Route::post('listing', array('uses' => 'Admin\ProjectController@advancePaymentListing'));
        });
        Route::group(['prefix' => 'receipt-payment'], function () {
            Route::post('create', array('uses' => 'Admin\ProjectController@addReceiptPayment'));
            Route::post('listing', array('uses' => 'Admin\ProjectController@receiptPaymentListing'));
        });
        Route::group(['prefix' => 'indirect-expense'], function () {
            Route::post('create', array('uses' => 'Admin\ProjectController@addIndirectExpense'));
            Route::post('listing', array('uses' => 'Admin\ProjectController@indirectExpenseListing'));
        });
    });

    Route::group(['prefix' => 'grn'], function () {
        Route::group(['prefix' => 'delete'], function () {
            Route::get('/', array('uses' => 'Purchase\PurchaseOrderController@grnDeleteView'));
            Route::post('/', array('uses' => 'Purchase\PurchaseOrderController@grnDelete'));
            Route::get('/listing', array('uses' => 'Purchase\PurchaseOrderController@grnDeleteListing'));
        });
        Route::group(['prefix' => 'restore'], function () {
            Route::get('/', array('uses' => 'Purchase\PurchaseOrderController@grnRestoreView'));
            Route::post('/', array('uses' => 'Purchase\PurchaseOrderController@grnRestore'));
            Route::get('/listing', array('uses' => 'Purchase\PurchaseOrderController@grnRestoreListing'));
        });
    });

    Route::group(['prefix' => 'purchase'], function () {
        Route::get('get-detail/{materialRequestComponentID}', array('uses' => 'User\PurchaseController@getPurchaseDetails'));
        Route::get('projects/{client_id}', array('uses' => 'User\PurchaseController@getProjects'));
        Route::get('project-sites/{project_id}', array('uses' => 'User\PurchaseController@getProjectSites'));

        Route::group(['prefix' => 'purchase-order-delete'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseOrderController@getManageGRNDeleteView'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseOrderController@grnDeleteListing'));
        });

        Route::group(['prefix' => 'material-request'], function () {
            Route::get('manage', array('uses' => 'User\PurchaseController@getManageView'));
            Route::get('create', array('uses' => 'User\PurchaseController@getCreateView'));
            Route::post('listing', array('uses' => 'User\PurchaseController@getMaterialRequestListing'));
            Route::get('edit', array('uses' => 'User\PurchaseController@editMaterialRequest'));
            Route::get('get-items', array('uses' => 'User\PurchaseController@autoSuggest'));
            Route::post('get-units', array('uses' => 'User\PurchaseController@getUnitsList'));
            Route::post('get-users', array('uses' => 'User\PurchaseController@getUsersList'));
            Route::post('create', array('uses' => 'User\PurchaseController@createMaterialList'));
            Route::post('material-requestWise-listing', array('uses' => 'User\PurchaseController@getMaterialRequestWiseListing'));
            Route::get('material-requestWise-listing-view', array('uses' => 'User\PurchaseController@getMaterialRequestWiseListingView'));
            Route::post('change-status/{newStatus}/{componentId?}', array('uses' => 'User\PurchaseController@changeMaterialRequestComponentStatus'));
            Route::post('change-status-mti', array('uses' => 'User\PurchaseController@changeMaterialRequestComponentStatustoMTI'));
            Route::get('get-material-request-component-details/{materialRequestComponent}', array('uses' => 'User\PurchaseController@getMaterialRequestComponentDetail'));
            Route::post('validate-quantity', array('uses' => 'User\PurchaseController@validateQuantity'));
        });

        Route::group(['prefix' => 'purchase-request'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseRequestController@getManageView'));
            Route::get('create', array('uses' => 'Purchase\PurchaseRequestController@getCreateView'));
            Route::get('edit/{status}/{id}', array('uses' => 'Purchase\PurchaseRequestController@getEditView'));
            Route::post('create', array('uses' => 'Purchase\PurchaseRequestController@create'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseRequestController@purchaseRequestListing'));
            Route::post('change-status/{newStatus}/{componentId?}', array('uses' => 'Purchase\PurchaseRequestController@changePurchaseRequestStatus'));
            Route::post('assign-vendors', array('uses' => 'Purchase\PurchaseRequestController@assignVendors'));
            Route::post('get-material-inventory-quantity', array('uses' => 'Purchase\PurchaseRequestController@getMaterialInventoryQuantity'));
            Route::post('edit-quantity', array('uses' => 'Purchase\PurchaseRequestController@editComponentQuantity'));
            Route::get('get-detail/{purchaseRequestId}', array('uses' => 'Purchase\PurchaseRequestController@getPurchaseRequestDetails'));
        });

        Route::group(['prefix' => 'purchase-order'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseOrderController@getManageView'));
            Route::get('create', array('uses' => 'Purchase\PurchaseOrderController@getCreateView'));
            Route::get('edit/{id}', array('uses' => 'Purchase\PurchaseOrderController@getEditView'));
            Route::post('edit/{purchaseOrder}', array('uses' => 'Purchase\PurchaseOrderController@editPurchaseOrder'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseOrderController@getListing'));
            Route::post('get-details', array('uses' => 'Purchase\PurchaseOrderController@getPurchaseOrderComponentDetails'));
            //Route::post('get-bill-details',array('uses'=> 'Purchase\PurchaseOrderController@getPurchaseOrderBillDetails'));
            Route::post('add-advance-payment', array('uses' => 'Purchase\PurchaseOrderController@createAdvancePayment'));
            Route::post('change-status', array('uses' => 'Purchase\PurchaseOrderController@changeStatus'));
            Route::post('create-material', array('uses' => 'Purchase\PurchaseOrderController@createMaterial'));
            Route::post('create-asset', array('uses' => 'Purchase\PurchaseOrderController@createAsset'));
            Route::post('create', array('uses' => 'Purchase\PurchaseOrderController@createPurchaseOrder'));
            Route::get('get-purchase-request-component/{purchaseRequest}', array('uses' => 'Purchase\PurchaseOrderController@getPurchaseRequestComponents'));
            Route::get('get-client-project/{purchaseRequest}', array('uses' => 'Purchase\PurchaseOrderController@getClientProjectName'));
            Route::get('download-po-pdf/{purchaseOrder}', array('uses' => 'Purchase\PurchaseOrderController@downloadPoPDF'));
            Route::post('close-purchase-order', array('uses' => 'Purchase\PurchaseOrderController@closePurchaseOrder'));
            Route::post('reopen', array('uses' => 'Purchase\PurchaseOrderController@reopenPurchaseOrder'));
            Route::post('get-component-details', array('uses' => 'Purchase\PurchaseOrderController@getComponentDetails'));
            Route::get('get-purchase-order-details/{purchaseRequestId}', array('uses' => 'Purchase\PurchaseOrderController@getOrderDetails'));
            Route::post('get-tax-details/{purchaseRequestComponent}', array('uses' => 'Purchase\PurchaseOrderController@getComponentTaxData'));
            Route::get('get-detail/{purchaseOrderId}', array('uses' => 'Purchase\PurchaseOrderController@getPurchaseOrderDetails'));
            Route::post('authenticate-purchase-order-close', array('uses' => 'Purchase\PurchaseOrderController@authenticatePOClose'));

            Route::group(['prefix' => 'transaction'], function () {
                Route::post('upload-pre-grn-images', array('uses' => 'Purchase\PurchaseOrderController@preGrnImageUpload'));
                Route::post('create', array('uses' => 'Purchase\PurchaseOrderController@createTransaction'));
                Route::get('get-details', array('uses' => 'Purchase\PurchaseOrderController@getTransactionDetails'));
                Route::get('check-generated-grn/{purchaseOrder}', array('uses' => 'Purchase\PurchaseOrderController@checkGeneratedGRN'));
                Route::get('edit/{purchaseOrderTransaction}', array('uses' => 'Purchase\PurchaseOrderController@getTransactionEditView'));
                Route::post('edit/{purchaseOrderTransaction}', array('uses' => 'Purchase\PurchaseOrderController@transactionEdit'));
                Route::post('check-quantity', array('uses' => 'Purchase\PurchaseOrderController@checkTransactionRemainingQuantity'));
            });
            Route::group(['prefix' => 'advance-payment'], function () {
                Route::post('listing', array('uses' => 'Purchase\PurchaseOrderController@getAdvancePaymentListing'));
            });
        });

        Route::group(['prefix' => 'purchase-order-bill'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseOrderBillingController@getManageView'));
            Route::get('create', array('uses' => 'Purchase\PurchaseOrderBillingController@getCreateView'));
            Route::post('create', array('uses' => 'Purchase\PurchaseOrderBillingController@createBill'));
            Route::post('get-project-sites', array('uses' => 'Purchase\PurchaseOrderBillingController@getProjectSites'));
            Route::get('get-purchase-orders', array('uses' => 'Purchase\PurchaseOrderBillingController@getPurchaseOrders'));
            Route::get('get-purchase-orders-bill-number', array('uses' => 'Purchase\PurchaseOrderBillingController@getPurchaseOrdersByBillNumber'));
            Route::get('get-bill-pending-transactions', array('uses' => 'Purchase\PurchaseOrderBillingController@getBillPendingTransactions'));
            Route::post('get-transaction-subtotal', array('uses' => 'Purchase\PurchaseOrderBillingController@getTransactionSubtotal'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseOrderBillingController@listing'));
            Route::get('edit/{purchaseOrderBill}', array('uses' => 'Purchase\PurchaseOrderBillingController@getEditView'));
            Route::post('edit/{purchaseOrderBill}', array('uses' => 'Purchase\PurchaseOrderBillingController@editPurchaseOrderBill'));
            Route::group(['prefix' => 'payment'], function () {
                Route::post('listing/{purchaseOrderBillId}', array('uses' => 'Purchase\PurchaseOrderBillingController@paymentListing'));
                Route::post('create', array('uses' => 'Purchase\PurchaseOrderBillingController@createPayment'));
            });
            Route::post('check-bill-number', array('uses' => 'Purchase\PurchaseOrderBillingController@checkBillNumber'));
        });
        Route::group(['prefix' => 'vendor-mail'], function () {
            Route::get('manage', array('uses' => 'Purchase\VendorMailController@getManageView'));
            Route::post('listing', array('uses' => 'Purchase\VendorMailController@listing'));
            Route::get('pdf/{VendorMailInfoId}/{slug}', array('uses' => 'Purchase\VendorMailController@getPDF'));
        });
        Route::group(['prefix' => 'purchase-order-request'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseOrderRequestController@getManageView'));
            Route::get('create', array('uses' => 'Purchase\PurchaseOrderRequestController@getCreateView'));
            Route::post('create', array('uses' => 'Purchase\PurchaseOrderRequestController@createPurchaseOrderRequest'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseOrderRequestController@listing'));
            Route::post('get-purchase-request-component-details', array('uses' => 'Purchase\PurchaseOrderRequestController@getPurchaseRequestComponentDetails'));
            Route::post('get-component-tax-details/{purchaseComponentVendorRelation}', array('uses' => 'Purchase\PurchaseOrderRequestController@getComponentTaxDetails'));
            Route::post('get-purchase-order-request-component-tax-details/{purchaseOrderRequestComponent}', array('uses' => 'Purchase\PurchaseOrderRequestController@getPurchaseOrderRequestComponentTaxDetails'));
            Route::get('edit/{purchaseOrderRequest}', array('uses' => 'Purchase\PurchaseOrderRequestController@getEditView'));
            Route::get('approve/{purchaseOrderRequest}', array('uses' => 'Purchase\PurchaseOrderRequestController@getApproveView'));
            Route::post('edit/{purchaseOrderRequest}', array('uses' => 'Purchase\PurchaseOrderRequestController@editPurchaseOrderRequest'));
            Route::post('approve/{purchaseOrderRequest}', array('uses' => 'Purchase\PurchaseOrderRequestController@approvePurchaseOrderRequest'));
            Route::get('purchase-request-auto-suggest/{keyword}', array('uses' => 'Purchase\PurchaseOrderRequestController@purchaseRequestAutoSuggest'));
            Route::get('make-ready-to-approve/{purchaseOrderRequest}', array('uses' => 'Purchase\PurchaseOrderRequestController@makeReadyToApprove'));
            Route::get('disapprove-component/{purchaseOrderRequest}/{purchaseRequestComponent}', array('uses' => 'Purchase\PurchaseOrderRequestController@disapproveComponent'));
            Route::post('file-upload/{purchaseRequestComponentId}', array('uses' => 'Purchase\PurchaseOrderRequestController@uploadTempFiles'));
            Route::post('display-files/{forSlug}/{purchaseOrderRequestID}', array('uses' => 'Purchase\PurchaseOrderRequestController@displayFiles'));
            Route::post('delete-temp-file', array('uses' => 'Purchase\PurchaseOrderRequestController@removeTempImage'));
        });

        Route::group(['prefix' => 'pending-po-bills'], function () {
            Route::get('manage', array('uses' => 'Purchase\PurchaseOrderBillingController@getManageViewForPendingPOBill'));
            Route::post('listing', array('uses' => 'Purchase\PurchaseOrderBillingController@getManageViewForPendingPOBillListing'));
        });
    });

    Route::group(['prefix' => 'inventory'], function () {
        Route::get('challan', array('uses' => 'Inventory\InventoryManageController@generateChallanView'));
        Route::get('pdf/{inventoryComponentTransferId}', array('uses' => 'Inventory\InventoryManageController@getInventoryComponentTransferPDF'));
        Route::get('manage', array('uses' => 'Inventory\InventoryManageController@getManageView'));
        Route::post('listing', array('uses' => 'Inventory\InventoryManageController@inventoryListing'));
        Route::post('get-project-sites', array('uses' => 'Inventory\InventoryManageController@getProjectSites'));
        Route::group(['prefix' => 'component'], function () {
            Route::post('create', array('uses' => 'Inventory\InventoryManageController@createInventoryComponent'));
            Route::post('listing/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@inventoryComponentListing'));
            Route::get('manage/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@getComponentManageView'));
            Route::post('add-transfer/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@addComponentTransfer'));
            Route::post('image-upload/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@uploadTempImages'));
            Route::post('display-images/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@displayTempImages'));
            Route::post('delete-temp-inventory-image', array('uses' => 'Drawing\ImagesController@removeTempImage'));
            Route::post('edit-opening-stock', ['uses' => 'Inventory\InventoryManageController@editOpeningStock']);
            Route::post('get-detail', ['uses' => 'Inventory\InventoryManageController@getGRNDetails']);
            Route::get('detail/{inventoryComponentTransfer}/{slug}', ['uses' => 'Inventory\InventoryManageController@getInventoryComponentTransferDetail']);
            Route::group(['prefix' => 'readings'], function () {
                Route::post('listing/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@inventoryComponentReadingListing'));
                Route::post('add/{inventoryComponent}', array('uses' => 'Inventory\InventoryManageController@addInventoryComponentReading'));
            });
        });
        Route::group(['prefix' => 'transfer'], function () {
            Route::get('manage', array('uses' => 'Inventory\InventoryManageController@getTransferManageView'));
            Route::post('listing', array('uses' => 'Inventory\InventoryManageController@getSiteTransferRequestListing'));
            Route::post('check-quantity', array('uses' => 'Inventory\InventoryManageController@checkAvailableQuantity'));
            Route::get('auto-suggest/{type}/{keyword}', array('uses' => 'Inventory\InventoryManageController@autoSuggest'));
            Route::get('employee-auto-suggest/{keyword}', array('uses' => 'Inventory\InventoryManageController@employeeAutoSuggest'));
            Route::post('change-status/{status}/{inventoryTransferId}', array('uses' => 'Inventory\InventoryManageController@changeStatus'));
            Route::post('upload-pre-grn-images', array('uses' => 'Inventory\InventoryManageController@preGrnImageUpload'));
            Route::post('challen-generation', array('uses' => 'Inventory\InventoryManageController@downloadChallan'));
            Route::group(['prefix' => 'billing'], function () {
                Route::get('manage', array('uses' => 'Inventory\SiteTransferBillingController@getManageView'));
                Route::get('create', array('uses' => 'Inventory\SiteTransferBillingController@getCreateView'));
                Route::get('edit/{siteTransferBill}', array('uses' => 'Inventory\SiteTransferBillingController@getEditView'));
                Route::post('create', array('uses' => 'Inventory\SiteTransferBillingController@createSiteTransferBill'));
                Route::post('listing', array('uses' => 'Inventory\SiteTransferBillingController@listing'));
                Route::get('get-approved-transaction', array('uses' => 'Inventory\SiteTransferBillingController@getApprovedTransaction'));
                Route::group(['prefix' => 'payment'], function () {
                    Route::post('create', array('uses' => 'Inventory\SiteTransferBillingController@createPayment'));
                    Route::post('listing/{siteTransferBill}', array('uses' => 'Inventory\SiteTransferBillingController@paymentListing'));
                });
            });
            Route::group(['prefix' => 'challan'], function () {
                Route::post('cart/create', array('uses' => 'Inventory\InventoryTransferChallanController@createCartItems'));
                Route::post('cart/update', array('uses' => 'Inventory\InventoryTransferChallanController@updateCartItems'));
                Route::post('cart/delete', array('uses' => 'Inventory\InventoryTransferChallanController@deleteCartItems'));
                Route::post('create', array('uses' => 'Inventory\InventoryTransferChallanController@createChallan'));
                Route::get('manage', array('uses' => 'Inventory\InventoryTransferChallanController@getManageView'));
                Route::post('listing', array('uses' => 'Inventory\InventoryTransferChallanController@getChallanListing'));
                Route::get('{challanId}/change-status', array('uses' => 'Inventory\InventoryTransferChallanController@approveDisapproveChallan'));
                Route::get('info/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@getDetail'));
                Route::get('reopen/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@reopenChallan'));
                Route::get('edit/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@show'));
                Route::post('edit/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@editChallan'));

                Route::get('pdf/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@generatePDF'));
                Route::post('authenticate-challan-close', array('uses' => 'Inventory\InventoryTransferChallanController@authenticateChallanClose'));
                Route::get('close/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@closeChallan'));
                Route::get('site/in', array('uses' => 'Inventory\InventoryTransferChallanController@showSiteIn'));
                Route::post('site/in', array('uses' => 'Inventory\InventoryTransferChallanController@createSiteIn'));
                Route::post('site/in/upload-pre-grn-images', array('uses' => 'Inventory\InventoryTransferChallanController@preUploadSIteInImages'));
                Route::get('detail/{challanId}', array('uses' => 'Inventory\InventoryTransferChallanController@getChallanDetail'));
            });
        });
    });

    Route::group(['prefix' => 'vendors'], function () {
        Route::get('manage', array('uses' => 'Admin\VendorController@getManageView'));
        Route::get('create', array('uses' => 'Admin\VendorController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\VendorController@createVendor'));
        Route::get('edit/{vendor}', array('uses' => 'Admin\VendorController@getEditView'));
        Route::put('edit/{vendor}', array('uses' => 'Admin\VendorController@editVendor'));
        Route::get('get-materials/{category}', array('uses' => 'Admin\VendorController@getMaterials'));
        Route::post('get-city-info', array('uses' => 'Admin\VendorController@getCityInfo'));
        Route::post('listing', array('uses' => 'Admin\VendorController@vendorListing'));
        Route::post('check-name', array('uses' => 'Admin\VendorController@checkVendorName'));
        Route::get('change-status/{vendor}', array('uses' => 'Admin\VendorController@changeVendorStatus'));
        Route::get('auto-suggest/{keyword}', array('uses' => 'Admin\VendorController@autoSuggest'));
    });

    Route::group(['prefix' => 'asset'], function () {
        Route::get('manage', array('uses' => 'Admin\AssetManagementController@getManageView'));
        Route::get('create', array('uses' => 'Admin\AssetManagementController@getCreateView'));
        Route::get('edit/{asset}', array('uses' => 'Admin\AssetManagementController@getEditView'));
        Route::post('edit/{asset}', array('uses' => 'Admin\AssetManagementController@editAsset'));
        Route::post('edit/assign-project-site/{asset}', array('uses' => 'Admin\AssetManagementController@assignProjectSite'));
        Route::post('create', array('uses' => 'Admin\AssetManagementController@createAsset'));
        Route::post('listing', array('uses' => 'Admin\AssetManagementController@assetListing'));
        Route::post('project-site-asset/listing/{assetId}', array('uses' => 'Admin\AssetManagementController@projectSiteAssetListing'));
        Route::post('image-upload', array('uses' => 'Admin\AssetManagementController@uploadTempAssetImages'));
        Route::post('display-images', array('uses' => 'Admin\AssetManagementController@displayAssetImages'));
        Route::post('delete-temp-product-image', array('uses' => 'Admin\AssetManagementController@removeAssetImage'));
        Route::post('check-name', array('uses' => 'Admin\AssetManagementController@checkModel'));
        Route::get('change-status/{asset}', array('uses' => 'Admin\AssetManagementController@changeAssetStatus'));
        Route::group(['prefix' => 'vendor'], function () {
            Route::get('auto-suggest/{keyword}', array('uses' => 'Admin\AssetManagementController@getVendorAutoSuggest'));
            Route::post('assign/{asset}', array('uses' => 'Admin\AssetManagementController@assignVendors'));
        });
        Route::group(['prefix' => "maintenance"], function () {
            Route::group(['prefix' => 'request'], function () {
                Route::get('create', array('uses' => 'Admin\AssetMaintenanceController@getCreateView'));
                Route::post('create', array('uses' => 'Admin\AssetMaintenanceController@createAssetMaintenanceRequest'));
                Route::get('manage', array('uses' => 'Admin\AssetMaintenanceController@getManageView'));
                Route::post('listing', array('uses' => 'Admin\AssetMaintenanceController@getMaintenanceRequestListing'));
                Route::get('auto-suggest/{keyword}', array('uses' => 'Admin\AssetMaintenanceController@autoSuggest'));
                Route::get('view/{assetMaintenanceId}', array('uses' => 'Admin\AssetMaintenanceController@getDetailView'));
                Route::post('image-upload', array('uses' => 'Admin\AssetMaintenanceController@uploadTempAssetMaintenanceImages'));
                Route::post('display-images', array('uses' => 'Admin\AssetMaintenanceController@displayAssetMaintenanceImages'));
                Route::post('delete-temp-product-image', array('uses' => 'Admin\AssetMaintenanceController@removeAssetMaintenanceImage'));
                Route::group(['prefix' => 'vendor'], function () {
                    Route::get('auto-suggest/{keyword}/{assetMaintenanceId}', array('uses' => 'Admin\AssetMaintenanceController@getAssetVendorAutoSuggest'));
                    Route::post('assign/{assetMaintenanceId}', array('uses' => 'Admin\AssetMaintenanceController@assetMaintenanceVendorAssign'));
                });
                Route::group(['prefix' => 'approval'], function () {
                    Route::post('change-status/{status}/{assetMaintenanceVendorID}', array('uses' => 'Admin\AssetMaintenanceController@changeMaintenanceRequestStatus'));
                    Route::get('manage', array('uses' => 'Admin\AssetMaintenanceController@getApprovalManageView'));
                    Route::post('listing', array('uses' => 'Admin\AssetMaintenanceController@getMaintenanceRequestApprovalListing'));
                });
                Route::group(['prefix' => 'transaction'], function () {
                    Route::post('create', array('uses' => 'Admin\AssetMaintenanceController@createTransaction'));
                    Route::post('upload-pre-grn-images', array('uses' => 'Admin\AssetMaintenanceController@preGrnImageUpload'));
                    Route::get('check-generated-grn/{assetMaintenanceId}', array('uses' => 'Admin\AssetMaintenanceController@checkGeneratedGRN'));
                    Route::get('view/{assetMaintenanceTransactionId}', array('uses' => 'Admin\AssetMaintenanceController@viewTransaction'));
                });
                Route::group(['prefix' => 'bill'], function () {
                    Route::get('manage', array('uses' => 'Admin\AssetMaintenanceController@getBillManageView'));
                    Route::post('listing', array('uses' => 'Admin\AssetMaintenanceController@getBillListing'));
                    Route::get('create', array('uses' => 'Admin\AssetMaintenanceController@getBillCreateView'));
                    Route::post('create', array('uses' => 'Admin\AssetMaintenanceController@createBill'));
                    Route::get('view/{assetMaintenanceBillId}', array('uses' => 'Admin\AssetMaintenanceController@viewBill'));
                    Route::get('get-bill-pending-transactions', array('uses' => 'Admin\AssetMaintenanceController@getBillPendingTransactions'));
                    Route::group(['prefix' => 'payment'], function () {
                        Route::post('create', array('uses' => 'Admin\AssetMaintenanceController@createPayment'));
                        Route::post('listing/{assetMaintenanceBillId}', array('uses' => 'Admin\AssetMaintenanceController@paymentListing'));
                    });
                });
            });
        });
    });

    Route::group(['prefix' => 'bank'], function () {
        Route::get('manage', array('uses' => 'Admin\BankController@getManageView'));
        Route::post('listing', array('uses' => 'Admin\BankController@bankListing'));
        Route::get('create', array('uses' => 'Admin\BankController@getCreateView'));
        Route::post('create', array('uses' => 'Admin\BankController@CreateBank'));
        Route::get('edit/{bank_info}', array('uses' => 'Admin\BankController@getEditView'));
        Route::put('edit/{bank_info}', array('uses' => 'Admin\BankController@editBank'));
        Route::get('change-status/{bank_info}', array('uses' => 'Admin\BankController@changeBankStatus'));
        Route::group(['prefix' => 'transaction'], function () {
            Route::post('create/{bank_info}', array('uses' => 'Admin\BankController@createTransaction'));
            Route::post('listing', array('uses' => 'Admin\BankController@getBankTransactionListing'));
        });
    });

    Route::group(['prefix' => 'checklist'], function () {
        Route::post('get-projects', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getProjects'));
        Route::post('get-project-sites', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getProjectSites'));
        Route::group(['prefix' => 'category-management'], function () {
            Route::get('manage', array('uses' => 'Checklist\CategoryManagementController@getManageView'));
            Route::get('edit', array('uses' => 'Checklist\CategoryManagementController@getEditView'));
            Route::post('listing/{slug}', array('uses' => 'Checklist\CategoryManagementController@getCategoryManagementListing'));
            Route::post('create/{slug}', array('uses' => 'Checklist\CategoryManagementController@createCategories'));
            Route::get('change-status/{checklistCategory}', array('uses' => 'Checklist\CategoryManagementController@changeStatus'));
        });

        Route::group(['prefix' => 'structure'], function () {
            Route::get('manage', array('uses' => 'Checklist\ChecklistController@getManageView'));
            Route::get('create', array('uses' => 'Checklist\ChecklistController@getCreateView'));
            Route::get('edit/{checklistCategory}', array('uses' => 'Checklist\ChecklistController@getStructureEditView'));
            Route::post('edit/{checklistCategory}', array('uses' => 'Checklist\ChecklistController@editStructure'));
            Route::post('create', array('uses' => 'Checklist\ChecklistController@createStructure'));
            Route::post('listing', array('uses' => 'Checklist\ChecklistController@structureListing'));
            Route::post('get-sub-category', array('uses' => 'Checklist\ChecklistController@getSubCategories'));
            Route::post('get-checkpoint-partial-view', array('uses' => 'Checklist\ChecklistController@getCheckpointPartialView'));
            Route::post('get-checkpoint-image-partial-view', array('uses' => 'Checklist\ChecklistController@getCheckpointImagePartialView'));
        });

        Route::group(['prefix' => 'site-assignment'], function () {
            Route::get('manage', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getManageView'));
            Route::get('create', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getCreateView'));
            Route::get('edit/{projectSiteChecklist}', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getSiteAssignmentEditView'));
            Route::get('get-checkpoints/{checklistCategory}', array('uses' => 'Checklist\ChecklistSiteAssignmentController@getCheckpoints'));
            Route::post('create', array('uses' => 'Checklist\ChecklistSiteAssignmentController@siteAssignmentCreate'));
            Route::post('listing', array('uses' => 'Checklist\ChecklistSiteAssignmentController@siteAssignmentListing'));
        });

        Route::group(['prefix' => 'user-assignment'], function () {
            Route::get('manage', array('uses' => 'Checklist\ChecklistUserAssignmentController@getManageView'));
            Route::get('create', array('uses' => 'Checklist\ChecklistUserAssignmentController@getCreateView'));
            Route::post('create', array('uses' => 'Checklist\ChecklistUserAssignmentController@createUserAssignment'));
            Route::post('get-categories', array('uses' => 'Checklist\ChecklistUserAssignmentController@getCategories'));
            Route::post('get-users', array('uses' => 'Checklist\ChecklistUserAssignmentController@getUsers'));
        });
    });

    Route::group(['prefix' => 'drawing'], function () {
        Route::group(['prefix' => 'category-management'], function () {
            Route::get('manage', array('uses' => 'Drawing\CategoryManagementController@getManageView'));
            Route::get('sub-category-manage', array('uses' => 'Drawing\CategoryManagementController@getSubCategoryManageView'));
            Route::get('create-main', array('uses' => 'Drawing\CategoryManagementController@getCreateMainView'));
            Route::post('create-main-category', array('uses' => 'Drawing\CategoryManagementController@getCreateMainCategory'));
            Route::post('main-category-listing', array('uses' => 'Drawing\CategoryManagementController@MainCategoryListing'));
            Route::post('sub-category-listing', array('uses' => 'Drawing\CategoryManagementController@SubCategoryListing'));
            Route::post('create-sub-category', array('uses' => 'Drawing\CategoryManagementController@createSubCategory'));
            Route::get('create-sub', array('uses' => 'Drawing\CategoryManagementController@getCreateSubView'));
            Route::get('change-status/{id}/{status}', array('uses' => 'Drawing\CategoryManagementController@changeStatus'));
            Route::get('edit/{id}', array('uses' => 'Drawing\CategoryManagementController@getMainEditView'));
            Route::post('edit-main-category', array('uses' => 'Drawing\CategoryManagementController@mainCategoryEdit'));
            Route::get('edit-sub/{id}', array('uses' => 'Drawing\CategoryManagementController@getSubEditView'));
            Route::post('edit-sub-category', array('uses' => 'Drawing\CategoryManagementController@editSubCategory'));
        });
        Route::group(['prefix' => 'images'], function () {
            Route::get('manage', array('uses' => 'Drawing\ImagesController@getManageView'));
            Route::get('create', array('uses' => 'Drawing\ImagesController@getCreateView'));
            Route::get('get-details/{id}', array('uses' => 'Drawing\ImagesController@getDetails'));
            Route::get('manage-drawings', array('uses' => 'Drawing\ImagesController@getManageDrawingsView'));
            Route::get('edit/{id}/{site_id}', array('uses' => 'Drawing\ImagesController@getEditView'));
            Route::post('edit', array('uses' => 'Drawing\ImagesController@edit'));
            Route::post('image-upload', array('uses' => 'Drawing\ImagesController@uploadTempDrawingImages'));
            Route::post('get-projects', array('uses' => 'Drawing\ImagesController@getProjects'));
            Route::post('get-project-sites', array('uses' => 'Drawing\ImagesController@getProjectSites'));
            Route::post('get-sub-categories', array('uses' => 'Drawing\ImagesController@getSubCategories'));
            Route::post('display-images', array('uses' => 'Drawing\ImagesController@displayDrawingImages'));
            Route::post('delete-temp-product-image', array('uses' => 'Drawing\ImagesController@removeTempImage'));
            Route::post('create', array('uses' => 'Drawing\ImagesController@create'));
            Route::post('listing', array('uses' => 'Drawing\ImagesController@listing'));
            Route::post('add-version', array('uses' => 'Drawing\ImagesController@createVersion'));
            Route::post('get-data', array('uses' => 'Drawing\ImagesController@getData'));
            Route::post('get-versions', array('uses' => 'Drawing\ImagesController@getAllVersions'));
            Route::post('get-version-images', array('uses' => 'Drawing\ImagesController@getAllVersionImages'));
            Route::post('add-comment', array('uses' => 'Drawing\ImagesController@addComment'));
        });
    });

    Route::group(['prefix' => 'labour'], function () {
        Route::get('create', array('uses' => 'Labour\LabourController@getCreateView'));
        Route::post('create', array('uses' => 'Labour\LabourController@createLabour'));
        Route::get('manage', array('uses' => 'Labour\LabourController@getManageView'));
        Route::post('listing', array('uses' => 'Labour\LabourController@labourListing'));
        Route::get('change-status/{labour}', array('uses' => 'Labour\LabourController@changeLabourStatus'));
        Route::get('edit/{labour}', array('uses' => 'Labour\LabourController@getEditView'));
        Route::post('edit/{labour}', array('uses' => 'Labour\LabourController@editLabour'));
        Route::get('employee-id/{employee_type}', array('uses' => 'Labour\LabourController@getEmployeeId'));
    });

    Route::group(['prefix' => 'subcontractor'], function () {
        Route::get('create', array('uses' => 'Subcontractor\SubcontractorController@getCreateView'));
        Route::post('create', array('uses' => 'Subcontractor\SubcontractorController@createSubcontractor'));
        Route::get('manage', array('uses' => 'Subcontractor\SubcontractorController@getManageView'));
        Route::post('listing', array('uses' => 'Subcontractor\SubcontractorController@subcontractorListing'));
        Route::get('change-status/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@changeSubcontractorStatus'));
        Route::get('edit/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@getEditView'));
        Route::post('edit/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@editSubcontractor'));
        Route::get('projects/{client_id}', array('uses' => 'Subcontractor\SubcontractorController@getProjects'));
        Route::get('project-sites/{project_id}', array('uses' => 'Subcontractor\SubcontractorController@getProjectSites'));

        Route::group(['prefix' => 'advance-payment'], function () {
            Route::post('add', array('uses' => 'Subcontractor\SubcontractorController@addAdvancePayment'));
            Route::post('listing', array('uses' => 'Subcontractor\SubcontractorController@advancePaymentListing'));
        });

        Route::group(['prefix' => 'dpr'], function () {
            Route::get('auto-suggest/{keyword}', array('uses' => 'Subcontractor\SubcontractorController@dprAutoSuggest'));
            Route::post('assign-categories/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@assignDprCategories'));
        });

        Route::group(['prefix' => 'subcontractor-structure'], function () {
            Route::get('manage', array('uses' => 'Subcontractor\SubcontractorController@getManageStructureView'));
            Route::post('create', array('uses' => 'Subcontractor\SubcontractorController@createSubcontractorStructure'));
            Route::get('create', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureCreateView'));
            Route::post('listing', array('uses' => 'Subcontractor\SubcontractorController@subcontractorStructureListing'));
            Route::get('view/{subcontractorStructureId}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureView'));
            Route::get('edit/{subcontractor_struct}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureEditView'));
            Route::post('edit/{subcontractor_struct}', array('uses' => 'Subcontractor\SubcontractorController@editSubcontractorStructure'));
        });

        Route::group(['prefix' => 'subcontractor-bills'], function () {
            Route::get('manage/{subcontractorStructureId}', array('uses' => 'Subcontractor\SubcontractorController@getBillManageView'));
            Route::post('listing/{subcontractorStructureId}/{billStatusSlug}', array('uses' => 'Subcontractor\SubcontractorController@getBillListing'));
            Route::get('view/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureBillView'));
            Route::get('edit/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureBillEditView'));
            Route::post('edit/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorController@editSubcontractorStructureBill'));
            Route::get('create/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorBillCreateView'));
            Route::post('create/{subcontractorStructureId}', array('uses' => 'Subcontractor\SubcontractorController@createSubcontractorBill'));
            Route::get('change-status/{statusSlug}/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorController@changeBillStatus'));

            Route::group(['prefix' => 'transaction'], function () {
                Route::post('create', array('uses' => 'Subcontractor\SubcontractorController@createTransaction'));
                Route::post('listing/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorController@getTransactionListing'));
            });

            Route::group(['prefix' => 'reconcile'], function () {
                Route::post('add-transaction', array('uses' => 'Subcontractor\SubcontractorController@addReconcileTransaction'));
                Route::post('hold-listing', array('uses' => 'Subcontractor\SubcontractorController@getHoldReconcileListing'));
                Route::post('retention-listing', array('uses' => 'Subcontractor\SubcontractorController@getRetentionReconcileListing'));
            });
        });

        /* -------- New Bill related Changes -------- */
        Route::group(['prefix' => 'structure'], function () {
            Route::get('manage', array('uses' => 'Subcontractor\SubcontractorStructureController@getManageView'));
            Route::get('create', array('uses' => 'Subcontractor\SubcontractorStructureController@getCreateView'));
            Route::post('create', array('uses' => 'Subcontractor\SubcontractorStructureController@createStructure'));
            Route::post('listing', array('uses' => 'Subcontractor\SubcontractorStructureController@structureListing'));
            Route::get('details', array('uses' => 'Subcontractor\SubcontractorStructureController@getStructureDetails'));
            Route::get('edit/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorStructureController@getEditView'));
            Route::post('edit/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorStructureController@editStructure'));
            Route::get('delete-extra-item/{id}/{structureId}', array('uses' => 'Subcontractor\SubcontractorStructureController@deleteExtraItem'));
        });

        Route::group(['prefix' => 'cashentry'], function () {
            Route::get('manage', array('uses' => 'Subcontractor\SubcontractorStructureController@cashentryManage'));
            Route::get('listing', array('uses' => 'Subcontractor\SubcontractorStructureController@cashEntryListing'));
            Route::post('edit/{id}', array('uses' => 'Subcontractor\SubcontractorStructureController@cashEntryEdit'));
        });

        Route::group(['prefix' => 'bill'], function () {
            Route::get('manage/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorBillController@getManageView'));
            Route::post('listing/{subcontractorStructure}/{billStatusSlug}', array('uses' => 'Subcontractor\SubcontractorBillController@billListing'));
            Route::get('create/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorBillController@getCreateView'));
            Route::post('create/{subcontractorStructure}', array('uses' => 'Subcontractor\SubcontractorBillController@createBill'));
            Route::get('view/{subcontractorBill}', array('uses' => 'Subcontractor\SubcontractorBillController@getBillView'));
            Route::get('edit/{subcontractorBill}', array('uses' => 'Subcontractor\SubcontractorBillController@getEditView'));
            Route::post('edit/{subcontractorBill}', array('uses' => 'Subcontractor\SubcontractorBillController@editBill'));
            Route::get('change-status/{statusSlug}/{subcontractorBill}', array('uses' => 'Subcontractor\SubcontractorBillController@changeBillStatus'));

            Route::group(['prefix' => 'transaction'], function () {
                Route::post('create', array('uses' => 'Subcontractor\SubcontractorBillController@createTransaction'));
                Route::post('listing/{subcontractorStructureBillId}', array('uses' => 'Subcontractor\SubcontractorBillController@getTransactionListing'));
                Route::post('change-status', array('uses' => 'Subcontractor\SubcontractorBillController@changeBillTransactionStatus'));
            });
        });

        /* -------- End of New Bill related Changes -------- */
    });

    Route::group(['prefix' => 'peticash'], function () {

        Route::get('projects/{client_id}', array('uses' => 'Peticash\PeticashController@getProjects'));
        Route::get('project-sites/{project_id}', array('uses' => 'Peticash\PeticashController@getProjectSites'));
        Route::post('change-status', array('uses' => 'Peticash\PeticashController@changeSalaryStatus'));
        Route::post('change-status-purchase-disapproved', array('uses' => 'Peticash\PeticashController@changePurchaseStatus'));
        Route::post('stats-salary', array('uses' => 'Peticash\PeticashController@getSalaryStats'));
        Route::post('change-status-purchase', array('uses' => 'Peticash\PeticashController@changePurchaseTxnStatus'));

        Route::group(['prefix' => 'master-peticash-account'], function () {
            Route::get('manage', array('uses' => 'Peticash\PeticashController@getManageViewForMasterPeticashAccount'));
            Route::get('createpage', array('uses' => 'Peticash\PeticashController@getCreateViewForMasterPeticashAccount'));
            Route::post('create', array('uses' => 'Peticash\PeticashController@createMasterPeticashAccount'));
            Route::get('editpage/{txnid}', array('uses' => 'Peticash\PeticashController@editViewMasterPeticashAccount'));
            Route::post('edit', array('uses' => 'Peticash\PeticashController@editMasterPeticashAccount'));
            Route::post('listing', array('uses' => 'Peticash\PeticashController@masterAccountListing'));
        });

        Route::group(['prefix' => 'sitewise-peticash-account'], function () {
            Route::get('manage', array('uses' => 'Peticash\PeticashController@getManageViewForSitewisePeticashAccount'));
            Route::get('createpage', array('uses' => 'Peticash\PeticashController@getCreateViewForSitewisePeticashAccount'));
            Route::post('create', array('uses' => 'Peticash\PeticashController@createSitewisePeticashAccount'));
            Route::post('listing', array('uses' => 'Peticash\PeticashController@sitewiseAccountListing'));
            Route::get('getuserlistbysite/{siteid}', array('uses' => 'Peticash\PeticashController@getUserBySites'));
            Route::get('editpage/{txnid}', array('uses' => 'Peticash\PeticashController@editViewSitewisePeticashAccount'));
            Route::post('edit', array('uses' => 'Peticash\PeticashController@editSitewisePeticashAccount'));
        });

        Route::group(['prefix' => 'peticash-approval-request'], function () {
            Route::get('manage-purchase-list', array('uses' => 'Peticash\PeticashController@getManageViewPeticashPurchaseApproval'));
            Route::get('manage-salary-list', array('uses' => 'Peticash\PeticashController@getManageViewPeticashSalaryApproval'));
            Route::post('manage-purchase-list-ajax', array('uses' => 'Peticash\PeticashController@purchaseApprovalListing'));
            Route::post('manage-salary-list-ajax', array('uses' => 'Peticash\PeticashController@salaryApprovalListing'));
            Route::post('manage-salary-details-ajax', array('uses' => 'Peticash\PeticashController@getSalaryTransactionDetails'));
            Route::post('manage-purchase-details-ajax', array('uses' => 'Peticash\PeticashController@getPurchaseTransactionDetails'));
            Route::post('approve-purchase-ajax', array('uses' => 'Peticash\PeticashController@approvePurchaseAjaxRequest'));
        });

        Route::group(['prefix' => 'salary-request'], function () {
            Route::get('create', array('uses' => 'Peticash\PeticashController@getSalaryRequestCreateView'));
            Route::post('create', array('uses' => 'Peticash\PeticashController@createSalaryRequestCreate'));
            Route::post('change-status', array('uses' => 'Peticash\PeticashController@salaryRequestedChangeStatus'));
        });


        Route::group(['prefix' => 'peticash-management'], function () {
            Route::post('change-voucher-status', array('uses' => 'Peticash\PeticashController@changeVoucherStatus'));
            Route::group(['prefix' => 'purchase'], function () {
                Route::get('manage', array('uses' => 'Peticash\PeticashController@getPurchaseManageView'));
                Route::post('listing', array('uses' => 'Peticash\PeticashController@purchaseTransactionListing'));
            });
            Route::group(['prefix' => 'salary'], function () {
                Route::get('create', array('uses' => 'Peticash\PeticashController@getSalaryCreateView'));
                Route::get('auto-suggest/{type}/{keyword}', array('uses' => 'Peticash\PeticashController@autoSuggest'));
                Route::post('create', array('uses' => 'Peticash\PeticashController@createSalaryCreate'));
                Route::get('manage', array('uses' => 'Peticash\PeticashController@getSalaryManageView'));
                Route::group(['prefix' => 'delete'], function () {
                    Route::delete('/', array('uses' => 'Peticash\PeticashController@deleteSalary'));
                    Route::post('show', array('uses' => 'Peticash\PeticashController@showdeleteSalary'));
                });
                Route::post('listing', array('uses' => 'Peticash\PeticashController@salaryTransactionListing'));
                Route::get('payment-voucher-pdf/{salaryTransactionId}', array('uses' => 'Peticash\PeticashController@getPaymentVoucherPdf'));
            });
            Route::group(['prefix' => 'cash-transaction'], function () {
                Route::get('manage', array('uses' => 'Peticash\PeticashController@getCashTransactionManage'));
                Route::post('listing', array('uses' => 'Peticash\PeticashController@getCashTransactionListing'));
            });
        });
    });

    Route::group(['prefix' => 'awareness'], function () {

        Route::group(['prefix' => 'category-management'], function () {
            Route::get('main-category-manage', array('uses' => 'Awareness\CategoryManagementController@getManageView'));
            Route::get('main-category-create', array('uses' => 'Awareness\CategoryManagementController@getCategoryCreateView'));
            Route::get('main-category-edit/{id}', array('uses' => 'Awareness\CategoryManagementController@getCategoryEditView'));
            Route::post('main-category-edit', array('uses' => 'Awareness\CategoryManagementController@mainCategoryEdit'));
            Route::post('sub-category-edit', array('uses' => 'Awareness\CategoryManagementController@subCategoryEdit'));
            Route::get('sub-category-edit/{id}', array('uses' => 'Awareness\CategoryManagementController@subCategoryEditView'));
            Route::post('main-category-create', array('uses' => 'Awareness\CategoryManagementController@createMainCategory'));
            Route::post('sub-category-create', array('uses' => 'Awareness\CategoryManagementController@createSubCategory'));
            Route::post('main-category-listing', array('uses' => 'Awareness\CategoryManagementController@mainCategoryListing'));
            Route::post('sub-category-listing', array('uses' => 'Awareness\CategoryManagementController@subCategoryListing'));
            Route::get('sub-category-manage', array('uses' => 'Awareness\CategoryManagementController@getSubManageView'));
            Route::get('sub-category-create', array('uses' => 'Awareness\CategoryManagementController@getSubCategoryCreateView'));
            Route::get('change-status/{slug}/{categoryId}', array('uses' => 'Awareness\CategoryManagementController@changeCategoryStatus'));
        });
        Route::group(['prefix' => 'file-management'], function () {
            Route::get('manage', array('uses' => 'Awareness\FileManagementController@getManageView'));
            Route::get('create', array('uses' => 'Awareness\FileManagementController@getCategoryCreateView'));
            Route::get('get-sub-categories/{id}', array('uses' => 'Awareness\FileManagementController@getMainCategories'));
            Route::post('file-upload', array('uses' => 'Awareness\FileManagementController@uploadFiles'));
            Route::post('get-files', array('uses' => 'Awareness\FileManagementController@displayFiles'));
            Route::post('create-awareness', array('uses' => 'Awareness\FileManagementController@create'));
            Route::post('edit-awareness', array('uses' => 'Awareness\FileManagementController@edit'));
            Route::post('get-subcategories', array('uses' => 'Awareness\FileManagementController@getSubCategories'));
            Route::post('get-subcategories-details', array('uses' => 'Awareness\FileManagementController@getSubCategoriesDetails'));
        });
    });

    Route::group(['prefix' => 'dpr'], function () {
        Route::get('category_manage', array('uses' => 'Dpr\DprController@getCategoryManageView'));
        Route::get('create-category-view', array('uses' => 'Dpr\DprController@getCategoryCreateView'));
        Route::get('create-dpr-view', array('uses' => 'Dpr\DprController@getDprCreateView'));
        Route::post('dpr-edit-view', array('uses' => 'Dpr\DprController@getDprEditView'));
        Route::get('category-edit/{id}', array('uses' => 'Dpr\DprController@getCategoryEditView'));
        Route::post('dpr-edit', array('uses' => 'Dpr\DprController@dprEdit'));
        Route::post('category-edit', array('uses' => 'Dpr\DprController@categoryEdit'));
        Route::post('create-category', array('uses' => 'Dpr\DprController@createCategory'));
        Route::post('create-dpr', array('uses' => 'Dpr\DprController@createDpr'));
        Route::get('manage_dpr', array('uses' => 'Dpr\DprController@getDprManageView'));
        Route::post('category-listing', array('uses' => 'Dpr\DprController@categoryListing'));
        Route::post('dpr-listing', array('uses' => 'Dpr\DprController@dprListing'));
        Route::post('temp-image-upload', array('uses' => 'Dpr\DprController@uploadTempImages'));
        Route::post('display-temp-files', array('uses' => 'Dpr\DprController@displayTempImages'));
        Route::post('delete-temp-image', array('uses' => 'Dpr\DprController@removeTempImage'));
        Route::post('delete-image', array('uses' => 'Dpr\DprController@removeImage'));
        Route::get('change-status/{id}/{status}', array('uses' => 'Dpr\DprController@changeStatus'));
        Route::group(['prefix' => 'subcontractor'], function () {
            Route::post('get-category', array('uses' => 'Dpr\DprController@getSubcontractorsCategories'));
        });
    });
    Route::get('inventory', array('uses' => 'Inventory\InventoryManageController@inventoryTransfer'));
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', array('uses' => 'Report\ReportController@reportsRoute'));
        Route::get('manage', array('uses' => 'Report\ReportManagementController@getView'));
        Route::post('download', array('uses' => 'Report\ReportController@downloadReports'));
        Route::post('detail', array('uses' => 'Report\ReportManagementController@getButtonDetail'));
        Route::get('get-report/{reportType}/{projectSiteId}/{firstParameter}/{secondParameter}/{thirdParameter}', array('uses' => 'Report\ReportManagementController@downloadDetailReport'));
        Route::post('subcontractor', array('uses' => 'Report\ReportManagementController@getSubcontractor'));
        Route::get('demo', array('uses' => 'Report\ReportManagementController@getSalesAmount'));
        Route::group(['prefix' => 'listing'], function () {
            Route::post('sales', array('uses' => 'Report\ReportManagementController@getSalesListing'));
            Route::post('expense', array('uses' => 'Report\ReportManagementController@getExpensesListing'));
            Route::post('advance-expense', array('uses' => 'Report\ReportManagementController@getAdvanceExpensesListing'));
        });
        Route::group(['prefix' => 'rental'], function () {
            Route::get('manage', array('uses' => 'Report\RentalReportController@getManageView'));
            Route::post('listing', array('uses' => 'Report\RentalReportController@listing'));
            Route::get('/bill/{rentBillId}', array('uses' => 'Report\RentalReportController@exportReport'));
            Route::get('/summary/{rentBillId}', array('uses' => 'Report\RentalReportController@exportSummaryReport'));
            Route::get('/rent', array('uses' => 'Report\RentalReportController@rentCalculationCron'));
        });
    });

    Route::group(['prefix' => 'notification'], function () {
        Route::post('store-fcm-token', array('uses' => 'Notification\NotificationController@storeFcmToken'));
    });
});
