<?php

namespace App\Console\Commands;

use App\AssetMaintenanceBill;
use App\AssetMaintenanceBillPayment;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Month;
use App\ProjectSite;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillMonthlyExpense;
use App\SiteTransferBill;
use App\Year;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderBillMonthlyExpenseCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //php artisan custom:purchase-order-bill-monthly-expense-calculation --month=all --year=all  => Executes Case 1
    //php artisan custom:purchase-order-bill-monthly-expense-calculation --month=all --year=2018  => Executes Case 2
    //php artisan custom:purchase-order-bill-monthly-expense-calculation  => Executes Case 3 => $thisMonth == 'null' && $thisYear == 'null'
    //php artisan custom:purchase-order-bill-monthly-expense-calculation --month=8 --year=2018  => Executes Case 4

    protected $signature = 'custom:purchase-order-bill-monthly-expense-calculation {--month=null} {--year=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        try{
            $thisMonth = $this->option('month');
            $thisYear = $this->option('year');
            $year = new Year();
            $month = new Month();
            $projectSite = new ProjectSite();
            $purchaseOrderBill = new PurchaseOrderBill();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryTransferTypes = new InventoryTransferTypes();
            $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
            $siteTransferBill = new SiteTransferBill();
            $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
            $purchaseOrderBillMonthlyExpenses = new PurchaseOrderBillMonthlyExpense();

            $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
            $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
            switch (true){
                case ($thisMonth == 'all' && $thisYear == 'all') :
                    $currentYearId = $year->where('slug',date('Y'))->pluck('id')->first();
                    $tillCurrentYearIds = $year->where('id','>=',$currentYearId)->pluck('id');
                    $monthsData = $month->pluck('id')->toArray();
                    foreach ($tillCurrentYearIds as $thisYearId){
                        $thisYear = $year->where('id',$thisYearId)->pluck('slug')->first();
                        foreach ($monthsData as $thisMonth){
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $purchaseOrderBillData = $purchaseOrderBill
                                    ->join('purchase_orders','purchase_orders.id','='
                                        ,'purchase_order_bills.purchase_order_id')
                                    ->join('purchase_requests','purchase_requests.id','='
                                        ,'purchase_orders.purchase_request_id')
                                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)
                                    ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                    ->whereYear('purchase_order_bills.created_at',$thisYear)
                                    // ->select('purchase_order_bills.amount as basic_amount',
                                    // 'purchase_order_bills.transportation_tax_amount as transportation_tax_amount',
                                    // 'purchase_order_bills.tax_amount as tax_amount',
                                    // 'purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                    // ->get();
                                    ->sum(DB::raw('purchase_order_bills.amount +
                                    purchase_order_bills.extra_amount +
                                    purchase_order_bills.transportation_total_amount'));
                                $purchaseOrderBillTotalAmount =  round($purchaseOrderBillData,3);
                                // $purchaseOrderBillTotalAmount = $purchaseOrderBillAmount;

                                $purchaseOrderGst = round($purchaseOrderBill
                                ->join('purchase_orders','purchase_orders.id','='
                                    ,'purchase_order_bills.purchase_order_id')
                                ->join('purchase_requests','purchase_requests.id','='
                                    ,'purchase_orders.purchase_request_id')
                                ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                ->where('purchase_requests.project_site_id',$projectSiteId)
                                ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                ->whereYear('purchase_order_bills.created_at',$thisYear)
                                ->sum(DB::raw('purchase_order_bills.transportation_tax_amount +
                                 purchase_order_bills.tax_amount +
                                 purchase_order_bills.extra_tax_amount')),3);

                                $inventorySiteTransfersInTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum('inventory_component_transfers.total');

                                $inventorySiteTransfersInGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum(DB::raw('inventory_component_transfers.cgst_amount + 
                                                   inventory_component_transfers.sgst_amount + 
                                                   inventory_component_transfers.igst_amount'));

                                $inventorySiteTransfersOutTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum('inventory_component_transfers.total');
                                
                                $inventorySiteTransfersOutGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum(DB::raw('inventory_component_transfers.cgst_amount +
                                                 inventory_component_transfers.sgst_amount +
                                                 inventory_component_transfers.igst_amount'));
        
                                $inventorySiteTransfersInTotalBasic = $inventorySiteTransfersInTotal - $inventorySiteTransfersInGst;
                                $inventorySiteTransfersOutTotalBasic = $inventorySiteTransfersOutTotal - $inventorySiteTransfersOutGst;

                                $inventorySiteTransfersTotalBasic = $inventorySiteTransfersInTotalBasic - $inventorySiteTransfersOutTotalBasic;
                                $inventorySiteTransfersTotalGST = $inventorySiteTransfersInGst - $inventorySiteTransfersOutGst;

                                $siteTransferBillTotal = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                    '=','site_transfer_bills.inventory_component_transfer_id')
                                    ->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                    ->whereYear('site_transfer_bills.created_at',$thisYear)
                                    ->sum('site_transfer_bills.total');

                                $siteTransferBillGst = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                    '=','site_transfer_bills.inventory_component_transfer_id')
                                    ->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                    ->whereYear('site_transfer_bills.created_at',$thisYear)
                                    ->sum(DB::raw('site_transfer_bills.tax_amount + 
                                    site_transfer_bills.extra_amount_cgst_amount + 
                                    site_transfer_bills.extra_amount_sgst_amount + 
                                    site_transfer_bills.extra_amount_igst_amount'));

                                $siteTransferBillTotalBasic = $siteTransferBillTotal - $siteTransferBillGst;

                                $assetMaintenanceBillPaymentTotal = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                    ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                                    ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                    ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                    ->sum(DB::raw('asset_maintenance_bills.amount + 
                                    asset_maintenance_bills.extra_amount'));


                                $assetMaintenanceGst = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                        ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                        ->where('asset_maintenance.project_site_id',$projectSiteId)
                                        ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                        ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                        ->sum(DB::raw('asset_maintenance_bills.cgst_amount + 
                                                    asset_maintenance_bills.sgst_amount + 
                                                    asset_maintenance_bills.igst_amount'));

                                $totalAmount = $purchaseOrderBillTotalAmount + $inventorySiteTransfersTotalBasic 
                                                + $assetMaintenanceBillPaymentTotal + $siteTransferBillTotalBasic
                                                + $purchaseOrderGst + $inventorySiteTransfersTotalGST
                                                + $assetMaintenanceGst + $siteTransferBillGst;

                                if($totalAmount != 0){
                                    $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)
                                                    ->where('month_id',$thisMonth)
                                                    ->where('year_id',$thisYearId)->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                            'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                            'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                            'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                            'total_expense' => round($totalAmount,3),
                                            'purchase_gst' => round($purchaseOrderGst,3),
                                            'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                            'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                            'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                        ]);
                                        
                                    }else{
                                        $purchaseOrderBillMonthlyExpenses->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $thisMonth,
                                            'year_id' => $thisYearId,
                                            'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                            'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                            'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                            'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                            'total_expense' => round($totalAmount,3),
                                            'purchase_gst' => round($purchaseOrderGst,3),
                                            'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                            'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                            'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                    break;

                case ($thisMonth == 'all' && $thisYear != null) :
                    $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                    if($yearId == null){
                        $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                    }else{
                        $monthsData = $month->pluck('id')->toArray();
                            foreach ($monthsData as $thisMonth){
                                $projectSiteIds = $projectSite->pluck('id')->toArray();
                                foreach ($projectSiteIds as $projectSiteId){
                                    $purchaseOrderBillData = $purchaseOrderBill
                                        ->join('purchase_orders','purchase_orders.id','='
                                            ,'purchase_order_bills.purchase_order_id')
                                        ->join('purchase_requests','purchase_requests.id','='
                                            ,'purchase_orders.purchase_request_id')
                                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                        ->where('purchase_requests.project_site_id',$projectSiteId)
                                        ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                        ->whereYear('purchase_order_bills.created_at',$thisYear)
                                        ->sum(DB::raw('purchase_order_bills.amount +
                                        purchase_order_bills.extra_amount +
                                        purchase_order_bills.transportation_total_amount'));

                                    $purchaseOrderBillTotalAmount =  round($purchaseOrderBillData,3);

                                    $purchaseOrderGst = round($purchaseOrderBill
                                        ->join('purchase_orders','purchase_orders.id','='
                                            ,'purchase_order_bills.purchase_order_id')
                                        ->join('purchase_requests','purchase_requests.id','='
                                            ,'purchase_orders.purchase_request_id')
                                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                        ->where('purchase_requests.project_site_id',$projectSiteId)
                                        ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                        ->whereYear('purchase_order_bills.created_at',$thisYear)
                                        ->sum(DB::raw('purchase_order_bills.transportation_tax_amount +
                                        purchase_order_bills.tax_amount +
                                        purchase_order_bills.extra_tax_amount')),3);

                                    $inventorySiteTransfersInTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->where('inventory_components.is_material',true)
                                        ->where('inventory_component_transfers.transfer_type_id',
                                            $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                        ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                        ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                        ->sum('inventory_component_transfers.total');

                                    $inventorySiteTransfersInGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->where('inventory_components.is_material',true)
                                        ->where('inventory_component_transfers.transfer_type_id',
                                            $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                        ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                        ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                        ->sum(DB::raw('inventory_component_transfers.cgst_amount + 
                                                       inventory_component_transfers.sgst_amount + 
                                                       inventory_component_transfers.igst_amount'));

                                    $inventorySiteTransfersOutTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->where('inventory_components.is_material',true)
                                        ->where('inventory_component_transfers.transfer_type_id',
                                            $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                        ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                        ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                        ->sum('inventory_component_transfers.total');
                                    
                                    $inventorySiteTransfersOutGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->where('inventory_components.is_material',true)
                                        ->where('inventory_component_transfers.transfer_type_id',
                                            $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                        ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                        ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                        ->sum(DB::raw('inventory_component_transfers.cgst_amount +
                                                     inventory_component_transfers.sgst_amount +
                                                     inventory_component_transfers.igst_amount'));
            
                                    $inventorySiteTransfersInTotalBasic = $inventorySiteTransfersInTotal - $inventorySiteTransfersInGst;
                                    $inventorySiteTransfersOutTotalBasic = $inventorySiteTransfersOutTotal - $inventorySiteTransfersOutGst;
    
                                    $inventorySiteTransfersTotalBasic = $inventorySiteTransfersInTotalBasic - $inventorySiteTransfersOutTotalBasic;
                                    $inventorySiteTransfersTotalGST = $inventorySiteTransfersInGst - $inventorySiteTransfersOutGst;

                                   

                                    $siteTransferBillTotal = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                        '=','site_transfer_bills.inventory_component_transfer_id')
                                        ->join('inventory_components','inventory_components.id'
                                            ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                        ->whereYear('site_transfer_bills.created_at',$thisYear)
                                        ->sum('site_transfer_bills.total');

                                    $siteTransferBillGst = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                        '=','site_transfer_bills.inventory_component_transfer_id')
                                        ->join('inventory_components','inventory_components.id'
                                            ,'=','inventory_component_transfers.inventory_component_id')
                                        ->where('inventory_components.project_site_id',$projectSiteId)
                                        ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                        ->whereYear('site_transfer_bills.created_at',$thisYear)
                                        ->sum(DB::raw('site_transfer_bills.tax_amount + 
                                        site_transfer_bills.extra_amount_cgst_amount + 
                                        site_transfer_bills.extra_amount_sgst_amount + 
                                        site_transfer_bills.extra_amount_igst_amount'));

                                    $siteTransferBillTotalBasic = $siteTransferBillTotal - $siteTransferBillGst;

                                    $assetMaintenanceBillPaymentTotal = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                        ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                        ->where('asset_maintenance.project_site_id',$projectSiteId)
                                        ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                        ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                        ->sum(DB::raw('asset_maintenance_bills.amount + 
                                                       asset_maintenance_bills.extra_amount'));

                                    $assetMaintenanceGst = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                        ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                        ->where('asset_maintenance.project_site_id',$projectSiteId)
                                        ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                        ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                        ->sum(DB::raw('asset_maintenance_bills.cgst_amount + 
                                                    asset_maintenance_bills.sgst_amount + 
                                                    asset_maintenance_bills.igst_amount'));

                                    $totalAmount = $purchaseOrderBillTotalAmount + $inventorySiteTransfersTotalBasic 
                                                    + $assetMaintenanceBillPaymentTotal + $siteTransferBillTotalBasic
                                                    + $purchaseOrderGst + $inventorySiteTransfersTotalGST
                                                    + $assetMaintenanceGst + $siteTransferBillGst;

                                    if($totalAmount != 0){
                                        $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)
                                                        ->where('month_id',$thisMonth)
                                                        ->where('year_id',$yearId)->first();
                                        if($alreadyExist != null){
                                            $alreadyExist->update([
                                                'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                                'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                                'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                                'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                                'total_expense' => round($totalAmount,3),
                                                'purchase_gst' => round($purchaseOrderGst,3),
                                                'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                                'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                                'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                            ]);
                                        }else{
                                            $purchaseOrderBillMonthlyExpenses->create([
                                                'project_site_id' => $projectSiteId,
                                                'month_id' => $thisMonth,
                                                'year_id' => $yearId,
                                                'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                                'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                                'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                                'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                                'total_expense' => round($totalAmount,3),
                                                'purchase_gst' => round($purchaseOrderGst,3),
                                                'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                                'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                                'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                            ]);
                                        }
                                    }

                                }
                            }

                    }
                    break;

                case ($thisMonth == 'null' && $thisYear == 'null') :
                    $projectSiteIds = $projectSite->pluck('id')->toArray();
                    $todayDate = Carbon::today();
                    $monthId = date('n',strtotime($todayDate));
                    $yearId = $year->where('slug',date('Y',strtotime($todayDate)))->pluck('id')->first();
                    foreach ($projectSiteIds as $projectSiteId){
                        $purchaseOrderBillData = $purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$projectSiteId)
                            ->whereDate('purchase_order_bills.created_at','=',$todayDate)
                              // ->select('purchase_order_bills.amount as basic_amount',
                                    // 'purchase_order_bills.transportation_tax_amount as transportation_tax_amount',
                                    // 'purchase_order_bills.tax_amount as tax_amount',
                                    // 'purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                    // ->get();
                            ->sum(DB::raw('purchase_order_bills.amount +
                                    purchase_order_bills.extra_amount +
                                    purchase_order_bills.transportation_total_amount'));
                        
                        $purchaseOrderBillTotalAmount =  round($purchaseOrderBillData,3);

                        $purchaseOrderGst = round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$projectSiteId)
                            ->whereDate('purchase_order_bills.created_at','=',$todayDate)
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount +
                             purchase_order_bills.tax_amount +
                             purchase_order_bills.extra_tax_amount')),3);

                        $inventorySiteTransfersInTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereDate('inventory_component_transfers.created_at','=',$todayDate)
                            ->sum('inventory_component_transfers.total');

                            $inventorySiteTransfersInGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereDate('inventory_component_transfers.created_at','=',$todayDate)
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + 
                                           inventory_component_transfers.sgst_amount + 
                                           inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersOutTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereDate('inventory_component_transfers.created_at','=',$todayDate)
                            ->sum('inventory_component_transfers.total');

                        $inventorySiteTransfersOutGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereDate('inventory_component_transfers.created_at','=',$todayDate)
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount +
                                         inventory_component_transfers.sgst_amount +
                                         inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersInTotalBasic = $inventorySiteTransfersInTotal - $inventorySiteTransfersInGst;
                        $inventorySiteTransfersOutTotalBasic = $inventorySiteTransfersOutTotal - $inventorySiteTransfersOutGst;

                        $inventorySiteTransfersTotalBasic = $inventorySiteTransfersInTotalBasic - $inventorySiteTransfersOutTotalBasic;
                        $inventorySiteTransfersTotalGST = $inventorySiteTransfersInGst - $inventorySiteTransfersOutGst;

                        $siteTransferBillTotal = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->whereDate('site_transfer_bills.created_at','=',$todayDate)
                            ->sum('site_transfer_bills.total');

                        $siteTransferBillGst = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->whereDate('site_transfer_bills.created_at','=',$todayDate)
                            ->sum(DB::raw('site_transfer_bills.tax_amount + 
                            site_transfer_bills.extra_amount_cgst_amount + 
                            site_transfer_bills.extra_amount_sgst_amount + 
                            site_transfer_bills.extra_amount_igst_amount'));

                        $siteTransferBillTotalBasic = $siteTransferBillTotal - $siteTransferBillGst;

                        $assetMaintenanceBillPaymentTotal = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                            ->join('assets','assets.id','=','asset_maintenance.asset_id')
                            ->where('asset_maintenance.project_site_id',$projectSiteId)
                            ->whereDate('asset_maintenance_bills.created_at','=',$todayDate)
                            ->sum(DB::raw('asset_maintenance_bills.amount + 
                                           asset_maintenance_bills.extra_amount'));

                        $assetMaintenanceGst = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                                ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                ->where('asset_maintenance.project_site_id',$projectSiteId)
                                ->whereDate('asset_maintenance_bills.created_at','=',$todayDate)
                                ->sum(DB::raw('asset_maintenance_bills.cgst_amount + 
                                            asset_maintenance_bills.sgst_amount + 
                                            asset_maintenance_bills.igst_amount'));
   
                        $totalAmount = $purchaseOrderBillTotalAmount + $inventorySiteTransfersTotalBasic 
                                    + $assetMaintenanceBillPaymentTotal + $siteTransferBillTotalBasic
                                    + $purchaseOrderGst + $inventorySiteTransfersTotalGST
                                    + $assetMaintenanceGst + $siteTransferBillGst;

                        if($totalAmount != 0){

                            $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)
                                    ->where('month_id',$monthId)
                                    ->where('year_id',$yearId)->first();
                            if($alreadyExist != null){
                                $alreadyExist->update([
                                    'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                    'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                    'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                    'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                    'total_expense' => round($totalAmount,3),
                                    'purchase_gst' => round($purchaseOrderGst,3),
                                    'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                    'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                    'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                ]);
                            }else{
                                $purchaseOrderBillMonthlyExpenses->create([
                                    'project_site_id' => $projectSiteId,
                                    'month_id' => $thisMonth,
                                    'year_id' => $yearId,
                                    'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                    'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                    'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                    'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                    'total_expense' => round($totalAmount,3),
                                    'purchase_gst' => round($purchaseOrderGst,3),
                                    'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                    'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                    'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                ]);
                            }
                        }

                    }
                    break;

                case ($thisMonth != null && $thisYear != null) :
                        $yearId = $year->where('slug',$thisYear)->pluck('id')->first();
                        $monthId = $month->where('id',$thisMonth)->pluck('id')->first();
                        if($yearId == null){
                            $this->info("Please enter proper year in 4 digit (Eg. 2018)");
                        }elseif($monthId == null){
                            $this->info("Please enter proper month (Eg. 2)");
                        }else{
                            $projectSiteIds = $projectSite->pluck('id')->toArray();
                            foreach ($projectSiteIds as $projectSiteId){
                                $purchaseOrderBillData = $purchaseOrderBill
                                    ->join('purchase_orders','purchase_orders.id','='
                                        ,'purchase_order_bills.purchase_order_id')
                                    ->join('purchase_requests','purchase_requests.id','='
                                        ,'purchase_orders.purchase_request_id')
                                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)
                                    ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                    ->whereYear('purchase_order_bills.created_at',$thisYear)
                                      // ->select('purchase_order_bills.amount as basic_amount',
                                    // 'purchase_order_bills.transportation_tax_amount as transportation_tax_amount',
                                    // 'purchase_order_bills.tax_amount as tax_amount',
                                    // 'purchase_order_bills.extra_tax_amount as extra_tax_amount')
                                    // ->get();
                                    ->sum(DB::raw('purchase_order_bills.amount +
                                    purchase_order_bills.extra_amount +
                                    purchase_order_bills.transportation_total_amount'));
                                $purchaseOrderBillTotalAmount =  round($purchaseOrderBillData,3);

                                $purchaseOrderGst = round($purchaseOrderBill
                                    ->join('purchase_orders','purchase_orders.id','='
                                        ,'purchase_order_bills.purchase_order_id')
                                    ->join('purchase_requests','purchase_requests.id','='
                                        ,'purchase_orders.purchase_request_id')
                                    ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)
                                    ->whereMonth('purchase_order_bills.created_at',$thisMonth)
                                    ->whereYear('purchase_order_bills.created_at',$thisYear)
                                    ->sum(DB::raw('purchase_order_bills.transportation_tax_amount +
                                    purchase_order_bills.tax_amount +
                                    purchase_order_bills.extra_tax_amount')),3);

                                $inventorySiteTransfersInTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum('inventory_component_transfers.total');

                                $inventorySiteTransfersInGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum(DB::raw('inventory_component_transfers.cgst_amount + 
                                                   inventory_component_transfers.sgst_amount + 
                                                   inventory_component_transfers.igst_amount'));

                                $inventorySiteTransfersOutTotal = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum('inventory_component_transfers.total');

                                $inventorySiteTransfersOutGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                                    ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->where('inventory_components.is_material',true)
                                    ->where('inventory_component_transfers.transfer_type_id',
                                        $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                                    ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                                    ->whereMonth('inventory_component_transfers.created_at',$thisMonth)
                                    ->whereYear('inventory_component_transfers.created_at',$thisYear)
                                    ->sum(DB::raw('inventory_component_transfers.cgst_amount +
                                                 inventory_component_transfers.sgst_amount +
                                                 inventory_component_transfers.igst_amount'));
        
                                $inventorySiteTransfersInTotalBasic = $inventorySiteTransfersInTotal - $inventorySiteTransfersInGst;
                                $inventorySiteTransfersOutTotalBasic = $inventorySiteTransfersOutTotal - $inventorySiteTransfersOutGst;

                                $inventorySiteTransfersTotalBasic = $inventorySiteTransfersInTotalBasic - $inventorySiteTransfersOutTotalBasic;
                                $inventorySiteTransfersTotalGST = $inventorySiteTransfersInGst - $inventorySiteTransfersOutGst;

                                $siteTransferBillTotal = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                    '=','site_transfer_bills.inventory_component_transfer_id')
                                    ->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                    ->whereYear('site_transfer_bills.created_at',$thisYear)
                                    ->sum('site_transfer_bills.total');

                                $siteTransferBillGst = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                                    '=','site_transfer_bills.inventory_component_transfer_id')
                                    ->join('inventory_components','inventory_components.id'
                                        ,'=','inventory_component_transfers.inventory_component_id')
                                    ->where('inventory_components.project_site_id',$projectSiteId)
                                    ->whereMonth('site_transfer_bills.created_at',$thisMonth)
                                    ->whereYear('site_transfer_bills.created_at',$thisYear)
                                    ->sum(DB::raw('site_transfer_bills.tax_amount + 
                                    site_transfer_bills.extra_amount_cgst_amount + 
                                    site_transfer_bills.extra_amount_sgst_amount + 
                                    site_transfer_bills.extra_amount_igst_amount'));

                                $siteTransferBillTotalBasic = $siteTransferBillTotal - $siteTransferBillGst;

                                $assetMaintenanceBillPaymentTotal = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                    ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                                    ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                    ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                    ->sum(DB::raw('asset_maintenance_bills.amount + 
                                                   asset_maintenance_bills.extra_amount'));

                                $assetMaintenanceGst = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                                    ->join('assets','assets.id','=','asset_maintenance.asset_id')
                                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                                    ->whereMonth('asset_maintenance_bills.created_at',$thisMonth)
                                    ->whereYear('asset_maintenance_bills.created_at',$thisYear)
                                    ->sum(DB::raw('asset_maintenance_bills.cgst_amount + 
                                                asset_maintenance_bills.sgst_amount + 
                                                asset_maintenance_bills.igst_amount'));
           
                                $totalAmount = $purchaseOrderBillTotalAmount + $inventorySiteTransfersTotalBasic 
                                                + $assetMaintenanceBillPaymentTotal + $siteTransferBillTotalBasic
                                                + $purchaseOrderGst + $inventorySiteTransfersTotalGST
                                                + $assetMaintenanceGst + $siteTransferBillGst;

                                if($totalAmount != 0){
                                    $alreadyExist = $purchaseOrderBillMonthlyExpenses->where('project_site_id',$projectSiteId)
                                                    ->where('month_id',$monthId)
                                                    ->where('year_id',$yearId)
                                                    ->first();
                                    if($alreadyExist != null){
                                        $alreadyExist->update([
                                            'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                            'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                            'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                            'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                            'total_expense' => round($totalAmount,3),
                                            'purchase_gst' => round($purchaseOrderGst,3),
                                            'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                            'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                            'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                        ]);
                                    }else{
                                        $purchaseOrderBillMonthlyExpenses->create([
                                            'project_site_id' => $projectSiteId,
                                            'month_id' => $thisMonth,
                                            'year_id' => $yearId,
                                            'purchase_expense' => round($purchaseOrderBillTotalAmount,3),
                                            'site_transfer_expense' => round($inventorySiteTransfersTotalBasic,3),
                                            'asset_maintenance_expense' => round($assetMaintenanceBillPaymentTotal,3),
                                            'site_transfer_bill_expense' => round($siteTransferBillTotalBasic,3),
                                            'total_expense' => round($totalAmount,3),
                                            'purchase_gst' => round($purchaseOrderGst,3),
                                            'site_transfer_gst' => round($inventorySiteTransfersTotalGST,3),
                                            'asset_maintenance_gst' => round($assetMaintenanceGst,3),
                                            'site_transfer_bill_gst' => round($siteTransferBillGst,3),
                                        ]);
                                    }
                                }

                            }
                        }
                    break;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Purchase Order Bill Monthly Expense Calculations',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
