<?php
/**
     * Created by PhpStorm.
     * User: manoj
     * Date: 5/9/18
     * Time: 6:11 PM
     */

namespace App\Http\Controllers\Report;


use App\AssetMaintenanceBill;
use App\AssetMaintenanceBillPayment;
use App\AssetMaintenanceVendorRelation;
use App\Bill;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillReconcileTransaction;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Helper\MaterialProductHelper;
use App\Http\Controllers\Controller;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Month;
use App\PeticashPurchaseTransactionMonthlyExpense;
use App\PeticashSalaryTransaction;
use App\PeticashSalaryTransactionMonthlyExpense;
use App\PeticashTransactionType;
use App\Product;
use App\ProductDescription;
use App\ProjectSite;
use App\ProjectSiteAdvancePayment;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillMonthlyExpense;
use App\SiteTransferBill;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\Quotation;
use App\QuotationProduct;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\Unit;
use App\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ReportManagementController extends Controller{

    public function __construct()
    {
        $this->middleware('custom.auth');

    }

    public function getView(Request $request) {
        try {
            $projectSite = new ProjectSite();
            $subcontractor = new Subcontractor();
            $subcontractorBillStatus = new SubcontractorBillStatus();
            $quotation = new Quotation();
            $bill = new Bill();
            $billStatus = new BillStatus();
            $month = new Month();
            $year = new Year();
            $startDate = date('d/m/Y',strtotime(Carbon::now()->subDays(30)));
            $endDate = date('d/m/Y',strtotime(Carbon::now()));
            $projectSites = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                            ->orderBy('projects.name','asc')
                            ->where('projects.is_active',true)
                            ->select('project_sites.id','project_sites.name','projects.name as project_name')
                            ->get()->toArray();
            $billIds = $bill->where('bill_status_id',$billStatus->where('slug','approved')->pluck('id')->first())->pluck('id');
            $billProjectSites = $quotation->join('bills','quotations.id','=','bills.quotation_id')
                ->join('project_sites','quotations.project_site_id','=','project_sites.id')
                ->join('projects','projects.id','=','project_sites.project_id')
                ->whereIn('bills.id',$billIds)
                ->distinct('project_sites.id')
                ->orderBy('projects.name','asc')
                ->select('project_sites.id','project_sites.name','project_sites.address','projects.name as project_name')->get()->toArray();
            $approvedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');

            $subcontractorProjectSitesData = $subcontractor->join('subcontractor_structure','subcontractor_structure.subcontractor_id','=','subcontractor.id')
                                ->join('subcontractor_bills','subcontractor_bills.sc_structure_id','=','subcontractor_structure.id')
                                ->join('project_sites','subcontractor_structure.project_site_id','=','project_sites.id')
                                ->join('projects','projects.id','=','project_sites.project_id')
                                ->where('subcontractor_bills.subcontractor_bill_status_id',$approvedBillStatusId)
                                ->distinct('project_sites.id')
                                ->orderBy('projects.name','asc')
                                ->select('project_sites.id','project_sites.name','project_sites.address','projects.name as project_name')->get()->toArray();

            $monthData = $month->all();
            $yearData = $year->all();

            return view('report.report')->with(compact('startDate','endDate','projectSites','billProjectSites','subcontractorProjectSitesData','monthData','yearData'));

        } catch(\Exception $e) {
            $data = [
                'action' => 'Get Report Management View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getButtonDetail(Request $request) {
        try{
            $startDate = explode('/',$request->start_date);
            $start_date = $startDate[2].'-'.$startDate[1].'-'.$startDate[0].' 00:00:00';
            $endDate = explode('/',$request->end_date);
            $end_date = $endDate[2].'-'.$endDate[1].'-'.$endDate[0].' 23:59:59';
            $globalProjectSiteId = $request['project_site_id'];
            $reportLimit = env('REPORT_LIMIT['.$request['report_name'].']');

            $downloadButtonDetails = array();
            $startLimit = 1; $endLimit = $reportLimit;

            switch ($request['report_name']) {
                case 'sitewise_purchase_report' :
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $inventoryComponentTransfer = new InventoryComponentTransfers();
                    $inventoryTransferTypes = new InventoryTransferTypes();
                    $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
                    $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
                    $purchaseCount = $purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$globalProjectSiteId)
                        ->whereBetween('purchase_order_bills.created_at',[$start_date,$end_date])
                        ->orderBy('created_at','desc')
                        ->count();

                    $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
                    $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
                    $inventorySiteTransferCount = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                        ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$globalProjectSiteId)
                        ->whereIn('inventory_component_transfers.transfer_type_id',$inventoryComponentSiteTransferIds->pluck('id'))
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                        ->whereBetween('inventory_component_transfers.created_at',[$start_date,$end_date])
                        ->count();

                    $assetMaintenanceBillCount = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                        ->where('asset_maintenance.project_site_id',$globalProjectSiteId)
                        ->whereBetween('asset_maintenance_bill_payments.created_at',[$start_date,$end_date])
                        ->count();
                    $count = $purchaseCount + $inventorySiteTransferCount + $assetMaintenanceBillCount;
                    $noOfButtons = $count/$reportLimit;
                    for($iterator = 0; $iterator < $noOfButtons; $iterator++){
                        $downloadButtonDetails[$iterator]['start_date'] = $start_date;
                        $downloadButtonDetails[$iterator]['end_date'] = $end_date;
                        $downloadButtonDetails[$iterator]['start_limit'] = $startLimit;
                        $downloadButtonDetails[$iterator]['end_limit'] = $endLimit;
                        $downloadButtonDetails[$iterator]['button_no'] = $iterator;
                        $startLimit = $endLimit + 1;
                        $endLimit = $endLimit + $reportLimit;
                    }
                    break;

                case 'sitewise_salary_report' :
                    $peticashSalaryTransaction = new PeticashSalaryTransaction();
                    $count = $peticashSalaryTransaction
                        ->where('project_site_id',$globalProjectSiteId)
                        ->whereBetween('date',[$start_date,$end_date])
                        ->orderBy('date','asc')
                        ->count();
                    $noOfButtons = $count/$reportLimit;
                    for($iterator = 0; $iterator < $noOfButtons; $iterator++){
                        $totalRecords = $iterator * $reportLimit;
                        $peticashSalaryTransactionDates = $peticashSalaryTransaction
                            ->where('project_site_id',$globalProjectSiteId)
                            ->whereBetween('date',[$start_date,$end_date])
                            ->take($reportLimit)->skip($totalRecords)
                            ->orderBy('date','asc')
                            ->pluck('date');
                        $downloadButtonDetails[$iterator]['start_date'] = $peticashSalaryTransactionDates->last();
                        $downloadButtonDetails[$iterator]['end_date'] = $peticashSalaryTransactionDates->first();
                        $downloadButtonDetails[$iterator]['start_limit'] = $startLimit;
                        $downloadButtonDetails[$iterator]['end_limit'] = $endLimit;
                        $startLimit = $endLimit + 1;
                        $endLimit = $endLimit + $reportLimit;
                    }
                    break;

                case 'sitewise_mis_purchase_report' :
                    $peticashPurchaseTransaction = new PurcahsePeticashTransaction();
                    $count = $peticashPurchaseTransaction
                        ->where('project_site_id',$globalProjectSiteId)
                        ->whereBetween('created_at',[$start_date,$end_date])
                        ->orderBy('created_at','asc')
                        ->count();
                    $noOfButtons = $count/$reportLimit;
                    for($iterator = 0; $iterator < $noOfButtons; $iterator++){
                        $totalRecords = $iterator * $reportLimit;
                        $peticashPurchaseTransactionDates = $peticashPurchaseTransaction
                            ->where('project_site_id',$globalProjectSiteId)
                            ->whereBetween('date',[$start_date,$end_date])
                            ->take($reportLimit)->skip($totalRecords)
                            ->orderBy('date','asc')
                            ->pluck('date');
                        $downloadButtonDetails[$iterator]['start_date'] = $peticashPurchaseTransactionDates->last();
                        $downloadButtonDetails[$iterator]['end_date'] = $peticashPurchaseTransactionDates->first();
                        $downloadButtonDetails[$iterator]['start_limit'] = $startLimit;
                        $downloadButtonDetails[$iterator]['end_limit'] = $endLimit;
                        $startLimit = $endLimit + 1;
                        $endLimit = $endLimit + $reportLimit;
                    }
                    break;

                case 'sitewise_sales_receipt_report' :
                        $iterator = 0;
                        $bill = new Bill();
                        $billStatus = new BillStatus();
                        $approvedBillStatusId = $billStatus->where('slug','approved')->pluck('id')->first();
                        $billCreatedDate = $bill->join('quotations','quotations.id','=','bills.quotation_id')
                                        ->where('quotations.project_site_id',$globalProjectSiteId)
                                        ->where('bills.bill_status_id',$approvedBillStatusId)
                                        ->orderBy('bills.date','asc')
                                        ->pluck('bills.date');
                        if(count($billCreatedDate) > 0){
                            $downloadButtonDetails[$iterator]['start_date'] = $billCreatedDate->last();
                            $downloadButtonDetails[$iterator]['end_date'] = $billCreatedDate->first();

                        }
                    break;

                case 'sitewise_subcontractor_report' :
                    $subcontractorBill = new SubcontractorBill();
                    $subcontractorBillStatus = new SubcontractorBillStatus();
                    $approvedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');
                    $subcontractorStructureIds = $subcontractorBill->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                            ->join('subcontractor_structure_types','subcontractor_structure_types.id','=','subcontractor_structure.sc_structure_type_id')
                            ->join('summaries','summaries.id','=','subcontractor_structure.summary_id')
                            ->where('subcontractor_structure.project_site_id',$globalProjectSiteId)
                            ->where('subcontractor_structure.subcontractor_id',$request['subcontractor_id'])
                            ->where('subcontractor_bills.subcontractor_bill_status_id',$approvedBillStatusId)
                            ->orderBy('subcontractor_structure.id','asc')
                            ->distinct('subcontractor_structure.id')
                            ->select('subcontractor_structure.id','subcontractor_structure_types.name','summaries.name as summary_name','subcontractor_structure.created_at')->get();
                    for($iterator = 0; $iterator < count($subcontractorStructureIds); $iterator++){
                        $downloadButtonDetails[$iterator]['id'] = $subcontractorStructureIds[$iterator]['id'];
                        $downloadButtonDetails[$iterator]['type'] = $subcontractorStructureIds[$iterator]['name'];
                        $downloadButtonDetails[$iterator]['summary_name'] = $subcontractorStructureIds[$iterator]['summary_name'];
                        $downloadButtonDetails[$iterator]['created_at'] = $subcontractorStructureIds[$iterator]['created_at'];
                    }
                    break;

                case 'sitewise_subcontractor_summary_report' :
                        $downloadButtonDetails[0]['show_button'] = true;
                    break;

                case 'sitewise_indirect_expenses_report' :
                    $downloadButtonDetails[0]['start_month_id'] = $request['start_month_id'];
                    $downloadButtonDetails[0]['end_month_id'] = $request['end_month_id'];
                    $downloadButtonDetails[0]['year_id'] = $request['year_id'];
                    break;

                case 'sitewise_pNl_report' :
                    $downloadButtonDetails[0]['start_month_id'] = $request['start_month_id'];
                    $downloadButtonDetails[0]['end_month_id'] = $request['end_month_id'];
                    $downloadButtonDetails[0]['year_id'] = $request['year_id'];
                    break;
            }
            $subcontractorId = $request['subcontractor_id'];
            $reportType = $request['report_name'];
            $project_site_id = $request['project_site_id'];
            return view('report.manage')->with(compact('noOfButtons','reportType','project_site_id','downloadButtonDetails','subcontractorId'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Button Detail Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
                'type' => $request->report_type
            ];
            Log::critical(json_encode($data));
        }
    }

    public function downloadDetailReport(Request $request,$reportType,$project_site_id,$firstParameter,$secondParameter,$thirdParameter) {
        try{
            $year = new Year();
            $month = new Month();
            $currentDate = date('d_m_Y_h_i_s',strtotime(Carbon::now()));

            $row = 0;
            $data = $header = array();

            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');
            $months = $month->get();
            $iterator = 1;
            $monthlyTotal[0]['month'] = 'Month-Year';
            $monthlyTotal[0]['total'] = 'Total';

            switch($reportType) {

                case 'sitewise_purchase_report' :
                    $projectSite = $projectSiteId = new ProjectSite();
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $inventoryComponentTransfer = new InventoryComponentTransfers();
                    $inventoryTransferTypes = new InventoryTransferTypes();
                    $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
                    $siteTransferBill = new SiteTransferBill();
                    $assetMaintenanceBill = new AssetMaintenanceBill();
                    $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
                    $purchaseOrderBillMonthlyExpense = new PurchaseOrderBillMonthlyExpense();
                    $data[$row] = array(
                        'Bill Date', 'Bill Create Date', 'Bill No', 'Paritculars', 'Basic Amount', 'Tax Amount',
                        'Bill Amount', 'Monthly Total'
                    );

                    $date = date('l, d F Y',strtotime($firstParameter)) .' - '. date('l, d F Y',strtotime($secondParameter));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($firstParameter)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($secondParameter)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    $monthlyTotalAmount = 0;
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $purchaseOrderBillMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? round($total,3) : 0;
                            $monthlyTotalAmount += ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
                    $monthlyTotal[$iterator]['make_bold' ] = true;
                    $monthlyTotal[$iterator]['total' ] = 'Total Purchase';
                    $monthlyTotal[$iterator]['amount'] = round($monthlyTotalAmount,3);
                    $colorData[0]['Purchase'] = '#f2f2f2';
                    $colorData[1]['Site Transfer'] = '#efd2d5';
                    $colorData[3]['Site Transfer Bill'] = '#f9d6a2';
                    $colorData[2]['Asset Maintenance'] = '#b2cdff';
                    $reportLimit = env('REPORT_LIMIT['.$reportType.']');
                    $buttonNo = $thirdParameter;

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $purchaseOrderBillsData = $purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$project_site_id)
                        ->where('purchase_order_bills.created_at','>=',$firstParameter)
                        ->where('purchase_order_bills.created_at','<=',$secondParameter)

                        ->select('purchase_order_bills.amount as amount','purchase_order_bills.transportation_tax_amount as transportation_tax_amount'
                            ,'purchase_order_bills.tax_amount as tax_amount','purchase_order_bills.extra_tax_amount as extra_tax_amount','purchase_order_bills.bill_date as bill_date'
                            ,'purchase_order_bills.bill_number as bill_number','purchase_order_bills.created_at as created_at','vendors.company as company')
                        ->orderBy('purchase_order_bills.created_at','desc')
                        ->get()->toArray();


                    $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
                    $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
                    $inventorySiteTransfersData = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                        ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$project_site_id)
                        ->where('inventory_components.is_material',true)
                        ->whereIn('inventory_component_transfers.transfer_type_id',$inventoryComponentSiteTransferIds->pluck('id'))
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                        ->where('inventory_component_transfers.created_at','>=',$firstParameter)
                        ->where('inventory_component_transfers.created_at','<=',$secondParameter)
                        ->select('inventory_component_transfers.id as inventory_component_transfer_id','inventory_component_transfers.created_at as created_at')
                        ->orderBy('inventory_component_transfers.created_at','desc')
                        ->get()->toArray();

                    $siteTransferBillData = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                        '=','site_transfer_bills.inventory_component_transfer_id')
                        ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$project_site_id)
                        ->where('site_transfer_bills.created_at','>=',$firstParameter)
                        ->where('site_transfer_bills.created_at','<=',$secondParameter)
                        ->select('site_transfer_bills.id as site_transfer_bill_id','site_transfer_bills.created_at as created_at')
                        ->orderBy('site_transfer_bills.created_at','desc')
                        ->get()->toArray();

                    $assetMaintenanceBillsData = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                        ->join('assets','assets.id','=','asset_maintenance.asset_id')
                        ->where('asset_maintenance.project_site_id',$project_site_id)
                        ->where('asset_maintenance_bill_payments.created_at','>=',$firstParameter)
                        ->where('asset_maintenance_bill_payments.created_at','<=',$secondParameter)
                        ->select('asset_maintenance_bills.id as asset_maintenance_bill_id'
                            ,'asset_maintenance_bill_payments.amount as total','asset_maintenance_bill_payments.created_at as created_at'
                            ,'assets.name as asset_name','asset_maintenance.id as asset_maintenance_id')
                        ->orderBy('asset_maintenance_bill_payments.created_at','desc')
                        ->get()->toArray();
                    $totalData = array_merge($purchaseOrderBillsData,$inventorySiteTransfersData,$siteTransferBillData,$assetMaintenanceBillsData);
                    usort($totalData, function ($item1, $item2) {
                        return $item2['created_at'] > $item1['created_at'];
                    });
                    $distributedReportData = array_chunk($totalData,($reportLimit));
                    $thisReportData = $distributedReportData[$buttonNo];
                    $row = 1;
                    foreach($thisReportData as $key => $reportData){
                        if(array_key_exists('company',$reportData)){
                            $data[$row]['background'] = '#f2f2f2';
                            $thisMonth = (int)date('n',strtotime($reportData['created_at']));
                            $data[$row]['bill_entry_date'] = date('d-m-Y',strtotime($reportData['bill_date']));
                            $data[$row]['bill_created_date'] = date('d-m-Y',strtotime($reportData['created_at']));
                            $data[$row]['bill_number'] = $reportData['bill_number'];
                            $data[$row]['company_name'] = $reportData['company'];
                            $taxAmount = round(($reportData['transportation_tax_amount'] + $reportData['extra_tax_amount'] + $reportData['tax_amount']),3);
                            $data[$row]['basic_amount'] = round(($reportData['amount'] - $taxAmount),3);
                            $data[$row]['tax_amount'] = $taxAmount;
                            $data[$row]['bill_amount'] = round($data[$row]['basic_amount'],3) + round($data[$row]['tax_amount'],3);
                        }elseif(array_key_exists('inventory_component_transfer_id',$reportData)){
                            $data[$row]['background'] = '#efd2d5';
                            $inventoryComponentTransferData = $inventoryComponentTransfer->where('id',$reportData['inventory_component_transfer_id'])
                                        ->first();
                            $thisMonth = (int)date('n',strtotime($inventoryComponentTransferData['created_at']));
                            $data[$row]['bill_entry_date'] = date('d-m-Y',strtotime($inventoryComponentTransferData['created_at']));
                            $data[$row]['bill_created_date'] = date('d-m-Y',strtotime($inventoryComponentTransferData['created_at']));
                            $data[$row]['bill_number'] = $inventoryComponentTransferData['grn'];
                            $data[$row]['source_name'] = $inventoryComponentTransferData['source_name'].' - '.$inventoryComponentTransferData->transferType->type;
                            $data[$row]['basic_amount'] = $inventoryComponentTransferData['rate_per_unit'] * $inventoryComponentTransferData['quantity'];
                            $data[$row]['tax_amount'] = $inventoryComponentTransferData['cgst_amount'] + $inventoryComponentTransferData['sgst_amount'] + $inventoryComponentTransferData['igst_amount'] ;
                            $total = $data[$row]['basic_amount'] + $data[$row]['tax_amount'];
                            if($inventoryComponentTransferData->transferType->type == 'OUT'){
                                $data[$row]['bill_amount'] = -round($total,3);
                            }else{
                                $data[$row]['bill_amount'] = round($total,3);
                            }
                        }elseif(array_key_exists('site_transfer_bill_id',$reportData)){
                            $data[$row]['background'] = '#f9d6a2';
                            $siteTransferBillData = $siteTransferBill->where('id',$reportData['site_transfer_bill_id'])
                                ->first();
                            $thisMonth = (int)date('n',strtotime($siteTransferBillData['created_at']));
                            $data[$row]['bill_entry_date'] = date('d-m-Y',strtotime($siteTransferBillData['bill_date']));
                            $data[$row]['bill_created_date'] = date('d-m-Y',strtotime($siteTransferBillData['created_at']));
                            $data[$row]['bill_number'] = $siteTransferBillData['bill_number'];
                            $data[$row]['particular'] = $siteTransferBillData->inventoryComponentTransfer->vendor->company;
                            $data[$row]['basic_amount'] = round(($siteTransferBillData['subtotal'] + $siteTransferBillData['extra_amount']),3);
                            $data[$row]['tax_amount'] = round(($siteTransferBillData['tax_amount'] + $siteTransferBillData['extra_amount_cgst_amount'] + $siteTransferBillData['extra_amount_sgst_amount'] + $siteTransferBillData['extra_amount_igst_amount']),3);
                            $data[$row]['bill_amount'] =  $siteTransferBillData['total'];
                        }else{
                            $data[$row]['background'] = '#b2cdff';
                            $assetMaintenanceBillData = $assetMaintenanceBill->where('id',$reportData['asset_maintenance_bill_id'])->first();
                            $vendorCompany = $assetMaintenanceBillData->assetMaintenance->assetMaintenanceVendorRelation->where('is_approved',true)->first()->vendor->company;
                            $thisMonth = (int)date('n',strtotime($reportData['created_at']));
                            $data[$row]['bill_entry_date'] = date('d-m-Y',strtotime($reportData['created_at']));
                            $data[$row]['bill_created_date'] = date('d-m-Y',strtotime($reportData['created_at']));
                            $data[$row]['bill_number'] = $assetMaintenanceBillData['bill_number'];
                            $data[$row]['company_name'] = $reportData['asset_name'].' - '.$vendorCompany;
                            $data[$row]['basic_amount'] = $assetMaintenanceBillData['amount'] + $assetMaintenanceBillData['extra_amount'];
                            $data[$row]['tax_amount'] = $assetMaintenanceBillData['cgst_amount'] + $assetMaintenanceBillData['sgst_amount'] + $assetMaintenanceBillData['igst_amount'];
                            $data[$row]['bill_amount'] = (float)$reportData['total'];
                        }

                        if($row == 1){
                            $newMonth = $thisMonth;
                            $newMonthRow = $row;
                            $data[$row]['monthly_total'] = round($data[$row]['bill_amount'],3);
                            $data[$row]['set_color'] = true;
                        }else{
                            if($newMonth == $thisMonth){
                                $data[$newMonthRow]['monthly_total'] += round($data[$row]['bill_amount'],3);
                                $data[$newMonthRow]['set_color'] = true;
                                $data[$row]['monthly_total'] = null;
                            }else{
                                $newMonth = $thisMonth;
                                $newMonthRow = $row;
                                $data[$row]['set_color'] = true;
                                $data[$row]['monthly_total'] = round($data[$row]['bill_amount'],3);
                            }
                        }
                        $row++;
                    }
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName, $colorData) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName, $colorData) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Purchase Bill Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $colorRow = $row = 10;
                            foreach($colorData as $key => $rowData){
                                $next_column = 'D';
                                $colorRow++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($colorRow)->setRowHeight(20);
                                    $sheet->cell($current_column.($colorRow), function($cell) use($cellData,$colorRow, $key1) {
                                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                            $cell->setAlignment('center')->setValignment('center');
                                            $cell->setBackground($cellData);
                                            $cell->setValue($key1);
                                    });

                                }
                            }
                            $monthHeaderRow =  $row+1;
                            foreach($monthlyTotal as $key => $rowData){
                                $next_column = 'A';
                                if(array_key_exists('make_bold',$rowData)){
                                    $makeBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $makeBold = false;
                                }
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow,$makeBold) {
                                        if($row == $monthHeaderRow || $makeBold){
                                            $cell->setFontWeight('bold');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            $row++; $row++;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('set_color',$rowData)){
                                    $setColor = true;
                                    unset($rowData['set_color']);
                                }else{
                                    $setColor = false;
                                }
                                if(array_key_exists('background',$rowData)){
                                    $backgroundColor = $rowData['background'];
                                    unset($rowData['background']);
                                }else{
                                    $backgroundColor = null;
                                }

                                unset($rowData['background']);
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor,$backgroundColor,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                            $cell->setBackground('#d7f442');
                                        }
                                        if($key1 == 'monthly_total'){
                                            $cell->setFontWeight('bold');
                                        }
                                        if($backgroundColor != null){
                                            $cell->setBackground($backgroundColor);
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        ($key1 === 'basic_amount' ||$key1 === 'tax_amount' || $key1 === 'bill_amount' || ($key1 === 'monthly_total' && $cellData !== null))
                                            ? $cell->setValue(round($cellData,3)) : $cell->setValue($cellData);

                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;

                case 'sitewise_salary_report':
                    $projectSite = $projectSiteId = new ProjectSite();
                    $peticashSalaryTransaction = new PeticashSalaryTransaction();
                    $peticashTransactionType = new PeticashTransactionType();
                    $peticashSalaryTransactionMonthlyExpense = new PeticashSalaryTransactionMonthlyExpense();
                    $data[$row] = array(
                        'Month', 'Employee Id', 'Employee Name', 'Type', 'Amount', 'Paid By', 'Monthly Total'
                    );
                    $date = date('l, d F Y',strtotime($secondParameter)) .' - '. date('l, d F Y',strtotime($firstParameter));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($firstParameter)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($secondParameter)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    $monthlyTotalAmount = 0;
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $peticashSalaryTransactionMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? number_format($total,3) : 0;
                            $monthlyTotalAmount += ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
                    $monthlyTotal[$iterator]['make_bold' ] = true;
                    $monthlyTotal[$iterator]['total' ] = 'Total Purchase';
                    $monthlyTotal[$iterator]['amount'] = round($monthlyTotalAmount,3);

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $peticashSalaryTransactionsData = $peticashSalaryTransaction
                        ->where('project_site_id',$project_site_id)
                        ->where('date','<=',$firstParameter)
                        ->where('date','>=',$secondParameter)
                        ->orderBy('date','desc')
                        ->get();
                    $row = 1;
                    $salaryPeticashTransactionTypeId = $peticashTransactionType->where('slug','salary')->pluck('id')->first();
                    foreach($peticashSalaryTransactionsData as $key => $peticashSalaryTransactionData){
                        $thisMonth = (int)date('n',strtotime($peticashSalaryTransactionData['created_at']));
                        $employeeDetail = $peticashSalaryTransactionData->employee;
                        $data[$row]['month'] = date('M - Y',strtotime($peticashSalaryTransactionData['date']));
                        $data[$row]['employee_id'] = $employeeDetail->employee_id;
                        $data[$row]['employee_name'] = $employeeDetail->name;
                        if($peticashSalaryTransactionData['peticash_transaction_type_id'] == $salaryPeticashTransactionTypeId){
                            $data[$row]['type'] = 'Salary';
                            $data[$row]['amount'] = round($peticashSalaryTransactionData['payable_amount'],3);
                            $data[$row]['paid_by'] = 'Salary';
                        }else{
                            $data[$row]['type'] = 'Advance';
                            $data[$row]['amount'] = round($peticashSalaryTransactionData['amount'],3);
                        }
                        $data[$row]['paid_by'] = ($peticashSalaryTransactionData['bank_id'] != null) ? 'Bank' : 'Cash';
                        if($row == 1){
                            $newMonth = $thisMonth;
                            $newMonthRow = $row;
                            $data[$row]['monthly_total'] = round($data[$row]['amount'],3);
                            $data[$row]['set_color'] = true;
                        }else{
                            if($newMonth == $thisMonth){
                                $data[$newMonthRow]['monthly_total'] += round($data[$row]['amount'],3);
                                $data[$newMonthRow]['set_color'] = true;
                                $data[$row]['monthly_total'] = null;
                            }else{
                                $newMonth = $thisMonth;
                                $newMonthRow = $row;
                                $data[$row]['set_color'] = true;
                                $data[$row]['monthly_total'] = round($data[$row]['amount'],3);
                            }
                        }
                        $row++;
                    }
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Salary Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 10;
                            $monthHeaderRow =  $row+1;
                            foreach($monthlyTotal as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $makeBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $makeBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow,$makeBold) {
                                        if($row == $monthHeaderRow || $makeBold){
                                            $cell->setFontWeight('bold');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            $row++; $row++;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('set_color',$rowData)){
                                    $setColor = true;
                                    unset($rowData['set_color']);
                                }else{
                                    $setColor = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                        }
                                        if($setColor){
                                            $cell->setBackground('#d7f442');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        if($key1 === 'amount' || ($key1 === 'monthly_total' && $cellData !== null)){
                                            $cell->setValue(round($cellData,3));
                                        }else{
                                            $cell->setValue($cellData);

                                        }
                                    });
                                }
                            }
                        });
                    })->export('xls');

                break;

                case 'sitewise_mis_purchase_report':
                    $projectSite = $projectSiteId = new ProjectSite();
                    $peticashPurchaseTransaction = new PurcahsePeticashTransaction();
                    $peticashPurchaseTransactionMonthlyExpense = new PeticashPurchaseTransactionMonthlyExpense();
                    $data[$row] = array(
                        'Bill Date', 'Bill No.', 'Vendor Name', 'Item Name', 'Bill Amount', 'Monthly Total'
                    );
                    $date = date('l, d F Y',strtotime($secondParameter)) .' - '. date('l, d F Y',strtotime($firstParameter));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($firstParameter)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($secondParameter)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    $monthlyTotalAmount = 0;
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $peticashPurchaseTransactionMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? number_format($total,3) : 0;
                            $monthlyTotalAmount += ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
                    $monthlyTotal[$iterator]['make_bold' ] = true;
                    $monthlyTotal[$iterator]['total' ] = 'Total Purchase';
                    $monthlyTotal[$iterator]['amount'] = round($monthlyTotalAmount,3);

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $peticashPurchaseTransactionsData = $peticashPurchaseTransaction
                        ->where('project_site_id',$project_site_id)
                        ->where('date','<=',$firstParameter)
                        ->where('date','>=',$secondParameter)
                        ->orderBy('date','desc')
                        ->get();
                    $row = 1;
                    foreach($peticashPurchaseTransactionsData as $key => $peticashPurchaseTransactionData){
                        $thisMonth = (int)date('n',strtotime($peticashPurchaseTransactionData['created_at']));
                        $data[$row]['date'] = date('n/d/Y',strtotime($peticashPurchaseTransactionData['created_at']));
                        $data[$row]['bill_no'] = $peticashPurchaseTransactionData['bill_number'];
                        $data[$row]['vendor_name'] = ucfirst($peticashPurchaseTransactionData->source_name);
                        $data[$row]['item_name'] = ucfirst($peticashPurchaseTransactionData->name);
                        $data[$row]['bill_amount'] = round($peticashPurchaseTransactionData['bill_amount'],3);
                        if($row == 1){
                            $newMonth = $thisMonth;
                            $newMonthRow = $row;
                            $data[$row]['monthly_total'] = round($data[$row]['bill_amount'],3);
                            $data[$row]['set_color'] = true;
                        }else{
                            if($newMonth == $thisMonth){
                                $data[$newMonthRow]['monthly_total'] += round($data[$row]['bill_amount'],3);
                                $data[$newMonthRow]['set_color'] = true;
                                $data[$row]['monthly_total'] = null;
                            }else{
                                $newMonth = $thisMonth;
                                $newMonthRow = $row;
                                $data[$row]['set_color'] = true;
                                $data[$row]['monthly_total'] = round($data[$row]['bill_amount'],3);
                            }
                        }
                        $row++;
                    }
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Misc. Purchase Bill Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 10;
                            $monthHeaderRow =  $row+1;
                            foreach($monthlyTotal as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $makeBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $makeBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow,$makeBold) {
                                        if($row == $monthHeaderRow || $makeBold){
                                            $cell->setFontWeight('bold');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            $row++; $row++;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('set_color',$rowData)){
                                    $setColor = true;
                                    unset($rowData['set_color']);
                                }else{
                                    $setColor = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;

                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                        }
                                        if($setColor){
                                            $cell->setBackground('#d7f442');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        if($key1 === 'bill_amount' || ($key1 === 'monthly_total' && $cellData !== null)){
                                            $cell->setValue(number_format($cellData,3));
                                        }else{
                                            $cell->setValue($cellData);
                                        }

                                    });
                                }
                            }
                        });
                    })->export('xls');

                    break;

                case 'sitewise_sales_receipt_report':
                    $projectSite = new ProjectSite();
                    $quotation = new Quotation();
                    $bill = new Bill();
                    $billStatus = new BillStatus();
                    $billTransaction = new BillTransaction();
                    $data[$row] = array(
                        ' Bill Date : (Created Date)', 'Bill No.', 'Basic Amount', 'GST', 'With Tax Amount', 'Transaction Amount', 'Mobilization', 'TDS', 'Retention',
                        'Hold', 'Debit', 'Other Recovery', 'Payable', 'Receipt', 'Total Paid', 'Remaining', 'Monthly Total'
                    );

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $quotationId = $quotation->where('project_site_id',$project_site_id)->pluck('id')->first();
                    $statusId = $billStatus->whereIn('slug',['approved','draft'])->get();
                    $totalBillData = $bill->where('quotation_id',$quotationId)
                                    ->whereIn('bill_status_id',array_column($statusId->toArray(),'id'))->orderBy('id')
                                    ->select('id','bill_status_id')->get();
                    $date = date('l, d F Y',strtotime($secondParameter)) .' - '. date('l, d F Y',strtotime($firstParameter));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($firstParameter)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($secondParameter)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    $monthlyTotalAmount = 0;
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $billIds = $bill->where('quotation_id',$quotationId)
                                ->whereIn('bill_status_id',array_column($statusId->toArray(),'id'))->orderBy('id')
                                ->whereMonth('date',$month['id'])
                                ->whereYear('date',$thisYear['slug'])
                                ->pluck('id');
                            $total = $billTransaction->whereIn('bill_id',$billIds)
                                ->sum('total');
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? round($total,3) : 0;
                            $monthlyTotalAmount += ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
                    $monthlyTotal[$iterator]['make_bold'] = true;
                    $monthlyTotal[$iterator]['total'] = 'Total';
                    $monthlyTotal[$iterator]['amount'] = round($monthlyTotalAmount,3);
                    $billNo = 1;
                    $row = 1;
                    $totalBasicAmount = $totalGst = $totalWithTaxAmount = $totalTransactionAmount = $totalMobilization = $totalTds =
                    $totalRetention = $totalHold = $totalDebit = $totalOtherRecovery = $totalPayable = $totalReceipt = $totalPaid = $totalRemaining = 0;
                    foreach ($totalBillData as $thisBill){
                        $billName = "R.A. ".$billNo;
                        if($thisBill['bill_status_id'] == $statusId->where('slug','approved')->pluck('id')->first()){
                            $billData = $this->getBillData($thisBill['id']);
                            $thisMonth = (int)date('n',strtotime($billData['date']));
                            $billRow = $row;
                                $data[$row]['make_bold'] = true;
                                $data[$row]['date'] = date('d/n/Y',strtotime($billData['date'])) .' : ('. date('d/n/Y',strtotime($billData['created_at'])) .')';
                                $data[$row]['bill_no'] = $billName;
                                $data[$row]['basic_amount'] = round($billData['basic_amount'], 3);
                                $data[$row]['gst'] = round($billData['tax_amount'], 3);
                                $data[$row]['total_amount'] = round($billData['total_amount_with_tax'], 3);
                                $data[$row] = array_merge($data[$row],array_fill(5,7,null));
                                $data[$row]['payable'] = $billData['total_amount_with_tax'];
                                $data[$row]['receipt'] = null;
                                $data[$row]['total_paid'] = 0;
                                $totalBasicAmount += $billData['basic_amount']; $totalGst += $billData['tax_amount'];
                                $totalWithTaxAmount += $billData['total_amount_with_tax']; $totalReceipt += $data[$row]['total_paid'];
                                $billTransactionData = $billTransaction->where('bill_id',$thisBill['id'])->orderBy('created_at','asc')->get();
                                if($row == 1){
                                    $newMonth = $thisMonth;
                                    $newMonthRow = $row;
                                }else{
                                    if($newMonth == $thisMonth){
                                        $setMonthlyTotalData = false;
                                    }else{
                                        $newMonth = $thisMonth;
                                        $newMonthRow = $row;
                                        $setMonthlyTotalData = true;
                                    }
                                }
                                $row++;

                                $receiptCount = 1;
                                foreach($billTransactionData as $key => $billTransaction){
                                    $data[$row]['date'] = null;
                                    $data[$row]['bill_no'] = 'Receipt '.$receiptCount;
                                    $data[$row] = array_merge($data[$row],array_fill(2,3,null));
                                    if($billTransaction['paid_from_advanced'] == true){
                                        $data[$row]['transaction_amount'] = 0;
                                        $data[$row]['mobilisation'] = round($billTransaction['amount'], 3);
                                        $totalMobilization += $billTransaction['amount'];
                                    }else{
                                        $data[$row]['transaction_amount'] = round($billTransaction['amount'], 3);
                                        $data[$row]['mobilisation'] = 0;
                                        $totalTransactionAmount += $billTransaction['amount'];
                                    }
                                    $data[$row]['tds'] = round($billTransaction['tds_amount'], 3);
                                    $data[$row]['retention'] = round($billTransaction['retention_amount'], 3);
                                    $data[$row]['hold'] = round($billTransaction['hold'], 3);
                                    $data[$row]['debit'] = round($billTransaction['debit'], 3);
                                    $data[$row]['other_recovery'] = round($billTransaction['other_recovery_value'], 3);
                                    $data[$row]['payable_amount'] = null;
                                    $receipt = $billTransaction['total'];
                                    $data[$row]['receipt'] = round($receipt, 3);
                                    $data[$row] = array_merge($data[$row],array_fill(14,3,null));
                                    $data[$billRow]['total_paid'] += $receipt;
                                    $row++;$receiptCount++;
                                    $totalTds += $billTransaction['tds_amount']; $totalRetention += $billTransaction['retention_amount'];
                                    $totalHold += $billTransaction['hold']; $totalDebit += $billTransaction['debit'];
                                    $totalOtherRecovery += $billTransaction['other_recovery_value'];
                                    $totalReceipt += $receipt;
                                }
                                $data[$row] = array_fill(0,17,null);
                                $row++;
                                $paidAmount = $data[$billRow ]['total_paid'];
                                $data[$billRow]['remaining'] = $data[$billRow]['payable'] - $data[$billRow ]['total_paid'];
                                $totalPaid += $data[$billRow]['total_paid'];
                                $totalPayable += $data[$billRow]['payable'];
                                $totalRemaining += $data[$billRow]['remaining'];
                                $data[$billRow]['remaining'] = round($data[$billRow]['remaining'], 3);
                                $data[$billRow]['payable'] = round($data[$billRow]['payable'], 3);
                                $data[$billRow]['total_paid'] = round($data[$billRow]['total_paid'], 3);
                                if($billRow == 1 || $setMonthlyTotalData){
                                    $data[$billRow]['monthly_total'] = $paidAmount;
                                }elseif($setMonthlyTotalData == false){
                                    $data[$newMonthRow]['monthly_total'] += $paidAmount;
                                    $data[$billRow]['monthly_total'] = null;
                                }
                            }
                            $billNo++;

                        }
                    $data[$row]['make_bold'] = true;
                    $totalRow = array(
                        'Total', null, round($totalBasicAmount,3), round($totalGst,3), round($totalWithTaxAmount,3), round($totalTransactionAmount,3)
                            , number_format($totalMobilization,3), round($totalTds,3), round($totalRetention,3),round($totalHold,3),
                        round($totalDebit,3),round($totalOtherRecovery,3), round($totalPayable,3), round($totalReceipt,3),
                        round($totalPaid,3), round($totalRemaining,3), null
                    );
                    $data[$row] = array_merge($data[$row],$totalRow);
                    $projectSiteAdvancePayment = new ProjectSiteAdvancePayment();
                    $mobilizeAdvance = $projectSiteAdvancePayment->where('project_site_id',$project_site_id)->sum('amount');
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName, $mobilizeAdvance) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName, $mobilizeAdvance) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Sales/Receipt Bill Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 10;
                            $monthHeaderRow =  $row+1;
                            foreach($monthlyTotal as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow,$setBold) {
                                        if($row == $monthHeaderRow || $setBold){
                                            $cell->setFontWeight('bold');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            $row++; $row++;

                            $sheet->mergeCells('A'.$row.':'.'B'.$row);
                            $sheet->cell('A'.$row, function($cell) use($sheet,$row) {
                                $sheet->getRowDimension($row)->setRowHeight(20);
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Total Mobilization Given (Advance)')->setFontWeight('bold');
                            });
                            $sheet->cell('C'.$row, function($cell) use($sheet,$row, $mobilizeAdvance) {
                                $sheet->getRowDimension($row)->setRowHeight(20);
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue(round($mobilizeAdvance,3))->setFontWeight('bold');
                            });
                            $row++;
                            $headerRow = $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setBold,$current_column,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        ($row == $headerRow || $setBold) ? $cell->setFontWeight('bold') : null;
                                        ($current_column == 'N') ? $cell->setBackground('#d7f442') : null;
                                        ($current_column == 'P') ? $cell->setFontColor('#d82517') : null;
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        if($key1 === 'monthly_total' && $cellData !== null){
                                            $cell->setValue(round($cellData,3));
                                        }else{
                                            $cell->setValue($cellData);

                                        }
                                    });
                                }
                            }

                        });
                    })->export('xls');
                    break;

                case 'sitewise_subcontractor_report':
                    $projectSite = new ProjectSite();
                    $subcontractorStructure = new SubcontractorStructure();
                    $subcontractorBillTransaction = new SubcontractorBillTransaction();
                    $subcontractorBill = new SubcontractorBill();
                    $data[$row] = array(
                        ' Bill Created Date)', 'Bill No.', 'Basic Amount', 'GST', 'With Tax Amount', 'Transaction Amount', 'TDS', 'Retention',
                        'Hold', 'Debit', 'Other Recovery', 'Payable', 'Receipt', 'Total Paid', 'Remaining', 'Monthly Total'
                    );
                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();

                    $subcontractorStructureId = $firstParameter;
                    $subcontractorBillStatus = new SubcontractorBillStatus();
                    $statusId = $subcontractorBillStatus->whereIn('slug',['approved','draft'])->get();
                    $subcontractorBillData = $subcontractorBill->where('sc_structure_id',$subcontractorStructureId)
                        ->whereIn('subcontractor_bill_status_id',array_column($statusId->toArray(),'id'))//->orderBy('id')
                        ->get();
                    $startDate = $subcontractorBillData->pluck('created_at')->first();
                    $endDate = $subcontractorBillData->pluck('created_at')->last();
                    $date = date('l, d F Y',strtotime($startDate)) .' - '. date('l, d F Y',strtotime($endDate));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($startDate)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($endDate)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorStructureId)->first();
                    $subcontractorCompanyName = $subcontractorStructureData->subcontractor->company_name;
                    if($subcontractorStructureData->contractType->slug == 'sqft'){
                        $rate = $subcontractorStructureData['rate'];
                    }else{
                        $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                    }

                    $monthlyTotalAmount = 0;
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $billIds = $subcontractorBill->where('sc_structure_id',$subcontractorStructureId)
                                ->whereIn('subcontractor_bill_status_id',array_column($statusId->toArray(),'id'))
                                ->whereMonth('created_at',$month['id'])
                                ->whereYear('created_at',$thisYear['slug'])
                                ->pluck('id');
                            $total = 0;
                            foreach ($billIds as $subcontractorBillId){
                                $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)
                                    ->first();
                                $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                                $subTotal = $subcontractorBillData['qty'] * $rate;
                                $taxTotal = 0;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                                }
                                $total += $subTotal + $taxTotal;
                            }
                            $monthlyTotal[$iterator]['total'] = round($total,3);
                            $monthlyTotalAmount += $total;
                            $iterator++;
                        }
                    }
                    $monthlyTotal[$iterator]['make_bold' ] = true;
                    $monthlyTotal[$iterator]['total' ] = 'Total Purchase';
                    $monthlyTotal[$iterator]['amount'] = round($monthlyTotalAmount,3);

                    $billNo = 1;
                    $row = 1;
                    $totalBasicAmount = $totalGst = $totalWithTaxAmount = $totalTransactionAmount = $totalTds =
                    $totalRetention = $totalHold = $totalDebit = $totalOtherRecovery = $totalPayable = $totalReceipt = $totalPaid = $totalRemaining = 0;
                    $subcontractorBillData = $subcontractorBill->where('sc_structure_id',$subcontractorStructureId)
                        ->whereIn('subcontractor_bill_status_id',array_column($statusId->toArray(),'id'))//->orderBy('id')
                        ->get();
                    foreach ($subcontractorBillData as $subcontractorBill){
                        $billName = "R.A. ".$billNo;
                        if($subcontractorBill['subcontractor_bill_status_id'] == $statusId->where('slug','approved')->pluck('id')->first()){
                            $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                            $subTotal = $subcontractorBill['qty'] * $rate;
                            $taxTotal = 0;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                            }
                            $finalTotal = $subTotal + $taxTotal;
                            $thisMonth = (int)date('n',strtotime($subcontractorBill['created_at']));
                            $billRow = $row;
                            $data[$row]['make_bold'] = true;
                            $data[$row]['date'] = date('d/n/Y',strtotime($subcontractorBill['created_at']));
                            $data[$row]['bill_no'] = $billName;
                            $data[$row]['basic_amount'] = round($subTotal, 3);
                            $data[$row]['gst'] = round($taxTotal, 3);
                            $data[$row]['total_amount'] = $finalTotal;
                            $data[$row] = array_merge($data[$row],array_fill(5,6,null));
                            $data[$row]['payable'] = $finalTotal;
                            $data[$row]['receipt'] = null;
                            $data[$row]['total_paid'] = 0;
                            $totalBasicAmount += $rate; $totalGst += $taxTotal;
                            $totalWithTaxAmount += $finalTotal; $totalReceipt += $data[$row]['total_paid'];
                            $billTransactionData = $subcontractorBillTransaction->where('subcontractor_bills_id',$subcontractorBill['id'])->orderBy('created_at','asc')->get();
                            if($row == 1){
                                $newMonth = $thisMonth;
                                $newMonthRow = $row;
                            }else{
                                if($newMonth == $thisMonth){
                                    $setMonthlyTotalData = false;
                                }else{
                                    $newMonth = $thisMonth;
                                    $newMonthRow = $row;
                                    $setMonthlyTotalData = true;
                                }
                            }
                            $row++;
                            $receiptCount = 1;
                            foreach($billTransactionData as $key => $billTransaction){
                                $data[$row]['date'] = null;
                                $data[$row]['bill_no'] = 'Receipt '.$receiptCount;
                                $data[$row] = array_merge($data[$row],array_fill(2,3,null));
                                $receipt = $billTransaction['total'];
                                $data[$row]['receipt'] = round($receipt, 3);
                                $totalTransactionAmount += $billTransaction['subtotal'];
                                $data[$row]['tds'] = round($billTransaction['tds_amount'], 3);
                                $data[$row]['retention'] = round($billTransaction['retention_amount'], 3);
                                $data[$row]['hold'] = round($billTransaction['hold'], 3);
                                $data[$row]['debit'] = round($billTransaction['debit'], 3);
                                $data[$row]['other_recovery'] = round($billTransaction['other_recovery'], 3);
                                $data[$row]['payable_amount'] = null;
                                $data[$row]['transaction_amount'] = round($billTransaction['subtotal'], 3);
                                $data[$row] = array_merge($data[$row],array_fill(14,3,null));
                                $data[$billRow]['total_paid'] += $receipt;
                                $row++;$receiptCount++;
                                $totalTds += $billTransaction['tds_amount']; $totalRetention += $billTransaction['retention_amount'];
                                $totalHold += $billTransaction['hold']; $totalDebit += $billTransaction['debit'];
                                $totalOtherRecovery += $billTransaction['other_recovery'];
                                $totalReceipt += $receipt;
                            }
                            $data[$row] = array_fill(0,16,null);
                            $row++;
                            $totalWithTax = $data[$billRow ]['total_amount'];
                            $data[$billRow]['remaining'] = $data[$billRow]['payable'] - $data[$billRow ]['total_paid'];
                            $totalPaid += $data[$billRow]['total_paid'];
                            $totalPayable += $data[$billRow]['payable'];
                            $totalRemaining += $data[$billRow]['remaining'];
                            $data[$billRow]['remaining'] = round($data[$billRow]['remaining'], 3);
                            $data[$billRow]['payable'] = round($data[$billRow]['payable'], 3);
                            $data[$billRow]['total_paid'] = round($data[$billRow]['total_paid'], 3);
                            if($billRow == 1 || $setMonthlyTotalData){
                                $data[$billRow]['monthly_total'] = $totalWithTax;
                            }elseif($setMonthlyTotalData == false){
                                $data[$newMonthRow]['monthly_total'] += $totalWithTax;
                                $data[$billRow]['monthly_total'] = null;
                            }
                        }
                        $billNo++;

                    }
                    $data[$row]['make_bold'] = true;
                    $totalRow = array(
                        'Total', null, round($totalBasicAmount,3), round($totalGst,3), round($totalWithTaxAmount,3), round($totalReceipt,3)
                        , round($totalTds,3), round($totalRetention,3),round($totalHold,3),
                        round($totalDebit,3),round($totalOtherRecovery,3), round($totalPayable,3), round($totalTransactionAmount,3),
                        round($totalPaid,3), round($totalRemaining,3), null
                    );
                    $data[$row] = array_merge($data[$row],$totalRow);
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName, $subcontractorCompanyName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName, $subcontractorCompanyName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName,$subcontractorCompanyName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Subcontractor  '.$subcontractorCompanyName .' - '. $projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 10;
                            $monthHeaderRow =  $row+1;
                            foreach($monthlyTotal as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow,$setBold) {
                                        if($row == $monthHeaderRow || $setBold){
                                            $cell->setFontWeight('bold');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            $row++; $row++;
                            $headerRow = $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setBold,$current_column) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        ($row == $headerRow || $setBold) ? $cell->setFontWeight('bold') : null;
                                        ($current_column == 'M') ? $cell->setBackground('#d7f442') : null;
                                        ($current_column == 'O') ? $cell->setFontColor('#d82517') : null;
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

                                    });
                                }
                            }

                        });
                    })->export('xls');
                    break;

                case 'sitewise_subcontractor_summary_report' :
                    $projectSite = new ProjectSite();
                    $subcontractorStructure = new SubcontractorStructure();
                    $subcontractorBill = new SubcontractorBill();
                    $subcontractorBillStatus = new SubcontractorBillStatus();
                    $subcontractorBillTransaction = new SubcontractorBillTransaction();
                    $approvedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');
                    $subcontractorData = $subcontractorBill->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                                            ->join('subcontractor','subcontractor.id','=','subcontractor_structure.subcontractor_id')
                                            ->where('subcontractor_bills.subcontractor_bill_status_id',$approvedBillStatusId)
                                            ->where('subcontractor_structure.project_site_id',$project_site_id)
                                            ->distinct('subcontractor_structure.subcontractor_id')
                                            ->orderBy('subcontractor.subcontractor_name','asc')
                                            ->select('subcontractor.id','subcontractor.subcontractor_name')->get();
                    $data[$row] = array(
                        ' Subcontractor Name', 'Basic Amount', 'Tax', 'With Tax Amount', 'Transaction Amount', 'TDS', 'Retention',
                        'Hold', 'Debit', 'Other Recovery', 'Payable', 'Receipt', 'Balance Amount'
                    );
                    $row = 1;
                    $statusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');
                    $totalBasicAmount = $totalGst = $totalAmount = $totalTransactionAmount = $totalTds = $totalRetention = $totalHold = 0;
                    $totalDebit = $totalOtherRecovery = $totalReceipt = $totalBalanceRemaining = 0;
                    foreach($subcontractorData as $subcontractor) {
                        $data[$row]['subcontractor_name'] = $subcontractor['subcontractor_name'];
                        $basic_amount = $gst = $finalAmount = $transaction_amount = $tds = $retention = $hold = 0;
                        $debit = $other_recovery = $receipt = $balanceRemaining = 0;

                        $subcontractorStructureData = $subcontractorStructure->where('subcontractor_id', $subcontractor['id'])
                                                        ->where('project_site_id',$project_site_id)
                                                            ->get();
                        foreach ($subcontractorStructureData as $subcontractorStructure) {
                            if ($subcontractorStructure->contractType->slug == 'sqft') {
                                $rate = round($subcontractorStructure['rate'],3);
                            } else {
                                $rate = round(($subcontractorStructure['rate'] * $subcontractorStructure['total_work_area']),3);
                            }
                            $subcontractorBillData = $subcontractorBill->where('sc_structure_id', $subcontractorStructure['id'])
                                ->where('subcontractor_bill_status_id', $statusId)
                                ->get();
                            foreach ($subcontractorBillData as $subcontractorBill) {
                                $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                                $subTotal = round(($subcontractorBill['qty'] * $rate),3);
                                $taxTotal = 0;
                                foreach ($subcontractorBillTaxes as $key => $subcontractorBillTaxData) {
                                    $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                                }
                                $basic_amount += $subTotal;
                                $gst += round($taxTotal,3);
                                $finalAmount += round(($subTotal + $taxTotal),3);
                                $billTransactionData = $subcontractorBillTransaction->where('subcontractor_bills_id', $subcontractorBill['id'])->orderBy('created_at', 'asc')->get();
                                foreach ($billTransactionData as $key => $billTransaction) {
                                    $transaction_amount += $billTransaction['subtotal'];
                                    $tds += $billTransaction['tds_amount'];
                                    $retention += $billTransaction['retention_amount'];
                                    $hold += $billTransaction['hold'];
                                    $debit += $billTransaction['debit'];
                                    $other_recovery += $billTransaction['other_recovery'];
                                    $receipt += $billTransaction['total'];
                                }
                            }
                        }
                        $data[$row]['basic_amount'] = round($basic_amount,3);
                        $data[$row]['gst'] = round($gst,3);
                        $data[$row]['total_amount'] = round($finalAmount,3);
                        $data[$row]['transaction_amount'] = round($receipt,3);
                        $data[$row]['tds'] = round($tds,3);
                        $data[$row]['retention'] = round($retention,3);
                        $data[$row]['hold'] = round($hold,3);
                        $data[$row]['debit'] = round($debit,3);
                        $data[$row]['other_recovery'] = round($other_recovery,3);
                        $data[$row]['payable'] = round($finalAmount,3);
                        $data[$row]['receipt'] = round($transaction_amount,3);
                        $data[$row]['balance_remaining'] = round($finalAmount - $receipt,3);
                        $totalBasicAmount += $basic_amount; $totalGst += $gst; $totalAmount += $finalAmount;
                        $totalTransactionAmount += $transaction_amount; $totalTds += $tds; $totalRetention += $retention;
                        $totalHold += $hold; $totalDebit += $debit; $totalOtherRecovery += $other_recovery;
                        $totalReceipt += $receipt; $totalBalanceRemaining += ($finalAmount - $receipt);
                        $row++;
                    }
                    $data[$row]['make_bold'] = true;
                    $totalRow = array(
                        'Total', round($totalBasicAmount,3), round($totalGst,3), round($totalAmount,3),
                        round($totalReceipt,3), round($totalTds,3),
                        round($totalRetention,3), round($totalHold,3), round($totalDebit,3), round($totalOtherRecovery,3),
                        round($totalAmount,3), round($totalTransactionAmount,3), round($totalBalanceRemaining,3)
                    );
                    $data[$row] = array_merge($data[$row],$totalRow);
                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $date = date('l, d F Y',strtotime(Carbon::now()));
                    $reportType = 'sitewise_subcontractor_summary';
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Subcontractor Summary Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 10;
                            $row++; $row++;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setBold) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        ($row == $headerRow || $setBold) ? $cell->setFontWeight('bold') : null;
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;

                case 'sitewise_indirect_expenses_report' :
                    $projectSite = new ProjectSite();
                    $quotation = new Quotation();
                    $bill = new Bill();
                    $billStatus = new BillStatus();
                    $subcontractorStructure = new SubcontractorStructure();
                    $subcontractorBill = new SubcontractorBill();
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
                    $inventoryComponentTransfer = new InventoryComponentTransfers();
                    $inventoryTransferTypes = new InventoryTransferTypes();
                    $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
                    $siteTransferBill = new SiteTransferBill();
                    $data[$row] = array(
                        'Month - Year', 'Sales GST', 'Subcontractor GST', 'Purchase GST', 'GST'
                    );
                    $startMonth = $month->where('id',$firstParameter)->first();
                    $endMonth = $month->where('id',$secondParameter)->first();
                    $selectedYear = $year->where('id',$thirdParameter)->first();
                    $date = $startMonth['name'].' '.$selectedYear['slug'].' - '.$endMonth['name'].' '.$selectedYear['slug'];
                    $totalMonths = $month->whereBetween('id',[$firstParameter,$secondParameter])->select('id','name','slug')->get();
                    $row = 1;
                    $yearlyGst = 0;
                    $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
                    $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
                    $statusId = $billStatus->where('slug','approved')->pluck('id');
                    foreach ($totalMonths as $month){
                        $data[$row]['month'] = $month['name'].'-'.$selectedYear['slug'];
                        $data[$row]['gst'] = $data[$row]['purchase_gst'] = $data[$row]['subcontractor_gst'] = $data[$row]['sales_gst'] =
                        $salesGst = $subcontractorGst = 0;
                        $quotationId = $quotation->where('project_site_id',$project_site_id)->pluck('id')->first();
                        $billIds = $bill->where('quotation_id',$quotationId)
                            ->where('bill_status_id',$statusId)->orderBy('id')
                            ->whereMonth('date',$month['id'])
                            ->whereYear('date',$selectedYear['slug'])
                            ->pluck('id');
                        if(count($billIds) > 0){
                            foreach ($billIds as $billId){
                                $billData = $this->getBillData($billId);
                                $salesGst +=  round($billData['tax_amount'],3);
                            }
                        }
                        $data[$row]['sales_gst'] = round($salesGst,3);

                        $subcontractorBillStatus = new SubcontractorBillStatus();
                        $approvedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');
                        $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                            ->where('subcontractor_structure.project_site_id',$project_site_id)
                            ->where('subcontractor_bills.subcontractor_bill_status_id',$approvedBillStatusId)
                            ->whereMonth('subcontractor_bills.created_at',$month['id'])
                            ->whereYear('subcontractor_bills.created_at',$selectedYear['slug'])
                            ->pluck('subcontractor_bills.id');
                        if(count($subcontractorBillIds) > 0){
                            foreach ($subcontractorBillIds as $subcontractorBillId){
                                $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                                $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                                if($subcontractorStructureData->contractType->slug == 'sqft'){
                                    $rate = $subcontractorStructureData['rate'];
                                }else{
                                    $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                                }
                                $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                                $subTotal = $subcontractorBillData['qty'] * $rate;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $subcontractorGst += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                            }
                        }
                        $data[$row]['subcontractor_gst'] = round($subcontractorGst,3);

                        $purchaseOrderGst = round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$project_site_id)
                            ->whereMonth('purchase_order_bills.created_at',$month['id'])
                            ->whereYear('purchase_order_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);
                        $assetMaintenanceGst = $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                            ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                            ->join('assets','assets.id','=','asset_maintenance.asset_id')
                            ->where('asset_maintenance.project_site_id',$project_site_id)
                            ->whereMonth('asset_maintenance_bill_payments.created_at',$month['id'])
                            ->whereYear('asset_maintenance_bill_payments.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('asset_maintenance_bills.cgst_amount +asset_maintenance_bills.sgst_amount +asset_maintenance_bills.igst_amount'));

                        $inventorySiteTransfersInGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersOutGst = $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $siteTransferBillGst = $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->whereMonth('site_transfer_bills.created_at',$month['id'])
                            ->whereYear('site_transfer_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('site_transfer_bills.tax_amount + site_transfer_bills.extra_amount_cgst_amount + site_transfer_bills.extra_amount_sgst_amount + site_transfer_bills.extra_amount_igst_amount'));

                        $purchaseGst = $purchaseOrderGst + $assetMaintenanceGst + $inventorySiteTransfersInGst + $siteTransferBillGst - $inventorySiteTransfersOutGst;
                        $data[$row]['purchase_gst'] = round($purchaseGst,3);
                        $totalMonthGst = $salesGst - $purchaseGst - $subcontractorGst;
                        $data[$row]['gst'] = round(($totalMonthGst),3);
                        $yearlyGst += $totalMonthGst;
                        $row++;
                    }
                    $data[$row]['make_bold'] = true;
                    $data[$row] = array_merge($data[$row],array('Total',null,null,null,round($yearlyGst,3)));
                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $reportType = 'Indirect Expenses';
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Indirect Expenses Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 9;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                if(array_key_exists('make_bold',$rowData)){
                                    $setBold = true;
                                    unset($rowData['make_bold']);
                                }else{
                                    $setBold = false;
                                }
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setBold,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        ($row == $headerRow || $setBold) ? $cell->setFontWeight('bold') : null;
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;

                case 'sitewise_pNl_report' :
                    $projectSite = new ProjectSite();
                    $quotation = new Quotation();
                    $bill = new Bill();
                    $billStatus = new BillStatus();
                    $billTransaction = new BillTransaction();
                    $billReconcileTransaction = new BillReconcileTransaction();
                    $subcontractorStructure = new SubcontractorStructure();
                    $subcontractorBill = new SubcontractorBill();
                    $subcontractorBillStatus = new SubcontractorBillStatus();
                    $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $inventoryComponentTransfer = new InventoryComponentTransfers();
                    $inventoryTransferTypes = new InventoryTransferTypes();
                    $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
                    $siteTransferBill = new SiteTransferBill();
                    $purchaseOrderBillMonthlyExpense = new PurchaseOrderBillMonthlyExpense();
                    $peticashSalaryTransactionMonthlyExpense = new PeticashSalaryTransactionMonthlyExpense();
                    $peticashPurchaseTransactionMonthlyExpense = new PeticashPurchaseTransactionMonthlyExpense();
                    $quotation = $quotation->where('project_site_id',$project_site_id)->first();
                    $approvedBillStatusId = $billStatus->where('slug','approved')->pluck('id')->first();
                    $subcontractorApprovedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id')->first();
                    $startMonth = $month->where('id',$firstParameter)->first();
                    $endMonth = $month->where('id',$secondParameter)->first();
                    $selectedYear = $year->where('id',$thirdParameter)->first();
                    $date = $startMonth['name'].' '.$selectedYear['slug'].' - '.$endMonth['name'].' '.$selectedYear['slug'];
                    $totalMonths = $month->whereBetween('id',[$firstParameter,$secondParameter])->select('id','name','slug')->get();
                    $sales = $receipt = $total = $totalRetention = $totalHold = $debitAmount = $tdsAmount = $subcontractorTotal =
                    $otherRecoveryAmount = $mobilization = $purchaseAmount = $salaryAmount = $peticashPurchaseAmount =
                    $salesTaxAmount = $purchaseOrderGst = $assetMaintenanceGst = $subcontractorGst = $inventorySiteTransfersInGst =
                    $inventorySiteTransfersOutGst = $siteTransferBillGst = 0;
                    $assetRent = 0;
                    $projectSiteAdvancePayment = new ProjectSiteAdvancePayment();
                    $outstandingMobilization = $projectSiteAdvancePayment->where('project_site_id',$project_site_id)->sum('amount');
                    $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
                    $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
                    foreach ($totalMonths as $month){
                        $billIds = $bill->where('quotation_id',$quotation['id'])
                            ->where('bill_status_id',$approvedBillStatusId)->orderBy('id')
                            ->whereMonth('date',$month['id'])
                            ->whereYear('date',$selectedYear['slug'])
                            ->pluck('id');
                        $billTransactionData = $billTransaction->whereIn('bill_id',$billIds)->get();
                        $billReconcileTransactionData = $billReconcileTransaction->whereIn('bill_id',$billIds)->get();
                        foreach ($billIds as $billId) {
                            $billData = $this->getBillData($billId);
                            $salesTaxAmount += $billData['tax_amount'];
                            $sales += $billData['total_amount_with_tax'];
                        }
                        $transactionTotal = $billTransactionData->sum('total');
                        $mobilization += $billTransactionData->where('paid_from_advanced',true)->sum('amount');
                        $receipt += ($transactionTotal != null) ? $transactionTotal : 0;
                        $retentionAmount = $billTransactionData->sum('retention_amount');
                        $reconciledRetentionAmount = $billReconcileTransactionData->where('transaction_slug','retention')->sum('amount');
                        $totalRetention += $retentionAmount - $reconciledRetentionAmount;
                        $holdAmount = $billTransactionData->sum('hold');
                        $reconciledHoldAmount = $billReconcileTransactionData->where('transaction_slug','hold')->sum('amount');
                        $totalHold += $holdAmount - $reconciledHoldAmount;
                        $debitAmount += $billTransactionData->sum('debit');
                        $tdsAmount += $billTransactionData->sum('tds_amount');
                        $otherRecoveryAmount += $billTransactionData->sum('other_recovery_value');

                        $purchaseAmount += $purchaseOrderBillMonthlyExpense->where('month_id',$month['id'])
                                            ->where('year_id',$selectedYear['id'])
                                            ->where('project_site_id',$project_site_id)->sum('total_expense');
                        $salaryAmount += $peticashSalaryTransactionMonthlyExpense->where('month_id',$month['id'])
                                            ->where('year_id',$selectedYear['id'])
                                            ->where('project_site_id',$project_site_id)->sum('total_expense');
                        $peticashPurchaseAmount += $peticashPurchaseTransactionMonthlyExpense->where('month_id',$month['id'])
                                            ->where('year_id',$selectedYear['id'])
                                            ->where('project_site_id',$project_site_id)->sum('total_expense');

                        $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_bills.sc_structure_id',
                                                    '=','subcontractor_structure.id')
                                                    ->where('subcontractor_structure.project_site_id',$project_site_id)
                                                    ->where('subcontractor_bills.subcontractor_bill_status_id',$subcontractorApprovedBillStatusId)
                                                    ->whereMonth('subcontractor_bills.created_at',$month['id'])
                                                    ->whereYear('subcontractor_bills.created_at',$selectedYear['slug'])
                                                    ->pluck('subcontractor_bills.id');

                        if(count($subcontractorBillIds) > 0){
                            foreach ($subcontractorBillIds as $subcontractorBillId){
                                $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                                $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                                if($subcontractorStructureData->contractType->slug == 'sqft'){
                                    $rate = $subcontractorStructureData['rate'];
                                }else{
                                    $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                                }
                                $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                                $subTotal = $subcontractorBillData['qty'] * $rate;
                                $taxTotal = 0;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                    $subcontractorGst += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                                $subcontractorTotal += round(($subTotal + $subcontractorGst),3);
                            }
                        }
                        $assetMaintenanceGst += $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                            ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                            ->join('assets','assets.id','=','asset_maintenance.asset_id')
                            ->where('asset_maintenance.project_site_id',$project_site_id)
                            ->whereMonth('asset_maintenance_bill_payments.created_at',$month['id'])
                            ->whereYear('asset_maintenance_bill_payments.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('asset_maintenance_bills.cgst_amount +asset_maintenance_bills.sgst_amount +asset_maintenance_bills.igst_amount'));

                        $purchaseOrderGst += round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$project_site_id)
                            ->whereMonth('purchase_order_bills.created_at',$month['id'])
                            ->whereYear('purchase_order_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);
                        $inventorySiteTransfersInGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersOutGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $siteTransferBillGst += $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$project_site_id)
                            ->whereMonth('site_transfer_bills.created_at',$month['id'])
                            ->whereYear('site_transfer_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('site_transfer_bills.tax_amount + site_transfer_bills.extra_amount_cgst_amount + site_transfer_bills.extra_amount_sgst_amount + site_transfer_bills.extra_amount_igst_amount'));

                    }
                    $purchaseTaxAmount = $assetMaintenanceGst + $purchaseOrderGst + $inventorySiteTransfersInGst + $siteTransferBillGst - $inventorySiteTransfersOutGst;
                    $indirectExpenses = $salesTaxAmount - $purchaseTaxAmount - $subcontractorGst;
                    $openingExpenses = $quotation['opening_expenses'];

                    $outstanding = $sales - $debitAmount - $tdsAmount - $totalRetention - $otherRecoveryAmount - $totalHold - $receipt - $mobilization;
                    $total = $purchaseAmount + $salaryAmount + $assetRent + $peticashPurchaseAmount + $indirectExpenses + $subcontractorTotal + $openingExpenses;
                    $salesPnL = $sales - $debitAmount - $tdsAmount - $totalHold - $otherRecoveryAmount;
                    $salesWisePnL = $salesPnL - $total;
                    $receiptWisePnL = $receipt - $total;
                    $data = array(
                        array_merge(array(null,'Sales', 'Retention', 'Receipt', 'Mobilization', 'Outstanding', 'Category', 'Amount')),
                        array_merge(array(null, round($sales,3), round($totalRetention,3), round($receipt,3), round($mobilization,3), round($outstanding,3), 'Purchase', round($purchaseAmount,3))),
                        array_merge(array('Debit Note', round($debitAmount,3)), array_fill(0,4,null) , array('Salary', round($salaryAmount,3))),
                        array_merge(array('TDS', round($tdsAmount,3)) , array_fill(0,4,null) , array('Asset Rent', round($assetRent,3))),
                        array_merge(array('Hold', round($totalHold,3)) , array_fill(0,4,null) , array('Asset Opening Balance', 0)),
                        array_merge(array('Other Recovery', round($otherRecoveryAmount,3)), array_fill(0,4,null) , array('Misc. Purchase', round($peticashPurchaseAmount,3))),
                        array_merge(array_fill(0,6,null) , array('Indirect expenses', round($indirectExpenses,3))),
                        array_merge(array_fill(0,6,null) , array('Opening Balance', round($openingExpenses,3))),
                        array_merge(array_fill(0,6,null) , array('Subcontractor', round($subcontractorTotal,3))),
                        array_merge(array_fill(0,5,null) , array(round($outstanding,3)), array_fill(0,1,null) ,array(round($total,3))),
                    );
                    $summaryData = array(
                        array_merge(array(null,'Total Bill/Receipt (A)','Total Expense (B)' , 'P/L (A-B)')),
                        array_merge(array('Sales P/L',round(($salesPnL),3) , round($total,3) , round(($salesWisePnL),3))),
                        array_merge(array('Receipt P/L',round($receipt,3) , round($total,3) , round(($receiptWisePnL),3))),
                        array_merge(array_fill(0,4,null)),
                        array_merge(array_fill(0,4,null)),
                        array_merge(array(null,'Total Mobilization Given','Total Deducted','Balance')),
                        array_merge(array('Outstanding Mobilization P/L',round($outstandingMobilization,3) , round($mobilization,3) , round(($outstandingMobilization - $mobilization),3))),
                    );
                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    Excel::create($reportType."_".$currentDate, function($excel) use($monthlyTotal, $data, $reportType, $header, $companyHeader, $date, $projectName, $summaryData) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($reportType, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName, $summaryData) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:H2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:H3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:H4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:H5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:H6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:H7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Sitewise PnL Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:H8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setFontWeight('bold');
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });
                            $row = 9;
                            $headerRow =  $row+1;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        $cell->setFontWeight('bold');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

                                    });
                                }
                            }
                            $row++;$row++;
                            foreach($summaryData as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$key1) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        $cell->setFontWeight('bold');
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;

                default :
                    break;
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Download Detail Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
                'type' => $reportType
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getBillData($billId){
        try{
            $billInstance = new Bill();
            $billStatusInstance = new BillStatus();
            $billQuotationProductInstance = new BillQuotationProducts();
            $quotationProductInstance = new QuotationProduct();
            $quotationInstance = new Quotation();
            $billQuotationExtraItemInstance = new BillQuotationExtraItem();
            $billTaxInstance = new BillTax();
            $bill = $billInstance->where('id',$billId)->first();
            $cancelBillStatusId = $billStatusInstance->where('slug','cancelled')->pluck('id')->first();
            $bills = $billInstance->where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $billQuotationProducts = $billQuotationProductInstance->where('bill_id',$bill['id'])->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_subtotal'] = $total['cumulative_bill_amount'] = $total_extra_item =  0;
            for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                $billQuotationProducts[$iterator]['quotationProducts'] = $quotationProductInstance->where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->where('quotation_id',$bill['quotation_id'])->first();
                $quotation_id = $billInstance->where('id',$billQuotationProducts[$iterator]['bill_id'])->pluck('quotation_id')->first();
                $discount = $quotationInstance->where('id',$quotation_id)->pluck('discount')->first();
                $rate_per_unit = $quotationProductInstance->where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->pluck('rate_per_unit')->first();
                $billQuotationProducts[$iterator]['rate'] = round(($rate_per_unit - ($rate_per_unit * ($discount / 100))),3);
                $billQuotationProducts[$iterator]['current_bill_subtotal'] = round(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                $billWithoutCancelStatus = $billInstance->where('id','<',$bill['id'])->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
                $previousBills = $billQuotationProductInstance->whereIn('bill_id',$billWithoutCancelStatus)->get();
                foreach($previousBills as $key => $previousBill){
                    if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                        $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                    }
                }
                $billQuotationProducts[$iterator]['cumulative_quantity'] = round(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $billQuotationProducts[$iterator]['current_bill_subtotal']),3);
            }
            $extraItems = $billQuotationExtraItemInstance->where('bill_id',$bill->id)->get();
            if(count($extraItems) > 0){
                $total_extra_item = 0;
                foreach($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = $billQuotationExtraItemInstance->whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total_extra_item = $total_extra_item + $extraItem['rate'];
                }
                $total['current_bill_subtotal'] = round(($total['current_bill_subtotal'] + $total_extra_item),3);
            }
            $total_rounded['current_bill_subtotal'] = round($total['current_bill_subtotal'],3);
            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] = $total['current_bill_amount'] = round(($total['current_bill_subtotal'] - $bill['discount_amount']),3);
            $billTaxes = $billTaxInstance->join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('bill_taxes.bill_id','=',$bill['id'])
                ->where('taxes.is_special','=', false)
                ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                ->get();
            $taxes = array();
            if($billTaxes != null){
                $billTaxes = $billTaxes->toArray();
            }
            for($j = 0 ; $j < count($billTaxes) ; $j++){
                $taxes[$billTaxes[$j]['tax_id']] = $billTaxes[$j];
                $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount'] = round(($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100)) , 3);
                $final['current_bill_amount'] = round(($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']),3);
            }
            $specialTaxes= $billTaxInstance->join('taxes','taxes.id','=','bill_taxes.tax_id')
                ->where('bill_taxes.bill_id','=',$bill['id'])
                ->where('taxes.is_special','=', true)
                ->select('bill_taxes.id as id','bill_taxes.percentage as percentage','taxes.id as tax_id','taxes.name as tax_name','bill_taxes.applied_on as applied_on')
                ->get();
            if($specialTaxes != null){
                $specialTaxes = $specialTaxes->toArray();
            }else{
                $specialTaxes = array();
            }
            if(count($specialTaxes) > 0){
                for($j = 0 ; $j < count($specialTaxes) ; $j++){
                    $specialTaxes[$j]['applied_on'] = json_decode($specialTaxes[$j]['applied_on']);
                    $specialTaxAmount = 0;
                    foreach($specialTaxes[$j]['applied_on'] as $appliedOnTaxId){
                        if($appliedOnTaxId == 0){
                            $specialTaxAmount = $specialTaxAmount + ($total['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                        }else{
                            $specialTaxAmount = $specialTaxAmount + ($taxes[$appliedOnTaxId]['current_bill_amount'] * ($specialTaxes[$j]['percentage'] / 100));
                        }
                    }
                    $specialTaxes[$j]['current_bill_amount'] = round($specialTaxAmount , 3);
                    $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount),3);
                }
            }else{
                $final['current_bill_gross_total_amount'] = round($final['current_bill_amount'],3);
            }
            $billData['date'] = $bill['date'];
            $billData['created_at'] = $bill['created_at'];
            $billData['basic_amount'] = $total_rounded['current_bill_amount'];
            $billData['total_amount_with_tax'] = $final['current_bill_gross_total_amount'];
            $billData['tax_amount'] = $final['current_bill_gross_total_amount'] - $total_rounded['current_bill_amount'];
            return $billData;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get bill data for report in Report Management',
                'params' => $billId,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubcontractor(Request $request){
        $subcontractorOptions = array();
        try{
            $subcontractor = new Subcontractor();
            $subcontractorData = $subcontractor->join('subcontractor_structure','subcontractor_structure.subcontractor_id','=','subcontractor.id')
                ->join('subcontractor_bills','subcontractor_bills.sc_structure_id','=','subcontractor_structure.id')
                ->join('project_sites','subcontractor_structure.project_site_id','=','project_sites.id')
                ->where('subcontractor_structure.project_site_id',$request['project_site_id'])
                ->distinct('subcontractor.id')
                ->orderBy('subcontractor.subcontractor_name','asc')
                ->select('subcontractor.id','subcontractor.subcontractor_name')->get();
            for($iterator = 0; $iterator < count($subcontractorData); $iterator++){
                    $subcontractorOptions[] = '<option value="'.$subcontractorData[$iterator]['id'].'">'.$subcontractorData[$iterator]['subcontractor_name'].'</option>';
                }
            $status = 200;
        }catch(\Exception $e){
            $status = 500;
            $data = [
                'action' => 'Get Subcontractor for Report',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response($subcontractorOptions,$status);
    }

    public function getSalesListing(Request $request){
        try{
            $iTotalRecords = 0;
            $projectSite = new ProjectSite();
            if(!(array_key_exists('sales_month_id',$request->all()))){
                $request['sales_month_id'] = 'all'; $request['sales_year_id'] = 'all'; $request['sales_project_site_id'] = null;
            }
            $month = new Month();
            $januaryMonthId = $month->where('slug','january')->pluck('id')->first();
            $decemberMonthId = $month->where('slug','december')->pluck('id')->first();
            switch(true) {
                case (($request['sales_month_id'] === 'all' && $request['sales_year_id'] === 'all' && $request['sales_project_site_id'] == null)) :
                    Log::info('inside sales case 1');
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount('null', 'null', 'null', $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                    break;

                case ($request['sales_month_id'] === 'all' && $request['sales_year_id'] === 'all' && $request['sales_project_site_id'] != null) :
                    Log::info('inside sales case 2');
                    $requestedProjectSiteIds = explode(',',$request['sales_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount('null', 'null', 'null', $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                    break;

                case ($request['sales_month_id'] === 'all' && $request['sales_year_id'] !== 'all' && $request['sales_project_site_id'] != null) :
                        Log::info('inside sales case 3');
                    $requestedProjectSiteIds = explode(',',$request['sales_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($januaryMonthId, $decemberMonthId, $request['sales_year_id'], $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                    break;

                case ($request['sales_month_id'] === 'all' && $request['sales_year_id'] !== 'all' && $request['sales_project_site_id'] == null) :
                    Log::info('inside sales case 4');
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($januaryMonthId, $decemberMonthId, $request['sales_year_id'], $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                    break;

                case ($request['sales_month_id'] !== 'all' && $request['sales_year_id'] === 'all' && $request['sales_project_site_id'] == null) :
                    Log::info('inside sales case 5');
                    $startMonthId = $endMonthId = $month->where('id',$request['sales_month_id'])->pluck('id')->first();

                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, 'null', $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                break;

                case ($request['sales_month_id'] !== 'all' && $request['sales_year_id'] === 'all' && $request['sales_project_site_id'] != null) :
                    Log::info('inside sales case 6 ');
                    $startMonthId = $endMonthId = $month->where('id',$request['sales_month_id'])->pluck('id')->first();

                    $requestedProjectSiteIds = explode(',',$request['sales_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, 'null', $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                break;

                case ($request['sales_month_id'] !== 'all' && $request['sales_year_id'] !== 'all' && $request['sales_project_site_id'] != null) :
                    Log::info('inside sales case 7 ');
                    $startMonthId = $endMonthId = $month->where('id',$request['sales_month_id'])->pluck('id')->first();

                    $requestedProjectSiteIds = explode(',',$request['sales_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, $request['sales_year_id'], $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                break;

                case ($request['sales_month_id'] !== 'all' && $request['sales_year_id'] !== 'all' && $request['sales_project_site_id'] == null) :
                    Log::info('inside sales case 8');
                    $startMonthId = $endMonthId = $month->where('id',$request['sales_month_id'])->pluck('id')->first();

                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $salesAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, $request['sales_year_id'], $projectSiteData[$pagination]['id'],'sales');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($salesAmount['sales'], 3),
                            number_format($salesAmount['receipt'], 3),
                            number_format($salesAmount['outstanding'], 3),
                            number_format($salesAmount['total_expense'], 3),
                            number_format($salesAmount['outstanding_mobilization'], 3),
                            number_format($salesAmount['sitewise_pNl'], 3),
                            number_format($salesAmount['receiptwise_pNl'], 3),
                        ];
                    }
                break;

            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Sales Listing Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($records,200);
    }

    public function getExpensesListing(Request $request){
        try{
            $projectSite = new ProjectSite();
            $iTotalRecords = 0;
            if(!(array_key_exists('expense_month_id',$request->all()))){
                $request['expense_month_id'] = 'all'; $request['expense_year_id'] = 'all'; $request['expense_project_site_id'] = null;
            }
            $month = new Month();
            $januaryMonthId = $month->where('slug','january')->pluck('id')->first();
            $decemberMonthId = $month->where('slug','december')->pluck('id')->first();
            switch(true) {
                case (($request['expense_month_id'] === 'all' && $request['expense_year_id'] === 'all' && $request['expense_project_site_id'] == null)) :
                    Log::info('inside expense case 1');
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount('null', 'null', 'null', $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] === 'all' && $request['expense_year_id'] === 'all' && $request['expense_project_site_id'] != null) :
                    Log::info('inside expense case 2');
                    $requestedProjectSiteIds = explode(',',$request['expense_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount('null', 'null', 'null', $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] === 'all' && $request['expense_year_id'] !== 'all' && $request['expense_project_site_id'] != null) :
                    Log::info('inside expense case 3');
                    $requestedProjectSiteIds = explode(',',$request['expense_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($januaryMonthId, $decemberMonthId, $request['expense_year_id'], $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] === 'all' && $request['expense_year_id'] !== 'all' && $request['expense_project_site_id'] == null) :
                    Log::info('inside expense case 4');
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($januaryMonthId, $decemberMonthId, $request['expense_year_id'], $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] !== 'all' && $request['expense_year_id'] === 'all' && $request['expense_project_site_id'] == null) :
                    Log::info('inside expense case 5');
                        $startMonthId = $endMonthId = $month->where('id',$request['expense_month_id'])->pluck('id')->first();

                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, 'null', $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] !== 'all' && $request['expense_year_id'] === 'all' && $request['expense_project_site_id'] != null) :
                    Log::info('inside expense case 6');
                        $startMonthId = $endMonthId = $month->where('id',$request['expense_month_id'])->pluck('id')->first();

                    $requestedProjectSiteIds = explode(',',$request['expense_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, 'null', $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] !== 'all' && $request['expense_year_id'] !== 'all' && $request['expense_project_site_id'] != null) :
                    Log::info('inside expense case 7');
                        $startMonthId = $endMonthId = $month->where('id',$request['expense_month_id'])->pluck('id')->first();

                    $requestedProjectSiteIds = explode(',',$request['expense_project_site_id']);
                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->whereIn('project_sites.id',$requestedProjectSiteIds)
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, $request['expense_year_id'], $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

                case ($request['expense_month_id'] !== 'all' && $request['expense_year_id'] !== 'all' && $request['expense_project_site_id'] == null) :
                    Log::info('inside expense case 8');
                        $startMonthId = $endMonthId = $month->where('id',$request['expense_month_id'])->pluck('id')->first();

                    $projectSiteData = $projectSite->join('projects', 'projects.id', '=', 'project_sites.project_id')
                        ->orderBy('projects.name')->select('project_sites.id', 'projects.name')->get();
                    $iTotalRecords = count($projectSiteData);
                    $records = array();
                    $records['data'] = array();
                    $end = $request->length < 0 ? count($projectSiteData) : $request->length;
                    for ($iterator = 0, $pagination = $request->start; $iterator < $end && $pagination < count($projectSiteData); $iterator++, $pagination++) {
                        $expenseAmount = $this->getSalesExpenseAmount($startMonthId, $endMonthId, $request['expense_year_id'], $projectSiteData[$pagination]['id'],'expense');
                        $records['data'][$iterator] = [
                            $projectName = ucwords($projectSiteData[$pagination]['name']),
                            number_format($expenseAmount['purchase'],3),
                            number_format($expenseAmount['salary'],3),
                            number_format($expenseAmount['asset_rent'],3),
                            number_format($expenseAmount['asset_opening_balance'],3),
                            number_format($expenseAmount['subcontractor'],3),
                            number_format($expenseAmount['misc_purchase'],3),
                            number_format($expenseAmount['indirect_expense'],3),
                            number_format($expenseAmount['opening_balance'],3),
                            number_format($expenseAmount['total_expense'],3),
                        ];
                    }
                    break;

            }

            /*for($iterator = 0 , $pagination = $request->start ; $iterator < $end && $pagination < count($projectSiteData) ; $iterator++ , $pagination++){
                $expenseAmount = $this->getSalesExpenseAmount('null','null','null',$projectSiteData[$pagination]['id']);
                $records['data'][$iterator] = [
                    $projectName = ucwords($projectSiteData[$pagination]['name']),
                    number_format($expenseAmount['purchase'],3),
                    number_format($expenseAmount['salary'],3),
                    number_format($expenseAmount['asset_rent'],3),
                    number_format($expenseAmount['asset_opening_balance'],3),
                    number_format($expenseAmount['subcontractor'],3),
                    number_format($expenseAmount['misc_purchase'],3),
                    number_format($expenseAmount['indirect_expense'],3),
                    number_format($expenseAmount['opening_balance'],3),
                    number_format($expenseAmount['total_expense'],3),
                ];
            }*/
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Expenses Listing Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
                'type' => $request->report_type
            ];
            Log::critical(json_encode($data));
        }
        return response()->json($records,200);
    }

    public function getSalesExpenseAmount($startMonthId,$endMonthId,$yearId,$projectSiteId,$slug){
        try{
            $salesData = array();
            $month = new Month();
            $year = new Year();
            $quotation = new Quotation();
            $bill = new Bill();
            $billStatus = new BillStatus();
            $billTransaction = new BillTransaction();
            $billReconcileTransaction = new BillReconcileTransaction();
            $subcontractorStructure = new SubcontractorStructure();
            $subcontractorBill = new SubcontractorBill();
            $subcontractorBillStatus = new SubcontractorBillStatus();
            $assetMaintenanceBillPayment = new AssetMaintenanceBillPayment();
            $purchaseOrderBill = new PurchaseOrderBill();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryTransferTypes = new InventoryTransferTypes();
            $inventoryComponentTransferStatus = new InventoryComponentTransferStatus();
            $siteTransferBill = new SiteTransferBill();
            $purchaseOrderBillMonthlyExpense = new PurchaseOrderBillMonthlyExpense();
            $peticashSalaryTransactionMonthlyExpense = new PeticashSalaryTransactionMonthlyExpense();
            $peticashPurchaseTransactionMonthlyExpense = new PeticashPurchaseTransactionMonthlyExpense();
            $projectSiteAdvancePayment = new ProjectSiteAdvancePayment();
            $outstandingMobilization = $projectSiteAdvancePayment->where('project_site_id',$projectSiteId)->sum('amount');
            $approvedBillStatusId = $billStatus->where('slug','approved')->pluck('id')->first();
            $sales = $receipt = $total = $totalRetention = $totalHold = $debitAmount = $tdsAmount = $subcontractorTotal =
            $otherRecoveryAmount = $mobilization = $purchaseAmount = $salaryAmount = $peticashPurchaseAmount =
            $salesTaxAmount = $purchaseOrderGst = $assetMaintenanceGst = $subcontractorGst = $inventorySiteTransfersInGst =
            $inventorySiteTransfersOutGst = $siteTransferBillGst = 0;
            $assetRent = 0;
            $quotation = $quotation->where('project_site_id',$projectSiteId)->first();
            $subcontractorApprovedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id')->first();
            $inventoryComponentSiteTransferIds = $inventoryTransferTypes->where('slug','site')->get();
            $approvedComponentTransferStatusId = $inventoryComponentTransferStatus->where('slug','approved')->pluck('id');
            switch(true){
                case ($yearId == 'null' && $startMonthId == 'null')  :
                    Log::info('Inside CASE 1');
                    $billIds = $bill->where('quotation_id',$quotation['id'])
                            ->where('bill_status_id',$approvedBillStatusId)->orderBy('id')
                            ->pluck('id');
                    $billTransactionData = $billTransaction->whereIn('bill_id',$billIds)->get();
                    $billReconcileTransactionData = $billReconcileTransaction->whereIn('bill_id',$billIds)->get();
                    foreach ($billIds as $billId) {
                        $billData = $this->getBillData($billId);
                        $salesTaxAmount += $billData['tax_amount'];
                        $sales += $billData['total_amount_with_tax'];
                    }
                    $transactionTotal = $billTransactionData->sum('total');
                    $mobilization += $billTransactionData->where('paid_from_advanced',true)->sum('amount');
                    $receipt += ($transactionTotal != null) ? $transactionTotal : 0;
                    $retentionAmount = $billTransactionData->sum('retention_amount');
                    $reconciledRetentionAmount = $billReconcileTransactionData->where('transaction_slug','retention')->sum('amount');
                    $totalRetention += $retentionAmount - $reconciledRetentionAmount;
                    $holdAmount = $billTransactionData->sum('hold');
                    $reconciledHoldAmount = $billReconcileTransactionData->where('transaction_slug','hold')->sum('amount');
                    $totalHold += $holdAmount - $reconciledHoldAmount;
                    $debitAmount += $billTransactionData->sum('debit');
                    $tdsAmount += $billTransactionData->sum('tds_amount');
                    $otherRecoveryAmount += $billTransactionData->sum('other_recovery_value');

                    $purchaseAmount += $purchaseOrderBillMonthlyExpense
                        ->where('project_site_id',$projectSiteId)->sum('total_expense');
                    $salaryAmount += $peticashSalaryTransactionMonthlyExpense
                        ->where('project_site_id',$projectSiteId)->sum('total_expense');
                    $peticashPurchaseAmount += $peticashPurchaseTransactionMonthlyExpense
                        ->where('project_site_id',$projectSiteId)->sum('total_expense');

                    $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_bills.sc_structure_id',
                        '=','subcontractor_structure.id')
                        ->where('subcontractor_structure.project_site_id',$projectSiteId)
                        ->where('subcontractor_bills.subcontractor_bill_status_id',$subcontractorApprovedBillStatusId)
                        ->pluck('subcontractor_bills.id');

                    if(count($subcontractorBillIds) > 0){
                        foreach ($subcontractorBillIds as $subcontractorBillId){
                            $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                            $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                            if($subcontractorStructureData->contractType->slug == 'sqft'){
                                $rate = $subcontractorStructureData['rate'];
                            }else{
                                $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                            }
                            $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                            $subTotal = $subcontractorBillData['qty'] * $rate;
                            $taxTotal = 0;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                $subcontractorGst += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                            }
                            $subcontractorTotal += round(($subTotal + $subcontractorGst),3);
                        }
                    }

                    $assetMaintenanceGst += $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                        ->join('assets','assets.id','=','asset_maintenance.asset_id')
                        ->where('asset_maintenance.project_site_id',$projectSiteId)
                        ->sum(DB::raw('asset_maintenance_bills.cgst_amount +asset_maintenance_bills.sgst_amount +asset_maintenance_bills.igst_amount'));

                    $purchaseOrderGst += round($purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$projectSiteId)
                        ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);

                    $inventorySiteTransfersInGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                        ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$projectSiteId)
                        ->where('inventory_components.is_material',true)
                        ->where('inventory_component_transfers.transfer_type_id',
                            $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                        ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                    $inventorySiteTransfersOutGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                        ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$projectSiteId)
                        ->where('inventory_components.is_material',true)
                        ->where('inventory_component_transfers.transfer_type_id',
                            $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                        ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                        ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                    $siteTransferBillGst += $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                        '=','site_transfer_bills.inventory_component_transfer_id')
                        ->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                        ->where('inventory_components.project_site_id',$projectSiteId)
                        ->sum(DB::raw('site_transfer_bills.tax_amount + site_transfer_bills.extra_amount_cgst_amount + site_transfer_bills.extra_amount_sgst_amount + site_transfer_bills.extra_amount_igst_amount'));
                    break;

                case ($yearId === 'null') :
                    Log::info('Inside CASE 2');
                    $totalMonths = $month->whereBetween('id',[$startMonthId,$endMonthId])
                        ->select('id','name','slug')->get();

                    foreach ($totalMonths as $month){
                        $billIds = $bill->where('quotation_id',$quotation['id'])
                            ->where('bill_status_id',$approvedBillStatusId)->orderBy('id')
                            ->whereMonth('date',$month['id'])
                            ->pluck('id');
                        $billTransactionData = $billTransaction->whereIn('bill_id',$billIds)->get();
                        $billReconcileTransactionData = $billReconcileTransaction->whereIn('bill_id',$billIds)->get();
                        foreach ($billIds as $billId) {
                            $billData = $this->getBillData($billId);
                            $salesTaxAmount += $billData['tax_amount'];
                            $sales += $billData['total_amount_with_tax'];
                        }
                        $transactionTotal = $billTransactionData->sum('total');
                        $mobilization += $billTransactionData->where('paid_from_advanced',true)->sum('amount');
                        $receipt += ($transactionTotal != null) ? $transactionTotal : 0;
                        $retentionAmount = $billTransactionData->sum('retention_amount');
                        $reconciledRetentionAmount = $billReconcileTransactionData->where('transaction_slug','retention')->sum('amount');
                        $totalRetention += $retentionAmount - $reconciledRetentionAmount;
                        $holdAmount = $billTransactionData->sum('hold');
                        $reconciledHoldAmount = $billReconcileTransactionData->where('transaction_slug','hold')->sum('amount');
                        $totalHold += $holdAmount - $reconciledHoldAmount;
                        $debitAmount += $billTransactionData->sum('debit');
                        $tdsAmount += $billTransactionData->sum('tds_amount');
                        $otherRecoveryAmount += $billTransactionData->sum('other_recovery_value');

                        $purchaseAmount += $purchaseOrderBillMonthlyExpense->where('month_id',$month['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');
                        $salaryAmount += $peticashSalaryTransactionMonthlyExpense->where('month_id',$month['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');
                        $peticashPurchaseAmount += $peticashPurchaseTransactionMonthlyExpense->where('month_id',$month['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');

                        $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_bills.sc_structure_id',
                            '=','subcontractor_structure.id')
                            ->where('subcontractor_structure.project_site_id',$projectSiteId)
                            ->where('subcontractor_bills.subcontractor_bill_status_id',$subcontractorApprovedBillStatusId)
                            ->whereMonth('subcontractor_bills.created_at',$month['id'])
                            ->pluck('subcontractor_bills.id');

                        if(count($subcontractorBillIds) > 0){
                            foreach ($subcontractorBillIds as $subcontractorBillId){
                                $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                                $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                                if($subcontractorStructureData->contractType->slug == 'sqft'){
                                    $rate = $subcontractorStructureData['rate'];
                                }else{
                                    $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                                }
                                $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                                $subTotal = $subcontractorBillData['qty'] * $rate;
                                $taxTotal = 0;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                    $subcontractorGst += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                                $subcontractorTotal += round(($subTotal + $subcontractorGst),3);
                            }
                        }

                        $assetMaintenanceGst += $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                            ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                            ->join('assets','assets.id','=','asset_maintenance.asset_id')
                            ->where('asset_maintenance.project_site_id',$projectSiteId)
                            ->whereMonth('asset_maintenance_bill_payments.created_at',$month['id'])
                            ->sum(DB::raw('asset_maintenance_bills.cgst_amount +asset_maintenance_bills.sgst_amount +asset_maintenance_bills.igst_amount'));

                        $purchaseOrderGst += round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$projectSiteId)
                            ->whereMonth('purchase_order_bills.created_at',$month['id'])
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);

                        $inventorySiteTransfersInGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersOutGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $siteTransferBillGst += $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->whereMonth('site_transfer_bills.created_at',$month['id'])
                            ->sum(DB::raw('site_transfer_bills.tax_amount + site_transfer_bills.extra_amount_cgst_amount + site_transfer_bills.extra_amount_sgst_amount + site_transfer_bills.extra_amount_igst_amount'));
                    }

                    break;

                case ($yearId != 'null') :
                    Log::info('Inside CASE 3');
                    $totalMonths = $month->whereBetween('id',[$startMonthId,$endMonthId])
                        ->select('id','name','slug')->get();
                    $selectedYear = $year->where('id',$yearId)->first();

                    foreach ($totalMonths as $month){
                        $billIds = $bill->where('quotation_id',$quotation['id'])
                            ->where('bill_status_id',$approvedBillStatusId)->orderBy('id')
                            ->whereMonth('date',$month['id'])
                            ->whereYear('date',$selectedYear['slug'])
                            ->pluck('id');
                        $billTransactionData = $billTransaction->whereIn('bill_id',$billIds)->get();
                        $billReconcileTransactionData = $billReconcileTransaction->whereIn('bill_id',$billIds)->get();
                        foreach ($billIds as $billId) {
                            $billData = $this->getBillData($billId);
                            $salesTaxAmount += $billData['tax_amount'];
                            $sales += $billData['total_amount_with_tax'];
                        }
                        $transactionTotal = $billTransactionData->sum('total');
                        $mobilization += $billTransactionData->where('paid_from_advanced',true)->sum('amount');
                        $receipt += ($transactionTotal != null) ? $transactionTotal : 0;
                        $retentionAmount = $billTransactionData->sum('retention_amount');
                        $reconciledRetentionAmount = $billReconcileTransactionData->where('transaction_slug','retention')->sum('amount');
                        $totalRetention += $retentionAmount - $reconciledRetentionAmount;
                        $holdAmount = $billTransactionData->sum('hold');
                        $reconciledHoldAmount = $billReconcileTransactionData->where('transaction_slug','hold')->sum('amount');
                        $totalHold += $holdAmount - $reconciledHoldAmount;
                        $debitAmount += $billTransactionData->sum('debit');
                        $tdsAmount += $billTransactionData->sum('tds_amount');
                        $otherRecoveryAmount += $billTransactionData->sum('other_recovery_value');

                        $purchaseAmount += $purchaseOrderBillMonthlyExpense->where('month_id',$month['id'])
                            ->where('year_id',$selectedYear['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');
                        $salaryAmount += $peticashSalaryTransactionMonthlyExpense->where('month_id',$month['id'])
                            ->where('year_id',$selectedYear['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');
                        $peticashPurchaseAmount += $peticashPurchaseTransactionMonthlyExpense->where('month_id',$month['id'])
                            ->where('year_id',$selectedYear['id'])
                            ->where('project_site_id',$projectSiteId)->sum('total_expense');

                        $subcontractorBillIds = $subcontractorBill->join('subcontractor_structure','subcontractor_bills.sc_structure_id',
                            '=','subcontractor_structure.id')
                            ->where('subcontractor_structure.project_site_id',$projectSiteId)
                            ->where('subcontractor_bills.subcontractor_bill_status_id',$subcontractorApprovedBillStatusId)
                            ->whereMonth('subcontractor_bills.created_at',$month['id'])
                            ->whereYear('subcontractor_bills.created_at',$selectedYear['slug'])
                            ->pluck('subcontractor_bills.id');

                        if(count($subcontractorBillIds) > 0){
                            foreach ($subcontractorBillIds as $subcontractorBillId){
                                $subcontractorBillData = $subcontractorBill->where('id',$subcontractorBillId)->first();
                                $subcontractorStructureData = $subcontractorStructure->where('id',$subcontractorBillData['sc_structure_id'])->first();
                                if($subcontractorStructureData->contractType->slug == 'sqft'){
                                    $rate = $subcontractorStructureData['rate'];
                                }else{
                                    $rate = $subcontractorStructureData['rate'] * $subcontractorStructureData['total_work_area'];
                                }
                                $subcontractorBillTaxes = $subcontractorBillData->subcontractorBillTaxes;
                                $subTotal = $subcontractorBillData['qty'] * $rate;
                                $taxTotal = 0;
                                foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                    $taxTotal += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                    $subcontractorGst += round((($subcontractorBillTaxData['percentage'] * $subTotal) / 100),3);
                                }
                                $subcontractorTotal += round(($subTotal + $subcontractorGst),3);
                            }
                        }

                        $assetMaintenanceGst += $assetMaintenanceBillPayment->join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                            ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                            ->join('assets','assets.id','=','asset_maintenance.asset_id')
                            ->where('asset_maintenance.project_site_id',$projectSiteId)
                            ->whereMonth('asset_maintenance_bill_payments.created_at',$month['id'])
                            ->whereYear('asset_maintenance_bill_payments.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('asset_maintenance_bills.cgst_amount +asset_maintenance_bills.sgst_amount +asset_maintenance_bills.igst_amount'));

                        $purchaseOrderGst += round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$projectSiteId)
                            ->whereMonth('purchase_order_bills.created_at',$month['id'])
                            ->whereYear('purchase_order_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);

                        $inventorySiteTransfersInGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','IN')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $inventorySiteTransfersOutGst += $inventoryComponentTransfer->join('inventory_components','inventory_components.id'
                            ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->where('inventory_components.is_material',true)
                            ->where('inventory_component_transfers.transfer_type_id',
                                $inventoryComponentSiteTransferIds->where('type','OUT')->pluck('id')->first())
                            ->where('inventory_component_transfers.inventory_component_transfer_status_id',$approvedComponentTransferStatusId)
                            ->whereMonth('inventory_component_transfers.created_at',$month['id'])
                            ->whereYear('inventory_component_transfers.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('inventory_component_transfers.cgst_amount + inventory_component_transfers.sgst_amount + inventory_component_transfers.igst_amount'));

                        $siteTransferBillGst += $siteTransferBill->join('inventory_component_transfers','inventory_component_transfers.id',
                            '=','site_transfer_bills.inventory_component_transfer_id')
                            ->join('inventory_components','inventory_components.id'
                                ,'=','inventory_component_transfers.inventory_component_id')
                            ->where('inventory_components.project_site_id',$projectSiteId)
                            ->whereMonth('site_transfer_bills.created_at',$month['id'])
                            ->whereYear('site_transfer_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('site_transfer_bills.tax_amount + site_transfer_bills.extra_amount_cgst_amount + site_transfer_bills.extra_amount_sgst_amount + site_transfer_bills.extra_amount_igst_amount'));
                    }

                    break;
            }
            $purchaseTaxAmount = $assetMaintenanceGst + $purchaseOrderGst + $inventorySiteTransfersInGst + $siteTransferBillGst - $inventorySiteTransfersOutGst;
            $indirectExpenses = $salesTaxAmount - $purchaseTaxAmount - $subcontractorGst;
            $openingExpenses = $quotation['opening_expenses'];

            $outstanding = $sales - $debitAmount - $tdsAmount - $totalRetention - $otherRecoveryAmount - $totalHold - $receipt - $mobilization;
            $totalExpense = $purchaseAmount + $salaryAmount + $assetRent + $peticashPurchaseAmount + $indirectExpenses + $subcontractorTotal + $openingExpenses;
            $salesPnL = $sales - $debitAmount - $tdsAmount - $totalHold - $otherRecoveryAmount;
            $salesWisePnL = $salesPnL - $totalExpense;
            $receiptWisePnL = $receipt - $totalExpense;
            $salesData['sales'] = $salesPnL;
            $salesData['receipt'] = $receipt;
            $salesData['outstanding'] = $outstanding;
            $salesData['total_expense'] = $totalExpense;
            $salesData['outstanding_mobilization'] = $outstandingMobilization - $mobilization;
            $salesData['sitewise_pNl'] = $salesWisePnL;
            $salesData['receiptwise_pNl'] = $receiptWisePnL;
            $salesData['purchase'] = $purchaseAmount;
            $salesData['salary'] = $salaryAmount;
            $salesData['asset_rent'] = $assetRent;
            $salesData['asset_opening_balance'] = $assetRent;
            $salesData['subcontractor'] = $subcontractorTotal;
            $salesData['misc_purchase'] = $peticashPurchaseAmount;
            $salesData['indirect_expense'] = $indirectExpenses;
            $salesData['opening_balance'] = $openingExpenses;
            return $salesData;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Sales Amount Report',
                'exception' => $e->getMessage(),
                'params' => $startMonthId,$endMonthId,$yearId,$projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
    }
}