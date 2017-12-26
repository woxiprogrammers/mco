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
    Route::get('testing-pdf',array('uses' => 'Purchase\PurchaseRequestController@createVendorQuotationPdf'));
    Route::get('/',array('uses' => 'Admin\AdminController@viewLogin'));
    Route::post('/authenticate',array('uses' => 'Auth\LoginController@login'));
    Route::get('/logout',array('uses' => 'Auth\LoginController@logout'));
    Route::get('/dashboard',array('uses' => 'Admin\DashboardController@index'));
    Route::post('/change-project-site',array('uses' => 'Auth\LoginController@changeProjectSite'));

    Route::group(['prefix' => 'user'],function (){
        Route::get('create',array('uses' => 'User\UserController@getUserView'));
        Route::post('create',array('uses' => 'User\UserController@createUser'));
        Route::post('get-permission',array('uses' => 'User\UserController@getPermission'));
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
        Route::get('projects/{client_id}',array('uses' => 'User\PurchaseController@getProjects'));
        Route::get('project-sites/{project_id}',array('uses' => 'User\PurchaseController@getProjectSites'));

        Route::group(['prefix' => 'material-request'], function(){
            Route::get('manage',array('uses'=> 'User\PurchaseController@getManageView'));
            Route::get('create',array('uses'=> 'User\PurchaseController@getCreateView'));
            Route::post('listing',array('uses'=> 'User\PurchaseController@getMaterialRequestListing'));
            Route::get('edit',array('uses'=> 'User\PurchaseController@editMaterialRequest'));
            Route::get('get-items',array('uses'=> 'User\PurchaseController@autoSuggest'));
            Route::post('get-units',array('uses'=> 'User\PurchaseController@getUnitsList'));
            Route::post('get-users',array('uses'=> 'User\PurchaseController@getUsersList'));
            Route::post('create',array('uses'=> 'User\PurchaseController@createMaterialList'));
            Route::post('material-requestWise-listing',array('uses'=> 'User\PurchaseController@getMaterialRequestWiseListing'));
            Route::get('material-requestWise-listing-view',array('uses'=> 'User\PurchaseController@getMaterialRequestWiseListingView'));
            Route::post('change-status/{newStatus}/{componentId?}',array('uses' => 'User\PurchaseController@changeMaterialRequestComponentStatus'));
            Route::get('get-material-request-component-details/{materialRequestComponent}',array('uses' => 'User\PurchaseController@getMaterialRequestComponentDetail'));
        });
        Route::group(['prefix' => 'purchase-request'], function(){
            Route::get('manage',array('uses'=> 'Purchase\PurchaseRequestController@getManageView'));
            Route::get('create',array('uses'=> 'Purchase\PurchaseRequestController@getCreateView'));
            Route::get('edit/{status}/{id}',array('uses'=> 'Purchase\PurchaseRequestController@getEditView'));
            Route::post('create',array('uses'=> 'Purchase\PurchaseRequestController@create'));
            Route::post('listing',array('uses'=> 'Purchase\PurchaseRequestController@purchaseRequestListing'));
            Route::post('change-status/{newStatus}/{componentId?}',array('uses' => 'Purchase\PurchaseRequestController@changePurchaseRequestStatus'));
            Route::post('assign-vendors',array('uses' => 'Purchase\PurchaseRequestController@assignVendors'));
            Route::post('get-in-indent-components',array('uses' => 'Purchase\PurchaseRequestController@getInIndentComponents'));
        });
        Route::group(['prefix' => 'purchase-order'], function(){
            Route::get('manage',array('uses'=> 'Purchase\PurchaseOrderController@getManageView'));
            Route::get('create',array('uses'=> 'Purchase\PurchaseOrderController@getCreateView'));
            Route::get('edit/{id}',array('uses'=> 'Purchase\PurchaseOrderController@getEditView'));
            Route::post('listing',array('uses'=> 'Purchase\PurchaseOrderController@getListing'));
            Route::post('get-details',array('uses'=> 'Purchase\PurchaseOrderController@getPurchaseOrderComponentDetails'));
            //Route::post('get-bill-details',array('uses'=> 'Purchase\PurchaseOrderController@getPurchaseOrderBillDetails'));
            Route::post('add-payment',array('uses'=> 'Purchase\PurchaseOrderController@createPayment'));
            Route::post('change-status',array('uses'=> 'Purchase\PurchaseOrderController@changeStatus'));
            Route::post('create-material',array('uses'=> 'Purchase\PurchaseOrderController@createMaterial'));
            Route::post('create-asset',array('uses'=> 'Purchase\PurchaseOrderController@createAsset'));
            Route::post('create',array('uses'=> 'Purchase\PurchaseOrderController@createPurchaseOrder'));
            Route::get('get-purchase-request-component/{purchaseRequest}',array('uses'=> 'Purchase\PurchaseOrderController@getPurchaseRequestComponents'));
            Route::get('get-client-project/{purchaseRequest}',array('uses'=> 'Purchase\PurchaseOrderController@getClientProjectName'));
            Route::get('download-po-pdf/{purchaseOrder}',array('uses'=> 'Purchase\PurchaseOrderController@downloadPoPDF'));
            Route::post('close-purchase-order',array('uses'=> 'Purchase\PurchaseOrderController@closePurchaseOrder'));
            Route::post('reopen',array('uses'=> 'Purchase\PurchaseOrderController@reopenPurchaseOrder'));
            Route::post('get-component-details',array('uses'=> 'Purchase\PurchaseOrderController@getComponentDetails'));
            Route::get('get-purchase-order-details/{purchaseRequestId}',array('uses'=> 'Purchase\PurchaseOrderController@getOrderDetails'));
            Route::group(['prefix' => 'transaction'], function(){
                Route::post('upload-pre-grn-images',array('uses'=> 'Purchase\PurchaseOrderController@preGrnImageUpload'));
                Route::post('create',array('uses'=> 'Purchase\PurchaseOrderController@createTransaction'));
                Route::get('get-details',array('uses'=> 'Purchase\PurchaseOrderController@getTransactionDetails'));
                Route::get('check-generated-grn/{purchaseOrder}',array('uses'=> 'Purchase\PurchaseOrderController@checkGeneratedGRN'));
                Route::get('edit/{purchaseOrderTransaction}',array('uses'=> 'Purchase\PurchaseOrderController@getTransactionEditView'));
                Route::post('edit/{purchaseOrderTransaction}',array('uses'=> 'Purchase\PurchaseOrderController@transactionEdit'));
            });
        });

        Route::group(['prefix' => 'purchase-order-bill'],function(){
            Route::get('manage',array('uses' => 'Purchase\PurchaseOrderBillingController@getManageView'));
            Route::get('create',array('uses' => 'Purchase\PurchaseOrderBillingController@getCreateView'));
            Route::post('create',array('uses' => 'Purchase\PurchaseOrderBillingController@createBill'));
            Route::post('get-project-sites',array('uses' => 'Purchase\PurchaseOrderBillingController@getProjectSites'));
            Route::post('get-purchase-orders',array('uses' => 'Purchase\PurchaseOrderBillingController@getPurchaseOrders'));
            Route::post('get-bill-pending-transactions',array('uses' => 'Purchase\PurchaseOrderBillingController@getBillPendingTransactions'));
            Route::post('get-transaction-subtotal',array('uses' => 'Purchase\PurchaseOrderBillingController@getTransactionSubtotal'));
            Route::post('listing',array('uses' => 'Purchase\PurchaseOrderBillingController@listing'));
            Route::get('edit/{purchaseOrderBill}',array('uses' => 'Purchase\PurchaseOrderBillingController@getEditView'));
            Route::group(['prefix' => 'payment'], function(){
                Route::post('listing/{purchaseOrderBillId}',array('uses' => 'Purchase\PurchaseOrderBillingController@paymentListing'));
                Route::post('create',array('uses' => 'Purchase\PurchaseOrderBillingController@createPayment'));
            });
        });
        Route::group(['prefix' => 'vendor-mail'],function(){
            Route::get('manage',array('uses' => 'Purchase\VendorMailController@getManageView'));
            Route::post('listing',array('uses' => 'Purchase\VendorMailController@listing'));
        });
    });

    Route::group(['prefix' => 'inventory'], function(){
        Route::get('manage',array('uses'=> 'Inventory\InventoryManageController@getManageView'));
        Route::post('listing',array('uses'=> 'Inventory\InventoryManageController@inventoryListing'));
        Route::post('get-project-sites',array('uses'=> 'Inventory\InventoryManageController@getProjectSites'));
       /* Route::get('create',array('uses'=> 'Inventory\InventoryManageController@getCreateView'));
        Route::get('edit',array('uses'=> 'Purchase\PurchaseOrderController@getEditView'));*/
        Route::group(['prefix' => 'component'], function(){
            Route::post('create',array('uses' => 'Inventory\InventoryManageController@createInventoryComponent'));
            Route::post('listing/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@inventoryComponentListing'));
            Route::get('manage/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@getComponentManageView'));
            Route::post('add-transfer/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@addComponentTransfer'));
            Route::post('image-upload/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@uploadTempImages'));
            Route::post('display-images/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@displayTempImages'));
            Route::post('delete-temp-inventory-image',array('uses'=>'Drawing\ImagesController@removeTempImage'));
            Route::post('edit-opening-stock',['uses' => 'Inventory\InventoryManageController@editOpeningStock']);
            Route::get('detail/{inventoryComponentTransfer}',['uses' => 'Inventory\InventoryManageController@getInventoryComponentTransferDetail']);
            Route::group(['prefix' => 'readings'],function(){
                Route::post('listing/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@inventoryComponentReadingListing'));
                Route::post('add/{inventoryComponent}',array('uses'=> 'Inventory\InventoryManageController@addInventoryComponentReading'));
            });
        });
        Route::group(['prefix' => 'transfer'], function (){
            Route::get('manage',array('uses' => 'Inventory\InventoryManageController@getTransferManageView'));
            Route::post('listing',array('uses'=> 'Inventory\InventoryManageController@getSiteTransferRequestListing'));
            Route::get('auto-suggest/{projectSiteId}/{type}/{keyword}',array('uses' => 'Inventory\InventoryManageController@autoSuggest'));
            Route::post('change-status/{status}/{inventoryTransferId}',array('uses'=> 'Inventory\InventoryManageController@changeStatus'));
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
        Route::post('check-name',array('uses'=> 'Admin\AssetManagementController@checkModel'));
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
        Route::post('get-projects',array('uses'=> 'Checklist\ChecklistSiteAssignmentController@getProjects'));
        Route::post('get-project-sites',array('uses'=> 'Checklist\ChecklistSiteAssignmentController@getProjectSites'));
        Route::post('get-quotation-floors',array('uses'=> 'Checklist\ChecklistSiteAssignmentController@getQuotationFloors'));
        Route::group(['prefix' => 'category-management'], function(){
            Route::get('manage',array('uses'=> 'Checklist\CategoryManagementController@getManageView'));
            Route::get('edit',array('uses'=> 'Checklist\CategoryManagementController@getEditView'));
            Route::post('listing/{slug}',array('uses'=> 'Checklist\CategoryManagementController@getCategoryManagementListing'));
            Route::post('create/{slug}',array('uses'=> 'Checklist\CategoryManagementController@createCategories'));
            Route::get('change-status/{checklistCategory}',array('uses'=> 'Checklist\CategoryManagementController@changeStatus'));
        });

        Route::group(['prefix' => 'structure'],function(){
            Route::get('manage',array('uses' => 'Checklist\ChecklistController@getManageView'));
            Route::get('create',array('uses' => 'Checklist\ChecklistController@getCreateView'));
            Route::get('edit/{checklistCategory}',array('uses' => 'Checklist\ChecklistController@getStructureEditView'));
            Route::post('create',array('uses' => 'Checklist\ChecklistController@createStructure'));
            Route::post('listing',array('uses' => 'Checklist\ChecklistController@structureListing'));
            Route::post('get-sub-category',array('uses' => 'Checklist\ChecklistController@getSubCategories'));
            Route::post('get-checkpoint-partial-view',array('uses' => 'Checklist\ChecklistController@getCheckpointPartialView'));
            Route::post('get-checkpoint-image-partial-view',array('uses' => 'Checklist\ChecklistController@getCheckpointImagePartialView'));
        });

        Route::group(['prefix' => 'site-assignment'],function(){
            Route::get('manage',array('uses' => 'Checklist\ChecklistSiteAssignmentController@getManageView'));
            Route::get('create',array('uses' => 'Checklist\ChecklistSiteAssignmentController@getCreateView'));
            Route::get('edit/{projectSiteChecklist}',array('uses' => 'Checklist\ChecklistSiteAssignmentController@getSiteAssignmentEditView'));
            Route::get('get-checkpoints/{checklistCategory}',array('uses' => 'Checklist\ChecklistSiteAssignmentController@getCheckpoints'));
            Route::post('create',array('uses' => 'Checklist\ChecklistSiteAssignmentController@siteAssignmentCreate'));
            Route::post('listing',array('uses' => 'Checklist\ChecklistSiteAssignmentController@siteAssignmentListing'));
        });

        Route::group(['prefix' => 'user-assignment'], function(){
            Route::get('manage',array('uses' => 'Checklist\ChecklistUserAssignmentController@getManageView'));
            Route::get('create',array('uses' => 'Checklist\ChecklistUserAssignmentController@getCreateView'));
            Route::post('create',array('uses' => 'Checklist\ChecklistUserAssignmentController@createUserAssignment'));
            Route::post('get-categories',array('uses' => 'Checklist\ChecklistUserAssignmentController@getCategories'));
            Route::post('get-users',array('uses' => 'Checklist\ChecklistUserAssignmentController@getUsers'));
        });
    });

    Route::group(['prefix'=>'drawing'],function() {
        Route::group(['prefix' => 'category-management'], function(){
            Route::get('manage',array('uses'=> 'Drawing\CategoryManagementController@getManageView'));
            Route::get('sub-category-manage',array('uses'=> 'Drawing\CategoryManagementController@getSubCategoryManageView'));
            Route::get('create-main',array('uses'=> 'Drawing\CategoryManagementController@getCreateMainView'));
            Route::post('create-main-category',array('uses'=> 'Drawing\CategoryManagementController@getCreateMainCategory'));
            Route::post('main-category-listing',array('uses'=> 'Drawing\CategoryManagementController@MainCategoryListing'));
            Route::post('sub-category-listing',array('uses'=> 'Drawing\CategoryManagementController@SubCategoryListing'));
            Route::post('create-sub-category',array('uses'=> 'Drawing\CategoryManagementController@createSubCategory'));
            Route::get('create-sub',array('uses'=> 'Drawing\CategoryManagementController@getCreateSubView'));
            Route::get('change-status/{id}/{status}',array('uses'=> 'Drawing\CategoryManagementController@changeStatus'));
            Route::get('edit/{id}',array('uses'=> 'Drawing\CategoryManagementController@getMainEditView'));
            Route::post('edit-main-category',array('uses'=> 'Drawing\CategoryManagementController@mainCategoryEdit'));
            Route::get('edit-sub/{id}',array('uses'=> 'Drawing\CategoryManagementController@getSubEditView'));
            Route::post('edit-sub-category',array('uses'=> 'Drawing\CategoryManagementController@editSubCategory'));
        });
        Route::group(['prefix' => 'images'], function(){
            Route::get('manage',array('uses'=> 'Drawing\ImagesController@getManageView'));
            Route::get('create',array('uses'=> 'Drawing\ImagesController@getCreateView'));
            Route::get('get-details/{id}',array('uses'=> 'Drawing\ImagesController@getDetails'));
            Route::get('manage-drawings',array('uses'=> 'Drawing\ImagesController@getManageDrawingsView'));
            Route::get('edit/{id}/{site_id}',array('uses'=> 'Drawing\ImagesController@getEditView'));
            Route::post('image-upload/{quotationId}',array('uses'=>'Drawing\ImagesController@uploadTempDrawingImages'));
            Route::post('get-projects',array('uses'=>'Drawing\ImagesController@getProjects'));
            Route::post('get-project-sites',array('uses'=>'Drawing\ImagesController@getProjectSites'));
            Route::post('get-sub-categories',array('uses'=>'Drawing\ImagesController@getSubCategories'));
            Route::post('display-images/{quotationId}',array('uses'=>'Drawing\ImagesController@displayDrawingImages'));
            Route::post('delete-temp-product-image',array('uses'=>'Drawing\ImagesController@removeTempImage'));
            Route::post('create',array('uses'=>'Drawing\ImagesController@create'));
            Route::post('listing',array('uses'=>'Drawing\ImagesController@listing'));
            Route::post('add-version',array('uses'=>'Drawing\ImagesController@createVersion'));
            Route::post('get-data',array('uses'=>'Drawing\ImagesController@getData'));
            Route::post('get-versions',array('uses'=>'Drawing\ImagesController@getAllVersions'));
            Route::post('add-comment',array('uses'=>'Drawing\ImagesController@addComment'));
        });
    });

    Route::group(['prefix'=>'labour'],function (){
       Route::get('create',array('uses' => 'Labour\LabourController@getCreateView'));
       Route::post('create',array('uses' => 'Labour\LabourController@createLabour'));
       Route::get('manage',array('uses' => 'Labour\LabourController@getManageView'));
       Route::post('listing',array('uses' => 'Labour\LabourController@labourListing'));
       Route::get('change-status/{labour}', array('uses' => 'Labour\LabourController@changeLabourStatus'));
       Route::get('edit/{labour}', array('uses' => 'Labour\LabourController@getEditView'));
       Route::post('edit/{labour}', array('uses' => 'Labour\LabourController@editLabour'));
       Route::get('employee-id/{employee_type}', array('uses' => 'Labour\LabourController@getEmployeeId'));
    });

    Route::group(['prefix'=>'subcontractor'],function (){
        Route::get('create',array('uses' => 'Subcontractor\SubcontractorController@getCreateView'));
        Route::post('create',array('uses' => 'Subcontractor\SubcontractorController@createSubcontractor'));
        Route::get('manage',array('uses' => 'Subcontractor\SubcontractorController@getManageView'));
        Route::post('listing',array('uses' => 'Subcontractor\SubcontractorController@subcontractorListing'));
        Route::get('change-status/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@changeSubcontractorStatus'));
        Route::get('edit/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@getEditView'));
        Route::post('edit/{subcontractor}', array('uses' => 'Subcontractor\SubcontractorController@editSubcontractor'));

        Route::group(['prefix' => 'subcontractor-structure'], function(){
            Route::get('manage',array('uses' => 'Subcontractor\SubcontractorController@getManageStructureView'));
            Route::post('create',array('uses' => 'Subcontractor\SubcontractorController@createSubcontractor'));
            Route::get('create',array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureView'));
            Route::post('listing',array('uses' => 'Subcontractor\SubcontractorController@subcontractorStructureListing'));
            Route::get('edit/{labour}', array('uses' => 'Subcontractor\SubcontractorController@getSubcontractorStructureEditView'));
            Route::post('edit/{labour}', array('uses' => 'Subcontractor\SubcontractorController@editSubcontractorStructure'));
        });

        Route::group(['prefix' => 'subcontractor-bills'], function(){
            Route::get('manage',array('uses' => 'Subcontractor\SubcontractorController@getManageBillsView'));
        });
    });

    Route::group(['prefix'=>'peticash'],function (){

        Route::get('projects/{client_id}',array('uses' => 'Peticash\PeticashController@getProjects'));
        Route::get('project-sites/{project_id}',array('uses' => 'Peticash\PeticashController@getProjectSites'));
        Route::post('change-status',array('uses' => 'Peticash\PeticashController@changeSalaryStatus'));
        Route::post('change-status-purchase-disapproved',array('uses' => 'Peticash\PeticashController@changePurchaseStatus'));
        Route::post('stats-salary',array('uses' => 'Peticash\PeticashController@getSalaryStats'));

        Route::group(['prefix' => 'master-peticash-account'], function(){
            Route::get('manage',array('uses' => 'Peticash\PeticashController@getManageViewForMasterPeticashAccount'));
            Route::get('createpage',array('uses' => 'Peticash\PeticashController@getCreateViewForMasterPeticashAccount'));
            Route::post('create',array('uses' => 'Peticash\PeticashController@createMasterPeticashAccount'));
            Route::get('editpage/{txnid}',array('uses' => 'Peticash\PeticashController@editViewMasterPeticashAccount'));
            Route::post('edit',array('uses' => 'Peticash\PeticashController@editMasterPeticashAccount'));
            Route::post('listing',array('uses' => 'Peticash\PeticashController@masterAccountListing'));
        });

        Route::group(['prefix' => 'sitewise-peticash-account'], function(){
            Route::get('manage',array('uses' => 'Peticash\PeticashController@getManageViewForSitewisePeticashAccount'));
            Route::get('createpage',array('uses' => 'Peticash\PeticashController@getCreateViewForSitewisePeticashAccount'));
            Route::post('create',array('uses' => 'Peticash\PeticashController@createSitewisePeticashAccount'));
            Route::post('listing',array('uses' => 'Peticash\PeticashController@sitewiseAccountListing'));
            Route::get('getuserlistbysite/{siteid}',array('uses' => 'Peticash\PeticashController@getUserBySites'));
            Route::get('editpage/{txnid}',array('uses' => 'Peticash\PeticashController@editViewSitewisePeticashAccount'));
            Route::post('edit',array('uses' => 'Peticash\PeticashController@editSitewisePeticashAccount'));
        });

        Route::group(['prefix' => 'peticash-approval-request'], function(){
            Route::get('manage-purchase-list',array('uses' => 'Peticash\PeticashController@getManageViewPeticashPurchaseApproval'));
            Route::get('manage-salary-list',array('uses' => 'Peticash\PeticashController@getManageViewPeticashSalaryApproval'));
            Route::post('manage-purchase-list-ajax',array('uses' => 'Peticash\PeticashController@purchaseApprovalListing'));
            Route::post('manage-salary-list-ajax',array('uses' => 'Peticash\PeticashController@salaryApprovalListing'));
            Route::post('manage-salary-details-ajax',array('uses' => 'Peticash\PeticashController@getSalaryTransactionDetails'));
            Route::post('manage-purchase-details-ajax',array('uses' => 'Peticash\PeticashController@getPurchaseTransactionDetails'));
            Route::post('approve-purchase-ajax',array('uses' => 'Peticash\PeticashController@approvePurchaseAjaxRequest'));
        });
        Route::group(['prefix' => 'salary-request'], function(){
            Route::get('create',array('uses' => 'Peticash\PeticashController@getSalaryRequestCreateView'));
            Route::post('create',array('uses' => 'Peticash\PeticashController@createSalaryRequestCreate'));
            Route::post('get-labours',array('uses' => 'Peticash\PeticashController@getLabours'));
            Route::post('change-status',array('uses' => 'Peticash\PeticashController@salaryRequestedChangeStatus'));
        });


        Route::group(['prefix' => 'peticash-management'], function(){
//            Route::get('manage',array('uses' => 'Peticash\PeticashController@getManageViewPeticashManagement'));
            Route::group(['prefix' => 'purchase'], function(){
                Route::get('manage',array('uses' => 'Peticash\PeticashController@getPurchaseManageView'));
                Route::post('listing',array('uses' => 'Peticash\PeticashController@purchaseTransactionListing'));
            });
            Route::group(['prefix' => 'salary'], function(){
                Route::get('manage',array('uses' => 'Peticash\PeticashController@getSalaryManageView'));
                Route::post('listing',array('uses' => 'Peticash\PeticashController@salaryTransactionListing'));
            });
        });
    });

    Route::group(['prefix' => 'awareness'], function(){

          Route::group(['prefix' => 'category-management'], function(){
                Route::get('main-category-manage',array('uses' => 'Awareness\CategoryManagementController@getManageView'));
                Route::get('main-category-create',array('uses' => 'Awareness\CategoryManagementController@getCategoryCreateView'));
                Route::get('main-category-edit/{id}',array('uses' => 'Awareness\CategoryManagementController@getCategoryEditView'));
                Route::post('main-category-edit',array('uses' => 'Awareness\CategoryManagementController@mainCategoryEdit'));
                Route::post('sub-category-edit',array('uses' => 'Awareness\CategoryManagementController@subCategoryEdit'));
                Route::get('sub-category-edit/{id}',array('uses' => 'Awareness\CategoryManagementController@subCategoryEditView'));
                Route::post('main-category-create',array('uses' => 'Awareness\CategoryManagementController@createMainCategory'));
                Route::post('sub-category-create',array('uses' => 'Awareness\CategoryManagementController@createSubCategory'));
                Route::post('main-category-listing',array('uses' => 'Awareness\CategoryManagementController@mainCategoryListing'));
                Route::post('sub-category-listing',array('uses' => 'Awareness\CategoryManagementController@subCategoryListing'));
                Route::get('sub-category-manage',array('uses' => 'Awareness\CategoryManagementController@getSubManageView'));
                Route::get('sub-category-create',array('uses' => 'Awareness\CategoryManagementController@getSubCategoryCreateView'));
          });
        Route::group(['prefix' => 'file-management'], function(){
            Route::get('manage',array('uses' => 'Awareness\FileManagementController@getManageView'));
            Route::get('create',array('uses' => 'Awareness\FileManagementController@getCategoryCreateView'));
            Route::get('get-sub-categories/{id}',array('uses' => 'Awareness\FileManagementController@getMainCategories'));
            Route::post('file-upload',array('uses'=>'Awareness\FileManagementController@uploadFiles'));
            Route::post('get-files',array('uses'=>'Awareness\FileManagementController@displayFiles'));
            Route::post('create-awareness',array('uses'=>'Awareness\FileManagementController@create'));
            Route::post('edit-awareness',array('uses'=>'Awareness\FileManagementController@edit'));
            Route::post('get-subcategories',array('uses'=>'Awareness\FileManagementController@getSubCategories'));
            Route::post('get-subcategories-details',array('uses'=>'Awareness\FileManagementController@getSubCategoriesDetails'));
        });

    });
    Route::group(['prefix'=>'dpr'],function (){
        Route::get('category_manage',array('uses' => 'Dpr\DprController@getCategoryManageView'));
        Route::get('create-category-view',array('uses' => 'Dpr\DprController@getCategoryCreateView'));
        Route::get('create-dpr-view',array('uses' => 'Dpr\DprController@getDprCreateView'));
        Route::get('dpr-edit/{id}',array('uses' => 'Dpr\DprController@getDprEditView'));
        Route::get('category-edit/{id}',array('uses' => 'Dpr\DprController@getCategoryEditView'));
        Route::post('dpr-edit',array('uses' => 'Dpr\DprController@dprEdit'));
        Route::post('category-edit',array('uses' => 'Dpr\DprController@categoryEdit'));
        Route::post('create-category',array('uses' => 'Dpr\DprController@createCategory'));
        Route::post('create-dpr',array('uses' => 'Dpr\DprController@createDpr'));
        Route::get('manage_dpr',array('uses' => 'Dpr\DprController@getDprManageView'));
        Route::post('category-listing',array('uses' => 'Dpr\DprController@categoryListing'));
        Route::post('dpr-listing',array('uses' => 'Dpr\DprController@dprListing'));
        Route::get('change-status/{id}/{status}',array('uses'=> 'Dpr\DprController@changeStatus'));
    });

});
