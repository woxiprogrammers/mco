<?php
/**
     * Created by PhpStorm.
     * User: manoj
     * Date: 5/9/18
     * Time: 6:11 PM
     */

namespace App\Http\Controllers\Report;


use App\Http\Controllers\Controller;
use App\Month;
use App\PeticashPurchaseTransactionMonthlyExpense;
use App\PeticashSalaryTransaction;
use App\PeticashSalaryTransactionMonthlyExpense;
use App\PeticashTransactionType;
use App\ProjectSite;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillMonthlyExpense;
use App\Subcontractor;
use App\SubcontractorBillStatus;
use App\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
            $startDate = date('d/m/Y',strtotime(Carbon::now()->subDays(30)));
            $endDate = date('d/m/Y',strtotime(Carbon::now()));
            $projectSites = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                            ->select('project_sites.id','project_sites.name','projects.name as project_name')
                            ->get()->toArray();
            $approvedBillStatusId = $subcontractorBillStatus->where('slug','approved')->pluck('id');
            $subcontractorData = $subcontractor->join('subcontractor_structure','subcontractor_structure.subcontractor_id','=','subcontractor.id')
                                ->join('subcontractor_bills','subcontractor_bills.sc_structure_id','=','subcontractor_structure.id')
                                ->where('subcontractor_bills.subcontractor_bill_status_id',$approvedBillStatusId)
                                ->distinct('subcontractor.id')
                                ->select('subcontractor.id','subcontractor.company_name')->get();
            return view('report.report')->with(compact('startDate','endDate','projectSites','subcontractorData'));
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
            }

            $reportType = $request['report_name'];
            $project_site_id = $request['project_site_id'];
            return view('report.manage')->with(compact('noOfButtons','reportType','project_site_id','downloadButtonDetails'));
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

    public function downloadDetailReport(Request $request,$reportType,$project_site_id,$start_date,$end_date) {
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

            $date = date('l, d F Y',strtotime($end_date)) .' - '. date('l, d F Y',strtotime($start_date));

            $startYearID = $year->where('slug',(int)date('Y',strtotime($start_date)))->pluck('id')->first();
            $endYearID = $year->where('slug',(int)date('Y',strtotime($end_date)))->pluck('id')->first();
            $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name')->get();
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
                        ->where('purchase_order_bills.created_at','<=',$start_date)
                        ->where('purchase_order_bills.created_at','>=',$end_date)
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
                        ->where('date','<=',$start_date)
                        ->where('date','>=',$end_date)
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
                        ->where('date','<=',$start_date)
                        ->where('date','>=',$end_date)
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

}