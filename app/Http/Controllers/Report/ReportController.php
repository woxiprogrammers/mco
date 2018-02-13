<?php

namespace App\Http\Controllers\Report;

use App\Bill;
use App\BillStatus;
use App\BillTax;
use App\BillTransaction;
use App\Category;
use App\Employee;
use App\Helper\UnitHelper;
use App\Http\Controllers\CustomTraits\BillTrait;
use App\Material;
use App\Helper\MaterialProductHelper;
use App\PeticashSalaryTransaction;
use App\PeticashStatus;
use App\PeticashTransactionType;
use App\ProjectSite;
use App\PurchaseOrderComponent;
use App\PurchaseOrderTransaction;
use App\PurchaseOrderTransactionComponent;
use App\PurchaseOrderTransactionStatus;
use App\Quotation;
use App\Subcontractor;
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
                                    ->whereIn('bills.id',$billIds)
                ->select('project_sites.id','project_sites.name','project_sites.address')->get()->toArray();
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
            $data = $header = array();
            switch($report_type) {
                case 'materialwise_purchase_report':
                    $header = array(
                        'Sr. No', 'Date', 'Category Name', 'Material Name', 'Quantity', 'Unit', 'Basic Amount', 'Total Tax Amount',
                        'Total Amount', 'Average Amount'
                    );
                    $row = 0;
                    $data = $purchaseOrderComponents = array();
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
                            ->where('material_request_components.name','ilike',$materialName)
                            ->where('material_requests.project_site_id',$request['materialwise_purchase_report_site_id'])
                            ->whereBetween('purchase_orders.created_at', [$start_date, $end_date])
                            ->select('purchase_orders.id as purchase_order_id','purchase_order_components.created_at','purchase_order_components.id as purchase_order_component_id','purchase_order_components.rate_per_unit'
                                ,'purchase_order_components.unit_id','purchase_order_components.cgst_percentage','purchase_order_components.sgst_percentage','purchase_order_components.igst_percentage','material_request_components.name as material_request_component_name')
                            ->get()->toArray();
                    }

                    $billGeneratedPOTransactionStatusId = PurchaseOrderTransactionStatus::where('slug','bill-generated')->pluck('id')->first();
                    foreach ($purchaseOrderComponents as $key => $purchaseOrderComponentArray){
                        foreach ($purchaseOrderComponentArray as $key2 => $purchaseOrderComponent){
                            $purchaseOrderTransactionComponents = PurchaseOrderTransactionComponent::join('purchase_order_transactions','purchase_order_transactions.id','=','purchase_order_transaction_components.purchase_order_transaction_id')
                                ->where('purchase_order_transaction_components.purchase_order_component_id',$purchaseOrderComponent['purchase_order_component_id'])
                                ->where('purchase_order_transactions.purchase_order_transaction_status_id',$billGeneratedPOTransactionStatusId)
                                ->select('purchase_order_transaction_components.id as purchase_order_transaction_component_id','purchase_order_transaction_components.quantity','purchase_order_transaction_components.unit_id')
                                ->get();
                            $quantity = $taxAmount = 0;
                            if($purchaseOrderTransactionComponents != null){
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
                                if($request['category_id'] == 0){
                                    $data[$row]['category_name'] = Category::join('category_material_relations','category_material_relations.category_id','=','categories.id')
                                        ->join('materials','materials.id','=','category_material_relations.material_id')
                                        ->where('materials.name','ilike',$purchaseOrderComponent['material_request_component_name'])
                                        ->pluck('categories.name')->first();
                                }else{
                                    $data[$row]['category_name'] = Category::where('id',$request['category_id'])->pluck('name')->first();
                                }
                                $data[$row]['material_name'] = $purchaseOrderComponent['material_request_component_name'];
                                $data[$row]['quantity'] = $quantity;
                                $data[$row]['unit_id'] = Unit::where('id',$purchaseOrderComponent['unit_id'])->pluck('name')->first();
                                $data[$row]['rate'] = $purchaseOrderComponent['rate_per_unit'] * $quantity;
                                $data[$row]['tax_amount'] = $taxAmount;
                                $data[$row]['total_amount'] = $taxAmount + $data[$row]['rate'];
                                $data[$row]['average_amount'] = $data[$row]['total_amount'] / $data[$row]['quantity'];
                            }
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


                case 'labour_specific_report':
                    $header = array(
                        'Date', 'Payment Type', 'Gross Salary', '-PT', '-PF', '-ESIC', '-TDS',
                        '-ADVANCE', 'Net Payment', 'Balance'
                    );
                    $approvedStatusId = PeticashStatus::where('slug','approved')->pluck('id')->first();
                    $paymentSlug = PeticashTransactionType::where('type','PAYMENT')->select('id','slug')->get();
                    $salaryTransactionData = array();
                    if($request['labour_specific_report_site_id'] == 'all'){
                        $salaryTransactionData = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('date','desc')
                            ->get();
                    }else{
                        $salaryTransactionData = PeticashSalaryTransaction::where('project_site_id',$request['labour_specific_report_site_id'])
                            ->where('employee_id',$request['labour_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->whereBetween('date', [$start_date, $end_date])
                            ->orderBy('date','desc')
                            ->get();
                    }

                    $row = 0;
                    $data = array();
                    foreach($salaryTransactionData as $key => $salaryTransaction){
                        $peticashTransactionTypeSlug = $salaryTransaction->peticashTransactionType->slug;
                        $data[$row]['date'] = date('d/m/y',strtotime($salaryTransaction['date']));;
                        $data[$row]['payment_type'] = $salaryTransaction->peticashTransactionType->name;
                        $data[$row]['gross_salary'] = ($peticashTransactionTypeSlug == 'salary') ? ($salaryTransaction->employee->per_day_wages * $salaryTransaction['days']) : 0;
                        $data[$row]['pt'] = $salaryTransaction['pt'];
                        $data[$row]['pf'] = $salaryTransaction['pf'];
                        $data[$row]['esic'] = $salaryTransaction['esic'];
                        $data[$row]['tds'] = $salaryTransaction['tds'];
                        $data[$row]['advance'] = ($peticashTransactionTypeSlug == 'salary') ? 0 : $salaryTransaction['amount'];
                        $data[$row]['net_payment'] = ($peticashTransactionTypeSlug == 'salary') ? $salaryTransaction['payable_amount'] : $salaryTransaction['amount'];
                        $lastSalaryTransactionId = PeticashSalaryTransaction::where('employee_id',$request['labour_id'])
                            ->where('project_site_id',$salaryTransaction['project_site_id'])
                            ->where('peticash_status_id',$approvedStatusId)
                            ->where('id','<',$salaryTransaction['id'])
                            ->where('peticash_transaction_type_id',$paymentSlug->where('slug','salary')->pluck('id')->first())
                            ->orderBy('date','desc')
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
                                    ->orderBy('date','desc')
                                    ->sum('amount');
                            }
                            $balance = $data[$row]['gross_salary'] - $salaryTransaction['pt'] - $salaryTransaction['pf'] - $salaryTransaction['esic'] - $salaryTransaction['tds'] + $advancesAfterLastSalary;
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
                                    ->orderBy('date','desc')
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
                case 'receiptwise_p_and_l_report':
                    $header = array(null, null);
                    $data = array(
                        array('Total Sale Entry', 1),
                        array('Total receipt entry', 1),
                        array(null, null),
                        array('Labour + Staff Salary', null),
                        array('Total Purchase', null),
                        array('Total Miscellaneous Purchase', null),
                        array('Subcontractor', null),
                        array('Indirect Expences (GST,TDS Paid to government from manisha)', null),
                        array('Total Expence', null),
                        array(null, null),
                        array('Profit/ Loss Salewise', 'Profit/ Loss Receiptwise'),
                        array(1, 1),
                    );
                    break;
                case 'subcontractor_report':
                    $header = array(
                        'Sr. No', 'Summary Type', 'Bill No', 'Total Bill Amount', 'TDS',
                        'Retention', 'Total Bill Amount', 'Total Pay Amount', 'Balance'
                    );
                    $data = array(
                        array('data1', 'data2'),
                        array('data3', 'data4')
                    );
                    break;
                case 'purchase_bill_tax_report':
                    $header = array(
                        'Sr. No', 'Basic Amount', 'IGST Amount', 'SGST Amount', 'CGST Amount',
                        'With Tax Amount'
                    );
                    $data = array(
                        array('data1', 'data2'),
                        array('data3', 'data4')
                    );
                    break;
                case 'sales_bill_tax_report':
                    $site_id = $request->sales_bill_tax_report_site_id;
                    $array_no = 1;
                    $iterator = $i= 0;
                    $data = $currentTaxes = $listingData = array();
                    $quotation = Quotation::where('project_site_id',$site_id)->first();
                    $allBills = Bill::where('quotation_id',$quotation->id)->get();
                    $statusId = BillStatus::whereIn('slug',['approved'])->get()->toArray();
                    $bills = Bill::where('quotation_id',$quotation->id)->whereIn('bill_status_id',array_column($statusId,'id'))->whereBetween('created_at', array($start_date, $end_date))->orderBy('created_at','asc')->get();
                    $cancelBillStatusId = BillStatus::where('slug','cancelled')->pluck('id')->first();
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
                        $listingData[$iterator]['status'] = $bill->bill_status->slug ;
                        $listingData[$iterator]['bill_id'] = $bill->id;
                        if($bill->bill_status_id != $cancelBillStatusId){
                            $listingData[$iterator]['array_no'] = "RA Bill - ".$array_no;
                            $array_no++;
                        }else{
                            $listingData[$iterator]['array_no'] = '-';
                        }
                        $listingData[$iterator]['bill_no_format'] = "B-".strtoupper(date('M',strtotime($bill['created_at'])))."-".$bill->id."/".date('y',strtotime($bill['created_at']));
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
                        $listingData[$iterator]['subTotal'] = $total_amount;
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
                        $listingData[$iterator]['final_total'] = $total_amount;
                        foreach($currentTaxes as $key2 => $tax){
                            if(array_key_exists('percentage',$tax)){
                                $listingData[$iterator]['tax'][$tax['tax_id']] = $total_amount * ($tax['percentage'] / 100);
                            }else{
                                $listingData[$iterator]['tax'][$tax['tax_id']] = 0;
                            }
                            $listingData[$iterator]['final_total'] = MaterialProductHelper::customRound($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
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
                                        $taxAmount += $listingData[$iterator]['tax'][$appliedTaxId] * ($tax['percentage'] / 100);
                                    }
                                }
                            }else{
                                $taxAmount += $total_amount * ($tax['percentage'] / 100);
                            }

                            $listingData[$iterator]['tax'][$tax['tax_id']] = $taxAmount;
                            $listingData[$iterator]['final_total'] = MaterialProductHelper::customRound($listingData[$iterator]['final_total'] + $listingData[$iterator]['tax'][$tax['tax_id']]);
                        }
                        $iterator++;
                    }
                    $jIterator = 0;
                    foreach($listingData as $key => $billData){
                        $data[$jIterator]['bill_no'] = $billData['array_no'];
                        $data[$jIterator]['basic_amount'] = $billData['subTotal'];
                        $data[$jIterator]['tax_amount'] = $billData['final_total'] - $billData['subTotal'];
                        $data[$jIterator]['total_amount'] = $billData['final_total'];
                        $bllTransactions = BillTransaction::where('bill_id',$billData['bill_id'])->get();
                        //need to work mobilise_amount
                        $data[$jIterator]['mobilise_amount'] = 0;
                        $data[$jIterator]['debit'] = -$bllTransactions->sum('debit');
                        $data[$jIterator]['hold'] = -$bllTransactions->sum('hold');
                        $data[$jIterator]['retention'] = -$bllTransactions->sum('retention');
                        $data[$jIterator]['tds'] = -$bllTransactions->sum('tds');
                        //need to work other_recovery
                        $data[$jIterator]['other_recovery'] = 0;
                        $data[$jIterator]['payable_amount'] = $billData['final_total'];
                        $data[$jIterator]['check_amount'] = $bllTransactions->sum('total');
                        $data[$jIterator]['balance'] = $data[$jIterator]['payable_amount'] - $data[$jIterator]['check_amount'];
                    }

                    $header = array(
                        'RA Bill Number', 'Basic Amount', 'Tax Amount', 'Total Amount',
                        'Mobilise Advance', 'Debit', 'Hold', 'Retention',
                        'TDS', 'Other Recovery', 'Payble Amount', 'Check Amount',
                        'Balance'
                    );

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
}
