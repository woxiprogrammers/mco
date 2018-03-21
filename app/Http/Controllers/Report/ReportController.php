<?php

namespace App\Http\Controllers\Report;

use App\AssetMaintenanceBill;
use App\Bill;
use App\BillQuotationExtraItem;
use App\BillQuotationProducts;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Employee;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\BillTrait;
use App\InventoryComponent;
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
use App\PurchaseOrderBillTransactionRelation;
use App\PurchaseOrderComponent;
use App\PurchaseOrderPayment;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionComponent;
use App\PurchaseOrderTransactionStatus;
use App\Quotation;
use App\QuotationProduct;
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
            $employees = Employee::where('employee_type_id', 1)->get(['id','name','employee_id'])->toArray();
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
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                        $excel->sheet($report_type, function($sheet) use($data, $header) {
                            $sheet->row(1, $header);
                            $row = 1;
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
                        'Date', 'Payment Type', 'Gross Salary', '-PT', '-PF', '-ESIC', '-TDS',
                        '-ADVANCE', 'Net Payment', 'Balance'
                    );
                    $approvedStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
                    $paymentSlug = PeticashTransactionType::where('type','PAYMENT')->select('id','slug')->get();
                    if($request['labour_specific_report_site_id'] == 'all'){
                        $salaryTransactionData = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('id','asc')
                            ->get();
                    }else{
                        $salaryTransactionData = PeticashSalaryTransaction::where('project_site_id',$request['labour_specific_report_site_id'])
                            ->where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('id','asc')
                            ->get();
                    }

                    foreach($salaryTransactionData as $key => $salaryTransaction){
                        $peticashTransactionTypeSlug = $salaryTransaction->peticashTransactionType->slug;
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
                                $advancesAfterLastSalary = -1;
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
                                $data[$row]['balance'] = 0;
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
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                        $excel->sheet($report_type, function($sheet) use($data, $header) {
                            $sheet->row(1, $header);
                            $row = 1;
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

                case 'subcontractor_report':
                    $header = array(
                        'Date', 'Summary Type', 'Bill No', 'Basic amount', 'Total tax', 'Total Bill Amount', 'Advance' ,'Debit', 'Hold', 'Retention',
                        'TDS', 'Other Recovery', 'Payable Amount', 'Check amount', 'Balance'
                    );
                    $subContractorBillTransactionList = SubcontractorBillTransaction::join('subcontractor_bills','subcontractor_bills.id','=','subcontractor_bill_transactions.subcontractor_bills_id')
                                                        ->join('subcontractor_structure','subcontractor_structure.id','=','subcontractor_bills.sc_structure_id')
                                                        ->whereBetween('subcontractor_bill_transactions.created_at',[$start_date, $end_date])
                                                        ->where('subcontractor_bills.subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())
                                                        ->where('subcontractor_structure.subcontractor_id',$request['subcontractor_id'])
                                                        ->where('subcontractor_structure.project_site_id',$request['subcontractor_report_site_id'])
                                                        ->orderBy('subcontractor_bills.created_at')
                                                        ->select('subcontractor_structure.summary_id','subcontractor_bill_transactions.id as subcontractor_bill_transaction_id','subcontractor_bill_transactions.subcontractor_bills_id as subcontractor_bill_id','subcontractor_bill_transactions.subtotal','subcontractor_bill_transactions.total','subcontractor_bill_transactions.debit','subcontractor_bill_transactions.hold',
                                                            'subcontractor_bill_transactions.retention_percent','subcontractor_bill_transactions.retention_amount','subcontractor_bill_transactions.tds_percent','subcontractor_bill_transactions.tds_amount','subcontractor_bill_transactions.other_recovery','subcontractor_bill_transactions.created_at')->get();

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
                            $data[$row]['date'] = date('d/m/y',strtotime($subContractorBillTransaction['created_at']));
                            $data[$row]['summary_type'] = Summary::where('id',$subContractorBillTransaction['summary_id'])->pluck('name')->first();
                            $data[$row]['bill_no'] = $billName;
                            $data[$row]['basic_amount'] = $rate;
                            $data[$row]['total_tax'] = $taxTotal;
                            $data[$row]['total_bill_amount'] = $finalTotal;
                            $data[$row]['advance'] = '-';
                            $data[$row]['debit'] = (-$subContractorBillTransaction['debit'] !=0 ) ? -$subContractorBillTransaction['debit'] : $subContractorBillTransaction['debit'];
                            $data[$row]['hold'] = ($subContractorBillTransaction['hold'] != 0) ? -$subContractorBillTransaction['hold'] : $subContractorBillTransaction['hold'];
                            $data[$row]['retention'] = ($subContractorBillTransaction['retention_amount'] != 0) ? -$subContractorBillTransaction['retention_amount'] : $subContractorBillTransaction['retention_amount'];
                            $data[$row]['tds'] = ($subContractorBillTransaction['tds_amount'] != 0) ? -$subContractorBillTransaction['tds_amount'] : $subContractorBillTransaction['tds_amount'];
                            $data[$row]['other_recovery'] = $subContractorBillTransaction['other_recovery'];
                            $data[$row]['payable_amount'] = $data[$row]['total_bill_amount'] + $data[$row]['debit'] + $data[$row]['hold'] + $data[$row]['retention'] + $data[$row]['tds'] + $data[$row]['other_recovery'];
                            $data[$row]['check_amount'] = $subContractorBillTransaction['total'];
                            $data[$row]['balance'] = '-';
                            $row++;
                        }
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                        $excel->sheet($report_type, function($sheet) use($data, $header) {
                            $sheet->row(1, $header);
                            $row = 1;
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
                            $data[$row]['balance'] = '-';
                            $row++;
                        }
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                        $excel->sheet($report_type, function($sheet) use($data, $header) {
                            $sheet->row(1, $header);
                            $row = 1;
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

                case 'purchase_bill_tax_report':
                    $header = array(
                        'Date', 'Bill Number', 'Basic Amount', 'IGST Amount', 'SGST Amount', 'CGST Amount',
                        'With Tax Amount', 'Total Amount', 'Paid Amount', 'Balance'
                    );
                    $purchaseOrderBillPayments = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                                                    ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                                    ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                                    ->where('purchase_requests.project_site_id',$request['purchase_bill_tax_report_site_id'])
                                                    ->whereBetween('purchase_order_payments.created_at',[$start_date, $end_date])
                                                    ->where('purchase_orders.vendor_id',$request['vendor_id'])
                                                    ->select('purchase_order_payments.id as purchase_order_payment_id','purchase_order_payments.purchase_order_bill_id','purchase_order_payments.payment_id'
                                                        ,'purchase_order_payments.amount','purchase_order_payments.reference_number','purchase_order_payments.is_advance'
                                                        ,'purchase_order_payments.created_at','purchase_order_bills.amount as bill_amount','purchase_order_bills.tax_amount'
                                                        ,'purchase_order_bills.bill_number','purchase_order_bills.extra_amount')->get()->toArray();
                    $total['basicAmount'] = $total['igstAmount'] = $total['sgstAmount'] = $total['cgstAmount'] = $total['paidAmount'] = $total['amount'] = $total['amountWithTax'] = $total['balance'] = 0;
                    foreach($purchaseOrderBillPayments as $key => $purchaseOrderBillPayment){
                        $transactionIds = PurchaseOrderBillTransactionRelation::where('purchase_order_bill_id',$purchaseOrderBillPayment['purchase_order_bill_id'])->pluck('purchase_order_transaction_id');
                        $purchaseOrderTransactions = PurchaseOrderTransaction::whereIn('id',$transactionIds)->get();
                        $cgstAmount = $sgstAmount = $igstAmount = 0;
                        foreach($purchaseOrderTransactions as $purchaseOrderTransaction){
                            foreach($purchaseOrderTransaction->purchaseOrderTransactionComponents as $purchaseOrderTransactionComponent){
                                $purchaseOrderComponent = $purchaseOrderTransactionComponent->purchaseOrderComponent;
                                $unitConversionRate = UnitHelper::unitConversion($purchaseOrderTransactionComponent->purchaseOrderComponent->unit_id,$purchaseOrderTransactionComponent->unit_id,$purchaseOrderTransactionComponent->purchaseOrderComponent->rate_per_unit);
                                if(!is_array($unitConversionRate)){
                                    $tempAmount = $purchaseOrderTransactionComponent->quantity * $unitConversionRate;
                                    if($purchaseOrderComponent->cgst_percentage != null || $purchaseOrderComponent->cgst_percentage != ''){
                                        $cgstAmount += $tempAmount * ($purchaseOrderComponent->cgst_percentage/100);
                                    }
                                    if($purchaseOrderComponent->sgst_percentage != null || $purchaseOrderComponent->sgst_percentage != ''){
                                        $sgstAmount += $tempAmount * ($purchaseOrderComponent->sgst_percentage/100);
                                    }
                                    if($purchaseOrderComponent->igst_percentage != null || $purchaseOrderComponent->igst_percentage != ''){
                                        $igstAmount += $tempAmount * ($purchaseOrderComponent->igst_percentage/100);
                                    }
                                }
                            }
                        }
                        $data[$row]['date'] = $purchaseOrderBillPayment['created_at'];
                        $data[$row]['bill_number'] = $purchaseOrderBillPayment['bill_number'];
                        $data[$row]['basic_amount'] = $purchaseOrderBillPayment['bill_amount'] - $purchaseOrderBillPayment['extra_amount'] - $purchaseOrderBillPayment['tax_amount'];
                        $data[$row]['igst_amount'] = $igstAmount;
                        $data[$row]['sgst_amount'] = $sgstAmount;
                        $data[$row]['cgst_amount'] = $cgstAmount;
                        $data[$row]['amount_with_tax'] = $purchaseOrderBillPayment['bill_amount'] - $purchaseOrderBillPayment['extra_amount'];
                        $data[$row]['total_amount'] = $purchaseOrderBillPayment['bill_amount'];
                        $data[$row]['paid_amount'] = $purchaseOrderBillPayment['amount'];
                        $data[$row]['balance'] = $data[$row]['amount_with_tax'] - $data[$row]['paid_amount'];
                        $total['basicAmount'] += $data[$row]['basic_amount'];
                        $total['igstAmount'] += $data[$row]['igst_amount'];
                        $total['sgstAmount'] += $data[$row]['sgst_amount'];
                        $total['cgstAmount'] += $data[$row]['cgst_amount'];
                        $total['amountWithTax'] += $data[$row]['amount_with_tax'];
                        $total['amount'] += $data[$row]['total_amount'];
                        $total['paidAmount'] += $data[$row]['paid_amount'];
                        $total['balance'] += $data[$row]['balance'];
                        $row++;
                    }
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header, $total) {
                        $excel->sheet($report_type, function($sheet) use($data, $header, $total) {
                            $sheet->row(1, $header);
                            $row = 1;
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
                            if($row > 2){
                                $sheet->row($row, array('','Total',$total['basicAmount'],$total['igstAmount'],$total['sgstAmount'],$total['cgstAmount'],$total['amountWithTax'],$total['amount'],$total['paidAmount'],$total['balance']));
                            }
                        });
                    })->export('xls');
                break;


                case 'receiptwise_p_and_l_report':
                    $projectSiteId = $request['receiptwise_p_and_l_report_site_id'];
                    $totalSalesEntry = $this->getTotalSalesEntry($projectSiteId);
                    $subcontractor = $this->getSubcontractorBillPaidAmount($projectSiteId);
                    $indirectExpensesAmount = $this->getIndirectExpensesAmount($projectSiteId);
                    $miscellaneousPurchaseAmount = $this->getPeticashPurchaseAmount($projectSiteId);
                    $totalReceiptEntry = $this->getBillTransactionsAmount($projectSiteId);
                    $purchasePaidAmount = $this->getPurchasePaidAmount($projectSiteId);
                    $assetRentAmount = $this->getAssetRentPaidAmount($projectSiteId);
                    $assetMaintenancePaidAmount = $this->getAssetMaintenancePaidAmount($projectSiteId);
                    $peticashSalaryAmount = $this->getPeticashSalaryAmount($projectSiteId);
                    $totalPurchase = $purchasePaidAmount + $assetMaintenancePaidAmount;
                    $total = $totalPurchase + $miscellaneousPurchaseAmount + $subcontractor + $indirectExpensesAmount + $peticashSalaryAmount;
                    $profitLossSaleWise = $totalSalesEntry - $total;
                    $profitLossReceiptWise = $totalReceiptEntry - $total;
                    $data = array(
                        array('Total Sale Entry', 'Total Sale Entry'),
                        array($totalSalesEntry, $totalReceiptEntry, 'Expences on', 'Total expence'),
                        array(null, null, 'Labour' , '1000000'),
                        array(null, null, 'Total purchase' , $totalPurchase),
                        array(null, null, 'Total miscellaneous purchase' , $miscellaneousPurchaseAmount),
                        array(null, null, 'Subcontractor' , $subcontractor),
                        array(null, null, 'SALARY' , $peticashSalaryAmount),
                        array(null, null, 'IndirectExpences(GST,TDS Paid to government from Manisha)' , $indirectExpensesAmount),
                        array(null, null, 'Subcontractor' , $subcontractor),
                        array(null, null),
                        array($totalSalesEntry, $totalReceiptEntry, null, $total),
                        array(null, null),
                        array('Profit/ Loss Salewise', 'Profit/ Loss Receiptwise'),
                        array($profitLossSaleWise, $profitLossReceiptWise),
                    );
                    Excel::create($report_type."_".$curr_date, function($excel) use($data, $report_type, $header) {
                        $excel->sheet($report_type, function($sheet) use($data, $header) {
                            $sheet->row(1, $header);
                            $row = 1;
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
                'action' => 'Get Bill Detail Listing',
                'project_site_id' => $projectSiteId,
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return $saleBillTotal;
    }

    public function getSubcontractorBillPaidAmount($projectSiteId){
        try{
            if($projectSiteId == 'all'){
                $subcontractorStructureData = SubcontractorStructure::get();
            }else{
                $subcontractorStructureData = SubcontractorStructure::where('project_site_id', $projectSiteId)->get();
            }
            $subcontractorAmount = 0;
            foreach ($subcontractorStructureData as $key => $subcontractorStructure){
                $subcontractorBillIds = $subcontractorStructure->subcontractorBill->where('subcontractor_bill_status_id',SubcontractorBillStatus::where('slug','approved')->pluck('id')->first())->pluck('id');
                $billTotals = 0;
                $billPaidAmount = 0;
                foreach ($subcontractorBillIds as $subcontractorStructureBillId){
                    $subcontractorBill = SubcontractorBill::where('id',$subcontractorStructureBillId)->first();
                    $subcontractorStructure = $subcontractorBill->subcontractorStructure;
                    $subcontractorBillTaxes = $subcontractorBill->subcontractorBillTaxes;
                    $taxTotal = 0;
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
                    $billTotals += $finalTotal;
                    $billPaidAmount += SubcontractorBillTransaction::where('subcontractor_bills_id',$subcontractorStructureBillId)->sum('total');
                }
                $subcontractorAmount += $billPaidAmount;
            }
        }catch(\Exception $e){
            $subcontractorAmount = 0;
            $data = [
                'action' => 'Get Subcontractor Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $subcontractorAmount;
    }

    public function getIndirectExpensesAmount($projectSiteId){
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
        }catch (\Exception $e){
            $indirectExpenseAmount = 0;
            $data = [
                'action' => 'Get Subcontractor Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $indirectExpenseAmount;
    }

    public function getPeticashPurchaseAmount($projectSiteId){
        try{
            if($projectSiteId == 'all'){
                $miscellaneousPurchaseAmount = PurcahsePeticashTransaction::sum('bill_amount');
            }else{
                $miscellaneousPurchaseAmount = PurcahsePeticashTransaction::where('project_site_id',$projectSiteId)->sum('bill_amount');
            }
        }catch(\Exception $e){
            $miscellaneousPurchaseAmount = 0;
            $data = [
                'action' => 'Get Subcontractor Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $miscellaneousPurchaseAmount;
    }

    public function getBillTransactionsAmount($projectSiteId){
        try{
            if($projectSiteId == 'all'){
                $quotations = Quotation::get();
            }else{
                $quotations = Quotation::where('project_site_id',$projectSiteId)->get();
            }
            $totalReceiptEntry = 0;
            foreach($quotations as $key4 => $quotation){
                $statusId = BillStatus::where('slug','approved')->pluck('id')->first();
                $bills = Bill::where('quotation_id',$quotation->id)->where('bill_status_id',$statusId)->orderBy('created_at','asc')->get();
                foreach($bills as $key => $bill){
                    $totalReceiptEntry += BillTransaction::where('bill_id', $bill->id)->sum('total');
                }
            }
        }catch(\Exception $e){
            $totalReceiptEntry = 0;
            $data = [
                'action' => 'Get Bill Transaction Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $totalReceiptEntry;
    }

    public function getPurchasePaidAmount($projectSiteId){
        try{
            if($projectSiteId == 'all'){
                $purchasePaidAmount = PurchaseOrderPayment::sum('amount');
            }else{
                $purchasePaidAmount = PurchaseOrderPayment::join('purchase_order_bills','purchase_order_bills.id','=','purchase_order_payments.purchase_order_bill_id')
                                        ->join('purchase_orders','purchase_orders.id','=','purchase_order_bills.purchase_order_id')
                                        ->join('purchase_requests','purchase_requests.id','=','purchase_orders.purchase_request_id')
                                        ->where('purchase_requests.project_site_id',$projectSiteId)->sum('purchase_order_payments.amount');
            }
        }catch(\Exception $e){
            $purchasePaidAmount = 0;
            $data = [
                'action' => 'Get Bill Transaction Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $purchasePaidAmount;
    }

    public function getAssetMaintenancePaidAmount($projectSiteId){
        try{
            if($projectSiteId == 'all'){
                $assetMaintenanceBillAmount = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bills.amount');
            }else{
                $assetMaintenanceBillAmount = AssetMaintenanceBill::join('asset_maintenance','asset_maintenance.id','=','asset_maintenance_bills.asset_maintenance_id')
                    ->where('asset_maintenance.project_site_id',$projectSiteId)
                    ->select('asset_maintenance.id as id','asset_maintenance_bills.id as bill_id','asset_maintenance_bills.bill_number as bill_number','asset_maintenance_bills.amount')
                    ->sum('asset_maintenance_bills.amount');
            }
        }catch(\Exception $e){
            $assetMaintenanceBillAmount = 0;
            $data = [
                'action' => 'Get Asset Maintenance Bill Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $assetMaintenanceBillAmount;
    }

    public function getAssetRentPaidAmount($projectSiteId){
        try{
            $assetRentAmount = 0;
            if($projectSiteId == 'all'){

            }else{

            }
        }catch(\Exception $e){
            $assetRentAmount = 0;
            $data = [
                'action' => 'Get Asset Maintenance Bill Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $assetRentAmount;
    }

    public function getPeticashSalaryAmount($projectSiteId){
        try{
            $peticashSalaryAmount = 0;
            $approvedPeticashStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
            $officeSiteId = ProjectSite::where('name',env('OFFICE_PROJECT_SITE_NAME'))->pluck('id')->first();
            if($projectSiteId == 'all'){
                $advanceAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('amount');

                $salaryPayableAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('payable_amount');
                $salaryPfAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('pf');
                $salaryTdsAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('tds');
                $salaryPtAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('pt');
                $salaryEsicAmountTotal = PeticashSalaryTransaction::
                    where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('esic');

                $salaryAmountTotal = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                $officeSiteDistributedAmount = ProjectSite::sum('distributed_salary_amount');
                $peticashSalaryAmount = $salaryAmountTotal + $advanceAmountTotal + $officeSiteDistributedAmount;
            }else{
                $advanceAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                                ->where('project_site_id','!=',$officeSiteId)
                                ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                                ->where('peticash_status_id',$approvedPeticashStatusId)
                                ->sum('amount');

                $salaryPayableAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('payable_amount');
                $salaryPfAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('pf');
                $salaryTdsAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('tds');
                $salaryPtAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('pt');
                $salaryEsicAmountTotal = PeticashSalaryTransaction::where('project_site_id',$projectSiteId)
                    ->where('project_site_id','!=',$officeSiteId)
                    ->where('peticash_transaction_type_id',PeticashTransactionType::where('type','ilike','payment')->where('slug','salary')->pluck('id')->first())
                    ->where('peticash_status_id',$approvedPeticashStatusId)
                    ->sum('esic');

                $salaryAmountTotal = $salaryPayableAmountTotal + $salaryPfAmountTotal + $salaryTdsAmountTotal + $salaryPtAmountTotal + $salaryEsicAmountTotal;
                $officeSiteDistributedAmount = ProjectSite::where('id',$projectSiteId)->pluck('distributed_salary_amount')->first();
                $officeSiteDistributedAmount = ($officeSiteDistributedAmount != null) ? $officeSiteDistributedAmount : 0;
                $peticashSalaryAmount = $salaryAmountTotal + $advanceAmountTotal + $officeSiteDistributedAmount;
            }
        }catch(\Exception $e){
            $peticashSalaryAmount = 0;
            $data = [
                'action' => 'Get Peticash Amount for Report',
                'exception' => $e->getMessage(),
                'project_site_id' => $projectSiteId,
            ];
            Log::critical(json_encode($data));
        }
        return $peticashSalaryAmount;
    }
}
