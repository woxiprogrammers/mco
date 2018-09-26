<?php
/**
     * Created by PhpStorm.
     * User: manoj
     * Date: 5/9/18
     * Time: 6:11 PM
     */

namespace App\Http\Controllers\Report;


use App\Bill;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Helper\MaterialProductHelper;
use App\Http\Controllers\Controller;
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
            $end_date = $endDate[2].'-'.$endDate[1].'-'.$endDate[0].' 24:00:00';
            $globalProjectSiteId = $request['project_site_id'];

            $reportLimit = env('REPORT_LIMIT['.$request['report_name'].']');

            $downloadButtonDetails = array();
            $startLimit = 1; $endLimit = $reportLimit;

            switch ($request['report_name']) {
                case 'sitewise_purchase_report' :
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $count = $purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$globalProjectSiteId)
                        ->whereBetween('purchase_order_bills.created_at',[$start_date,$end_date])
                        ->orderBy('created_at','desc')
                        ->count();
                    $noOfButtons = $count/$reportLimit;
                    for($iterator = 0; $iterator < $noOfButtons; $iterator++){
                        $totalRecords = $iterator * $reportLimit;
                        $purchaseOrderBillDates = $purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$globalProjectSiteId)
                            ->whereBetween('purchase_order_bills.created_at',[$start_date,$end_date])
                            ->take($reportLimit)->skip($totalRecords)
                            ->orderBy('purchase_order_bills.created_at','asc')
                            ->pluck('purchase_order_bills.created_at');
                        $downloadButtonDetails[$iterator]['start_date'] = $purchaseOrderBillDates->last();
                        $downloadButtonDetails[$iterator]['end_date'] = $purchaseOrderBillDates->first();
                        $downloadButtonDetails[$iterator]['start_limit'] = $startLimit;
                        $downloadButtonDetails[$iterator]['end_limit'] = $endLimit;
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
                    $purchaseOrderBillMonthlyExpense = new PurchaseOrderBillMonthlyExpense();
                    $data[$row] = array(
                        'Bill Date', 'Bill Create Date', 'Bill No', 'Vendor Name', 'Basic Amount', 'Tax Amount',
                        'Bill Amount', 'Monthly Total'
                    );
                    $date = date('l, d F Y',strtotime($secondParameter)) .' - '. date('l, d F Y',strtotime($firstParameter));
                    $startYearID = $year->where('slug',(int)date('Y',strtotime($firstParameter)))->pluck('id')->first();
                    $endYearID = $year->where('slug',(int)date('Y',strtotime($secondParameter)))->pluck('id')->first();
                    $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name','slug')->get();
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $purchaseOrderBillMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$project_site_id)->pluck('projects.name')->first();
                    $purchaseOrderBillsData = $purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$project_site_id)
                        ->where('purchase_order_bills.created_at','<=',$firstParameter)
                        ->where('purchase_order_bills.created_at','>=',$secondParameter)
                        ->select('purchase_order_bills.amount','purchase_order_bills.transportation_tax_amount'
                            ,'purchase_order_bills.tax_amount','purchase_order_bills.extra_tax_amount','purchase_order_bills.bill_date'
                            ,'purchase_order_bills.bill_number','purchase_order_bills.created_at','vendors.company')
                        ->orderBy('created_at','desc')
                        ->get()->toArray();
                    $row = 1;
                    foreach($purchaseOrderBillsData as $key => $purchaseOrderBillData){
                        $thisMonth = (int)date('n',strtotime($purchaseOrderBillData['created_at']));
                        $data[$row]['bill_entry_date'] = date('d-m-Y',strtotime($purchaseOrderBillData['bill_date']));
                        $data[$row]['bill_created_date'] = date('d-m-Y',strtotime($purchaseOrderBillData['created_at']));
                        $data[$row]['bill_number'] = $purchaseOrderBillData['bill_number'];
                        $data[$row]['company_name'] = $purchaseOrderBillData['company'];
                        $taxAmount = round(($purchaseOrderBillData['transportation_tax_amount'] + $purchaseOrderBillData['extra_tax_amount'] + $purchaseOrderBillData['tax_amount']),3);
                        $data[$row]['basic_amount'] = round(($purchaseOrderBillData['amount'] - $taxAmount),3);
                        $data[$row]['tax_amount'] = $taxAmount;
                        $data[$row]['bill_amount'] = round($data[$row]['basic_amount'],3) + round($data[$row]['tax_amount'],3);
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
                                $cell->setValue('Purchase Bill Report - '.$projectName);
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow) {
                                        if($row == $monthHeaderRow){
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
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                        }
                                        if($setColor){
                                            $cell->setBackground('#d7f442');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

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
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $peticashSalaryTransactionMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }

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
                                $cell->setValue('Purchase Bill Report - '.$projectName);
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow) {
                                        if($row == $monthHeaderRow){
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
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                        }
                                        if($setColor){
                                            $cell->setBackground('#d7f442');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

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
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $total = $peticashPurchaseTransactionMonthlyExpense->where('month_id',$month['id'])
                                ->where('year_id',$thisYear['id'])
                                ->where('project_site_id',$project_site_id)
                                ->pluck('total_expense')->first();
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }

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
                                $cell->setValue('Purchase Bill Report - '.$projectName);
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow) {
                                        if($row == $monthHeaderRow){
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

                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setColor) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
                                        }
                                        if($setColor){
                                            $cell->setBackground('#d7f442');
                                        }
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

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
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
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
                                $data[$row]['basic_amount'] = number_format($billData['basic_amount'], 3);
                                $data[$row]['gst'] = number_format($billData['tax_amount'], 3);
                                $data[$row]['total_amount'] = number_format($billData['total_amount_with_tax'], 3);
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
                                        $data[$row]['mobilisation'] = number_format($billTransaction['amount'], 3);
                                        $totalMobilization += $billTransaction['amount'];
                                    }else{
                                        $data[$row]['transaction_amount'] = number_format($billTransaction['amount'], 3);
                                        $data[$row]['mobilisation'] = 0;
                                        $totalTransactionAmount += $billTransaction['amount'];
                                    }
                                    $data[$row]['tds'] = number_format($billTransaction['tds_amount'], 3);
                                    $data[$row]['retention'] = number_format($billTransaction['retention_amount'], 3);
                                    $data[$row]['hold'] = number_format($billTransaction['hold'], 3);
                                    $data[$row]['debit'] = number_format($billTransaction['debit'], 3);
                                    $data[$row]['other_recovery'] = number_format($billTransaction['other_recovery_value'], 3);
                                    $data[$row]['payable_amount'] = null;
                                    $receipt = $billTransaction['total'];
                                    $data[$row]['receipt'] = number_format($receipt, 3);
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
                                $data[$billRow]['remaining'] = number_format($data[$billRow]['remaining'], 3);
                                $data[$billRow]['payable'] = number_format($data[$billRow]['payable'], 3);
                                $data[$billRow]['total_paid'] = number_format($data[$billRow]['total_paid'], 3);
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
                        'Total', null, number_format($totalBasicAmount,3), number_format($totalGst,3), number_format($totalWithTaxAmount,3), number_format($totalTransactionAmount,3)
                            , number_format($totalMobilization,3), number_format($totalTds,3), number_format($totalRetention,3),number_format($totalHold,3),
                        number_format($totalDebit,3),number_format($totalOtherRecovery,3), number_format($totalPayable,3), number_format($totalReceipt,3),
                        number_format($totalPaid,3), number_format($totalRemaining,3), null
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
                                $cell->setValue('Purchase Bill Report - '.$projectName);
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow) {
                                        if($row == $monthHeaderRow){
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
                                $cell->setValue($mobilizeAdvance)->setFontWeight('bold');
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
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow,$setBold,$current_column) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        ($row == $headerRow || $setBold) ? $cell->setFontWeight('bold') : null;
                                        ($current_column == 'N') ? $cell->setBackground('#d7f442') : null;
                                        ($current_column == 'P') ? $cell->setFontColor('#d82517') : null;
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);

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
                        ' Bill Date : (Created Date)', 'Bill No.', 'Basic Amount', 'GST', 'With Tax Amount', 'Transaction Amount', 'TDS', 'Retention',
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
                    $subcontractorBillData = $subcontractorBill->where('sc_structure_id',$subcontractorStructureId)
                        ->whereIn('subcontractor_bill_status_id',array_column($statusId->toArray(),'id'))//->orderBy('id')
                        ->get();
                    foreach ($totalYears as $thisYear){
                        foreach ($months as $month){
                            $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                            $billIds = $subcontractorBill->where('sc_structure_id',$subcontractorStructureId)
                                ->whereIn('subcontractor_bill_status_id',array_column($statusId->toArray(),'id'))
                                ->whereMonth('created_at',$month['id'])
                                ->whereYear('created_at',$thisYear['slug'])
                                ->pluck('id');
                            $total = $subcontractorBillTransaction
                                    ->whereIn('subcontractor_bills_id',$billIds)
                                    ->sum('total');
                            $monthlyTotal[$iterator]['total'] = ($total != null) ? $total : 0;
                            $iterator++;
                        }
                    }
                    $billNo = 1;
                    $row = 1;
                    $totalBasicAmount = $totalGst = $totalWithTaxAmount = $totalTransactionAmount = $totalTds =
                    $totalRetention = $totalHold = $totalDebit = $totalOtherRecovery = $totalPayable = $totalReceipt = $totalPaid = $totalRemaining = 0;

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
                            $data[$row]['basic_amount'] = number_format($subTotal, 3);
                            $data[$row]['gst'] = number_format($taxTotal, 3);
                            $data[$row]['total_amount'] = number_format($finalTotal, 3);
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
                                $data[$row]['transaction_amount'] = number_format($billTransaction['subtotal'], 3);
                                $totalTransactionAmount += $billTransaction['subtotal'];
                                $data[$row]['tds'] = number_format($billTransaction['tds_amount'], 3);
                                $data[$row]['retention'] = number_format($billTransaction['retention_amount'], 3);
                                $data[$row]['hold'] = number_format($billTransaction['hold'], 3);
                                $data[$row]['debit'] = number_format($billTransaction['debit'], 3);
                                $data[$row]['other_recovery'] = number_format($billTransaction['other_recovery'], 3);
                                $data[$row]['payable_amount'] = null;
                                $receipt = $billTransaction['total'];
                                $data[$row]['receipt'] = number_format($receipt, 3);
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
                            $paidAmount = $data[$billRow ]['total_paid'];
                            $data[$billRow]['remaining'] = $data[$billRow]['payable'] - $data[$billRow ]['total_paid'];
                            $totalPaid += $data[$billRow]['total_paid'];
                            $totalPayable += $data[$billRow]['payable'];
                            $totalRemaining += $data[$billRow]['remaining'];
                            $data[$billRow]['remaining'] = number_format($data[$billRow]['remaining'], 3);
                            $data[$billRow]['payable'] = number_format($data[$billRow]['payable'], 3);
                            $data[$billRow]['total_paid'] = number_format($data[$billRow]['total_paid'], 3);
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
                        'Total', null, number_format($totalBasicAmount,3), number_format($totalGst,3), number_format($totalWithTaxAmount,3), number_format($totalTransactionAmount,3)
                        , number_format($totalTds,3), number_format($totalRetention,3),number_format($totalHold,3),
                        number_format($totalDebit,3),number_format($totalOtherRecovery,3), number_format($totalPayable,3), number_format($totalReceipt,3),
                        number_format($totalPaid,3), number_format($totalRemaining,3), null
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->getRowDimension($row)->setRowHeight(20);
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$monthHeaderRow) {
                                        if($row == $monthHeaderRow){
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
                        $data[$row]['basic_amount'] = number_format($basic_amount,3);
                        $data[$row]['gst'] = number_format($gst,3);
                        $data[$row]['total_amount'] = number_format($finalAmount,3);
                        $data[$row]['transaction_amount'] = number_format($transaction_amount,3);
                        $data[$row]['tds'] = number_format($tds,3);
                        $data[$row]['retention'] = number_format($retention,3);
                        $data[$row]['hold'] = number_format($hold,3);
                        $data[$row]['debit'] = number_format($debit,3);
                        $data[$row]['other_recovery'] = number_format($other_recovery,3);
                        $data[$row]['payable'] = number_format($finalAmount,3);
                        $data[$row]['receipt'] = number_format($receipt,3);
                        $data[$row]['balance_remaining'] = number_format($finalAmount - $receipt,3);
                        $totalBasicAmount += $basic_amount; $totalGst += $gst; $totalAmount += $finalAmount;
                        $totalTransactionAmount += $transaction_amount; $totalTds += $tds; $totalRetention += $retention;
                        $totalHold += $hold; $totalDebit += $debit; $totalOtherRecovery += $other_recovery;
                        $totalReceipt += $receipt; $totalBalanceRemaining += ($finalAmount - $receipt);
                        $row++;
                    }
                    $data[$row]['make_bold'] = true;
                    $totalRow = array(
                        'Total', number_format($totalBasicAmount), number_format($totalGst), number_format($totalAmount),
                        number_format($totalTransactionAmount), number_format($totalTds),
                        number_format($totalRetention), number_format($totalHold), number_format($totalDebit), number_format($totalOtherRecovery),
                        number_format($totalAmount), number_format($totalReceipt), number_format($totalBalanceRemaining)
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
                            $row++;
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
                    $data[$row] = array(
                        'Month - Year', 'Sales GST', 'Subcontractor GST', 'Purchase GST', 'GST'
                    );
                    $startMonth = $month->where('id',$firstParameter)->first();
                    $endMonth = $month->where('id',$secondParameter)->first();
                    $selectedYear = $year->where('id',$thirdParameter)->first();
                    $date = $startMonth['name'].' '.$selectedYear['slug'].' - '.$endMonth['name'].' '.$selectedYear['slug'];
                    $totalMonths = $month->whereBetween('id',[$firstParameter,$secondParameter])->select('id','name','slug')->get();
                    $row = 1;
                    foreach ($totalMonths as $month){
                        $data[$row]['month'] = $month['name'].'-'.$selectedYear['slug'];
                        $data[$row]['gst'] = $data[$row]['purchase_gst'] = $data[$row]['subcontractor_gst'] = $data[$row]['sales_gst'] =
                        $salesGst = $subcontractorGst = 0;
                        $quotationId = $quotation->where('project_site_id',$project_site_id)->pluck('id')->first();
                        $statusId = $billStatus->where('slug','approved')->pluck('id');
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
                        $data[$row]['sales_gst'] = number_format($salesGst,3);

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
                        $data[$row]['subcontractor_gst'] = number_format($subcontractorGst,3);

                        $purchaseGst = round($purchaseOrderBill
                            ->join('purchase_orders','purchase_orders.id','='
                                ,'purchase_order_bills.purchase_order_id')
                            ->join('purchase_requests','purchase_requests.id','='
                                ,'purchase_orders.purchase_request_id')
                            ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                            ->where('purchase_requests.project_site_id',$project_site_id)
                            ->whereMonth('purchase_order_bills.created_at',$month['id'])
                            ->whereYear('purchase_order_bills.created_at',$selectedYear['slug'])
                            ->sum(DB::raw('purchase_order_bills.transportation_tax_amount + purchase_order_bills.tax_amount + purchase_order_bills.extra_tax_amount')),3);
                        $data[$row]['purchase_gst'] = number_format($purchaseGst,3);
                        //$data[$row]['gst'] = $salesGst - $purchaseGst - $subcontractorGst;
                        $data[$row]['gst'] = number_format(($salesGst - $purchaseGst - $subcontractorGst),3);
                        $row++;
                    }
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
}