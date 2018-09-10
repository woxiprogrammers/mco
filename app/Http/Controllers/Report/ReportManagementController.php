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
use App\ProjectSite;
use App\PurchaseOrderBill;
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
            $startDate = date('d/m/Y',strtotime(Carbon::now()->subDays(30)));
            $endDate = date('d/m/Y',strtotime(Carbon::now()));
            $projectSites = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                            ->select('project_sites.id','project_sites.name','projects.name as project_name')
                            ->get()->toArray();
            return view('report.report')->with(compact('startDate','endDate','projectSites'));
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

    public function getDetailReport(Request $request) {
        try{
            $startDate = explode('/',$request->start_date);
            $start_date = $startDate[2].'-'.$startDate[1].'-'.$startDate[0].' 00:00:00';
            $endDate = explode('/',$request->end_date);
            $end_date = $endDate[2].'-'.$endDate[1].'-'.$endDate[0].' 24:00:00';
            $globalProjectSiteId = $request['project_site_id'];
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
            $reportLimit = env('REPORT_LIMIT['.$request['report_name'].']');
            $noOfButtons = $count/$reportLimit;
            $downloadButtonDetails = array();
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
                    ->orderBy('purchase_order_bills.created_at','desc')
                    ->pluck('purchase_order_bills.created_at');

                $downloadButtonDetails[$iterator]['start_date'] = $purchaseOrderBillDates->first();
                $downloadButtonDetails[$iterator]['end_date'] = $purchaseOrderBillDates->last();

            }
            $reportType = $request['report_type'];
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

    public function getReport(Request $request,$reportType,$project_site_id,$start_date,$end_date) {
        try{
            $request['project_site_id'] = $project_site_id;
            $request['report_type'] = $reportType;
            $year = new Year();
            $month = new Month();
            $currentDate = date('d_m_Y_h_i_s',strtotime(Carbon::now()));
            $report_type = $request->report_type;

            $row = 0;
            $data = $header = array();

            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');

            $date = date('l, d F Y',strtotime($start_date)) .' - '. date('l, d F Y',strtotime($end_date));

            $startYearID = $year->where('slug',(int)date('Y',strtotime($start_date)))->pluck('id')->first();
            $endYearID = $year->where('slug',(int)date('Y',strtotime($end_date)))->pluck('id')->first();
            $totalYears = $year->whereBetween('id',[$startYearID,$endYearID])->select('id','name')->get();
            $months = $month->get();
            $iterator = 1;
            $monthlyTotal[0]['month'] = 'Month-Year';
            $monthlyTotal[0]['total'] = 'Total';
            foreach ($totalYears as $thisYear){
                foreach ($months as $month){
                    $monthlyTotal[$iterator]['month'] = $month['name'].'-'.$thisYear['name'];
                    $monthlyTotal[$iterator]['total'] = '235';
                    $iterator++;
                }
            }
            $globalProjectSiteId = $request['project_site_id'];
            switch($report_type) {

                case 'sitewise_purchase_report' :
                    $projectSite = $projectSiteId = new ProjectSite();
                    $purchaseOrderBill = new PurchaseOrderBill();
                    $data[$row] = array(
                        'Bill Entry Date', 'Bill Create Date', 'Bill No', 'Vendor Name', 'Basic Amount', 'Tax Amount',
                        'Bill Amount', 'Monthly Total'
                    );

                    $projectName = $projectSite->join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$globalProjectSiteId)->pluck('projects.name')->first();
                    $purchaseOrderBillsData = $purchaseOrderBill
                        ->join('purchase_orders','purchase_orders.id','='
                            ,'purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','='
                            ,'purchase_orders.purchase_request_id')
                        ->join('vendors','vendors.id','=','purchase_orders.vendor_id')
                        ->where('purchase_requests.project_site_id',$globalProjectSiteId)
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
                        $data[$row]['bill_entry_date'] = $purchaseOrderBillData['bill_date'];
                        $data[$row]['bill_created_date'] = $purchaseOrderBillData['created_at'];
                        $data[$row]['bill_number'] = $purchaseOrderBillData['bill_number'];
                        $data[$row]['company_name'] = $purchaseOrderBillData['company'];
                        $data[$row]['basic_amount'] = $purchaseOrderBillData['amount'];
                        $data[$row]['tax_amount'] = $purchaseOrderBillData['transportation_tax_amount'] + $purchaseOrderBillData['tax_amount'] + $purchaseOrderBillData['extra_tax_amount'];
                        $data[$row]['bill_amount'] = $data[$row]['basic_amount'] + $data[$row]['tax_amount'];
                        if($row == 1){
                            $newMonth = $thisMonth;
                            $newMonthRow = $row;
                            $data[$row]['monthly_total'] = $data[$row]['bill_amount'];
                        }else{
                            if($newMonth == $thisMonth){
                                $data[$newMonthRow]['monthly_total'] += $data[$row]['bill_amount'];
                                $data[$row]['monthly_total'] = null;
                            }else{
                                $newMonth = $thisMonth;
                                $newMonthRow = $row;
                                $data[$row]['monthly_total'] = $data[$row]['bill_amount'];
                            }
                        }
                        $row++;
                    }
                    Excel::create($report_type."_".$currentDate, function($excel) use($monthlyTotal, $data, $report_type, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                        $excel->sheet($report_type, function($sheet) use($monthlyTotal, $data, $header, $companyHeader, $date, $projectName) {
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
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData,$row,$sheet,$headerRow) {
                                        $sheet->getRowDimension($row)->setRowHeight(20);
                                        if($row == $headerRow) {
                                            $cell->setFontWeight('bold');
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
                'action' => 'Get Detail Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
                'type' => $request->report_type
            ];
            Log::critical(json_encode($data));
        }


    }
}