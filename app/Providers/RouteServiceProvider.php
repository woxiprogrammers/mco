<?php

namespace App\Providers;

use App\Address;
use App\Asset;
use App\BankInfo;
use App\Bill;
use App\BillTransaction;
use App\Category;
use App\ChecklistCategory;
use App\City;
use App\Client;
use App\Employee;
use App\ExtraItem;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\Material;
use App\MaterialRequestComponents;
use App\Product;
use App\ProfitMargin;
use App\Project;
use App\ProjectSite;
use App\ProjectSiteChecklist;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderRequest;
use App\PurchaseOrderRequestComponent;
use App\PurchaseOrderTransaction;
use App\PurchaseRequestComponent;
use App\PurchaseRequestComponentVendorRelation;
use App\Quotation;
use App\QuotationWorkOrder;
use App\Role;
use App\SiteTransferBill;
use App\Subcontractor;
use App\SubcontractorStructure;
use App\Summary;
use App\Unit;
use App\Tax;
use App\UnitConversion;
use App\User;
use App\Vendor;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        Route::model('category',Category::class);
        Route::model('summary',Summary::class);
        Route::model('unit',Unit::class);
        Route::model('tax',Tax::class);
        Route::model('material',Material::class);
        Route::model('profit_margin',ProfitMargin::class);
        Route::model('product',Product::class);
        Route::model('user',User::class);
        Route::model('client',Client::class);
        Route::model('project_site',ProjectSite::class);
        Route::model('bill',Bill::class);
        Route::model('unit_conversion',UnitConversion::class);
        Route::model('project',Project::class);
        Route::model('quotation',Quotation::class);
        Route::model('work_order',QuotationWorkOrder::class);
        Route::model('bill_transaction',BillTransaction::class);
        Route::model('role',Role::class);
        Route::model('extra_item',ExtraItem::class);
        Route::model('vendor',Vendor::class);
        Route::model('city',City::class);
        Route::model('bank_info',BankInfo::class);
        Route::model('asset',Asset::class);
        Route::model('inventoryComponent',InventoryComponent::class);
        Route::model('inventoryComponentTransfer',InventoryComponentTransfers::class);
        Route::model('labour',Employee::class);
        Route::model('materialRequestComponent',MaterialRequestComponents::class);
        Route::model('purchaseOrder',PurchaseOrder::class);
        Route::model('checklistCategory',ChecklistCategory::class);
        Route::model('purchaseOrderBill',PurchaseOrderBill::class);
        Route::model('subcontractor',Subcontractor::class);
        Route::model('purchaseOrderTransaction',PurchaseOrderTransaction::class);
        Route::model('projectSiteChecklist',ProjectSiteChecklist::class);
        Route::model('subcontractor_struct',SubcontractorStructure::class);
        Route::model('purchaseRequestComponent',PurchaseRequestComponent::class);
        Route::model('purchaseOrderRequest',PurchaseOrderRequest::class);
        Route::model('purchaseComponentVendorRelation',PurchaseRequestComponentVendorRelation::class);
        Route::model('purchaseOrderRequestComponent',PurchaseOrderRequestComponent::class);
        Route::model('subcontractorStructure',SubcontractorStructure::class);
        Route::model('siteTransferBill',SiteTransferBill::class);
        Route::model('address',Address::class);
        Route::model('subcontractorStructure', SubcontractorStructure::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
