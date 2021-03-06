<?php

namespace App\Http\Controllers\Report;

use App\AssetMaintenanceBill;
use App\AssetMaintenanceBillPayment;
use App\BankInfo;
use App\Bill;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Employee;
use App\EmployeeType;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\BillTrait;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\InventoryTransferTypes;
use App\Material;
use App\Helper\MaterialProductHelper;
use App\PeticashSalaryTransaction;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\Product;
use App\ProductDescription;
use App\ProjectSite;
use App\ProjectSiteIndirectExpense;
use App\PurcahsePeticashTransaction;
use App\PurchaseOrder;
use App\PurchaseOrderBill;
use App\PurchaseOrderBillTransactionRelation;
use App\PurchaseOrderComponent;
use App\PurchaseOrderPayment;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionComponent;
use App\PurchaseOrderTransactionStatus;
use App\PurchaseRequest;
use App\PurchaseRequestComponent;
use App\Quotation;
use App\QuotationProduct;
use App\SiteTransferBillPayment;
use App\Subcontractor;
use App\SubcontractorBill;
use App\SubcontractorBillStatus;
use App\SubcontractorBillTransaction;
use App\SubcontractorStructure;
use App\Summary;
use App\Tax;
use App\Unit;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use BillTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function reportsRoute(Request $request) {
        try {
            $curr_date = Carbon::now()->subDays(30);
            $last_date = Carbon::now();
            $start_date = date('d/m/Y',strtotime($curr_date));
            $end_date = date('d/m/Y',strtotime($last_date));
            $sites = ProjectSite::join('projects','projects.id','=','project_sites.project_id')->select('project_sites.id','project_sites.name','project_sites.address','projects.name as project_name')->get()->toArray();
            $billIds = Bill::where('bill_status_id',BillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
            $billProjectSites = Quotation::join('bills','quotations.id','=','bills.quotation_id')
                                    ->join('project_sites','quotations.project_site_id','=','project_sites.id')
                                    ->join('projects','projects.id','=','project_sites.project_id')
                                    ->whereIn('bills.id',$billIds)
                                    ->distinct('project_sites.id')
                ->select('project_sites.id','project_sites.name','project_sites.address','projects.name as project_name')->get()->toArray();
            $categories = Category::where('is_active', true)->get(['id','name','slug'])->toArray();
            $materials = Material::get(['id','name'])->toArray();
            $subcontractors = Subcontractor::get(['id','company_name'])->toArray();
            $employeeTypeId = EmployeeType::whereIn('slug',['labour','staff'])->pluck('id')->toArray();
            $employees = Employee::whereIn('employee_type_id', $employeeTypeId)->get(['id','name','employee_id'])->toArray();
            $vendors = Vendor::get(['id','name','company'])->toArray();
            return view('report.mainreport')->with(compact('vendors','employees','subcontractors','sites','categories','start_date','end_date','materials','billProjectSites'));
        } catch(\Exception $e) {
            $data = [
                'action' => 'Get Report View',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function downloadReports(Request $request) {
        try{
            $downloadSheetFlag = true;
            $curr_date = Carbon::now();
            $curr_date = date('d_m_Y_h_i_s',strtotime($curr_date));
            $report_type = $request->report_type;
            //$start_date = $request->start_date;
            $startDate = explode('/',$request->start_date);
            $start_date = $startDate[2].'-'.$startDate[1].'-'.$startDate[0].' 00:00:00';
            $endDate = explode('/',$request->end_date);
            $end_date = $endDate[2].'-'.$endDate[1].'-'.$endDate[0].' 24:00:00';
            $row = 0;
            $data = $header = array();
            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');
            $date = date('l, d F Y',strtotime($start_date)) .' - '. date('l, d F Y',strtotime($end_date));
            switch($report_type) {
                case 'materialwise_purchase_report':
                    $header = array(
                        'Sr. No', 'Date', 'Category Name', 'Material Name', 'Quantity', 'Unit', 'Basic Amount', 'Total Tax Amount',
                        'Total Amount', 'Average Amount'
                    );
                    $purchaseOrderComponents = array();
                    if(!$request->has('material_id')){
                        $materialNames = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                            ->where('material_requests.project_site_id',$request['materialwise_purchase_report_site_id'])
                            ->whereBetween('purchase_orders.created_at', [$start_date, $end_date])
                            ->distinct('material_request_components.name')
                            ->pluck('material_request_components.name')
                            ->toArray();
                    }else{
                        $materialNames = Material::whereIn('id',$request['material_id'])->pluck('name')->toArray();
                    }
                    $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$request['materialwise_purchase_report_site_id'])->pluck('projects.name')->first();
                    foreach ($materialNames as $materialName){
                        $purchaseOrderComponents[] = PurchaseOrderComponent::join('purchase_request_components','purchase_request_components.id','=','purchase_order_components.purchase_request_component_id')
                            ->join('material_request_components','material_request_components.id','=','purchase_request_components.material_request_component_id')
                            ->join('material_requests','material_requests.id','=','material_request_components.material_request_id')
                            ->join('purchase_orders','purchase_orders.id','=','purchase_order_components.purchase_order_id')
                            ->join('purchase_order_request_components','purchase_order_request_components.id','=','purchase_order_components.purchase_order_request_component_id')
                            ->where('material_request_components.name','ilike',$materialName)
                            ->where('material_requests.project_site_id',$request['materialwise_purchase_report_site_id'])
                            ->whereBetween('purchase_orders.created_at', [$start_date, $end_date])
                            ->select('purchase_orders.id as purchase_order_id','purchase_order_components.created_at','purchase_order_components.id as purchase_order_component_id','purchase_order_components.rate_per_unit'
                                ,'purchase_order_components.unit_id','purchase_order_components.cgst_percentage','purchase_order_components.sgst_percentage','purchase_order_components.igst_percentage',
                                'material_request_components.name as material_request_component_name','purchase_order_request_components.category_id')
                            ->get()->toArray();
                    }
                    $billGeneratedPOTransactionStatusId = PurchaseOrderTransactionStatus::where('slug','bill-generated')->pluck('id')->first();
                    foreach ($purchaseOrderComponents as $key => $purchaseOrderComponentArray){
                        foreach ($purchaseOrderComponentArray as $key2 => $purchaseOrderComponent){
                            $categoryDetails = Category::where('id',$purchaseOrderComponent['category_id'])->select('id','name')->first();
                            if($request['category_id'] == 0){
                                $purchaseOrderTransactionComponents = PurchaseOrderTransactionComponent::join('purchase_order_transactions','purchase_order_transactions.id','=','purchase_order_transaction_components.purchase_order_transaction_id')
                                    ->where('purchase_order_transaction_components.purchase_order_component_id',$purchaseOrderComponent['purchase_order_component_id'])
                                    ->where('purchase_order_transactions.purchase_order_transaction_status_id',$billGeneratedPOTransactionStatusId)
                                    ->select('purchase_order_transaction_components.id as purchase_order_transaction_component_id','purchase_order_transaction_components.quantity','purchase_order_transaction_components.unit_id')
                                    ->get();

                                if(count($purchaseOrderTransactionComponents) > 0){
                                    $quantity = $taxAmount = 0;
                                    foreach ($purchaseOrderTransactionComponents as $key1 => $purchaseOrderTransactionComponent){
                                        if($purchaseOrderTransactionComponent['unit_id'] == $purchaseOrderComponent['unit_id']){
                                            $quantity += $purchaseOrderTransactionComponent['quantity'];
                                        }else{
                                            $unitConvertedQuantity = UnitHelper::unitQuantityConversion($purchaseOrderTransactionComponent['unit_id'],$purchaseOrderComponent['unit_id'],$purchaseOrderTransactionComponent['quantity']);
                                            $quantity += $unitConvertedQuantity;
                                        }
                                        $subTotal = $purchaseOrderTransactionComponent['quantity'] * $purchaseOrderComponent['rate_per_unit'];
                                        $cgstAmount = ($purchaseOrderComponent['cgst_percentage'] * $subTotal) / 100;
                                        $sgstAmount = ($purchaseOrderComponent['sgst_percentage'] * $subTotal) / 100;
                                        $igstAmount = ($purchaseOrderComponent['igst_percentage'] * $subTotal) / 100;
                                        $taxAmount += ($cgstAmount + $sgstAmount + $igstAmount);
                                    }
                                    $data[$row]['sr_no'] = $row+1;
                                    $data[$row]['created_at'] = $purchaseOrderComponent['created_at'];
                                    $data[$row]['category_name'] = $categoryDetails['name'];
                                    $data[$row]['material_name'] = $purchaseOrderComponent['material_request_component_name'];
                                    $data[$row]['quantity'] = $quantity;
                                    $data[$row]['unit_id'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                                    $data[$row]['rate'] = $purchaseOrderComponent['rate_per_unit'] * $quantity;
                                    $data[$row]['tax_amount'] = $taxAmount;
                                    $data[$row]['total_amount'] = $taxAmount + $data[$row]['rate'];
                                    $data[$row]['average_amount'] = $data[$row]['total_amount'] / $data[$row]['quantity'];
                                    $row++;
                                }
                            }elseif($request['category_id'] == $categoryDetails['id']){
                                $purchaseOrderTransactionComponents = PurchaseOrderTransactionComponent::join('purchase_order_transactions','purchase_order_transactions.id','=','purchase_order_transaction_components.purchase_order_transaction_id')
                                    ->where('purchase_order_transaction_components.purchase_order_component_id',$purchaseOrderComponent['purchase_order_component_id'])
                                    ->where('purchase_order_transactions.purchase_order_transaction_status_id',$billGeneratedPOTransactionStatusId)
                                    ->select('purchase_order_transaction_components.id as purchase_order_transaction_component_id','purchase_order_transaction_components.quantity','purchase_order_transaction_components.unit_id')
                                    ->get();
                                if(count($purchaseOrderTransactionComponents) > 0){
                                    $quantity = $taxAmount = 0;
                                    foreach ($purchaseOrderTransactionComponents as $key1 => $purchaseOrderTransactionComponent){
                                        if($purchaseOrderTransactionComponent['unit_id'] == $purchaseOrderComponent['unit_id']){
                                            $quantity += $purchaseOrderTransactionComponent['quantity'];
                                        }else{
                                            $unitConvertedQuantity = UnitHelper::unitQuantityConversion($purchaseOrderTransactionComponent['unit_id'],$purchaseOrderComponent['unit_id'],$purchaseOrderTransactionComponent['quantity']);
                                            $quantity += $unitConvertedQuantity;
                                        }
                                        $subTotal = $purchaseOrderTransactionComponent['quantity'] * $purchaseOrderComponent['rate_per_unit'];
                                        $cgstAmount = ($purchaseOrderComponent['cgst_percentage'] * $subTotal) / 100;
                                        $sgstAmount = ($purchaseOrderComponent['sgst_percentage'] * $subTotal) / 100;
                                        $igstAmount = ($purchaseOrderComponent['igst_percentage'] * $subTotal) / 100;
                                        $taxAmount += ($cgstAmount + $sgstAmount + $igstAmount);
                                    }
                                    $data[$row]['sr_no'] = $row+1;
                                    $data[$row]['created_at'] = $purchaseOrderComponent['created_at'];
                                    $data[$row]['category_name'] = $categoryDetails['name'];
                                    $data[$row]['material_name'] = $purchaseOrderComponent['material_request_component_name'];
                                    $data[$row]['quantity'] = $quantity;
                                    $data[$row]['unit_id'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                                    $data[$row]['rate'] = $purchaseOrderComponent['rate_per_unit'] * $quantity;
                                    $data[$row]['tax_amount'] = $taxAmount;
                                    $data[$row]['total_amount'] = $taxAmount + $data[$row]['rate'];
                                    $data[$row]['average_amount'] = $data[$row]['total_amount'] / $data[$row]['quantity'];
                                    $row++;
                                }
                            }
                        }
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header, $companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:J2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:J3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:J4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:J5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:J6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:J7');
                            $sheet->cell('A7', function($cell) use($projectName){
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Material wise Purchase Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:J8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $sheet->setBorder('A9:J9','thin', 'none', 'thin', 'none');

                            $sheet->row(10, $header);
                            $sheet->setBorder('A9:J19', 'thin', "D8572C");
                            $row = 10;

                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });
                                }
                                /*$row++;

                                $sheet->cell('C'.($row), function($cell) {
                                    $cell->setAlignment('center')->setValignment('center');
                                    $cell->setValue('Total');
                                });
                                $row++;*/
                            }
                        });
                    })->export('xls');
                    break;

                case 'labour_specific_report':
                    $header = array(
                        'Project Name', 'Date', 'Payment Type', 'Gross Salary', '-PT', '-PF', '-ESIC', '-TDS',
                        '-ADVANCE', 'Net Payment', 'Balance'
                    );
                    $approvedStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
                    $paymentSlug = PeticashTransactionType::where('type','PAYMENT')->select('id','slug')->get();
                    if($request['labour_specific_report_site_id'] == 'all'){
                        $projectName = "All";
                        $salaryTransactionData = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('id','asc')
                            ->get();
                    }else{
                        $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                            ->where('project_sites.id',$request['labour_specific_report_site_id'])->pluck('projects.name')->first();
                        $salaryTransactionData = PeticashSalaryTransaction::where('project_site_id',$request['labour_specific_report_site_id'])
                            ->where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('id','asc')
                            ->get();
                    }

                    $labourData = Employee::where('id',$request['labour_id'])->select('name','employee_id')->first();

                    foreach($salaryTransactionData as $key => $salaryTransaction){
                        $peticashTransactionTypeSlug = $salaryTransaction->peticashTransactionType->slug;
                        $data[$row]['project_name'] = $salaryTransaction->projectSite->project->name;
                        $data[$row]['date'] = date('d/m/y',strtotime($salaryTransaction['date']));
                        $data[$row]['payment_type'] = $salaryTransaction->peticashTransactionType->name;
                        $data[$row]['gross_salary'] = ($peticashTransactionTypeSlug == 'salary') ? ($salaryTransaction->employee->per_day_wages * $salaryTransaction['days']) : 0;
                        $data[$row]['pt'] = ($salaryTransaction['pt'] != 0) ? -$salaryTransaction['pt'] : $salaryTransaction['pt'];
                        $data[$row]['pf'] = ($salaryTransaction['pf'] != 0) ? -$salaryTransaction['pf'] : $salaryTransaction['pf'];
                        $data[$row]['esic'] = ($salaryTransaction['esic'] != 0) ? -$salaryTransaction['esic'] : $salaryTransaction['esic'];
                        $data[$row]['tds'] = ($salaryTransaction['tds'] != 0) ? -$salaryTransaction['tds'] : $salaryTransaction['tds'];
                        $data[$row]['advance'] = ($peticashTransactionTypeSlug == 'salary') ? 0 : $salaryTransaction['amount'];
                        $data[$row]['net_payment'] = ($peticashTransactionTypeSlug == 'salary') ? $salaryTransaction['payable_amount'] : $salaryTransaction['amount'];
                        $lastSalaryTransactionId = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                            ->where('project_site_id',$salaryTransaction['project_site_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->where('id','<',$salaryTransaction['id'])
                            ->where('peticash_transaction_type_id',$paymentSlug->where('slug','salary')->pluck('id')->first())
                            ->orderBy('id','desc')
                            ->pluck('id')->first();

                        if($peticashTransactionTypeSlug == 'salary'){
                            if($lastSalaryTransactionId == null){
                                $advancesAfterLastSalary = 0;
                            }else{
                                $advancesAfterLastSalary = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                                    ->where('project_site_id',$salaryTransaction['project_site_id'])
                                    ->where('peticash_status_id',$approvedStatusId)
                                    ->where('peticash_transaction_type_id',$paymentSlug->where('slug','advance')->pluck('id')->first())
                                    ->where('id','>',$lastSalaryTransactionId)
                                    ->where('id','<=',$salaryTransaction['id'])
                                    ->orderBy('id','desc')
                                    ->sum('amount');
                            }
                            $balance = $data[$row]['gross_salary'] - $salaryTransaction['pt'] - $salaryTransaction['pf'] - $salaryTransaction['esic'] - $salaryTransaction['tds'] - $advancesAfterLastSalary;
                            $data[$row]['balance'] = ($balance > 0) ? 0 : $balance;
                        }else{
                            if($lastSalaryTransactionId == null){
                                $data[$row]['balance'] = -$salaryTransaction['amount'];
                            }else{
                                $advancesAfterLastSalary = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                                    ->where('project_site_id',$salaryTransaction['project_site_id'])
                                    ->where('peticash_status_id',$approvedStatusId)
                                    ->where('peticash_transaction_type_id',$paymentSlug->where('slug','advance')->pluck('id')->first())
                                    ->where('id','>',$lastSalaryTransactionId)
                                    ->where('id','<=',$salaryTransaction['id'])
                                    ->orderBy('id','desc')
                                    ->sum('amount');
                                $data[$row]['balance'] = -$advancesAfterLastSalary;
                            }
                        }
                        $row ++;
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $companyHeader, $date ,$labourData, $projectName) {

                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header, $companyHeader, $date, $labourData, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:K2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:K3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:K4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:K5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:K6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:K7');
                            $sheet->cell('A7', function($cell) use($labourData, $projectName) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Labour & Staff Report - ( '.$labourData['employee_id'].' - '.ucwords($labourData['name']).' - '.$projectName.' )');
                            });

                            $sheet->mergeCells('A8:K8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $sheet->setBorder('A9:K9','thin', 'none', 'thin', 'none');

                            $sheet->row(10, $header);
                            $sheet->setBorder('A9:K19', 'thin', "D8572C");
                            $row = 10;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;

                case 'subcontractor_report':
                    $header = array(
                        'Date', 'Summary Type', 'Bill No', 'Basic amount', 'Total tax', 'Total Bill Amount', 'Advance' ,'Debit', 'Hold', 'Retention',
                        'TDS', 'Other Recovery', 'Payable Amount', 'Check amount', 'Balance'
                    );
                    $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$request['subcontractor_report_site_id'])->pluck('projects.name')->first();
                    $totalAdvanceAmount = Subcontractor::where('id',$request['subcontractor_id'])->pluck('total_advance_amount')->first();
                    $subContractorBillTransactionList = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                                                        ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                                                        ->whereBetween('subcontractor_bill_transactions.created_at',[$start_date, $end_date])
                                                        ->where('subcontractor_bills.subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())
                                                        ->where('subcontractor_structure.subcontractor_id',$request['subcontractor_id'])
                                                        ->where('subcontractor_structure.project_site_id',$request['subcontractor_report_site_id'])
                                                        ->orderBy('subcontractor_bills.created_at')
                                                        ->select('subcontractor_structure.summary_id','subcontractor_bill_transactions.id as subcontractor_bill_transaction_id','subcontractor_bill_transactions.subcontractor_bills_id as subcontractor_bill_id','subcontractor_bill_transactions.subtotal','subcontractor_bill_transactions.total','subcontractor_bill_transactions.debit','subcontractor_bill_transactions.hold',
                                                            'subcontractor_bill_transactions.retention_percent','subcontractor_bill_transactions.retention_amount','subcontractor_bill_transactions.tds_percent','subcontractor_bill_transactions.tds_amount','subcontractor_bill_transactions.other_recovery','subcontractor_bill_transactions.created_at','subcontractor_bill_transactions.is_advance')->get();

                    $subcontractorCompanyName = Subcontractor::where('id',$request['subcontractor_id'])->pluck('company_name')->first();
                    $subContractorBillTransactions = $subContractorBillTransactionList->groupBy('subcontractor_bill_id')->toArray();
                    foreach ($subContractorBillTransactions as $subcontractorBillId => $subContractorBillTransactionData){
                        $subcontractorBill = SubcontractorBill::where('id',$subcontractorBillId)->first();
                        $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                        $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                        $totalBills = $subcontractorStructure->subcontractorBill->sortBy('id')->pluck('id');
                        $billNo = 1;
                        $taxTotal = 0;
                        $billName = '-';
                        foreach($totalBills as $billId){
                            $status = SubcontractorBill::join('subcontractor_bill_status','subcontractor_bill_status.id','=','subcontractor_bills.subcontractor_bill_status_id')
                                ->where('subcontractor_bills.id',$billId)->pluck('subcontractor_bill_status.slug')->first();
                            if($status != 'disapproved'){
                                if($billId == $subcontractorBillId){
                                    $billName = "R.A. ".$billNo;
                                    break;
                                }
                            }
                            $billNo++;
                        }
                        $structureSlug = $subcontractorStructure->contractType->slug;
                        if($structureSlug == 'sqft'){
                            $rate = $subcontractorStructure['rate'];
                            $subTotal = $subcontractorBill['qty'] * $rate;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                            }
                            $finalTotal = $subTotal + $taxTotal;
                        }else{
                            $rate = $subcontractorStructure['rate'] * $subcontractorStructure['total_work_area'];
                            $subTotal = $subcontractorBill['qty'] * $rate;
                            foreach($subcontractorBillTaxes as $key => $subcontractorBillTaxData){
                                $taxTotal += ($subcontractorBillTaxData['percentage'] * $subTotal) / 100;
                            }
                            $finalTotal = $subTotal + $taxTotal;
                        }
                        foreach ($subContractorBillTransactionData as $key =>$subContractorBillTransaction){
                            $totalAdvancedTillBill = SubcontractorBillTransaction::where('id','<=',$subContractorBillTransaction['subcontractor_bill_transaction_id'])->where('is_advance',true)->sum('total');

                            $data[$row]['date'] = date('d/m/y',strtotime($subContractorBillTransaction['created_at']));
                            $data[$row]['summary_type'] = Summary::where('id',$subContractorBillTransaction['summary_id'])->pluck('name')->first();
                            $data[$row]['bill_no'] = $billName;
                            $data[$row]['basic_amount'] = $rate;
                            $data[$row]['total_tax'] = $taxTotal;
                            $data[$row]['total_bill_amount'] = $finalTotal;
                            if($subContractorBillTransaction['is_advance'] == true){
                                $data[$row]['advance'] = $subContractorBillTransaction['total'];
                            }else{
                                $data[$row]['advance'] = 0;
                            }
                            $data[$row]['debit'] = (-$subContractorBillTransaction['debit'] !=0 ) ? -$subContractorBillTransaction['debit'] : $subContractorBillTransaction['debit'];
                            $data[$row]['hold'] = ($subContractorBillTransaction['hold'] != 0) ? -$subContractorBillTransaction['hold'] : $subContractorBillTransaction['hold'];
                            $data[$row]['retention'] = ($subContractorBillTransaction['retention_amount'] != 0) ? -$subContractorBillTransaction['retention_amount'] : $subContractorBillTransaction['retention_amount'];
                            $data[$row]['tds'] = ($subContractorBillTransaction['tds_amount'] != 0) ? -$subContractorBillTransaction['tds_amount'] : $subContractorBillTransaction['tds_amount'];
                            $data[$row]['other_recovery'] = $subContractorBillTransaction['other_recovery'];

                            $paidAmount = SubcontractorBillTransaction::where('id','<',$subContractorBillTransaction['subcontractor_bill_transaction_id'])->sum('total');
                            $data[$row]['payable_amount'] = $finalTotal - $paidAmount;
                            if($subContractorBillTransaction['is_advance'] == true){
                                $data[$row]['check_amount'] = 0;
                            }else{
                                $data[$row]['check_amount'] = $subContractorBillTransaction['total'];
                            }
                            $data[$row]['balance'] = $totalAdvanceAmount - $totalAdvancedTillBill;

                            $row++;
                        }
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $companyHeader, $date, $subcontractorCompanyName, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header, $companyHeader, $date, $subcontractorCompanyName,$projectName ) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:O2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:O3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:O4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:O5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:O6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:O7');
                            $sheet->cell('A7', function($cell) use($subcontractorCompanyName, $projectName) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Subcontractor Report - ( '.$subcontractorCompanyName.' - '.$projectName.' )');
                            });

                            $sheet->mergeCells('A8:O8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $sheet->setBorder('A9:O9','thin', 'none', 'thin', 'none');

                            $sheet->row(10, $header);
                            $sheet->setBorder('A9:O19', 'thin', "D8572C");
                            $row = 10;

                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });
                                }
                                /*$row++;

                                $sheet->cell('C'.($row), function($cell) {
                                    $cell->setAlignment('center')->setValignment('center');
                                    $cell->setValue('Total');
                                });
                                $row++;*/
                            }
                        });
                    })->export('xls');

                    break;

                case 'sales_bill_tax_report':
                    $header = array(
                        'Date', 'RA Bill Number', 'Basic Amount', 'Tax Amount', 'Total Amount',
                        'Mobilise Advance', 'Debit', 'Hold', 'Retention',
                        'TDS', 'Other Recovery', 'Payable Amount', 'Check Amount',
                        'Balance'
                    );
                    $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                                                ->where('project_sites.id',$request['sales_bill_tax_report_site_id'])->pluck('projects.name')->first();
                    $quotationId = Quotation::where('project_site_id',$request['sales_bill_tax_report_site_id'])->pluck('id')->first();
                    $billTransactionsList = BillTransaction::join('bills','bills.id','=','bill_transactions.bill_id')
                                            ->whereBetween('bill_transactions.created_at',[$start_date, $end_date])
                                            ->where('bills.quotation_id',$quotationId)
                                            ->where('bills.bill_status_id',BillStatus::where('slug','approved')->pluck('id')->first())
                                            ->orderBy('bills.created_at')
                                            ->select('bill_transactions.id as bill_transaction_id','bill_transactions.bill_id as bill_id','bill_transactions.total','bill_transactions.remark','bill_transactions.debit','bill_transactions.hold','bill_transactions.paid_from_advanced','bill_transactions.retention_percent','bill_transactions.retention_amount',
                                                'bill_transactions.tds_percent','bill_transactions.tds_amount','bill_transactions.amount','bill_transactions.other_recovery_value','bill_transactions.created_at')->get();
                    $billTransactions = $billTransactionsList->groupBy('bill_id')->toArray();

                    $statusId = BillStatus::whereIn('slug',['approved','draft'])->get()->toArray();
                    $totalBillIds = Bill::where('quotation_id',$quotationId)->whereIn('bill_status_id',array_column($statusId,'id'))->orderBy('id')->pluck('id');
                    $total['mobilise'] = $total['debit'] = $total['hold'] = $total['retention'] = $total['tds'] = $total['otherRecovery'] = $total['payableAmount'] = $total['checkAmount'] = $total['balance'] = 0;
                    foreach ($billTransactions as $billId => $billTransactionData){
                        $billNo = 1;
                        $billName = '-';
                        foreach($totalBillIds as $thisBillId){
                            if($thisBillId == $billId){
                                $billName = "R.A. ".$billNo;
                                break;
                            }
                            $billNo++;
                        }
                        $billData = $this->getBillData($billId);

                        foreach($billTransactionData as $billTransaction){
                            $data[$row]['date'] = date('d/m/y',strtotime($billTransaction['created_at']));;;
                            $data[$row]['bill_number'] = $billName;
                            $data[$row]['basic_amount'] = $billData['basic_amount'];
                            $data[$row]['tax_amount'] = $billData['tax_amount'];
                            $data[$row]['total_amount'] = $billData['total_amount_with_tax'];
                            $data[$row]['mobilise'] = ($billTransaction['paid_from_advanced'] == true) ? $billTransaction['amount'] : 0;
                            $data[$row]['debit'] = -$billTransaction['debit'];
                            $data[$row]['hold'] = -$billTransaction['hold'];
                            $data[$row]['retention'] = -$billTransaction['retention_amount'];
                            $data[$row]['tds'] = -$billTransaction['tds_amount'];
                            $data[$row]['other_recovery'] = $billTransaction['other_recovery_value'];
                            $data[$row]['payable_amount'] = $data[$row]['total_amount'] + $data[$row]['mobilise'] + $data[$row]['debit'] + $data[$row]['hold'] + $data[$row]['retention'] + $data[$row]['tds'] + $data[$row]['other_recovery'];
                            $data[$row]['check_amount'] = ($billTransaction['paid_from_advanced'] == false) ? $billTransaction['amount'] : 0;
                            $data[$row]['balance'] = $data[$row]['payable_amount'] - $data[$row]['check_amount'] - $data[$row]['mobilise'];
                            $total['mobilise'] += $data[$row]['mobilise'];
                            $total['debit'] += $data[$row]['debit'];
                            $total['hold'] += $data[$row]['hold'];
                            $total['retention'] += $data[$row]['retention'];
                            $total['tds'] += $data[$row]['tds'];
                            $total['otherRecovery'] += $data[$row]['other_recovery'];
                            $total['balance'] = $data[$row]['balance'];
                            $total['checkAmount'] += $data[$row]['check_amount'];

                            $row++;
                        }
                        $total['payableAmount'] += $billData['total_amount_with_tax'];

                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header,$companyHeader ,$date , $projectName, $total) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header,$companyHeader, $date, $projectName, $total) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:N2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:N3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:N4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:N5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:N6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:N7');
                            $sheet->cell('A7', function($cell) use($projectName) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Sales Bill Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:N8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $sheet->setBorder('A9:N9','thin', 'none', 'thin', 'none');

                            $sheet->row(10, $header);
                            $sheet->setBorder('A9:N19', 'thin', "D8572C");
                            $row = 10;

                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });
                                }
                            }
                            $row++;
                            if($row > 11){
                                $sheet->row($row, ['','','','','',$total['mobilise'], $total['debit'], $total['hold'] , $total['retention'] , $total['tds'] , $total['otherRecovery'] , $total['payableAmount'] , $total['checkAmount'] , $total['balance']]);
                                $sheet->setBorder('A9:N19', 'thin', "D8572C");
                            }

                        });
                    })->export('xls');

                    break;

                case 'purchase_bill_tax_report':
                    $header = array(
                        'Date', 'Bill Number', 'Basic Amount', 'IGST Amount', 'SGST Amount', 'CGST Amount',
                        'Extra Amount Tax', 'With Tax Amount', 'Paid Amount', 'Balance'
                    );
                    $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                        ->where('project_sites.id',$request['purchase_bill_tax_report_site_id'])->pluck('projects.name')->first();
                    $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                                                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                    ->where('purchase_requests.project_site_id',$request['purchase_bill_tax_report_site_id'])
                                                    ->whereBetween('purchase_order_payments.created_at',[$start_date, $end_date])
                                                    ->where('purchase_orders.vendor_id',$request['vendor_id'])
                        ->select('purchase_order_payments.id as purchase_order_payment_id','purchase_order_payments.purchase_order_bill_id','purchase_order_payments.payment_id'
                                                        ,'purchase_order_payments.amount','purchase_order_payments.reference_number','purchase_order_payments.is_advance'
                                                        ,'purchase_order_payments.created_at','purchase_order_bills.purchase_order_id as purchase_order_id','purchase_order_bills.amount as bill_amount','purchase_order_bills.tax_amount'
                                                        ,'purchase_order_bills.bill_number','purchase_order_bills.extra_amount','purchase_order_bills.transportation_tax_amount','purchase_order_bills.extra_tax_amount','purchase_order_bills.transportation_total_amount')->get()->toArray();
                    $total['basicAmount'] = $total['igstAmount'] = $total['sgstAmount'] = $total['cgstAmount'] = $total['paidAmount'] = $total['amount'] = $total['amountWithTax'] = $total['balance'] = 0;
                    foreach($purchaseOrderBillPayments as $key => $purchaseOrderBillPayment){
                        $transactionIds = PurchaseOrderBillTransactionRelation::where('purchase_order_bill_id',$purchaseOrderBillPayment['purchase_order_bill_id'])->pluck('purchase_order_transaction_id');
                        $purchaseOrderTransactions = PurchaseOrderTransaction::whereIn('id',$transactionIds)->get();
                        $cgstAmount = $sgstAmount = $igstAmount = $extraTaxAmount = 0;
                        $transporationAmount = $purchaseOrderBillPayment['transportation_total_amount'];
                        foreach($purchaseOrderTransactions as $purchaseOrderTransaction){
                            foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                                $transporationCgstAmount = $transporationSgstAmount = $transporationIgstAmount = 0;
                                $thisCgstAmount = $thisSgstAmount = $thisIgstAmount = 0;
                                $purchaseOrderComponent = $purchaseOrderTransactionComponent->purchaseOrderComponent;
                                $purchaseRequestComponent = $purchaseOrderComponent->purchaseOrderRequestComponent;
                                $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                                if($transporationAmount != null){
                                    $transporationCgstAmount =  $transporationAmount * ($purchaseRequestComponent->transportation_cgst_percentage / 100);
                                    $transporationSgstAmount =  $transporationAmount * ($purchaseRequestComponent->transportation_sgst_percentage / 100);
                                    $transporationIgstAmount =  $transporationAmount * ($purchaseRequestComponent->transportation_igst_percentage / 100);
                                }
                                if(!is_array($unitConversionRate)){
                                    $tempAmount = $purchaseOrderTransactionComponent->quantity * $unitConversionRate;

                                    if($purchaseOrderComponent->cgst_percentage != null || $purchaseOrderComponent->cgst_percentage != ''){
                                        $thisCgstAmount = $tempAmount * ($purchaseOrderComponent->cgst_percentage/100);
                                    }
                                    if($purchaseOrderComponent->sgst_percentage != null || $purchaseOrderComponent->sgst_percentage != ''){
                                        $thisSgstAmount = $tempAmount * ($purchaseOrderComponent->sgst_percentage/100);
                                    }
                                    if($purchaseOrderComponent->igst_percentage != null || $purchaseOrderComponent->igst_percentage != ''){
                                        $thisIgstAmount = $tempAmount * ($purchaseOrderComponent->igst_percentage/100);
                                    }
                                }
                                $cgstAmount += ($thisCgstAmount + $transporationCgstAmount);
                                $sgstAmount += ($thisSgstAmount + $transporationSgstAmount);
                                $igstAmount += ($thisIgstAmount + $transporationIgstAmount);
                            }
                        }

                        $taxAmount = $purchaseOrderBillPayment['transportation_tax_amount'] + $purchaseOrderBillPayment['extra_tax_amount'] + $purchaseOrderBillPayment['tax_amount'];

                        $data[$row]['date'] = $purchaseOrderBillPayment['created_at'];
                        $data[$row]['bill_number'] = $purchaseOrderBillPayment['bill_number'];
                        $data[$row]['basic_amount'] = $purchaseOrderBillPayment['bill_amount'] - $taxAmount;
                        $data[$row]['igst_amount'] = $igstAmount;
                        $data[$row]['sgst_amount'] = $sgstAmount;
                        $data[$row]['cgst_amount'] = $cgstAmount;
                        $data[$row]['extra_amount_tax'] = ($purchaseOrderBillPayment['extra_amount'] > 0) ? $purchaseOrderBillPayment['extra_tax_amount'] : 0;
                        $data[$row]['amount_with_tax'] = $purchaseOrderBillPayment['bill_amount'];
                        $data[$row]['paid_amount'] = $purchaseOrderBillPayment['amount'];
                        $data[$row]['balance'] = $data[$row]['amount_with_tax'] - $data[$row]['paid_amount'];
                        $total['basicAmount'] += $data[$row]['basic_amount'];
                        $total['igstAmount'] += $data[$row]['igst_amount'];
                        $total['sgstAmount'] += $data[$row]['sgst_amount'];
                        $total['cgstAmount'] += $data[$row]['cgst_amount'];
                        $total['amountWithTax'] += $data[$row]['amount_with_tax'];
                        $total['paidAmount'] += $data[$row]['paid_amount'];
                        $total['balance'] += $data[$row]['balance'];
                        $row++;
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $total, $companyHeader, $date, $projectName) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header, $total,$companyHeader, $date, $projectName) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:J2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:J3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:J4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:J5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:J6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:J7');
                            $sheet->cell('A7', function($cell) use ($projectName){
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Purchase Bill Report - '.$projectName);
                            });

                            $sheet->mergeCells('A8:J8');
                            $sheet->cell('A8', function($cell) use($date) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($date);
                            });

                            $sheet->setBorder('A9:J9','thin', 'none', 'thin', 'none');

                            $sheet->row(10, $header);
                            $sheet->setBorder('A10:J20', 'thin', "D8572C");
                            $row = 10;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });

                                }
                            }
                            /*if($row > 2){
                                $sheet->row($row, array('','Total',$total['basicAmount'],$total['igstAmount'],$total['sgstAmount'],$total['cgstAmount'],$total['amountWithTax'],$total['paidAmount'],$total['balance']));
                            }*/
                        });
                    })->export('xls');
                break;

                case 'receiptwise_p_and_l_report':
                    $projectSiteId = $request['receiptwise_p_and_l_report_site_id'];
                    $bankData = BankInfo::orderBy('bank_name','asc')->select('id','bank_name')->get()->toArray();
                    $banks = array_column($bankData,'bank_name');
                    $bankIds = array_column($bankData,'id');
                    $totalSalesEntry = $this->getTotalSalesEntry($projectSiteId);
                    $subcontractor = $this->getSubcontractorBillPaidAmount($projectSiteId,$bankIds);
                    $indirectExpensesAmount = $this->getIndirectExpensesAmount($projectSiteId,$bankIds);
                    $miscellaneousPurchaseAmount = $this->getPeticashPurchaseAmount($projectSiteId,$bankIds);
                    $totalReceiptEntry = $this->getBillTransactionsAmount($projectSiteId,$bankIds);
                    $purchasePaidAmount = $this->getPurchasePaidAmount($projectSiteId,$bankIds);
                    $assetRentAmount = $this->getAssetRentPaidAmount($projectSiteId,$bankIds);
                    $assetMaintenancePaidAmount = $this->getAssetMaintenancePaidAmount($projectSiteId,$bankIds);
                    $peticashSalaryAmount = $this->getPeticashSalaryAmount($projectSiteId,$bankIds);
                    $siteTransferAmount = $this->getSiteTransferAmount($projectSiteId,$bankIds);
                    $quotationOpeningExpenseAmount = $this->getQuotationOpeningExpenseAmount($projectSiteId,$bankIds);
                    $totalPurchase = $total = array();
                    for($iterator = 0; $iterator < (count($bankIds) + 2) ; $iterator++){
                        $totalPurchase[$iterator] = $purchasePaidAmount[$iterator] + $assetMaintenancePaidAmount[$iterator] + $assetRentAmount[$iterator] + $siteTransferAmount[$iterator] + $quotationOpeningExpenseAmount[$iterator];
                        $total[$iterator] = $totalPurchase[$iterator] + $miscellaneousPurchaseAmount[$iterator] + $subcontractor[$iterator] + $indirectExpensesAmount[$iterator] + $peticashSalaryAmount[$iterator];
                    }
                    $profitLossSaleWise = $totalSalesEntry - $total[0];
                    $profitLossReceiptWise = $totalReceiptEntry[0] - $total[0];
                    if($projectSiteId == 'all'){
                        $projectName = 'All';
                    }else{
                        $projectName = ProjectSite::join('projects','projects.id','=','project_sites.project_id')
                            ->where('project_sites.id',$projectSiteId)->pluck('projects.name')->first();
                    }
                    $data = array(
                        array_merge(array('Total Sale Entry', 'Total Receipt Entry'),$banks ,array( 'Expenses on', 'Total expense'), $banks , array('Expenses on Cash')),
                        array_merge(array($totalSalesEntry), $totalReceiptEntry , array('Total purchase' ), $totalPurchase),
                        array_merge(array_fill(0,(count($banks) + 2),null),array( 'Total miscellaneous purchase' ), $miscellaneousPurchaseAmount),
                        array_merge(array_fill(0,(count($banks) + 2),null),array('Subcontractor' ), $subcontractor),
                        array_merge(array_fill(0,(count($banks) + 2),null),array( 'Salary' ), $peticashSalaryAmount),
                        array_merge(array_fill(0,(count($banks) + 2),null),array( 'Indirect Expenses(GST,TDS Paid to government from Manisha)' ), $indirectExpensesAmount),
                        array(null, null),
                        array_merge(array($totalSalesEntry) , $totalReceiptEntry , array_fill(0,(count($banks) - 1),null), $total),
                        array(null, null),
                        array('Profit/ Loss Sale wise', 'Profit/ Loss Receipt wise'),
                        array($profitLossSaleWise, $profitLossReceiptWise),
                    );
                    $alphabets = array('A','B','C','D','E','F','G','H','I','J','K', 'L','M','N','O','P','Q','R','S','T','U','V','W','X ','Y','Z');
                    $totalColumns = (count($bankIds) * 2 ) + 4;
                    $excelLastColumn = $alphabets[$totalColumns];
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $companyHeader, $projectName, $excelLastColumn) {
                        $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12)->setBold(true);
                        $excel->sheet($report_type, function($sheet) use($data, $header, $companyHeader, $projectName, $excelLastColumn) {
                            $objDrawing = new \PHPExcel_Worksheet_Drawing();
                            $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                            $objDrawing->setWidthAndHeight(148,74);
                            $objDrawing->setResizeProportional(true);
                            $objDrawing->setCoordinates('A1');
                            $objDrawing->setWorksheet($sheet);

                            $sheet->mergeCells('A2:'.$excelLastColumn.'2');
                            $sheet->cell('A2', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['company_name']);
                            });

                            $sheet->mergeCells('A3:'.$excelLastColumn.'3');
                            $sheet->cell('A3', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['designation']);
                            });

                            $sheet->mergeCells('A4:'.$excelLastColumn.'4');
                            $sheet->cell('A4', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['address']);
                            });

                            $sheet->mergeCells('A5:'.$excelLastColumn.'5');
                            $sheet->cell('A5', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['contact_no']);
                            });

                            $sheet->mergeCells('A6:'.$excelLastColumn.'6');
                            $sheet->cell('A6', function($cell) use($companyHeader) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue($companyHeader['gstin_number']);
                            });

                            $sheet->mergeCells('A7:'.$excelLastColumn.'7');
                            $sheet->cell('A7', function($cell) use($projectName) {
                                $cell->setAlignment('center')->setValignment('center');
                                $cell->setValue('Profit and Loss Report - '.$projectName);
                            });
                            $sheet->setBorder('A8:'.$excelLastColumn.'8','thin', 'none', 'thin', 'none');

                            $sheet->setBorder('A9:'.$excelLastColumn.'19', 'thin', "D8572C");
                            $row = 8;
                            foreach($data as $key => $rowData){
                                $next_column = 'A';
                                $row++;
                                foreach($rowData as $key1 => $cellData){
                                    $current_column = $next_column++;
                                    $sheet->cell($current_column.($row), function($cell) use($cellData) {
                                        $cell->setAlignment('center')->setValignment('center');
                                        $cell->setValue($cellData);
                                    });
                                }
                            }
                        });
                    })->export('xls');
                    break;
                    

                default :
                    $downloadSheetFlag = false;
                    break;
            }


            //"=SUM($columnForTotal$rowForDiscountSubtotal:$columnForTotal$beforeTotalRowNumber)"
        }catch(\Exception $e){
            $data = [
                'action' => 'Download Report',
                'exception' => $e->getMessage(),
                'params' => $request->all(),
                'type' => $request->report_type
            ];
            Log::critical(json_encode($data));
        }


    }


    public function getBillData($billId){
        try{
            $bill = Bill::where('id',$billId)->first();
            $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
            $bills = Bill::where('quotation_id',$bill['quotation_id'])->where('bill_status_id','!=',$cancelBillStatusId)->orderBy('created_at','asc')->get()->toArray();
            $billQuotationProducts = BillQuotationProducts::where('bill_id',$bill['id'])->get()->toArray();
            $total['previous_bill_amount'] = $total['current_bill_subtotal'] = $total['cumulative_bill_amount'] = $total_extra_item =  0;
            for($iterator = 0 ; $iterator < count($billQuotationProducts) ; $iterator++){
                $billQuotationProducts[$iterator]['previous_quantity'] = 0;
                $billQuotationProducts[$iterator]['quotationProducts'] = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->where('quotation_id',$bill['quotation_id'])->first();
                $billQuotationProducts[$iterator]['productDetail'] = Product::where('id',$billQuotationProducts[$iterator]['quotationProducts']['product_id'])->first();
                $billQuotationProducts[$iterator]['product_description'] = ProductDescription::where('id',$billQuotationProducts[$iterator]['product_description_id'])->where('quotation_id',$bill['quotation_id'])->first();
                $billQuotationProducts[$iterator]['unit'] = Unit::where('id',$billQuotationProducts[$iterator]['productDetail']['unit_id'])->pluck('name')->first();
                $quotation_id = Bill::where('id',$billQuotationProducts[$iterator]['bill_id'])->pluck('quotation_id')->first();
                $discount = Quotation::where('id',$quotation_id)->pluck('discount')->first();
                $rate_per_unit = QuotationProduct::where('id',$billQuotationProducts[$iterator]['quotation_product_id'])->pluck('rate_per_unit')->first();
                $billQuotationProducts[$iterator]['rate'] = MaterialProductHelper::customRound(($rate_per_unit - ($rate_per_unit * ($discount / 100))),3);
                $billQuotationProducts[$iterator]['current_bill_subtotal'] = MaterialProductHelper::customRound(($billQuotationProducts[$iterator]['quantity'] * $billQuotationProducts[$iterator]['rate']),3);
                $billWithoutCancelStatus = Bill::where('id','<',$bill['id'])->where('bill_status_id','!=',$cancelBillStatusId)->pluck('id')->toArray();
                $previousBills = BillQuotationProducts::whereIn('bill_id',$billWithoutCancelStatus)->get();
                foreach($previousBills as $key => $previousBill){
                    if($billQuotationProducts[$iterator]['quotation_product_id'] == $previousBill['quotation_product_id']){
                        $billQuotationProducts[$iterator]['previous_quantity'] = $billQuotationProducts[$iterator]['previous_quantity'] +  $previousBill['quantity'];
                    }
                }
                $billQuotationProducts[$iterator]['cumulative_quantity'] = MaterialProductHelper::customRound(($billQuotationProducts[$iterator]['quantity'] + $billQuotationProducts[$iterator]['previous_quantity']),3);
                $total['current_bill_subtotal'] = MaterialProductHelper::customRound(($total['current_bill_subtotal'] + $billQuotationProducts[$iterator]['current_bill_subtotal']),3);
            }
            $extraItems = BillQuotationExtraItem::where('bill_id',$bill->id)->get();
            if(count($extraItems) > 0){
                $total_extra_item = 0;
                foreach($extraItems as $key => $extraItem){
                    $extraItem['previous_rate'] = BillQuotationExtraItem::whereIn('bill_id',array_column($bills,'id'))->where('bill_id','!=',$bill->id)->where('quotation_extra_item_id',$extraItem->quotation_extra_item_id)->sum('rate');
                    $total_extra_item = $total_extra_item + $extraItem['rate'];
                }
                $total['current_bill_subtotal'] = MaterialProductHelper::customRound(($total['current_bill_subtotal'] + $total_extra_item),3);
            }
            $total_rounded['current_bill_subtotal'] = MaterialProductHelper::customRound($total['current_bill_subtotal']);
            $final['current_bill_amount'] = $total_rounded['current_bill_amount'] =$total['current_bill_amount'] = MaterialProductHelper::customRound(($total['current_bill_subtotal'] - $bill['discount_amount']),3);
            $billTaxes = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
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
                $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount'] = MaterialProductHelper::customRound($total['current_bill_amount'] * ($taxes[$billTaxes[$j]['tax_id']]['percentage'] / 100) , 3);
                $final['current_bill_amount'] = MaterialProductHelper::customRound(($final['current_bill_amount'] + $taxes[$billTaxes[$j]['tax_id']]['current_bill_amount']),3);
            }
            $specialTaxes= BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
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
                    $specialTaxes[$j]['current_bill_amount'] = MaterialProductHelper::customRound($specialTaxAmount , 3);
                    $final['current_bill_gross_total_amount'] = round(($final['current_bill_amount'] + $specialTaxAmount));
                }
            }else{
                $final['current_bill_gross_total_amount'] = round($final['current_bill_amount']);
            }
            $billData['basic_amount'] = $total_rounded['current_bill_amount'];
            $billData['total_amount_with_tax'] = $final['current_bill_gross_total_amount'];
            $billData['tax_amount'] = $final['current_bill_gross_total_amount'] - $total_rounded['current_bill_amount'];
            return $billData;
        }catch(\Exception $e){
            $data = [
                'action' => 'get bill data for report',
                'params' => $billId,
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getTotalSalesEntry($projectSiteId){
        try{
            $billData = $currentTaxes = array();
            $i = 0;
            if($projectSiteId == 'all'){
                $quotations = Quotation::get();
            }else{
                $quotations = Quotation::where('project_site_id',$projectSiteId)->get();
            }
            $saleBillTotal = 0;
            foreach($quotations as $key4 => $quotation){
                $allBills = Bill::where('quotation_id',$quotation->id)->get();
                $statusId = BillStatus::where('slug','approved')->pluck('id')->first();
                $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->orderBy('created_at','asc')->get();
                $taxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                    ->where('taxes.is_special', false)
                    ->distinct('bill_taxes.tax_id')
                    ->orderBy('bill_taxes.tax_id')
                    ->pluck('bill_taxes.tax_id')
                    ->toArray();
                $specialTaxesAppliedToBills = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                    ->whereIn('bill_taxes.bill_id',array_column($allBills->toArray(),'id'))
                    ->where('taxes.is_special', true)
                    ->distinct('bill_taxes.tax_id')
                    ->orderBy('bill_taxes.tax_id')
                    ->pluck('bill_taxes.tax_id')
                    ->toArray();
                foreach($bills as $key => $bill){
                    $total_amount = 0;
                    foreach($bill->bill_quotation_product as $key1 => $product){
                        $rate = MaterialProductHelper::customRound(($product->quotation_products->rate_per_unit - ($product->quotation_products->rate_per_unit * ($product->quotation_products->quotation->discount / 100))),3);
                        $total_amount = $total_amount + ($product->quantity * $rate) ;
                    }
                    if(count($bill->bill_quotation_extraItems) > 0){
                        $extraItemsTotal = $bill->bill_quotation_extraItems->sum('rate');
                    }else{
                        $extraItemsTotal = 0;
                    }
                    $total_amount = ($total_amount + $extraItemsTotal) - $bill->discount_amount;
                    $billData['subTotal'] = $total_amount;
                    $thisBillTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', false)
                        ->pluck('bill_taxes.tax_id')
                        ->toArray();
                    $otherTaxes = array_values(array_diff($taxesAppliedToBills,$thisBillTax));
                    if($thisBillTax != null){
                        $currentTaxes = Tax::whereIn('id',$otherTaxes)->where('is_active',true)->where('is_special', false)->select('id as tax_id','name')->get();
                    }
                    if($currentTaxes != null){
                        $thisBillTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('bill_taxes.bill_id',$bill->id)
                            ->where('taxes.is_special', false)
                            ->select('bill_taxes.percentage as percentage','bill_taxes.tax_id as tax_id')
                            ->get()
                            ->toArray();
                        $currentTaxes = array_merge($thisBillTaxInfo,$currentTaxes->toArray());
                        usort($currentTaxes, function($a, $b) {
                            return $a['tax_id'] > $b['tax_id'];
                        });
                    }else{
                        $currentTaxes = Tax::where('is_active',true)->where('is_special', false)->select('id as tax_id')->get();
                    }
                    $billData['final_total'] = $total_amount;
                    foreach($currentTaxes as $key2 => $tax){
                        if(array_key_exists('percentage',$tax)){
                            $billData['tax'][$tax['tax_id']] = $total_amount * ($tax['percentage'] / 100);
                        }else{
                            $billData['tax'][$tax['tax_id']] = 0;
                        }
                        $billData['final_total'] = MaterialProductHelper::customRound($billData['final_total'] + $billData['tax'][$tax['tax_id']]);
                        $i++;
                    }
                    $thisBillSpecialTax = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                        ->where('bill_taxes.bill_id',$bill->id)
                        ->where('taxes.is_special', true)
                        ->pluck('bill_taxes.tax_id')
                        ->toArray();
                    $otherSpecialTaxes = array_values(array_diff($specialTaxesAppliedToBills,$thisBillSpecialTax));
                    if($thisBillSpecialTax != null){
                        $currentSpecialTaxes = Tax::whereIn('id',$otherSpecialTaxes)->where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();
                    }else{
                        $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','name','base_percentage as percentage')->get();

                    }
                    if($currentSpecialTaxes != null){
                        $thisBillSpecialTaxInfo = BillTax::join('taxes','taxes.id','=','bill_taxes.tax_id')
                            ->where('bill_taxes.bill_id',$bill->id)
                            ->where('taxes.is_special', true)
                            ->select('bill_taxes.percentage as percentage','bill_taxes.applied_on as applied_on','bill_taxes.tax_id as tax_id')
                            ->get()
                            ->toArray();
                        if(!is_array($currentSpecialTaxes)){
                            $currentSpecialTaxes = $currentSpecialTaxes->toArray();
                        }
                        $currentSpecialTaxes = array_merge($thisBillSpecialTaxInfo,$currentSpecialTaxes);
                        usort($currentSpecialTaxes, function($a, $b) {
                            return $a['tax_id'] > $b['tax_id'];
                        });
                    }else{
                        $currentSpecialTaxes = Tax::where('is_active',true)->where('is_special', true)->select('id as tax_id','base_percentage as percentage')->get();
                    }
                    foreach($currentSpecialTaxes as $key2 => $tax){
                        $taxAmount = 0;
                        if(array_key_exists('applied_on',$tax)){
                            $appliedOnTaxes = json_decode($tax['applied_on']);
                            foreach($appliedOnTaxes as $appliedTaxId){
                                if($appliedTaxId == 0){                 // On Subtotal
                                    $taxAmount += $total_amount * ($tax['percentage'] / 100);
                                }else{
                                    $taxAmount += $billData['tax'][$appliedTaxId] * ($tax['percentage'] / 100);
                                }
                            }
                        }else{
                            $taxAmount += $total_amount * ($tax['percentage'] / 100);
                        }

                        $billData['tax'][$tax['tax_id']] = $taxAmount;
                        $billData['final_total'] = MaterialProductHelper::customRound($billData['final_total'] + $billData['tax'][$tax['tax_id']]);
                    }
                    $saleBillTotal += $billData['final_total'];
                }
            }
        }catch(\Exception $e){
            $saleBillTotal = 0;
            $data = [
                'action' => 'Get Total Sale amount for report',
                'project_site_id' => $projectSiteId,
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return $saleBillTotal;
    }

    public function getSubcontractorBillPaidAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $subcontractorStructureData = SubcontractorStructure::get();
            }else{
                $subcontractorStructureData = SubcontractorStructure::where('project_site_id', $projectSiteId)->get();
            }
            $subcontractorIDs = $subcontractorStructureData->unique('subcontractor_id')->pluck('subcontractor_id');
            $advanceAmount = Subcontractor::whereIn('id',$subcontractorIDs)->sum('balance_advance_amount');
            $subcontractorAmount = $subcontractorCashAmount = $subcontractorAmountForBank = 0;
            foreach ($subcontractorStructureData as $key => $subcontractorStructure){
                $subcontractorBillIds = $subcontractorStructure->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                $billPaidAmount = 0;
                foreach ($subcontractorBillIds as $subcontractorStructureBillId){
                    $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
                    $subcontractorBillTransaction = $subcontractorBill->subcontractorBillTransaction;
                    $subcontractorBillReconcileTransaction = $subcontractorBill->subcontractorBillReconcileTransaction;
                    $transactionTotal = $subcontractorBillTransaction->sum('total');
                    $tdsTotal = $subcontractorBillTransaction->sum('tds_amount');
                    $debitTotal = $subcontractorBillTransaction->sum('debit');
                    $otherRecoveryTotal = $subcontractorBillTransaction->sum('other_recovery');
                    $holdTotal = $subcontractorBillTransaction->sum('hold');
                    $retentionTotal = $subcontractorBillTransaction->sum('retention_amount');
                    $totalTransaction = $transactionTotal - ($tdsTotal + $debitTotal + $holdTotal + $retentionTotal + $otherRecoveryTotal);
                    $reconcileTotal = $subcontractorBillReconcileTransaction->sum('amount');
                    $finalTotal = $reconcileTotal + $totalTransaction;
                    $billPaidAmount += $finalTotal;
                }
                $subcontractorAmount += $billPaidAmount;
            }
            $subcontractorAmount += $advanceAmount;

            $finalArray[0] = $subcontractorAmount;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                foreach ($subcontractorStructureData as $key => $subcontractorStructure){
                    $subcontractorBillIds = $subcontractorStructure->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                    $billPaidAmountForBank = 0;
                    foreach ($subcontractorBillIds as $subcontractorStructureBillId){
                        $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
                        $subcontractorBillTransaction = $subcontractorBill->subcontractorBillTransaction->where('bank_id',$bankId);
                        $subcontractorBillReconcileTransaction = $subcontractorBill->subcontractorBillReconcileTransaction->where('bank_id',$bankId);
                        $transactionTotal = $subcontractorBillTransaction->sum('total');
                        $tdsTotal = $subcontractorBillTransaction->sum('tds_amount');
                        $debitTotal = $subcontractorBillTransaction->sum('debit');
                        $otherRecoveryTotal = $subcontractorBillTransaction->sum('other_recovery');
                        $holdTotal = $subcontractorBillTransaction->sum('hold');
                        $retentionTotal = $subcontractorBillTransaction->sum('retention_amount');
                        $totalTransaction = $transactionTotal - ($tdsTotal + $debitTotal + $holdTotal + $retentionTotal + $otherRecoveryTotal);
                        $reconcileTotal = $subcontractorBillReconcileTransaction->where('bank_id',$bankId)->sum('amount');
                        $finalTotal = $reconcileTotal + $totalTransaction;
                        $billPaidAmountForBank += $finalTotal;
                    }
                    $subcontractorAmountForBank += $billPaidAmountForBank;
                }

                $finalArray[$bankIterator] = $subcontractorAmountForBank;
                $bankIterator++;
            }

            //for cash
            foreach ($subcontractorStructureData as $key => $subcontractorStructure){
                $subcontractorBillIds = $subcontractorStructure->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                $billPaidCashAmountForCash = 0;
                foreach ($subcontractorBillIds as $subcontractorStructureBillId){
                    $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
                    $subcontractorBillTransaction = $subcontractorBill->subcontractorBillTransaction->where('paid_from_slug','cash');
                    $subcontractorBillReconcileTransaction = $subcontractorBill->subcontractorBillReconcileTransaction->where('paid_from_slug','cash');
                    $transactionTotal = $subcontractorBillTransaction->sum('total');
                    $tdsTotal = $subcontractorBillTransaction->sum('tds_amount');
                    $debitTotal = $subcontractorBillTransaction->sum('debit');
                    $holdTotal = $subcontractorBillTransaction->sum('hold');
                    $otherRecoveryTotal = $subcontractorBillTransaction->sum('other_recovery');
                    $retentionTotal = $subcontractorBillTransaction->sum('retention_amount');
                    $totalTransaction = $transactionTotal - ($tdsTotal + $debitTotal + $holdTotal + $retentionTotal + $otherRecoveryTotal);
                    $reconcileTotal = $subcontractorBillReconcileTransaction->where('paid_from_slug','cash')->sum('amount');
                    $finalTotal = $reconcileTotal + $totalTransaction;
                    $billPaidCashAmountForCash += $finalTotal;
                }
                $subcontractorCashAmount += $billPaidCashAmountForCash;
            }

            $finalArray[$bankIterator] = $subcontractorCashAmount;
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Subcontractor Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getIndirectExpensesAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $projectSiteIndirectExpenseData = ProjectSiteIndirectExpense::orderBy('created_at','desc')->get();
            }else{
                $projectSiteIndirectExpenseData = ProjectSiteIndirectExpense::where('project_site_id',$projectSiteId)->orderBy('created_at','desc')->get();
            }
            $indirectExpenseAmount = 0;
            foreach ($projectSiteIndirectExpenseData as $key => $projectSiteIndirectExpense){
                $indirectExpenseAmount += $projectSiteIndirectExpense['tds'] + $projectSiteIndirectExpense['gst'];
            }
            $finalArray[0] = $indirectExpenseAmount;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                $projectSiteIndirectExpenseDataForBank = $projectSiteIndirectExpenseData->where('bank_id',$bankId);
                $indirectExpenseAmountForBank = 0;
                foreach ($projectSiteIndirectExpenseDataForBank as $key => $projectSiteIndirectExpense){
                    $indirectExpenseAmountForBank += $projectSiteIndirectExpense['tds'] + $projectSiteIndirectExpense['gst'];
                }

                $finalArray[$bankIterator] = $indirectExpenseAmountForBank;
                $bankIterator ++ ;
            }

            //for cash
            $projectSiteIndirectExpenseDataForCash = $projectSiteIndirectExpenseData->where('paid_from_slug','cash');
            $indirectExpenseAmountForCash = 0;
            foreach ($projectSiteIndirectExpenseDataForCash as $key => $projectSiteIndirectExpense){
                $indirectExpenseAmountForCash += $projectSiteIndirectExpense['tds'] + $projectSiteIndirectExpense['gst'];
            }
            $finalArray[$bankIterator] = $indirectExpenseAmountForCash;
        }catch (\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Indirect Expenses Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getPeticashPurchaseAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $purchasePeticashTransactionAmount = PurcahsePeticashTransaction::sum('bill_amount');
                $officeSiteDistributedAmount = ProjectSite::sum('distributed_purchase_peticash_amount');
                $miscellaneousPurchaseAmount = $purchasePeticashTransactionAmount + $officeSiteDistributedAmount;
            }else{
                $purchasePeticashTransactionAmount = PurcahsePeticashTransaction::where('project_site_id',$projectSiteId)->sum('bill_amount');
                $officeSiteDistributedAmount = ProjectSite::where('id',$projectSiteId)->pluck('distributed_purchase_peticash_amount')->first();
                $officeSiteDistributedAmount = ($officeSiteDistributedAmount != null) ? $officeSiteDistributedAmount : 0;
                $miscellaneousPurchaseAmount = $purchasePeticashTransactionAmount + $officeSiteDistributedAmount;
            }
            $finalArray[0] = $miscellaneousPurchaseAmount;

            //for bank & cash
            $finalArray = array_merge($finalArray,array_fill(0,count($bankIds) + 1,0));
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Subcontractor Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getBillTransactionsAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $quotations = Quotation::get();
            }else{
                $quotations = Quotation::where('project_site_id',$projectSiteId)->get();
            }
            $statusId = BillStatus::where('slug','approved')->pluck('id')->first();
            $totalReceiptEntry = 0;
            foreach($quotations as $key4 => $quotation){
                $balanceAdvancedAmount = ProjectSite::where('id',$quotation['project_site_id'])->pluck('advanced_balance')->first();

                $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->orderBy('created_at','asc')->get();
                foreach($bills as $key => $bill){
                    $billTransaction = $bill->transactions;
                    $billReconcileTransaction = $bill->billReconcileTransaction;
                    $billTransactionSubTotal = $billTransaction->sum('total');
                    /*$billTransactionDebit = $billTransaction->sum('debit');
                    $billTransactionTds = $billTransaction->sum('tds_amount');
                    $billTransactionHold = $billTransaction->sum('hold') - $billReconcileTransaction->where('transaction_slug','hold')->sum('amount');
                    $billTransactionRetention = $billTransaction->sum('retention_amount') - $billReconcileTransaction->where('transaction_slug','retention')->sum('amount');*/
                    $billTransactionTotal = $billTransactionSubTotal + $billReconcileTransaction->where('transaction_slug','hold')->sum('amount') + $billReconcileTransaction->where('transaction_slug','retention')->sum('amount');
                    $totalReceiptEntry += $billTransactionTotal;
                }
                $totalReceiptEntry += $balanceAdvancedAmount;
            }
            $finalArray[0] = $totalReceiptEntry;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                $totalReceiptEntry = 0;
                foreach($quotations as $key4 => $quotation){
                    $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->orderBy('created_at','asc')->get();
                    foreach($bills as $key => $bill){
                        $billTransaction = $bill->transactions->where('bank_id',$bankId);
                        $billReconcileTransaction = $bill->billReconcileTransaction->where('bank_id',$bankId);
                        $billTransactionSubTotal = $billTransaction->sum('total');
                        /*$billTransactionDebit = $billTransaction->sum('debit');
                        $billTransactionTds = $billTransaction->sum('tds_amount');
                        $billTransactionHold = $billTransaction->sum('hold') - $billReconcileTransaction->where('transaction_slug','hold')->sum('amount');
                        $billTransactionRetention = $billTransaction->sum('retention_amount') - $billReconcileTransaction->where('transaction_slug','retention')->sum('amount');*/
                        $billTransactionTotal = $billTransactionSubTotal + $billReconcileTransaction->where('transaction_slug','hold')->where('bank_id',$bankId)->sum('amount') + $billReconcileTransaction->where('transaction_slug','retention')->where('bank_id',$bankId)->sum('amount')/*- ($billTransactionDebit + $billTransactionTds + $billTransactionHold + $billTransactionRetention)*/;
                        $totalReceiptEntry += $billTransactionTotal ;
                    }
                }
                $finalArray[$bankIterator] = $totalReceiptEntry;
                $bankIterator++;
            }
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 1),0);
            $data = [
                'action' => 'Get Bill Transaction Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getPurchasePaidAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $purchasePaymentAmount = PurchaseOrderPayment::sum('amount');
                $advancedAmounts = PurchaseOrder::sum('balance_advance_amount');

            }else{
                $purchasePaymentAmount = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                        ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                        ->where('purchase_requests.project_site_id',$projectSiteId)->sum('purchase_order_payments.amount');
                $advancedAmounts = PurchaseOrder::join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                    ->where('purchase_requests.project_site_id',$projectSiteId)->sum('purchase_orders.balance_advance_amount');
            }

            $purchasePaidAmount = $purchasePaymentAmount + $advancedAmounts;
            $finalArray[0] = $purchasePaidAmount;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                if($projectSiteId == 'all'){
                    $purchasePaymentBankAmount = PurchaseOrderPayment::where('bank_id',$bankId)->sum('amount');
                }else{
                    $purchasePaymentBankAmount = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                        ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                        ->where('purchase_requests.project_site_id',$projectSiteId)->where('purchase_order_payments.bank_id',$bankId)->sum('purchase_order_payments.amount');
                }
                $purchasePaidBankAmount = $purchasePaymentBankAmount;
                $finalArray[$bankIterator] = $purchasePaidBankAmount;
                $bankIterator++;
            }

            //for cash
            if($projectSiteId == 'all'){
                $purchasePaymentCashAmount = PurchaseOrderPayment::where('paid_from_slug','cash')->sum('amount');
            }else{
                $purchasePaymentCashAmount = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                    ->where('purchase_requests.project_site_id',$projectSiteId)->where('purchase_order_payments.paid_from_slug','cash')->sum('purchase_order_payments.amount');
            }
            $purchasePaidCashAmount = $purchasePaymentCashAmount;
            $finalArray[$bankIterator] = $purchasePaidCashAmount;
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Purchase Order Payment Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getAssetMaintenancePaidAmount($projectSiteId,$bankIds){
        try{
            if($projectSiteId == 'all'){
                $assetMaintenanceBillAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bill_payments.amount');
            }else{
                $assetMaintenanceBillAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bill_payments.amount');
            }
            $finalArray[0] = $assetMaintenanceBillAmount;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                if($projectSiteId == 'all'){
                    $assetMaintenanceBillBankAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                        ->where('asset_maintenance_bill_payments.bank_id',$bankId)
                        ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                        ->sum('asset_maintenance_bill_payments.amount');
                }else{
                    $assetMaintenanceBillBankAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                        ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                        ->where('asset_maintenance.project_site_id',$projectSiteId)
                        ->where('asset_maintenance_bill_payments.bank_id',$bankId)
                        ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                        ->sum('asset_maintenance_bill_payments.amount');
                }
                $finalArray[$bankIterator] = $assetMaintenanceBillBankAmount;
                $bankIterator++;
            }

            //for cash
            if($projectSiteId == 'all'){
                $assetMaintenanceBillCashAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->where('asset_maintenance_bill_payments.paid_from_slug','cash')
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bill_payments.amount');
            }else{
                $assetMaintenanceBillCashAmount = AssetMaintenanceBillPayment::join('asset_maintenance_bills','asset_maintenance_bills.id','=','asset_maintenance_bill_payments.asset_maintenance_bill_id')
                    ->join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                    ->where('asset_maintenance_bill_payments.paid_from_slug','cash')
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bill_payments.amount');
            }
            $finalArray[$bankIterator] = $assetMaintenanceBillCashAmount;
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Asset Maintenance Bill Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getAssetRentPaidAmount($projectSiteId,$bankIds){
        try{
            $assetRentAmount = 0;
            if($projectSiteId == 'all'){
                $inventoryComponents = InventoryComponent::where('is_material',false)->get();
            }else{
                $inventoryComponents = InventoryComponent::where('project_site_id',$projectSiteId)->where('is_material',false)->get();
            }
            foreach ($inventoryComponents as $key => $inventoryComponent){
                $transferInData = $inventoryComponent->inventoryComponentTransfers
                    ->where('transfer_type_id',InventoryTransferTypes::whereIn('slug',['site','office'])->where('type','ilike','IN')->pluck('id')->first())
                    ->where('inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())->first();
                if($transferInData == null){
                    $assetRentAmount += 0;
                }else{
                    $transferOutData = $inventoryComponent->inventoryComponentTransfers
                        ->where('id','>',$transferInData['id'])
                        ->where('transfer_type_id',InventoryTransferTypes::where('slug','site')->where('type','ilike','OUT')->pluck('id')->first())
                        ->where('inventory_component_transfer_status_id',InventoryComponentTransferStatus::where('slug','approved')->pluck('id')->first())->first();
                    if($transferOutData == null){
                        $transferOutDate = Carbon::now();
                    }else{
                        $transferOutDate = $transferOutData['created_at'];
                    }
                    $rentDays = Carbon::parse(date('Y-m-d',strtotime($transferInData['created_at'])))->diffInDays(Carbon::parse(date('Y-m-d',strtotime($transferOutDate))));
                    $assetRentAmount += $rentDays * $transferInData['rate_per_unit'] * $transferInData['quantity'];
                }
            }
            $finalArray[0] = $assetRentAmount;
            //for bank & cash
            $finalArray = array_merge($finalArray,array_fill(1,count($bankIds) + 1 ,0));
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Asset Rent Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getSiteTransferAmount($projectSiteId,$bankIds){
        try{
            $inOutTransferTypes = InventoryTransferTypes::where('slug','site')->whereIn('type',['IN','OUT'])->select('id','type')->get();
            if($projectSiteId == 'all'){
                $siteTransferBillAmount = SiteTransferBillPayment::sum('amount');
                $siteInTransferTotal = InventoryComponentTransfers::where('transfer_type_id',$inOutTransferTypes->where('type','IN')->pluck('id')->first())->sum('total');
                $siteOutTransferTotal = InventoryComponentTransfers::where('transfer_type_id',$inOutTransferTypes->where('type','OUT')->pluck('id')->first())->sum('total');
                $siteTransferAmount = $siteTransferBillAmount + $siteInTransferTotal - $siteOutTransferTotal;
            }else{
                $siteTransferBillAmount = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                                                                    ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                                                                    ->join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                                                    ->where('inventory_components.project_site_id',$projectSiteId)->sum('site_transfer_bill_payments.amount');
                $siteInTransferTotal = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                                                                        ->where('inventory_components.project_site_id',$projectSiteId)->where('inventory_component_transfers.transfer_type_id',$inOutTransferTypes->where('type','IN')->pluck('id')->first())->sum('inventory_component_transfers.total');
                $siteOutTransferTotal = InventoryComponentTransfers::join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')

                                                                        ->where('inventory_components.project_site_id',$projectSiteId)->where('inventory_component_transfers.transfer_type_id',$inOutTransferTypes->where('type','OUT')->pluck('id')->first())->sum('inventory_component_transfers.total');
                $siteTransferAmount = $siteTransferBillAmount + $siteInTransferTotal - $siteOutTransferTotal;
            }
            $finalArray[0] = $siteTransferAmount;


            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                if($projectSiteId == 'all'){
                    $siteTransferBankAmount = SiteTransferBillPayment::where('bank_id',$bankId)->sum('amount');
                }else{
                    $siteTransferBankAmount = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                        ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                        ->join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                        ->where('site_transfer_bill_payments.bank_id',$bankId)
                        ->where('inventory_components.project_site_id',$projectSiteId)->sum('site_transfer_bill_payments.amount');
                }
                $finalArray[$bankIterator] = $siteTransferBankAmount;
                $bankIterator++;
            }

            //for cash
            if($projectSiteId == 'all'){
                $siteTransferCashAmount = SiteTransferBillPayment::where('paid_from_slug','cash')->sum('amount');
            }else{
                $siteTransferCashAmount = SiteTransferBillPayment::join('site_transfer_bills','site_transfer_bills.id','=','site_transfer_bill_payments.site_transfer_bill_id')
                    ->join('inventory_component_transfers','inventory_component_transfers.id','=','site_transfer_bills.inventory_component_transfer_id')
                    ->join('inventory_components','inventory_components.id','=','inventory_component_transfers.inventory_component_id')
                    ->where('site_transfer_bill_payments.paid_from_slug','cash')
                    ->where('inventory_components.project_site_id',$projectSiteId)->sum('site_transfer_bill_payments.amount');
            }
            $finalArray[$bankIterator] = $siteTransferCashAmount;
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Site Transfer Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function timeDelay($actualDate,$estimatedDate){
        return Carbon::parse($actualDate)->diff(Carbon::parse($estimatedDate));
    }

    public function getPeticashSalaryAmount($projectSiteId,$bankIds){
        try{
            $peticashSalaryAmount = 0;
            $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $officeSiteId = ProjectSite::where('name',env('OFFICE_PROJECT_SITE_NAME'))->pluck('id')->first();
            if($projectSiteId == 'all'){
                $peticashTransactions = PeticashSalaryTransaction::where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->get();
                $advanceAmountTotal = $peticashTransactions
                            ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','advance')->pluck('id')->first())
                            ->sum('amount');

                $salaryTransactions = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());

                $salaryPayableAmountTotal = $salaryTransactions->sum('payable_amount');

                $salaryPfAmountTotal = $salaryTransactions->sum('pf');

                $salaryTdsAmountTotal = $salaryTransactions->sum('tds');

                $salaryPtAmountTotal = $salaryTransactions->sum('pt');

                $salaryEsicAmountTotal = $salaryTransactions->sum('esic');

                $salaryAmountTotal = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                $officeSiteDistributedAmount = ProjectSite::sum('distributed_salary_amount');
                $peticashSalaryAmount = $salaryAmountTotal + $advanceAmountTotal + $officeSiteDistributedAmount;
            }else{
                $peticashTransactions = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                                ->where('project_site_id','!=',$officeSiteId)
                                ->where('peticash_status_id',$approvedPeticashStatusId)
                                ->get();

                $advanceAmountTotal = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','PAYMENT')->where('slug','advance')->pluck('id')->first())
                                        ->sum('amount');
                $salaryTransactions = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());
                $salaryPayableAmountTotal = $salaryTransactions->sum('payable_amount');
                $salaryPfAmountTotal = $salaryTransactions->sum('pf');
                $salaryTdsAmountTotal = $salaryTransactions->sum('tds');
                $salaryPtAmountTotal = $salaryTransactions->sum('pt');
                $salaryEsicAmountTotal = $salaryTransactions->sum('esic');

                $salaryAmountTotal = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                $officeSiteDistributedAmount = ProjectSite::where('id',$projectSiteId)->pluck('distributed_salary_amount')->first();
                $officeSiteDistributedAmount = ($officeSiteDistributedAmount != null) ? $officeSiteDistributedAmount : 0;
                $peticashSalaryAmount = $salaryAmountTotal + $advanceAmountTotal + $officeSiteDistributedAmount;
            }
            $finalArray[0] = $peticashSalaryAmount;

            //for bank
            $bankIterator = 1;
            foreach($bankIds as $bankId){
                if($projectSiteId == 'all'){
                    $peticashTransactions = PeticashSalaryTransaction::where('project_site_id','!=',$officeSiteId)
                        ->where('peticash_status_id',$approvedPeticashStatusId)
                        ->where('bank_id',$bankId)
                        ->get();

                    $advanceAmountTotalForBank = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','advance')->pluck('id')->first())
                                                ->sum('amount');
                    $salaryTransactions = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());

                    $salaryPayableAmountTotal = $salaryTransactions->sum('payable_amount');

                    $salaryPfAmountTotal = $salaryTransactions->sum('pf');

                    $salaryTdsAmountTotal = $salaryTransactions->sum('tds');

                    $salaryPtAmountTotal = $salaryTransactions->sum('pt');

                    $salaryEsicAmountTotal = $salaryTransactions->sum('esic');

                    $salaryAmountTotalForBank = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                    $peticashSalaryBankAmount = $salaryAmountTotalForBank + $advanceAmountTotalForBank ;
                }else{
                    $peticashTransactions = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                        ->where('project_site_id','!=',$officeSiteId)
                        ->where('peticash_status_id',$approvedPeticashStatusId)
                        ->where('bank_id',$bankId)
                        ->get();


                    $salaryTransactions = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());
                    $advanceAmountTotalForBank = $peticashTransactions->where('peticash_transaction_type_id',PeticashTransactionType::where('type','PAYMENT')->where('slug','advance')->pluck('id')->first())
                                                ->sum('amount');

                    $salaryPayableAmountTotal = $salaryTransactions->sum('payable_amount');
                    $salaryPfAmountTotal = $salaryTransactions->sum('pf');
                    $salaryTdsAmountTotal = $salaryTransactions->sum('tds');
                    $salaryPtAmountTotal = $salaryTransactions->sum('pt');
                    $salaryEsicAmountTotal = $salaryTransactions->sum('esic');

                    $salaryAmountTotalForBank = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                    $peticashSalaryBankAmount = $salaryAmountTotalForBank + $advanceAmountTotalForBank;
                }
                $finalArray[$bankIterator] = $peticashSalaryBankAmount;
                $bankIterator++;
            }

            //for cash
            if($projectSiteId == 'all'){
                $peticashTransactionsForCash = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->where('bank_id',null)
                    ->get();

                $salaryTransactionsForCash = $peticashTransactionsForCash->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());

                $salaryPayableAmountTotalForCash = $salaryTransactionsForCash->sum('payable_amount');
                $salaryPfAmountTotalForCash = $salaryTransactionsForCash->sum('pf');
                $salaryTdsAmountTotalForCash = $salaryTransactionsForCash->sum('tds');
                $salaryPtAmountTotalForCash = $salaryTransactionsForCash->sum('pt');
                $salaryEsicAmountTotalForCash = $salaryTransactionsForCash->sum('esic');

                $salaryAmountTotalForCash = $salaryPayableAmountTotalForCash + $salaryPfAmountTotalForCash + $salaryTdsAmountTotalForCash + $salaryPtAmountTotalForCash + $salaryEsicAmountTotalForCash;
                $advanceAmountTotalForCash = $peticashTransactionsForCash
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','advance')->pluck('id')->first())
                    ->where('bank_id',null)
                    ->sum('amount');
            }else{
                $peticashTransactionsForCash = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->where('bank_id',null)
                    ->get();


                $salaryTransactionsForCash = $peticashTransactionsForCash->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first());
                $salaryPayableAmountTotalForCash = $salaryTransactionsForCash->sum('payable_amount');
                $salaryPfAmountTotalForCash = $salaryTransactionsForCash->sum('pf');
                $salaryTdsAmountTotalForCash = $salaryTransactionsForCash->sum('tds');
                $salaryPtAmountTotalForCash = $salaryTransactionsForCash->sum('pt');
                $salaryEsicAmountTotalForCash = $salaryTransactionsForCash->sum('esic');

                $salaryAmountTotalForCash = $salaryPayableAmountTotalForCash + $salaryPfAmountTotalForCash + $salaryTdsAmountTotalForCash + $salaryPtAmountTotalForCash + $salaryEsicAmountTotalForCash;
                $advanceAmountTotalForCash = $peticashTransactionsForCash
                                        ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','advance')->pluck('id')->first())
                                        ->where('bank_id',null)
                                        ->sum('amount');
            }
            $finalArray[$bankIterator] = $salaryAmountTotalForCash + $advanceAmountTotalForCash;
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Peticash Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }

    public function getQuotationOpeningExpenseAmount($projectSiteId,$bankIds){
        try{
            $openingExpenseAmount = 0;
            if($projectSiteId == 'all'){
                $openingExpenseAmount = Quotation::sum('opening_expenses');
            }else{
                $openingExpenseAmount = Quotation::where('project_site_id',$projectSiteId)->pluck('opening_expenses')->first();
            }
            $finalArray[0] = $openingExpenseAmount;
            $finalArray = array_merge($finalArray,array_fill(1,count($bankIds) + 1 ,0));
        }catch(\Exception $e){
            $finalArray = array_fill(0,(count($bankIds) + 2),0);
            $data = [
                'action' => 'Get Opening Expenses for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $finalArray;
    }


}
